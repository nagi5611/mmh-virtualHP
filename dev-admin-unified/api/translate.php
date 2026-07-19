<?php
// public_html/dev-admin-unified/api/translate.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

// Gemini APIキー設定ファイル
$configPath = __DIR__ . '/../gemini_config.php';
if (file_exists($configPath)) {
    require $configPath;
}

// APIキーが設定されているか確認
if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === '') {
    json_error('Gemini APIキーが設定されていません。gemini_config.phpを作成してください。', 500);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('POSTリクエストのみ許可されています', 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['text'])) {
    json_error('翻訳するテキストが必要です');
}

$sourceText = $input['text'];
$targetLangs = $input['targetLangs'] ?? ['en', 'zh'];

// Gemini API呼び出し（モデル ID: https://ai.google.dev/gemini-api/docs/models ）
function translate_with_gemini(string $text, array $targetLangs): array {
    $apiKey = GEMINI_API_KEY;
    $model = 'gemini-2.5-flash';
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    
    $translations = [];
    
    foreach ($targetLangs as $lang) {
        $langName = match($lang) {
            'en' => 'English',
            'zh' => 'Chinese (Simplified)',
            default => $lang
        };
        $prompt = "Translate the following Japanese text to {$langName}. Return only the translation without any explanation or additional text. When translating, do not use any words or expressions that are inappropriate, offensive, or violate compliance or social norms.\n\nJapanese text: {$text}";
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 128,
            ]
        ];
        
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "x-goog-api-key: {$apiKey}"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            $translations[$lang] = ['error' => 'cURL Error: ' . $error];
            continue;
        }
        
        if ($httpCode !== 200) {
            $errorDetail = '';
            if ($response) {
                $errorData = json_decode($response, true);
                if (isset($errorData['error']['message'])) {
                    $errorDetail = ': ' . $errorData['error']['message'];
                }
            }
            $translations[$lang] = ['error' => 'API Error: HTTP ' . $httpCode . $errorDetail];
            continue;
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $translatedText = trim($result['candidates'][0]['content']['parts'][0]['text']);
            $translations[$lang] = $translatedText;
        } else {
            // エラー情報があれば取得
            $errorMsg = 'Translation failed';
            if (isset($result['promptFeedback']['blockReason'])) {
                $errorMsg .= ': ' . $result['promptFeedback']['blockReason'];
            }
            $translations[$lang] = ['error' => $errorMsg];
        }
    }
    
    return $translations;
}

try {
    $translations = translate_with_gemini($sourceText, $targetLangs);
    
    // エラーチェック
    $hasErrors = false;
    foreach ($translations as $lang => $text) {
        if (is_array($text) && isset($text['error'])) {
            $hasErrors = true;
            break;
        }
    }
    
    if ($hasErrors) {
        json_response([
            'status' => 'partial_success',
            'data' => $translations,
            'message' => '一部の翻訳が失敗しました'
        ], 200);
    } else {
        json_success([
            'ja' => $sourceText,
            'en' => $translations['en'] ?? '',
            'zh' => $translations['zh'] ?? ''
        ], '翻訳が完了しました');
    }
} catch (Exception $e) {
    json_error('翻訳エラー: ' . $e->getMessage(), 500);
}

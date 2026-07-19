<?php
// public_html/airport/dev/api/translate.php — 日本語から en/zh/zh-TW/ko（Gemini）
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$geminiConfig = __DIR__ . '/../../../dev-admin-unified/gemini_config.php';
if (file_exists($geminiConfig)) {
    require_once $geminiConfig;
}

if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === '') {
    json_error('Gemini APIキーが設定されていません。dev-admin-unified/gemini_config.php を確認してください。', 500);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('POSTリクエストのみ許可されています', 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || empty($input['text'])) {
    json_error('翻訳するテキストが必要です');
}

$sourceText = (string)$input['text'];
// モデル ID: https://ai.google.dev/gemini-api/docs/models
$model = 'gemini-2.5-flash';
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
$apiKey = GEMINI_API_KEY;

/**
 * @return array{error?:string,text?:string}
 */
function gemini_generate(string $endpoint, string $apiKey, string $prompt, int $maxTokens = 2048): array {
    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt],
                ],
            ],
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => $maxTokens,
        ],
    ];

    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "x-goog-api-key: {$apiKey}",
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 60,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['error' => 'cURL: ' . $err];
    }
    if ($httpCode !== 200) {
        $detail = '';
        if ($response) {
            $decoded = json_decode($response, true);
            if (isset($decoded['error']['message'])) {
                $detail = ': ' . $decoded['error']['message'];
            }
        }
        return ['error' => 'HTTP ' . $httpCode . $detail];
    }

    $result = json_decode($response, true);
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        return ['text' => trim($result['candidates'][0]['content']['parts'][0]['text'])];
    }

    $msg = 'Translation failed';
    if (isset($result['promptFeedback']['blockReason'])) {
        $msg .= ': ' . $result['promptFeedback']['blockReason'];
    }
    return ['error' => $msg];
}

/**
 * @return array<string, string|array{error:string}>
 */
function translate_one_lang(string $endpoint, string $apiKey, string $ja, string $lang): array {
    $langName = match ($lang) {
        'en' => 'English',
        'zh' => 'Chinese (Simplified)',
        'zh-TW' => 'Traditional Chinese as used in Taiwan',
        'ko' => 'Korean',
        default => $lang,
    };
    $encoded = json_encode($ja, JSON_UNESCAPED_UNICODE);
    $prompt = "Translate the following Japanese text (given as JSON string {$encoded} — decode it first) to {$langName}. Return only the translation without any explanation or additional text. Do not use inappropriate or offensive wording.";
    $out = gemini_generate($endpoint, $apiKey, $prompt, 512);
    if (isset($out['error'])) {
        return [$lang => ['error' => $out['error']]];
    }
    return [$lang => $out['text']];
}

/**
 * @param string $raw
 * @return array<string, string>|null
 */
function parse_translation_json(string $raw): ?array {
    $t = trim($raw);
    if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/s', $t, $m)) {
        $t = trim($m[1]);
    }
    $decoded = json_decode($t, true);
    if (!is_array($decoded)) {
        return null;
    }
    $out = [];
    foreach (['en', 'zh', 'zh-TW', 'ko'] as $k) {
        if (!isset($decoded[$k]) || !is_string($decoded[$k])) {
            return null;
        }
        $out[$k] = trim($decoded[$k]);
    }
    return $out;
}

$safeJa = json_encode($sourceText, JSON_UNESCAPED_UNICODE);
$jsonPrompt = <<<PROMPT
You are a professional translator. The Japanese announcement is given as a JSON-encoded string (including quotes): {$safeJa}
Decode it mentally, then translate that Japanese text into exactly four languages.
Output ONLY a single JSON object with these keys (use exactly these spellings, including hyphens):
"en" — natural English
"zh" — Simplified Chinese (Mainland)
"zh-TW" — Traditional Chinese as used in Taiwan
"ko" — Korean

Rules:
- No markdown code fences. No explanations before or after the JSON.
- Escape any double quotes inside string values as \\".
- Do not use inappropriate or offensive wording.
PROMPT;

$combined = gemini_generate($endpoint, $apiKey, $jsonPrompt, 2048);
$translations = null;
if (isset($combined['text'])) {
    $translations = parse_translation_json($combined['text']);
}

if ($translations === null) {
    $translations = [];
    foreach (['en', 'zh', 'zh-TW', 'ko'] as $lang) {
        $translations = array_merge($translations, translate_one_lang($endpoint, $apiKey, $sourceText, $lang));
    }
}

$hasErrors = false;
foreach (['en', 'zh', 'zh-TW', 'ko'] as $lang) {
    $v = $translations[$lang] ?? '';
    if (is_array($v) && isset($v['error'])) {
        $hasErrors = true;
        break;
    }
}

if ($hasErrors) {
    json_response([
        'status' => 'partial_success',
        'data' => array_merge(['ja' => $sourceText], $translations),
        'message' => '一部の翻訳が失敗しました',
    ], 200);
}

json_success([
    'ja' => $sourceText,
    'en' => is_string($translations['en'] ?? null) ? $translations['en'] : '',
    'zh' => is_string($translations['zh'] ?? null) ? $translations['zh'] : '',
    'zh-TW' => is_string($translations['zh-TW'] ?? null) ? $translations['zh-TW'] : '',
    'ko' => is_string($translations['ko'] ?? null) ? $translations['ko'] : '',
], '翻訳が完了しました');

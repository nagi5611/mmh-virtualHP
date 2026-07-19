<?php
// public_html/dev-admin-unified/api/news.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$jsonPath = realpath(__DIR__ . '/../../data/news.json');

// JSONファイル読み込み
function load_news(): array {
    global $jsonPath;
    if (!file_exists($jsonPath)) {
        return [];
    }
    $raw = file_get_contents($jsonPath);
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// JSONファイル保存
function save_news(array $data): bool {
    global $jsonPath;
    return file_put_contents(
        $jsonPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

// GET: 一覧取得、検索・フィルタ
if ($method === 'GET') {
    $news = load_news();
    
    // 検索
    if (!empty($_GET['search'])) {
        $search = mb_strtolower($_GET['search']);
        $news = array_filter($news, function($item) use ($search) {
            $textJa = mb_strtolower($item['text']['ja'] ?? '');
            $textEn = mb_strtolower($item['text']['en'] ?? '');
            $textZh = mb_strtolower($item['text']['zh'] ?? '');
            $tags = implode(' ', array_map('mb_strtolower', $item['tags'] ?? []));
            
            return str_contains($textJa, $search) 
                || str_contains($textEn, $search)
                || str_contains($textZh, $search)
                || str_contains($tags, $search);
        });
    }
    
    // タグフィルタ
    if (!empty($_GET['tag'])) {
        $tag = $_GET['tag'];
        $news = array_filter($news, function($item) use ($tag) {
            return in_array($tag, $item['tags'] ?? [], true);
        });
    }
    
    // ソート
    $sort = $_GET['sort'] ?? 'order';
    usort($news, function($a, $b) use ($sort) {
        switch ($sort) {
            case 'date-asc':
                return strcmp($a['date'], $b['date']);
            case 'date-desc':
                return strcmp($b['date'], $a['date']);
            case 'title-asc':
                return strcmp($a['text']['ja'], $b['text']['ja']);
            case 'title-desc':
                return strcmp($b['text']['ja'], $a['text']['ja']);
            case 'order':
            default:
                // orderの昇順（orderが小さいほど新しい）
                $orderA = $a['order'] ?? 999999;
                $orderB = $b['order'] ?? 999999;
                return $orderA <=> $orderB;
        }
    });
    
    json_success(array_values($news));
}

// POST: 新規作成
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['date']) || empty($input['text']['ja'])) {
        json_error('日付と日本語テキストは必須です');
    }
    
    $news = load_news();
    $maxId = empty($news) ? 0 : max(array_column($news, 'id'));
    
    // 新規ニュースはorder: 0（最新）を設定
    // 既存のすべてのニュースのorderを+1する（古くなる）
    foreach ($news as &$item) {
        if (isset($item['order'])) {
            $item['order'] = $item['order'] + 1;
        } else {
            $item['order'] = 1;
        }
    }
    unset($item);
    
    $newItem = [
        'id' => $maxId + 1,
        'date' => $input['date'],
        'text' => [
            'ja' => $input['text']['ja'],
            'en' => $input['text']['en'] ?? '',
            'zh' => $input['text']['zh'] ?? ''
        ],
        'order' => 0, // 最新のニュースはorder: 0
        'tags' => $input['tags'] ?? [],
        'createdAt' => date('c'),
        'updatedAt' => date('c')
    ];
    
    $news[] = $newItem;
    
    // orderの降順でソート（orderが小さいほど新しい）
    usort($news, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB; // 昇順（order: 0が最初）
    });
    
    if (!save_news($news)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($newItem, '作成しました');
}

// PUT: 更新
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['id'])) {
        json_error('IDが必要です');
    }
    
    $news = load_news();
    $index = array_search($input['id'], array_column($news, 'id'));
    
    if ($index === false) {
        json_error('ニュースが見つかりません', 404);
    }
    
    // 更新
    $news[$index]['date'] = $input['date'] ?? $news[$index]['date'];
    $news[$index]['text'] = [
        'ja' => $input['text']['ja'] ?? $news[$index]['text']['ja'],
        'en' => $input['text']['en'] ?? $news[$index]['text']['en'],
        'zh' => $input['text']['zh'] ?? $news[$index]['text']['zh']
    ];
    $news[$index]['tags'] = $input['tags'] ?? $news[$index]['tags'];
    $news[$index]['updatedAt'] = date('c');
    
    if (!save_news($news)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($news[$index], '更新しました');
}

// DELETE: 削除
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['ids']) || !is_array($input['ids'])) {
        json_error('削除するIDを指定してください');
    }
    
    $news = load_news();
    $news = array_filter($news, function($item) use ($input) {
        return !in_array($item['id'], $input['ids'], true);
    });
    
    // orderの昇順でソート（orderが小さいほど新しい）
    $news = array_values($news);
    usort($news, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    
    if (!save_news($news)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success(['deleted' => count($input['ids'])], '削除しました');
}

// PATCH: 並び替え
if ($method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['orders']) || !is_array($input['orders'])) {
        json_error('並び順データが必要です');
    }
    
    $news = load_news();
    
    // IDをキーとした配列に変換
    $newsById = [];
    foreach ($news as $item) {
        $newsById[$item['id']] = $item;
    }
    
    // 新しい順序で再構築
    $newNews = [];
    foreach ($input['orders'] as $order => $id) {
        if (isset($newsById[$id])) {
            $newsById[$id]['order'] = $order;
            $newNews[] = $newsById[$id];
        }
    }
    
    // orderの昇順でソート（orderが小さいほど新しい）
    usort($newNews, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    
    if (!save_news($newNews)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($newNews, '並び順を更新しました');
}

json_error('無効なリクエストメソッドです', 405);

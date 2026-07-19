<?php
// public_html/diorama/dev/api/news.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
// api/ 直下のため diorama/data/news.json は 2 階層上
$jsonPath = realpath(__DIR__ . '/../../data/news.json');

/**
 * @param array<string, mixed> $t
 * @return array{ja:string,zh:string,zh-TW:string,en:string,ko:string}
 */
function normalize_diorama_text(array $t): array {
    return [
        'ja' => (string)($t['ja'] ?? ''),
        'zh' => (string)($t['zh'] ?? ''),
        'zh-TW' => (string)($t['zh-TW'] ?? ''),
        'en' => (string)($t['en'] ?? ''),
        'ko' => (string)($t['ko'] ?? ''),
    ];
}

function load_diorama_news(): array {
    global $jsonPath;
    if ($jsonPath === false || !is_readable($jsonPath)) {
        return [];
    }
    $raw = file_get_contents($jsonPath);
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function save_diorama_news(array $data): bool {
    global $jsonPath;
    if ($jsonPath === false) {
        return false;
    }
    return file_put_contents(
        $jsonPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

if ($jsonPath === false) {
    json_error('news.json が見つかりません。diorama/data/news.json を作成してください。', 500);
}

if ($method === 'GET') {
    $news = load_diorama_news();

    if (!empty($_GET['search'])) {
        $search = mb_strtolower($_GET['search']);
        $news = array_filter($news, function ($item) use ($search) {
            $text = normalize_diorama_text($item['text'] ?? []);
            $blob = mb_strtolower(implode(' ', $text));
            return str_contains($blob, $search);
        });
    }

    $sort = $_GET['sort'] ?? 'order';
    usort($news, function ($a, $b) use ($sort) {
        switch ($sort) {
            case 'date-asc':
                return strcmp($a['date'] ?? '', $b['date'] ?? '');
            case 'date-desc':
                return strcmp($b['date'] ?? '', $a['date'] ?? '');
            case 'order':
            default:
                $orderA = $a['order'] ?? 999999;
                $orderB = $b['order'] ?? 999999;
                return $orderA <=> $orderB;
        }
    });

    json_success(array_values($news));
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        json_error('無効なJSONです');
    }

    if (empty($input['date']) || empty($input['text']['ja'])) {
        json_error('日付と日本語テキストは必須です');
    }

    $news = load_diorama_news();
    $maxId = empty($news) ? 0 : max(array_column($news, 'id'));

    foreach ($news as &$item) {
        if (isset($item['order'])) {
            $item['order'] = $item['order'] + 1;
        } else {
            $item['order'] = 1;
        }
    }
    unset($item);

    $datetime = isset($input['datetime']) ? (string)$input['datetime'] : '';
    if ($datetime === '') {
        $datetime = str_replace('.', '-', (string)$input['date']);
    }

    $newItem = [
        'id' => $maxId + 1,
        'date' => (string)$input['date'],
        'datetime' => $datetime,
        'link' => isset($input['link']) ? trim((string)$input['link']) : '',
        'text' => normalize_diorama_text($input['text'] ?? []),
        'order' => 0,
        'createdAt' => date('c'),
        'updatedAt' => date('c'),
    ];

    $news[] = $newItem;

    usort($news, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });

    if (!save_diorama_news($news)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($newItem, '作成しました');
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || !isset($input['id'])) {
        json_error('IDが必要です');
    }

    $news = load_diorama_news();
    $targetId = (int)$input['id'];
    $index = null;
    foreach ($news as $i => $row) {
        if ((int)($row['id'] ?? 0) === $targetId) {
            $index = $i;
            break;
        }
    }

    if ($index === null) {
        json_error('お知らせが見つかりません', 404);
    }

    if (isset($input['date'])) {
        $news[$index]['date'] = (string)$input['date'];
    }
    if (array_key_exists('datetime', $input)) {
        $news[$index]['datetime'] = (string)$input['datetime'];
    }
    if (array_key_exists('link', $input)) {
        $news[$index]['link'] = trim((string)$input['link']);
    }
    if (isset($input['text']) && is_array($input['text'])) {
        $news[$index]['text'] = normalize_diorama_text($input['text']);
    }
    $news[$index]['updatedAt'] = date('c');

    if (!save_diorama_news($news)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($news[$index], '更新しました');
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || empty($input['ids']) || !is_array($input['ids'])) {
        json_error('削除するIDを指定してください');
    }

    $news = load_diorama_news();
    $news = array_filter($news, function ($item) use ($input) {
        return !in_array($item['id'], $input['ids'], true);
    });
    $news = array_values($news);
    usort($news, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });

    if (!save_diorama_news($news)) {
        json_error('保存に失敗しました', 500);
    }

    json_success(['deleted' => count($input['ids'])], '削除しました');
}

if ($method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || empty($input['orders']) || !is_array($input['orders'])) {
        json_error('並び順データが必要です');
    }

    $news = load_diorama_news();
    $byId = [];
    foreach ($news as $item) {
        $byId[$item['id']] = $item;
    }

    $newNews = [];
    foreach ($input['orders'] as $order => $id) {
        if (isset($byId[$id])) {
            $byId[$id]['order'] = (int)$order;
            $newNews[] = $byId[$id];
        }
    }

    usort($newNews, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });

    if (!save_diorama_news($newNews)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($newNews, '並び順を更新しました');
}

json_error('無効なリクエストメソッドです', 405);

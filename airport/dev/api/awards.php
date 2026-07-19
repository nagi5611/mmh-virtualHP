<?php
// public_html/airport/dev/api/awards.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$jsonPath = realpath(__DIR__ . '/../../data/awards.json');

const AWARD_STATUSES = ['won', 'finalist', 'first_pass', 'ongoing', 'nominated'];

/**
 * @param array<string, mixed> $t
 * @return array{ja:string,zh:string,zh-TW:string,en:string,ko:string}
 */
function normalize_award_text(array $t): array {
    return [
        'ja' => (string)($t['ja'] ?? ''),
        'zh' => (string)($t['zh'] ?? ''),
        'zh-TW' => (string)($t['zh-TW'] ?? ''),
        'en' => (string)($t['en'] ?? ''),
        'ko' => (string)($t['ko'] ?? ''),
    ];
}

function load_awards(): array {
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

function save_awards(array $data): bool {
    global $jsonPath;
    if ($jsonPath === false) {
        return false;
    }
    return file_put_contents(
        $jsonPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

function normalize_award_status(string $status): string {
    return in_array($status, AWARD_STATUSES, true) ? $status : 'nominated';
}

/**
 * @param mixed $attachments
 * @return list<array{id:int,label:string,url:string,type:string,mime?:string}>
 */
function normalize_award_attachments($attachments): array {
    if (!is_array($attachments)) {
        return [];
    }
    $out = [];
    foreach ($attachments as $item) {
        if (!is_array($item)) {
            continue;
        }
        $url = trim((string)($item['url'] ?? ''));
        if ($url === '') {
            continue;
        }
        $out[] = [
            'id' => (int)($item['id'] ?? (microtime(true) * 1000)),
            'label' => (string)($item['label'] ?? '添付ファイル'),
            'url' => $url,
            'type' => (string)($item['type'] ?? 'file'),
            'mime' => (string)($item['mime'] ?? ''),
        ];
    }
    return $out;
}

/**
 * @param mixed $frame
 * @return array{width:float,height:int,cropLeft:float,cropTop:float,cropRight:float,cropBottom:float}
 */
function normalize_award_thumbnail_frame($frame): array {
    if (!is_array($frame)) {
        return [
            'width' => 100,
            'height' => 0,
            'cropLeft' => 0,
            'cropTop' => 0,
            'cropRight' => 0,
            'cropBottom' => 0,
        ];
    }
    $clampPct = static function ($v, float $min, float $max): float {
        return max($min, min($max, (float)$v));
    };
    return [
        'width' => $clampPct($frame['width'] ?? 100, 20, 100),
        'height' => max(0, (int)($frame['height'] ?? 0)),
        'cropLeft' => $clampPct($frame['cropLeft'] ?? 0, 0, 95),
        'cropTop' => $clampPct($frame['cropTop'] ?? 0, 0, 95),
        'cropRight' => $clampPct($frame['cropRight'] ?? 0, 0, 95),
        'cropBottom' => $clampPct($frame['cropBottom'] ?? 0, 0, 95),
    ];
}

if ($jsonPath === false) {
    json_error('awards.json が見つかりません。airport/data/awards.json を作成してください。', 500);
}

if ($method === 'GET') {
    $awards = load_awards();

    if (!empty($_GET['search'])) {
        $search = mb_strtolower($_GET['search']);
        $awards = array_filter($awards, function ($item) use ($search) {
            $org = normalize_award_text($item['organization'] ?? []);
            $title = normalize_award_text($item['title'] ?? []);
            $result = normalize_award_text($item['result'] ?? []);
            $desc = normalize_award_text($item['description'] ?? []);
            $blob = mb_strtolower(implode(' ', array_merge($org, $title, $result, $desc)));
            return str_contains($blob, $search);
        });
    }

    $sort = $_GET['sort'] ?? 'order';
    usort($awards, function ($a, $b) use ($sort) {
        switch ($sort) {
            case 'year-asc':
                return strcmp($a['year'] ?? '', $b['year'] ?? '');
            case 'year-desc':
                return strcmp($b['year'] ?? '', $a['year'] ?? '');
            case 'order':
            default:
                $orderA = $a['order'] ?? 999999;
                $orderB = $b['order'] ?? 999999;
                return $orderA <=> $orderB;
        }
    });

    json_success(array_values($awards));
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        json_error('無効なJSONです');
    }

    if (empty($input['year']) || empty($input['organization']['ja']) || empty($input['title']['ja']) || empty($input['result']['ja'])) {
        json_error('年度・主催名・部門名・結果（日本語）は必須です');
    }

    $awards = load_awards();
    $maxId = empty($awards) ? 0 : max(array_column($awards, 'id'));

    foreach ($awards as &$item) {
        if (isset($item['order'])) {
            $item['order'] = $item['order'] + 1;
        } else {
            $item['order'] = 1;
        }
    }
    unset($item);

    $datetime = isset($input['datetime']) ? (string)$input['datetime'] : '';
    if ($datetime === '' && !empty($input['year'])) {
        $datetime = (string)$input['year'] . '-01-01';
    }

    $newItem = [
        'id' => $maxId + 1,
        'year' => (string)$input['year'],
        'datetime' => $datetime,
        'status' => normalize_award_status((string)($input['status'] ?? 'nominated')),
        'featured' => !empty($input['featured']),
        'published' => !empty($input['published']),
        'link' => isset($input['link']) ? trim((string)$input['link']) : '',
        'organization' => normalize_award_text($input['organization'] ?? []),
        'title' => normalize_award_text($input['title'] ?? []),
        'result' => normalize_award_text($input['result'] ?? []),
        'description' => normalize_award_text($input['description'] ?? []),
        'thumbnail' => isset($input['thumbnail']) ? trim((string)$input['thumbnail']) : '',
        'thumbnailFrame' => normalize_award_thumbnail_frame($input['thumbnailFrame'] ?? null),
        'attachments' => normalize_award_attachments($input['attachments'] ?? []),
        'order' => 0,
        'createdAt' => date('c'),
        'updatedAt' => date('c'),
    ];

    $awards[] = $newItem;

    usort($awards, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });

    if (!save_awards($awards)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($newItem, '作成しました');
}

if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || !isset($input['id'])) {
        json_error('IDが必要です');
    }

    $awards = load_awards();
    $targetId = (int)$input['id'];
    $index = null;
    foreach ($awards as $i => $row) {
        if ((int)($row['id'] ?? 0) === $targetId) {
            $index = $i;
            break;
        }
    }

    if ($index === null) {
        json_error('受賞歴が見つかりません', 404);
    }

    if (isset($input['year'])) {
        $awards[$index]['year'] = (string)$input['year'];
    }
    if (array_key_exists('datetime', $input)) {
        $awards[$index]['datetime'] = (string)$input['datetime'];
    }
    if (array_key_exists('status', $input)) {
        $awards[$index]['status'] = normalize_award_status((string)$input['status']);
    }
    if (array_key_exists('featured', $input)) {
        $awards[$index]['featured'] = !empty($input['featured']);
    }
    if (array_key_exists('published', $input)) {
        $awards[$index]['published'] = !empty($input['published']);
    }
    if (array_key_exists('link', $input)) {
        $awards[$index]['link'] = trim((string)$input['link']);
    }
    if (isset($input['organization']) && is_array($input['organization'])) {
        $awards[$index]['organization'] = normalize_award_text($input['organization']);
    }
    if (isset($input['title']) && is_array($input['title'])) {
        $awards[$index]['title'] = normalize_award_text($input['title']);
    }
    if (isset($input['result']) && is_array($input['result'])) {
        $awards[$index]['result'] = normalize_award_text($input['result']);
    }
    if (isset($input['description']) && is_array($input['description'])) {
        $awards[$index]['description'] = normalize_award_text($input['description']);
    }
    if (array_key_exists('thumbnail', $input)) {
        $awards[$index]['thumbnail'] = trim((string)$input['thumbnail']);
    }
    if (array_key_exists('thumbnailFrame', $input)) {
        $awards[$index]['thumbnailFrame'] = normalize_award_thumbnail_frame($input['thumbnailFrame']);
    }
    if (array_key_exists('attachments', $input)) {
        $awards[$index]['attachments'] = normalize_award_attachments($input['attachments']);
    }
    $awards[$index]['updatedAt'] = date('c');

    if (!save_awards($awards)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($awards[$index], '更新しました');
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || empty($input['ids']) || !is_array($input['ids'])) {
        json_error('削除するIDを指定してください');
    }

    $awards = load_awards();
    $awards = array_filter($awards, function ($item) use ($input) {
        return !in_array($item['id'], $input['ids'], true);
    });
    $awards = array_values($awards);
    usort($awards, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });

    if (!save_awards($awards)) {
        json_error('保存に失敗しました', 500);
    }

    json_success(['deleted' => count($input['ids'])], '削除しました');
}

if ($method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input) || empty($input['orders']) || !is_array($input['orders'])) {
        json_error('並び順データが必要です');
    }

    $awards = load_awards();
    $byId = [];
    foreach ($awards as $item) {
        $byId[$item['id']] = $item;
    }

    foreach ($input['orders'] as $order => $id) {
        if (isset($byId[$id])) {
            $byId[$id]['order'] = (int)$order;
        }
    }

    $newAwards = array_values($byId);
    usort($newAwards, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });

    if (!save_awards($newAwards)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($newAwards, '並び順を更新しました');
}

json_error('無効なリクエストメソッドです', 405);

<?php
// public_html/dev-admin-unified/api/posters.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$jsonPath = realpath(__DIR__ . '/../../data/posters.json');
$docsDir = realpath(__DIR__ . '/../../development/assets/docs');

const MATERIAL_TYPES = [
    '発表ポスター',
    '発表論文',
    '発表提出動画',
    '発表スライド',
];

function load_posters(): array {
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

function save_posters(array $data): bool {
    global $jsonPath;
    return file_put_contents(
        $jsonPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

function normalize_poster(array $item): array {
    if (empty($item['filePath']) && !empty($item['pdfPath'])) {
        $item['filePath'] = $item['pdfPath'];
    }
    if (!isset($item['externalUrl'])) {
        $item['externalUrl'] = '';
    }
    if (!empty($item['type'])) {
        $item['title'] = $item['type'];
    }
    return $item;
}

function sort_posters(array $posters): array {
    usort($posters, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    return $posters;
}

function store_uploaded_file(array $file): string {
    global $docsDir;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        json_error('ファイルアップロードエラー');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'mp4', 'webm', 'mov'];
    if (!in_array($ext, $allowed, true)) {
        json_error('許可されていないファイル形式です（PDF / MP4 / WEBM / MOV）');
    }

    $prefix = $ext === 'pdf' ? 'material' : 'video';
    $filename = $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $filePath = $docsDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        json_error('ファイル保存に失敗しました', 500);
    }

    return 'development/assets/docs/' . $filename;
}

function delete_material_file(?string $path): void {
    if (empty($path)) {
        return;
    }
    $fullPath = __DIR__ . '/../../' . $path;
    if (file_exists($fullPath)) {
        @unlink($fullPath);
    }
}

if ($method === 'GET') {
    $posters = array_map('normalize_poster', load_posters());

    if (!empty($_GET['search'])) {
        $search = mb_strtolower($_GET['search']);
        $posters = array_filter($posters, function ($item) use ($search) {
            $title = mb_strtolower($item['title'] ?? '');
            $description = mb_strtolower($item['description'] ?? '');
            $type = mb_strtolower($item['type'] ?? '');
            $tags = implode(' ', array_map('mb_strtolower', $item['tags'] ?? []));
            return str_contains($title, $search)
                || str_contains($description, $search)
                || str_contains($type, $search)
                || str_contains($tags, $search);
        });
    }

    if (!empty($_GET['year'])) {
        $year = (int) $_GET['year'];
        $posters = array_filter($posters, function ($item) use ($year) {
            return ($item['year'] ?? 0) === $year;
        });
    }

    if (!empty($_GET['type'])) {
        $type = $_GET['type'];
        $posters = array_filter($posters, function ($item) use ($type) {
            return ($item['type'] ?? '') === $type;
        });
    }

    if (!empty($_GET['contestId'])) {
        $contestId = (int) $_GET['contestId'];
        $posters = array_filter($posters, function ($item) use ($contestId) {
            return (int) ($item['contestId'] ?? 0) === $contestId;
        });
    }

    $sort = $_GET['sort'] ?? 'order';
    $posters = array_values($posters);
    usort($posters, function ($a, $b) use ($sort) {
        switch ($sort) {
            case 'date-asc':
            case 'year-asc':
                return ($a['year'] ?? 0) <=> ($b['year'] ?? 0);
            case 'date-desc':
            case 'year-desc':
                return ($b['year'] ?? 0) <=> ($a['year'] ?? 0);
            case 'title-asc':
                return strcmp($a['title'] ?? '', $b['title'] ?? '');
            case 'title-desc':
                return strcmp($b['title'] ?? '', $a['title'] ?? '');
            case 'order':
            default:
                $orderA = $a['order'] ?? 999999;
                $orderB = $b['order'] ?? 999999;
                return $orderA <=> $orderB;
        }
    });

    json_success($posters);
}

if ($method === 'POST' && empty($_POST['_method'])) {
    $filePath = trim($_POST['filePath'] ?? '');
    $externalUrl = trim($_POST['externalUrl'] ?? '');

    if (!empty($_FILES['file'])) {
        $filePath = store_uploaded_file($_FILES['file']);
    } elseif (!empty($_FILES['pdf'])) {
        $filePath = store_uploaded_file($_FILES['pdf']);
    }

    if (empty($_POST['contestId']) || empty($_POST['type'])) {
        json_error('コンテストと種別は必須です');
    }

    if (!in_array($_POST['type'], MATERIAL_TYPES, true)) {
        json_error('無効な資料種別です');
    }

    if ($filePath === '' && $externalUrl === '') {
        json_error('ファイルまたは外部URLのいずれかが必要です');
    }

    $posters = load_posters();
    $maxId = empty($posters) ? 0 : max(array_column($posters, 'id'));

    foreach ($posters as &$item) {
        $item['order'] = ($item['order'] ?? 0) + 1;
    }
    unset($item);

    $tags = !empty($_POST['tags']) ? array_map('trim', explode(',', $_POST['tags'])) : [];

    $newItem = [
        'id' => $maxId + 1,
        'contestId' => (int) $_POST['contestId'],
        'title' => $_POST['type'],
        'year' => 0,
        'type' => $_POST['type'],
        'filePath' => $filePath,
        'externalUrl' => $externalUrl,
        'description' => '',
        'order' => 0,
        'tags' => $tags,
        'createdAt' => date('c'),
        'updatedAt' => date('c'),
    ];

    $posters[] = $newItem;
    $posters = sort_posters($posters);

    if (!save_posters($posters)) {
        json_error('保存に失敗しました', 500);
    }

    json_success(normalize_poster($newItem), '作成しました');
}

if ($method === 'PUT' || ($method === 'POST' && !empty($_POST['_method']) && $_POST['_method'] === 'PUT')) {
    if ($method === 'POST') {
        $id = (int) ($_POST['id'] ?? 0);
        $input = $_POST;
    } else {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $id = (int) ($input['id'] ?? 0);
    }

    if ($id === 0) {
        json_error('IDが必要です');
    }

    $posters = load_posters();
    $index = array_search($id, array_column($posters, 'id'), true);

    if ($index === false) {
        json_error('資料が見つかりません', 404);
    }

    if (!empty($_FILES['file'])) {
        delete_material_file($posters[$index]['filePath'] ?? $posters[$index]['pdfPath'] ?? null);
        $posters[$index]['filePath'] = store_uploaded_file($_FILES['file']);
    } elseif (!empty($_FILES['pdf'])) {
        delete_material_file($posters[$index]['filePath'] ?? $posters[$index]['pdfPath'] ?? null);
        $posters[$index]['filePath'] = store_uploaded_file($_FILES['pdf']);
    }

    if (isset($input['contestId'])) {
        $posters[$index]['contestId'] = (int) $input['contestId'];
    }
    $posters[$index]['year'] = 0;
    $posters[$index]['description'] = '';
    if (isset($input['type'])) {
        if (!in_array($input['type'], MATERIAL_TYPES, true)) {
            json_error('無効な資料種別です');
        }
        $posters[$index]['type'] = $input['type'];
        $posters[$index]['title'] = $input['type'];
    }
    if (isset($input['externalUrl'])) {
        $posters[$index]['externalUrl'] = trim($input['externalUrl']);
    }
    if (isset($input['filePath'])) {
        $posters[$index]['filePath'] = trim($input['filePath']);
    }
    if (isset($input['tags'])) {
        $tags = is_array($input['tags']) ? $input['tags'] : explode(',', $input['tags']);
        $posters[$index]['tags'] = array_map('trim', $tags);
    }

    $posters[$index]['updatedAt'] = date('c');

    if (!save_posters($posters)) {
        json_error('保存に失敗しました', 500);
    }

    json_success(normalize_poster($posters[$index]), '更新しました');
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    if (empty($input['ids']) || !is_array($input['ids'])) {
        json_error('削除するIDを指定してください');
    }

    $posters = load_posters();

    foreach ($posters as $poster) {
        if (in_array($poster['id'], $input['ids'], true)) {
            delete_material_file($poster['filePath'] ?? $poster['pdfPath'] ?? null);
        }
    }

    $posters = array_values(array_filter($posters, function ($item) use ($input) {
        return !in_array($item['id'], $input['ids'], true);
    }));

    $posters = sort_posters($posters);

    if (!save_posters($posters)) {
        json_error('保存に失敗しました', 500);
    }

    json_success([
        'deleted' => count($input['ids']),
    ], '削除しました');
}

if ($method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    if (empty($input['orders']) || !is_array($input['orders'])) {
        json_error('並び順データが必要です');
    }

    $posters = load_posters();
    $postersById = [];
    foreach ($posters as $item) {
        $postersById[$item['id']] = $item;
    }

    $newPosters = [];
    foreach ($input['orders'] as $order => $id) {
        if (isset($postersById[$id])) {
            $postersById[$id]['order'] = $order;
            $newPosters[] = $postersById[$id];
        }
    }

    $newPosters = sort_posters($newPosters);

    if (!save_posters($newPosters)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($newPosters, '並び順を更新しました');
}

json_error('無効なリクエストメソッドです', 405);

<?php
// public_html/dev-admin-unified/api/contests.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$jsonPath = realpath(__DIR__ . '/../../data/contests.json');
$materialsPath = realpath(__DIR__ . '/../../data/posters.json');
$imagesDir = realpath(__DIR__ . '/../../development/assets/images');

function load_contests(): array {
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

function save_contests(array $data): bool {
    global $jsonPath;
    return file_put_contents(
        $jsonPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

function load_materials(): array {
    global $materialsPath;
    if (!file_exists($materialsPath)) {
        return [];
    }
    $raw = file_get_contents($materialsPath);
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function save_materials(array $data): bool {
    global $materialsPath;
    return file_put_contents(
        $materialsPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

function sort_by_order(array $items): array {
    usort($items, function ($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    return $items;
}

function store_contest_thumb(array $file): string {
    global $imagesDir;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        json_error('サムネイルのアップロードに失敗しました');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['png', 'jpg', 'jpeg', 'webp'], true)) {
        json_error('サムネイルは PNG / JPG / WEBP のみ許可されています');
    }

    $filename = 'contest_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destPath = $imagesDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        json_error('サムネイルの保存に失敗しました', 500);
    }

    return 'development/assets/images/' . $filename;
}

function delete_contest_thumb(?string $path): void {
    if (empty($path)) {
        return;
    }

    $fullPath = __DIR__ . '/../../' . ltrim($path, '/');
    if (file_exists($fullPath)) {
        @unlink($fullPath);
    }
}

if ($method === 'GET') {
    $contests = load_contests();

    if (!empty($_GET['search'])) {
        $search = mb_strtolower($_GET['search']);
        $contests = array_filter($contests, function ($item) use ($search) {
            $name = mb_strtolower($item['name'] ?? '');
            $description = mb_strtolower($item['description'] ?? '');
            return str_contains($name, $search) || str_contains($description, $search);
        });
    }

    if (!empty($_GET['year'])) {
        $year = (int) $_GET['year'];
        $contests = array_filter($contests, function ($item) use ($year) {
            return ($item['year'] ?? 0) === $year;
        });
    }

    $contests = sort_by_order(array_values($contests));
    json_success($contests);
}

if ($method === 'POST' && empty($_POST['_method'])) {
    if (empty($_POST['name'])) {
        json_error('コンテスト名は必須です');
    }

    $thumbPath = '';
    if (!empty($_FILES['thumbFile']) && $_FILES['thumbFile']['error'] !== UPLOAD_ERR_NO_FILE) {
        $thumbPath = store_contest_thumb($_FILES['thumbFile']);
    }

    $contests = load_contests();
    $maxId = empty($contests) ? 0 : max(array_column($contests, 'id'));

    foreach ($contests as &$item) {
        $item['order'] = ($item['order'] ?? 0) + 1;
    }
    unset($item);

    $newItem = [
        'id' => $maxId + 1,
        'name' => trim($_POST['name']),
        'description' => trim($_POST['description'] ?? ''),
        'year' => 0,
        'url' => '',
        'thumbPath' => $thumbPath,
        'order' => 0,
        'createdAt' => date('c'),
        'updatedAt' => date('c'),
    ];

    $contests[] = $newItem;
    $contests = sort_by_order($contests);

    if (!save_contests($contests)) {
        if ($thumbPath !== '') {
            delete_contest_thumb($thumbPath);
        }
        json_error('保存に失敗しました', 500);
    }

    json_success($newItem, '作成しました');
}

if ($method === 'PUT' || ($method === 'POST' && !empty($_POST['_method']) && $_POST['_method'] === 'PUT')) {
    $input = $_POST;
    $id = (int) ($input['id'] ?? 0);

    if ($id === 0) {
        json_error('IDが必要です');
    }

    $contests = load_contests();
    $index = array_search($id, array_column($contests, 'id'), true);

    if ($index === false) {
        json_error('コンテストが見つかりません', 404);
    }

    if (!empty($_FILES['thumbFile']) && $_FILES['thumbFile']['error'] !== UPLOAD_ERR_NO_FILE) {
        delete_contest_thumb($contests[$index]['thumbPath'] ?? null);
        $contests[$index]['thumbPath'] = store_contest_thumb($_FILES['thumbFile']);
    }

    if (isset($input['name'])) {
        $contests[$index]['name'] = trim($input['name']);
    }
    if (isset($input['description'])) {
        $contests[$index]['description'] = trim($input['description']);
    }
    $contests[$index]['year'] = 0;
    $contests[$index]['url'] = '';
    $contests[$index]['updatedAt'] = date('c');

    if (!save_contests($contests)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($contests[$index], '更新しました');
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($input['ids']) || !is_array($input['ids'])) {
        json_error('削除するIDを指定してください');
    }

    $contests = load_contests();
    $materials = load_materials();
    $ids = array_map('intval', $input['ids']);

    foreach ($ids as $contestId) {
        foreach ($materials as $material) {
            if ((int) ($material['contestId'] ?? 0) === $contestId) {
                json_error('資料が紐づいているコンテストは削除できません。先に資料を削除してください。');
            }
        }
    }

    foreach ($contests as $contest) {
        if (in_array((int) $contest['id'], $ids, true)) {
            delete_contest_thumb($contest['thumbPath'] ?? null);
        }
    }

    $contests = array_values(array_filter($contests, function ($item) use ($ids) {
        return !in_array((int) $item['id'], $ids, true);
    }));

    $contests = sort_by_order($contests);

    if (!save_contests($contests)) {
        json_error('保存に失敗しました', 500);
    }

    json_success(['deleted' => count($ids)], '削除しました');
}

if ($method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($input['orders']) || !is_array($input['orders'])) {
        json_error('並び順データが必要です');
    }

    $contests = load_contests();
    $contestsById = [];
    foreach ($contests as $item) {
        $contestsById[$item['id']] = $item;
    }

    $newContests = [];
    foreach ($input['orders'] as $order => $id) {
        if (isset($contestsById[$id])) {
            $contestsById[$id]['order'] = $order;
            $newContests[] = $contestsById[$id];
        }
    }

    $newContests = sort_by_order($newContests);

    if (!save_contests($newContests)) {
        json_error('保存に失敗しました', 500);
    }

    json_success($newContests, '並び順を更新しました');
}

json_error('無効なリクエストメソッドです', 405);

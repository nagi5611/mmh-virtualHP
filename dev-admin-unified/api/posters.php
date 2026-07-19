<?php
// public_html/dev-admin-unified/api/posters.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$jsonPath = realpath(__DIR__ . '/../../data/posters.json');
$docsDir = realpath(__DIR__ . '/../../development/assets/docs');

// JSONファイル読み込み
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

// JSONファイル保存
function save_posters(array $data): bool {
    global $jsonPath;
    return file_put_contents(
        $jsonPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

// GET: 一覧取得
if ($method === 'GET') {
    $posters = load_posters();
    
    // 検索
    if (!empty($_GET['search'])) {
        $search = mb_strtolower($_GET['search']);
        $posters = array_filter($posters, function($item) use ($search) {
            $title = mb_strtolower($item['title'] ?? '');
            $contestName = mb_strtolower($item['contestName'] ?? '');
            $description = mb_strtolower($item['description'] ?? '');
            $tags = implode(' ', array_map('mb_strtolower', $item['tags'] ?? []));
            
            return str_contains($title, $search) 
                || str_contains($contestName, $search)
                || str_contains($description, $search)
                || str_contains($tags, $search);
        });
    }
    
    // 年フィルタ
    if (!empty($_GET['year'])) {
        $year = (int)$_GET['year'];
        $posters = array_filter($posters, function($item) use ($year) {
            return ($item['year'] ?? 0) === $year;
        });
    }
    
    // タイプフィルタ
    if (!empty($_GET['type'])) {
        $type = $_GET['type'];
        $posters = array_filter($posters, function($item) use ($type) {
            return ($item['type'] ?? '') === $type;
        });
    }
    
    // ソート
    $sort = $_GET['sort'] ?? 'order';
    usort($posters, function($a, $b) use ($sort) {
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
                // orderの昇順（orderが小さいほど新しい）
                $orderA = $a['order'] ?? 999999;
                $orderB = $b['order'] ?? 999999;
                return $orderA <=> $orderB;
        }
    });
    
    json_success(array_values($posters));
}

// POST: 新規作成（マルチパート対応）
if ($method === 'POST') {
    // マルチパートデータの場合
    if (!empty($_FILES['pdf'])) {
        $file = $_FILES['pdf'];
        
        // バリデーション
        if ($file['error'] !== UPLOAD_ERR_OK) {
            json_error('ファイルアップロードエラー');
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            json_error('PDFファイルのみ許可されています');
        }
        
        // ファイル保存
        $filename = 'poster_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
        $filePath = $docsDir . DIRECTORY_SEPARATOR . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            json_error('ファイル保存に失敗しました', 500);
        }
        
        $pdfPath = 'development/assets/docs/' . $filename;
    } else {
        $pdfPath = $_POST['pdfPath'] ?? '';
    }
    
    // データ検証
    if (empty($_POST['contestName']) || empty($_POST['title'])) {
        json_error('コンテスト名とタイトルは必須です');
    }
    
    $posters = load_posters();
    $maxId = empty($posters) ? 0 : max(array_column($posters, 'id'));
    
    // 新規ポスターはorder: 0（最新）を設定
    // 既存のすべてのポスターのorderを+1する（古くなる）
    foreach ($posters as &$item) {
        if (isset($item['order'])) {
            $item['order'] = $item['order'] + 1;
        } else {
            $item['order'] = 1;
        }
    }
    unset($item);
    
    $tags = !empty($_POST['tags']) ? explode(',', $_POST['tags']) : [];
    $tags = array_map('trim', $tags);
    
    $newItem = [
        'id' => $maxId + 1,
        'contestName' => $_POST['contestName'],
        'title' => $_POST['title'],
        'year' => (int)($_POST['year'] ?? date('Y')),
        'type' => $_POST['type'] ?? 'ポスター',
        'pdfPath' => $pdfPath,
        'description' => $_POST['description'] ?? '',
        'order' => 0, // 最新のポスターはorder: 0
        'tags' => $tags,
        'createdAt' => date('c'),
        'updatedAt' => date('c')
    ];
    
    $posters[] = $newItem;
    
    // orderの昇順でソート（orderが小さいほど新しい）
    usort($posters, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB; // 昇順（order: 0が最初）
    });
    
    if (!save_posters($posters)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($newItem, '作成しました');
}

// PUT: 更新（マルチパート対応）
if ($method === 'PUT' || ($method === 'POST' && !empty($_POST['_method']) && $_POST['_method'] === 'PUT')) {
    if ($method === 'POST') {
        $id = (int)($_POST['id'] ?? 0);
    } else {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
    }
    
    if ($id === 0) {
        json_error('IDが必要です');
    }
    
    $posters = load_posters();
    $index = array_search($id, array_column($posters, 'id'));
    
    if ($index === false) {
        json_error('ポスターが見つかりません', 404);
    }
    
    // PDFファイル更新
    if (!empty($_FILES['pdf'])) {
        $file = $_FILES['pdf'];
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                // 古いファイル削除
                if (!empty($posters[$index]['pdfPath'])) {
                    $oldPath = __DIR__ . '/../../' . $posters[$index]['pdfPath'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                
                // 新ファイル保存
                $filename = 'poster_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
                $filePath = $docsDir . DIRECTORY_SEPARATOR . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $posters[$index]['pdfPath'] = 'development/assets/docs/' . $filename;
                }
            }
        }
    }
    
    // データ更新
    if ($method === 'POST') {
        if (!empty($_POST['contestName'])) $posters[$index]['contestName'] = $_POST['contestName'];
        if (!empty($_POST['title'])) $posters[$index]['title'] = $_POST['title'];
        if (isset($_POST['year'])) $posters[$index]['year'] = (int)$_POST['year'];
        if (isset($_POST['type'])) $posters[$index]['type'] = $_POST['type'];
        if (isset($_POST['description'])) $posters[$index]['description'] = $_POST['description'];
        if (isset($_POST['tags'])) {
            $tags = explode(',', $_POST['tags']);
            $posters[$index]['tags'] = array_map('trim', $tags);
        }
    } else {
        if (isset($input['contestName'])) $posters[$index]['contestName'] = $input['contestName'];
        if (isset($input['title'])) $posters[$index]['title'] = $input['title'];
        if (isset($input['year'])) $posters[$index]['year'] = (int)$input['year'];
        if (isset($input['type'])) $posters[$index]['type'] = $input['type'];
        if (isset($input['description'])) $posters[$index]['description'] = $input['description'];
        if (isset($input['tags'])) $posters[$index]['tags'] = $input['tags'];
    }
    
    $posters[$index]['updatedAt'] = date('c');
    
    if (!save_posters($posters)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($posters[$index], '更新しました');
}

// DELETE: 削除
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['ids']) || !is_array($input['ids'])) {
        json_error('削除するIDを指定してください');
    }
    
    $posters = load_posters();
    $deletedFiles = [];
    
    // ファイル削除
    foreach ($posters as $poster) {
        if (in_array($poster['id'], $input['ids'], true)) {
            if (!empty($poster['pdfPath'])) {
                $filePath = __DIR__ . '/../../' . $poster['pdfPath'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                    $deletedFiles[] = $filePath;
                }
            }
        }
    }
    
    // データ削除
    $posters = array_filter($posters, function($item) use ($input) {
        return !in_array($item['id'], $input['ids'], true);
    });
    
    // orderの昇順でソート（orderが小さいほど新しい）
    $posters = array_values($posters);
    usort($posters, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    
    if (!save_posters($posters)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success([
        'deleted' => count($input['ids']),
        'files' => count($deletedFiles)
    ], '削除しました');
}

// PATCH: 並び替え
if ($method === 'PATCH') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['orders']) || !is_array($input['orders'])) {
        json_error('並び順データが必要です');
    }
    
    $posters = load_posters();
    
    // IDをキーとした配列に変換
    $postersById = [];
    foreach ($posters as $item) {
        $postersById[$item['id']] = $item;
    }
    
    // 新しい順序で再構築
    $newPosters = [];
    foreach ($input['orders'] as $order => $id) {
        if (isset($postersById[$id])) {
            $postersById[$id]['order'] = $order;
            $newPosters[] = $postersById[$id];
        }
    }
    
    // orderの昇順でソート（orderが小さいほど新しい）
    usort($newPosters, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    
    if (!save_posters($newPosters)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($newPosters, '並び順を更新しました');
}

json_error('無効なリクエストメソッドです', 405);

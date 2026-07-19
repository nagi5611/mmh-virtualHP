<?php
// public_html/dev-admin-unified/api/models.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$jsonPath = realpath(__DIR__ . '/../../development/assets/models.json');
$docsDir = realpath(__DIR__ . '/../../development/assets/docs');
$imagesDir = realpath(__DIR__ . '/../../development/assets/images');

// JSONファイル読み込み
function load_models(): array {
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
function save_models(array $data): bool {
    global $jsonPath;
    return file_put_contents(
        $jsonPath,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    ) !== false;
}

// GET: 一覧取得
if ($method === 'GET') {
    $models = load_models();
    
    // orderフィールドがない場合は追加
    foreach ($models as $i => $model) {
        if (!isset($model['order'])) {
            $models[$i]['order'] = $i;
        }
    }
    
    // 検索
    if (!empty($_GET['search'])) {
        $search = mb_strtolower($_GET['search']);
        $models = array_filter($models, function($item) use ($search) {
            $title = mb_strtolower($item['title'] ?? '');
            $category = mb_strtolower($item['category'] ?? '');
            $description = mb_strtolower($item['description'] ?? '');
            
            return str_contains($title, $search) 
                || str_contains($category, $search)
                || str_contains($description, $search);
        });
    }
    
    // カテゴリフィルタ
    if (!empty($_GET['category'])) {
        $category = $_GET['category'];
        $models = array_filter($models, function($item) use ($category) {
            return ($item['category'] ?? '') === $category;
        });
    }
    
    // ソート
    $sort = $_GET['sort'] ?? 'order';
    usort($models, function($a, $b) use ($sort) {
        switch ($sort) {
            case 'title-asc':
                return strcmp($a['title'] ?? '', $b['title'] ?? '');
            case 'title-desc':
                return strcmp($b['title'] ?? '', $a['title'] ?? '');
            case 'date-asc':
            case 'date-desc':
                return ($b['id'] ?? 0) <=> ($a['id'] ?? 0);
            default:
                return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
        }
    });
    
    json_success(array_values($models));
}

// POST: 新規作成
if ($method === 'POST') {
    // バリデーション
    if (empty($_POST['title']) || empty($_POST['category'])) {
        json_error('タイトルとカテゴリは必須です');
    }
    
    $allowedCategories = ['high', 'mid', 'low', 'misc'];
    if (!in_array($_POST['category'], $allowedCategories, true)) {
        json_error('無効なカテゴリです');
    }
    
    // GLBファイル処理
    if (empty($_FILES['glbFile']) || $_FILES['glbFile']['error'] !== UPLOAD_ERR_OK) {
        json_error('GLBファイルが必要です');
    }
    
    $glbFile = $_FILES['glbFile'];
    $glbExt = strtolower(pathinfo($glbFile['name'], PATHINFO_EXTENSION));
    if (!in_array($glbExt, ['glb', 'gltf'], true)) {
        json_error('GLBまたはGLTFファイルのみ許可されています');
    }
    
    // サムネイル処理
    if (empty($_FILES['thumbFile']) || $_FILES['thumbFile']['error'] !== UPLOAD_ERR_OK) {
        json_error('サムネイル画像が必要です');
    }
    
    $thumbFile = $_FILES['thumbFile'];
    $thumbExt = strtolower(pathinfo($thumbFile['name'], PATHINFO_EXTENSION));
    if (!in_array($thumbExt, ['png', 'jpg', 'jpeg', 'webp'], true)) {
        json_error('画像ファイルのみ許可されています');
    }
    
    // ファイル保存
    $glbFilename = 'model_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $glbExt;
    $glbPath = $docsDir . DIRECTORY_SEPARATOR . $glbFilename;
    
    $thumbFilename = 'thumb_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $thumbExt;
    $thumbPath = $imagesDir . DIRECTORY_SEPARATOR . $thumbFilename;
    
    if (!move_uploaded_file($glbFile['tmp_name'], $glbPath)) {
        json_error('GLBファイル保存に失敗しました', 500);
    }
    
    if (!move_uploaded_file($thumbFile['tmp_name'], $thumbPath)) {
        @unlink($glbPath);
        json_error('サムネイル保存に失敗しました', 500);
    }
    
    // ファイルサイズ計算
    $bytes = filesize($glbPath);
    $mb = round($bytes / 1048576, 2);
    $fileSize = "GLB {$mb}MB";
    
    // ポリゴン数整形
    $polyNum = $_POST['polyNum'] ?? '';
    $poly = '';
    if ($polyNum !== '') {
        $poly = '約' . $polyNum . '万ポリ';
    }
    
    // テクスチャ設定
    $hasTexture = !empty($_POST['hasTexture']) && $_POST['hasTexture'] === '1';
    $note = $hasTexture ? 'テクスチャ有り' : 'テクスチャ無し';
    
    $models = load_models();
    $maxId = empty($models) ? 0 : max(array_column($models, 'id'));
    
    // 新規モデルはorder: 0（最新）を設定
    // 既存のすべてのモデルのorderを+1する（古くなる）
    foreach ($models as &$item) {
        if (isset($item['order'])) {
            $item['order'] = $item['order'] + 1;
        } else {
            $item['order'] = 1;
        }
    }
    unset($item);
    
    $newModel = [
        'id' => $maxId + 1,
        'title' => $_POST['title'],
        'category' => $_POST['category'],
        'poly' => $poly,
        'fileSize' => $fileSize,
        'note' => $note,
        'description' => $_POST['description'] ?? '',
        'glbPath' => 'assets/docs/' . $glbFilename,
        'thumbPath' => 'assets/images/' . $thumbFilename,
        'order' => 0 // 最新のモデルはorder: 0
    ];
    
    $models[] = $newModel;
    
    // orderの昇順でソート（orderが小さいほど新しい）
    usort($models, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    
    if (!save_models($models)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($newModel, '作成しました');
}

// PUT: 更新
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
    
    $models = load_models();
    $index = array_search($id, array_column($models, 'id'));
    
    if ($index === false) {
        json_error('モデルが見つかりません', 404);
    }
    
    // GLBファイル更新
    if (!empty($_FILES['glbFile']) && $_FILES['glbFile']['error'] === UPLOAD_ERR_OK) {
        $glbFile = $_FILES['glbFile'];
        $glbExt = strtolower(pathinfo($glbFile['name'], PATHINFO_EXTENSION));
        
        if (in_array($glbExt, ['glb', 'gltf'], true)) {
            // 古いファイル削除
            if (!empty($models[$index]['glbPath'])) {
                $oldPath = __DIR__ . '/../../development/' . $models[$index]['glbPath'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            // 新ファイル保存
            $glbFilename = 'model_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $glbExt;
            $glbPath = $docsDir . DIRECTORY_SEPARATOR . $glbFilename;
            
            if (move_uploaded_file($glbFile['tmp_name'], $glbPath)) {
                $models[$index]['glbPath'] = 'assets/docs/' . $glbFilename;
                
                // ファイルサイズ更新
                $bytes = filesize($glbPath);
                $mb = round($bytes / 1048576, 2);
                $models[$index]['fileSize'] = "GLB {$mb}MB";
            }
        }
    }
    
    // サムネイル更新
    if (!empty($_FILES['thumbFile']) && $_FILES['thumbFile']['error'] === UPLOAD_ERR_OK) {
        $thumbFile = $_FILES['thumbFile'];
        $thumbExt = strtolower(pathinfo($thumbFile['name'], PATHINFO_EXTENSION));
        
        if (in_array($thumbExt, ['png', 'jpg', 'jpeg', 'webp'], true)) {
            // 古いファイル削除
            if (!empty($models[$index]['thumbPath'])) {
                $oldPath = __DIR__ . '/../../development/' . $models[$index]['thumbPath'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            
            // 新ファイル保存
            $thumbFilename = 'thumb_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $thumbExt;
            $thumbPath = $imagesDir . DIRECTORY_SEPARATOR . $thumbFilename;
            
            if (move_uploaded_file($thumbFile['tmp_name'], $thumbPath)) {
                $models[$index]['thumbPath'] = 'assets/images/' . $thumbFilename;
            }
        }
    }
    
    // データ更新
    if ($method === 'POST') {
        if (isset($_POST['title'])) $models[$index]['title'] = $_POST['title'];
        if (isset($_POST['category'])) $models[$index]['category'] = $_POST['category'];
        if (isset($_POST['description'])) $models[$index]['description'] = $_POST['description'];
        
        if (isset($_POST['polyNum']) && $_POST['polyNum'] !== '') {
            $models[$index]['poly'] = '約' . $_POST['polyNum'] . '万ポリ';
        }
        
        if (isset($_POST['hasTexture'])) {
            $models[$index]['note'] = $_POST['hasTexture'] === '1' ? 'テクスチャ有り' : 'テクスチャ無し';
        }
    } else {
        if (isset($input['title'])) $models[$index]['title'] = $input['title'];
        if (isset($input['category'])) $models[$index]['category'] = $input['category'];
        if (isset($input['description'])) $models[$index]['description'] = $input['description'];
        if (isset($input['poly'])) $models[$index]['poly'] = $input['poly'];
        if (isset($input['note'])) $models[$index]['note'] = $input['note'];
    }
    
    if (!save_models($models)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($models[$index], '更新しました');
}

// DELETE: 削除
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input['ids']) || !is_array($input['ids'])) {
        json_error('削除するIDを指定してください');
    }
    
    $models = load_models();
    $deletedFiles = [];
    
    // ファイル削除
    foreach ($models as $model) {
        if (in_array($model['id'], $input['ids'], true)) {
            // GLB削除
            if (!empty($model['glbPath'])) {
                $glbPath = __DIR__ . '/../../development/' . $model['glbPath'];
                if (file_exists($glbPath)) {
                    @unlink($glbPath);
                    $deletedFiles[] = $glbPath;
                }
            }
            
            // サムネイル削除
            if (!empty($model['thumbPath'])) {
                $thumbPath = __DIR__ . '/../../development/' . $model['thumbPath'];
                if (file_exists($thumbPath)) {
                    @unlink($thumbPath);
                    $deletedFiles[] = $thumbPath;
                }
            }
        }
    }
    
    // データ削除
    $models = array_filter($models, function($item) use ($input) {
        return !in_array($item['id'], $input['ids'], true);
    });
    
    // orderの昇順でソート（orderが小さいほど新しい）
    $models = array_values($models);
    usort($models, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    
    if (!save_models($models)) {
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
    
    $models = load_models();
    
    // IDをキーとした配列に変換
    $modelsById = [];
    foreach ($models as $item) {
        $modelsById[$item['id']] = $item;
    }
    
    // 新しい順序で再構築
    $newModels = [];
    foreach ($input['orders'] as $order => $id) {
        if (isset($modelsById[$id])) {
            $modelsById[$id]['order'] = $order;
            $newModels[] = $modelsById[$id];
        }
    }
    
    // orderの昇順でソート（orderが小さいほど新しい）
    usort($newModels, function($a, $b) {
        $orderA = $a['order'] ?? 999999;
        $orderB = $b['order'] ?? 999999;
        return $orderA <=> $orderB;
    });
    
    if (!save_models($newModels)) {
        json_error('保存に失敗しました', 500);
    }
    
    json_success($newModels, '並び順を更新しました');
}

json_error('無効なリクエストメソッドです', 405);

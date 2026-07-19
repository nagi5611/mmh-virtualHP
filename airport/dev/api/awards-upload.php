<?php
// public_html/airport/dev/api/awards-upload.php
require __DIR__ . '/../config.php';
admin_require_login();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

$imagesDir = realpath(__DIR__ . '/../../images/awards');
$filesDir = realpath(__DIR__ . '/../../files/awards');

if ($imagesDir === false || $filesDir === false) {
    json_error('アップロード先ディレクトリが見つかりません', 500);
}

/**
 * 受賞歴サムネの既存ファイルを削除（バージョン付きファイル名にも対応）
 */
function delete_award_thumbnail_files(int $awardId, string $imagesDir): void {
    $pattern = $imagesDir . DIRECTORY_SEPARATOR . 'award-' . $awardId . '-thumb*';
    foreach (glob($pattern) ?: [] as $oldFile) {
        if (is_file($oldFile)) {
            @unlink($oldFile);
        }
    }
}

if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        json_error('無効なJSONです');
    }
    $awardId = isset($input['awardId']) ? (int)$input['awardId'] : 0;
    $kind = isset($input['kind']) ? (string)$input['kind'] : '';
    if ($awardId <= 0) {
        json_error('awardId が必要です');
    }
    if ($kind !== 'thumbnail') {
        json_error('削除は thumbnail のみ対応しています');
    }
    delete_award_thumbnail_files($awardId, $imagesDir);
    json_success(['thumbnail' => ''], 'サムネイルを削除しました');
}

if ($method !== 'POST') {
    json_error('無効なリクエストメソッドです', 405);
}

$awardId = isset($_POST['awardId']) ? (int)$_POST['awardId'] : 0;
$kind = isset($_POST['kind']) ? (string)$_POST['kind'] : '';

if ($awardId <= 0) {
    json_error('awardId が必要です');
}

if (!in_array($kind, ['thumbnail', 'attachment'], true)) {
    json_error('kind は thumbnail または attachment です');
}

if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    json_error('ファイルのアップロードに失敗しました');
}

$file = $_FILES['file'];
$originalName = (string)($file['name'] ?? 'file');
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$mime = (string)($file['type'] ?? 'application/octet-stream');
$label = pathinfo($originalName, PATHINFO_FILENAME);

if ($kind === 'thumbnail') {
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $allowed, true)) {
        json_error('サムネイルは JPG, PNG, WebP, GIF のみ許可されています');
    }
    if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
        json_error('サムネイルは8MB以下にしてください');
    }

    delete_award_thumbnail_files($awardId, $imagesDir);

    $filename = 'award-' . $awardId . '-thumb-' . time() . '.' . $ext;
    $dest = $imagesDir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        json_error('ファイル保存に失敗しました', 500);
    }

    $relativeUrl = 'images/awards/' . $filename;
    json_success([
        'thumbnail' => $relativeUrl,
        'url' => $relativeUrl,
    ], 'サムネイルをアップロードしました');
}

$allowedAttach = ['pdf', 'mp4', 'webm', 'mov', 'doc', 'docx', 'ppt', 'pptx'];
if (!in_array($ext, $allowedAttach, true)) {
    json_error('添付は PDF, 動画(MP4/WebM/MOV), Office ファイルのみ許可されています');
}
if (($file['size'] ?? 0) > 200 * 1024 * 1024) {
    json_error('添付ファイルは200MB以下にしてください');
}

$filename = 'award-' . $awardId . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest = $filesDir . DIRECTORY_SEPARATOR . $filename;
if (!move_uploaded_file($file['tmp_name'], $dest)) {
    json_error('ファイル保存に失敗しました', 500);
}

$relativeUrl = 'files/awards/' . $filename;
$type = in_array($ext, ['mp4', 'webm', 'mov'], true) ? 'video' : ($ext === 'pdf' ? 'pdf' : 'file');

json_success([
    'attachment' => [
        'id' => (int)(microtime(true) * 1000),
        'label' => $label,
        'url' => $relativeUrl,
        'type' => $type,
        'mime' => $mime,
    ],
    'url' => $relativeUrl,
], 'ファイルをアップロードしました');

<?php
// public_html/diorama/dev/login.php
require __DIR__ . '/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

    if ($username === '' || $password === '') {
        $error = 'ユーザー名とパスワードを入力してください。';
    } elseif (admin_login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'ユーザー名またはパスワードが正しくありません。';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン - ジオラマお知らせ管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            color: #202124;
        }
        .login-wrapper { width: 100%; max-width: 450px; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-header { text-align: center; margin-bottom: 48px; }
        .login-header h1 { font-size: 24px; font-weight: 400; color: #202124; margin-bottom: 8px; }
        .login-header p { font-size: 16px; color: #5f6368; font-weight: 400; }
        .login-card {
            background: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            padding: 48px 40px 36px;
        }
        .form-group { margin-bottom: 24px; }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #202124;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 13px 15px;
            font-size: 16px;
            border: 1px solid #dadce0;
            border-radius: 4px;
            background: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }
        .form-group input:hover { border-color: #80868b; }
        .form-group input:focus {
            outline: none;
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }
        .error-message {
            background: #fce8e6;
            color: #c5221f;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .error-message::before { content: "⚠"; font-size: 18px; }
        .btn-primary {
            width: 100%;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            color: #ffffff;
            background: #1a73e8;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: inherit;
            margin-top: 8px;
            min-height: 48px;
        }
        .btn-primary:hover { background: #1557b0; }
        @media (max-width: 480px) {
            .login-card { padding: 32px 24px 28px; border: none; }
            .login-header { margin-bottom: 32px; }
            .login-header h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-header">
            <h1>ログイン</h1>
            <p>ジオラマサイト · お知らせ管理</p>
        </div>
        <div class="login-card">
            <?php if ($error !== ''): ?>
                <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" autocomplete="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
                <button type="submit" class="btn-primary">ログイン</button>
            </form>
        </div>
    </div>
</body>
</html>

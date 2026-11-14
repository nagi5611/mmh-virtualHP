<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="copyright" content="Copyright Xserver Inc.">
<title>メールフォーム ログインページ</title>
<link type="text/css" rel="stylesheet" href="./css/admin.css?ver=1.3.1" />
</head>

<body>
<div id="wrapper">
    <header id="header" class="clearfix">
        <h1 class="logo"><a href="./"><img src="images/logo.png" alt="エックスサーバー">メールフォーム設定</a></h1>
    </header>
    <!-- /#header -->
    <div id="main">
    <form name="main_menu" method="post" action="./">
        <div id="basic_conf" class="setting_section section">
            <h2 class="section__ttl">ログイン</h2>
            <div class="section__body">
                <table class="table" summary="ログイン" cellspacing="0">
                    <tr>
                        <th scope="row">ユーザーID</th>
                        <td><input type="text" name="username" size="40" /></td>
                    </tr>
                    <tr>
                        <th scope="row">パスワード</th>
                        <td><input type="password" name="password" size="40" /></td>
                    </tr>
                </table>
                {$error_txt}
            </div>
            
        </div>
        

        <div class="button_box tac">
            <input type="submit" name="login_button" value="ログイン" />
        </div>
        <!-- /button_box -->
    </form>   
    </div>
    <!-- /#main -->
    
    <footer id="footer">
        &copy; 2012-2018 XSERVER Inc. All rights reserved.&nbsp;&nbsp;Custom MailForm {$version}
    </footer>
    <!-- /#footer -->
</div>
<!-- /#wrapper -->
</body>
</html>
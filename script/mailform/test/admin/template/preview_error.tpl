<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="copyright" content="Copyright Xserver Inc.">
<title>メールフォーム 設定ページ</title>
<link type="text/css" rel="stylesheet" href="./css/admin.css?ver=1.3.1" />
</head>

<body id="form_setting">
<div id="wrapper">
    <header id="header" class="clearfix">
        
        <ul class="subNav clearfix">
            <li><a href="https://www.xserver.ne.jp/manual/man_install_cgi_mailform.php" target="_blank">マニュアル</a></li>
            <li><a href="./?logout=on">ログアウト</a></li>
        </ul>
    </header>
    <!-- /#header -->
    
    <div id="main">
		<p>
            エラー項目があります。<br>
            下記の内容を修正後、再度「プレビュー」ボタンを押下してください。
        </p>

		<div class="red_txt">
			{$error_txt}
		</div>
    </div>
    <footer id="footer">
        &copy; 2012-2018 XSERVER Inc. All rights reserved.&nbsp;&nbsp;Custom MailForm {$version}
    </footer>
    <!-- /#footer -->
</div>
<!-- /#wrapper -->
</body>
</html>


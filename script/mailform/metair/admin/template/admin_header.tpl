<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
{$system_js}
<meta name="copyright" content="Copyright Xserver Inc.">
<title>メールフォーム 設定ページ</title>
<script type="text/javascript" src="./js/design_func.js"></script>
<script type="text/javascript" src="./js/other_func.js"></script>
<link type="text/css" rel="stylesheet" href="./css/admin.css?ver=1.3.1" />
</head>

<body id="{$setting_body_id}">
<div id="wrapper">
    <header id="header" class="clearfix">
        <h1 class="logo"><a href="./"><img src="images/logo.png" alt="エックスサーバー">メールフォーム設定</a></h1>
        
        <a href="{$form_seturl}" target="_blank" class="formDisplay">メールフォームを表示</a>
        
        <ul class="subNav clearfix">
            <li><a href="https://www.xserver.ne.jp/manual/man_install_cgi_mailform.php" target="_blank">マニュアル</a></li>
            <li><a href="./?logout=on">ログアウト</a></li>
        </ul>
    </header>
    <!-- /#header -->

    <div id="main" class="clearfix">
        <ul class="settingNav clearfix">
            <li id="setting_navi_form"><a href="./">基本項目の設定</a></li>
            <li id="setting_navi_design"><a href="./design.php">デザインの設定</a></li>
            <li id="setting_navi_option"><a href="./option.php">自動返信メールの設定</a></li>
            <li id="setting_navi_sethtml"><a href="./sethtml.php">設置用のHTMLタグ</a></li>
        </ul>    



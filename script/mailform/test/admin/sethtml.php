<?php
//============================================================================
//	メールフォーム
//============================================================================

//-----------------------------------------------------------------------------
//エラー出力
//ini_set("display_errors", "On");

//インクルード宣言
require_once dirname(__FILE__) . '/lib/CLogin.php';
require_once dirname(__FILE__) . '/lib/CXmailform.php';

//カレントディレクトリの設定
chdir( dirname(__FILE__) );

//変数の宣言
$mMailForm      = NULL;     //メールの制御クラス

//メールフォームの初期化
$mMailForm    = new CXmailform();

//get_magic_quotes_gpc対策
$mMailForm->CheckMagicQuotesGpc();

//mbstring.encoding_translation対策
$mMailForm->CheckTranslationEncoding();

//-------------------------------------
// HTMLの出力
//-------------------------------------
echo $mMailForm->getSetHtml();

?>


<?php
//============================================================================
//	メールフォーム
//============================================================================

//エラー出力
//ini_set("display_errors", "On");

//インクルード宣言
require_once dirname(__FILE__) . '/admin/lib/CXmailform.php';
require_once dirname(__FILE__) . '/admin/lib/CFormDesign.php';

//カレントディレクトリの設定
chdir( dirname(__FILE__) );

//変数の宣言
$mMailForm      = NULL;     //メールの制御クラス
$mFormDesign    = NULL;     //フォームのデザイン制御クラス
$mActionMode    = '';       //現在のモード

//クラスの初期化
$mMailForm    = new CXmailform();
$mFormDesign  = new CFormDesign();

//get_magic_quotes_gpc対策
$mMailForm->CheckMagicQuotesGpc();

//mbstring.encoding_translation対策
$mMailForm->CheckTranslationEncoding();

//---------------------------------
//表示モードの決定
//---------------------------------
if( isset( $_POST['submit_button'] ) ){
    switch( $_POST['submit_button'] ){
        case '確認':    $mActionMode = 'conf';    break;    //確認画面
        case '送信':    $mActionMode = 'send';    break;    //送信時
        default:        $mActionMode = 'normal';  break;    //通常時
    }
}else{
    $mActionMode = 'normal';
}

//---------------------------------
//設定ファイルの呼込
//---------------------------------
$mMailForm->LoadData( dirname(__FILE__) . '/admin/config/setting.ini' );
$mFormDesign->LoadData( dirname(__FILE__) . '/admin/config/design.ini' );

//---------------------------------
// POST データの呼び込み
//---------------------------------
$mMailForm->SetUserValue();

//-------------------------------------
// 状態別の処理
//-------------------------------------
//アイテムが１つもない場合は、常に入力開始画面を表示する
if( ! $mMailForm->CheckItemNum() ){
	$mActionMode = 'normal';
}

if( $mActionMode == 'conf' ){
    //確認画面表示前に入力値に問題がないかを確認する
    if( $mMailForm->UserValidate() != '' ){
        //入力値に問題がある場合はエラー画面の表示
        $mActionMode = 'normal_c';
    }elseif( ! $mMailForm->IPAccessCheck( $_SERVER['REMOTE_ADDR'], dirname(__FILE__) . '/admin/config/ip_check.ini', false ) ){
        //連続投稿に問題がある場合は、エラー画面を表示する
        $mActionMode = 'normal_c';
    }
}elseif( $mActionMode == 'send' ){
    if( ! $mMailForm->TwoPostCheck( $_SERVER['REMOTE_ADDR'], dirname(__FILE__) . '/admin/config/twopost_check.ini' ) ){
        //２重ポストの場合は何もせずに送信完了画面の表示
    }elseif( ! $mMailForm->IPAccessCheck( $_SERVER['REMOTE_ADDR'], dirname(__FILE__) . '/admin/config/ip_check.ini' ) ){
        //連続投稿に問題がある場合は、エラー画面を表示する
        $mActionMode = 'normal_c';
    }else{
        //メールの送信
        $mMailForm->SendMail();
    }
}

//-------------------------------------
// HTMLの出力
//-------------------------------------
$strHTML = '';
switch( $mActionMode ){
    case 'conf':    $strHTML = $mMailForm->getUserConfirmHtml( $mFormDesign->mUseMyDesign );        break;    //確認画面
    case 'send':    $strHTML = $mMailForm->getUserExitHtml( $mFormDesign->mUseMyDesign );           break;    //送信完了
    case 'normal_c':$strHTML = $mMailForm->getUserInputHtml( $mFormDesign->mUseMyDesign, true );    break;    //通常時(エラー表示あり)
    default:        $strHTML = $mMailForm->getUserInputHtml( $mFormDesign->mUseMyDesign, false );   break;    //通常時
}

echo $mFormDesign->changeDesignHtml( $strHTML );

?>

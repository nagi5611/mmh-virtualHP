<?php
//============================================================================
//  メールフォーム
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
$mActionMode    = '';       //現在のモード

//メールフォームの初期化
$mMailForm    = new CXmailform();

//get_magic_quotes_gpc対策
$mMailForm->CheckMagicQuotesGpc();

//mbstring.encoding_translation対策
$mMailForm->CheckTranslationEncoding();

//---------------------------------
//表示モードの決定
//---------------------------------
if( isset( $_POST['submit_button'] ) ){
    switch( $_POST['submit_button'] ){
        case '設定を保存する(確認)':    $mActionMode = 'conf';    break;    //確認画面
        case '設定を保存する(確定)':    $mActionMode = 'save';    break;    //保存時
        default:                        $mActionMode = 'normal';  break;    //通常時
    }
}else{
    $mActionMode = 'normal';
}

//モード状態の再設定
if( isset( $_GET['mode'] ) ){
    if( $_GET['mode'] == 'exit' ){
        $mActionMode = $_GET['mode'];
    }
}

//---------------------------------
//設定ファイルの呼込
//---------------------------------
$mMailForm->LoadData( dirname(__FILE__) . '/config/setting.ini' );

if( isset( $_POST['submit_button'] ) ){
    $mMailForm->SetValue( $_POST );
}


//-------------------------------------
// 状態別の処理
//-------------------------------------
if( $mActionMode == 'conf' || $mActionMode == 'save' ){
    //確認画面表示前に入力値に問題がないかを確認する
    if( $mMailForm->SystemValidate() != '' || $mMailForm->SystemItemsValidate() != '' ){
        $mActionMode = 'normal_c';
    }
}

if( $mActionMode == 'save' ){
    //設定を保存する
    $mMailForm->SaveData( dirname(__FILE__) . '/config/setting.ini' );

    $mActionMode = 'exit';
}

//-------------------------------------
// HTMLの出力
//-------------------------------------
switch( $mActionMode ){
    case 'conf':    echo $mMailForm->getSystemConfirmHtml();        break;    //確認画面
    case 'exit':    echo $mMailForm->getSystemExitHtml();           break;    //送信完了
    case 'normal_c':echo $mMailForm->getSystemInputHtml( true );    break;    //通常時(エラー表示あり)
    default:        echo $mMailForm->getSystemInputHtml( false );   break;    //通常時
}

?>

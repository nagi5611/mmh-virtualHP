<?php
//============================================================================
//	デザイン修正フォーム
//============================================================================

//-----------------------------------------------------------------------------
//エラー出力
//ini_set("display_errors", "On");

//インクルード宣言
require_once dirname(__FILE__) . '/lib/CLogin.php';
require_once dirname(__FILE__) . '/lib/CXmailform.php';
require_once dirname(__FILE__) . '/lib/CFormDesign.php';

//カレントディレクトリの設定
chdir( dirname(__FILE__) );

//変数の宣言
$mMailForm      = NULL;     //メールの制御クラス
$mFormDesign    = NULL;     //デザインの制御クラス
$mActionMode    = '';       //現在のモード

//クラスの初期化
$mMailForm      = new CXmailform();
$mFormDesign    = new CFormDesign();

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
        case '保存する':                $mActionMode = 'save';    break;    //テンプレートの保存
        case 'リセット':                $mActionMode = 'init';    break;    //テンプレートの初期化
        default:                        $mActionMode = 'normal';  break;    //通常時
    }
}else{
    $mActionMode = 'normal';
}

//---------------------------------
//設定ファイルの呼込
//---------------------------------
if( ! isset( $_POST['submit_button'] ) || $_POST['submit_button'] == '' ){
    $mFormDesign->LoadData( dirname(__FILE__) . '/config/design.ini' );
}else{
    $mFormDesign->LoadData( dirname(__FILE__) . '/config/design.ini' );
    $mFormDesign->SetValue( $_POST );
}

//テンプレートの初期化
if( $mActionMode == 'init' ){
    $mFormDesign->InitTemplate();
    $mActionMode = 'normal';
}

//---------------------------------
//スキンの変更
//---------------------------------
if( isset( $_GET['skin'] ) && $_GET['skin'] != '' ){
    $strDesignMode = $_GET['skin'];

    //デザインのモード変更
    if( $strDesignMode == 'original' ){
        //定型→自作
        $mFormDesign->ChangeDesignMode( 1 );
        $mFormDesign->InitOriginalDesign();
    }else{
        //自作→定型
        $mFormDesign->ChangeDesignMode( 0 );
    }
    $mFormDesign->SaveDesignData( dirname(__FILE__) . '/config/design.ini' );
    $mFormDesign->LoadTemplate();
}


//-------------------------------------
// 状態別の処理
//-------------------------------------
if( $mActionMode == 'conf' || $mActionMode == 'save' ){
    //確認画面表示前に入力値に問題がないかを確認する
    if( $mFormDesign->DesignBasicValidate() != '' || $mFormDesign->DesignColorValidate() != '' ||
        $mFormDesign->DesignTxtValidate() != '' || $mFormDesign->DesignTemplateValidate() != '' ){
        $mActionMode = 'normal_c';
    }
}

//設定を保存する
if( $mActionMode == 'save' ){
    //テンプレート保存の場合は、確認／完了画面に遷移しない
    if( $_POST['submit_button'] == '保存する' ){
        $strSaveFile = '';
        if( isset( $_POST['nowhtml'] ) ){
            switch( $_POST['nowhtml'] ){
                case 0:     $strSaveFile = 'input'; break;
                case 1:     $strSaveFile = 'conf';  break;
                case 2:     $strSaveFile = 'exit';  break;
                case 3:     $strSaveFile = 'css';   break;
            }
        }

        $mFormDesign->SaveTemplateData( $strSaveFile );
        $mActionMode = 'normal';
    }else{
        $mFormDesign->SaveDesignData( dirname(__FILE__) . '/config/design.ini' );
        $mActionMode = 'exit';
    }
}

//-------------------------------------
// HTMLの出力
//-------------------------------------
switch( $mActionMode ){
    case 'conf':    echo $mFormDesign->getDesignConfirmHtml();        break;    //確認画面
    case 'exit':    echo $mFormDesign->getDesignExitHtml();           break;    //送信完了
    case 'normal_c':echo $mFormDesign->getDesignInputHtml( true );    break;    //通常時(エラー表示あり)
    default:        echo $mFormDesign->getDesignInputHtml( false );   break;    //通常時
}

?>

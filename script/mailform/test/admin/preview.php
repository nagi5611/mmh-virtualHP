<?php
//============================================================================
//  メールフォーム
//============================================================================

//-----------------------------------------------------------------------------
//エラー出力
//ini_set("display_errors", "On");

//インクルード宣言
require_once dirname(__FILE__) . '/lib/CCompatible.php';
require_once dirname(__FILE__) . '/lib/CXmailform.php';
require_once dirname(__FILE__) . '/lib/CFormDesign.php';

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
//設定ファイルの呼込
//---------------------------------
if( $_POST['nowpage'] == "system_page" ){    $mMailForm->SetValue( $_POST );
}else{                                       $mMailForm->LoadData( dirname(__FILE__) . '/config/setting.ini' );
}

$mFormDesign->LoadData( dirname(__FILE__) . '/config/design.ini' );
if( $_POST['nowpage'] == "design_page" ){    $mFormDesign->SetValue( $_POST );
}


//-------------------------------------
// エラーメッセージの取得
//-------------------------------------
$strError = '';
if( $_POST['nowpage'] == "system_page" ){
    $strError .= $mMailForm->SystemPreviewValidate();
    $strError .= $mMailForm->SystemItemsValidate();
}
if( $_POST['nowpage'] == "design_page" ){
    $strError .= $mFormDesign->DesignBasicValidate();
    $strError .= $mFormDesign->DesignColorValidate();
    $strError .= $mFormDesign->DesignTxtValidate();
    $strError .= $mFormDesign->DesignTemplateValidate();
}


//-------------------------------------
// HTMLの出力
//-------------------------------------
if( $strError != '' ){
    $strHTML = $mMailForm->getPreviewErrorHtml( $strError );
    echo $strHTML;
}else{
    $strPageName = '';
    if( isset( $_GET['page'] ) ){
        $strPageName = $_GET['page'];
    }
    $strHTML = '';
    switch( $strPageName ){
        case 'conf': $strHTML = $mMailForm->getPreviewConfHtml( $mFormDesign->mUseMyDesign, $mFormDesign->mTemplateConf );    break;
        case 'exit': $strHTML = $mMailForm->getPreviewExitHtml( $mFormDesign->mUseMyDesign, $mFormDesign->mTemplateExit );    break;
        default:     $strHTML = $mMailForm->getPreviewInputHtml( $mFormDesign->mUseMyDesign, $mFormDesign->mTemplateInput );  break;
    }

    //キャッシュ破棄
    $strHTML = CCompatible::str_replace_allowed_arg( '{$update_time}', date("YmdHis"), $strHTML );

    //自作テンプレート対策
    $strHTML = CCompatible::str_replace_allowed_arg( '<input type="submit"','<input type="button"',$strHTML );

    echo $mFormDesign->changeDesignHtml( $strHTML, true );
}
?>

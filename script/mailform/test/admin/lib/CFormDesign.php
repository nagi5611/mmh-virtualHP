<?php
//===========================================
//
//  エックスサーバーのフォーム制御
//
//===========================================
//エラー出力
//ini_set("display_errors", "On");


require_once dirname(__FILE__) . '/CCompatible.php';
require_once dirname(dirname(__FILE__)) . '/config/password.php';
require_once dirname(__FILE__) . '/CXmailform.php';
require_once dirname(__FILE__) . '/CVersion.php';

class CFormDesign {
    var $mCheckString;       //確認を行う文字列郡

    //デザイン設定の変数
    var $mUseMyDesign;       //自作のメールフォームを使用するか？

    //基本項目の変数
    var $mBasicFormWidth;    //フォームの横幅の取得

    //色彩関連の変数
    var $mColorTitle;        //タイトルのフォントカラー
    var $mColorExplain;      //説明文のフォントカラー
    var $mColorBackground;   //全体の背景色
    var $mColorLine;         //枠線の色
    var $mColorMenuBG;       //メニューの背景色
    var $mColorMenuFont;     //メニューのフォントカラー
    var $mColorError;        //エラー文のフォントカラー

    //テキスト関連の変数
    var $mTxtExplain;        //説明文のテキスト
    var $mTxtConfirm;        //確認画面のテキスト
    var $mTxtExit;           //メール送信後のテキスト

    //自作テンプレートの変数
    var $mTemplateCSS;       //自作ＣＳＳ
    var $mTemplateInput;     //入力画面
    var $mTemplateConf;      //確認画面
    var $mTemplateExit;      //完了画面

    //------------------------------------------------------
    //=====================================
    // コンストラクタ
    //=====================================
    function __construct(){
        $this->Init();
    }

    //=====================================
    // デストラクタ
    //=====================================
    function __destruct(){
        $this->Free();
    }

    //------------------------------------------------------
    // public 関数

    //=====================================
    //データの初期設定
    //=====================================
    public function Init(){
        $count = 0;
        //確認を行う文字列郡
        $this->mCheckString       = array();
        $this->mCheckString[$count++] = 'MY_DESIGN';
        $this->mCheckString[$count++] = 'BASIC_WIDTH';
        $this->mCheckString[$count++] = 'COLOR_TITLE';
        $this->mCheckString[$count++] = 'COLOR_EXPLAIN';
        $this->mCheckString[$count++] = 'COLOR_BACKGROUND';
        $this->mCheckString[$count++] = 'COLOR_LINE';
        $this->mCheckString[$count++] = 'COLOR_MENUBG';
        $this->mCheckString[$count++] = 'COLOR_MENUFONT';
        $this->mCheckString[$count++] = 'COLOR_ERROR';
        $this->mCheckString[$count++] = 'TXT_EXPLAIN';
        $this->mCheckString[$count++] = 'TXT_CONFIRM';
        $this->mCheckString[$count++] = 'TXT_EXIT';

        //デザイン設定の変数
        $this->mUseMyDesign       = 0;     //自作のメールフォームを使用するか？

        //基本項目の変数
        $this->mBasicFormWidth    = 0;     //フォームの横幅の取得

        //色彩関連の変数
        $this->mColorTitle        = "";    //タイトルのフォントカラー
        $this->mColorExplain      = "";    //説明文のフォントカラー
        $this->mColorBackground   = "";    //全体の背景色
        $this->mColorLine         = "";    //枠線の色
        $this->mColorMenuBG       = "";    //メニューの背景色
        $this->mColorMenuFont     = "";    //メニューのフォントカラー
        $this->mColorError        = "";    //エラー文のフォントカラー

        //テキスト関連の変数
        $this->mTxtExplain        = "";    //説明文のテキスト
        $this->mTxtConfirm        = "";    //確認画面のテキスト
        $this->mTxtExit           = "";    //メール送信後のテキスト

        //テンプレートの変数
        $this->mTemplateCSS       = "";    //自作ＣＳＳ
        $this->mTemplateInput     = "";    //入力画面
        $this->mTemplateConf      = "";    //確認画面
        $this->mTemplateExit      = "";    //完了画面
    }

    //=====================================
    //データの解放処理
    //=====================================
    public function Free(){
    }

    //=====================================
    //設定ファイルのロード
    //=====================================
    public function LoadData( $strFileName ){

        //設定の初期化
        $this->Init();

        //設定ファイルの呼び込み
        $lines = file( $strFileName );

        $strKey   = '';
        $strValue = '';

        foreach( $lines as $value ) {

            //空白の削除
            $value = trim($value);

            //開始／終了キーの確認
            if( $strKey ){
                //終了タグの検索
                $checkValue = $this->CheckStrCode( $value, false );
                if( $checkValue ){
                    $strKey = '';
                }
            }else{
                //空行の場合は判定を行わない
                if( $value == "" ){
                    continue;
                }
                //開始タグの検索
                $checkValue = $this->CheckStrCode( $value, true );
                if( $checkValue ){
                    $strKey = $checkValue;
                    continue;
                }
            }
            if( ! $strKey ) continue;

            switch( $strKey ){
                case 'MY_DESIGN':        $this->mUseMyDesign        = $value; break;
                case 'BASIC_WIDTH':      $this->mBasicFormWidth     = $value; break;
                case 'COLOR_TITLE':      $this->mColorTitle        .= $value; break;
                case 'COLOR_EXPLAIN':    $this->mColorExplain      .= $value; break;
                case 'COLOR_BACKGROUND': $this->mColorBackground   .= $value; break;
                case 'COLOR_LINE':       $this->mColorLine         .= $value; break;
                case 'COLOR_MENUBG':     $this->mColorMenuBG       .= $value; break;
                case 'COLOR_MENUFONT':   $this->mColorMenuFont     .= $value; break;
                case 'COLOR_ERROR':      $this->mColorError        .= $value; break;
                case 'TXT_EXPLAIN':      $this->mTxtExplain        .= $value . "\n"; break;
                case 'TXT_CONFIRM':      $this->mTxtConfirm        .= $value . "\n"; break;
                case 'TXT_EXIT':         $this->mTxtExit           .= $value . "\n"; break;
            }
        }

        //400px 未満の場合は 400px を設定する
        if( $this->mBasicFormWidth < 400 ){
            $this->mBasicFormWidth = 400;
        }

        //テンプレートのロード
        $this->LoadTemplate();

    }

    //=====================================
    //テンプレートファイルのロード
    //=====================================
    public function LoadTemplate(){
        //自作テンプレート
        $this->mTemplateCSS   = file_get_contents( dirname(dirname(__FILE__)) . '/user_template/use_css.tpl' );
        $this->mTemplateInput = file_get_contents( dirname(dirname(__FILE__)) . '/user_template/use_index.tpl' );
        $this->mTemplateConf  = file_get_contents( dirname(dirname(__FILE__)) . '/user_template/use_conf.tpl' );
        $this->mTemplateExit  = file_get_contents( dirname(dirname(__FILE__)) . '/user_template/use_exit.tpl' );
    }

    public function SetValue( $arrValues ){

        //自作のメールフォームを使用するか？
        if( isset( $arrValues['design_mode'] ) ){     $this->mUseMyDesign  = trim( $arrValues['design_mode'] );
        }else{                                        $this->mUseMyDesign  = 0;
        }

        if( $this->mUseMyDesign == 0 ){
        //----------------------------------------------------------------------------------------------------------
        //定型テンプレート

        //基本設定の取得
        if( isset( $arrValues['form_width'] ) ){      $this->mBasicFormWidth  = trim( $arrValues['form_width'] );
        }else{                                        $this->mBasicFormWidth  = 400;
        }

        //色彩の取得
        if( isset( $arrValues['color_title'] ) ){     $this->mColorTitle      = $arrValues['color_title'];     }
        if( isset( $arrValues['color_explain'] ) ){   $this->mColorExplain    = $arrValues['color_explain'];   }
        if( isset( $arrValues['color_background'] ) ){$this->mColorBackground = $arrValues['color_background'];}
        if( isset( $arrValues['color_line'] ) ){      $this->mColorLine       = $arrValues['color_line'];      }
        if( isset( $arrValues['color_menubg'] ) ){    $this->mColorMenuBG     = $arrValues['color_menubg'];    }
        if( isset( $arrValues['color_menufont'] ) ){  $this->mColorMenuFont   = $arrValues['color_menufont'];  }
        if( isset( $arrValues['color_error'] ) ){     $this->mColorError      = $arrValues['color_error'];     }

        //テキストの取得
        if( isset( $arrValues['txt_explain'] ) ){  $this->mTxtExplain   = $arrValues['txt_explain'];  }
        if( isset( $arrValues['txt_confirm'] ) ){  $this->mTxtConfirm   = $arrValues['txt_confirm'];  }
        if( isset( $arrValues['txt_exit'] ) ){     $this->mTxtExit      = $arrValues['txt_exit'];     }

        //ＨＴＭＬタグを変換する
        $this->mColorTitle          = trim( htmlspecialchars( $this->mColorTitle, ENT_QUOTES, "UTF-8" ) );
        $this->mColorExplain        = trim( htmlspecialchars( $this->mColorExplain, ENT_QUOTES, "UTF-8" ) );
        $this->mColorBackground     = trim( htmlspecialchars( $this->mColorBackground, ENT_QUOTES, "UTF-8" ) );
        $this->mColorLine           = trim( htmlspecialchars( $this->mColorLine, ENT_QUOTES, "UTF-8" ) );
        $this->mColorMenuBG         = trim( htmlspecialchars( $this->mColorMenuBG, ENT_QUOTES, "UTF-8" ) );
        $this->mColorMenuFont       = trim( htmlspecialchars( $this->mColorMenuFont, ENT_QUOTES, "UTF-8" ) );
        $this->mColorError          = trim( htmlspecialchars( $this->mColorError, ENT_QUOTES, "UTF-8" ) );
        $this->mTxtExplain          = trim( htmlspecialchars( $this->mTxtExplain, ENT_QUOTES, "UTF-8" ) );
        $this->mTxtConfirm          = trim( htmlspecialchars( $this->mTxtConfirm, ENT_QUOTES, "UTF-8" ) );
        $this->mTxtExit             = trim( htmlspecialchars( $this->mTxtExit, ENT_QUOTES, "UTF-8" ) );

        //----------------------------------------------------------------------------------------------------------
        }else{
        //----------------------------------------------------------------------------------------------------------
            //自作テンプレート
            if( isset( $arrValues['template_css'] ) ){    $this->mTemplateCSS   = $arrValues['template_css'];    }
            if( isset( $arrValues['template_input'] ) ){  $this->mTemplateInput = $arrValues['template_input'];  }
            if( isset( $arrValues['template_conf'] ) ){   $this->mTemplateConf  = $arrValues['template_conf'];   }
            if( isset( $arrValues['template_exit'] ) ){   $this->mTemplateExit  = $arrValues['template_exit'];   }
            $this->mTemplateCSS         = trim( htmlspecialchars( $this->mTemplateCSS, ENT_QUOTES, "UTF-8" ) );
            $this->mTemplateInput       = trim( htmlspecialchars( $this->mTemplateInput, ENT_QUOTES, "UTF-8" ) );
            $this->mTemplateConf        = trim( htmlspecialchars( $this->mTemplateConf, ENT_QUOTES, "UTF-8" ) );
            $this->mTemplateExit        = trim( htmlspecialchars( $this->mTemplateExit, ENT_QUOTES, "UTF-8" ) );
        //----------------------------------------------------------------------------------------------------------
        }

    }

    //=====================================
    // チェック文字の確認
    //=====================================
    function CheckStrCode( $strTxt, $isStart ){
        foreach( $this->mCheckString as $value ){
            if( $isStart ){
                //開始タグ
                if( strpos( $strTxt, 'BEGIN_' . $value . ' ', 0 ) === 0 ){
                    return $value;
                }
            }else{
                //終了タグ
                if( $strTxt === 'END_' . $value ){
                    return $value;
                }
            }
        }
        return false;
    }


    //=====================================
    //基本項目の入力値チェック
    //=====================================
    public function DesignBasicValidate(){
        $nCount		= 0;
        $strError   = '';

        $strError  .= "<strong>\r\n";
        $strError  .= "<ul>\r\n";

        //------------------------------------
        //入力値のチェック
        //------------------------------------
        if( ! $this->mBasicFormWidth ){
            $strError  .= "<li>[横幅] を入力してください</li>\r\n";
            $nCount++;
        }elseif( ! is_numeric( $this->mBasicFormWidth ) ){
            $strError  .= "<li>[横幅] には数値を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasicFormWidth, "UTF-8" ) > 4 ){
            $strError  .= "<li>[横幅] は 4 桁以内にして下さい</li>\r\n";
            $nCount++;
        }else{
            $this->mBasicFormWidth = intval( $this->mBasicFormWidth );
            if( $this->mBasicFormWidth < 400 ){
                $strError  .= "<li>[横幅] 400px以上を設定してください</li>\r\n";
                $nCount++;
            }
        }

        $strError  .= "</ul>\r\n";
        $strError  .= "</strong>\r\n";

        $strError  .= "<br />\r\n";

        //エラーがない場合は、空文字で初期化する
        if( $nCount <= 0 ){
            $strError = '';
        }

        return $strError;
    }

    //=====================================
    //配色の入力値チェック
    //=====================================
    public function DesignColorValidate(){
        $nCount     = 0;
        $strError   = '';

        $strError  .= "<strong>\r\n";
        $strError  .= "<ul>\r\n";

        //------------------------------------
        //入力値のチェック
        //------------------------------------
            if( mb_strlen( $this->mColorTitle, "UTF-8" ) != 6 || ! preg_match("/^([a-fA-F0-9])+$/", $this->mColorTitle) ){
                $strError  .= "<li>[タイトル] には 6 文字のカラーコードで記入して下さい</li>\r\n";
                $nCount++;
            }
            if( mb_strlen( $this->mColorExplain, "UTF-8" ) != 6 || ! preg_match("/^([a-fA-F0-9])+$/", $this->mColorExplain) ){
                $strError  .= "<li>[説明文] には 6 文字のカラーコードで記入して下さい</li>\r\n";
                $nCount++;
            }
            if( mb_strlen( $this->mColorBackground, "UTF-8" ) != 6 || ! preg_match("/^([a-fA-F0-9])+$/", $this->mColorBackground) ){
                $strError  .= "<li>[背景色] には 6 文字のカラーコードで記入して下さい</li>\r\n";
                $nCount++;
            }
            if( mb_strlen( $this->mColorLine, "UTF-8" ) != 6 || ! preg_match("/^([a-fA-F0-9])+$/", $this->mColorLine) ){
                $strError  .= "<li>[枠線] には 6 文字のカラーコードで記入して下さい</li>\r\n";
                $nCount++;
            }
            if( mb_strlen( $this->mColorMenuBG, "UTF-8" ) != 6 || ! preg_match("/^([a-fA-F0-9])+$/", $this->mColorMenuBG) ){
                $strError  .= "<li>[項目背景] には 6 文字のカラーコードで記入して下さい</li>\r\n";
                $nCount++;
            }
            if( mb_strlen( $this->mColorMenuFont, "UTF-8" ) != 6 || ! preg_match("/^([a-fA-F0-9])+$/", $this->mColorMenuFont) ){
                $strError  .= "<li>[項目文字] には 6 文字のカラーコードで記入して下さい</li>\r\n";
                $nCount++;
            }
            if( mb_strlen( $this->mColorError, "UTF-8" ) != 6 || ! preg_match("/^([a-fA-F0-9])+$/", $this->mColorError) ){
                $strError  .= "<li>[エラー表示] には 6 文字のカラーコードで記入して下さい</li>\r\n";
                $nCount++;
            }

        $strError  .= "</ul>\r\n";
        $strError  .= "</strong>\r\n";

        $strError  .= "<br />\r\n";

        //エラーがない場合は、空文字で初期化する
        if( $nCount <= 0 ){
            $strError = '';
        }

        return $strError;
    }


    //=====================================
    //メッセージの入力値チェック
    //=====================================
    public function DesignTxtValidate(){
        $nCount     = 0;
        $strError   = '';

        $strError  .= "<strong>\r\n";
        $strError  .= "<ul>\r\n";

        //------------------------------------
        //入力値のチェック
        //------------------------------------
        if( mb_strlen( $this->mTxtExplain, "UTF-8" ) > 2000 ){
            $strError  .= "<li>[メッセージ(入力画面)] には 2000 文字以内にして下さい</li>\r\n";
            $nCount++;
        }
        if( mb_strlen( $this->mTxtConfirm, "UTF-8" ) > 2000 ){
            $strError  .= "<li>[メッセージ(入力確認画面)] には 2000 文字以内にして下さい</li>\r\n";
            $nCount++;
        }
        if( mb_strlen( $this->mTxtExit, "UTF-8" ) > 2000 ){
            $strError  .= "<li>[メッセージ(送信完了画面)] には 2000 文字以内にして下さい</li>\r\n";
            $nCount++;
        }
        $strError  .= "</ul>\r\n";
        $strError  .= "</strong>\r\n";

        $strError  .= "<br />\r\n";

        //エラーがない場合は、空文字で初期化する
        if( $nCount <= 0 ){
            $strError = '';
        }

        return $strError;
    }
    
    //=====================================
    //自作テンプレートの入力値チェック
    //=====================================
    public function DesignTemplateValidate() {
        $nCount     = 0;
        $strError   = '';

        $strError  .= "<strong>\r\n";
        $strError  .= "<ul>\r\n";

        //------------------------------------
        //入力値のチェック
        //------------------------------------
        if( strpos( $this->mTemplateInput, '{$form_data}' ) === false ){
            $strError  .= '<li>[入力画面テンプレート] には {$form_data} を記入して下さい</li>' . "\r\n";
            $nCount++;
        }
        if( strpos( $this->mTemplateInput, '{$form_error}' ) === false ){
            $strError  .= '<li>[入力画面テンプレート] には {$form_error} を記入して下さい</li>' . "\r\n";
            $nCount++;
        }
        if( strpos( $this->mTemplateConf, '{$form_data}' ) === false ){
            $strError  .= '<li>[確認画面テンプレート] には {$form_data} を記入して下さい</li>' . "\r\n";
            $nCount++;
        }

        $strError  .= "</ul>\r\n";
        $strError  .= "</strong>\r\n";

        $strError  .= "<br />\r\n";

        //エラーがない場合は、空文字で初期化する
        if( $nCount <= 0 ){
            $strError = '';
        }

        return $strError;
    }


    //=====================================
    //設定ファイルのセーブ
    //=====================================
    public function SaveDesignData( $strFileName )
    {
        $put_contents  = '';
        $txt_data      = '';
        foreach( $this->mCheckString as $value ){
            //無駄なスペースを削除する
            $value = trim( $value );

            //内容
            $txt_data = '';
            switch( $value ){
                case 'MY_DESIGN':        $txt_data  .= $this->mUseMyDesign;     break;
                case 'BASIC_WIDTH':      $txt_data  .= $this->mBasicFormWidth;  break;
                case 'COLOR_TITLE':      $txt_data  .= $this->mColorTitle;      break;
                case 'COLOR_EXPLAIN':    $txt_data  .= $this->mColorExplain;    break;
                case 'COLOR_BACKGROUND': $txt_data  .= $this->mColorBackground; break;
                case 'COLOR_LINE':       $txt_data  .= $this->mColorLine;       break;
                case 'COLOR_MENUBG':     $txt_data  .= $this->mColorMenuBG;     break;
                case 'COLOR_MENUFONT':   $txt_data  .= $this->mColorMenuFont;   break;
                case 'COLOR_ERROR':      $txt_data  .= $this->mColorError;      break;
                case 'TXT_EXPLAIN':      $txt_data  .= $this->mTxtExplain;      break;
                case 'TXT_CONFIRM':      $txt_data  .= $this->mTxtConfirm;      break;
                case 'TXT_EXIT':         $txt_data  .= $this->mTxtExit;         break;
            }
            //無駄なスペースを削除する
            $txt_data = trim( $txt_data );

            //開始タグ
            $put_contents  .= 'BEGIN_' . $value . ' ';
            $put_contents  .= count( explode( "\n", $txt_data ) );
            $put_contents  .= "\r\n";

            //内容
            $put_contents  .= $txt_data;
            $put_contents  .= "\r\n";

            //終了タグ
            $put_contents  .= 'END_' . $value . ' '. "\r\n";
            $put_contents  .= "\r\n";
        }

        //ファイルに出力を実行
        file_put_contents( $strFileName, $put_contents );
    }

    //=====================================
    //テンプレートファイルのセーブ
    //=====================================
    public function ChangeDesignMode( $nDesignMode )
    {
        $this->mUseMyDesign = $nDesignMode;
    }

    //=====================================
    //テンプレートファイルのセーブ
    //=====================================
    public function SaveTemplateData( $strSaveFile )
    {
        $strFolder = dirname(dirname(__FILE__)) . '/user_template/';
        //自作テンプレート
        switch( $strSaveFile ){
            case 'css':   file_put_contents( $strFolder . '/use_css.tpl',   $this->mTemplateCSS );    break;
            case 'input': file_put_contents( $strFolder . '/use_index.tpl', $this->mTemplateInput );  break;
            case 'conf':  file_put_contents( $strFolder . '/use_conf.tpl',  $this->mTemplateConf );   break;
            case 'exit':  file_put_contents( $strFolder . '/use_exit.tpl',  $this->mTemplateExit );   break;
        }
    }

    //=====================================
    //テンプレートファイルの初期化
    //=====================================
    public function InitTemplate()
    {
        $strFolder = dirname(dirname(__FILE__)) . '/user_template/';
        if( isset( $_POST['nowhtml'] ) ) {
            switch( $_POST['nowhtml'] ){
                case 3:    $this->mTemplateCSS   = file_get_contents( $strFolder . '/use_css.tpl' );    break;
                case 0:    $this->mTemplateInput = file_get_contents( $strFolder . '/use_index.tpl' );  break;
                case 1:    $this->mTemplateConf  = file_get_contents( $strFolder . '/use_conf.tpl' );   break;
                case 2:    $this->mTemplateExit  = file_get_contents( $strFolder . '/use_exit.tpl' );   break;
            }
        }
    }


    //=====================================
    //テンプレートファイルの初期化
    //=====================================
    public function InitOriginalDesign(){
        //CSSの初期設定
        file_put_contents( dirname(dirname(__FILE__)) . '/user_template/use_css.tpl',   $this->getDefaultCSS() );

        //デフォルトのファイル取得
        $strIndex = file_get_contents( dirname(dirname(__FILE__)) . '/template/user_index.tpl' );
        $strConf  = file_get_contents( dirname(dirname(__FILE__)) . '/template/user_conf.tpl' );
        $strExit  = file_get_contents( dirname(dirname(__FILE__)) . '/template/user_exit.tpl' );

        //説明文のみ置換後に再設定
        $strHtmlExplain = CCompatible::str_replace_allowed_arg( "\n", '<br />', $this->mTxtExplain );
        $strHtmlConfirm = CCompatible::str_replace_allowed_arg( "\n", '<br />', $this->mTxtConfirm );
        $strHtmlExit    = CCompatible::str_replace_allowed_arg( "\n", '<br />', $this->mTxtExit );
        $strIndex = CCompatible::str_replace_allowed_arg( '{$html_txt_explain}', $strHtmlExplain, $strIndex );   //説明文のテキスト
        $strConf  = CCompatible::str_replace_allowed_arg( '{$html_txt_confirm}', $strHtmlConfirm, $strConf );    //確認画面のテキスト
        $strExit  = CCompatible::str_replace_allowed_arg( '{$html_txt_exit}',    $strHtmlExit,    $strExit );    //メール送信後のテキスト

        //初期ファイルの保存
        file_put_contents( dirname(dirname(__FILE__)) . '/user_template/use_index.tpl', $strIndex );
        file_put_contents( dirname(dirname(__FILE__)) . '/user_template/use_conf.tpl',  $strConf );
        file_put_contents( dirname(dirname(__FILE__)) . '/user_template/use_exit.tpl',  $strExit );
    }

    //=====================================
    //デザインの設定
    //=====================================
    public function changeDesignHtml( $strHtml, $isPreview = false ){
        //ＣＳＳの設定
        $strCSS  = '';
        if( $this->mUseMyDesign == 0 ){
            $strCSS  = $this->getDefaultCSS();
        }else{
            $strCSS  = $this->mTemplateCSS;
        }

        $strCSS = htmlspecialchars_decode( $strCSS );
        if( $isPreview ){ file_put_contents( dirname(dirname(__FILE__)) . '/css/form.css', $strCSS );
        }else{            file_put_contents( dirname(dirname(dirname(__FILE__))) . '/css/form.css', $strCSS );
        }

        //テキスト関連の変数
        $strHtmlExplain = CCompatible::str_replace_allowed_arg( "\n", '<br />', $this->mTxtExplain );
        $strHtmlConfirm = CCompatible::str_replace_allowed_arg( "\n", '<br />', $this->mTxtConfirm );
        $strHtmlExit    = CCompatible::str_replace_allowed_arg( "\n", '<br />', $this->mTxtExit );
        $strHtml = CCompatible::str_replace_allowed_arg( '{$html_txt_explain}', $strHtmlExplain, $strHtml );
        $strHtml = CCompatible::str_replace_allowed_arg( '{$html_txt_confirm}', $strHtmlConfirm, $strHtml );
        $strHtml = CCompatible::str_replace_allowed_arg( '{$html_txt_exit}',    $strHtmlExit,    $strHtml );

        //CSSファイルの更新日付の取得
        $strHtml = CCompatible::str_replace_allowed_arg( '{$update_time}', $this->_fetchCSSUpdateTime(), $strHtml );      

       return $strHtml;

    }

    /**
     * CSSファイルの更新日付を取得する
     * 
     * @return string
     */
    private function _fetchCSSUpdateTime(){
        $ref_file_path = null;
        if( $this->mUseMyDesign == 0 ){
            // 定型スキン
            $ref_file_path = dirname(dirname(__FILE__)) . '/config/design.ini';
        }else{
            // 自作スキン
            $ref_file_path = dirname(dirname(__FILE__)) . '/user_template/use_css.tpl';
        }
        $replace_update_time = null;
        if (file_exists($ref_file_path)) {
            $replace_update_time = date("YmdHis", filemtime($ref_file_path));
        } else {
            $replace_update_time = date("YmdHis");
        }
        return $replace_update_time;
    }

    //=====================================
    //初期設定のＣＳＳを取得する
    //=====================================
    public function getDefaultCSS(){
        $strCSS  = file_get_contents( dirname(dirname(__FILE__)) . '/template/user_css.tpl' );

        //横幅設定
        $width = $this->mBasicFormWidth;
        $table_width = ( $this->mBasicFormWidth - 30 );
        $input_width = ( $this->mBasicFormWidth - 340 );
        $textarea_width = ( $this->mBasicFormWidth - 310 );
        if( $input_width < 130 ){    $input_width       = 130;    }
        if( $textarea_width < 150 ){ $textarea_width    = 150;    }
        $strCSS = CCompatible::str_replace_allowed_arg( '{$basic_formwidth}',     $width, $strCSS );             //フォームの横幅の取得
        $strCSS = CCompatible::str_replace_allowed_arg( '{$basic_tablewidth}',    ( $table_width ), $strCSS );       //テーブルの横幅の取得
        $strCSS = CCompatible::str_replace_allowed_arg( '{$basic_inputwidth}',    ( $input_width ), $strCSS );       //入力値の横幅の取得
        $strCSS = CCompatible::str_replace_allowed_arg( '{$basic_textareawidth}', ( $textarea_width ), $strCSS );    //テキストエリア値の横幅の取得

        //色彩関連の変数
        if( $this->mColorTitle ){   //タイトルのフォントカラー
            $strTitle = '';
            $strTitle .= "background : #$this->mColorTitle;\r\n" . "    ";

            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_title}',      $strTitle,      $strCSS );
        }else{
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_title}',      '',      $strCSS );
        }
        if( $this->mColorExplain ){ //説明文のフォントカラー
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_explain}',    "color: #$this->mColorExplain;",    $strCSS );
        }else{
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_explain}',    '',    $strCSS );
        }
        if( $this->mColorBackground ){//全体の背景色
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_background}', "background : #$this->mColorBackground;", $strCSS );
        }else{
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_background}', '', $strCSS );
        }
        if( $this->mColorLine ){    //枠線の色
            $strLine = '';
            $strLine .= "border-left : 1px solid #$this->mColorLine ;\r\n" . "    ";
            $strLine .= "border-right : 1px solid #$this->mColorLine ;\r\n" . "    ";
            $strLine .= "border-bottom : 1px solid #$this->mColorLine ;";

            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_line}',       $strLine,       $strCSS );
        }else{
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_line}',       '',       $strCSS );
        }
        if( $this->mColorMenuBG ){  //メニューの背景色
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_menubg}',     "background : #$this->mColorMenuBG;",     $strCSS );
        }else{
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_menubg}',     '',     $strCSS );
        }
        if( $this->mColorMenuFont ){ //メニューのフォントカラー
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_menufont}',   "color: #$this->mColorMenuFont;",   $strCSS );
        }else{
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_menufont}',   '',   $strCSS );
        }
        if( $this->mColorError ){   //エラー文のフォントカラー
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_error}',      "color: #$this->mColorError;",      $strCSS );
        }else{
            $strCSS = CCompatible::str_replace_allowed_arg( '{$color_error}',      '',      $strCSS );
        }
        return $strCSS;
    }

    //=====================================
    //デザイン変更用の入力用HTMLの取得
    //=====================================
    public function getDesignInputHtml( $isChkError ){
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt    = file_get_contents( dirname(dirname(__FILE__)) . '/template/design_index.tpl' );

        //デザイン設定の変数
        $strTxt = CCompatible::str_replace_allowed_arg( '{$default_design_mode}', $this->mUseMyDesign, $strTxt );

        $strStyleNone = 'style="display:none"';
        if( $this->mUseMyDesign == 0 ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$design_mode_0}',       'selected',    $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$design_mode_1}',       '',            $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$set_default_design}',  '',            $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$set_original_design}', $strStyleNone, $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$design_mode_0}',       '',            $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$design_mode_1}',       'selected',    $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$set_default_design}',  $strStyleNone, $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$set_original_design}', '',            $strTxt );
        }

        $nNowHTML = 0;
        if( isset( $_POST['nowhtml'] ) ){
            $nNowHTML = $_POST['nowhtml'];
        }
        $strTxt = CCompatible::str_replace_allowed_arg( '{$nowhtml}', $nNowHTML, $strTxt );
        for( $i=0;$i<4;$i++ ){
            if( $i == $nNowHTML ){
                $strTxt = CCompatible::str_replace_allowed_arg( '{$template_div_' . $i . '}',   'class="current_sub_navi"',  $strTxt );
                $strTxt = CCompatible::str_replace_allowed_arg( '{$template_table_' . $i . '}', 'style="display:block"',     $strTxt );
            }else{
                $strTxt = CCompatible::str_replace_allowed_arg( '{$template_div_' . $i . '}',   '', $strTxt );
                $strTxt = CCompatible::str_replace_allowed_arg( '{$template_table_' . $i . '}', 'style="display:none"',      $strTxt );
            }
        }

        if( isset( $_POST['change_template_input'] ) && $_POST['change_template_input'] == 1 && $nNowHTML != 0 ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_input}', '1',                   $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_input}',     'style="font-weight : bold;"', $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_input}', '0', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_input}',     '',  $strTxt );
        }
        if( isset( $_POST['change_template_conf'] ) && $_POST['change_template_conf'] == 1 && $nNowHTML != 1 ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_conf}', '1',                   $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_conf}',     'style="font-weight : bold;"', $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_conf}', '0', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_conf}',     '',  $strTxt );
        }
        if( isset( $_POST['change_template_exit'] ) && $_POST['change_template_exit'] == 1 && $nNowHTML != 2 ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_exit}', '1',                   $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_exit}',     'style="font-weight : bold;"', $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_exit}', '0', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_exit}',     '',  $strTxt );
        }
        if( isset( $_POST['change_template_css'] ) && $_POST['change_template_css'] == 1 && $nNowHTML != 3 ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_css}', '1',                   $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_css}',     'style="font-weight : bold;"', $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_template_css}', '0', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$change_bold_css}',     '',  $strTxt );
        }


       //基本項目の変数
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formwidth}',  $this->mBasicFormWidth,  $strTxt );	//フォームの横幅の取得

       //色彩関連の変数
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_title}',      $this->mColorTitle,      $strTxt );	//タイトルのフォントカラー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_explain}',    $this->mColorExplain,    $strTxt );	//説明文のフォントカラー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_background}', $this->mColorBackground, $strTxt );	//全体の背景色
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_line}',       $this->mColorLine,       $strTxt );	//枠線の色
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_menubg}',     $this->mColorMenuBG,     $strTxt );	//メニューの背景色
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_menufont}',   $this->mColorMenuFont,   $strTxt );	//メニューのフォントカラー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_error}',      $this->mColorError,      $strTxt );	//エラー文のフォントカラー

        //テキスト関連の変数
        $strTxt = CCompatible::str_replace_allowed_arg( '{$txt_explain}',      $this->mTxtExplain,      $strTxt );	//説明文のテキスト
        $strTxt = CCompatible::str_replace_allowed_arg( '{$txt_confirm}',      $this->mTxtConfirm,      $strTxt );	//確認画面のテキスト
        $strTxt = CCompatible::str_replace_allowed_arg( '{$txt_exit}',         $this->mTxtExit,         $strTxt );	//メール送信後のテキスト

        //テンプレートの変数
        $strTxt = CCompatible::str_replace_allowed_arg( '{$template_css}',     $this->mTemplateCSS,     $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$template_input}',   $this->mTemplateInput,   $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$template_conf}',    $this->mTemplateConf,    $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$template_exit}',    $this->mTemplateExit,    $strTxt );

        //入力エラーがメッセージ
        if( $isChkError ){
            //定型
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_basic_error}', $this->DesignBasicValidate(), $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_color_error}', $this->DesignColorValidate(), $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_txt_error}',   $this->DesignTxtValidate(),   $strTxt );
            //自作
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_template_error}', $this->DesignTemplateValidate(), $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_basic_error}', '', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_color_error}', '', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_txt_error}', '', $strTxt );
            //自作
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_template_error}', '', $strTxt );
        }

        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'design_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //確認画面
    //=====================================
    public function getDesignConfirmHtml(){
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/design_conf.tpl' );

       //スキンのモード
        $strTxt = CCompatible::str_replace_allowed_arg( '{$design_mode}',  $this->mUseMyDesign,  $strTxt );

       //基本項目の変数
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formwidth}',  $this->mBasicFormWidth,  $strTxt );		////フォームの横幅の取得

       //色彩関連の変数
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_title}',      $this->mColorTitle,      $strTxt );	//タイトルのフォントカラー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_explain}',    $this->mColorExplain,    $strTxt );	//説明文のフォントカラー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_background}', $this->mColorBackground, $strTxt );	//全体の背景色
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_line}',       $this->mColorLine,       $strTxt );	//枠線の色
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_menubg}',     $this->mColorMenuBG,     $strTxt );	//メニューの背景色
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_menufont}',   $this->mColorMenuFont,   $strTxt );	//メニューのフォントカラー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$color_error}',      $this->mColorError,      $strTxt );	//エラー文のフォントカラー

       //テキスト関連の変数
        $strHtmlExplain = CCompatible::str_replace_allowed_arg( "\r\n", '<br />', $this->mTxtExplain );
        $strHtmlConfirm = CCompatible::str_replace_allowed_arg( "\r\n", '<br />', $this->mTxtConfirm );
        $strHtmlExit    = CCompatible::str_replace_allowed_arg( "\r\n", '<br />', $this->mTxtExit );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$html_txt_explain}', $strHtmlExplain, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$html_txt_confirm}', $strHtmlConfirm, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$html_txt_exit}',    $strHtmlExit,    $strTxt );

        $strTxt = CCompatible::str_replace_allowed_arg( '{$txt_explain}', $this->mTxtExplain,   $strTxt );	//説明文のテキスト
        $strTxt = CCompatible::str_replace_allowed_arg( '{$txt_confirm}', $this->mTxtConfirm,   $strTxt );	//確認画面のテキスト
        $strTxt = CCompatible::str_replace_allowed_arg( '{$txt_exit}',    $this->mTxtExit,      $strTxt );	//メール送信後のテキスト

        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'design_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //変更完了
    //=====================================
    public function getDesignExitHtml(){
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/design_exit.tpl' );

        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'design_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

}

?>

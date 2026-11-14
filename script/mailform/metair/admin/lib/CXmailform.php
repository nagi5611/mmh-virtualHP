<?php
//===========================================
//
//  エックスサーバーのフォーム制御
//
//===========================================
//エラー出力
//ini_set("display_errors", "On");


require_once dirname(dirname(__FILE__)) . '/config/password.php';
require_once dirname(__FILE__) . '/CCompatible.php';
require_once dirname(__FILE__) . '/CFormItems.php';
require_once dirname(__FILE__) . '/CFormItemBinder.php';
require_once dirname(__FILE__) . '/CReturnMail.php';
require_once dirname(__FILE__) . '/CVersion.php';


define( 'eEXCHANGE_STR', '$$\t$$' );


class CXmailform {
    var $mBasePageTitle;      //ページタイトル
    var $mBasicFormName;      //フォーム名の取得
    var $mBasicMoveURL;       //遷移先URLの取得
    var $mBasicMailaddress;   //送信先メールアドレスの取得
    var $mBasicMailSubject;   //送信メールの件名
    var $mBasicMailLimitTime; //メールの連続送信を規制する時間

    var $mReturnMail;         //自動返信機能の制御クラス

    var $mFormViewBinder;     //表示アイテムのバインダークラス
    var $mFormContentBinder;  //設定アイテムのバインダークラス

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
        $this->mBasePageTitle       = "";    //ページタイトル
        $this->mBasicFormName       = "";    //フォーム名の取得
        $this->mBasicMoveURL        = "";    //遷移先URLの取得
        $this->mBasicMailaddress    = "";    //送信先メールアドレスの取得
        $this->mBasicMailSubject    = "";    //送信メールの件名
        $this->mBasicMailLimitTime  = 0;     //メールの連続送信を規制する時間

        //自動返信機能の制御クラス
        $this->mReturnMail          = new CReturnMail();

        //フォームのバインダー
        $this->mFormViewBinder      = new CFormItemBinder();   //使用アイテムのバインダー
        $this->mFormContentBinder   = new CFormItemBinder();   //設定アイテムのバインダー

        $this->mFormContentBinder->AddItem( new CFullNameItem( 'item_name', 'お名前', 'お名前', 0, 'none', 0 ) );
        $this->mFormContentBinder->AddItem( new CFullNameItem( 'item_kana', 'ふりがな', 'ふりがな', 0, 'kana', 0 ) );
        $this->mFormContentBinder->AddItem( new CTextItem( 'item_hp', 'ＵＲＬ', 'ホームページ', 0, 'url', 0 ) );
        $this->mFormContentBinder->AddItem( new CTextItem( 'item_age', '年齢', '年齢', 0, 'numeric', 64 ) );
        $this->mFormContentBinder->AddItem( new CSexItem( 'item_sex', '性別', '性別', 0, 'none', 0 ) );
        $this->mFormContentBinder->AddItem( new CCodeItem( 'item_poscode', '郵便番号', '郵便番号', 0, 'numeric', 1 ) );
        $this->mFormContentBinder->AddItem( new CSelAreaItem( 'item_selarea', '都道府県', '都道府県', 0, 'none', 0 ) );
        $this->mFormContentBinder->AddItem( new CAddressItem( 'item_address', '住所', 'ご住所', 0, 'none', 0 ) );
        $this->mFormContentBinder->AddItem( new CTextItem( 'item_tel', 'ＴＥＬ', 'お電話番号', 0, 'tel', 0 ) );
        $this->mFormContentBinder->AddItem( new CTextItem( 'item_fax', 'ＦＡＸ', 'ＦＡＸ', 0, 'fax', 0 ) );
        $this->mFormContentBinder->AddItem( new CMailItem( 'item_mail', 'ご連絡先メールアドレス', 'ご連絡先メールアドレス', 0, 'mail', 0 ) );
        $this->mFormContentBinder->AddItem( new CTextItem( 'item_subject', '件名', '件名', 0, 'none', 0 ) );
        $this->mFormContentBinder->AddItem( new CTextAreaItem( 'item_contents', 'お問い合わせ内容', 'お問い合わせ内容', 0, 'none', 6 ) );

        $i = 0;
        for( $i=0;$i<9;$i++ ){
            $this->mFormContentBinder->AddItem( new CTextItem(     "item_stext0$i",    "予備テキスト0$i",         '予備テキスト',         0, 'none', 0 ) );
            $this->mFormContentBinder->AddItem( new CTextAreaItem( "item_sarea0$i",    "予備テキストボックス0$i", '予備テキストボックス', 0, 'none', 6 ) );
            $this->mFormContentBinder->AddItem( new CRadioItem(    "item_sradio0$i",   "予備ラジオボタン0$i",     '予備ラジオボタン',     0, 'none', '選択肢１,選択肢２' ) );
            $this->mFormContentBinder->AddItem( new CSelectItem(   "item_sselect0$i",  "予備プルダウンメニュー0$i",   '予備リストメニュー',   0, 'none', '選択肢１,選択肢２' ) );
            $this->mFormContentBinder->AddItem( new CCheckboxItem( "item_scheckbox0$i","予備チェックボックス0$i", '予備チェックボックス', 0, 'none', '選択肢１,選択肢２' ) );
        }
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
        //新しいロードにて読み込めない場合は、古いロードを適応する
        if( ! $this->LoadDataVer2( $strFileName ) ){
            $this->LoadDataVer1( $strFileName );

            //自動返信メールの情報を設定する
            $this->mReturnMail->InitVersionUp( $this->mBasicMailaddress );
        }

        //戻り先URLが未定の場合は、サーバー名を代入する
        if( ! $this->mBasicMoveURL ){
            $this->mBasicMoveURL = 'http://' . $_SERVER["SERVER_NAME"] . '/';
        }

    }
    //バージョンアップ前のロード
    public function LoadDataVer1( $strFileName ){
        //設定ファイルの呼び込み
        $lines = file( $strFileName );

        foreach( $lines as $value ) {
            $value = explode( "\t", trim($value) );

            if( isset( $value[1] ) ){
                $value[1] = CCompatible::str_replace_allowed_arg( eEXCHANGE_STR, "\t", $value[1] );

                //----------------
                //基本項目
                //----------------
                switch( $value[0] ){
                    case 'page_title':       $this->mBasePageTitle        = $value[1];    break;    //ページタイトル
                    case 'form_name':        $this->mBasicFormName        = $value[1];    break;    //フォーム名の取得
                    case 'move_url':         $this->mBasicMoveURL         = $value[1];    break;    //遷移先URLの取得
                    case 'mail_address':     $this->mBasicMailaddress     = $value[1];    break;    //送信先メールアドレスの取得
                    case 'mail_subject':     $this->mBasicMailSubject     = $value[1];    break;    //送信メールの件名
                    case 'mail_limit_time':  $this->mBasicMailLimitTime   = $value[1];    break;    //メールの連続送信を規制する時間
                }

                //----------------
                //設定項目
                //----------------
                switch( $value[0] ){
                    case 'text':       $this->mFormViewBinder->AddItem( new CTextItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );      break;
                    case 'textarea':   $this->mFormViewBinder->AddItem( new CTextAreaItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );  break;
                    case 'select':     $this->mFormViewBinder->AddItem( new CSelectItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );    break;
                    case 'radio':      $this->mFormViewBinder->AddItem( new CRadioItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );     break;
                    case 'fullname':   $this->mFormViewBinder->AddItem( new CFullNameItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );  break;
                    case 'address':    $this->mFormViewBinder->AddItem( new CAddressItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );   break;
                    case 'mail':       $this->mFormViewBinder->AddItem( new CMailItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );      break;
                    case 'code':       $this->mFormViewBinder->AddItem( new CCodeItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );      break;
                    case 'sex':        $this->mFormViewBinder->AddItem( new CSexItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );       break;
                    case 'selarea':    $this->mFormViewBinder->AddItem( new CSelAreaItem(  $value[2], $value[3], $value[4], $value[5], $value[1], $value[6] ) );   break;
                }

            }
        }

        //メールアドレスが未定の場合は、初期設定のメールアドレスを設定する
        if( ! $this->mBasicMailaddress ){
            $this->mBasicMailaddress = RECEIVE_MAILADDRESS;
        }
    }
    //バージョンアップ後のロード
    public function LoadDataVer2( $strFileName ){
        //設定ファイルの呼び込み
        $data = file_get_contents( $strFileName );
        $list = @unserialize( $data );

        //データが復元できない場合は、Ver.1の読み込みへ移行する
        if( $list === false ){
            return false;
        }

        $this->mBasePageTitle        = $list['page_title'];      //ページタイトル
        $this->mBasicFormName        = $list['form_name'];       //フォーム名の取得
        $this->mBasicMoveURL         = $list['move_url'];        //遷移先URLの取得
        $this->mBasicMailaddress     = $list['mail_address'];    //送信先メールアドレスの取得
        $this->mBasicMailSubject     = $list['mail_subject'];    //送信メールの件名
        $this->mBasicMailLimitTime   = $list['mail_limit_time']; //メールの連続送信を規制する時間

        $this->mReturnMail->LoadSaveData( $list['return_mail'] );
        
        $this->mFormViewBinder         = new CFormItemBinder();
        if (isset($list['binder_items']) && is_array($list['binder_items'])) {
            $this->mFormViewBinder->mItems = $list['binder_items'];
        } else {
            $this->mFormViewBinder->mItems = array();
        }
        
        //メールアドレスが未定の場合は、初期設定のメールアドレスを設定する
        if( ! $this->mBasicMailaddress ){
            $this->mBasicMailaddress = RECEIVE_MAILADDRESS;
        }

        return true;
    }


    //=====================================
    //設定ファイルのセーブ
    //=====================================
    public function SaveData( $strFileName ){
        $list = array();
        
        $list['page_title']      = $this->mBasePageTitle;       //ページタイトル
        $list['form_name']       = $this->mBasicFormName;       //フォーム名の取得
        $list['move_url']        = $this->mBasicMoveURL;        //遷移先URLの取得
        $list['mail_address']    = $this->mBasicMailaddress;    //送信先メールアドレスの取得
        $list['mail_subject']    = $this->mBasicMailSubject;    //送信メールの件名
        $list['mail_limit_time'] = $this->mBasicMailLimitTime;  //メールの連続送信を規制する時間

        //自動返信機能の保存
        $list['return_mail']     = $this->mReturnMail->CreateSaveData();

        //アイテム情報の保存
        $list['binder_items']    = $this->mFormViewBinder->mItems;

        //ファイルに出力を実行
        file_put_contents( $strFileName, serialize( $list ) );
    }
    
    //設定ファイルのセーブ（バージョンアップ前）
    //※現在は未使用であるが確認用のため記載する
    public function _SaveData( $strFileName ){
        $put_contents   = '';
        $put_contents  .= 'page_title'      . "\t" . CCompatible::str_replace_allowed_arg( "\t", eEXCHANGE_STR, $this->mBasePageTitle )      . "\r\n";  //ページタイトル
        $put_contents  .= 'form_name'       . "\t" . CCompatible::str_replace_allowed_arg( "\t", eEXCHANGE_STR, $this->mBasicFormName )      . "\r\n";  //フォーム名の取得
        $put_contents  .= 'move_url'        . "\t" . CCompatible::str_replace_allowed_arg( "\t", eEXCHANGE_STR, $this->mBasicMoveURL )       . "\r\n";  //遷移先URLの取得
        $put_contents  .= 'mail_address'    . "\t" . CCompatible::str_replace_allowed_arg( "\t", eEXCHANGE_STR, $this->mBasicMailaddress )   . "\r\n";  //送信先メールアドレスの取得
        $put_contents  .= 'mail_subject'    . "\t" . CCompatible::str_replace_allowed_arg( "\t", eEXCHANGE_STR, $this->mBasicMailSubject )   . "\r\n";  //送信メールの件名
        $put_contents  .= 'mail_limit_time' . "\t" . CCompatible::str_replace_allowed_arg( "\t", eEXCHANGE_STR, $this->mBasicMailLimitTime ) . "\r\n";  //メールの連続送信を規制する時間
        $put_contents  .= "\r\n";

        $put_contents  .= "\r\n";

        //セーブデータ形式の出力
        $put_contents  .= html_entity_decode( $this->mFormViewBinder->GetSaveData(), ENT_QUOTES, "UTF-8" );

        //ファイルに出力を実行
        file_put_contents( $strFileName, $put_contents );
    }


    //=====================================
    // データのロード
    //=====================================
    public function SetValue( $arrValues ){
        if( isset( $arrValues['page_title'] ) ){        $this->mBasePageTitle       = trim( $arrValues['page_title'] );         //ページタイトル
        }else{                                          $this->mBasePageTitle       = '';
        }
        if( isset( $arrValues['form_name'] ) ){         $this->mBasicFormName       = trim( $arrValues['form_name'] );          //フォーム名の取得
        }else{                                          $this->mBasicFormName       = '';
        }
        if( isset( $arrValues['move_url'] ) ){          $this->mBasicMoveURL        = trim( $arrValues['move_url'] );           //戻り先URL
        }else{                                          $this->mBasicMoveURL        = '';
        }
        if( isset( $arrValues['mail_address'] ) ){      $this->mBasicMailaddress    = trim( $arrValues['mail_address'] );       //送信先メールアドレスの取得
        }else{                                          $this->mBasicMailaddress    = '';
        }
        if( isset( $arrValues['mail_subject'] ) ){      $this->mBasicMailSubject    = trim( $arrValues['mail_subject'] );       //送信メールの件名
        }else{                                          $this->mBasicMailSubject    = '';
        }
        if( isset( $arrValues['mail_limit_time'] ) ){   $this->mBasicMailLimitTime  = trim( $arrValues['mail_limit_time'] );    //メールの連続送信を規制する時間
        }else{                                          $this->mBasicMailLimitTime  = 3;
        }

        //ＨＴＭＬタグを変換する
        $this->mBasePageTitle       = htmlspecialchars( $this->mBasePageTitle, ENT_QUOTES, "UTF-8" );
        $this->mBasicFormName       = htmlspecialchars( $this->mBasicFormName, ENT_QUOTES, "UTF-8" );
        $this->mBasicMoveURL        = htmlspecialchars( $this->mBasicMoveURL, ENT_QUOTES, "UTF-8" );
        $this->mBasicMailaddress    = htmlspecialchars( $this->mBasicMailaddress, ENT_QUOTES, "UTF-8" );
        $this->mBasicMailSubject    = htmlspecialchars( $this->mBasicMailSubject, ENT_QUOTES, "UTF-8" );
        $this->mBasicMailLimitTime  = htmlspecialchars( $this->mBasicMailLimitTime, ENT_QUOTES, "UTF-8" );

        //項目一覧を設定する
        $this->mFormViewBinder      = new CFormItemBinder();
        $num = $this->mFormContentBinder->Counter();
        for( $i=1;$i<=$num;$i++ ){
            if( ! isset( $arrValues["id"][$i] ) ){    break;    }

            $name    = $arrValues["id"][$i];

            $need    = 0;
            if( isset($arrValues["need"][$name]) ){
                $need    = $arrValues["need"][$name];
            }

            $option    = 0;
            if( isset($arrValues["option"][$name]) ){
                $option    = $arrValues["option"][$name];
            }

            $objData = NULL;
            switch( $arrValues["type"][$name] ){
                case 'text':        $objData = new CTextItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );        break;
                case 'textarea':    $objData = new CTextareaItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );    break;
                case 'select':      $objData = new CSelectItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );      break;
                case 'checkbox':    $objData = new CCheckboxItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );    break;
                case 'radio':       $objData = new CRadioItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );       break;
                case 'fullname':    $objData = new CFullNameItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );    break;
                case 'address':     $objData = new CAddressItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );     break;
                case 'mail':        $objData = new CMailItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );        break;
                case 'code':        $objData = new CCodeItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );        break;
                case 'sex':         $objData = new CSexItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );         break;
                case 'selarea':     $objData = new CSelAreaItem( $name, $arrValues["name"][$name], $arrValues["$name"], $need, $arrValues["check"][$name], $option );     break;
            }
            if( ! is_null($objData) ){
                $this->mFormViewBinder->AddItem( $objData );
            }
        }
    }


    //=====================================
    //エラー値チェック(管理画面)
    //=====================================
    public function SystemValidate(){
        $nCount     = 0;
        $strError   = '';

        $strError  .= "<strong>\r\n";
        $strError  .= "<ul>\r\n";

        //------------------------------------
        //入力値のチェック
        //------------------------------------
        if( ! $this->mBasePageTitle ){
            $strError  .= "<li>[ページのタイトル] を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasePageTitle, "UTF-8" ) > 500 ){
            $strError  .= "<li>[ページのタイトル] は 500 文字以内にして下さい</li>\r\n";
            $nCount++;
        }

        if( ! $this->mBasicFormName ){
            $strError  .= "<li>[メールフォームの名前] を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasicFormName, "UTF-8" ) > 500 ){
            $strError  .= "<li>[メールフォームの名前] は 500 文字以内にして下さい</li>\r\n";
            $nCount++;
        }

        if( ! $this->mBasicMoveURL ){
            $strError  .= "<li>[サイトへの戻りURL] を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasicMoveURL, "UTF-8" ) > 2000 ){
            $strError  .= "<li>[サイトへの戻りURL] は 2000 文字以内にして下さい</li>\r\n";
            $nCount++;
        }

        if( ! $this->mBasicMailaddress ){
            $strError  .= "<li>[お問合せ内容を受信するメールアドレス] を入力してください</li>\r\n";
            $nCount++;
        }elseif ( ! preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\.\+_-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $this->mBasicMailaddress) ){
            $strError  .= "<li>[お問合せ内容を受信するメールアドレス] の形式が正しくありません</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasicMailaddress, "UTF-8" ) > 1000 ){
            $strError  .= "<li>[お問合せ内容を受信するメールアドレス] は 1000 文字以内にして下さい</li>\r\n";
            $nCount++;
        }

        if( ! $this->mBasicMailSubject ){
            $strError  .= "<li>[受信するメールの件名] を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasicMailSubject, "UTF-8" ) > 500 ){
            $strError  .= "<li>[受信するメールの件名] は 500 文字以内にして下さい</li>\r\n";
            $nCount++;
        }

        if( $this->mBasicMailLimitTime === '' ){
            $strError  .= "<li>[メールの連続送信を制限する時間] を入力してください</li>\r\n";
            $nCount++;
        }elseif( ! is_numeric( $this->mBasicMailLimitTime ) ){
            $strError  .= "<li>[メールの連続送信を制限する時間] には数値を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasicMailLimitTime, "UTF-8" ) > 2 ){
            $strError  .= "<li>[メールの連続送信を制限する時間] は 2 桁以内にして下さい</li>\r\n";
            $nCount++;
        }else{
            $this->mBasicMailLimitTime = intval( $this->mBasicMailLimitTime );
            if( $this->mBasicMailLimitTime < 0 ){
                $strError  .= "<li>[メールの連続送信を制限する時間] ０分以上を設定してください</li>\r\n";
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
    //エラー値チェック(管理画面)
    //=====================================
    public function SystemPreviewValidate(){
        $nCount     = 0;
        $strError   = '';

        $strError  .= "<strong>\r\n";
        $strError  .= "<ul>\r\n";

        //------------------------------------
        //入力値のチェック
        //------------------------------------
        if( ! $this->mBasePageTitle ){
            $strError  .= "<li>[ページのタイトル] を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasePageTitle, "UTF-8" ) > 500 ){
            $strError  .= "<li>[ページのタイトル] は 500 文字以内にして下さい</li>\r\n";
            $nCount++;
        }

        if( ! $this->mBasicFormName ){
            $strError  .= "<li>[メールフォームの名前] を入力してください</li>\r\n";
            $nCount++;
        }elseif( mb_strlen( $this->mBasicFormName, "UTF-8" ) > 500 ){
            $strError  .= "<li>[メールフォームの名前] は 500 文字以内にして下さい</li>\r\n";
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
    //エラー値チェック(管理画面の設置項目)
    //=====================================
    public function SystemItemsValidate(){
        $arrErrors  = $this->mFormViewBinder->systemValidate();
        $strError   = '';

        //設定項目のエラーチェック
        if( count( $arrErrors ) ){
            $strError  .= "<br />\r\n";
            $strError  .= "<strong>\r\n";
            $strError  .= "<ul>\r\n";
            foreach( $arrErrors as $value ){
                $strError .= "<li>";
                $strError .= $value;
                $strError .= "</li>\r\n";
            }
            $strError  .= "</ul>\r\n";
            $strError  .= "</strong>\r\n";
            $strError  .= "<br />\r\n";
        }

        return $strError;
    }

    //=====================================
    //入力値の設定
    //=====================================
    public function SetUserValue(){
        $this->mFormViewBinder->SetUserValue( $_POST );
    }

    //=====================================
    //エラー値チェック(ユーザー画面)
    //=====================================
    public function UserValidate(){
        $strError   = '';
        $arrErrors  = $this->mFormViewBinder->validate( $_POST );

        //IPアドレスの制限設定も確認します
        if( ! $this->IPAccessCheck( $_SERVER['REMOTE_ADDR'], dirname(dirname(__FILE__)) . '/config/ip_check.ini', false ) ){
            $arrErrors[count($arrErrors)] = '連続投稿のためお問い合わせが行えません。しばらく時間を空けてから再度お問い合わせ下さい。';
        }

        //設定項目のエラーチェック
        if( count( $arrErrors ) ){
            $strError  .= "<br />\r\n";
            $strError  .= "<ul>\r\n";
            foreach( $arrErrors as $value ){
                $strError .= "<li>";
                $strError .= $value;
                $strError .= "</li>\r\n";
            }
            $strError  .= "</ul>\r\n";
            $strError  .= "<br />\r\n";
        }

        return $strError;
    }

    //=====================================
    //ユーザー向け入力用HTMLの取得
    //=====================================
    public function getUserInputHtml( $isMyDesign, $isChkError ){
        //テンプレの呼び込み
        $strTxt = '';
        if( $isMyDesign ){    //自作テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/user_template/use_index.tpl' );
            $strTxt = htmlspecialchars_decode( $strTxt );
        }else{                //定型テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/user_index.tpl' );
        }

        //--------------------------
        //既定文字の置換
        //--------------------------
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(), $strTxt );        //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(), $strTxt );         //遷移先URLの取得

        //使用アイテムのバインダー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_data}', $this->mFormViewBinder->getUserInputHtml(), $strTxt );

        //入力エラーがメッセージ
        if( $isChkError ){
            $strError   = $this->UserValidate();
            if( $strError == "" ){
                //入力値に問題がない場合は連続投稿のエラーチェック
                if( ! $mMailForm->IPAccessCheck( $_SERVER['REMOTE_ADDR'], dirname(__FILE__) . '/admin/config/ip_check.ini', false ) ){
                    $strError    = '';
                }
            }
            
            //入力値のエラー
            $strTxt     = CCompatible::str_replace_allowed_arg( '{$form_error}', $strError, $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_error}', '', $strTxt );
        }

        return $strTxt;
    }

    //=====================================
    //ユーザー向け確認用HTMLの取得
    //=====================================
    public function getUserConfirmHtml( $isMyDesign ){
        //テンプレの呼び込み
        $strTxt = '';
        if( $isMyDesign ){    //自作テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/user_template/use_conf.tpl' );
            $strTxt = htmlspecialchars_decode( $strTxt );
        }else{                //定型テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/user_conf.tpl' );
        }

        //--------------------------
        //既定文字の置換
        //--------------------------
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(), $strTxt );        //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(), $strTxt );         //遷移先URLの取得

        //使用アイテムのバインダー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_data}', $this->mFormViewBinder->getUserConfirmHtml(), $strTxt );

        return $strTxt;
    }

    //=====================================
    //ユーザー向け終了用HTMLの取得
    //=====================================
    public function getUserExitHtml( $isMyDesign ){
        //テンプレの呼び込み
        $strTxt = '';
        if( $isMyDesign ){    //自作テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/user_template/use_exit.tpl' );
            $strTxt = htmlspecialchars_decode( $strTxt );
        }else{                //定型テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/user_exit.tpl' );
        }

        //--------------------------
        //既定文字の置換
        //--------------------------
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(), $strTxt );        //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(), $strTxt );         //遷移先URLの取得

        return $strTxt;
    }


    //=====================================
    //管理者向け入力用HTMLの取得
    //=====================================
    public function getSystemInputHtml( $isChkError ){
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/system_index.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        //基本情報の置換
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(), $strTxt );        //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(), $strTxt );         //遷移先URLの取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_mailaddress}',   $this->_formBasicMailaddress(), $strTxt );     //送信先メールアドレスの取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_mailsubject}',   $this->_formBasicMailSubject(), $strTxt );     //送信メールの件名

        //メールの連続送信を規制する時間
        $strLimitTxt = '';
        for( $i=0;$i<=60;$i++ ){
            $strLimitTxt .= '<option value="' . $i . '"';
            if( $i == $this->_formBasicMailLimitTime() ){
                $strLimitTxt .= ' selected ';
            }
            $strLimitTxt .= '>' . $i . '</option>';
        }
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_maillimittime}', $strLimitTxt, $strTxt );

        //使用アイテムのバインダー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_view_data}', $this->mFormViewBinder->getSystemInputHtml(), $strTxt );

        //設定アイテムのバインダー
//        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_set_data}', $this->mFormContentBinder->getSystemHiddenInputHtml(), $strTxt );

        //入力エラーがメッセージ
        if( $isChkError ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_error}',       $this->SystemValidate(), $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_items_error}', $this->SystemItemsValidate(), $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_error}', '', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$form_items_error}', '', $strTxt );
        }

        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '<script type="text/javascript" src="./js/system_func.js"></script>', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'form_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //管理者向け確認用HTMLの取得
    //=====================================
    public function getSystemConfirmHtml(){
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/system_conf.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        //基本情報の置換
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(), $strTxt );        //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(), $strTxt );         //遷移先URLの取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_mailaddress}',   $this->_formBasicMailaddress(), $strTxt );     //送信先メールアドレスの取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_mailsubject}',   $this->_formBasicMailSubject(), $strTxt );     //送信メールの件名
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_maillimittime}', $this->_formBasicMailLimitTime(), $strTxt );   //メールの連続送信を規制する時間

        //使用アイテムのバインダー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_view_data}', $this->mFormViewBinder->getSystemConfirmHtml(), $strTxt );

        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'form_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //管理者向け終了用HTMLの取得
    //=====================================
    public function getSystemExitHtml(){
        //テンプレの呼び込み
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/system_exit.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'form_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //プレビューの出力
    //=====================================
    public function getPreviewInputHtml( $isMyDesign = 0, $strMyDesign = "" ){
        //テンプレの呼び込み
        $strTxt = '';
        if( $isMyDesign != 0 ){    //自作テンプレ
            $strTxt = html_entity_decode( $strMyDesign, ENT_QUOTES, "UTF-8" );
        }else{                //定型テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/preview_index.tpl' );
        }

        //--------------------------
        //既定文字の置換
        //--------------------------
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_error}',          '',                           $strTxt );       //ページエラー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(),  $strTxt );       //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(),   $strTxt );       //遷移先URLの取得

        //使用アイテムのバインダー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_data}', $this->mFormViewBinder->getUserInputHtml(), $strTxt );

        //リンクタグの追加
//      $strTxt = $this->setPreviewTag( $strTxt );
        return $strTxt;
    }

    //=====================================
    //プレビューの出力
    //=====================================
    public function getPreviewConfHtml( $isMyDesign = 0, $strMyDesign = "" ){
        //テンプレの呼び込み
        $strTxt = '';
        if( $isMyDesign != 0 ){    //自作テンプレ
            $strTxt = html_entity_decode( $strMyDesign, ENT_QUOTES, "UTF-8" );
        }else{                //定型テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/preview_conf.tpl' );
        }

        //--------------------------
        //既定文字の置換
        //--------------------------
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(), $strTxt );        //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(), $strTxt );         //遷移先URLの取得

        //使用アイテムのバインダー
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_data}', $this->mFormViewBinder->getUserConfirmHtml(), $strTxt );

        //リンクタグの追加
//      $strTxt = $this->setPreviewTag( $strTxt );
        return $strTxt;
    }

    //=====================================
    //プレビューの出力
    //=====================================
    public function getPreviewExitHtml( $isMyDesign = 0, $strMyDesign = "" ){
        //テンプレの呼び込み
        $strTxt = '';
        if( $isMyDesign != 0 ){    //自作テンプレ
            $strTxt = html_entity_decode( $strMyDesign, ENT_QUOTES, "UTF-8" );
        }else{                //定型テンプレ
            $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/preview_exit.tpl' );
        }

        //--------------------------
        //既定文字の置換
        //--------------------------
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_pagetitle}',     $this->_formBasicPageTitle(), $strTxt );       //ページタイトル
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_formname}',      $this->_formBasicFormName(), $strTxt );        //フォーム名の取得
        $strTxt = CCompatible::str_replace_allowed_arg( '{$basic_moveurl}',       $this->_formBasicMoveURL(), $strTxt );         //遷移先URLの取得

        //リンクタグの追加
//      $strTxt = $this->setPreviewTag( $strTxt );
        return $strTxt;
    }

    //=====================================
    //プレビューのエラー出力
    //=====================================
    public function getPreviewErrorHtml( $strError ){
        //テンプレの呼び込み
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/preview_error.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        $strTxt = CCompatible::str_replace_allowed_arg( '{$error_txt}', $strError, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strTxt
        );

        return $strTxt;
    }

    //=====================================
    //プレビュー用のタグ設定
    //※<body>タグ直後にURLを記載する
    //=====================================
    public function setPreviewTag( $strHtml ){
        $strTag  = '';
        $strTag .= '<div>';
        $strTag .= '[<a href="./preview.php">入力画面のプレビュー</a>]';
        $strTag .= '　';
        $strTag .= '[<a href="./preview.php?page=conf">確認画面のプレビュー</a>]';
        $strTag .= '　';
        $strTag .= '[<a href="./preview.php?page=exit">完了画面のプレビュー</a>]';
        $strTag .= '</div>';
        //--------------------------
        //既定文字の置換
        //--------------------------
        $nStartPoint = strpos( $strHtml, '<body' );
        if( $nStartPoint === false ){   return $strHtml;    }
        $nStartPoint = strpos( $strHtml, '>', $nStartPoint );
        if( $nStartPoint === false ){   return $strHtml;    }
        $nStartPoint++;

        $strAddHtml  = '';
        $strAddHtml .= mb_substr( $strHtml, 0,            $nStartPoint );
        $strAddHtml .= $strTag;
        $strAddHtml .= mb_substr( $strHtml, $nStartPoint, strlen($strHtml) );

        return $strAddHtml;
    }


    //=====================================
    //自動返信メールの入力値チェック
    //=====================================
    public function OptionValidate()
    {
        $strError   = '';
        $arrErrors  = array();

        $used      = $this->mReturnMail->mIsUsed;
        $subject   = trim( $this->mReturnMail->mMailSubject );
        $body      = trim( $this->mReturnMail->mMailBody );
        $from      = trim( $this->mReturnMail->mFromAddress );

        //自動返信を利用するときのみ、入力値チェックを行う
        if( $used != 0 ){
            //メールの件名
            if( $subject == '' ){
                $arrErrors[count($arrErrors)] = '[メールの件名] を入力してください';
            }elseif( mb_strlen( $subject, "UTF-8" ) > 500 ){
                $arrErrors[count($arrErrors)] = '[メールの件名] は 500 文字以内にして下さい';
            }

            //メールの本文
            if( $body == '' ){
                $arrErrors[count($arrErrors)] = '[メールの本文] を入力してください';
            }elseif( mb_strlen( $body, "UTF-8" ) > 2000 ){
                $arrErrors[count($arrErrors)] = '[メールの本文] は 2000 文字以内にして下さい';
            }

            //差出人のメールアドレス
            if( $from == '' ){
                $arrErrors[count($arrErrors)] = '[差出人のアドレス] を入力してください';
            }elseif( mb_strlen( $from, "UTF-8" ) > 500 ){
                $arrErrors[count($arrErrors)] = '[差出人のアドレス] は 500 文字以内にして下さい';
            }elseif ( ! preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\.\+_-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $from) ){
                $arrErrors[count($arrErrors)] = '[差出人のアドレス] の形式が正しくありません';
            }
        }

        //設定項目のエラーチェック
        if( count( $arrErrors ) ){
            $strError  .= "<br />\r\n";
            $strError  .= "<ul>\r\n";
            foreach( $arrErrors as $value ){
                $strError .= "<li>";
                $strError .= $value;
                $strError .= "</li>\r\n";
            }
            $strError  .= "</ul>\r\n";
            $strError  .= "<br />\r\n";
        }

        return $strError;
    }

    //=====================================
    //自動返信メールの入力値の設定
    //=====================================
    public function SetOptionValue( $arrValues )
    {
        if( isset( $arrValues['auto_mail'] ) ){     $this->mReturnMail->mIsUsed       = trim( $arrValues['auto_mail'] );   }
        if( isset( $arrValues['rmail_subject'] ) ){ $this->mReturnMail->mMailSubject  = trim( $arrValues['rmail_subject'] );}
        if( isset( $arrValues['rmail_body'] ) ){    $this->mReturnMail->mMailBody     = trim( $arrValues['rmail_body'] );   }
        if( isset( $arrValues['from_address'] ) ){  $this->mReturnMail->mFromAddress  = trim( $arrValues['from_address'] );}
        $this->mReturnMail->mMailSubject  = htmlspecialchars( $this->mReturnMail->mMailSubject, ENT_QUOTES, "UTF-8" );
        $this->mReturnMail->mMailBody     = htmlspecialchars( $this->mReturnMail->mMailBody, ENT_QUOTES, "UTF-8" );
        $this->mReturnMail->mFromAddress  = htmlspecialchars( $this->mReturnMail->mFromAddress, ENT_QUOTES, "UTF-8" );
    }

    //=====================================
    //管理者向け自動返信メールの入力用HTMLの取得
    //=====================================
    public function getOptionInputHtml( $isChkError )
    {
        //テンプレの呼び込み
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/option_index.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'option_setting', $strHeader );

        for( $i=0;$i<2;$i++ ){
            if( $i == $this->mReturnMail->mIsUsed ){    $strTxt = CCompatible::str_replace_allowed_arg( '{$used_auto_' . $i . '}', 'selected', $strTxt );
            }else{                                      $strTxt = CCompatible::str_replace_allowed_arg( '{$used_auto_' . $i . '}', '', $strTxt );
            }
        }

        //自動返信フォームの表示設定
        if( $this->mReturnMail->mIsUsed != 0 ){ $strTxt = CCompatible::str_replace_allowed_arg( '{$set_used_automail}', 'style="display:block"', $strTxt );
        }else{                                  $strTxt = CCompatible::str_replace_allowed_arg( '{$set_used_automail}', 'style="display:none"', $strTxt );
        }
 
        $strTxt = CCompatible::str_replace_allowed_arg( '{$mail_subject}', $this->mReturnMail->mMailSubject, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$mail_body}',    $this->mReturnMail->mMailBody,    $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$from_address}', $this->mReturnMail->mFromAddress, $strTxt );

        //入力エラーがメッセージ
        if( $isChkError ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$automail_error}', $this->OptionValidate(), $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$automail_error}', '', $strTxt );
        }

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //管理者向け自動返信メールの終了用HTMLの取得
    //=====================================
    public function getOptionConfirmHtml()
    {
        //テンプレの呼び込み
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/option_conf.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'option_setting', $strHeader );

        //テキスト関連の変数
        $strHtmlMailBody = CCompatible::str_replace_allowed_arg( "\r\n", '<br />', $this->mReturnMail->mMailBody );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$html_mail_body}', $strHtmlMailBody, $strTxt );

        if( $this->mReturnMail->mIsUsed != 0 ){
            $strTxt = CCompatible::str_replace_allowed_arg( '{$html_auto_mail}', '自動返信メールを使用する', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$set_used_automail}', 'style="display:block"', $strTxt );
        }else{
            $strTxt = CCompatible::str_replace_allowed_arg( '{$html_auto_mail}', '自動返信メールを使用しない', $strTxt );
            $strTxt = CCompatible::str_replace_allowed_arg( '{$set_used_automail}', 'style="display:none"', $strTxt );
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$auto_mail}',    $this->mReturnMail->mIsUsed,      $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$mail_subject}', $this->mReturnMail->mMailSubject, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$mail_body}',    $this->mReturnMail->mMailBody,    $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$from_address}', $this->mReturnMail->mFromAddress, $strTxt );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //管理者向け自動返信メールの終了用HTMLの取得
    //=====================================
    public function getOptionExitHtml()
    {
        //テンプレの呼び込み
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/option_exit.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        //設置先URLの切り替え
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'option_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    //設置用HTMLタグの取得
    //=====================================
    public function getSetHtml(){
        //テンプレの呼び込み
        $strHeader = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_header.tpl' );
        $strFooter = file_get_contents( dirname(dirname(__FILE__)) . '/template/admin_footer.tpl' );
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/sethtml_index.tpl' );

        //--------------------------
        //既定文字の置換
        //--------------------------
        //設置先URLの切り替え
        $strTxt = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strTxt );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$system_js}', '', $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$form_seturl}', CXmailform::_formSetFormTag(), $strHeader );
        $strHeader = CCompatible::str_replace_allowed_arg( '{$setting_body_id}', 'sethtml_setting', $strHeader );

        // version表記設定
        $strFooter = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strFooter
        );

        return $strHeader . $strTxt . $strFooter;
    }

    //=====================================
    // メールの送信処理
    //=====================================
    function SendMail()
    {
        $reply_to      = $this->GetReplyTo();
        $mail_contetns = $this->mFormViewBinder->GetMailData();

        mb_language("ja");
        mb_internal_encoding("ISO-2022-JP");

        //ヘッダの構成
        $AddHeader  = '';
        $AddHeader .= "Content-Type: text/plain;charset=ISO-2022-JP\r\n";
        $AddHeader .= "Content-Transfer-Encoding: 7bit\r\n";
        $AddHeader .= "MIME-Version: 1.0\r\n";
        $AddHeader .= "From: " . MAILADDRESS . "\n";
        $AddHeader .= "Reply-To: " . $reply_to . "\r\n";

        //メールの件名
        $Subject = html_entity_decode( $this->mBasicMailSubject, ENT_QUOTES, "UTF-8" );
        $Subject = mb_convert_encoding( $Subject, "ISO-2022-JP","UTF-8" );
        $Subject = mb_encode_mimeheader( $Subject,"ISO-2022-JP" );

        //メールの内容
        $Mail = '';
        $Mail .= '下記のお問い合わせを受信いたしました。' . "\r\n\r\n";
        $Mail .= html_entity_decode( $mail_contetns, ENT_QUOTES, "UTF-8" );
        $Mail  = mb_convert_encoding( $Mail, "ISO-2022-JP", "UTF-8" );

        //管理者に送信実行
        $ret    = mail( 
                $this->mBasicMailaddress,
                $Subject,
                $Mail,
                $AddHeader );

        if( $ret ){
            $this->mReturnMail->SendMail( $reply_to, html_entity_decode( $mail_contetns, ENT_QUOTES, "UTF-8" ) );
        }

        return $ret;
    }

    //=====================================
    // Reply-To に設定するご連絡先メールアドレスを取得する
    //=====================================
    function GetReplyTo()
    {
        //初期返信先を空白に設定する
        $reply_to = '';

        foreach( $this->mFormViewBinder->mItems as $data ){
            //ご連絡先メールアドレス以外はハジく
            if( $data->mCheckType != 'mail' ){
                continue;
            }

            //ご連絡先メールアドレスを設定する
            $reply_to = $data->mValue;
            break;
        }

        return $reply_to;
    }

    //=====================================
    // ２重POSTの確認
    //=====================================
    function TwoPostCheck( $strIP, $strFileName ){
        //IP未指定のときはtrue返して終了
        if(!$strIP){
            return true;
        }

        //時間の確認(保持期間は１時間)
        $checktime = 3600;

        //時間、ＩＰアドレスおよび入力値より
        //ハッシュ値を作成し、２重POSTを防止する
        $strPostHash  = md5( date("Y/m/d H") . $strIP . serialize($this->mFormViewBinder) );

        //記録ファイル読み込み
        $logfp = fopen($strFileName, 'r+');
        if(filesize($strFileName) > 0){
            $content = fread($logfp, filesize($strFileName));
        }else{
            $content = "";
        }
        fclose($logfp);

        //改行で配列化
        $content_list = explode("\n", $content);

        //初アクセスのときは時間記録して終了
        if( !is_array( $content_list ) ||
            count( $content_list )==0 ||
            trim( $content_list[0] )==""){

            $fp = fopen( $strFileName, 'a+' );
            fwrite( $fp, $strIP . " " . time() . " " . $strPostHash . "\n" );
            fclose( $fp );
            return true;
        }

        //アクセス時間チェック
        $time=0;
        $chkflag=0;
        $content_edit = "";
        for( $i=0; $i<count($content_list); $i++ ){
            $arr = explode(" ", $content_list[$i]);
            if( trim($arr[0]) == "" || trim($arr[1]) == "" || trim($arr[2]) == "" ){continue;}

            //発見
            if( trim($arr[0]) == trim($strIP) && trim($arr[2]) == trim($strPostHash)){
                $time = $arr[1];
                $chkflag = 1;
                $content_edit .= $strIP . " " . $arr[1] . " " . $arr[2] . "\n";
            }else{
                $c_time = time() - $arr[1];
                if($c_time > $checktime){
                    continue;
                }

                $content_edit .= $content_list[$i] . "\n";
            }
        }

        //編集内容の書き込み
        $fp = fopen($strFileName, 'w');
        fwrite($fp, $content_edit);
        fclose($fp);

        //規定時間以内ならfalseを返す
        if($time){
            $time = time() - $time;
            if($time <= $checktime){
                return false;
            }
        }

        return true;
    }

    //=====================================
    // 指定時間ないの重複アクセスの場合にfalseを返す
    //=====================================
    function IPAccessCheck( $strIP, $strFileName, $isWrite=true )
    {
        //IP未指定のときはtrue返して終了
        if(!$strIP){
            return true;
        }

        $checktime = ( $this->mBasicMailLimitTime * 60 );
        if( $checktime < 0 ){   $checktime = 1; }

        //記録ファイル読み込み
        $logfp = fopen($strFileName, 'r+');
        if(filesize($strFileName) > 0){
            $content = fread($logfp, filesize($strFileName));
        }else{
            $content = "";
        }
        fclose($logfp);

        //改行で配列化
        $content_list = explode("\n", $content);

        //初アクセスのときは時間記録して終了
        if( !is_array( $content_list ) ||
            count( $content_list )==0 ||
            trim( $content_list[0] )==""){
            if( $isWrite ){
                $fp = fopen($strFileName, 'a+');
                fwrite($fp,  $strIP." ".time()."\n");
                fclose($fp);
            }
            return true;
        }

        //アクセス時間チェック
        $time=0;
        $chkflag=0;
        $content_edit = "";
        for( $i=0; $i<count($content_list); $i++ ){
            $arr = explode(" ", $content_list[$i]);
            if( trim($arr[0])=="" || trim($arr[1])=="" ){continue;}

            //発見
            if(trim($arr[0]) == trim($strIP)){
                $time = $arr[1];
                $chkflag = 1;
                $content_edit .= $arr[0]." ".time()."\n";
            }else{
                $c_time = time() - $arr[1];
                if($c_time > $checktime){
                    continue;
                }

                $content_edit .= $content_list[$i]."\n";
            }
        }

        //IPアドレスが発見できなければ追記する
        if( $chkflag == 0 ){
            $content_edit .= $strIP . " " . time() . "\n";
        }

        if( $isWrite ){
            //編集内容の書き込み
            $fp = fopen($strFileName, 'w');
            fwrite($fp, $content_edit);
            fclose($fp);
        }

        //規定時間以内ならfalseを返す
        if($time){
            $time = time() - $time;
            if($time <= $checktime){
                return false;
            }
        }

        return true;
    }


    //------------------------------------------------------
    //=====================================
    // アイテムが０個の場合のチェック
    //=====================================
    public function CheckItemNum(){
        if( $this->mFormViewBinder->Counter() > 0 ){
            return true;
        }
        return false;
    }

    //=====================================
    // mbstring.encoding_translation = On 用の対策
    //=====================================
    public function CheckTranslationEncoding(){
        $default     = 'UTF-8';
        $translaton  = ini_get('mbstring.encoding_translation');
        $encoding    = ini_get('mbstring.internal_encoding');
        if( $translaton ){
            $_GET       = $this->CheckArrayStringEncode( $_GET,     $default, $encoding );
            $_POST      = $this->CheckArrayStringEncode( $_POST,    $default, $encoding );
            $_REQUEST   = $this->CheckArrayStringEncode( $_REQUEST, $default, $encoding );
            $_COOKIE    = $this->CheckArrayStringEncode( $_COOKIE,  $default, $encoding );
        }
    }
    private function CheckArrayStringEncode( $arrData, $strOutputEncode, $strInputEncode ) {
        $strDefaultEncode = 'UTF-8';
        if( ! $this->CheckEffectiveEncode( $strInputEncode ) ){
            $strInputEncode = $strDefaultEncode;
        }
        if( ! $this->CheckEffectiveEncode( $strOutputEncode ) ){
            $strOutputEncode = $strDefaultEncode;
        }
        if( is_array( $arrData ) ){
            foreach( $arrData as $key => $value ) {
                $arrData[$key] = $this->CheckArrayStringEncode( $value, $strOutputEncode, $strInputEncode );
            }
            return $arrData;
        }
        return mb_convert_encoding( $arrData, $strOutputEncode, $strInputEncode );
    }

    //既存の文字コードであるかを判定する
    public function CheckEffectiveEncode( $strEncode ){
        switch( $strEncode ){
            case 'ASCII':  return true;
            case 'JIS':    return true;
            case 'UTF-8':  return true;
            case 'EUC-JP': return true;
            case 'SJIS':   return true;
        }
        return false;
    }

    //=====================================
    // magic_quotes_gpc = On 用の対策
    //=====================================
    public function CheckMagicQuotesGpc(){
        // PHP7.4.0未満で、magic_quotes_gpcが有効の場合
        if(version_compare(phpversion(), '7.4.0', '<') && get_magic_quotes_gpc()) {
            $_GET       = $this->CheckStripslashes( $_GET );
            $_POST      = $this->CheckStripslashes( $_POST );
            $_REQUEST   = $this->CheckStripslashes( $_REQUEST );
            $_COOKIE    = $this->CheckStripslashes( $_COOKIE );
        }
    }
    private function CheckStripslashes( $arrData ) {
        if( is_array( $arrData ) ){
            foreach( $arrData as $key => $value ){
                $arrData[$key] = $this->CheckStripslashes( $value );
            }
            return $arrData;
        }
        return stripslashes( $arrData );
    }

    //------------------------------------------------------
    // メールフォーム設置用のHTMLタグ
    //------------------------------------------------------
    public static function _formSetFormTag(){
        //設定先URLを生成する
        $strURL   = '';
        $strURL  .= 'http://';
        $strURL  .= $_SERVER["SERVER_NAME"];
        $strURL  .= $_SERVER["SCRIPT_NAME"];

        //現在のURLから設置先URLを取得
        $chkpos   = strrpos( $strURL, '/' );
        if( $chkpos > 10 ){ $strURL = substr( $strURL, 0, $chkpos );    }
        $chkpos   = strrpos( $strURL, '/' );
        if( $chkpos > 10 ){ $strURL = substr( $strURL, 0, $chkpos + 1 );    }

        return $strURL;
    }

    //------------------------------------------------------
    // private 関数
    private function _formBasicPageTitle(){     return $this->mBasePageTitle;   }   //ページタイトル
    private function _formBasicFormName(){      return $this->mBasicFormName;   }   //フォーム名の取得
    private function _formBasicMoveURL(){       return $this->mBasicMoveURL;    }   //遷移先URLの取得
    private function _formBasicMailaddress(){   return $this->mBasicMailaddress;}   //送信先メールアドレスの取得
    private function _formBasicMailSubject(){   return $this->mBasicMailSubject;}   //送信メールの件名
    private function _formBasicMailLimitTime(){ return $this->mBasicMailLimitTime;} //メールの連続送信を規制する時間


}

?>

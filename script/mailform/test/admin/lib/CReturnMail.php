<?php
//=============================================================
//
//　自動返信メールの管理クラス
//
//=============================================================
//エラー出力
//ini_set("display_errors", "On");


require_once dirname(__FILE__) . '/CCompatible.php';

class CReturnMail {
    var $mIsUsed;         //仕様フラグ
    var $mMailSubject;    //メールの件名
    var $mMailBody;       //メールの本文
    var $mFromAddress;    //送信元(From)のアドレス

    //=============================================
    //
    // コンストラクタ
    //
    //=============================================
    function __construct(){
        $this->Init();
    }

    //=============================================
    // 初期化
    //=============================================
    function Init() {
        $this->mIsUsed       = 0;
        $this->mFromAddress  = '';
        $this->mMailSubject  = '';
        $this->mMailBody     = '';
    }

    //=============================================
    // 初期化(バージョンアップ時の初期値)
    // $strFromAddress   ：送信元(From)に記載するメールアドレス
    // 返り値            ：なし
    //=============================================
    function InitVersionUp( $strFromAddress ) {
        $this->mIsUsed       = 0;
        $this->mFromAddress  = $strFromAddress;
        $this->mMailSubject  = "お問い合わせ完了のお知らせ";

        $this->mMailBody     = "";
        $this->mMailBody    .= "お問い合わせいただき、ありがとうございます。\r\n";
        $this->mMailBody    .= "下記の内容にて、お問い合わせをお受けいたしました。\r\n";
        $this->mMailBody    .= "---------------------------------------------------\r\n";
        $this->mMailBody    .= "###お問い合わせ内容###\r\n";
    }

    //=============================================
    // 設定情報の保存用配列の作成
    // 返り値            ：情報の配列データ
    //=============================================
    function CreateSaveData() {
        $list = array();
        $list['is_used']       = $this->mIsUsed;
        $list['rmail_subject'] = $this->mMailSubject;
        $list['rmail_body']    = $this->mMailBody;
        $list['from_address']  = $this->mFromAddress;

        return $list;
    }

    //=============================================
    // 設定情報の保存用配列の作成
    // 返り値            ：情報の配列データ
    //=============================================
    function LoadSaveData( $arrList ) {
        if( isset( $arrList['is_used'] ) ){        $this->mIsUsed      = $arrList['is_used'];       }
        if( isset( $arrList['rmail_subject'] ) ){  $this->mMailSubject = $arrList['rmail_subject'];  }
        if( isset( $arrList['rmail_body'] ) ){     $this->mMailBody    = $arrList['rmail_body'];     }
        if( isset( $arrList['from_address'] ) ){   $this->mFromAddress = $arrList['from_address'];  }
    }

    //=====================================
    // メールの送信処理
    // $strSendAddress   ：送信先のメールアドレス
    // $strRecContents   ：受信したお問い合わせの内容
    //=====================================
    function SendMail( $strSendAddress, $strRecContents )
    {
        //入力データの確認
        if( $strSendAddress == '' ){      return false;   }
        if( $strRecContents == '' ){      return false;   }

        //使用状態かの確認
        if( ! $this->mIsUsed ){           return false;   }
        if( $this->mFromAddress == '' ){  return false;   }

        mb_language("ja");
        mb_internal_encoding("ISO-2022-JP");

        //ヘッダの構成
        $AddHeader  = '';
        $AddHeader .= "Content-Type: text/plain;charset=ISO-2022-JP\r\n";
        $AddHeader .= "Content-Transfer-Encoding: 7bit\r\n";
        $AddHeader .= "MIME-Version: 1.0\r\n";
        $AddHeader .= "From: " . $this->mFromAddress . "\n";

        //メールの件名
        $Subject = html_entity_decode( $this->mMailSubject, ENT_QUOTES, "UTF-8" );
        $Subject = mb_convert_encoding( $Subject, "ISO-2022-JP","UTF-8" );
        $Subject = mb_encode_mimeheader( $Subject,"ISO-2022-JP" );

        //メールの内容
        $Mail = html_entity_decode( $this->mMailBody, ENT_QUOTES, "UTF-8" );
        $Mail = CCompatible::str_replace_allowed_arg( '###お問い合わせ内容###', $strRecContents, $Mail );
        $Mail = mb_convert_encoding( $Mail, "ISO-2022-JP", "UTF-8" );

        //管理者に送信実行
        $ret    = mail( 
                $strSendAddress,
                $Subject,
                $Mail,
                $AddHeader );
        return $ret;
    }
}

?>

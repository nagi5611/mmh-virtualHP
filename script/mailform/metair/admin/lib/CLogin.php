<?php

// セッションの開始
session_start();

//---------------------------------
//　上部メニュー(リンク)の処理
//---------------------------------
//ログアウトの処理
if( isset( $_GET['logout'] ) ){
    if( $_GET['logout'] == 'on' ){
        $_SESSION = array();
        session_destroy();
        header( "Location: ./" );
        exit;
    }
}

require_once dirname(dirname(__FILE__)) . '/config/password.php';
require_once dirname(__FILE__) . '/CCompatible.php';
require_once dirname(__FILE__) . '/CVersion.php';

// ログイン制御
$mLogin = new CLogin();

//チェック
if( ! $mLogin->Login() ){
    //ログイン中でない場合は、ログインページを表示する
    echo $mLogin->GetLoginPage();
    exit;
}


//===========================================
//
//  ログイン制御
//
//===========================================
class CLogin {

    //===============================================
    // ログインチェックを行う
    // 返り値：ログイン中 true、ログイン前 false
    //===============================================
    public function Login() {
        //セッション変数を確認
        if( isset( $_SESSION['ID'] ) && isset( $_SESSION['PASSTYPE'] ) && isset( $_SESSION['PASSWORD'] ) ){
            if( $_SESSION['ID'] == ID ){
                if( $_SESSION['PASSTYPE'] == 'PASSWORD'  && $_SESSION['PASSWORD'] == PASSWORD  ){ return true; }
                if( $_SESSION['PASSTYPE'] == 'PASSWORD2' && $_SESSION['PASSWORD'] == PASSWORD2 ){ return true; }
            }
        }

        //ポスト値の確認
        $passtype = '';
        if( isset( $_POST['username'] ) && isset( $_POST['password'] ) ){
            //入力フォームからの入力の場合は「PASSWORD」
            //管理パネルからの遷移の場合は「PASSWORD2」を設定する
            if( $_POST['username'] == ID  && md5( $_POST['password'] ) == PASSWORD ){  $passtype = 'PASSWORD';  }
            if( $_POST['username'] == ID  && md5( $_POST['password'] ) == PASSWORD2 ){ $passtype = 'PASSWORD2'; }

            if( $passtype ){
                //セッション変数へ保存する
                $_SESSION['ID']         = $_POST['username'];
                $_SESSION['PASSWORD']   = md5( $_POST['password'] );
                $_SESSION['PASSTYPE']   = $passtype;

                //変数内部の値を初期化する(次の画面の影響を抑えるため)
                $_POST['username']      = '';
                $_POST['password']      = '';
                $_POST['login_button']  = '';
                return true;
            }
        }

        return false;
    }

    //===============================================
    // ログイン状態の確認
    // 返り値：ログイン中 true、ログイン前 false
    //===============================================
    public function GetLoginPage(){
        $strTxt = file_get_contents( dirname(dirname(__FILE__)) . '/template/system_login.tpl' );
        $errTxt = '';
        if( isset( $_POST['login_button'] ) ){
            if( $_POST['login_button'] == 'ログイン' ){
                //ログインに失敗したときのみエラー文字を表示
                $errTxt .= '<br />';
                $errTxt .= '<div class="red_txt">';
                $errTxt .= '<ul><li>';
                $errTxt .= 'ユーザーＩＤもしくはパスワードが間違っています';
                $errTxt .= '</li></ul>';
                $errTxt .= '</div>';
            }
        }

        //置換用文字列を変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$error_txt}', $errTxt, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg(
            '{$version}',
            CVersion::getVersionString(),
            $strTxt
        );

        return $strTxt;
    }

}

?>

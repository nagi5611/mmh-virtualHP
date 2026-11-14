<?php
//=============================================================
//
//　フォームのアイテムバインダー
//
//=============================================================
//エラー出力
//ini_set("display_errors", "On");


require_once dirname(__FILE__) . '/CCompatible.php';

class CFormItemBinder {
    var $mItems;

    //=============================================
    //
    // コンストラクタ
    //
    //=============================================
    function __construct(){
        $this->mItems    = array();
        
    }

    //=============================================
    // アイテムの取得
    // $nItemID   ：取得するアイテム番号
    // 返り値     ：取得アイテム
    //=============================================
    public function Item( $nItemID ){
        return $this->mItems[$nItemID];
    }

    //=============================================
    // アイテム個数を返す
    //=============================================
    public function Counter(){
        return count( $this->mItems );
    }

    //=============================================
    // アイテムの検索
    // $nItemID   ：取得するアイテム番号
    // 返り値     ：取得アイテム
    //=============================================
    public function SearchName( $nItemName ){
        foreach( $this->mItems as $value ){
            if( $nItemName == $value->mName ){
                return $value;
            }
        }

        return NULL;
    }

    //=============================================
    // アイテムの追加
    // $objItem   ：追加するアイテムオブジェクト
    // 返り値     ：アイテム追加の成否
    //=============================================
    public function AddItem( $objItem ){
        $count = count( $this->mItems );

        $this->mItems[$count++] = $objItem;
        return true;
    }

    //=============================================
    // アイテムの削除
    // $nItemName   ：削除するアイテム名
    //=============================================
    public function DeleteItem( $nItemID ){
        for( $i=$nItemID;$i<($count-1);$i++ ){
            $this->mItems[$i] = $this->mItems[( $i + 1 )];
        }
        $this->mItems[($count-1)] = NULL;
    }

    //=============================================
    // アイテムの入れ替え
    // $nItemName01 ：入れ替えるアイテム名
    // $nItemName02 ：入れ替えるアイテム名
    //=============================================
    public function ChangeItem( $nItemName01, $nItemName02 ){
        $objStackItem = $this->mItems[$nItemName01];
        $this->mItems[$nItemName01] = $this->mItems[$nItemName02];
        $this->mItems[$nItemName02] = $objStackItem;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function GetMailData(){
        $strTxt = '';
        foreach( $this->mItems as $value ){
            $strTxt .= $value->GetMailData();
        }
        return $strTxt;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function SetUserValue( $arrRequest ){
        foreach( $this->mItems as $value ){
            $value->SetUserValue( $arrRequest );
        }
    }

    //=============================================
    // セーブデータの形式を返す
    //=============================================
    public function GetSaveData(){
        $strTxt = '';
        foreach( $this->mItems as $value ){
            $strTxt .= $value->GetSaveData();
        }
        return $strTxt;
    }

    //=============================================
    // エラーチェック
    // $arrRequest：$_POST or $_GET or $_REQUEST
    // 返り値     ：エラーメッセージの配列
    //=============================================
    public function validate( $arrRequest ){
        $arrError = array();
        $count    = 0;

        foreach( $this->mItems as $value ){
            $strTxt = '';
            $strTxt = $value->validate( $arrRequest );

            if( strlen( $strTxt ) > 0 ){
                $arrError[$count++] = $strTxt;
            }
        }
        return $arrError;
    }

    //=============================================
    // エラーチェック
    // $arrRequest：$_POST or $_GET or $_REQUEST
    // 返り値     ：エラーメッセージの配列
    //=============================================
    public function systemValidate(){
        $arrError = array();
        $count    = 0;

        foreach( $this->mItems as $value ){
            $strTxt = '';
            $strTxt = $value->systemValidate();

            if( strlen( $strTxt ) > 0 ){
                $arrError[$count++] = $strTxt;
            }
        }
        return $arrError;
    }

    //=============================================
    // ユーザー向け入力画面のHTML取得
    // 返り値     ：HTMLデータ
    //=============================================
    public function getUserInputHtml(){
        $strTxt = '';    //全体のテキスト

        foreach( $this->mItems as $value ){
            $strTxt .= $value->getUserInputHtml();
        }
        return $strTxt;
    }

    //=============================================
    // ユーザー向け確認画面のHTML取得
    // 返り値     ：HTMLデータ
    //=============================================
    public function getUserConfirmHtml(){
        $strTxt = '';    //全体のテキスト

        foreach( $this->mItems as $value ){
            $strTxt .= $value->getUserConfirmHtml();
        }
        return $strTxt;
    }

    //=============================================
    // 管理者向け入力画面のHTML取得
    // 返り値     ：HTMLデータ
    //=============================================
    public function getSystemInputHtml(){
        $strTxt = '';    //全体のテキスト
        $strStack = '';
        $max    = 58;
        $backID = 0;
        $nextID = 0;
        $count  = 1;

        foreach( $this->mItems as $value ){
            $strTxt .= "<div id=\"display_item";
            if( $count < 10 )    $strTxt .= "0" . $count;
            else                 $strTxt .= $count;
            $strTxt .= "\">";

            $strStack = $value->getSystemInputHtml();
            $backID = ( $count - 1 );
            $nextID = ( $count + 1 );
            if( $backID < 1 ){      $backID = 1;    }
            if( $nextID > $max ){   $nextID = $max; }
            $strStack = CCompatible::str_replace_allowed_arg( '{$nowID}', $count, $strStack );
            $strStack = CCompatible::str_replace_allowed_arg( '{$backID}', $backID, $strStack );
            $strStack = CCompatible::str_replace_allowed_arg( '{$nextID}', $nextID, $strStack );

            $strTxt .= $strStack;
            $strTxt .= "</div>";
            $count++;
        }
        for( $i=$count;$i<=$max;$i++ ){
            $strTxt .= "<div id=\"display_item";
            if( $i < 10 )    $strTxt .= "0" . $i;
            else             $strTxt .= $i;
            $strTxt .= "\"></div>";
        }

        return $strTxt;
    }

    //=============================================
    // 管理者向け確認画面のHTML取得
    // 返り値     ：HTMLデータ
    //=============================================
    public function getSystemConfirmHtml(){
        $strTxt = '';    //全体のテキスト
        $strStack = '';
        $count  = 1;

        foreach( $this->mItems as $value ){
            $strStack = $value->getSystemConfirmHtml();
            $strStack = CCompatible::str_replace_allowed_arg( '{$nowID}', $count, $strStack );
            $strTxt .= $strStack;
            $count++;
        }
        return $strTxt;
    }

    //=============================================
    // 管理者向け表示アイテムのHTML取得
    // 返り値     ：HTMLデータ
    //=============================================
    public function getSystemHiddenInputHtml(){
        $strTxt = '';    //全体のテキスト

        foreach( $this->mItems as $value ){
            $strTxt .= $value->getSystemHiddenInputHtml();
        }
        return $strTxt;
    }

}

?>

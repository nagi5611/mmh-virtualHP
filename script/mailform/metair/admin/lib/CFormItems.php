<?php
//=============================================================
//
//　フォームのアイテムクラス郡
//
//=============================================================
//エラー出力
//ini_set("display_errors", "On");


require_once dirname(__FILE__) . '/CCompatible.php';


//タイプの宣言
define( 'eITEM_TYPE_NONE',     0 ); //
define( 'eITEM_TYPE_TEXT',     1 ); //テキスト
define( 'eITEM_TYPE_TEXTAREA', 2 ); //テキストエリア
define( 'eITEM_TYPE_SELECT',   3 ); //セレクトボックス
define( 'eITEM_TYPE_RADIO',    4 ); //ラジオボタン
define( 'eITEM_TYPE_CHECKBOX', 5 ); //チェックボックス


define( 'eCHKTYPE_NONE', 'none' );      //規制なし
define( 'eCHKTYPE_NUM',  'numeric' );   //数字入力
define( 'eCHKTYPE_MAIL', 'mail' );      //メールアドレス入力
define( 'eCHKTYPE_URL',  'url' );       //URL入力
define( 'eCHKTYPE_TEL',  'tel' );       //電話番号入力
define( 'eCHKTYPE_FAX',  'fax' );       //ファックス番号入力


//==================================
//タイプの宣言
//==================================
//--------------------
// 共通
//--------------------
define( //必須文字
    'eREQUIRE_TEXT',
    '<span class="red">*</span>'
);


//--------------------
// テキスト
//--------------------
define( //基本的な入力画面
    'eITEM_INPUT_BASEHTML_TEXT',
    '<tr><th>{$view_name}{$require}</th><td><input type="text" name="{$name}" value="{$value}" /></td></tr>' .
    "\r\n"
);
define( //基本的な確認画面 
    'eITEM_CONF_BASEHTML_TEXT',
    '<tr><th>{$view_name}</th><td>{$val_item}<input type="hidden" name="{$name}" value="{$value}" /></td></tr>' .
    "\r\n"
);
define( //管理者向け表示項目の入力画面
    'eITEM_INPUT_SYSVIEWHTML_TEXT',
    '<div class="display_item">' . "\r\n" .
    '<h5>{$item_name}' . 
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" /><span class="assist">' .
    '(テキスト入力)<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '</span></h5>' . "\r\n" .
    '<div class="inner">' . "\r\n" .
    '<div class="item_basic">' . "\r\n" .
    '表示名：<input type="text" name="{$name}" value="{$view_name}" class="mr10" style="ime-mode: active" />' . "\r\n" .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' . "\r\n" .
    '<input type="checkbox" name="need[{$name}]" value="1" {$require} />入力必須' . "\r\n" .
    '</div><!-- /item_basic -->' . "\r\n" .
    '{$str_option}' .
    '</div><!-- /inner -->' . "\r\n" .
    '<div class="updown_button">' . "\r\n" .
    '並べ替え：' . "\r\n" .
    '<input type="button" value="▲" onclick="changeItem( {$nowID}, {$backID} );" />' . "\r\n" .
    '<input type="button" value="▼" onclick="changeItem( {$nowID}, {$nextID} );" />' . "\r\n" .
    '</div><!-- /updown_button -->' . "\r\n" .
    '<div class="no_display_button">' . "\r\n" .
    '<input type="button" value="除外" onclick="deleteItem( {$nowID}, \'{$name}\' );" />' . "\r\n" .
    '</div><!-- /no_display_button -->' . "\r\n" .
    '</div>' . "\r\n" .
    "\r\n"
);
define( //管理者向け表示項目の確認画面
    'eITEM_CONF_SYSVIEWHTML_TEXT',
    '<tr><th class="sub_th">{$item_name}' .
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" />' .
    '<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '<input type="hidden" name="{$name}" value="{$view_name}" />' .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' .
    '<input type="hidden" name="need[{$name}]" value="{$need}" />' .
    '<input type="hidden" name="option[{$name}]" value="{$option_val}" />' .
    '</th><td>{$view_name}</td><td>{$require}</td><td>テキスト入力</td><td>{$str_option}</td></tr>' .
    "\r\n"
);
define( //管理者向け非表示項目の入力画面
    'eITEM_INPUT_SYSHIDDENHTML_TEXT',
    '<tr>' . 
        '<th class="sub_th">{$item_name}</th>' . 
        '<td><input type="button" name="btn_{$name}" value="追加" onclick="addItem(\'{$name}\',\'{$item_name}\',\'{$view_name}\',\'{$type}\',\'{$check_type}\',\'{$option_val}\');" /></td>' . 
    '</tr>' . 
    "\r\n"
);

//--------------------
// テキストエリア
//--------------------
define( //基本的な入力画面
    'eITEM_INPUT_BASEHTML_TEXTAREA',
    '<tr><th>{$view_name}{$require}</th><td><textarea name="{$name}" rows="{$option_val}">{$value}</textarea></td></tr>' .
    "\r\n"
);
define( //基本的な確認画面
    'eITEM_CONF_BASEHTML_TEXTAREA',
    '<tr><th>{$view_name}</th><td>{$val_item}<input type="hidden" name="{$name}" value="{$value}" /></td></tr>' .
    "\r\n"
);
define( //管理者向け表示項目の入力画面
    'eITEM_INPUT_SYSVIEWHTML_TEXTAREA',
    '<div class="display_item">' . "\r\n" .
    '<h5>{$item_name}' . 
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" /><span class="assist">' .
    '(テキストボックス)<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '</span></h5>' . "\r\n" .
    '<div class="inner">' . "\r\n" .
    '<div class="item_basic">' . "\r\n" .
    '表示名：<input type="text" name="{$name}" value="{$view_name}" class="mr10" style="ime-mode: active" />' . "\r\n" .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' . "\r\n" .
    '<input type="checkbox" name="need[{$name}]" value="1" {$require} />入力必須' . "\r\n" .
    '</div><!-- /item_basic -->' . "\r\n" .
    '<div class="item_option">' . "\r\n" .
    '(オプション)　入力ボックスの高さ設定：<input type="text" name="option[{$name}]" value="{$option_val}" size="3" style="ime-mode: inactive" maxlength="2" />行' . "\r\n" .
    '</div>' . "\r\n" .
    '<!-- item_option -->' . "\r\n" .
    '</div><!-- /inner -->' . "\r\n" .
    '<div class="updown_button">' . "\r\n" .
    '並べ替え：' . "\r\n" .
    '<input type="button" value="▲" onclick="changeItem( {$nowID}, {$backID} );" />' . "\r\n" .
    '<input type="button" value="▼" onclick="changeItem( {$nowID}, {$nextID} );" />' . "\r\n" .
    '</div><!-- /updown_button -->' . "\r\n" .
    '<div class="no_display_button">' . "\r\n" .
    '<input type="button" value="除外" onclick="deleteItem( {$nowID}, \'{$name}\' );" />' . "\r\n" .
    '</div><!-- /no_display_button -->' . "\r\n" .
    '</div>' . "\r\n" .
    "\r\n"
);
define( //管理者向け表示項目の確認画面
    'eITEM_CONF_SYSVIEWHTML_TEXTAREA',
    '<tr><th class="sub_th">{$item_name}' .
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" />' .
    '<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '<input type="hidden" name="{$name}" value="{$view_name}" />' .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' .
    '<input type="hidden" name="need[{$name}]" value="{$need}" />' .
    '<input type="hidden" name="option[{$name}]" value="{$option_val}" />' .
    '</th><td>{$view_name}</td><td>{$require}</td><td>テキストボックス</td><td>ボックスの高さ：{$option_val}行</td></tr>' .
    "\r\n"
);
define( //管理者向け非表示項目の入力画面
    'eITEM_INPUT_SYSHIDDENHTML_TEXTAREA',
    '<tr>' . 
        '<th class="sub_th">{$item_name}</th>' . 
        '<td><input type="button" name="btn_{$name}" value="追加" onclick="addItem(\'{$name}\',\'{$item_name}\',\'{$view_name}\',\'{$type}\',\'{$check_type}\',\'{$option_val}\');" /></td>' . 
    '</tr>' . 
    "\r\n"
);

//--------------------
// セレクト
//--------------------
define( //基本的な入力画面
     'eITEM_INPUT_BASEHTML_SELECT',
     '<tr><th>{$view_name}{$require}</th><td>{$input_list}</td></tr>' .
     "\r\n"
);
define( //基本的な確認画面 
    'eITEM_CONF_BASEHTML_SELECT',
    '<tr><th>{$view_name}</th><td>{$val_item}<input type="hidden" name="{$name}" value="{$value}" /></td></tr>' .
    "\r\n"
);
define( //管理者向け表示項目の入力画面
    'eITEM_INPUT_SYSVIEWHTML_SELECT',
    '<div class="display_item">' . "\r\n" .
    '<h5>{$item_name}' . 
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" /><span class="assist">' .
    '(選択式)<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '</span></h5>' . "\r\n" .
    '<div class="inner">' . "\r\n" .
    '<div class="item_basic">' . "\r\n" .
    '表示名：<input type="text" name="{$name}" value="{$view_name}" class="mr10" style="ime-mode: active" />' . "\r\n" .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' . "\r\n" .
    '<input type="checkbox" name="need[{$name}]" value="1" {$require} />入力必須' . "\r\n" .
    '</div><!-- /item_basic -->' . "\r\n" .
    '<div class="item_option">' . "\r\n" .
    '選択項目(改行、または「,」カンマ区切りで記入してください)<br />' .
    '<textarea name=option[{$name}] rows=3 cols=50>{$option_val}</textarea>' .
    '</div>' . "\r\n" .
    '<!-- item_option -->' . "\r\n" .
    '</div><!-- /inner -->' . "\r\n" .
    '<div class="updown_button">' . "\r\n" .
    '並べ替え：' . "\r\n" .
    '<input type="button" value="▲" onclick="changeItem( {$nowID}, {$backID} );" />' . "\r\n" .
    '<input type="button" value="▼" onclick="changeItem( {$nowID}, {$nextID} );" />' . "\r\n" .
    '</div><!-- /updown_button -->' . "\r\n" .
    '<div class="no_display_button">' . "\r\n" .
    '<input type="button" value="除外" onclick="deleteItem( {$nowID}, \'{$name}\' );"  />' . "\r\n" .
    '</div><!-- /no_display_button -->' . "\r\n" .
    '</div>' . "\r\n" .
    "\r\n"
);

define( //管理者向け表示項目の確認画面 
    'eITEM_CONF_SYSVIEWHTML_SELECT',
    '<tr><th class="sub_th">{$item_name}' .
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" />' .
    '<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '<input type="hidden" name="{$name}" value="{$view_name}" />' .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' .
    '<input type="hidden" name="need[{$name}]" value="{$need}" />' .
    '<input type="hidden" name="option[{$name}]" value="{$option_val}" />' .
    '</th><td>{$view_name}</td><td>{$require}</td><td>選択式</td><td>{$str_option}</td></tr>' .
    "\r\n"
);
define( //管理者向け非表示項目の入力画面 
    'eITEM_INPUT_SYSHIDDENHTML_SELECT',
    '<tr>' . 
        '<th class="sub_th">{$item_name}</th>' . 
        '<td><input type="button" name="btn_{$name}" value="追加" onclick="addItem(\'{$name}\',\'{$item_name}\',\'{$view_name}\',\'{$type}\',\'{$check_type}\',\'{$option_val}\');" /></td>' . 
    '</tr>' . 
    "\r\n"
);

//--------------------
// ラジオ
//--------------------
define( //基本的な入力画面
    'eITEM_INPUT_BASEHTML_RADIO',
    '<tr><th>{$view_name}{$require}</th><td>{$input_list}</td></tr>' .
    "\r\n"
);
define( //基本的な確認画面 
    'eITEM_CONF_BASEHTML_RADIO',
    '<tr><th>{$view_name}</th><td>{$val_item}<input type="hidden" name="{$name}" value="{$value}" /></td></tr>' .
    "\r\n"
);
define( //管理者向け表示項目の入力画面
     'eITEM_INPUT_SYSVIEWHTML_RADIO',
    '<div class="display_item">' . "\r\n" .
    '<h5>{$item_name}' . 
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" /><span class="assist">' .
    '(選択式)<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '</span></h5>' . "\r\n" .
    '<div class="inner">' . "\r\n" .
    '<div class="item_basic">' . "\r\n" .
    '表示名：<input type="text" name="{$name}" value="{$view_name}" class="mr10" style="ime-mode: active" />' . "\r\n" .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' . "\r\n" .
    '<input type="checkbox" name="need[{$name}]" value="1" {$require} />入力必須' . "\r\n" .
    '</div><!-- /item_basic -->' . "\r\n" .
    '<div class="item_option">' . "\r\n" .
    '選択項目(改行、または「,」カンマ区切りで記入してください)<br />' .
    '<textarea name=option[{$name}] rows=3 cols=50>{$option_val}</textarea>' .
    '</div>' . "\r\n" .
    '<!-- item_option -->' . "\r\n" .
    '</div><!-- /inner -->' . "\r\n" .
    '<div class="updown_button">' . "\r\n" .
    '並べ替え：' . "\r\n" .
    '<input type="button" value="▲" onclick="changeItem( {$nowID}, {$backID} );" />' . "\r\n" .
    '<input type="button" value="▼" onclick="changeItem( {$nowID}, {$nextID} );" />' . "\r\n" .
    '</div><!-- /updown_button -->' . "\r\n" .
    '<div class="no_display_button">' . "\r\n" .
    '<input type="button" value="除外" onclick="deleteItem( {$nowID}, \'{$name}\' );"  />' . "\r\n" .
    '</div><!-- /no_display_button -->' . "\r\n" .
    '</div>' . "\r\n" .
     "\r\n"
);
define( //管理者向け表示項目の確認画面 
    'eITEM_CONF_SYSVIEWHTML_RADIO',
    '<tr><th class="sub_th">{$item_name}' .
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" />' .
    '<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '<input type="hidden" name="{$name}" value="{$view_name}" />' .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' .
    '<input type="hidden" name="need[{$name}]" value="{$need}" />' .
    '<input type="hidden" name="option[{$name}]" value="{$option_val}" />' .
    '</th><td>{$view_name}</td><td>{$require}</td><td>選択式</td><td>{$str_option}</td></tr>' .
    "\r\n"
);


define( //管理者向け非表示項目の入力画面 
     'eITEM_INPUT_SYSHIDDENHTML_RADIO',
     '<tr>' . 
        '<th class="sub_th">{$item_name}</th>' . 
        '<td><input type="button" name="btn_{$name}" value="追加" onclick="addItem(\'{$name}\',\'{$item_name}\',\'{$view_name}\',\'{$type}\',\'{$check_type}\',\'{$option_val}\');" /></td>' . 
     '</tr>' . 
     "\r\n"
);


//--------------------
// チェックボックス
//--------------------
define( //基本的な入力画面
    'eITEM_INPUT_BASEHTML_CHECKBOX',
    '<tr><th>{$view_name}{$require}</th><td>{$input_list}</td></tr>' .
    "\r\n"
);
define( //基本的な確認画面 
    'eITEM_CONF_BASEHTML_CHECKBOX',
    '<tr><th>{$view_name}</th><td>{$val_item}<input type="hidden" name="{$name}" value="{$value}" /></td></tr>' .
    "\r\n"
);
define( //管理者向け表示項目の入力画面
     'eITEM_INPUT_SYSVIEWHTML_CHECKBOX',
    '<div class="display_item">' . "\r\n" .
    '<h5>{$item_name}' . 
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" /><span class="assist">' .
    '(選択式)<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '</span></h5>' . "\r\n" .
    '<div class="inner">' . "\r\n" .
    '<div class="item_basic">' . "\r\n" .
    '表示名：<input type="text" name="{$name}" value="{$view_name}" class="mr10" style="ime-mode: active" />' . "\r\n" .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' . "\r\n" .
    '<input type="checkbox" name="need[{$name}]" value="1" {$require} />入力必須' . "\r\n" .
    '</div><!-- /item_basic -->' . "\r\n" .
    '<div class="item_option">' . "\r\n" .
    '選択項目(改行、または「,」カンマ区切りで記入してください)<br />' .
    '<textarea name=option[{$name}] rows=3 cols=50>{$option_val}</textarea>' .
    '</div>' . "\r\n" .
    '<!-- item_option -->' . "\r\n" .
    '</div><!-- /inner -->' . "\r\n" .
    '<div class="updown_button">' . "\r\n" .
    '並べ替え：' . "\r\n" .
    '<input type="button" value="▲" onclick="changeItem( {$nowID}, {$backID} );" />' . "\r\n" .
    '<input type="button" value="▼" onclick="changeItem( {$nowID}, {$nextID} );" />' . "\r\n" .
    '</div><!-- /updown_button -->' . "\r\n" .
    '<div class="no_display_button">' . "\r\n" .
    '<input type="button" value="除外" onclick="deleteItem( {$nowID}, \'{$name}\' );"  />' . "\r\n" .
    '</div><!-- /no_display_button -->' . "\r\n" .
    '</div>' . "\r\n" .
     "\r\n"
);
define( //管理者向け表示項目の確認画面 
    'eITEM_CONF_SYSVIEWHTML_CHECKBOX',
    '<tr><th class="sub_th">{$item_name}' .
    '<input type="hidden" name="name[{$name}]" value="{$item_name}" />' .
    '<input type="hidden" name="id[{$nowID}]" value="{$name}" />' .
    '<input type="hidden" name="type[{$name}]" value="{$type}" />' .
    '<input type="hidden" name="{$name}" value="{$view_name}" />' .
    '<input type="hidden" name="check[{$name}]" value="{$check_type}" />' .
    '<input type="hidden" name="need[{$name}]" value="{$need}" />' .
    '<input type="hidden" name="option[{$name}]" value="{$option_val}" />' .
    '</th><td>{$view_name}</td><td>{$require}</td><td>選択式</td><td>{$str_option}</td></tr>' .
    "\r\n"
);


define( //管理者向け非表示項目の入力画面 
     'eITEM_INPUT_SYSHIDDENHTML_CHECKBOX',
     '<tr>' . 
        '<th class="sub_th">{$item_name}</th>' . 
        '<td><input type="button" name="btn_{$name}" value="追加" onclick="addItem(\'{$name}\',\'{$item_name}\',\'{$view_name}\',\'{$type}\',\'{$check_type}\',\'{$option_val}\');" /></td>' . 
     '</tr>' . 
     "\r\n"
);




//===========================================
//
//  フォームアイテム共通
//
//===========================================
class CFormItem {
    var $mName;         //識別名
    var $mNeed;         //必須項目
    var $mType;         //入力タイプ
    var $mInputType;    //入力用のタイプ
    var $mCheckType;    //入力値のチェックタイプ
    var $mItemName;     //項目名
    var $mViewName;     //表示名
    var $mValue;        //値
    var $mOption;       //オプション値
    var $mError;        //エラーメッセージ

    //===============================
    // セーブデータ形式の出力
    //===============================
    public function GetSaveData(){
        $strTxt  = '';
        $strTxt .= $this->mInputType . "\t";
        $strTxt .= $this->mCheckType . "\t";
        $strTxt .= $this->mName . "\t";
        $strTxt .= $this->mItemName . "\t";
        $strTxt .= $this->mViewName . "\t";
        $strTxt .= $this->mNeed . "\t";
        $strTxt .= $this->mOption . "\r\n";
        return $strTxt;
    }
    
    //===============================
    // メール形式の出力
    //===============================
    public function GetMailData(){
        $strTxt  = '';
        $strTxt .= '[' . $this->mViewName . ']' . "\r\n";
        $strTxt .= $this->mValue . "\r\n";
        $strTxt .= "\r\n";
        return $strTxt;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function SetUserValue( $arrRequest ){
        if( isset( $arrRequest[$this->mName] ) ){
            $this->mValue = htmlspecialchars( $arrRequest[$this->mName], ENT_QUOTES, "UTF-8" );
        }
    }

    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt = eITEM_INPUT_BASEHTML_TEXT;
        switch( $this->mType ){
            case eITEM_TYPE_TEXTAREA:   $strTxt = eITEM_INPUT_BASEHTML_TEXTAREA;  break;
            case eITEM_TYPE_SELECT:     $strTxt = eITEM_INPUT_BASEHTML_SELECT;    break;
            case eITEM_TYPE_CHECKBOX:   $strTxt = eITEM_INPUT_BASEHTML_CHECKBOX;  break;
            case eITEM_TYPE_RADIO:      $strTxt = eITEM_INPUT_BASEHTML_RADIO;     break;
            default:                    $strTxt = eITEM_INPUT_BASEHTML_TEXT;      break;
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // ユーザー向け確認画面のHTML取得
    //===============================
    public function getUserConfirmHtml(){
        $strTxt = eITEM_CONF_BASEHTML_TEXT;
        switch( $this->mType ){
            case eITEM_TYPE_TEXTAREA:   $strTxt = eITEM_CONF_BASEHTML_TEXTAREA;  break;
            case eITEM_TYPE_SELECT:     $strTxt = eITEM_CONF_BASEHTML_SELECT;    break;
            case eITEM_TYPE_CHECKBOX:   $strTxt = eITEM_CONF_BASEHTML_CHECKBOX;  break;
            case eITEM_TYPE_RADIO:      $strTxt = eITEM_CONF_BASEHTML_RADIO;     break;
            default:                    $strTxt = eITEM_CONF_BASEHTML_TEXT;      break;
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //内部の値と変更する
        $val_item = $this->mValue;
        $val_item = CCompatible::str_replace_allowed_arg( "\r\n", '<br />', $val_item );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$val_item}', $val_item, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け入力画面のHTML取得
    //===============================
    public function getSystemInputHtml(){
        $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;
        switch( $this->mType ){
            case eITEM_TYPE_TEXTAREA:   $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXTAREA;  break;
            case eITEM_TYPE_SELECT:     $strTxt = eITEM_INPUT_SYSVIEWHTML_SELECT;    break;
            case eITEM_TYPE_CHECKBOX:   $strTxt = eITEM_INPUT_SYSVIEWHTML_CHECKBOX;  break;
            case eITEM_TYPE_RADIO:      $strTxt = eITEM_INPUT_SYSVIEWHTML_RADIO;     break;
            default:                    $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;      break;
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', '', $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け確認画面のHTML取得
    //===============================
    public function getSystemConfirmHtml(){
        $strTxt = eITEM_CONF_SYSVIEWHTML_TEXT;
        switch( $this->mType ){
            case eITEM_TYPE_TEXTAREA:   $strTxt = eITEM_CONF_SYSVIEWHTML_TEXTAREA;  break;
            case eITEM_TYPE_SELECT:     $strTxt = eITEM_CONF_SYSVIEWHTML_SELECT;    break;
            case eITEM_TYPE_CHECKBOX:   $strTxt = eITEM_CONF_SYSVIEWHTML_CHECKBOX;  break;
            case eITEM_TYPE_RADIO:      $strTxt = eITEM_CONF_SYSVIEWHTML_RADIO;     break;
            default:                    $strTxt = eITEM_CONF_SYSVIEWHTML_TEXT;      break;
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', '&nbsp;', $strTxt );

        //必須文字の置換
        $strTxt = CCompatible::str_replace_allowed_arg( '{$need}', $this->mNeed, $strTxt );
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '○', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '&nbsp;', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け非表示画面のHTML取得
    //===============================
    public function getSystemHiddenInputHtml(){
        $strTxt = eITEM_INPUT_SYSHIDDENHTML_TEXT;
        switch( $this->mType ){
            case eITEM_TYPE_TEXTAREA:   $strTxt = eITEM_INPUT_SYSHIDDENHTML_TEXTAREA;    break;
            case eITEM_TYPE_SELECT:     $strTxt = eITEM_INPUT_SYSHIDDENHTML_SELECT;      break;
            case eITEM_TYPE_CHECKBOX:   $strTxt = eITEM_INPUT_SYSHIDDENHTML_CHECKBOX;    break;
            case eITEM_TYPE_RADIO:      $strTxt = eITEM_INPUT_SYSHIDDENHTML_RADIO;       break;
            default:                    $strTxt = eITEM_INPUT_SYSHIDDENHTML_TEXT;        break;
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // エラーチェック
    //===============================
    public function validate(){
        if( $this->mNeed ){
            //入力必須！
            if( ! $this->mValue ){
                return '[ ' . $this->mViewName . ' ]を入力して下さい。';
            }
        }else{
            //空文字の場合は判定なしでOKとする
            if( ! $this->mValue ){
                return '';
            }
        }

        //最大文字数のチェック
        if( $this->mValue ){
            //種類別の文字数判定
            if( $this->mType == eITEM_TYPE_TEXTAREA ){
                if( mb_strlen( $this->mValue, "UTF-8" ) > 2000 ){
                    return '[ ' . $this->mViewName . ' ] は 2000 文字以内にして下さい。';
                }
            }else{
                if( mb_strlen( $this->mValue, "UTF-8" ) > 200 ){
                    return '[ ' . $this->mViewName . ' ] は 200 文字以内にして下さい。';
                }
            }
        }

        //文字のチェック
        if( $this->mCheckType == 'mail' ){
            //メールアドレス
            if ( ! preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\.\+_-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $this->mValue) ){
                return '[ ' . $this->mViewName . ' ] には メールアドレス を入力して下さい。';
            }
        }elseif( $this->mCheckType == 'url' ){
            //URLのみ
            if( ! preg_match('/^(https?|ftp)(:\/\/.+)$/', $this->mValue) ){
                return '[ ' . $this->mViewName . ' ] にはURLを入力して下さい。';
            }
        }elseif( $this->mCheckType == 'numeric' ){
            //数字のみ
            if ( ! preg_match("/^[0-9-]+$/", $this->mValue) ){
                return '[ ' . $this->mViewName . ' ] には数字を入力して下さい。';
            }
        }elseif( $this->mCheckType == 'tel' ){
            //数値(電話番号)のみ
            if ( ! preg_match("/^[0-9-]+$/", $this->mValue) ){
                return '[ ' . $this->mViewName . ' ] にはお電話番号を入力して下さい。';
            }
        }elseif( $this->mCheckType == 'fax' ){
            //数値(FAX番号)のみ
            if ( ! preg_match("/^[0-9-]+$/", $this->mValue) ){
                return '[ ' . $this->mViewName . ' ] にはＦＡＸ番号を入力して下さい。';
            }
        }

        //入力値に問題がないので、空文字を返す
        return '';
    }

    //===============================
    // エラーチェック(管理画面用)
    //===============================
    public function systemValidate(){
        $errStr       = '';
        $err_ViewName = '';
        $err_Option   = '';
        
        if( $this->mViewName ){
            if( mb_strlen( $this->mViewName, "UTF-8" ) > 500 ){
                return '表示名は 500 文字以内にして下さい。';
            }
        }else{
            return '[ ' . $this->mItemName . ' ]の表示名を入力して下さい。';
        }
        if( $this->mOption ){
            if( $this->mType <= eITEM_TYPE_TEXTAREA ){
                if( ! is_numeric($this->mOption) ){
                    return '[ ' . $this->mItemName . ' ]のオプションの値には数字を入力して下さい。';
                }elseif( mb_strlen( $this->mOption, "UTF-8" ) > 2 ){
                    return '[ ' . $this->mItemName . ' ]のオプションの値は２桁以内にして下さい。';
                }else{
                    $this->mOption = intval( $this->mOption );
                    if( $this->mOption < 1 ){
                        return '[ ' . $this->mItemName . ' ]のオプションの値は１以上を設定してください';
                    }
                }
            }else{
                $strCheck = $this->mOption;
                $strCheck = CCompatible::str_replace_allowed_arg( "\r", "", $strCheck );
                $strCheck = CCompatible::str_replace_allowed_arg( "\n", "", $strCheck );
                $strCheck = CCompatible::str_replace_allowed_arg( ",", "", $strCheck );
                $strCheck = trim($strCheck);
                
                if( $strCheck == '' ){
                    return '[ ' . $this->mItemName . ' ]の選択項目を入力して下さい。';
                }
            }
        }else{
            if( $this->mType == eITEM_TYPE_TEXTAREA ){
                return '[ ' . $this->mItemName . ' ]のオプションの値を入力して下さい。';
            }elseif( $this->mType > eITEM_TYPE_TEXTAREA ){
                if( $this->mInputType != 'sex' &&
                    $this->mInputType != 'selarea' ){
                    return '[ ' . $this->mItemName . ' ]の選択項目を入力して下さい。';
                }
            }
        }

        return '';
    }
}



//===========================================
//
// テキスト項目
//
//===========================================
class CTextItem extends CFormItem {

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_TEXT;
        $this->mInputType   = 'text';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }

    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        //親元の関数を実行する
        $strTxt = parent::getUserInputHtml();

        return $strTxt;
    }


    //===============================
    // 管理者向け入力画面のHTML取得
    //===============================
    public function getSystemInputHtml(){
        $strOption = '<input type="hidden" name="option[{$name}]" value="{$option_val}" />';

        $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', $strOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

}

//===========================================
//
// テキストエリア項目
//
//===========================================
class CTextAreaItem extends CFormItem {

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_TEXTAREA;
        $this->mInputType   = 'textarea';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }
}


//===========================================
//
// セレクトボックス項目
//
//===========================================
class CSelectItem extends CFormItem {

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_SELECT;
        $this->mInputType   = 'select';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = htmlspecialchars( $option, ENT_QUOTES, "UTF-8" );
        $this->mError       = '';
    }


    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt   = eITEM_INPUT_BASEHTML_SELECT;
        $strCheck = $this->mOption;
        $i        = 0;
        $j        = 0;

        $strCheck    = CCompatible::str_replace_allowed_arg( "\r\n", "\n", $strCheck );
        $strCheck    = CCompatible::str_replace_allowed_arg( "\r", "\n", $strCheck );

        //要素の配列化
        $arrSplit    = explode( "\n", $strCheck );
        $arrMenu     = array();
        for( $i=0;$i<count($arrSplit);$i++ ){
            $arrMenu[$i] = explode( ',', $arrSplit[$i] );
        }

        //出力データの作成
        $strList    = '';
        $strList   .= '<select name="{$name}">';
        $strList   .= '<option value="" />--------';
        for( $i=0;$i<count($arrMenu);$i++ ){
            for( $j=0;$j<count($arrMenu[$i]);$j++ ){
                $arrMenu[$i][$j] = trim( $arrMenu[$i][$j] );
                if( $arrMenu[$i][$j] == '' ){    continue;    }

                $strList   .= '<option value="';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '" ';
                if( $arrMenu[$i][$j] == $this->mValue ){
                    $strList   .= 'selected';
                }
                $strList   .= ' />';
                $strList   .= $arrMenu[$i][$j];
            }
        }
        $strList   .= '</select>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$input_list}', $strList, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        return $strTxt;
    }
}

//===========================================
//
// チェックボックス項目
//
//===========================================
class CCheckboxItem extends CFormItem {
    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_CHECKBOX;
        $this->mInputType   = 'checkbox';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = htmlspecialchars( $option, ENT_QUOTES, "UTF-8" );
        $this->mError       = '';
    }

    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt = eITEM_INPUT_BASEHTML_RADIO;
        $strCheck = $this->mOption;
        $isBR     = false;
        $count    = 0;
        $i        = 0;
        $j        = 0;

        $strCheck    = CCompatible::str_replace_allowed_arg( "\r\n", "\n", $strCheck );
        $strCheck    = CCompatible::str_replace_allowed_arg( "\r", "\n", $strCheck );

        //要素の配列化
        $arrSplit    = explode( "\n", $strCheck );
        $arrMenu     = array();
        for( $i=0;$i<count($arrSplit);$i++ ){
            $arrMenu[$i] = explode( ',', $arrSplit[$i] );
        }

        //出力データの作成
        $strList    = '';
        for( $i=0;$i<count($arrMenu);$i++ ){
            $isBR     = false;
            for( $j=0;$j<count($arrMenu[$i]);$j++ ){
                $arrMenu[$i][$j] = trim( $arrMenu[$i][$j] );
                if( $arrMenu[$i][$j] == '' ){    continue;    }

                $strList   .= '<input type="checkbox" name="{$name}[' . $count . ']" value="';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '" id="{$name}_';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '" style="width:20px"';
                if( $this->ChkCheckedValue( $arrMenu[$i][$j] ) ){
                    $strList   .= ' checked';
                }
                $strList   .= ' />';
                $strList   .= '<label for="{$name}_';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '">';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '</label>';

                $count++;

                //改行命令の許諾フラグ
                $isBR = true;
            }
            if( $isBR ){
                $strList   .= '<br />';
            }
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$input_list}', $strList, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        return $strTxt;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function SetUserValue( $arrRequest ){
        if( isset( $arrRequest[$this->mName] ) ){

            $this->mValue = '';

            $list = $arrRequest[$this->mName];
            if( is_array($list) ){
                foreach( $list as $value ){
                    if( $this->mValue != '' ){
                        $this->mValue .= ',';
                    }
                    $this->mValue .= $value;
                }
            }else{
                $this->mValue = $list;
            }
        }
    }

    //=============================================
    // 選択項目の確認を行う
    //=============================================
    public function ChkCheckedValue( $strChkValue ){
        $arrList = explode( ',', $this->mValue );
        $i       = 0;

        if( $strChkValue == "" ){
            return false;
        }

        for( $i=0;$i<count($arrList);$i++ ){
            if( $arrList[$i] == $strChkValue ){
                return true;
            }
        }

        return false;
    }
}

//===========================================
//
// ラジオボタン項目
//
//===========================================
class CRadioItem extends CFormItem {

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_RADIO;
        $this->mInputType   = 'radio';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = htmlspecialchars( $option, ENT_QUOTES, "UTF-8" );
        $this->mError       = '';
    }

    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt = eITEM_INPUT_BASEHTML_RADIO;
        $strCheck = $this->mOption;
        $isBR     = false;
        $i        = 0;
        $j        = 0;

        $strCheck    = CCompatible::str_replace_allowed_arg( "\r\n", "\n", $strCheck );
        $strCheck    = CCompatible::str_replace_allowed_arg( "\r", "\n", $strCheck );

        //要素の配列化
        $arrSplit    = explode( "\n", $strCheck );
        $arrMenu     = array();
        for( $i=0;$i<count($arrSplit);$i++ ){
            $arrMenu[$i] = explode( ',', $arrSplit[$i] );
        }

        //出力データの作成
        $strList    = '';
        for( $i=0;$i<count($arrMenu);$i++ ){
            $isBR     = false;
            for( $j=0;$j<count($arrMenu[$i]);$j++ ){
                $arrMenu[$i][$j] = trim( $arrMenu[$i][$j] );
                if( $arrMenu[$i][$j] == '' ){    continue;    }

                $strList   .= '<input type="radio" name="{$name}" value="';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '" id="{$name}_';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '" style="width:20px"';
                if( $arrMenu[$i][$j] == $this->mValue ){
                    $strList   .= ' checked';
                }
                $strList   .= ' />';
                $strList   .= '<label for="{$name}_';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '">';
                $strList   .= $arrMenu[$i][$j];
                $strList   .= '</label>';
                
                //改行命令の許諾フラグ
                $isBR = true;
            }
            if( $isBR ){
                $strList   .= '<br />';
            }
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$input_list}', $strList, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        return $strTxt;
    }
}

//------------------------------------------------------------------------------------------------------------------
//===========================================
// 名前用
//===========================================
class CFullNameItem extends CTextItem {
    var $mValue2;

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_TEXT;
        $this->mInputType   = 'fullname';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }

    //===============================
    // メール形式の出力
    //===============================
    public function GetMailData(){
        $strTxt  = '';
        $strTxt .= '[' . $this->mViewName . ']' . "\r\n";
        $strTxt .= $this->mValue;
        if( $this->mOption ){
            $strTxt .= '　';
            $strTxt .= $this->mValue2 . "\r\n";
        }else{
            $strTxt .= "\r\n";
        }
        $strTxt .= "\r\n";
        return $strTxt;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function SetUserValue( $arrRequest ){
        if( isset( $arrRequest[$this->mName . '_1'] ) ){
            $this->mValue = htmlspecialchars( $arrRequest[$this->mName . '_1'], ENT_QUOTES, "UTF-8" );
        }
        if( isset( $arrRequest[$this->mName . '_2'] ) ){
            $this->mValue2 = htmlspecialchars( $arrRequest[$this->mName . '_2'], ENT_QUOTES, "UTF-8" );
        }
    }

    //===============================
    // エラーチェック
    //===============================
    public function validate(){
        if( $this->mNeed != 0 ){
            //入力必須！
            if( ! $this->mValue ||  ( $this->mOption && ! $this->mValue2 ) ){
                return '[ ' . $this->mViewName . ' ]を入力して下さい。';
            }
        }else{
            //空文字の場合は判定なしでOKとする
            if( ! $this->mValue || !$this->mValue2 ){
                return '';
            }
        }

        //最大文字数のチェック
        if( $this->mValue ){
            if( mb_strlen( $this->mValue, "UTF-8" ) > 200 ){
                return '[ ' . $this->mViewName . ' ] は 200 文字以内にして下さい。';
            }
        }
        if( $this->mValue2 ){
            if( mb_strlen( $this->mValue2, "UTF-8" ) > 200 ){
                return '[ ' . $this->mViewName . ' ] は 200 文字以内にして下さい。';
            }
        }

        //入力値に問題がないので、空文字を返す
        return '';
    }
    //------------------------------------------------------
    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt  = '';
        $strTxt .= '<tr>';
        $strTxt .= '<th>{$view_name}{$require}</th><td>';
        if( $this->mOption ){
            if( $this->mCheckType == 'kana' ){
                $strTxt .= 'せい：<input type="text" name="{$name}_1", value="{$value_1}" style="width:80px" />';
                $strTxt .= '　';
                $strTxt .= 'めい：<input type="text" name="{$name}_2", value="{$value_2}" style="width:80px" />';
            }else{
                $strTxt .= '姓：<input type="text" name="{$name}_1", value="{$value_1}" style="width:80px" />';
                $strTxt .= '　';
                $strTxt .= '名：<input type="text" name="{$name}_2", value="{$value_2}" style="width:80px" />';
            }
        }else{
            $strTxt .= '<input type="text" name="{$name}_1", value="{$value_1}" />';
        }
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

    //===============================
    // ユーザー向け確認画面のHTML取得
    //===============================
    public function getUserConfirmHtml(){
        $strTxt  = '';
        $strTxt .= '<tr>';
        $strTxt .= '<th>{$view_name}</th><td>';
        if( $this->mOption ){
            if( $this->mCheckType == 'kana' ){
                $strTxt .= 'せい：{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" />';
                $strTxt .= '　';
                $strTxt .= 'めい：{$value_2}<input type="hidden" name="{$name}_2", value="{$value_2}" />';
            }else{
                $strTxt .= '姓：{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" />';
                $strTxt .= '　';
                $strTxt .= '名：{$value_2}<input type="hidden" name="{$name}_2", value="{$value_2}" />';
            }
        }else{
            $strTxt .= '{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" />';
        }
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け入力画面のHTML取得
    //===============================
    public function getSystemInputHtml(){
        $strOption    = '';
        $strOption   .= '<div class="item_option">';
        $strOption   .= '(オプション)　姓名を分ける：<input type="checkbox" name="option[{$name}]" value="1" {$option_val} />';
        $strOption   .= '</div>';

        $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', $strOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //オプションのチェック
        if( $this->mOption )    $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', '', $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け確認画面のHTML取得
    //===============================
    public function getSystemConfirmHtml(){
        $strOption    = '';
        if( $this->mOption ){     $strOption    = '姓名を分ける';
        }else{                    $strOption    = '姓名を分けない';
        }

        $strTxt = eITEM_CONF_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', $strOption, $strTxt );

        //必須文字の置換
        $strTxt = CCompatible::str_replace_allowed_arg( '{$need}', $this->mNeed, $strTxt );
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '○', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '&nbsp;', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }
}

//===========================================
// 住所用
//===========================================
class CAddressItem extends CTextItem {
    var $mValue2;

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_TEXT;
        $this->mInputType   = 'address';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }

    //===============================
    // メール形式の出力
    //===============================
    public function GetMailData(){
        $strTxt  = '';
        $strTxt .= '[' . $this->mViewName . ']' . "\r\n";
        $strTxt .= $this->mValue . "\r\n";
        $strTxt .= "\r\n";
        if( $this->mOption ){
            $strTxt .= $this->mValue2  . "\r\n";
            $strTxt .= "\r\n";
        }
        return $strTxt;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function SetUserValue( $arrRequest ){
        if( isset( $arrRequest[$this->mName . '_1'] ) ){
            $this->mValue = htmlspecialchars( $arrRequest[$this->mName . '_1'], ENT_QUOTES, "UTF-8" );
        }
        if( isset( $arrRequest[$this->mName . '_2'] ) ){
            $this->mValue2 = htmlspecialchars( $arrRequest[$this->mName . '_2'], ENT_QUOTES, "UTF-8" );
        }
    }

    //===============================
    // エラーチェック
    //===============================
    public function validate(){
        if( $this->mNeed ){
            //入力必須！
            if( ! $this->mValue ||  ( $this->mOption && ! $this->mValue2 ) ){
                return '[ ' . $this->mViewName . ' ]を入力して下さい。';
            }
        }

        //最大文字数のチェック
        if( $this->mValue ){
            if( mb_strlen( $this->mValue, "UTF-8" ) > 200 ){
                return '[ ' . $this->mViewName . ' ] は 200 文字以内にして下さい。';
            }
        }
        if( $this->mValue2 ){
            if( mb_strlen( $this->mValue2, "UTF-8" ) > 200 ){
                return '[ ' . $this->mViewName . ' ] は 200 文字以内にして下さい。';
            }
        }

        //入力値に問題がないので、空文字を返す
        return '';
    }
    //------------------------------------------------------
    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt  = '';
        $strTxt .= '<tr>';
        $strTxt .= '<th>{$view_name}{$require}</th><td>';
        if( $this->mOption ){
            $strTxt .= '<input type="text" name="{$name}_1", value="{$value_1}" /><br />';
            $strTxt .= '<input type="text" name="{$name}_2", value="{$value_2}" />';
        }else{
            $strTxt .= '<input type="text" name="{$name}_1", value="{$value_1}" />';
        }
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

    //===============================
    // ユーザー向け確認画面のHTML取得
    //===============================
    public function getUserConfirmHtml(){
        $strTxt  = '';
        $strTxt .= '<tr>';
        $strTxt .= '<th>{$view_name}</th><td>';
        if( $this->mOption ){
            $strTxt .= '{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" /><br />';
            $strTxt .= '<br />';
            $strTxt .= '{$value_2}<input type="hidden" name="{$name}_2", value="{$value_2}" />';
        }else{
            $strTxt .= '{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" />';
        }
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け入力画面のHTML取得
    //===============================
    public function getSystemInputHtml(){
        $strOption    = '';
        $strOption   .= '<div class="item_option">';
        $strOption   .= '(オプション)　表示する行数を２行にする：<input type="checkbox" name="option[{$name}]" value="1" {$option_val} />';
        $strOption   .= '</div>';

        $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', $strOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //オプションのチェック
        if( $this->mOption )    $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', '', $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け確認画面のHTML取得
    //===============================
    public function getSystemConfirmHtml(){
        $strOption    = '';
        if( $this->mOption ){     $strOption    = '表示する行数：2行';
        }else{                    $strOption    = '表示する行数：1行';
        }

        $strTxt = eITEM_CONF_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', $strOption, $strTxt );

        //必須文字の置換
        $strTxt = CCompatible::str_replace_allowed_arg( '{$need}', $this->mNeed, $strTxt );
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '○', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '&nbsp;', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }
}



//===========================================
// メールアドレス
//===========================================
class CMailItem extends CTextItem {
    var $mValue2;

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_TEXT;
        $this->mInputType   = 'mail';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }

    //===============================
    // メール形式の出力
    //===============================
    public function GetMailData(){
        $strTxt  = '';
        $strTxt .= '[' . $this->mViewName . ']' . "\r\n";
        $strTxt .= $this->mValue . "\r\n";
        $strTxt .= "\r\n";
        return $strTxt;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function SetUserValue( $arrRequest ){
        if( isset( $arrRequest[$this->mName . '_1'] ) ){
            $this->mValue = htmlspecialchars( $arrRequest[$this->mName . '_1'], ENT_QUOTES, "UTF-8" );
        }
        if( isset( $arrRequest[$this->mName . '_2'] ) ){
            $this->mValue2 = htmlspecialchars( $arrRequest[$this->mName . '_2'], ENT_QUOTES, "UTF-8" );
        }
    }

    //===============================
    // エラーチェック
    //===============================
    public function validate(){
        $strTxt = parent::validate();
        if( $strTxt != '' ){        return $strTxt;     }
        if( $this->mNeed ){
            //入力必須！
            if( ! $this->mValue ||  ( $this->mOption && ! $this->mValue2 ) ){
                return '[ ' . $this->mViewName . ' ]を入力して下さい。';
            }
        }else{
            //空文字の場合は判定なしでOKとする
            if( ! $this->mValue || !$this->mValue2 ){
                return '';
            }
        }

        //メールアドレスの整合チェック
        if( $this->mOption ){
            if( $this->mValue && $this->mValue2 ){
                if( $this->mValue != $this->mValue2 ){
                    return '入力したメールアドレスに相違があります。';
                }
            }
        }

        //入力値に問題がないので、空文字を返す
        return '';
    }
    //------------------------------------------------------
    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt  = '';
            $strTxt .= '<tr>';
            $strTxt .= '<th>{$view_name}{$require}</th>';
            $strTxt .= '<td>';
            $strTxt .= '<input type="text" name="{$name}_1", value="{$value_1}" />';
            $strTxt .= '</td>';
            $strTxt .= '</tr>';
            //-------------------------------
        if( $this->mOption ){
            $strTxt .= '<tr>';
            $strTxt .= '<th>{$view_name}(確認){$require}</th><td>';
            $strTxt .= '<input type="text" name="{$name}_2", value="{$value_2}" />';
            $strTxt .= '</td>';
            $strTxt .= '</tr>';
        }

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

    //===============================
    // ユーザー向け確認画面のHTML取得
    //===============================
    public function getUserConfirmHtml(){
        $strTxt  = '';
        $strTxt .= '<tr>';
        $strTxt .= '<th>{$view_name}</th><td>';
        if( $this->mOption ){
            $strTxt .= '{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" />';
            $strTxt .= '<input type="hidden" name="{$name}_2", value="{$value_2}" />';
        }else{
            $strTxt .= '{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" />';
        }
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け入力画面のHTML取得
    //===============================
    public function getSystemInputHtml(){
        $strOption    = '';
        $strOption   .= '<div class="item_option">';
        $strOption   .= '(オプション)　確認用の入力欄を表示する：<input type="checkbox" name="option[{$name}]" value="1" {$option_val} />';
        $strOption   .= '</div>';

        $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', $strOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //オプションのチェック
        if( $this->mOption )    $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', '', $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // 管理者向け確認画面のHTML取得
    //===============================
    public function getSystemConfirmHtml(){
        $strOption    = '';
        if( $this->mOption ){     $strOption    = '確認用の入力欄を表示する';
        }else{                    $strOption    = '確認用の入力欄を表示しない';
        }

        $strTxt = eITEM_CONF_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', $strOption, $strTxt );

        //必須文字の置換
        $strTxt = CCompatible::str_replace_allowed_arg( '{$need}', $this->mNeed, $strTxt );
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '○', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '&nbsp;', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }
}


//===========================================
// 郵便番号
//===========================================
class CCodeItem extends CTextItem {
    var $mValue2;

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_TEXT;
        $this->mInputType   = 'code';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }

    //===============================
    // メール形式の出力
    //===============================
    public function GetMailData(){
        $strTxt  = '';
        $strTxt .= '[' . $this->mViewName . ']' . "\r\n";
        $strTxt .= $this->mValue;
        if( $this->mValue && $this->mValue2 ){  $strTxt .= '－';    }
        $strTxt .= $this->mValue2  . "\r\n";
        $strTxt .= "\r\n";
        return $strTxt;
    }

    //=============================================
    // ユーザーの入力値を設定する
    //=============================================
    public function SetUserValue( $arrRequest ){
        if( isset( $arrRequest[$this->mName . '_1'] ) ){
            $this->mValue = htmlspecialchars( $arrRequest[$this->mName . '_1'], ENT_QUOTES, "UTF-8" );
        }
        if( isset( $arrRequest[$this->mName . '_2'] ) ){
            $this->mValue2 = htmlspecialchars( $arrRequest[$this->mName . '_2'], ENT_QUOTES, "UTF-8" );
        }
    }

    //===============================
    // エラーチェック
    //===============================
    public function validate(){
        if( $this->mNeed != 0 ){
            //入力必須！
            if( ! $this->mValue ||  ! $this->mValue2  ){
                return '[ ' . $this->mViewName . ' ]を入力して下さい。';
            }
        }else{
            //空文字の場合は判定なしでOKとする
            if( ! $this->mValue || !$this->mValue2 ){
                return '';
            }
        }

        //数字のみ
        if ( ! preg_match("/^[0-9]+$/", $this->mValue) || ! preg_match("/^[0-9]+$/", $this->mValue2) ){
            return '[ ' . $this->mViewName . ' ] には数字を入力して下さい。';
        }else if( strlen( $this->mValue ) != 3 || strlen( $this->mValue2 ) != 4 ){
            return '[ ' . $this->mViewName . ' ] の桁数が間違っています。';
        }

        //入力値に問題がないので、空文字を返す
        return '';
    }
    //------------------------------------------------------
    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt  = '';
        $strTxt .= '<tr>';
        $strTxt .= '<th>{$view_name}{$require}</th><td>';
        $strTxt .= '<input type="text" name="{$name}_1", value="{$value_1}" style="width:48px" />';
        $strTxt .= '－';
        $strTxt .= '<input type="text" name="{$name}_2", value="{$value_2}" style="width:80px" />';
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

    //===============================
    // ユーザー向け確認画面のHTML取得
    //===============================
    public function getUserConfirmHtml(){
        $strTxt  = '';
        $strTxt .= '<tr>';
        $strTxt .= '<th>{$view_name}</th><td>';
        $strTxt .= '{$value_1}<input type="hidden" name="{$name}_1", value="{$value_1}" />';
        if( $this->mValue && $this->mValue2 ){  $strTxt .= '－';    }
        $strTxt .= '{$value_2}<input type="hidden" name="{$name}_2", value="{$value_2}" />';
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_1}', $this->mValue, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value_2}', $this->mValue2, $strTxt );

        return $strTxt;
    }

}

//===========================================
// 都道府県
//===========================================
class CSelAreaItem extends CSelectItem {

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_SELECT;
        $this->mInputType   = 'selarea';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }

    //------------------------------------------------------
    //===============================
    // 管理者向け入力画面のHTML取得
    //===============================
    public function getSystemInputHtml(){
        $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', '', $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $arrItem    = array( '北海道',
                            '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
                            '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
                            '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県',
                            '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
                            '鳥取県', '島根県', '岡山県', '広島県', '山口県',
                            '徳島県', '香川県', '愛媛県', '高知県',
                            '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県' );

        $strTxt  = '';
        $strTxt .= '<tr><th>{$view_name}{$require}</th><td>';
        $strTxt .= '<select name="{$name}">';
        $strTxt .= '<option value="">--都道府県--';
        foreach( $arrItem as $item ){
            $strTxt .= "<option value=\"$item\"";
            if( $item == $this->mValue ){    $strTxt .= " selected ";    }
            $strTxt .= ">$item";
        }
        $strTxt .= '</select>';
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        
        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

}


//===========================================
// 性別
//===========================================
class CSexItem extends CRadioItem {

    //=====================================
    // コンストラクタ
    //=====================================
    function __construct( $name, $item_name, $view_name, $item_need, $chk_type, $option ){
        $this->mName        = htmlspecialchars( $name, ENT_QUOTES, "UTF-8" );
        $this->mNeed        = $item_need;
        $this->mType        = eITEM_TYPE_RADIO;
        $this->mInputType   = 'sex';
        $this->mItemName    = htmlspecialchars( $item_name, ENT_QUOTES, "UTF-8" );
        $this->mViewName    = htmlspecialchars( $view_name, ENT_QUOTES, "UTF-8" );
        $this->mCheckType   = $chk_type;
        $this->mOption      = $option;
        $this->mError       = '';
    }

    //------------------------------------------------------
    //===============================
    // 管理者向け入力画面のHTML取得
    //===============================
    public function getSystemInputHtml(){
        $strTxt = eITEM_INPUT_SYSVIEWHTML_TEXT;
        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$check_type}', $this->mCheckType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$option_val}', $this->mOption, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$str_option}', '', $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', 'checked', $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

    //===============================
    // ユーザー向け入力画面のHTML取得
    //===============================
    public function getUserInputHtml(){
        $strTxt  = '';
        $strTxt .= '<tr><th>{$view_name}{$require}</th><td>';
        $strTxt .= '<input type="radio" value="男性" id="{$name}_男性" name="{$name}" style="width:20px" ';
        if( $this->mValue == "男性" ){  $strTxt .= ' checked ';    }
        $strTxt .= ' /><label for="{$name}_男性">男性</label>';
        $strTxt .= '　';
        $strTxt .= '<input type="radio" value="女性" id="{$name}_女性" name="{$name}"  style="width:20px" ';
        if( $this->mValue == "女性" ){ $strTxt .= ' checked ';    }
        $strTxt .= ' /><label for="{$name}_女性">女性</label>';
        $strTxt .= '</td></tr>';

        $strTxt = CCompatible::str_replace_allowed_arg( '{$name}', $this->mName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$item_name}', $this->mItemName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$view_name}', $this->mViewName, $strTxt );
        $strTxt = CCompatible::str_replace_allowed_arg( '{$type}', $this->mInputType, $strTxt );

        //必須文字の置換
        if( $this->mNeed )      $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', eREQUIRE_TEXT, $strTxt );
        else                    $strTxt = CCompatible::str_replace_allowed_arg( '{$require}', '', $strTxt );

        //内部の値と変更する
        $strTxt = CCompatible::str_replace_allowed_arg( '{$value}', $this->mValue, $strTxt );

        return $strTxt;
    }

}

?>

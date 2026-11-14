//---------------------------------------------
//
//デザイン画面の javascript まとめ
//
//---------------------------------------------

//色の設定
function setDesignColor( strName, strColor ) {

    document.getElementById( strName ).value = strColor;
    
    return false;
}


//カラーパネルの設定
function setColorPanel( strTargetName ) {

    var newColorCode = document.getElementById( strTargetName ).value;

    //カラーコードの確認
    if( newColorCode.match( /^[0-9A-F]{6}$/i ) ){
        document.getElementById( 'panel_' + strTargetName ).style.background      = '#' + newColorCode;
        document.getElementById( 'code_' + strTargetName ).innerHTML              = newColorCode;
    }
}

//デザインの全初期化
function allDesignReset() {
    var flag;

    flag = confirm('全ての配色を初期設定値に戻します。\r\n本当によろしいですか？');

    if( flag ){
        setDesignColor( 'color_title', 'ff6600' );
        setColorPanel('color_title');
        setDesignColor( 'color_explain', '000000' );
        setColorPanel('color_explain');
        setDesignColor( 'color_background', 'ffffff' );
        setColorPanel('color_background');
        setDesignColor( 'color_line', 'b3b3b3' );
        setColorPanel('color_line');
        setDesignColor( 'color_menubg', '444444' );
        setColorPanel('color_menubg');
        setDesignColor( 'color_menufont', 'ffffff' );
        setColorPanel('color_menufont');
        setDesignColor( 'color_error', 'ff3333' );
        setColorPanel('color_error');
        ClosePreview();
    }
}


//=================================
//プレビュー関数郡
//=================================
var PreviewWin = null;

//========================
//プレビューを開く
//========================
function OpenPreview( thisForm, openPage ) {
    var bufTarget  = thisForm.target;
    var bufAction  = thisForm.action;
    var previewURL = "preview.php";
    if( openPage.length > 0 ){
        previewURL += "?page=" + openPage;
    }

    //プレビューを閉じる
    ClosePreview();

    //子ウインドウを開く
    thisForm.target = "preview";
    thisForm.action = previewURL;
    
    PreviewWin = window.open( "about:blank", thisForm.target, "width=800,height=400,resizable=yes,scrollbars=yes" );
    PreviewWin.focus();
    thisForm.submit();

    //遷移先を元に戻す
    thisForm.target = bufTarget;
    thisForm.action = bufAction;
}

//========================
//プレビューを閉じる
//========================
function ClosePreview() {
    if( PreviewWin ){
        PreviewWin.close();
        PreviewWin = null;
    }
}

//========================
// 定型／自作デザインの切り替え
//========================
function ChangeDesignMode() {
    //現在選択されているモードの確認
    var nMode  = document.main_menu.design_mode.value;
    var strMsg = '';
    var isFlag = true;

    //確認メッセージの表示
    if( nMode == 1 ){
        strMsg += '「自作スキンを利用する」に変更すると、\r\n';
        strMsg += '保存されていない設定は破棄されます。';
    }else{
        strMsg += '「定型スキンを利用する」に変更すると、\r\n';
        strMsg += '自作デザインのテンプレートは全て初期化されます。\r\n';
        strMsg += 'よろしいですか？';
    }
    isFlag = confirm( strMsg );

    if( isFlag ){
        //ページ遷移を行う
        if( nMode == 1 ){    document.location = './design.php?skin=original';
        }else{               document.location = './design.php?skin=plain';
        }
    }else{
        //カーソルを元の位置に戻す
        document.main_menu.design_mode.value = document.main_menu.default_design_mode.value;
    }

}

//========================
// 自作テンプレートの切り替え
//========================
function ChangeTemplate( selTemplate ) {
    var strBaseName    = 'table_template_';
    var strBaseClass   = 'sub_div_template_';
    var strCssName     = 'css';
    var strInputName   = 'input';
    var strConfName    = 'conf';
    var strExitName    = 'exit';

    document.getElementById( strBaseClass + strCssName ).className  = " ";
    document.getElementById( strBaseClass + strInputName ).className = " ";
    document.getElementById( strBaseClass + strConfName ).className  = " ";
    document.getElementById( strBaseClass + strExitName ).className  = " ";
    if( selTemplate == strCssName ){
        document.getElementById( strBaseName + strCssName ).style.display  = 'block';
        document.getElementById( strBaseClass + strCssName ).className     = "current_sub_navi";
        document.main_menu.nowhtml.value = 3;
    }else{
        document.getElementById( strBaseName + strCssName ).style.display  = 'none';
    }
    if( selTemplate == strInputName ){
        document.getElementById( strBaseName + strInputName ).style.display  = 'block';
        document.getElementById( strBaseClass + strInputName ).className     = "current_sub_navi";
        document.main_menu.nowhtml.value = 0;
    }else{
        document.getElementById( strBaseName + strInputName ).style.display  = 'none';
    }
    if( selTemplate == strConfName ){
        document.getElementById( strBaseName + strConfName ).style.display  = 'block';
        document.getElementById( strBaseClass + strConfName ).className  = "current_sub_navi";
        document.main_menu.nowhtml.value = 1;
    }else{
        document.getElementById( strBaseName + strConfName ).style.display  = 'none';
    }
    if( selTemplate == strExitName ){
        document.getElementById( strBaseName + strExitName ).style.display  = 'block';
        document.getElementById( strBaseClass + strExitName ).className  = "current_sub_navi";
        document.main_menu.nowhtml.value = 2;
    }else{
        document.getElementById( strBaseName + strExitName ).style.display  = 'none';
    }
}

//========================
// 太字の切り替え
//========================
function BoldChangeTemplate( strClassName, isBold ) {
    if( isBold ){
        document.getElementById( 'sub_div_template_' + strClassName ).style.fontWeight  = "bold";
        switch( strClassName ){
            case 'input':  document.main_menu.change_template_input.value = 1; break;
            case 'conf':   document.main_menu.change_template_conf.value = 1;  break;
            case 'exit':   document.main_menu.change_template_exit.value = 1;  break;
            case 'css':    document.main_menu.change_template_css.value = 1;   break;
        }
    }else{
        document.getElementById( 'sub_div_template_' + strClassName ).style.fontWeight  = "normal";
        switch( strClassName ){
            case 'input':  document.main_menu.change_template_input.value = 0; break;
            case 'conf':   document.main_menu.change_template_conf.value  = 0; break;
            case 'exit':   document.main_menu.change_template_exit.value  = 0; break;
            case 'css':    document.main_menu.change_template_css.value   = 0; break;
        }
    }
}

//========================
// リセットの確認
//========================
function IsReset( strName ) {
    var flag;
    flag = confirm( strName + 'をリセットします。\r\n本当によろしいですか？');
    return flag;
}

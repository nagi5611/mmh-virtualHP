//---------------------------------------------
//
//システム画面の javascript まとめ
//
//---------------------------------------------

//アイテムの追加
function addItem( name, itemName, viewName, type, check, option, isSub ) {
    var objElement;
    var strTxt = "";
    var cnt    = 0;
    var nowID  = 0;
    var backID = 0;
    var nextID = 0;

    for( cnt=1;cnt<=58;cnt++ ){
        if( cnt < 10 ){   objElement = document.getElementById("display_item0" + cnt );
        }else{            objElement = document.getElementById("display_item" + cnt );
        }
        if( objElement.innerHTML == "" ){
            nowID  = cnt;
            backID = ( cnt - 1 );
            nextID = ( cnt + 1 );
            if( backID < 1 ){    backID = 1;    }
            if( nextID > 58 ){   nextID = 58;   }
            break;
        }
    }

    strTxt += "<div class=\"display_item\">";
    strTxt += "<h5>" + itemName;
    strTxt += "<input type=\"hidden\" name=\"name[" + name + "]\" value=\"" + itemName + "\" />";
    strTxt += "<input type=\"hidden\" name=\"id[" + nowID + "]\" value=\"" + name + "\" /><span class=\"assist\">";

    //入力タイプの表示
    if( type == "text" || type == "fullname" || type == "address" ||
        type == "code" || type == "mail" ){
        strTxt += "(テキスト入力)";
    }else if( type == "textarea" ){  strTxt += "(テキストボックス)";
    }else {                          strTxt += "(選択式)";
    }
    strTxt += "<input type=\"hidden\" name=\"type[" + name + "]\" value=\"" + type + "\" />";
    strTxt += "<input type=\"hidden\" name=\"check[" + name + "]\" value=\"" + check + "\" />";
    strTxt += "</span></h5>";
    
    strTxt += "<div class=\"inner\">";
    strTxt += "<div class=\"item_basic\">";
    strTxt += "表示名：<input type=\"text\" name=\"" + name + "\" value=\"" + viewName + "\" class=\"mr10\" style=\"ime-mode: active\" />";
    strTxt += "<input type=\"checkbox\" name=\"need[" + name + "]\" value=\"1\" />入力必須";
    strTxt += "</div><!-- /item_basic -->";

    if( type == "textarea" ){
        strTxt += "<div class=\"item_option\">";
        strTxt += "(オプション)　入力ボックスの高さ設定：<input type=\"text\" name=\"option[" + name + "]\" value=\"" + option + "\" size=\"3\" style=\"ime-mode: inactive\" maxlength=\"2\" />行";
        strTxt += "</div><!-- item_option -->";
    }else if( type == "fullname" ){
        strTxt += "<div class=\"item_option\">";
        strTxt += "(オプション)　姓名を分ける：<input type=\"checkbox\" name=\"option[" + name + "]\" value=\"1\" />";
        strTxt += "</div>";
    }else if( type == "address" ){
        strTxt += "<div class=\"item_option\">";
        strTxt += "(オプション)　表示する行数を２行にする：<input type=\"checkbox\" name=\"option[" + name + "]\" value=\"1\" />";
        strTxt += "</div>";
    }else if( type == "mail" ){
        strTxt += "<div class=\"item_option\">";
        strTxt += "(オプション)　確認用の入力欄を表示する：<input type=\"checkbox\" name=\"option[" + name + "]\" value=\"1\" maxlength=\"2\" />";
        strTxt += "</div>";
    }else if( type == "select" || type == "checkbox" || type == "radio" ){
        strTxt += "<div class=\"item_option\">";
        strTxt += "選択項目(改行、または「,」カンマ区切りで記入してください)<br />";
        strTxt += "<textarea  name=\"option[" + name + "]\" rows=3 cols=50>選択肢１,選択肢２</textarea>";
        strTxt += "</div>";
    }else{
        strTxt += "<input type=\"hidden\" name=\"option[" + name + "]\" value=\"" + option + "\" />";
    }
    strTxt += "</div><!-- /inner -->";
    strTxt += "<div class=\"updown_button\">";
    strTxt += "並べ替え：";
    strTxt += "<input type=\"button\" value=\"▲\" onclick=\"changeItem( " + nowID + ", " + backID + " );\" />";
    strTxt += "<input type=\"button\" value=\"▼\" onclick=\"changeItem( " + nowID + ", " + nextID + " );\" />";
    strTxt += "</div><!-- /updown_button -->";
    strTxt += "<div class=\"no_display_button\">";
    strTxt += "<input type=\"button\" value=\"除外\" onclick=\"deleteItem( " + nowID + ", '" + name + "' );\" />";
    strTxt += "</div><!-- /no_display_button -->";
    strTxt += "</div>";

    objElement.innerHTML = strTxt;

    if( ! isSub ){
        //基本項目の場合は、非アクティブ化
        document.getElementsByName( "btn_" + name )[0].disabled = true;
    }

    //プレビューを閉じる
    ClosePreview();
}

//予備アイテムの追加
function addSubItem( name, itemName, type, check, option ) {
    var flag  = false;
    var count = 0;
    var i;

    //アイテムの追加処理
    for( i=1;i<=9;i++ ){
        if( document.getElementsByName( "chk_" + name + '0' + i )[0].value == 1 ){
            continue;
        }

        addItem( name + '0' + i, itemName + '0' + i, itemName + '0' + i, type, check, option, true );
        document.getElementsByName( "chk_" + name + '0' + i )[0].value = 1;
        break;
    }

    //追加ボタンの非表示処理
    for( i=1;i<=9;i++ ){
        if( document.getElementsByName( "chk_" + name + '0' + i )[0].value == 1 ){
            continue;
        }
        flag = true;
        count++;
    }

    //追加ボタンを非表示へ
    if( ! flag ){
        //基本項目の場合は、非アクティブ化
        document.getElementsByName( "btn_" + name )[0].disabled = true;
    }
}

//アイテムの削除
function deleteItem( itemID, itemName ) {
    var objElement;
    var count = 0;
    var i;

    for( i=itemID;i<58;i++ ){
        delsetItem( i, ( i + 1 ) );
    }

    objElement = document.getElementById("display_item58");
    objElement.innerHTML    = '';

    //アクティブ化
    if( document.getElementsByName( "chk_" + itemName ).length > 0 ){
        document.getElementsByName( "chk_" + itemName )[0].value = 0;
        itemName = itemName.substr( 0, ( itemName.length - 2 ) );
        document.getElementsByName( "btn_" + itemName )[0].disabled = false;

        //アイテム数の変更
        for( i=1;i<=9;i++ ){
            if( document.getElementsByName( "chk_" + itemName + '0' + i )[0].value == "0" ){
                count++;
            }
        }
    }else{
        document.getElementsByName( "btn_" + itemName )[0].disabled = false;
    }

    //プレビューを閉じる
    ClosePreview();
}

function delsetItem( itemID01, itemID02 ) {
    var objElement01;
    var objElement02;
    var strTxt;
    var strBackID01 = ( itemID01 - 1 );
    var strNextID01 = ( itemID01 + 1 );
    var strBackID02 = ( itemID02 - 1 );
    var strNextID02 = ( itemID02 + 1 );

    if( strBackID01 <= 0 ){    strBackID01 = 1; }
    if( strNextID01 > 58 ){    strNextID01 = 58; }
    if( strBackID02 <= 0 ){    strBackID02 = 1; }
    if( strNextID02 > 58 ){    strNextID02 = 58; }


    //同じ場所の場合は必要なし
    if( itemID01 == itemID02 ){
        return;
    }

    if( itemID01 < 10 ){ objElement01 = document.getElementById("display_item0" + itemID01 );
    }else{               objElement01 = document.getElementById("display_item" + itemID01 );
    }
    if( itemID02 < 10 ){ objElement02 = document.getElementById("display_item0" + itemID02 );
    }else{               objElement02 = document.getElementById("display_item" + itemID02 );
    }

    //テキストを入れ替える
    strTxt                    = objElement01.innerHTML;
    objElement01.innerHTML    = objElement02.innerHTML;
    objElement02.innerHTML    = strTxt;

    //ID番号の入れ替え
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'id[' + itemID02 + ']', 'id[' + itemID01 + ']' );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'id[' + itemID01 + ']', 'id[' + itemID02 + ']' );

    //入れ替えボタンを再設定する
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'onclick="changeItem( ' + itemID02 + ', ' + strBackID02 + ' );"', 'onclick="changeItem( ' + itemID01 + ', ' + strBackID01 + ' );"' );
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'onclick="changeItem( ' + itemID02 + ', ' + strNextID02 + ' );"', 'onclick="changeItem( ' + itemID01 + ', ' + strNextID01 + ' );"' );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'onclick="changeItem( ' + itemID01 + ', ' + strBackID01 + ' );"', 'onclick="changeItem( ' + itemID02 + ', ' + strBackID02 + ' );"' );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'onclick="changeItem( ' + itemID01 + ', ' + strNextID01 + ' );"', 'onclick="changeItem( ' + itemID02 + ', ' + strNextID02 + ' );"' );

    //削除ボタンを再設定する
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'onclick="deleteItem( ' + itemID02, 'onclick="deleteItem( ' + itemID01 );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'onclick="deleteItem( ' + itemID01, 'onclick="deleteItem( ' + itemID02 );
}

//アイテムの並び替え
function changeItem( itemID01, itemID02 ) {
    var objElement01;
    var objElement02;
    var strTxt;
    var strBackID01 = ( itemID01 - 1 );
    var strNextID01 = ( itemID01 + 1 );
    var strBackID02 = ( itemID02 - 1 );
    var strNextID02 = ( itemID02 + 1 );

    if( strBackID01 <= 0 ){    strBackID01 = 1; }
    if( strNextID01 > 58 ){    strNextID01 = 58; }
    if( strBackID02 <= 0 ){    strBackID02 = 1; }
    if( strNextID02 > 58 ){    strNextID02 = 58; }

    //同じ場所の場合は必要なし
    if( itemID01 == itemID02 ){
        return;
    }

    if( itemID01 < 10 ){ objElement01 = document.getElementById("display_item0" + itemID01 );
    }else{               objElement01 = document.getElementById("display_item" + itemID01 );
    }
    if( itemID02 < 10 ){ objElement02 = document.getElementById("display_item0" + itemID02 );
    }else{               objElement02 = document.getElementById("display_item" + itemID02 );
    }

    //空文章がある場合は入れ替えなし
    if( objElement01.innerHTML == '' ){    return;    }
    if( objElement02.innerHTML == '' ){    return;    }

    //テキストを入れ替える
    strTxt                    = objElement01.innerHTML;
    objElement01.innerHTML    = objElement02.innerHTML;
    objElement02.innerHTML    = strTxt;

    //ID番号の入れ替え
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'id[' + itemID02 + ']', 'id[' + itemID01 + ']' );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'id[' + itemID01 + ']', 'id[' + itemID02 + ']' );

    //入れ替えボタンを再設定する
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'onclick="changeItem( ' + itemID02 + ', ' + strBackID02 + ' );"', 'onclick="changeItem( ' + itemID01 + ', ' + strBackID01 + ' );"' );
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'onclick="changeItem( ' + itemID02 + ', ' + strNextID02 + ' );"', 'onclick="changeItem( ' + itemID01 + ', ' + strNextID01 + ' );"' );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'onclick="changeItem( ' + itemID01 + ', ' + strBackID01 + ' );"', 'onclick="changeItem( ' + itemID02 + ', ' + strBackID02 + ' );"' );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'onclick="changeItem( ' + itemID01 + ', ' + strNextID01 + ' );"', 'onclick="changeItem( ' + itemID02 + ', ' + strNextID02 + ' );"' );

    //削除ボタンを再設定する
    objElement01.innerHTML    = objElement01.innerHTML.replace( 'onclick="deleteItem( ' + itemID02, 'onclick="deleteItem( ' + itemID01 );
    objElement02.innerHTML    = objElement02.innerHTML.replace( 'onclick="deleteItem( ' + itemID01, 'onclick="deleteItem( ' + itemID02 );

    //プレビューを閉じる
    ClosePreview();
}

//アイテムの設定確認
function chkSetItem( strItemName ) {
    var objElement;

    if( document.getElementsByName( strItemName ).length > 0 ){
        //非アクティブ化
        document.getElementsByName( "btn_" + strItemName )[0].disabled = true;
    }
}

//アイテムの設定確認
function chkSetSubItem( strItemName ) {
    var objElement;
    var count = 0;
    var i;

    for( i=1;i<=9;i++ ){
        if( document.getElementsByName( strItemName + '0' + i ).length > 0 ){
            document.getElementsByName( "chk_" + strItemName + '0' + i )[0].value = 1;
        }else{
            count++;
        }
    }

    if( count <= 0 ){
        //非アクティブ化
        document.getElementsByName( "btn_" + strItemName )[0].disabled = true;
    }
}

//自動ロード
window.onload = function() {
    chkSetItem( 'item_name' );
    chkSetItem( 'item_kana' );
    chkSetItem( 'item_hp' );
    chkSetItem( 'item_age' );
    chkSetItem( 'item_sex' );
    chkSetItem( 'item_poscode' );
    chkSetItem( 'item_selarea' );
    chkSetItem( 'item_address' );
    chkSetItem( 'item_tel' );
    chkSetItem( 'item_fax' );
    chkSetItem( 'item_mail' );
    chkSetItem( 'item_subject' );
    chkSetItem( 'item_contents' );

    chkSetSubItem( 'item_stext' );
    chkSetSubItem( 'item_sarea' );
    chkSetSubItem( 'item_sselect' );
    chkSetSubItem( 'item_sradio' );
    chkSetSubItem( 'item_scheckbox' );
}

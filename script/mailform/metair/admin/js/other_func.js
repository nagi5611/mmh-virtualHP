//---------------------------------------------
//
//基本／デザイン以外の javascript まとめ
//
//---------------------------------------------

//========================
// 自動返信メールの切り替え
//========================
function ViewChangeAutoMail() {
    if( document.main_menu.auto_mail.value == 1 ){
        document.getElementById( 'auto_mail_table' ).style.display  = 'block';
    }else{
        document.getElementById( 'auto_mail_table' ).style.display  = 'none';
    }
}

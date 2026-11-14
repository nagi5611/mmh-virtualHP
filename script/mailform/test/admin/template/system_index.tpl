        <noscript>
			<div class="red_txt">
				このページは javascript を利用しています。<br />
				javascript が利用できる設定に変更してください。<br />
			</div>
		</noscript>

        <form name="main_menu" method="post" action="./">
        <div id="basic_setting" class="section">
            <h2 class="section__ttl">基本設定</h2>
            <div class="section__body">
                <p>メールフォームの基本の設定を行うことができます。</p>
                
                <div class="red_txt">{$form_error}</div>
                
                <table class="table">
                    <tbody>
                        <tr>
                            <th class="w20per">ページのタイトル</th>
                            <td><input type="text" name="page_title" value="{$basic_pagetitle}" size="40" style="ime-mode: active" onchange="ClosePreview();"></td>
                        </tr>
                        <tr>
                            <th>メールフォームの名前</th>
                            <td><input type="text" name="form_name" value="{$basic_formname}" size="40" style="ime-mode: active" onchange="ClosePreview();"></td>
                        </tr>
                        <tr>
                            <th>サイトの戻りURL</th>
                            <td>
                                <input type="text" name="move_url" value="{$basic_moveurl}" size="40" style="ime-mode: inactive" onchange="ClosePreview();" class="mb5">
                                <p class="mb0">※メールフォームからサイトに戻るためのリンクに使用します</p>
                            </td>
                        </tr>
                        <tr>
                            <th>受信するメールアドレス</th>
                            <td>
                                <input type="text" name="mail_address" value="{$basic_mailaddress}" size="40" style="ime-mode: inactive" onchange="ClosePreview();" class="mb5">
                                <p class="mb0">※お問い合わせの内容を受信するメールアドレスを設定します</p>
                            </td>
                        </tr>
                        <tr>
                            <th>受信するメールの件名</th>
                            <td>
                                <input type="text" name="mail_subject" value="{$basic_mailsubject}" size="40" style="ime-mode: active" onchange="ClosePreview();">
                            </td>
                        </tr>
                        <tr>
                            <th>メールの連続送信を制限する時間</th>
                            <td>
                                <select name="mail_limit_time" class="mb5 mr5">
                                    {$basic_maillimittime}
                                </select>分
                                <p class="mb0">※同一IPによるメールの連続送信を制限する時間を設定します</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="section">
            <h2 id="item_setting" class="section__ttl">メールフォーム項目の設定</h2>
            <div class="section__body">
                <p>メールフォームの項目内容を設定することができます。</p>
                <ul class="ul">
                    <li>「使用可能な項目」から使用する項目を選び、「追加」をクリックしてください。また、「使用中の項目」で使用しないものは「除外」をクリックしてください。</li>
                    <li>「使用中の項目」を並べ替える場合は、「▲」「▼」ボタンをクリックしてください。</li>
                </ul>
                <p class="note">※予備項目は各項目ごとに９つまで追加することができます。</p>
                
                <div class="red_txt">{$form_items_error}</div>
                
                <div class="mailformSetting clearfix">
                    <div class="mailformSetting__use">
                        <h3 class="mailformSetting__ttl">使用中の項目</h3>
                        <div class="mailformSetting__body">
                            {$form_view_data}
                        </div>

                    </div>
                    <div class="mailformSetting__add">
                        <h3 class="mailformSetting__ttl">使用可能な項目</h3>
                        <dl class="addContents">
                            <dt>基本項目</dt>
                            <dd>お名前<input type="button" name="btn_item_name" value="追加" onclick="addItem('item_name','お名前','お名前','fullname','none','0',false);"></dd>
                            <dd>ふりがな<input type="button" name="btn_item_kana" value="追加" onclick="addItem('item_kana','ふりがな','ふりがな','fullname','kana','0',false);"></dd>
                            <dd>ＵＲＬ<input type="button" name="btn_item_hp" value="追加" onclick="addItem('item_hp','ＵＲＬ','ホームページ','text','url','0',false);"></dd>
                            <dd>年齢<input type="button" name="btn_item_age" value="追加" onclick="addItem('item_age','年齢','年齢','text','numeric','64',false);"></dd>
                            <dd>性別<input type="button" name="btn_item_sex" value="追加" onclick="addItem('item_sex','性別','性別','sex','none','0',false);"></dd>
                            <dd>郵便番号<input type="button" name="btn_item_poscode" value="追加" onclick="addItem('item_poscode','郵便番号','郵便番号','code','numeric','1',false);"></dd>
                            <dd>都道府県<input type="button" name="btn_item_selarea" value="追加" onclick="addItem('item_selarea','都道府県','都道府県','selarea','none','0',false);"></dd>
                            <dd>住所<input type="button" name="btn_item_address" value="追加" onclick="addItem('item_address','住所','ご住所','address','none','0',false);"></dd>
                            <dd>ＴＥＬ<input type="button" name="btn_item_tel" value="追加" onclick="addItem('item_tel','ＴＥＬ','お電話番号','text','tel','0',false);"></dd>
                            <dd>ＦＡＸ<input type="button" name="btn_item_fax" value="追加" onclick="addItem('item_fax','ＦＡＸ','ＦＡＸ','text','fax','0',false);"></dd>
                            <dd>ご連絡先メールアドレス<input type="button" name="btn_item_mail" value="追加" onclick="addItem('item_mail','ご連絡先メールアドレス','ご連絡先メールアドレス','mail','mail','0',false);"></dd>
                            <dd>件名<input type="button" name="btn_item_subject" value="追加" onclick="addItem('item_subject','件名','件名','text','none','0',false);"></dd>
                            <dd>お問い合わせ内容<input type="button" name="btn_item_contents" value="追加" onclick="addItem('item_contents','お問い合わせ内容','お問い合わせ内容','textarea','none','6',false);"></dd>
                        </dl>
                        <input type="hidden" name="chk_item_stext01" value="0"><input type="hidden" name="chk_item_stext02" value="0"><input type="hidden" name="chk_item_stext03" value="0">
                        <input type="hidden" name="chk_item_stext04" value="0" /><input type="hidden" name="chk_item_stext05" value="0" /><input type="hidden" name="chk_item_stext06" value="0" />
                        <input type="hidden" name="chk_item_stext07" value="0" /><input type="hidden" name="chk_item_stext08" value="0" /><input type="hidden" name="chk_item_stext09" value="0" />

                        <input type="hidden" name="chk_item_sarea01" value="0" /><input type="hidden" name="chk_item_sarea02" value="0" /><input type="hidden" name="chk_item_sarea03" value="0" />
                        <input type="hidden" name="chk_item_sarea04" value="0" /><input type="hidden" name="chk_item_sarea05" value="0" /><input type="hidden" name="chk_item_sarea06" value="0" />
                        <input type="hidden" name="chk_item_sarea07" value="0" /><input type="hidden" name="chk_item_sarea08" value="0" /><input type="hidden" name="chk_item_sarea09" value="0" />

                        <input type="hidden" name="chk_item_sselect01" value="0" /><input type="hidden" name="chk_item_sselect02" value="0" /><input type="hidden" name="chk_item_sselect03" value="0" />
                        <input type="hidden" name="chk_item_sselect04" value="0" /><input type="hidden" name="chk_item_sselect05" value="0" /><input type="hidden" name="chk_item_sselect06" value="0" />
                        <input type="hidden" name="chk_item_sselect07" value="0" /><input type="hidden" name="chk_item_sselect08" value="0" /><input type="hidden" name="chk_item_sselect09" value="0" />

                        <input type="hidden" name="chk_item_sradio01" value="0" /><input type="hidden" name="chk_item_sradio02" value="0" /><input type="hidden" name="chk_item_sradio03" value="0" />
                        <input type="hidden" name="chk_item_sradio04" value="0" /><input type="hidden" name="chk_item_sradio05" value="0" /><input type="hidden" name="chk_item_sradio06" value="0" />
                        <input type="hidden" name="chk_item_sradio07" value="0" /><input type="hidden" name="chk_item_sradio08" value="0" /><input type="hidden" name="chk_item_sradio09" value="0" />

                        <input type="hidden" name="chk_item_scheckbox01" value="0" /><input type="hidden" name="chk_item_scheckbox02" value="0" /><input type="hidden" name="chk_item_scheckbox03" value="0" />
                        <input type="hidden" name="chk_item_scheckbox04" value="0" /><input type="hidden" name="chk_item_scheckbox05" value="0" /><input type="hidden" name="chk_item_scheckbox06" value="0" />
                        <input type="hidden" name="chk_item_scheckbox07" value="0" /><input type="hidden" name="chk_item_scheckbox08" value="0" /><input type="hidden" name="chk_item_scheckbox09" value="0" />
                        <dl class="addContents">
                            <dt>予備項目</dt>
                            <dd>テキスト<input type="button" name="btn_item_stext" value="追加" onclick="addSubItem('item_stext','予備テキスト','text','none','0');"></dd>
                            <dd>テキストボックス<input type="button" name="btn_item_sarea" value="追加" onclick="addSubItem('item_sarea','予備テキストボックス','textarea','none','6');"></dd>
                            <dd>プルダウンメニュー<input type="button" name="btn_item_sselect" value="追加" onclick="addSubItem('item_sselect','予備プルダウンメニュー','select','none','0');"></dd>
                            <dd>ラジオボタン<input type="button" name="btn_item_sradio" value="追加" onclick="addSubItem('item_sradio','予備ラジオボタン','radio','none','0');"></dd>
                            <dd>チェックボックス<input type="button" name="btn_item_scheckbox" value="追加" onclick="addSubItem('item_scheckbox','予備チェックボックス','checkbox','none','0');"></dd>
                        </dl>
                    </div>
                </div>
                <div class="mailformSetting__fix">
                    <p> 「基本設定」または「メールフォーム項目」の変更を行う場合は、「設定を保存する(確認)」ボタンを押してください。</p>
                    <input type="hidden" name="nowpage" value="system_page">
                    <input type="button" name="reset_button" value="リセット" onclick="document.location = document.location.href">
                    <input type="submit" name="submit_button" value="設定を保存する(確認)">
                    <input type="button" name="preview_button" value="プレビュー" onclick="OpenPreview(this.form, '');">
                </div>
            </div>
        </div>
        
    </div>
    <!-- /#main -->
    </form>
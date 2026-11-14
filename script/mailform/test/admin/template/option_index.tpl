	<noscript>
		<div class="red_txt">
			このページは javascript を利用しています。<br />
			javascript が利用できる設定に変更してください。<br />
		</div>
	</noscript>

	<form name="main_menu" method="post" action="./option.php">
        <div class="section">
		<h2 class="section__ttl">自動返信メールの設定</h2>
		<div class="section__body">
			<div class="left_txt">
				<p>
				お問い合わせを受信した時に自動的に返信するメールを設定することができます。<br />
				自動返信メールを使用するには、フォームに「ご連絡先メールアドレス」の項目を設置する必要があります。<br />
				「ご連絡先メールアドレス」が未記入の場合は、自動返信メールは送信されません。
				</p>
			</div>

			<div class="red_txt">{$automail_error}</div>

			<div style="text-align:left;">
				<select name="auto_mail" onchange="ViewChangeAutoMail();" style="padding-bottom: 4px;">
					<option value="0" {$used_auto_0} />自動返信メールを使用しない
					<option value="1" {$used_auto_1} />自動返信メールを使用する
				</select>
			</div>

			<br />

			<div id="auto_mail_table" {$set_used_automail}>

				<table class="table" border="1" summary="自動返信メールの設定を行うフォーム" cellspacing="0">
					<tr>
						<th scope="row">メールの件名</th>
						<td>
							<input type="text" name="rmail_subject" value="{$mail_subject}" size="40" style="ime-mode: active" />
						</td>
					</tr>
					<tr>
						<th>メールの本文</th>
						<td>
							<textarea name="rmail_body" cols="80" rows="10" style="ime-mode: active" class="mb5" />{$mail_body}</textarea><br />
							<div class="note">
								※###お問い合わせ内容### はお問い合わせいただいた内容が表示されます<br />
							</div>
						</td>
					</tr>
					<tr>
						<th>差出人のアドレス</th>
						<td>
							<input type="text" name="from_address" value="{$from_address}" size="40" style="ime-mode: inactive" class="mb5" />
							<div class="note">
								※自動返信メールの差出人(From)を記入します<br />
							</div>
						</td>
					</tr>
				</table>

			</div>

		</div>
        </div>

        <div class="mailformSetting__fix">
            <p>「自動返信メール」の変更を行う場合は、「<strong>設定を保存する(確認)</strong>」ボタンを押してください。</p>
            <input type="hidden" name="nowpage" value="system_page" />
            <input type="button" name="reset_button" value="リセット" onclick="document.location = document.location.href" />
			<input type="submit" name="submit_button" value="設定を保存する(確認)" />
        </div>

	</form>

	<br />

</div><!-- /contents -->

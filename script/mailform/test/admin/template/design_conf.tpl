
		<form name="main_menu" method="post" action="./design.php">

		<h2 class="section__ttl">サイズの設定</h2>

		<div class="section__body">
			<table class="table" border="1" summary="デザインの設定を行うフォーム">
			<tr>
				<th style="width:30%">横幅</th>
				<td>{$basic_formwidth}px</td>
			</tr>
			</table>
		</div>

		<br />
		<br />

        <form name="main_menu" method="post" action="./design.php">
        <div class="section">
		<h2 class="section__ttl">配色の設定</h2>

		<div class="section__body">
			<table class="table" border="1" summary="デザインの設定を行うフォーム">
			<tr>
				<th style="width:30%">タイトル</th>
				<td>{$color_title}</td>
			</tr>
			<tr>
				<th>説明文</th>
				<td>{$color_explain}</td>
			</tr>
			<tr>
				<th>背景色</th>
				<td>{$color_background}</td>
			</tr>
			<tr>
				<th>枠線</th>
				<td>{$color_line}</td>
			</tr>
			<tr>
				<th>項目背景</th>
				<td>{$color_menubg}</td>
			</tr>
			<tr>
				<th>項目文字</th>
				<td>{$color_menufont}</td>
			</tr>
			<tr>
				<th>エラー表示</th>
				<td>{$color_error}</td>
			</tr>
			</table>

		</div>
        </div>
        <div class="section">
		<h2 class="section__ttl">メッセージの設定</h2>

		<div class="section__body">
		
			<table class="table" border="1" summary="文言の編集を行うフォーム">
			<tr>
				<th style="width:30%">メッセージ<br />（入力画面）</th>
				<td>{$html_txt_explain}</td>
			</tr>
			<tr>
				<th>メッセージ<br />（確認画面）</th>
				<td>{$html_txt_confirm}</td>
			</tr>
			<tr>
				<th>メッセージ<br />（完了画面）</th>
				<td>{$html_txt_exit}</td>
			</tr>
			</table>
		</div>
        </div>

		<input type="hidden" name="design_mode" value="{$design_mode}" />
		<input type="hidden" name="form_width" value="{$basic_formwidth}" />
		<input type="hidden" name="color_title" value="{$color_title}" />
		<input type="hidden" name="color_explain" value="{$color_explain}" />
		<input type="hidden" name="color_background" value="{$color_background}" />
		<input type="hidden" name="color_line" value="{$color_line}" />
		<input type="hidden" name="color_menubg" value="{$color_menubg}" />
		<input type="hidden" name="color_menufont" value="{$color_menufont}" />
		<input type="hidden" name="color_error" value="{$color_error}" />

		<input type="hidden" name="txt_explain" value="{$txt_explain}" />
		<input type="hidden" name="txt_confirm" value="{$txt_confirm}" />
		<input type="hidden" name="txt_exit" value="{$txt_exit}" />
        
        <div class="mailformSetting__fix">
            <p> 上記の内容で保存を行う場合は、「<strong>設定を保存する(確定)</strong>」ボタンを押してください。</p>
            <input type="hidden" name="nowpage" value="design_page" />
            <input type="submit" name="submit_button" value="戻る">
			<input type="submit" name="submit_button" value="設定を保存する(確定)">
			<input type="button" name="preview_button" value="プレビュー" onclick="OpenPreview(this.form, '');" />
        </div>
            
    </div>
    <!-- /#main -->
    </form>

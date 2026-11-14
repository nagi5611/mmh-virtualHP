
	
		<noscript>
			<div class="red_txt">
				このページは javascript を利用しています。<br />
				javascript が利用できる設定に変更してください。
			</div>
		</noscript>

		<form name="main_menu" method="post" action="./design.php">
        <div class="section">
		<h2 class="section__ttl">スキンの変更</h2>
 
		<div class="section__body">
			<p>メールフォームのスキンを変更することができます。<br />
			・「定型スキン」は既定のメールフォームを使用してサイズや色、メッセージを簡単に設定できます。<br />
			・「自作スキン」はHTML、CSSを編集してメールフォームを使用サイトにあわせて自由に設定できます。<br />
				<div style="text-align:left;">
					<input type="hidden" name="default_design_mode" value="{$default_design_mode}" />
					<select name="design_mode" onchange="ChangeDesignMode();" style="padding-bottom: 4px;">
						<option value="0" {$design_mode_0}>定型スキンを利用する
						<option value="1" {$design_mode_1}>自作スキンを利用する
					</select>
				</div>
			</p>
		</div>
        </div>


<!-- ========== ↓ここから「定型スキン」 ================================================== -->
	<div id="default_design" {$set_default_design}>
        <div class="section">
		<h2 class="section__ttl">サイズの設定</h2>

		<div class="section__body">
			<p>メールフォームのサイズ設定を行うことができます。</p>

			<div class="red_txt">{$form_basic_error}</div>

			<table class="table" summary="デザインの設定を行うフォーム">
			<tr>
				<th>横幅</th>
				<td>
					<input type="text" name="form_width" value="{$basic_formwidth}" style="ime-mode: inactive; margin-right: 5px;" maxlength="4" onchange="ClosePreview();" />px
				</td>
			</tr>
			</table>
		</div>
        </div>
        
        <div class="section">
		<h2 class="section__ttl">配色の設定</h2>

		<div class="section__body">
			<p>メールフォームの配色を設定を行うことができます。<br />
			配色の設定を行う場合は、<strong>６桁のカラーコード</strong>を「設定色」に記入してください。</p>

			<div class="red_txt">{$form_color_error}</div>

			<table class="table" border="1" summary="デザインの設定を行うフォーム">
			<tr>
				<th>タイトル</th>
				<td>
					<div class="color_setting_wrapper">
					<div class="explain_item">タイトルの背景色を設定します</div>
                    <div class="clearfix">
					<div class="setting_item">設定色：#<input name="color_title" id="color_title" type="text" value="{$color_title}" maxlength="6" style="ime-mode: inactive; margin-left: 5px;" onchange="ClosePreview();setColorPanel('color_title');" /></div>
					<div class="current_color">
						<div id="panel_color_title" class="current_setting" style="background-color : #{$color_title} ;">現在の設定カラー</div><!-- current_setting -->
						#<span id="code_color_title">{$color_title}</span>
					</div>
                    </div>
                    <a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ff6600' );setColorPanel('color_title');ClosePreview();return false;" class="set_default">初期設定値に戻す</a>

					<dl class="select_item clearfix">
						<dt>色見本：</dt>
						<dd>
							<ul class="color_sample">
                                <li style="background-color : #000000 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '000000' );setColorPanel('color_title');ClosePreview();return false;" title="黒">黒</a></li>
                                <li style="background-color : #ffffff ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ffffff' );setColorPanel('color_title');ClosePreview();return false;" title="白">白</a></li>
                                <li style="background-color : #4d4d4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '4d4d4d' );setColorPanel('color_title');ClosePreview();return false;" title="グレー70%">グレー70%</a></li>
                                <li style="background-color : #999999 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '999999' );setColorPanel('color_title');ClosePreview();return false;" title="グレー40%">グレー40%</a></li>
                                <li style="background-color : #cccccc ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'cccccc' );setColorPanel('color_title');ClosePreview();return false;" title="グレー20%">グレー20%</a></li>
                                <li style="background-color : #ff2323 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ff2323' );setColorPanel('color_title');ClosePreview();return false;" title="赤100%">赤100%</a></li>
                                <li style="background-color : #ff6565 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ff6565' );setColorPanel('color_title');ClosePreview();return false;" title="赤70%">赤70%</a></li>
                                <li style="background-color : #ffa7a7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ffa7a7' );setColorPanel('color_title');ClosePreview();return false;" title="赤40%">赤40%</a></li>
                                <li style="background-color : #ff5500 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ff5500' );setColorPanel('color_title');ClosePreview();return false;" title="橙100%">橙100%</a></li>
                                <li style="background-color : #ff884d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ff884d' );setColorPanel('color_title');ClosePreview();return false;" title="橙70%">橙70%</a></li>
                                <li style="background-color : #ffbb99 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ffbb99' );setColorPanel('color_title');ClosePreview();return false;" title="橙40%">橙40%</a></li>
                                <li style="background-color : #ffb400 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ffb400' );setColorPanel('color_title');ClosePreview();return false;" title="黄100%">黄100%</a></li>
                                <li style="background-color : #ffcb4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ffcb4d' );setColorPanel('color_title');ClosePreview();return false;" title="黄70%">黄70%</a></li>
                                <li style="background-color : #ffe199 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'ffe199' );setColorPanel('color_title');ClosePreview();return false;" title="黄40%">黄40%</a></li>
                                <li style="background-color : #00aa32 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '00aa32' );setColorPanel('color_title');ClosePreview();return false;" title="緑100%">緑100%</a></li>
                                <li style="background-color : #4dc470 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '4dc470' );setColorPanel('color_title');ClosePreview();return false;" title="緑70%">緑70%</a></li>
                                <li style="background-color : #99ddad ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '99ddad' );setColorPanel('color_title');ClosePreview();return false;" title="緑40%">緑40%</a></li>
                                <li style="background-color : #2ca6e0 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '2ca6e0' );setColorPanel('color_title');ClosePreview();return false;" title="薄青100%">薄青100%</a></li>
                                <li style="background-color : #6cc1e9 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '6cc1e9' );setColorPanel('color_title');ClosePreview();return false;" title="薄青70%">薄青70%</a></li>
                                <li style="background-color : #abdbf3 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'abdbf3' );setColorPanel('color_title');ClosePreview();return false;" title="薄青40%">薄青40%</a></li>
                                <li style="background-color : #233287 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '233287' );setColorPanel('color_title');ClosePreview();return false;" title="青100%">青100%</a></li>
                                <li style="background-color : #6570ab ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '6570ab' );setColorPanel('color_title');ClosePreview();return false;" title="青70%">青70%</a></li>
                                <li style="background-color : #a7adcf ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'a7adcf' );setColorPanel('color_title');ClosePreview();return false;" title="青40%">青40%</a></li>
                                <li style="background-color : #731e82 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '731e82' );setColorPanel('color_title');ClosePreview();return false;" title="紫100%">紫100%</a></li>
                                <li style="background-color : #9d62a8 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', '9d62a8' );setColorPanel('color_title');ClosePreview();return false;" title="紫70%">紫70%</a></li>
                                <li style="background-color : #c7a5cd ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'c7a5cd' );setColorPanel('color_title');ClosePreview();return false;" title="紫40%">紫40%</a></li>
                                <li style="background-color : #eb3c73 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'eb3c73' );setColorPanel('color_title');ClosePreview();return false;" title="桃100%">桃100%</a></li>
                                <li style="background-color : #f1779d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'f1779d' );setColorPanel('color_title');ClosePreview();return false;" title="桃70%">桃70%</a></li>
                                <li style="background-color : #f7b1c7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_title', 'f7b1c7' );setColorPanel('color_title');ClosePreview();return false;" title="桃40%">桃40%</a></li>
                            </ul>
                        </dd>
                    </dl>
                    </div>
                    <!-- /color_setting_wrapper -->
				</td>
			</tr>
			<tr>
				<th>説明文</th>
				<td>
					<div class="color_setting_wrapper">
					<div class="explain_item">説明文の文字色を設定します</div>
                    <div class="clearfix">
					<div class="setting_item">設定色：#<input name="color_explain" id="color_explain" type="text" value="{$color_explain}" maxlength="6" style="ime-mode: inactive; margin-left: 5px;" onchange="ClosePreview();setColorPanel('color_explain');" /></div>
					<div class="current_color">
						<div id="panel_color_explain" class="current_setting" style="background-color : #{$color_explain} ;">現在の設定カラー</div><!-- current_setting -->
						#<span id="code_color_explain">{$color_explain}</span>
					</div>
                    </div>
                    <a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '000000' );setColorPanel('color_explain');ClosePreview();return false;" class="set_default">初期設定値に戻す</a>
					<dl class="select_item clearfix">
                    	<dt>色見本：</dt>
                        <dd>
                            <ul class="color_sample">
                                <li style="background-color : #000000 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '000000' );setColorPanel('color_explain');ClosePreview();return false;" title="黒">黒</a></li>
                                <li style="background-color : #ffffff ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ffffff' );setColorPanel('color_explain');ClosePreview();return false;" title="白">白</a></li>
                                <li style="background-color : #4d4d4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '4d4d4d' );setColorPanel('color_explain');ClosePreview();return false;" title="グレー70%">グレー70%</a></li>
                                <li style="background-color : #999999 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '999999' );setColorPanel('color_explain');ClosePreview();return false;" title="グレー40%">グレー40%</a></li>
                                <li style="background-color : #cccccc ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'cccccc' );setColorPanel('color_explain');ClosePreview();return false;" title="グレー20%">グレー20%</a></li>
                                <li style="background-color : #ff2323 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ff2323' );setColorPanel('color_explain');ClosePreview();return false;" title="赤100%">赤100%</a></li>
                                <li style="background-color : #ff6565 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ff6565' );setColorPanel('color_explain');ClosePreview();return false;" title="赤70%">赤70%</a></li>
                                <li style="background-color : #ffa7a7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ffa7a7' );setColorPanel('color_explain');ClosePreview();return false;" title="赤40%">赤40%</a></li>
                                <li style="background-color : #ff5500 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ff5500' );setColorPanel('color_explain');ClosePreview();return false;" title="橙100%">橙100%</a></li>
                                <li style="background-color : #ff884d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ff884d' );setColorPanel('color_explain');ClosePreview();return false;" title="橙70%">橙70%</a></li>
                                <li style="background-color : #ffbb99 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ffbb99' );setColorPanel('color_explain');ClosePreview();return false;" title="橙40%">橙40%</a></li>
                                <li style="background-color : #ffb400 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ffb400' );setColorPanel('color_explain');ClosePreview();return false;" title="黄100%">黄100%</a></li>
                                <li style="background-color : #ffcb4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ffcb4d' );setColorPanel('color_explain');ClosePreview();return false;" title="黄70%">黄70%</a></li>
                                <li style="background-color : #ffe199 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'ffe199' );setColorPanel('color_explain');ClosePreview();return false;" title="黄40%">黄40%</a></li>
                                <li style="background-color : #00aa32 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '00aa32' );setColorPanel('color_explain');ClosePreview();return false;" title="緑100%">緑100%</a></li>
                                <li style="background-color : #4dc470 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '4dc470' );setColorPanel('color_explain');ClosePreview();return false;" title="緑70%">緑70%</a></li>
                                <li style="background-color : #99ddad ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '99ddad' );setColorPanel('color_explain');ClosePreview();return false;" title="緑40%">緑40%</a></li>
                                <li style="background-color : #2ca6e0 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '2ca6e0' );setColorPanel('color_explain');ClosePreview();return false;" title="薄青100%">薄青100%</a></li>
                                <li style="background-color : #6cc1e9 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '6cc1e9' );setColorPanel('color_explain');ClosePreview();return false;" title="薄青70%">薄青70%</a></li>
                                <li style="background-color : #abdbf3 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'abdbf3' );setColorPanel('color_explain');ClosePreview();return false;" title="薄青40%">薄青40%</a></li>
                                <li style="background-color : #233287 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '233287' );setColorPanel('color_explain');ClosePreview();return false;" title="青100%">青100%</a></li>
                                <li style="background-color : #6570ab ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '6570ab' );setColorPanel('color_explain');ClosePreview();return false;" title="青70%">青70%</a></li>
                                <li style="background-color : #a7adcf ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'a7adcf' );setColorPanel('color_explain');ClosePreview();return false;" title="青40%">青40%</a></li>
                                <li style="background-color : #731e82 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '731e82' );setColorPanel('color_explain');ClosePreview();return false;" title="紫100%">紫100%</a></li>
                                <li style="background-color : #9d62a8 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', '9d62a8' );setColorPanel('color_explain');ClosePreview();return false;" title="紫70%">紫70%</a></li>
                                <li style="background-color : #c7a5cd ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'c7a5cd' );setColorPanel('color_explain');ClosePreview();return false;" title="紫40%">紫40%</a></li>
                                <li style="background-color : #eb3c73 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'eb3c73' );setColorPanel('color_explain');ClosePreview();return false;" title="桃100%">桃100%</a></li>
                                <li style="background-color : #f1779d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'f1779d' );setColorPanel('color_explain');ClosePreview();return false;" title="桃70%">桃70%</a></li>
                                <li style="background-color : #f7b1c7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_explain', 'f7b1c7' );setColorPanel('color_explain');ClosePreview();return false;" title="桃40%">桃40%</a></li>
                            </ul>
                        </dd>
                    </dl>
                    </div>
                    <!-- /color_setting_wrapper -->
				</td>
			</tr>
			<tr>
				<th>背景色</th>
				<td>
                	<div class="color_setting_wrapper">
					<div class="explain_item">画面全体の背景色を設定します</div>
                    <div class="clearfix">
					<div class="setting_item">設定色：#<input name="color_background" id="color_background" type="text" value="{$color_background}" maxlength="6" style="ime-mode: inactive; margin-left: 5px;" onchange="ClosePreview();setColorPanel('color_background');" /></div>
					<div class="current_color">
						<div id="panel_color_background" class="current_setting" style="background-color : #{$color_background} ;">現在の設定カラー</div><!-- current_setting -->
						#<span id="code_color_background">{$color_background}</span>
					</div>
                    </div>
                    <a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ffffff' );setColorPanel('color_background');ClosePreview();return false;" class="set_default">初期設定値に戻す</a>
					<dl class="select_item clearfix">
                    	<dt>色見本：</dt>
                        <dd>
                            <ul class="color_sample">
                                <li style="background-color : #000000 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '000000' );setColorPanel('color_background');ClosePreview();return false;" title="黒">黒</a></li>
                                <li style="background-color : #ffffff ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ffffff' );setColorPanel('color_background');ClosePreview();return false;" title="白">白</a></li>
                                <li style="background-color : #4d4d4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '4d4d4d' );setColorPanel('color_background');ClosePreview();return false;" title="グレー70%">グレー70%</a></li>
                                <li style="background-color : #999999 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '999999' );setColorPanel('color_background');ClosePreview();return false;" title="グレー40%">グレー40%</a></li>
                                <li style="background-color : #cccccc ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'cccccc' );setColorPanel('color_background');ClosePreview();return false;" title="グレー20%">グレー20%</a></li>
                                <li style="background-color : #ff2323 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ff2323' );setColorPanel('color_background');ClosePreview();return false;" title="赤100%">赤100%</a></li>
                                <li style="background-color : #ff6565 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ff6565' );setColorPanel('color_background');ClosePreview();return false;" title="赤70%">赤70%</a></li>
                                <li style="background-color : #ffa7a7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ffa7a7' );setColorPanel('color_background');ClosePreview();return false;" title="赤40%">赤40%</a></li>
                                <li style="background-color : #ff5500 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ff5500' );setColorPanel('color_background');ClosePreview();return false;" title="橙100%">橙100%</a></li>
                                <li style="background-color : #ff884d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ff884d' );setColorPanel('color_background');ClosePreview();return false;" title="橙70%">橙70%</a></li>
                                <li style="background-color : #ffbb99 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ffbb99' );setColorPanel('color_background');ClosePreview();return false;" title="橙40%">橙40%</a></li>
                                <li style="background-color : #ffb400 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ffb400' );setColorPanel('color_background');ClosePreview();return false;" title="黄100%">黄100%</a></li>
                                <li style="background-color : #ffcb4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ffcb4d' );setColorPanel('color_background');ClosePreview();return false;" title="黄70%">黄70%</a></li>
                                <li style="background-color : #ffe199 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'ffe199' );setColorPanel('color_background');ClosePreview();return false;" title="黄40%">黄40%</a></li>
                                <li style="background-color : #00aa32 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '00aa32' );setColorPanel('color_background');ClosePreview();return false;" title="緑100%">緑100%</a></li>
                                <li style="background-color : #4dc470 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '4dc470' );setColorPanel('color_background');ClosePreview();return false;" title="緑70%">緑70%</a></li>
                                <li style="background-color : #99ddad ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '99ddad' );setColorPanel('color_background');ClosePreview();return false;" title="緑40%">緑40%</a></li>
                                <li style="background-color : #2ca6e0 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '2ca6e0' );setColorPanel('color_background');ClosePreview();return false;" title="薄青100%">薄青100%</a></li>
                                <li style="background-color : #6cc1e9 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '6cc1e9' );setColorPanel('color_background');ClosePreview();return false;" title="薄青70%">薄青70%</a></li>
                                <li style="background-color : #abdbf3 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'abdbf3' );setColorPanel('color_background');ClosePreview();return false;" title="薄青40%">薄青40%</a></li>
                                <li style="background-color : #233287 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '233287' );setColorPanel('color_background');ClosePreview();return false;" title="青100%">青100%</a></li>
                                <li style="background-color : #6570ab ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '6570ab' );setColorPanel('color_background');ClosePreview();return false;" title="青70%">青70%</a></li>
                                <li style="background-color : #a7adcf ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'a7adcf' );setColorPanel('color_background');ClosePreview();return false;" title="青40%">青40%</a></li>
                                <li style="background-color : #731e82 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '731e82' );setColorPanel('color_background');ClosePreview();return false;" title="紫100%">紫100%</a></li>
                                <li style="background-color : #9d62a8 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', '9d62a8' );setColorPanel('color_background');ClosePreview();return false;" title="紫70%">紫70%</a></li>
                                <li style="background-color : #c7a5cd ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'c7a5cd' );setColorPanel('color_background');ClosePreview();return false;" title="紫40%">紫40%</a></li>
                                <li style="background-color : #eb3c73 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'eb3c73' );setColorPanel('color_background');ClosePreview();return false;" title="桃100%">桃100%</a></li>
                                <li style="background-color : #f1779d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'f1779d' );setColorPanel('color_background');ClosePreview();return false;" title="桃70%">桃70%</a></li>
                                <li style="background-color : #f7b1c7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_background', 'f7b1c7' );setColorPanel('color_background');ClosePreview();return false;" title="桃40%">桃40%</a></li>
                            </ul>
                        </dd>
                    </dl>
                    </div>
                    <!-- /color_setting_wrapper -->
				</td>
			</tr>
			<tr>
				<th>枠線</th>
				<td>
                	<div class="color_setting_wrapper">
					<div class="explain_item">枠線の色を設定します</div>
                    <div class="clearfix">
					<div class="setting_item">設定色：#<input name="color_line" id="color_line" type="text" value="{$color_line}" maxlength="6" style="ime-mode: inactive; margin-left: 5px;" onchange="ClosePreview();setColorPanel('color_line');" /></div>
					<div class="current_color">
						<div id="panel_color_line" class="current_setting" style="background-color : #{$color_line} ;">現在の設定カラー</div><!-- current_setting -->
						#<span id="code_color_line">{$color_line}</span>
					</div>
                    </div>
                    <a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'b3b3b3' );setColorPanel('color_line');ClosePreview();return false;" class="set_default">初期設定値に戻す</a>
					<dl class="select_item clearfix">
                    	<dt>色見本：</dt>
                        <dd>
                            <ul class="color_sample">
                                <li style="background-color : #000000 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '000000' );setColorPanel('color_line');ClosePreview();return false;" title="黒">黒</a></li>
                                <li style="background-color : #ffffff ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ffffff' );setColorPanel('color_line');ClosePreview();return false;" title="白">白</a></li>
                                <li style="background-color : #4d4d4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '4d4d4d' );setColorPanel('color_line');ClosePreview();return false;" title="グレー70%">グレー70%</a></li>
                                <li style="background-color : #999999 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '999999' );setColorPanel('color_line');ClosePreview();return false;" title="グレー40%">グレー40%</a></li>
                                <li style="background-color : #cccccc ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'cccccc' );setColorPanel('color_line');ClosePreview();return false;" title="グレー20%">グレー20%</a></li>
                                <li style="background-color : #ff2323 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ff2323' );setColorPanel('color_line');ClosePreview();return false;" title="赤100%">赤100%</a></li>
                                <li style="background-color : #ff6565 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ff6565' );setColorPanel('color_line');ClosePreview();return false;" title="赤70%">赤70%</a></li>
                                <li style="background-color : #ffa7a7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ffa7a7' );setColorPanel('color_line');ClosePreview();return false;" title="赤40%">赤40%</a></li>
                                <li style="background-color : #ff5500 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ff5500' );setColorPanel('color_line');ClosePreview();return false;" title="橙100%">橙100%</a></li>
                                <li style="background-color : #ff884d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ff884d' );setColorPanel('color_line');ClosePreview();return false;" title="橙70%">橙70%</a></li>
                                <li style="background-color : #ffbb99 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ffbb99' );setColorPanel('color_line');ClosePreview();return false;" title="橙40%">橙40%</a></li>
                                <li style="background-color : #ffb400 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ffb400' );setColorPanel('color_line');ClosePreview();return false;" title="黄100%">黄100%</a></li>
                                <li style="background-color : #ffcb4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ffcb4d' );setColorPanel('color_line');ClosePreview();return false;" title="黄70%">黄70%</a></li>
                                <li style="background-color : #ffe199 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'ffe199' );setColorPanel('color_line');ClosePreview();return false;" title="黄40%">黄40%</a></li>
                                <li style="background-color : #00aa32 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '00aa32' );setColorPanel('color_line');ClosePreview();return false;" title="緑100%">緑100%</a></li>
                                <li style="background-color : #4dc470 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '4dc470' );setColorPanel('color_line');ClosePreview();return false;" title="緑70%">緑70%</a></li>
                                <li style="background-color : #99ddad ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '99ddad' );setColorPanel('color_line');ClosePreview();return false;" title="緑40%">緑40%</a></li>
                                <li style="background-color : #2ca6e0 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '2ca6e0' );setColorPanel('color_line');ClosePreview();return false;" title="薄青100%">薄青100%</a></li>
                                <li style="background-color : #6cc1e9 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '6cc1e9' );setColorPanel('color_line');ClosePreview();return false;" title="薄青70%">薄青70%</a></li>
                                <li style="background-color : #abdbf3 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'abdbf3' );setColorPanel('color_line');ClosePreview();return false;" title="薄青40%">薄青40%</a></li>
                                <li style="background-color : #233287 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '233287' );setColorPanel('color_line');ClosePreview();return false;" title="青100%">青100%</a></li>
                                <li style="background-color : #6570ab ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '6570ab' );setColorPanel('color_line');ClosePreview();return false;" title="青70%">青70%</a></li>
                                <li style="background-color : #a7adcf ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'a7adcf' );setColorPanel('color_line');ClosePreview();return false;" title="青40%">青40%</a></li>
                                <li style="background-color : #731e82 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '731e82' );setColorPanel('color_line');ClosePreview();return false;" title="紫100%">紫100%</a></li>
                                <li style="background-color : #9d62a8 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', '9d62a8' );setColorPanel('color_line');ClosePreview();return false;" title="紫70%">紫70%</a></li>
                                <li style="background-color : #c7a5cd ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'c7a5cd' );setColorPanel('color_line');ClosePreview();return false;" title="紫40%">紫40%</a></li>
                                <li style="background-color : #eb3c73 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'eb3c73' );setColorPanel('color_line');ClosePreview();return false;" title="桃100%">桃100%</a></li>
                                <li style="background-color : #f1779d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'f1779d' );setColorPanel('color_line');ClosePreview();return false;" title="桃70%">桃70%</a></li>
                                <li style="background-color : #f7b1c7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_line', 'f7b1c7' );setColorPanel('color_line');ClosePreview();return false;" title="桃40%">桃40%</a></li>
                            </ul>
                        </dd>
                    </dl>

                    </div>
                    <!-- /color_setting_wrapper -->
				</td>
			</tr>
			<tr>
				<th>項目背景</th>
				<td>
                	<div class="color_setting_wrapper">
					<div class="explain_item">項目内の背景色を設定します</div>
                    <div class="clearfix">
					<div class="setting_item">設定色：#<input name="color_menubg" id="color_menubg" type="text" value="{$color_menubg}" maxlength="6" style="ime-mode: inactive; margin-left: 5px;" onchange="ClosePreview();setColorPanel('color_menubg');" /></div>
					<div class="current_color">
						<div id="panel_color_menubg" class="current_setting" style="background-color : #{$color_menubg} ;">現在の設定カラー</div><!-- current_setting -->
						#<span id="code_color_menubg">{$color_menubg}</span>
					</div>
                    </div>
                    <a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '444444' );setColorPanel('color_menubg');ClosePreview();return false;" class="set_default">初期設定値に戻す</a>
					<dl class="select_item clearfix">
						<dt>色見本：</dt>
						<dd>
							<ul class="color_sample">
                                <li style="background-color : #000000 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '000000' );setColorPanel('color_menubg');ClosePreview();return false;" title="黒">黒</a></li>
                                <li style="background-color : #ffffff ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ffffff' );setColorPanel('color_menubg');ClosePreview();return false;" title="白">白</a></li>
                                <li style="background-color : #4d4d4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '4d4d4d' );setColorPanel('color_menubg');ClosePreview();return false;" title="グレー70%">グレー70%</a></li>
                                <li style="background-color : #999999 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '999999' );setColorPanel('color_menubg');ClosePreview();return false;" title="グレー40%">グレー40%</a></li>
                                <li style="background-color : #cccccc ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'cccccc' );setColorPanel('color_menubg');ClosePreview();return false;" title="グレー20%">グレー20%</a></li>
                                <li style="background-color : #ff2323 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ff2323' );setColorPanel('color_menubg');ClosePreview();return false;" title="赤100%">赤100%</a></li>
                                <li style="background-color : #ff6565 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ff6565' );setColorPanel('color_menubg');ClosePreview();return false;" title="赤70%">赤70%</a></li>
                                <li style="background-color : #ffa7a7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ffa7a7' );setColorPanel('color_menubg');ClosePreview();return false;" title="赤40%">赤40%</a></li>
                                <li style="background-color : #ff5500 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ff5500' );setColorPanel('color_menubg');ClosePreview();return false;" title="橙100%">橙100%</a></li>
                                <li style="background-color : #ff884d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ff884d' );setColorPanel('color_menubg');ClosePreview();return false;" title="橙70%">橙70%</a></li>
                                <li style="background-color : #ffbb99 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ffbb99' );setColorPanel('color_menubg');ClosePreview();return false;" title="橙40%">橙40%</a></li>
                                <li style="background-color : #ffb400 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ffb400' );setColorPanel('color_menubg');ClosePreview();return false;" title="黄100%">黄100%</a></li>
                                <li style="background-color : #ffcb4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ffcb4d' );setColorPanel('color_menubg');ClosePreview();return false;" title="黄70%">黄70%</a></li>
                                <li style="background-color : #ffe199 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'ffe199' );setColorPanel('color_menubg');ClosePreview();return false;" title="黄40%">黄40%</a></li>
                                <li style="background-color : #00aa32 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '00aa32' );setColorPanel('color_menubg');ClosePreview();return false;" title="緑100%">緑100%</a></li>
                                <li style="background-color : #4dc470 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '4dc470' );setColorPanel('color_menubg');ClosePreview();return false;" title="緑70%">緑70%</a></li>
                                <li style="background-color : #99ddad ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '99ddad' );setColorPanel('color_menubg');ClosePreview();return false;" title="緑40%">緑40%</a></li>
                                <li style="background-color : #2ca6e0 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '2ca6e0' );setColorPanel('color_menubg');ClosePreview();return false;" title="薄青100%">薄青100%</a></li>
                                <li style="background-color : #6cc1e9 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '6cc1e9' );setColorPanel('color_menubg');ClosePreview();return false;" title="薄青70%">薄青70%</a></li>
                                <li style="background-color : #abdbf3 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'abdbf3' );setColorPanel('color_menubg');ClosePreview();return false;" title="薄青40%">薄青40%</a></li>
                                <li style="background-color : #233287 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '233287' );setColorPanel('color_menubg');ClosePreview();return false;" title="青100%">青100%</a></li>
                                <li style="background-color : #6570ab ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '6570ab' );setColorPanel('color_menubg');ClosePreview();return false;" title="青70%">青70%</a></li>
                                <li style="background-color : #a7adcf ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'a7adcf' );setColorPanel('color_menubg');ClosePreview();return false;" title="青40%">青40%</a></li>
                                <li style="background-color : #731e82 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '731e82' );setColorPanel('color_menubg');ClosePreview();return false;" title="紫100%">紫100%</a></li>
                                <li style="background-color : #9d62a8 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', '9d62a8' );setColorPanel('color_menubg');ClosePreview();return false;" title="紫70%">紫70%</a></li>
                                <li style="background-color : #c7a5cd ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'c7a5cd' );setColorPanel('color_menubg');ClosePreview();return false;" title="紫40%">紫40%</a></li>
                                <li style="background-color : #eb3c73 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'eb3c73' );setColorPanel('color_menubg');ClosePreview();return false;" title="桃100%">桃100%</a></li>
                                <li style="background-color : #f1779d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'f1779d' );setColorPanel('color_menubg');ClosePreview();return false;" title="桃70%">桃70%</a></li>
                                <li style="background-color : #f7b1c7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menubg', 'f7b1c7' );setColorPanel('color_menubg');ClosePreview();return false;" title="桃40%">桃40%</a></li>
                            </ul>
                        </dd>
                    </dl>

                    </div>
                    <!-- /color_setting_wrapper -->				</td>
			</tr>
			<tr>
				<th>項目文字</th>
				<td>
                	<div class="color_setting_wrapper">
					<div class="explain_item">項目内の文字色を設定します</div>
                    <div class="clearfix">
					<div class="setting_item">設定色：#<input name="color_menufont" id="color_menufont" type="text" value="{$color_menufont}" maxlength="6" style="ime-mode: inactive; margin-left: 5px;" onchange="ClosePreview();setColorPanel('color_menufont');" /></div>
					<div class="current_color">
						<div id="panel_color_menufont" class="current_setting" style="background-color : #{$color_menufont} ;">現在の設定カラー</div><!-- current_setting -->
						#<span id="code_color_menufont">{$color_menufont}</span>
					</div>
                    </div>
                    <a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ffffff' );setColorPanel('color_menufont');ClosePreview();return false;" class="set_default">初期設定値に戻す</a>
					<dl class="select_item clearfix">
                    	<dt>色見本：</dt>
                        <dd>
                            <ul class="color_sample">
                                <li style="background-color : #000000 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '000000' );setColorPanel('color_menufont');ClosePreview();return false;" title="黒">黒</a></li>
                                <li style="background-color : #ffffff ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ffffff' );setColorPanel('color_menufont');ClosePreview();return false;" title="白">白</a></li>
                                <li style="background-color : #4d4d4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '4d4d4d' );setColorPanel('color_menufont');ClosePreview();return false;" title="グレー70%">グレー70%</a></li>
                                <li style="background-color : #999999 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '999999' );setColorPanel('color_menufont');ClosePreview();return false;" title="グレー40%">グレー40%</a></li>
                                <li style="background-color : #cccccc ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'cccccc' );setColorPanel('color_menufont');ClosePreview();return false;" title="グレー20%">グレー20%</a></li>
                                <li style="background-color : #ff2323 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ff2323' );setColorPanel('color_menufont');ClosePreview();return false;" title="赤100%">赤100%</a></li>
                                <li style="background-color : #ff6565 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ff6565' );setColorPanel('color_menufont');ClosePreview();return false;" title="赤70%">赤70%</a></li>
                                <li style="background-color : #ffa7a7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ffa7a7' );setColorPanel('color_menufont');ClosePreview();return false;" title="赤40%">赤40%</a></li>
                                <li style="background-color : #ff5500 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ff5500' );setColorPanel('color_menufont');ClosePreview();return false;" title="橙100%">橙100%</a></li>
                                <li style="background-color : #ff884d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ff884d' );setColorPanel('color_menufont');ClosePreview();return false;" title="橙70%">橙70%</a></li>
                                <li style="background-color : #ffbb99 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ffbb99' );setColorPanel('color_menufont');ClosePreview();return false;" title="橙40%">橙40%</a></li>
                                <li style="background-color : #ffb400 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ffb400' );setColorPanel('color_menufont');ClosePreview();return false;" title="黄100%">黄100%</a></li>
                                <li style="background-color : #ffcb4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ffcb4d' );setColorPanel('color_menufont');ClosePreview();return false;" title="黄70%">黄70%</a></li>
                                <li style="background-color : #ffe199 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'ffe199' );setColorPanel('color_menufont');ClosePreview();return false;" title="黄40%">黄40%</a></li>
                                <li style="background-color : #00aa32 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '00aa32' );setColorPanel('color_menufont');ClosePreview();return false;" title="緑100%">緑100%</a></li>
                                <li style="background-color : #4dc470 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '4dc470' );setColorPanel('color_menufont');ClosePreview();return false;" title="緑70%">緑70%</a></li>
                                <li style="background-color : #99ddad ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '99ddad' );setColorPanel('color_menufont');ClosePreview();return false;" title="緑40%">緑40%</a></li>
                                <li style="background-color : #2ca6e0 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '2ca6e0' );setColorPanel('color_menufont');ClosePreview();return false;" title="薄青100%">薄青100%</a></li>
                                <li style="background-color : #6cc1e9 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '6cc1e9' );setColorPanel('color_menufont');ClosePreview();return false;" title="薄青70%">薄青70%</a></li>
                                <li style="background-color : #abdbf3 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'abdbf3' );setColorPanel('color_menufont');ClosePreview();return false;" title="薄青40%">薄青40%</a></li>
                                <li style="background-color : #233287 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '233287' );setColorPanel('color_menufont');ClosePreview();return false;" title="青100%">青100%</a></li>
                                <li style="background-color : #6570ab ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '6570ab' );setColorPanel('color_menufont');ClosePreview();return false;" title="青70%">青70%</a></li>
                                <li style="background-color : #a7adcf ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'a7adcf' );setColorPanel('color_menufont');ClosePreview();return false;" title="青40%">青40%</a></li>
                                <li style="background-color : #731e82 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '731e82' );setColorPanel('color_menufont');ClosePreview();return false;" title="紫100%">紫100%</a></li>
                                <li style="background-color : #9d62a8 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', '9d62a8' );setColorPanel('color_menufont');ClosePreview();return false;" title="紫70%">紫70%</a></li>
                                <li style="background-color : #c7a5cd ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'c7a5cd' );setColorPanel('color_menufont');ClosePreview();return false;" title="紫40%">紫40%</a></li>
                                <li style="background-color : #eb3c73 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'eb3c73' );setColorPanel('color_menufont');ClosePreview();return false;" title="桃100%">桃100%</a></li>
                                <li style="background-color : #f1779d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'f1779d' );setColorPanel('color_menufont');ClosePreview();return false;" title="桃70%">桃70%</a></li>
                                <li style="background-color : #f7b1c7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_menufont', 'f7b1c7' );setColorPanel('color_menufont');ClosePreview();return false;" title="桃40%">桃40%</a></li>
                            </ul>
                        </dd>
                    </dl>

                    </div>
                    <!-- /color_setting_wrapper -->
				</td>
			</tr>
			<tr>
				<th>エラー表示</th>
				<td>
                	<div class="color_setting_wrapper">
					<div class="explain_item">エラー表示の文字色を設定します</div>
                    <div class="clearfix">
					<div class="setting_item">設定色：#<input name="color_error" id="color_error" type="text" value="{$color_error}" maxlength="6" style="ime-mode: inactive; margin-left: 5px;" onchange="ClosePreview();setColorPanel('color_error');" /></div>
					<div class="current_color">
						<div id="panel_color_error" class="current_setting" style="background-color : #{$color_error} ;">現在の設定カラー</div><!-- current_setting -->
						#<span id="code_color_error">{$color_error}</span>
					</div>
                    </div>
                    <a href="javascript:void(0)"  onclick="setDesignColor( 'color_error', 'ff3333' );setColorPanel('color_error');ClosePreview();return false;" class="set_default">初期設定値に戻す</a>
					<dl class="select_item clearfix">
                    	<dt>色見本：</dt>
                        <dd>
                            <ul class="color_sample">
                                <li style="background-color : #000000 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '000000' );setColorPanel('color_error');ClosePreview();return false;" title="黒">黒</a></li>
                                <li style="background-color : #ffffff ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ffffff' );setColorPanel('color_error');ClosePreview();return false;" title="白">白</a></li>
                                <li style="background-color : #4d4d4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '4d4d4d' );setColorPanel('color_error');ClosePreview();return false;" title="グレー70%">グレー70%</a></li>
                                <li style="background-color : #999999 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '999999' );setColorPanel('color_error');ClosePreview();return false;" title="グレー40%">グレー40%</a></li>
                                <li style="background-color : #cccccc ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'cccccc' );setColorPanel('color_error');ClosePreview();return false;" title="グレー20%">グレー20%</a></li>
                                <li style="background-color : #ff2323 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ff2323' );setColorPanel('color_error');ClosePreview();return false;" title="赤100%">赤100%</a></li>
                                <li style="background-color : #ff6565 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ff6565' );setColorPanel('color_error');ClosePreview();return false;" title="赤70%">赤70%</a></li>
                                <li style="background-color : #ffa7a7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ffa7a7' );setColorPanel('color_error');ClosePreview();return false;" title="赤40%">赤40%</a></li>
                                <li style="background-color : #ff5500 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ff5500' );setColorPanel('color_error');ClosePreview();return false;" title="橙100%">橙100%</a></li>
                                <li style="background-color : #ff884d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ff884d' );setColorPanel('color_error');ClosePreview();return false;" title="橙70%">橙70%</a></li>
                                <li style="background-color : #ffbb99 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ffbb99' );setColorPanel('color_error');ClosePreview();return false;" title="橙40%">橙40%</a></li>
                                <li style="background-color : #ffb400 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ffb400' );setColorPanel('color_error');ClosePreview();return false;" title="黄100%">黄100%</a></li>
                                <li style="background-color : #ffcb4d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ffcb4d' );setColorPanel('color_error');ClosePreview();return false;" title="黄70%">黄70%</a></li>
                                <li style="background-color : #ffe199 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'ffe199' );setColorPanel('color_error');ClosePreview();return false;" title="黄40%">黄40%</a></li>
                                <li style="background-color : #00aa32 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '00aa32' );setColorPanel('color_error');ClosePreview();return false;" title="緑100%">緑100%</a></li>
                                <li style="background-color : #4dc470 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '4dc470' );setColorPanel('color_error');ClosePreview();return false;" title="緑70%">緑70%</a></li>
                                <li style="background-color : #99ddad ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '99ddad' );setColorPanel('color_error');ClosePreview();return false;" title="緑40%">緑40%</a></li>
                                <li style="background-color : #2ca6e0 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '2ca6e0' );setColorPanel('color_error');ClosePreview();return false;" title="薄青100%">薄青100%</a></li>
                                <li style="background-color : #6cc1e9 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '6cc1e9' );setColorPanel('color_error');ClosePreview();return false;" title="薄青70%">薄青70%</a></li>
                                <li style="background-color : #abdbf3 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'abdbf3' );setColorPanel('color_error');ClosePreview();return false;" title="薄青40%">薄青40%</a></li>
                                <li style="background-color : #233287 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '233287' );setColorPanel('color_error');ClosePreview();return false;" title="青100%">青100%</a></li>
                                <li style="background-color : #6570ab ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '6570ab' );setColorPanel('color_error');ClosePreview();return false;" title="青70%">青70%</a></li>
                                <li style="background-color : #a7adcf ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'a7adcf' );setColorPanel('color_error');ClosePreview();return false;" title="青40%">青40%</a></li>
                                <li style="background-color : #731e82 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '731e82' );setColorPanel('color_error');ClosePreview();return false;" title="紫100%">紫100%</a></li>
                                <li style="background-color : #9d62a8 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', '9d62a8' );setColorPanel('color_error');ClosePreview();return false;" title="紫70%">紫70%</a></li>
                                <li style="background-color : #c7a5cd ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'c7a5cd' );setColorPanel('color_error');ClosePreview();return false;" title="紫40%">紫40%</a></li>
                                <li style="background-color : #eb3c73 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'eb3c73' );setColorPanel('color_error');ClosePreview();return false;" title="桃100%">桃100%</a></li>
                                <li style="background-color : #f1779d ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'f1779d' );setColorPanel('color_error');ClosePreview();return false;" title="桃70%">桃70%</a></li>
                                <li style="background-color : #f7b1c7 ;"><a href="javascript:void(0)" onclick="setDesignColor( 'color_error', 'f7b1c7' );setColorPanel('color_error');ClosePreview();return false;" title="桃40%">桃40%</a></li>
                            </ul>
                        </dd>
                    </dl>

                    </div>
                    <!-- /color_setting_wrapper -->
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align : right ;">
					<input type="button" value="リセット" onclick="allDesignReset();" />
				</td>
			</tr>
			</table>

		</div>
        </div>
        
        
        <div class="section">
		<h2 class="section__ttl">メッセージの設定</h2>

		<div class="section__body">
			<p>メールフォームに表示するメッセージの設定を行うことができます。</p>

			<div class="red_txt">{$form_txt_error}</div>

			<table class="table" border="1" summary="文言の編集を行うフォーム">
			<tr>
				<th>入力画面</th>
				<td>
					<textarea name="txt_explain" cols="80" rows="6" style="ime-mode: active" onchange="ClosePreview();">{$txt_explain}</textarea>
				</td>
			</tr>
			<tr>
				<th>確認画面</th>
				<td>
					<textarea name="txt_confirm" cols="80" rows="6" style="ime-mode: active" onchange="ClosePreview();">{$txt_confirm}</textarea>
				</td>
			</tr>
			<tr>
				<th>完了画面</th>
				<td>
					<textarea name="txt_exit" cols="80" rows="6" style="ime-mode: active" onchange="ClosePreview();">{$txt_exit}</textarea>
				</td>
			</tr>
			</table>
		</div>
        </div>

		<div class="mailformSetting__fix">
            <p> 「サイズの設定」、「配色の設定」または「メッセージの設定」の変更を行う場合は、「<strong>設定を保存する(確認)</strong>」ボタンを押してください。</p>
            <input type="hidden" name="nowpage" value="design_page" />
            <input type="button" name="reset_button" value="リセット" onclick="document.location = document.location.href" />
			<input type="submit" name="submit_button" value="設定を保存する(確認)" />
			<input type="button" name="preview_button" value="プレビュー" onclick="OpenPreview( this.form, '' );" />
        </div>

	</div>
<!-- ========== ↑ここまで「定型スキン」 ================================================== -->
<!-- ========== ↓ここから「自作スキン」 ================================================== -->
	<div id="original_design" {$set_original_design}>
        <div class="section">
		<h2 class="section__ttl">自作デザインの設定</h2>
 
		<div class="section__body">
			<p>cssファイルやhtmlファイルを直接修正することができます。<br />
			利用されるサイトにあわせてメールフォームを自由にカスタマイズできます。
			</p>
 
			<div class="red_txt">{$form_template_error}</div>

			<div id="sub_navi" class="clearfix">
				<ul>
					<li id="sub_navi_template_input"><div id="sub_div_template_input" {$change_bold_input} {$template_div_0}><a href="javascript:void(0);" onclick="ChangeTemplate('input');">入力画面テンプレート</a></div></li>
					<li id="sub_navi_template_conf"><div id="sub_div_template_conf" {$change_bold_conf} {$template_div_1}><a href="javascript:void(0);" onclick="ChangeTemplate('conf');">確認画面テンプレート</a></div></li>
					<li id="sub_navi_template_exit"><div id="sub_div_template_exit" {$change_bold_exit} {$template_div_2}><a href="javascript:void(0);" onclick="ChangeTemplate('exit');">完了画面テンプレート</a></div></li>
					<li id="sub_navi_template_css"><div id="sub_div_template_css" {$change_bold_css} {$template_div_3}><a href="javascript:void(0);" onclick="ChangeTemplate('css');">ＣＳＳ</a></div></li>
				</ul>
			</div>

			<div id="table_template_input" {$template_table_0}>
                <div class="clearfix">
				<div class="input_textarea">
					<textarea name="template_input" cols="92" rows="30" style="ime-mode: active;" onchange="ClosePreview();BoldChangeTemplate( 'input', true );">{$template_input}</textarea>
					<input type="hidden" name="change_template_input" value="{$change_template_input}" />
				</div>
				<!-- /input_textarea -->

				<div class="side_menu">
					<div class="operation_button">
                        <input type="submit" name="submit_button" value="リセット" onclick="return IsReset('「入力画面テンプレート」')" />
						<input type="submit" name="submit_button" value="保存する" onchange="BoldChangeTemplate( 'input', false );" />
					</div>
					<div class="preview_button">
						<input type="button" name="preview_button" value="プレビュー(入力画面)" onclick="OpenPreview( this.form, '' );" />
					</div>
					<!-- /preview_button -->
				</div>
				<!-- /side_menu -->
                </div>
				<p class="indent_ajust">「css」を適用する場合は「&lt;link type="text/css" rel="stylesheet" href="./css/form.css?{$update_time}" /&gt;」を指定してください。</p>

				<p class="indent_ajust">
					「{$文字列}」の箇所はシステムが自動変換するタグです。<br />
					タグの詳細は下記を参照して、必須のタグは必ず入力してください。
				</p>
				<table class="table">
					<tr>
						<th>変数名</th>
						<th>必須／任意</th>
						<th>説明</th>
					</tr>
					<tr>
						<td>{$form_data}</td>
						<td><span class="red_txt">(必須)</span></td>
						<td>フォームの入力情報を出力します</td>
					</tr>
					<tr>
						<td>{$form_error}</td>
						<td><span class="red_txt">(必須)</span></td>
						<td>フォームのエラー情報を出力します</td>
					</tr>
					<tr>
						<td>{$basic_pagetitle}</td>
						<td>(任意)</td>
						<td>フォームのページタイトルを出力します</td>
					</tr>
					<tr>
						<td>{$basic_formname}</td>
						<td>(任意)</td>
						<td>フォームの名前を出力します</td>
					</tr>
					<tr>
						<td>{$basic_moveurl}</td>
						<td>(任意)</td>
						<td>フォームの戻り先ＵＲＬを出力します</td>
					</tr>
				</table>
			</div>
			<div id="table_template_conf" {$template_table_1}>
                <div class="clearfix">
				<div class="input_textarea">
					<textarea name="template_conf" cols="92" rows="30" style="ime-mode: active;" onchange="ClosePreview();BoldChangeTemplate( 'conf', true );">{$template_conf}</textarea>
					<input type="hidden" name="change_template_conf" value="{$change_template_conf}" />
				</div>
				<!-- /input_textarea -->

				<div class="side_menu">
					<div class="operation_button">
                        <input type="submit" name="submit_button" value="リセット" onclick="return IsReset('「確認画面テンプレート」')" />
						<input type="submit" name="submit_button" value="保存する" onchange="BoldChangeTemplate( 'conf', false );" />
					</div>
					<div class="preview_button">
						<input type="button" name="preview_button" value="プレビュー(確認画面)" onclick="OpenPreview( this.form, 'conf' );" />
					</div>
					<!-- /preview_button -->
				</div>
				<!-- /side_menu -->
                </div>
				<p class="indent_ajust">「css」を適用する場合は「&lt;link type="text/css" rel="stylesheet" href="./css/form.css?{$update_time}" /&gt;」を指定してください。</p>

				<p class="indent_ajust">
					「{$文字列}」の箇所はシステムが自動変換するタグです。<br />
					タグの詳細は下記を参照して、必須のタグは必ず入力してください。
				</p>
				<table class="table">
					<tr>
						<th>変数名</th>
						<th>必須／任意</th>
						<th>説明</th>
					</tr>
					<tr>
						<td>{$form_data}</td>
						<td><span class="red_txt">(必須)</span></td>
						<td>フォームの入力情報を出力します</td>
					</tr>
					<tr>
						<td>{$basic_pagetitle}</td>
						<td>(任意)</td>
						<td>フォームのページタイトルを出力します</td>
					</tr>
					<tr>
						<td>{$basic_formname}</td>
						<td>(任意)</td>
						<td>フォームの名前を出力します</td>
					</tr>
					<tr>
						<td>{$basic_moveurl}</td>
						<td>(任意)</td>
						<td>フォームの戻り先ＵＲＬを出力します</td>
					</tr>
				</table>

			</div>
			<div id="table_template_exit" {$template_table_2}>
                <div class="clearfix">
				<div class="input_textarea">
					<textarea name="template_exit" cols="92" rows="30" style="ime-mode: active;" onchange="ClosePreview();BoldChangeTemplate( 'exit', true );">{$template_exit}</textarea>
					<input type="hidden" name="change_template_exit" value="{$change_template_exit}" />
				</div>
				<!-- /input_textarea -->

				<div class="side_menu">
					<div class="operation_button">
                        <input type="submit" name="submit_button" value="リセット" onclick="return IsReset('「完了画面テンプレート」')" />
						<input type="submit" name="submit_button" value="保存する" onchange="BoldChangeTemplate( 'css', false );" />
					</div>
					<div class="preview_button">
						<input type="button" name="preview_button" value="プレビュー(完了画面)" onclick="OpenPreview( this.form, 'exit' );" />
					</div>
					<!-- /preview_button -->
				</div>
				<!-- /side_menu -->
                </div>
				<p class="indent_ajust">「css」を適用する場合は「&lt;link type="text/css" rel="stylesheet" href="./css/form.css?{$update_time}" /&gt;」を指定してください。</p>

				<p class="indent_ajust">
					「{$文字列}」の箇所はシステムが自動変換するタグです。<br />
					タグの詳細は下記を参照して、必須のタグは必ず入力してください。
				</p>
				<table class="table">
					<tr>
						<th>変数名</th>
						<th>必須／任意</th>
						<th>説明</th>
					</tr>
					<tr>
						<td>{$basic_pagetitle}</td>
						<td>(任意)</td>
						<td>フォームのページタイトルを出力します</td>
					</tr>
					<tr>
						<td>{$basic_formname}</td>
						<td>(任意)</td>
						<td>フォームの名前を出力します</td>
					</tr>
					<tr>
						<td>{$basic_moveurl}</td>
						<td>(任意)</td>
						<td>フォームの戻り先ＵＲＬを出力します</td>
					</tr>
				</table>

			</div>

			<div id="table_template_css" {$template_table_3}>
                <div class="clearfix">
				<div class="input_textarea">
					<textarea name="template_css" cols="92" rows="30" style="ime-mode: active;" onchange="ClosePreview();BoldChangeTemplate( 'css', true );">{$template_css}</textarea>
					<input type="hidden" name="change_template_css" value="{$change_template_css}" />
				</div>
				<!-- /input_textarea -->

				<div class="side_menu">
					<div class="operation_button">
                        <input type="submit" name="submit_button" value="リセット" onclick="return IsReset('「ＣＳＳ」')" />
						<input type="submit" name="submit_button" value="保存する" onchange="BoldChangeTemplate( 'css', false );" />
					</div>
					<div class="preview_button">
						<input type="button" name="preview_button" value="プレビュー(入力画面)" onclick="OpenPreview( this.form, '' );" />
						<input type="button" name="preview_button" value="プレビュー(確認画面)" onclick="OpenPreview( this.form, 'conf' );" />
						<input type="button" name="preview_button" value="プレビュー(完了画面)" onclick="OpenPreview( this.form, 'exit' );" />
					</div>
					<!-- /preview_button -->
				</div>
				<!-- /side_menu -->
                </div>

			</div>
		</div>

        </div>
		<div class="button_box">
			<input type="hidden" name="nowpage" value="design_page" />
			<input type="hidden" name="nowhtml" value="{$nowhtml}" />
		</div>
		<!-- /button_box -->

		<!-- /button_box -->

	</div>
<!-- ========== ↑ここまで「自作スキン」 ================================================== -->

        
    </div>
    <!-- /#main -->
    </form>
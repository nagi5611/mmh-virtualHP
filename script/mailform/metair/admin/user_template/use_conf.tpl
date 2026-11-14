<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>{$basic_pagetitle}</title>
<link rel="stylesheet" type="text/css" href="css/form.css?{$update_time}" />
</head>

<body>
<div id="wrapper">

    <div id="main">
		<form method="post" action="./">
             <div class="section">
				<h2 class="section__ttl">{$basic_formname}(確認)</h2>
				<div class="section__body">
					<div id="txt_explain">
					<p>記入事項をご確認の上、問題がなければ「送信」をクリックしてください。</p>
					</div>

					<table class="table" cellspacing="0">
						{$form_data}
					</table>

					<div class="button_box">
						<input type="submit" name="submit_button" value="送信">
    					<input type="submit" name="submit_button" value="戻る">
					</div>

			</div>

		</div>
		
		
		</form>

		<div class="button_box">
			[<a href="{$basic_moveurl}">サイトへ戻る</a>]
		</div>

	</div>

</div>
 
</body>
</html> 
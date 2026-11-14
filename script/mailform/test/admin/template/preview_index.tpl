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
				<h2 class="section__ttl">{$basic_formname}</h2>
				<div class="section__body">
					<div id="txt_explain">
					<p>{$html_txt_explain}</p>
					</div>

					<div class="red_txt">
					</div>

					<table class="table" cellspacing="0">
						{$form_data}
					</table>

					<div class="button_box">
						<input type="button" name="submit_button" value="確認">
					</div>

			</div>

		</div>
		
		
		</form>

		<div class="button_box">
			[<a href="#">サイトへ戻る</a>]
		</div>

	</div>

</div>
 
</body>
</html> 


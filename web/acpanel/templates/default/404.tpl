<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
	<title>@@title@@{if $site_name} &#9679; {$site_name|htmlspecialchars}{/if}</title>
	<link href="acpanel/templates/{$tpl}/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div class="wrapper">
		<div class="block small center login">
			<div class="block_head">
				<div class="bheadl"></div>
				<div class="bheadr"></div>
				<h2>@@head@@</h2>
			</div>
			<div class="block_content">
				<p>
					@@error_text@@
				</p>
				<p>
					@@you_can@@ <a href="{$home}">@@home_link@@</a> | <a href="{$home}?do=login">@@login_link@@</a>
				</p>
			</div>
			<div class="bendl"></div>
			<div class="bendr"></div>
		</div>
	</div>
</body>
</html>
{literal}
<style type="text/css">
	#facebox .phpinfo td, #facebox .phpinfo th, #facebox .phpinfo h1, #facebox .phpinfo h2 {font-family: sans-serif;}
	#facebox .phpinfo pre {margin: 0px; font-family: monospace;}
	#facebox .phpinfo a {color: black;}
	#facebox .phpinfo a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
	#facebox .phpinfo a:hover {text-decoration: underline;}
	#facebox .phpinfo table {border-collapse: collapse;}
	#facebox .phpinfo {text-align: center; font-size: 16px;}
	#facebox .phpinfo table { margin-left: auto; margin-right: auto; text-align: left;}
	#facebox .phpinfo th { text-align: center !important; }
	#facebox .phpinfo td, #facebox .phpinfo th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
	#facebox .phpinfo h1 {font-size: 150%;}
	#facebox .phpinfo h2 {font-size: 125%;}
	#facebox .phpinfo .p {text-align: left;}
	#facebox .phpinfo .e {padding: 2px;background-color: #ccccff; font-weight: bold; color: #000000;}
	#facebox .phpinfo .h {background-color: #9999cc; font-weight: bold; color: #000000;}
	#facebox .phpinfo .v {padding: 2px;background-color: #cccccc; color: #000000;}
	#facebox .phpinfo .vr {background-color: #cccccc; text-align: right; color: #000000;}
	#facebox .phpinfo img {float: right; border: 0px;}
	#facebox .phpinfo hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
</style>
{/literal} 
<div style="width: 100%;">
	<h2 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@php_settings@@</h2>
	<div class="phpinfo">
		{$result}
	</div>
</div>
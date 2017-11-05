<?php
$result = $db->Query("SELECT * FROM `acp_category` WHERE parentid = '{do}'", array('do'=>$_GET['do']), $config['sql_debug']);

if( is_array($result) )
{
	foreach ($result as $obj)
	{
		$array_opt[] = (array)$obj;
	}

	$smarty->assign("array_opt",$array_opt);
}

$headinclude = "
	<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$('a[rel*=facebox]').facebox()
			});
		})(jQuery);
	</script>
";

if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("head_title","@@head_optimization@@");

?>
<?php

	$result_group = $db->Query("SELECT usergroupid, usergroupname FROM `acp_usergroups` WHERE usergroupid IS NOT NULL", array(), $config['sql_debug']);
	if( is_array($result_group) )
	{
		foreach ($result_group as $obj)
		{
			$array_groups[] = (array)$obj;
		}

		$smarty->assign("array_groups", $array_groups);
	}

	$result_tz = $db->Query("SELECT type, options FROM `acp_config` WHERE varname = 'timezone' LIMIT 1", array(), $config['sql_debug']);
	if (is_array($result_tz))
	{
		foreach ($result_tz as $obj)
		{
			$box = explode("\n", $obj->options);
			foreach($box as $b) {
				$box_value = explode("|", $b);
				$array_tz[$box_value[0]] = $box_value[1];
			}
		}

		$smarty->assign("array_tz", $array_tz);
	}

	unset($cat_users_list);
	foreach ($all_categories as $key => $value)
	{
		$search = array_search("p_users", $value);
		if ($search)
		{			$cat_users_list = $key;
			break;
		}
	}
	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".$cat_users_list;

	$smarty->assign("action_uri", $action_uri);
	$smarty->assign("head_title","@@add_user@@");

	$headinclude = "
		<link href='acpanel/templates/".$config['template']."/css/date_input.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='acpanel/scripts/js/jquery.date_input.js'></script>
		<script type='text/javascript'>
			(function ($) {
				$(function () {
					// Date picker
					$('input.date_picker').date_input();
				});
			})(jQuery);
		</script>
	";

	$smarty->assign("cfg_timezone",$config['timezone']);
	if(isset($error)) $smarty->assign("iserror",$error);

?>
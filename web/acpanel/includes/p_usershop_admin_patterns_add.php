<?php

	$result_groups = $db->Query("SELECT gid, name FROM `acp_payment_groups`", array(), $config['sql_debug']);
	
	if( is_array($result_groups) )
	{
		foreach( $result_groups as $obj )
		{
			$array_groups[$obj->gid] = $obj->name;
		}
	}

	$result_usergroups = $db->Query("SELECT usergroupid, usergroupname FROM `acp_usergroups`", array(), $config['sql_debug']);
	
	if( is_array($result_usergroups) )
	{
		foreach( $result_usergroups as $obj )
		{
			$array_usergroups[$obj->usergroupid] = $obj->usergroupname;
		}
	}

	$result_servers = $db->Query("SELECT id, hostname, address FROM `acp_servers`", array(), $config['sql_debug']);
	
	if( is_array($result_servers) )
	{
		foreach( $result_servers as $obj )
		{
			$array_servers[$obj->id] = array('hostname' => $obj->hostname, 'address' => $obj->address);
		}
	}

	unset($cat_patterns_list);
	foreach( $all_categories as $key => $value )
	{
		$search_patterns_list = array_search("p_usershop_admin_patterns", $value);
		if( $search_patterns_list )
		{
			$cat_patterns_list = $key;
			break;
		}
	}

	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".$cat_patterns_list;
	$smarty->assign("action_uri", $action_uri);

	$headinclude = "
		<link href='acpanel/templates/".$config['template']."/css/usershop.css' rel='stylesheet' type='text/css' />
	";

	if(isset($error)) $smarty->assign("iserror",$error);
	if(isset($array_usergroups)) $smarty->assign("array_usergroups",$array_usergroups);
	if(isset($array_groups)) $smarty->assign("array_groups",$array_groups);
	if(isset($array_servers)) $smarty->assign("array_servers",$array_servers);

?>
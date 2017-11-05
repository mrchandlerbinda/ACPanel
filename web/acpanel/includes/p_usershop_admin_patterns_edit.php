<?php

$headinclude = "
	<link href='acpanel/templates/".$config['template']."/css/usershop.css' rel='stylesheet' type='text/css' />
";

if( isset($_GET['id']) && is_numeric($_GET['id']) )
{
	$id = $_GET['id'];
	$result_pattern = $db->Query("SELECT a.id, a.name, a.description, a.price_mm, a.price_points, a.duration_type, a.active, a.item_duration, a.item_duration_select, 
		a.max_sale_items, a.max_sale_items_duration, a.max_sale_for_user, a.max_sale_for_user_duration, a.new_usergroup_id, a.enable_server_select, 
		a.add_flags, a.add_points, a.do_php_exec, b.gid AS mygroup, GROUP_CONCAT(c.server_id SEPARATOR ',') AS servers_access, GROUP_CONCAT(d.usergroup_id SEPARATOR ',') AS usergroups_access 
		FROM `acp_payment_patterns` a
		LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
		LEFT JOIN `acp_payment_patterns_server` c ON c.pattern_id = a.id
		LEFT JOIN `acp_payment_patterns_usergroups` d ON d.pattern_id = a.id
		WHERE a.id = ".$id." GROUP BY a.id", array(), $config['sql_debug']);

	if( is_array($result_pattern) )
	{
		foreach( $result_pattern as $pat )
		{
			if( is_null($pat->mygroup) ) $pat->mygroup = 0;
			if( is_null($pat->servers_access) ) $pat->servers_access = array();
			else $pat->servers_access = explode(",", $pat->servers_access);
			if( is_null($pat->usergroups_access) ) $pat->usergroups_access = array();
			else $pat->usergroups_access = explode(",", $pat->usergroups_access);
			if( $pat->duration_type == "date" )
			{
				$pat->item_duration_date = ($pat->item_duration > 0) ? get_datetime($pat->item_duration, 'd-m-Y, H:i') : "";
				$pat->item_duration = "";
			}
			else
			{
				$pat->item_duration_date = "";
			}

			$pattern = (array)$pat;

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
		}
	}
	else $error = "@@not_payment_pattern@@";

	if(isset($array_usergroups)) $smarty->assign("array_usergroups", $array_usergroups);
	if(isset($array_groups)) $smarty->assign("array_groups", $array_groups);
	if(isset($array_servers)) $smarty->assign("array_servers", $array_servers);
	if(isset($pattern)) $smarty->assign("pattern", $pattern);
}
else $error = "@@not_payment_pattern@@";

if(isset($error)) $smarty->assign("iserror", $error);

?>
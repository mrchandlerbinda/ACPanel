<?php

	$result_servers = $db->Query("SELECT id, hostname, address FROM `acp_servers`", array(), $config['sql_debug']);
	
	if( is_array($result_servers) )
	{
		foreach( $result_servers as $obj )
		{
			$array_servers[$obj->address] = $obj->hostname;
		}
	}

	$result_cats = $db->Query("SELECT categoryid, sectionid FROM `acp_category` WHERE link = 'p_users'", array(), $config['sql_debug']);
	
	if( is_array($result_cats) )
	{
		foreach( $result_cats as $obj )
		{
			$smarty->assign("cat_users", $obj->sectionid);
			$smarty->assign("cat_user_edit", $obj->categoryid);
		}
	}

	header('Content-type: text/html; charset='.$config['charset']);

	$bid = trim($_GET['id']);
	if( !is_numeric($bid) ) die("Hacking Attempt");

	$arguments = array('bid'=>$bid);
	$result = $db->Query("SELECT * FROM (
			(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, player_id, ban_reason, ban_length, admin_nick, admin_ip, admin_id, admin_uid, server_ip, server_name, ban_type, unban_created, unban_reason, unban_admin_uid FROM `acp_bans_history`)
			UNION ALL
			(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, player_id, ban_reason, ban_length, admin_nick, admin_ip, admin_id, admin_uid, server_ip, server_name, ban_type, NULL, NULL, NULL FROM `acp_bans`)
		) temp WHERE bid = '{bid}' LIMIT 1", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach ($result as $obj)
		{
			if( is_null($obj->unban_created) )
			{
				if( !$obj->ban_length )
				{
					$obj->ban_remain = "@@ban_permanently@@";
				}
				else
				{
					$obj->ban_remain = $obj->ban_length*60 + $obj->ban_created - time();
					$obj->ban_remain = ($obj->ban_remain <= 0) ? "@@ban_expired@@" : "@@ban_remain@@".compacttime($obj->ban_remain, "ddd hhh mmm sss");
				}
			}
			else
			{
				$obj->ban_remain = "@@ban_history@@";
			}
			$obj->ban_created = ($obj->ban_created > 0) ? get_datetime($obj->ban_created, 'd-m-Y, H:i') : 0;

			$array_ban = (array)$obj;
		}
	}

	$smarty->assign("ban_edit",$array_ban);
	if(isset($array_servers)) $smarty->assign("array_servers",$array_servers);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamebans_players_edit.tpl');

	exit;

?>
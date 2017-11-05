<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$result = false;

	$query = $db->Query("SELECT bid FROM `acp_bans` WHERE ".time()." > (ban_created+(ban_length*60)) AND ban_length > 0", array(), $config['sql_debug']);

	if( is_array($query) )
	{
		foreach( $query as $obj )
		{
			$ids[] = $obj->bid;
		}
	}
	else
	{
		$ids = ($query) ? array($query) : array();
	}

	if( !empty($ids) )
	{
		$arguments = array('ids' => $ids);
		$query = $db->Query("INSERT INTO `acp_bans_history` (bid, player_ip, player_id, player_nick, cookie_ip, admin_ip, admin_id, admin_nick, admin_uid, ban_type, ban_reason, ban_created, ban_length, server_ip, server_name, unban_created) 
			SELECT bid, player_ip, player_id, player_nick, cookie_ip, admin_ip, admin_id, admin_nick, admin_uid, ban_type, ban_reason, ban_created, ban_length, server_ip, server_name, UNIX_TIMESTAMP() 
			FROM `acp_bans` WHERE bid IN ('{ids}') 
			ON DUPLICATE KEY UPDATE player_ip = VALUES(player_ip), player_id = VALUES(player_id), player_nick = VALUES(player_nick), cookie_ip = VALUES(cookie_ip), admin_ip = VALUES(admin_ip), admin_id = VALUES(admin_ip), 
			admin_nick = VALUES(admin_nick), admin_uid = VALUES(admin_uid), ban_type = VALUES(ban_type), ban_reason = VALUES(ban_reason), ban_created = VALUES(ban_created), ban_length = VALUES(ban_length), 
			server_ip = VALUES(server_ip), server_name = VALUES(server_name)", $arguments, $config['sql_debug']);

		if( $query )
		{
			$query = $db->Query("DELETE FROM `acp_bans` WHERE bid IN ('{ids}')", $arguments, $config['sql_debug']);

			if( $query )
			{
				$result = "@@bans_prune_success@@ ".count($ids);
			}
			else
			{
				$result = "@@bans_prune_error@@";
				$error = true;
			}
		}
		else
		{
			$result = "@@bans_prune_error@@";
			$error = true;
		}
	}
	else
	{
		$result = "@@bans_prune_not_find@@";
		$error = true;
	}

	$smarty->assign("result", $result);
	if( isset($error) ) $smarty->assign("error", $error);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('optimization/bans_prune.tpl');

	exit;

?>
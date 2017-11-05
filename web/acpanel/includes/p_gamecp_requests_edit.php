<?php

	$id = trim($_GET['id']);
	$cat_where = "";

	if(!is_numeric($id))
	{
		$error = "@@not_ticket@@";
	}
	else
	{
		$langs = create_lang_list();
	
		unset($translate);
		$filter = "lp_name='p_gamecp.tpl' AND lp_id = lw_page";
		$arguments = array('lang'=>get_language(1));
		$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
		if(is_array($tr_result))
		{
			foreach ($tr_result as $obj)
			{
				$translate[$obj->lw_word] = $obj->lw_translate;
			}
		}

		$result = $db->Query("SELECT 
			acp_players_requests.id AS id,
			acp_players_requests.userid AS userid,
			acp_players_requests.fields_update AS fields_update,
			acp_players_requests.timestamp AS timestamp,
			acp_players_requests.ticket_status AS ticket_status,
			acp_players_requests.closed_time AS closed_time,
			acp_players_requests.closed_admin AS closed_admin,
			acp_players_requests.comment AS comment,
			acp_players_requests.ticket_type AS ticket_type,
			acp_players_requests.productid AS productid,
			acp_ticket_type.label AS label,
			acp_ticket_type.varname AS varname,
			acp_users.username AS username, 
			acp_users.ipaddress AS ipaddress,
			acp_users.hid AS hid 
			FROM `acp_players_requests` 
			LEFT JOIN `acp_ticket_type` ON acp_players_requests.ticket_type = acp_ticket_type.id 
			LEFT JOIN `acp_users` ON acp_users.uid = acp_players_requests.userid 
			WHERE acp_players_requests.id = ".$id." LIMIT 1", array(), $config['sql_debug']);

		if( is_array($result) )
		{
			foreach( $result as $obj )
			{
				if( !isset($start_type[$obj->productid]) )
					$start_type[$obj->productid] = $db->Query("SELECT id FROM `acp_ticket_type` WHERE productid = 'gameAccounts' ORDER BY id LIMIT 1", array(), $config['sql_debug']);

				$obj->ticket_type = $obj->ticket_type - $start_type[$obj->productid] + 1;
				$obj->fields_update = unserialize($obj->fields_update);
				$obj->elapsed = ($obj->closed_time > 0 && $obj->ticket_status) ? compacttime($obj->closed_time - $obj->timestamp, $config['ga_time_format']) : compacttime(time() - $obj->timestamp, $config['ga_time_format']);
				$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, $config['date_format']) : "-";
				$obj->label = str_replace("@@", "", $obj->label);
				$obj->ticket_type_head = ($var = $obj->varname) ? sprintf($translate[$obj->label].': %s', $obj->fields_update[$var]) : $translate[$obj->label];
				$ticket = (array)$obj;
			}

			$arguments = array('hid'=>$ticket['hid'], 'uid'=>$ticket['userid']);
			$result_hid = $db->Query("SELECT uid, username FROM `acp_users` WHERE hid = '{hid}' AND uid != {uid} AND hid IS NOT NULL", $arguments, $config['sql_debug']);
			if( is_array($result_hid) )
			{
				foreach( $result_hid as $obj )
				{
					$arrDuplicate[] = (array)$obj;
				}
			}
			else
			{
				$arrDuplicate = array();
			}

			$productID = getProduct("gameBans");
			if( !empty($productID) && $ticket['productid'] == "gameAccounts" )
			{
				$smarty->assign("acp_bans", true);
				$cat_where .= " OR link = 'p_gamebans_search'";

				$ticket['player_nick'] = "";
				$ticket['player_ip'] = $ticket['ipaddress'];
				$ticket['player_steam'] = "";

				$result_player = $db->Query("SELECT userid, player_nick, player_ip, steamid FROM `acp_players` WHERE userid = ".$ticket['userid'], array(), $config['sql_debug']);
				if( is_array($result_player) )
				{
					foreach( $result_player as $obj )
					{
						$ticket['player_nick'] = $obj->player_nick;
						if( $ticket['player_ip'] )
							$ticket['player_ip'] = $obj->player_ip;
						$ticket['player_steam'] = $obj->steamid;
					}
				}
			}

			$smarty->assign("ticket", array_merge($ticket, array('duplicate'=>$arrDuplicate)));
		}
		else
		{
			$error = "@@not_ticket@@";
		}
	}

	$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_users'".$cat_where, array(), $config['sql_debug']);
	
	if( is_array($result_cats) )
	{
		foreach ($result_cats as $obj)
		{
			if( $obj->link == 'p_users' )
			{
				$smarty->assign("cat_users", $obj->sectionid);
				$smarty->assign("cat_user_edit", $obj->categoryid);
			}
			else
				$smarty->assign("cat_bans_search", array('cat' => $obj->sectionid, 'do' => $obj->categoryid));
		}
	}

	if(isset($error)) $smarty->assign("iserror",$error);

	header('Content-type: text/html; charset='.$config['charset']);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamecp_requests_edit.tpl');

	exit;

?>
<?php

if (!defined('IN_ACP')) die("Hacking attempt!");

function ticket_approve($obj, $arguments = array(), &$error)
{
	global $db, $config, $translate;

	$fields_update = unserialize($obj['fields_update']);

	if( is_array($fields_update) && $obj['ticket_type'] > 0 )
	{
		switch($obj['productid'])
		{
			case "gameAccounts":

				switch($obj['ticket_type'])
				{
					case 1:
					case 2:
					case 3:
		
						$arrKeys = array('userid', 'flag', 'timestamp');
						$arrValues = array($obj['userid'], $fields_update['flag'], $arguments['closed_time']);
		
						if( $fields_update['flag'] == 1 )
						{
							$arrKeys[] = "player_nick";
							$arrValues[] = mysql_real_escape_string($fields_update['player_nick']);
							$arrKeys[] = "password";
							$arrValues[] = $fields_update['password'];
						}
						elseif( $fields_update['flag'] == 2 )
						{
							$arrKeys[] = "player_ip";
							$arrValues[] = $fields_update['player_ip'];
						}
						else
						{
							$arrKeys[] = "steamid";
							$arrValues[] = $fields_update['steamid'];
						}
		
						$result = $db->Query("INSERT INTO `acp_players` (".implode(',',$arrKeys).") VALUES ('".implode('\',\'',$arrValues)."')", array(), $config['sql_debug']);
						$def_time = (!$config['default_access_time']) ? 0 : ($arguments['closed_time'] + ($config['default_access_time']*3600));
						$result_mask = $db->Query("INSERT INTO `acp_access_mask_players` (userid, mask_id, access_expired) VALUES ('".$obj['userid']."', '".$config['default_access']."', '{expired}')", array('expired' => $def_time), $config['sql_debug']);
		
						if( $result && $result_mask )
						{
							$result_update = $db->Query("UPDATE `acp_players_requests` SET ticket_status = 1, comment = '{comment}', closed_time = '{closed_time}', closed_admin = '{closed_admin}' WHERE id = ".$obj['id'], $arguments, $config['sql_debug']);
		
							if( !$result_update )
							{
								$error[] = $translate['error_ticket_update'].$obj['id'];
							}
						}
						else
						{
							$error[] = $translate['error_account_created'].$obj['userid'];
						}
		
						break;
		
					case 4:
					case 5:
					case 6:
		
						$sqlconds = "";
		
						if( $fields_update['flag'] == 1 )
						{
							$sqlconds = "player_nick = '".mysql_real_escape_string($fields_update['player_nick'])."'";
		
							if( isset($fields_update['password']) && strlen($fields_update['password']) )
							{
								$sqlconds .= ", password = '".$fields_update['password']."'";
							}
						}
						elseif( $fields_update['flag'] == 2 )
						{
							$sqlconds = "player_ip = '".$fields_update['player_ip']."'";
						}
						else
						{
							$sqlconds = "steamid = '".$fields_update['steamid']."'";
						}
		
						$result = $db->Query("UPDATE `acp_players` SET ".$sqlconds." WHERE userid = ".$obj['userid'], array(), $config['sql_debug']);
		
						if( $result )
						{
							$result_update = $db->Query("UPDATE `acp_players_requests` SET ticket_status = 1, comment = '{comment}', closed_time = '{closed_time}', closed_admin = '{closed_admin}' WHERE id = ".$obj['id'], $arguments, $config['sql_debug']);
		
							if( !$result_update )
							{
								$error[] = $translate['error_ticket_update'].$obj['id'];
							}
						}
						else
						{
							$error[] = $translate['error_account_update'].$obj['userid'];
						}
		
						break;
		
					case 7:
					case 8:
					case 9:
		
						$sqlconds = "flag = ".$fields_update['flag'];
		
						if( $fields_update['flag'] == 1 )
						{
							$sqlconds .= ", player_nick = '".mysql_real_escape_string($fields_update['player_nick'])."'";
							$sqlconds .= ", password = '".$fields_update['password']."'";
						}
						elseif( $fields_update['flag'] == 2 )
						{
							$sqlconds .= ", player_ip = '".$fields_update['player_ip']."'";
						}
						else
						{
							$sqlconds .= ", steamid = '".$fields_update['steamid']."'";
						}
		
						$result = $db->Query("UPDATE `acp_players` SET ".$sqlconds." WHERE userid = ".$obj['userid'], array(), $config['sql_debug']);
		
						if( $result )
						{
							$result_update = $db->Query("UPDATE `acp_players_requests` SET ticket_status = 1, comment = '{comment}', closed_time = '{closed_time}', closed_admin = '{closed_admin}' WHERE id = ".$obj['id'], $arguments, $config['sql_debug']);
		
							if( !$result_update )
							{
								$error[] = $translate['error_ticket_update'].$obj['id'];
							}
						}
						else
						{
							$error[] = $translate['error_account_update'].$obj['userid'];
						}
		
						break;
				}

				break;

			case "ratingServers":


				break;

			default:

				$error[] = $translate['error_not_product'].$obj['id'];
				break;
		}
	}
	else
	{
		$error[] = $translate['error_unserialize_ticket_type'].$obj['id'];
	}
}

?>
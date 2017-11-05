<?php

header('Content-type: text/html; charset='.$config['charset']);

if( isset($_GET['id']) && is_numeric($_GET['id']) )
{
	if( !$userinfo['uid'] )
	{
		$error = "@@payment_only_registered@@ <a href='".$config['acpanel'].".php?do=login' rel='nofollow'>@@user_login@@</a> | <a href='".$config['acpanel'].".php?do=register' rel='nofollow'>@@user_register@@</a>";
	}
	else
	{
		$id = $_GET['id'];

		$result_pattern = $db->Query("SELECT a.id, a.name, a.description, a.price_mm, a.price_points, a.duration_type, a.active, a.item_duration, a.item_duration_select, 
			a.max_sale_items, a.max_sale_items_duration, a.max_sale_for_user, a.max_sale_for_user_duration, a.new_usergroup_id, a.enable_server_select, a.purchased, 
			a.add_flags, a.add_points, a.do_php_exec, b.gid AS mygroup, GROUP_CONCAT(c.server_id SEPARATOR ',') AS servers_access, GROUP_CONCAT(d.usergroup_id SEPARATOR ',') AS usergroups_access 
			FROM `acp_payment_patterns` a
			LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
			LEFT JOIN `acp_payment_patterns_server` c ON c.pattern_id = a.id
			LEFT JOIN `acp_payment_patterns_usergroups` d ON d.pattern_id = a.id
			WHERE a.id = ".$id." AND a.active = 1 AND (a.duration_type != 'date' OR a.item_duration > UNIX_TIMESTAMP() OR a.item_duration = 0) AND (a.max_sale_items_duration != 'total' OR a.max_sale_items = 0 OR (a.max_sale_items_duration = 'total' AND a.max_sale_items > a.purchased)) GROUP BY a.id", array(), $config['sql_debug']);
	
		if( is_array($result_pattern) )
		{
			$user = array('uid' => $userinfo['uid'], 'money' => $userinfo['money'], 'points' => 0);
			date_default_timezone_set('UTC');
			include_once(INCLUDE_PATH . 'functions.payment.php');
	
			foreach( $result_pattern as $pat )
			{
				// check usergroup access
				if( is_null($pat->usergroups_access) ) $pat->usergroups_access = array();
				else $pat->usergroups_access = explode(",", $pat->usergroups_access);
				if( !in_array($userinfo['usergroupid'], $pat->usergroups_access) )
				{
					$error = "@@payment_usergroup_not_access@@";
					break;
				}
				
				// check server select list
				if( is_null($pat->servers_access) ) $pat->servers_access = array();
				else
				{
					$result_servers = $db->Query("SELECT id, hostname, address FROM `acp_servers` WHERE id IN(".$pat->servers_access.")", array(), $config['sql_debug']);
					$pat->servers_access = array();
					
					if( is_array($result_servers) )
					{
						foreach( $result_servers as $obj )
						{
							$pat->servers_access[$obj->id] = array('hostname' => $obj->hostname, 'address' => $obj->address);
						}
					}
				}
				if( $pat->enable_server_select && empty($pat->servers_access) )
				{
					$error = "@@payment_servers_list_empty@@";
					break;
				}

				// check price
				if( $pat->price_mm > $userinfo['money'] )
				{
					$error = "@@payment_price_not@@";
					break;
				}
				if( $pat->price_points > 0 )
				{
					$product_GA = getProduct("gameAccounts");
					if( !empty($product_GA) )
					{
						$result_player = $db->Query("SELECT userid, points FROM `acp_players` WHERE userid = ".$userinfo['uid'], array(), $config['sql_debug']);
	
						if( is_array($result_player) )
						{
							foreach( $result_player as $obj )
							{
								$user['points'] = $obj->points;
							}

							if( $pat->price_points > $user['points'] )
							{
								$error = "@@payment_price_not@@";
								break;
							}
						}
						else
						{
							$error = "@@not_game_account@@";
							break;
						}
					}
					else
					{
						$error = "@@plugin_ga_not_active@@";
						break;
					}
				}

				$time = time();

				// check limit time max items
				if( $pat->max_sale_items > 0 && $pat->max_sale_items_duration != "total" && $pat->purchased > 0 )
				{
					switch($pat->max_sale_items_duration)
					{
						case "month":
                                $purchased_time = 30*24*3600;
                                break;
 
                            case "week":
                                $purchased_time = 7*24*3600;
                                break;
 
                            case "day":
                                $purchased_time = 24*3600;
                                break;
					}
					$purchased_time = $time - $purchased_time - 1;
					$result_purchased = $db->Query("SELECT MIN(date_start) AS mintime, count(*) AS cnt FROM `acp_payment_user` WHERE date_start > ".$purchased_time." AND pattern_id = ".$id, array(), $config['sql_debug']);

					if( is_array($result_purchased) )
					{
						foreach( $result_purchased as $obj )
						{
							$count_purchased = $obj->cnt;
							$mintime = (is_null($obj->mintime)) ? 0 : ($obj->mintime - $purchased_time);
						}

						if( $count_purchased >= $pat->max_sale_items )
						{
							$error = "@@store_item_limit_time_purchased@@ ".compacttime($mintime, 'dddd hhhh mmmm ssss');
							break;
						}
					}
				}

				// check limit time max items for user
				if( $pat->max_sale_for_user > 0 && $pat->purchased > 0 )
				{
					$sqlconds = "";
					if( $pat->max_sale_for_user_duration != "total" )
					{
						switch($pat->max_sale_for_user_duration)
						{
							case "month":
								$purchased_time = $pat->max_sale_items*30*24*3600;
								break;
	
							case "week":
								$purchased_time = $pat->max_sale_items*7*24*3600;
								break;
	
							case "day":
								$purchased_time = $pat->max_sale_items*24*3600;
								break;
						}
						$purchased_time_user = $time - $purchased_time - 1;
						$sqlconds .= " AND date_start > ".$purchased_time_user;
					}

					$result_purchased = $db->Query("SELECT MIN(date_start) AS mintime, count(*) AS cnt FROM `acp_payment_user` WHERE uid = ".$userinfo['uid']." AND pattern_id = ".$id.$sqlconds, array(), $config['sql_debug']);

					if( is_array($result_purchased) )
					{
						foreach( $result_purchased as $obj )
						{
							$count_purchased = $obj->cnt;
							$mintime_user = (is_null($obj->mintime) || !isset($purchased_time_user)) ? 0 : ($obj->mintime - $purchased_time_user);
						}

						if( $count_purchased >= $pat->max_sale_for_user )
						{
							$error = ($mintime_user > 0) ? "@@store_item_for_user_limit_time_purchased@@ ".compacttime($mintime_user, 'dddd hhhh mmmm ssss') : "@@store_item_for_user_limit_purchased@@";
							break;
						}
					}
				}

				if( $pat->price_mm > 0 ) $pat->price_mm_info = $config['ub_currency_suffix'];
				if( $pat->price_points > 0 ) $pat->price_points_info = "@@points_suffix@@";

				if( !is_numeric($pat->item_duration) ) $pat->item_duration = 0;
				if( $pat->duration_type == "date" )
				{
					$pat->item_duration_select = 0;
					$pat->item_duration_info = ($pat->item_duration > 0) ? get_datetime($pat->item_duration, 'd-m-Y, H:i') : "@@sale_item_forever@@";
				}
				else
				{
					$array_durations = getArrayDurations($pat->item_duration, $pat->duration_type);
					$pat->item_duration_info = ($pat->item_duration > 0) ? get_correct_str($pat->item_duration, "@@time_".$pat->duration_type."_one@@", "@@time_".$pat->duration_type."_several@@", "@@time_".$pat->duration_type."_many@@") : "@@sale_item_forever@@";
				}
	
				$pattern = (array)$pat;
			}
		}
		else $error = "@@not_payment_pattern@@";
	}

	if(isset($array_durations)) $smarty->assign("array_durations", $array_durations);
	if(isset($pattern)) $smarty->assign("item", $pattern);
	if(isset($user)) $smarty->assign("user", $user);
}
else $error = "@@not_payment_pattern@@";

if(isset($error)) $smarty->assign("iserror", $error);

$smarty->registerFilter("output", "translate_template");
$smarty->display('p_usershop_buywindow.tpl');

exit;

?>
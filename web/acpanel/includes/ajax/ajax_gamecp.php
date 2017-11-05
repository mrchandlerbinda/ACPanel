<?php

if(!isset($_POST['go']))
{
	die("Hacking Attempt");
}
else
{
	require_once(INCLUDE_PATH . 'class.mysql.php');

	try {
		$db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
	} catch (Exception $e) {
		die($e->getMessage());
	}

	$array_cfg = $db->Query("SELECT varname, value FROM `acp_config` WHERE varname IS NOT NULL", array(), true);

	if(is_array($array_cfg))
	{
		foreach ($array_cfg as $obj)
		{
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();
	}

	include(INCLUDE_PATH . 'functions.main.php');
	$langs = create_lang_list();

	unset($translate);
	$filter = "lp_name='p_gamecp.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
	$arguments = array('lang'=>get_language(1));
	$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
	if(is_array($tr_result))
	{
		foreach ($tr_result as $obj)
		{
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}
	require_once(INCLUDE_PATH . '_auth.php');

	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create list
	// 2 - add item
	// 3 - del item
	// 4 - multilpy del items
	// 5 - edit item
	// 6 - multiply active items
	// 7 - multiply inactive items
	// 8 - username autocomplete
	// 9 - load user info
	// 10 - active/inactive account
	// 11 - list tickets for user
	// 12 - create list for mask
	// 13 - add mask
	// 14 - del mask
	// 15 - edit mask
	// 16 - withdraw ticket
	// 17 - load account info
	// 18 - edit user account
	// 19 - create ticket list
	// 20 - delete ticket
	// 21 - multiply delete ticket
	// 22 - multiply approve ticket
	// 23 - multiply disapprove ticket
	// 24 - load reg accounts stats
	// 25 - delete search accounts (don't show)
	// 26 - show result search accounts
	// 27 - find bans

	switch($_POST['go']) {

		case "1":

			$result_cats = $db->Query("SELECT categoryid, sectionid FROM `acp_category` WHERE link = 'p_users'", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach ($result_cats as $obj)
				{
					$cat_users = $obj->sectionid;
					$cat_user_edit = $obj->categoryid;
				}
			}

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$s = $_POST['s'];
			$t = $_POST['t'];

			$sqlconds = "WHERE 1 = 1";
			$arguments = array('offset'=>$offset,'limit'=>$limit);

			if( $s )
			{
				$approved = ($s == 1) ? "yes" : "no";
				$arguments['approved'] = $approved;
				$sqlconds .= " AND approved = '{approved}'";
			}
			
			if( $t )
			{
				$arguments['flag'] = $t;
				$sqlconds .= " AND flag = '{flag}'";
			}

			$result = $db->Query("SELECT 
				acp_players.userid AS userid,
				acp_players.flag AS flag,
				acp_players.player_nick AS player_nick,
				acp_players.password AS password,
				acp_players.player_ip AS player_ip,
				acp_players.steamid AS steamid,
				acp_players.timestamp AS timestamp,
				acp_players.last_time AS last_time,
				acp_players.approved AS approved,
				acp_players.online AS online,
				acp_players.points AS points,
				acp_users.username AS username, 
				(SELECT COUNT(u.hid) FROM `acp_users` u WHERE u.hid = acp_users.hid AND u.hid IS NOT NULL GROUP BY u.hid) AS cnt_hid
				FROM `acp_players` LEFT JOIN `acp_users` ON acp_users.uid = acp_players.userid ".$sqlconds." ORDER BY timestamp DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, $config['date_format']) : "-";
					$obj->last_time = ($obj->last_time > 0) ? get_datetime($obj->last_time, $config['date_format']) : "-";
					$accounts[] = (array)$obj;
				}
			}

			require_once("scripts/smarty/Smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($cat_users)) $smarty->assign("cat_users", $cat_users);
			if(isset($cat_user_edit)) $smarty->assign("cat_user_edit", $cat_user_edit);
			$smarty->assign("get_status",$s);
			$smarty->assign("get_type",$t);
			if(isset($accounts)) $smarty->assign("accounts",$accounts);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamecp_accounts_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				unset($_POST['go'],$array_keys,$mask_players, $mask_expired);
				$error = array();
	
				if( is_array($_POST) )
				{
					require_once(INCLUDE_PATH . 'class.Permissions.php');
					$permClass = new Permissions($db);
	
					foreach( $_POST as $var => $value )
					{
						if( strpos($var, "access_mask_") !== FALSE )
						{
							$mask_players[] = $value;
						}
						elseif( strpos($var, "access_expired_") !== FALSE )
						{
							$mask_expired[] = $value;
						}
						else
						{
							switch($var) 
							{
								case "username":
		
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
										$_POST['username'] = iconv('utf-8', $config['charset'], $_POST['username']);
									}
		
									$arguments = array('uname'=>$value);
						
									$check_user = $db->Query("SELECT acp_users.uid AS uid FROM `acp_users` 
										LEFT JOIN `acp_players` ON acp_users.uid = acp_players.userid 
										LEFT JOIN `acp_players_requests` ON acp_users.uid = acp_players_requests.userid 
										WHERE acp_users.username = '{uname}' AND acp_players.userid IS NULL 
										AND (acp_players_requests.userid IS NULL OR acp_players_requests.ticket_status > 0 OR (acp_players_requests.ticket_status = 0 AND acp_players_requests.productid != 'gameAccounts'))", $arguments, $config['sql_debug']);
						
									if( !$check_user )
									{
										$error[] = $translate['user_not_found'];
									}
		
									break;
		
								case "password":
		
									if( $value == '' )
									{
										$error[] = $translate['pass_not_empty'];
									}
									else
									{
										$value = md5($value);
									}
		
									break;
		
								case "player_ip":
		
									if( $value != '' )
									{
										if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value) )
										{
											$error[] = $translate['ip_not_valid'];
										}
									}
		
									break;
		
								default:
		
									break;
							}
	
							if( $var != 'username' )
								$array_keys[$var] = $value;
							else
								$array_keys['userid'] = $check_user;
						}
					}
	
					$array_keys['timestamp'] = time();
	
					if( !empty($error) )
					{
						if( count($error) > 1 )
						{
							$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
						}
						else
						{
							$error = $error[0];
						}
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$result = $db->Query("INSERT INTO `acp_players` (".implode(',',array_keys($array_keys)).") VALUES ('".implode('\',\'',array_map('mysql_real_escape_string', $array_keys))."')", array(), $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							$err = 0;
							date_default_timezone_set('UTC');
							$mask_unique = array_count_values($mask_players);
							foreach( $mask_players AS $k => $v )
							{
								if( $mask_unique[$v] > 1 )
								{
									$mask_unique[$v] = $mask_unique[$v] - 1;
									continue;
								}
	
								if( $mask_expired[$k] )
								{									
									$mask_expired[$k] = strtotime($mask_expired[$k]);
									$mask_expired[$k] = get_datetime($mask_expired[$k], false, true);
								}
								else
								{
									$mask_expired[$k] = 0;
								}
	
								$result_srv = $db->Query("INSERT INTO `acp_access_mask_players` (mask_id, userid, access_expired) VALUES ('".$v."', '".$check_user."', '{expired}')", array('expired' => $mask_expired[$k]), $config['sql_debug']);
	
								if( !$result_srv )
								{
									$err++;
								}
							}
	
							if( !$err )
							{
								if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "add account for user: ".$_POST['username']);
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
							}
							else
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_sync_failed'].'</span>';
							}
						}
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['empty_array'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "3":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_players` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$result_mask = $db->Query("DELETE FROM `acp_access_mask_players` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
					$result_tickets = $db->Query("DELETE FROM `acp_players_requests` WHERE userid = '{id}' AND productid = 'gameAccounts'", $arguments, $config['sql_debug']);
	
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "delete account for userid: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "4":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_players` WHERE userid IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$result_mask = $db->Query("DELETE FROM `acp_access_mask_players` WHERE userid IN ('{ids}')", $arguments, $config['sql_debug']);
					$result_tickets = $db->Query("DELETE FROM `acp_players_requests` WHERE userid IN ('{ids}') AND productid = 'gameAccounts'", $arguments, $config['sql_debug']);
	
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "multiple delete accounts: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "5":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['userid'];
	
				unset($_POST['go'], $_POST['userid']);
				$query_string = "";
				if( is_array($_POST) )
				{
					foreach( $_POST as $var => $value )
					{
						if( strpos($var, "access_mask_") !== FALSE )
						{
							$mask_players[] = $value;
						}
						elseif( strpos($var, "access_expired_") !== FALSE )
						{
							$mask_expired[] = $value;
						}
						else
						{
							switch($var)
							{
								case "password":
		
									$value = trim($value);
									if( $value == '' )
									{
										$arguments = array('userid'=>$id);				
										$check_auth = $db->Query("SELECT flag FROM `acp_players` WHERE userid = '{userid}'", $arguments, $config['sql_debug']);
							
										if( $check_auth != $_POST['flag'] )
										{
											$error[] = $translate['pass_not_empty'];	
										}
									}
									else
									{
										$value = md5($value);
									}
									break;
		
								case "player_nick":
		
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
									}
									break;
		
								case "player_ip":
		
									if( $value != '' )
									{
										if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value) )
										{
											$error[] = $translate['ip_not_valid'];
										}
									}
									break;
							}
	
							if( $var == 'password' &&  $value == '')
							{
								 continue;
							}
							else
							{
								$query_string .= $var." = '".mysql_real_escape_string($value)."',";
							}
						}
					}
	
					if (!empty($error))
					{
						if(count($error) > 1)
						{
							$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
						}
						else
						{
							$error = $error[0];
						}
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$query_string = substr($query_string, 0, strlen($query_string)-1);
						$arguments = array('id'=>$id);
						$result = $db->Query("UPDATE `acp_players` SET ".$query_string." WHERE userid = '{id}'", $arguments, $config['sql_debug']);
	
						if (!$result)
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
						}
						else
						{
							$err = 0;
							$curent_mask = array();
							$result_sync = $db->Query("SELECT mask_id, userid, access_expired FROM `acp_access_mask_players` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
		
							if( is_array($result_sync) )
							{
								foreach ($result_sync as $obj_sync)
								{
									$curent_mask[$obj_sync->mask_id] = $obj_sync->access_expired;
								}
							}
	
							date_default_timezone_set('UTC');
							$mask_unique = array();
							foreach( $mask_players AS $k => $v )
							{
								if( !isset($mask_unique[$v]) )
								{
									if( $mask_expired[$k] )
									{									
										$mask_expired[$k] = strtotime($mask_expired[$k]);
										$mask_expired[$k] = get_datetime($mask_expired[$k], false, true);
									}
									else
									{
										$mask_expired[$k] = 0;
									}
								
									$mask_unique[$v] = $mask_expired[$k];
								}
							}

							if( $mask_unique != $curent_mask )
							{
								$arr_add = array_diff_assoc($mask_unique, $curent_mask);
								$arr_del = array_diff_assoc($curent_mask, $mask_unique);
	
								if( !empty($arr_add) )
								{
									foreach( $arr_add as $k => $val )
									{
										$result = $db->Query("INSERT INTO `acp_access_mask_players` (mask_id, userid, access_expired) VALUES ('".$k."', '".$id."', '{expired}')
											ON DUPLICATE KEY UPDATE access_expired = '{expired}'", array('expired' => $val), $config['sql_debug']);
		
										if( !$result )
										{
											$err++; 
										}
									}
								}
		
								if( !empty($arr_del) )
								{
									foreach( $arr_del as $k => $val )
									{
										if( !array_key_exists($k, $mask_unique) )
										{
											$result = $db->Query("DELETE FROM `acp_access_mask_players` WHERE mask_id = ".$k." AND userid = ".$id."", array(), $config['sql_debug']);
			
											if( !$result )
											{
												$err++; 
											}
										}
									}
								}
							}
	
							if( $err )
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_sync_failed'].'</span>';
							}
							else
							{
								if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_account", "edit user account: ".$id);
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
							}
						}
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['empty_array'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "6":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("UPDATE `acp_players` SET approved = 'yes' WHERE userid IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "multiple active accounts: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['active_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['active_error'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "7":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("UPDATE `acp_players` SET approved = 'no' WHERE userid IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "multiple inactive accounts: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['inactive_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['active_error'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "8":

			$username = (string)$_POST["username"];
			if ($config['charset'] != 'utf-8')
			{
				$username = iconv('utf-8', $config['charset'], $username);
			}

			if (strlen($username) == 0) break;

			$output = "";
			$uid = 0;
			$arguments = array('uname'=>$username);

			$select_users = $db->Query("SELECT acp_users.username AS username, acp_users.uid AS uid FROM `acp_users` 
				LEFT JOIN `acp_players` ON acp_users.uid = acp_players.userid 
				LEFT JOIN `acp_players_requests` ON acp_users.uid = acp_players_requests.userid 
				WHERE acp_users.username LIKE '{uname}%' AND acp_players.userid IS NULL AND (acp_players_requests.userid IS NULL OR acp_players_requests.ticket_status > 0 OR (acp_players_requests.ticket_status = 0 AND acp_players_requests.productid != 'gameAccounts')) 
				LIMIT 15", $arguments, $config['sql_debug']);

			if( is_array($select_users) )
			{
				foreach ($select_users as $obj)
				{
					if( !$uid )
						$uid = $obj->uid;

					$uname = $obj->username;
					$output .= '<li onClick="fill(\''.addslashes($obj->username).'\');">'.htmlspecialchars($obj->username).'</li>';
				}
			}

			if ($output)
			{
				echo '<ul id="uid_'.$uid.'">'.$output.'</ul>';
			}

			break;

		case "9":

			$uid = $_POST["uid"];

			if( !is_numeric($uid) ) break;

			$output = "";
			$arguments = array('uid'=>$uid);

			$result_user = $db->Query("SELECT acp_usergroups.usergroupname AS usergroup, acp_users.mail AS mail, acp_users.icq AS icq, 
				acp_users.ipaddress AS ipaddress, acp_users.reg_date AS reg_date, acp_users.last_visit AS last_visit, acp_users.hid AS hid 
				FROM `acp_users` LEFT JOIN `acp_usergroups` ON acp_usergroups.usergroupid = acp_users.usergroupid 
				WHERE uid = '{uid}'", $arguments, $config['sql_debug']);

			if( is_array($result_user) )
			{
				foreach ($result_user as $obj)
				{
					$output .= '
						<li><b>'.$translate["user_reg_date"].'</b> '.( ($obj->reg_date > 0) ? get_datetime($obj->reg_date, $config['date_format']) : "-" ).'</li>
						<li><b>'.$translate["user_last_visit"].'</b> '.( ($obj->last_visit > 0) ? get_datetime($obj->last_visit, $config['date_format']) : "-" ).'</li>
						<li><b>'.$translate["user_mail"].'</b> '.$obj->mail.'</li>
						<li><b>'.$translate["user_icq"].'</b> '.( ($obj->icq) ? $obj->icq : "-" ).'</li>
						<li><b>'.$translate["user_hid"].'</b> '.( ($obj->hid) ? $obj->hid : "-" ).'</li>
						<li><b>'.$translate["user_group"].'</b> '.htmlspecialchars($obj->usergroup).'</li>
						<li><b>'.$translate["user_reg_ip"].'</b> '.$obj->ipaddress.'</li>
					';
				}
			}

			if ($output)
			{
				echo '<ul>'.$output.'</ul>';
			}

			break;

		case "10":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$uid = $_POST["id"];
	
				$arguments = array('uid'=>$uid);
				$result = $db->Query("UPDATE `acp_players` SET approved = IF(approved = 'no', 'yes', 'no') WHERE userid = '{uid}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "change status for player: ".$uid);
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['change_status_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['change_status_error'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "11":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$uid = $_POST['uid'];

			$arguments = array('offset'=>$offset,'limit'=>$limit,'id'=>$uid);
			$result = $db->Query("SELECT r.id, r.userid, r.timestamp, r.ticket_type, r.ticket_status, r.closed_time, r.closed_admin, r.comment, t.label, t.varname, r.fields_update FROM `acp_players_requests` r, `acp_ticket_type` t 
				WHERE r.ticket_type = t.id AND r.userid = '{id}' AND r.productid = 'gameAccounts' ORDER BY r.id DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach($result as $obj)
				{
					$fields_update = unserialize($obj->fields_update);
					$obj->elapsed = ($obj->closed_time > 0 && $obj->ticket_status) ? compacttime($obj->closed_time - $obj->timestamp, $config['ga_time_format']) : compacttime(time() - $obj->timestamp, $config['ga_time_format']);
					$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, $config['date_format']) : "-";
					$obj->label = str_replace("@@", "", $obj->label);
					$obj->ticket_type = ($var = $obj->varname) ? sprintf($translate[$obj->label].': %s', $fields_update[$var]) : $translate[$obj->label];

					$tickets[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($tickets)) $smarty->assign("tickets",$tickets);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("tpl", $config['template']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('profile_tickets.tpl');

			break;

		case "12":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT * FROM `acp_access_mask` LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				$mask_players = array();
				$result_players = $db->Query("SELECT m.mask_id, count(p.mask_id) AS cnt FROM `acp_access_mask` m 
					LEFT JOIN `acp_access_mask_players` p ON m.mask_id = p.mask_id 
					GROUP BY m.mask_id", array(), $config['sql_debug']);

				if( is_array($result_players) )
				{
					foreach ($result_players as $obj_pl)
					{
						$mask_players[$obj_pl->mask_id] = $obj_pl->cnt;
					}
				}

				foreach( $result as $obj )
				{
					$obj->players = ( isset($mask_players[$obj->mask_id]) ) ? $mask_players[$obj->mask_id] : 0;
					$masks[] = (array)$obj;
				}

				$mask_servers = array();
				$result = $db->Query("SELECT s.hostname, m.mask_id, m.server_id FROM `acp_access_mask_servers` m 
					LEFT JOIN `acp_servers` s ON m.server_id = s.id", array(), $config['sql_debug']);
	
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						$name = ( $obj->server_id === 0 ) ? "@@all@@" : htmlspecialchars($obj->hostname);
						$mask_servers[$obj->mask_id][] = array('id'=>$obj->server_id, 'name'=>$name);
					}
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($mask_servers)) $smarty->assign("mask_servers",$mask_servers);
			if(isset($masks)) $smarty->assign("masks",$masks);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("tpl", $config['template']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamecp_mask_list.tpl');

			break;

		case "13":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_masks', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$access_flags = trim($_POST['access_flags']);
	
				if( $_POST['servers_all'] == "yes" )
				{
					$access_servers = 0;
				}
				else
				{
					if( isset($_POST['access_servers']) )
					{
						$access_servers = $_POST['access_servers'];
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['server_not_select'].'</span>';
						break;
					}
				}
	
				if( $access_flags == "" )
				{
					print $translate['dont_empty'];
				}
				else
				{
					$arguments = array('flags'=>$access_flags);
	
					$result = $db->Query("INSERT INTO `acp_access_mask` (access_flags) VALUES ('{flags}')", $arguments, $config['sql_debug']);
	
					if( !$result )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
					}
					else
					{
						$mask_insert_id = $db->LastInsertID();
						$err = 0;
	
						if( $mask_insert_id )
						{
							if( $access_servers === 0 )
							{
								$result = $db->Query("INSERT INTO `acp_access_mask_servers` (mask_id, server_id) VALUES ('".$mask_insert_id."', '0')", array(), $config['sql_debug']);
	
								if( !$result )
								{
									$err++; 
								}
							}
							else
							{
								if( !empty($access_servers) )
								{
									foreach($access_servers as $val)
									{
										$result = $db->Query("INSERT INTO `acp_access_mask_servers` (mask_id, server_id) VALUES ('".$mask_insert_id."', '".$val."')", array(), $config['sql_debug']);
	
										if( !$result )
										{
											$err++; 
										}
									}
								}
							}
	
							if( !$err )
							{
								if (in_array("log_edititing", $config['user_action_log'])) saveLogs("access_mask", "add access mask");
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_mask_success'].'</span>';
							}
							else
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_srvsync_failed'].'</span>';
							}
						}
						else
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_lastid_failed'].'</span>';
						}
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "14":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_masks', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				if( $id == $config['default_access'] )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_default_mask'].'</span>';
				}
				else
				{
					$arguments = array('id'=>$id);
					$result = $db->Query("DELETE FROM `acp_access_mask` WHERE mask_id = '{id}'", $arguments, $config['sql_debug']);
		
					if( $result )
					{
						$err = 0;
						$result = $db->Query("DELETE FROM `acp_access_mask_players` WHERE mask_id = '{id}'", $arguments, $config['sql_debug']);
		
						if( !$result )
						{
							$err++;
						}
		
						$result = $db->Query("DELETE FROM `acp_access_mask_servers` WHERE mask_id = '{id}'", $arguments, $config['sql_debug']);
		
						if( !$result )
						{
							$err++;
						}
		
						if( !$err )
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("access_mask", "delete mask id: ".$id);
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_mask_success'].'</span>';
						}
						else
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_synctbl_failed'].'</span>';
						}
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "15":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_masks', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['mask_id'];
				$access_flags = trim($_POST['access_flags']);
	
				if( $_POST['servers_all'] == "yes" )
				{
					$access_servers = array(0);
				}
				else
				{
					$access_servers = ( isset($_POST['access_servers']) ) ? $_POST['access_servers'] : array();
				}
	
				if( $access_flags == "" )
				{
					print $translate['dont_empty'];
				}
				else
				{
					$arguments = array('id'=>$id,'flags'=>$access_flags);
	
					$result = $db->Query("UPDATE `acp_access_mask` SET access_flags = '{flags}' WHERE mask_id = {id}", $arguments, $config['sql_debug']);
	
					if( !$result )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_failed'].'</span>';
					}
					else
					{
						$curent_servers = array();
						$err = 0;
						$result_sync = $db->Query("SELECT mask_id, server_id FROM `acp_access_mask_servers` WHERE mask_id = '{id}'", $arguments, $config['sql_debug']);
	
						if( is_array($result_sync) )
						{
							foreach ($result_sync as $obj_sync)
							{
								$curent_servers[] = $obj_sync->server_id;
							}
						}
	
						if( $access_servers != $curent_servers )
						{
							$arr_add = array_diff($access_servers, $curent_servers);
							$arr_del = array_diff($curent_servers, $access_servers);
	
							if( !empty($arr_add) )
							{
								foreach($arr_add as $val)
								{
									$result = $db->Query("INSERT INTO `acp_access_mask_servers` (mask_id, server_id) VALUES ('".$id."', '".$val."')", array(), $config['sql_debug']);
	
									if( !$result )
									{
										$err++; 
									}
								}
							}
	
							if( !empty($arr_del) )
							{
								foreach($arr_del as $val)
								{
									$result = $db->Query("DELETE FROM `acp_access_mask_servers` WHERE mask_id = ".$id." AND server_id = ".$val."", array(), $config['sql_debug']);
	
									if( !$result )
									{
										$err++; 
									}
								}
							}
						}
	
						if( !$err )
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("access_mask", "edit access mask: ".$id);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_mask_success'].'</span>';
						}
						else
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_srvsync_failed'].'</span>';
						}
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "16":

			$id = $_POST['id'];
			if( !is_numeric($id) ) break;

			$ticket_status = $db->Query("SELECT ticket_status FROM `acp_players_requests` WHERE id = ".$id." AND userid = ".$userinfo['uid']." AND productid = 'gameAccounts'", array(), $config['sql_debug']);
			if( !is_null($ticket_status) )
			{
				if( $ticket_status > 0 )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_ticket_closed'].'</span>';
				}
				else
				{
					$result = $db->Query("DELETE FROM `acp_players_requests` WHERE id = ".$id, array(), $config['sql_debug']);

					if( !$result )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_error'].'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['ticket_withdraw_success'].'</span>';
					}
				}
			}
			else
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_ticket_not_found'].'</span>';
			}

			break;

		case "17":

			// -1 - account blocked
			// 0 - not account
			// 1 - first reg account
			// 2 - account moderate
			// 3 - account active

			if( $userinfo['uid'] != $_POST['uid'] ) break;

			$arguments = array('id'=>$_POST['uid']);
			$result_user = $db->Query("SELECT userid, timestamp, flag, player_nick, password, player_ip, steamid, last_time, approved, online, 
				(SELECT ticket_status FROM `acp_players_requests` WHERE userid = {id} AND ticket_status = 0 AND productid = 'gameAccounts' LIMIT 1) AS ticket 
				FROM `acp_players` WHERE userid = {id}", $arguments, $config['sql_debug']);
	
			if( is_array($result_user) )
			{
				foreach($result_user as $obj)
				{
					$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, 'd-m-Y, H:i') : '-';
					$obj->last_time = ($obj->last_time > 0) ? get_datetime($obj->last_time, 'd-m-Y, H:i') : '-';
					$obj->online = compacttime($obj->online, $config['ga_time_format']);
	
					$array_user = (array)$obj;
				}
	
				if( $array_user['approved'] == 'no' )
				{
					$account_status = -1;
					$error = "@@block_account@@";
				}
				elseif( !is_null($array_user['ticket']) )
				{
					$account_status = 2;
					$warnings[] = "@@ticket_on_moderate@@";
				}
				else
				{
					$account_status = 3;
				}
			}
			else
			{
				$array_user['userid'] = $userinfo['uid'];
				$result_reg = $db->Query("SELECT fields_update FROM `acp_players_requests` WHERE userid = {id} AND ticket_status = 0 AND productid = 'gameAccounts' LIMIT 1", $arguments, $config['sql_debug']);

				if( $result_reg )
				{
					$fields_update = unserialize($result_reg);
					if( is_array($fields_update) )
					{
						$array_user = array('flag'=>$fields_update['flag'],'player_nick'=>(isset($fields_update['player_nick'])) ? $fields_update['player_nick'] : '','player_ip'=>(isset($fields_update['player_ip'])) ? $fields_update['player_ip'] : '','steamid'=>(isset($fields_update['steamid'])) ? $fields_update['steamid'] : '');
					}
					$account_status = 1;
					$warnings[] = "@@ticket_reg_on_moderate@@";
				}
				else
				{
					$account_status = 0;
					$warnings[] = "@@not_account@@";
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("account", $array_user);
			$ga_access_type = ( $config['ga_access_type'] ) ? explode(',', $config['ga_access_type']) : array();
			$smarty->assign("ga_access_type",$ga_access_type);
			$smarty->assign("account_status",$account_status);
			if( isset($warnings) ) $smarty->assign("iswarn",$warnings);
			if( isset($error) ) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('profile_account_load.tpl');

			break;

		case "18":

			$id = $_POST['userid'];
			$error = array();
			unset($_POST['go'],$array_keys);

			$arguments = array('id'=>$id);
			$result_user = $db->Query("SELECT userid, timestamp, flag, player_nick, password, player_ip, steamid, last_time, approved, online, 
				(SELECT ticket_status FROM `acp_players_requests` WHERE userid = {id} AND ticket_status = 0 AND productid = 'gameAccounts' LIMIT 1) AS ticket 
				FROM `acp_players` WHERE userid = {id}", $arguments, $config['sql_debug']);

			if( is_array($result_user) )
			{
				foreach( $result_user as $obj )
				{
					$array_user = (array)$obj;
				}

				if( $array_user['approved'] == 'no' )
				{
					$account_status = -1;
				}
				elseif( !is_null($array_user['ticket']) )
				{
					$account_status = 2;
				}
				else
				{
					$account_status = 3;
				}
			}
			else
			{
				$result_reg = $db->Query("SELECT ticket_status	FROM `acp_players_requests` WHERE userid = {id} AND ticket_status = 0 AND productid = 'gameAccounts'", $arguments, $config['sql_debug']);

				if( is_null($result_reg) )
				{
					$account_status = 0;
				}
				else
				{
					$account_status = 1;
				}
			}

			if( in_array($account_status, array(-1,1,2)) )
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['create_ticket_denied'].'</span>';
			}
			else
			{
				if( is_array($_POST) )
				{
					foreach( $_POST as $var => $value )
					{
						$value = trim($value);

						switch($var)
						{
							case "steamid":
	
								if( preg_match("/".$config['ga_steam_validate']."/", $value) != 1 )
								{
									$error[] = $translate['steam_not_empty'];
								}
								$type = 3;

								break;
	
							case "password":
	
								if( $value == '' )
								{
									$check_auth = $db->Query("SELECT flag FROM `acp_players` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
						
									if( $check_auth != $_POST['flag'] )
									{
										$error[] = $translate['pass_not_empty'];	
									}
								}
								else
								{
									if( $config['ga_password_validate'] )
									{
										if( preg_match("/".$config['ga_password_validate']."/", $value) != 1 )
										{
											$error[] = $translate['password_not_valid'];
										}
									}
									$value = md5($value);
								}
								break;
	
							case "player_nick":

								if( !$value )
								{
									$error[] = $translate['nick_not_empty'];
								}
								else
								{
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
									}

									$nick_len = strlen($value);
									if( $nick_len < $config['ga_nicklen_min'] )
									{
										$error[] = $translate['nick_is_short'];
									}
									elseif( $nick_len > $config['ga_nicklen_max'] )
									{
										$error[] = $translate['nick_is_long'];
									}
									else
									{
										$args = array('userid'=>$id, 'nick'=>$value);				
										$check_nick = $db->Query("SELECT userid FROM `acp_players` WHERE userid != {userid} AND player_nick = '{nick}' LIMIT 1", $args, $config['sql_debug']);
										$check_ticket = $db->Query("SELECT userid, fields_update FROM `acp_players_requests` WHERE ticket_status = 0 AND productid = 'gameAccounts' LIMIT 1", array(), $config['sql_debug']);
										if( is_array($check_ticket) )
										{
											foreach($check_ticket as $tic)
											{
												$fields_update = unserialize($tic->fields_update);
												if( is_array($fields_update) )
												{
													if( $fields_update['flag'] == 1 && $fields_update['player_nick'] == $value )
													{
														$check_fail = true;
														break;
													}
												}
											}

											$check_ticket = (isset($check_fail)) ? $check_fail : NULL;
										}

										if( !is_null($check_nick) || !is_null($check_ticket) )
										{
											$error[] = $translate['nick_already_used'];
										}
									}
								}

								$type = 1;
								break;
	
							case "player_ip":
	
								if ($value != '')
								{
									if (!preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value))
									{
										$error[] = $translate['ip_not_valid'];
									}
								}
								else
								{
									$error[] = $translate['ip_not_empty'];
								}
								$type = 2;
								break;
						}
	
						if( $var == 'password' &&  $value == '' )
						{
							 continue;
						}
						else
						{
							$array_keys[$var] = $value;
						}
					}

					if( $account_status == 3 )
					{
						$array_keys['flag_old'] = $array_user['flag'];
						if( $array_user['flag'] == $_POST['flag'] )
						{
							switch($type)
							{
								case "1":

									$array_keys['player_nick_old'] = $array_user['player_nick'];
									if( $array_user['player_nick'] == $array_keys['player_nick'] )
									{
										if( !array_key_exists('password', $array_keys) )
										{
											$error[] = $translate['data_identity'];
										}
										else
										{
											$update_pass = $array_keys['password'];
										}
									}
									break;

								case "2":

									$array_keys['player_ip_old'] = $array_user['player_ip'];
									if( $array_user['player_ip'] == $array_keys['player_ip'] )
									{
										$error[] = $translate['data_identity'];
									}
									break;

								case "3":

									$array_keys['steamid_old'] = $array_user['steamid'];
									if( $array_user['steamid'] == $array_keys['steamid'] )
									{
										$error[] = $translate['data_identity'];
									}
									break;
							}

							$ticket_type = $type + 3;
						}
						else
						{
							$ticket_type = $type + 6;
						}
					}
					else
					{
							$ticket_type = $type;
					}

					if( $config['ticket_moderate'] || $account_status == 0 )
					{
						$arguments['timestamp'] = time();
					}
	
					if( !empty($error) )
					{
						if( count($error) > 1 )
						{
							$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
						}
						else
						{
							$error = $error[0];
						}
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].'&nbsp;'.$error.'</span>';
					}
					else
					{
						if( isset($update_pass) )
						{
							$result = $db->Query("UPDATE `acp_players` SET password = '".$update_pass."' WHERE userid = ".$id, array(), $config['sql_debug']);

							if (!$result)
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['update_pass_error'].'</span>';
							}
							else
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['update_pass_success'].'</span>';
							}
						}
						else
						{
							if( $config['ticket_moderate'] )
							{
								$start_type = $db->Query("SELECT id FROM `acp_ticket_type` WHERE productid = 'gameAccounts' ORDER BY id LIMIT 1", array(), $config['sql_debug']);
								$arguments['ticket_type'] = $start_type - 1 + $ticket_type;
								$arguments['fields_update'] = serialize($array_keys);
								$result = $db->Query("INSERT INTO `acp_players_requests` (userid, fields_update, ticket_type, timestamp, productid) VALUES ('{id}','{fields_update}', '{ticket_type}', '{timestamp}', 'gameAccounts')", $arguments, $config['sql_debug']);
							}
							else
							{
								if( $account_status == 0 )
								{
									$array_keys['timestamp'] = $arguments['timestamp'];
									$result = $db->Query("INSERT INTO `acp_players` (".implode(',',array_keys($array_keys)).") VALUES ('".implode('\',\'',array_map('mysql_real_escape_string', $array_keys))."')", array(), $config['sql_debug']);
									$def_time = (!$config['default_access_time']) ? 0 : (time() + ($config['default_access_time']*3600));
									$result_mask = $db->Query("INSERT INTO `acp_access_mask_players` (userid, mask_id, access_expired) VALUES ('".$id."', '".$config['default_access']."', '{expired}')", array('expired' => $def_time), $config['sql_debug']);
								}
								else
								{
									$update_string = "";

									if( array_key_exists('userid', $array_keys) )
									{
										unset($array_keys['userid']);
									}

									foreach($array_keys as $k=>$v)
									{
										$update_string .= $k." = '".mysql_real_escape_string($v)."',";
									}

									$update_string = substr($update_string, 0, -1);

									$result = $db->Query("UPDATE `acp_players` SET ".$update_string." WHERE userid = ".$id, array(), $config['sql_debug']);
								}
							}
		
							if( !$result )
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['ticket_error'].'</span>';
							}
							else
							{
								if (in_array("log_edititing", $config['user_action_log'])) saveLogs("user_account", "create ticket: ".$db->LastInsertID());
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.(($config['ticket_moderate']) ? $translate['ticket_success'] : $translate['ticket_success_edit']).'</span>';
							}
						}
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['empty_array'].'</span>';
				}
			}

			break;

		case "19":

			$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_users' OR link = 'p_users_search'", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach( $result_cats as $obj )
				{
					$cat_users = $obj->sectionid;
					if( $obj->link == 'p_users' )
						$cat_user_edit = $obj->categoryid;
					else
						$cat_user_search = $obj->categoryid;
				}
			}

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$s = $_POST['s'];

			$sqlconds = "WHERE 1 = 1";
			$arguments = array('offset'=>$offset,'limit'=>$limit);

			if( $s && is_numeric($s) )
			{
				$status = $s - 1;
				$sqlconds .= " AND ticket_status = ".$status;
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
				acp_players_requests.productid AS product,
				acp_ticket_type.label AS label,
				acp_ticket_type.varname AS varname,
				acp_users.username AS username, 
				(SELECT COUNT(u.hid) FROM `acp_users` u WHERE u.hid = acp_users.hid AND u.hid IS NOT NULL GROUP BY u.hid) AS cnt_hid
				FROM `acp_players_requests` 
				LEFT JOIN `acp_ticket_type` ON acp_players_requests.ticket_type = acp_ticket_type.id 
				LEFT JOIN `acp_users` ON acp_users.uid = acp_players_requests.userid ".$sqlconds." ORDER BY acp_players_requests.id DESC LIMIT {offset},{limit}
			", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach($result as $obj)
				{
					$fields_update = unserialize($obj->fields_update);
					unset($obj->fields_update);
					if( is_array($fields_update) )
					{
						foreach($fields_update as $k => $v)
						{
							$obj->$k = $v;
						}
					}
					$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, $config['date_format']) : "-";
					$obj->label = str_replace("@@", "", $obj->label);
					//echo "DEBUG varname:".$obj->varname;
					//print_r($fields_update);
					$obj->ticket_type = ($var = $obj->varname) ? sprintf($translate[$obj->label].': %s', $obj->$var) : $translate[$obj->label];
					$tickets[] = (array)$obj;
				}
			}

			require_once("scripts/smarty/Smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($cat_users)) $smarty->assign("cat_users", $cat_users);
			if(isset($cat_user_edit)) $smarty->assign("cat_user_edit", $cat_user_edit);
			if(isset($cat_user_search)) $smarty->assign("cat_user_search", $cat_user_search);
			$smarty->assign("get_status",$s);
			if(isset($tickets)) $smarty->assign("tickets",$tickets);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamecp_requests_list.tpl');

			break;

		case "20":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('perm_tickets', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_players_requests` WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "delete ticket id: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_ticket_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "21":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('perm_tickets', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_players_requests` WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "multiple delete tickets: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_tickets_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "22":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('perm_tickets', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
				$error = array();
				$comment = $_POST['comment'];
				if( $config['charset'] != 'utf-8' )
				{
					$comment = iconv('utf-8', $config['charset'], $comment);
				}
				$arguments_update = array('ids'=>$ids, 'comment'=>$comment, 'closed_time'=>time(), 'closed_admin'=>$_POST['username']);
	
				$result_select = $db->Query("SELECT id, userid, ticket_type, fields_update, productid FROM `acp_players_requests` WHERE ticket_status = 0 AND id IN ('{ids}')", $arguments_update, $config['sql_debug']);
	
				if( is_array($result_select) )
				{
					include(INCLUDE_PATH . 'functions.ticket.php');
	
					foreach( $result_select as $obj )
					{
						if( !isset($start_type[$obj->productid]) )
							$start_type[$obj->productid] = $db->Query("SELECT id FROM `acp_ticket_type` WHERE productid = '{product}' ORDER BY id LIMIT 1", array('product' => $obj->productid), $config['sql_debug']);
						$obj->ticket_type = $obj->ticket_type - $start_type[$obj->productid] + 1;
	
						ticket_approve((array)$obj, $arguments_update, $error);
					}
				}			
				else
				{
					$error[] = $translate['tickets_not_found'];
				}
	
				if( !empty($error) )
				{
					if(count($error) > 1)
					{
						$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
					}
					else
					{
						$error = $error[0];
					}
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].'&nbsp;'.$error.'</span>';
				}
				else
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "multiple approve tickets: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['approve_multiply_tickets_success'].'&nbsp;'.count($ids).'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "23":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('perm_tickets', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
				$comment = $_POST['comment'];
				if( $config['charset'] != 'utf-8' )
				{
					$comment = iconv('utf-8', $config['charset'], $comment);
				}
	
				$arguments = array('ids'=>$ids, 'comment'=>$comment, 'closed_time'=>time(), 'closed_admin'=>$_POST['username']);
				$result = $db->Query("UPDATE `acp_players_requests` SET ticket_status = 2, comment = '{comment}', closed_time = '{closed_time}', closed_admin = '{closed_admin}' WHERE id IN ('{ids}') AND ticket_status = 0", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("game_accounts", "multiple disapprove tickets: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['disapprove_multiply_tickets_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "24":

			date_default_timezone_set('UTC');

			function getDateArray($type, $currTime)
			{
				global $db;

				$arrOut = array();
				list($currYear, $currMonth, $currDay, $currHour) = explode("-", date('Y-m-d-H', $currTime));

				switch($type)
				{
					case "w":

						$currDateString = $currYear."-".$currMonth."-".$currDay." 00:00:00";
						$startTime = strtotime($currDateString) - (3600*24*6);

						$query = $db->Query("SELECT timestamp, FROM_UNIXTIME(timestamp, '%Y-%m-%d') AS time, count(userid) AS cnt 
							FROM `acp_players` WHERE timestamp > ".$startTime." GROUP BY time LIMIT 7", array());

						$i = 0;
						while( $i < 7 )
						{
							$index = (string)($startTime*1000);
							$arrOut["accreg"][$index] = 0;
							$startTime = $startTime + 86400;
							$i++;
						}
						break;

					case "y":

						$currDateString = $currYear."-".$currMonth."-01 00:00:00";
						$startTime = strtotime("1 year ago", strtotime($currDateString));
						$startTime = strtotime("next month", $startTime);

						$query = $db->Query("SELECT timestamp, FROM_UNIXTIME(timestamp, '%Y-%m') AS time, count(userid) AS cnt 
							FROM `acp_players` WHERE timestamp > ".$startTime." GROUP BY time LIMIT 12", array());

						$i = 0;
						while( $i < 12 )
						{
							$index = (string)($startTime*1000);
							$arrOut["accreg"][$index] = 0;
							$startTime = strtotime("next month", $startTime);
							$i++;
						}
						break;
				}

				if( is_array($query) )
				{
					foreach( $query as $obj )
					{
						$bd_time = explode("-", $obj->time);
						$time = strtotime($bd_time[0]."-".$bd_time[1]."-".((isset($bd_time[2])) ? $bd_time[2] : '01')." 00:00:00");
						$index = (string)($time*1000);

						if( isset($arrOut["accreg"][$index]) )
							$arrOut["accreg"][$index] = $obj->cnt;
					}
				}

				return $arrOut;
			}

			$action = $_POST['action'];
			$dateArray = array();

			if( in_array($action, array('w', 'y')) )
			{
				$dateArray = getDateArray($action, time());
			}

			echo json_encode($dateArray);

			break;

		case "25":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ga_perm_players', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				// delete search accounts
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "26":

			$result_cats = $db->Query("SELECT categoryid, sectionid FROM `acp_category` WHERE link = 'p_users'", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach ($result_cats as $obj)
				{
					$cat_users = $obj->sectionid;
					$cat_user_edit = $obj->categoryid;
				}
			}

			$offset = $_GET['offset'] - 1;
			$limit = $_GET['limit'];
			unset($_POST['go']);
			$sqlconds = 'WHERE 1=1';
			$arguments = array_merge($_POST, array('offset'=>$offset,'limit'=>$limit));

			foreach( $_POST as $var => $value )
			{
				switch($var)
				{
					case "flag":

						$sqlconds .= " AND a.flag IN ('{flag}')";	
						break;
	
					case "server_id":
	
						$sqlconds .= " AND e.server_id IN ('{server_id}')";	
						break;
	
					case "startdate":
	
						$sqlconds .= " AND a.last_time >= '{startdate}'";
						break;
	
					case "enddate":

						$sqlconds .= " AND a.last_time <= '{enddate}'";
						break;
	
					case "player_nick":
	
						if( $config['charset'] != 'utf-8' )
						{
							$value = iconv('utf-8', $config['charset'], $value);
						}	
						$sqlconds .= " AND a.".$var." LIKE '%{".$var."}%'";
						break;
	
					case "player_ip":
					case "steamid":
	
						$sqlconds .= " AND a.".$var." LIKE '%{".$var."}%'";	
						break;
	
					case "mask_id":
	
						$sqlconds .= " AND d.mask_id = '{mask_id}'";
						break;
	
					case "access_flags":

						$sqlconds .= " AND d.".$var." LIKE '%{".$var."}%'";
						break;
	
					case "access_expired":
	
						$sqlconds .= " AND c.".$var." < '{".$var."}' AND d.".$var." > 0";
						break;
	
					case "username":

						if( $config['charset'] != 'utf-8' )
						{
							$value = iconv('utf-8', $config['charset'], $value);
						}	
						$sqlconds .= " AND b.".$var." LIKE '%{".$var."}%'";
						break;
				}
			}

			$result = $db->Query("SELECT 
				a.userid AS userid,
				a.flag AS flag,
				a.player_nick AS player_nick,
				a.player_ip AS player_ip,
				a.steamid AS steamid,
				a.timestamp AS timestamp,
				a.last_time AS last_time,
				a.approved AS approved,
				a.online AS online,
				a.points AS points,
				b.username AS username,
				(SELECT COUNT(u.hid) FROM `acp_users` u WHERE u.hid = b.hid AND u.hid IS NOT NULL GROUP BY u.hid) AS cnt_hid 
				FROM `acp_players` a LEFT JOIN `acp_users` b ON b.uid = a.userid 
				LEFT JOIN `acp_access_mask_players` c ON c.userid = a.userid 
				LEFT JOIN `acp_access_mask` d ON d.mask_id = c.mask_id 
				LEFT JOIN `acp_access_mask_servers` e ON e.mask_id = c.mask_id 
				".$sqlconds." 
				GROUP BY a.userid ORDER BY a.timestamp DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				date_default_timezone_set('UTC');
				foreach( $result as $obj )
				{
					$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, $config['date_format']) : "-";
					$obj->last_time = ($obj->last_time > 0) ? get_datetime($obj->last_time, $config['date_format']) : "-";
					$accounts[] = (array)$obj;
				}
			}

			require_once("scripts/smarty/Smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($cat_users)) $smarty->assign("cat_users", $cat_users);
			if(isset($cat_user_edit)) $smarty->assign("cat_user_edit", $cat_user_edit);
			if(isset($accounts)) $smarty->assign("accounts",$accounts);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamecp_search_load.tpl');

			break;

		case "27":

			$dateArray = array(
				'str' => $translate['possible_bans_no'],
				'all' => 0,
				'all_a' => 0,
				'nick' => 0,
				'nick_a' => 0,
				'ip' => 0,
				'ip_a' => 0,
				'cookie' => 0,
				'cookie_a' => 0,
				'steam' => 0,
				'steam_a' => 0
			);

			$args = array('nick' => $_POST['nick'], 'ip' => $_POST['ip'], 'cookie' => $_POST['ip'], 'steam' => $_POST['steam']);

			if( $_POST['nick'] )
			{
				$query = $db->Query("SELECT count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, 1, NULL)) AS expired, count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, NULL, 1)) AS active FROM (
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, ban_length, unban_created FROM `acp_bans_history` WHERE player_nick = '{nick}')
					UNION ALL
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, ban_length, NULL FROM `acp_bans` WHERE player_nick = '{nick}')
				) temp", $args, $config['sql_debug']);

				if( is_array($query) )
				{
					foreach( $query as $obj )
					{
						$dateArray['nick_a'] = $obj->active;
						$dateArray['nick'] = $obj->expired + $dateArray['nick_a'];
					}
				}
			}

			if( $_POST['ip'] )
			{
				$query = $db->Query("SELECT count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, 1, NULL)) AS expired, count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, NULL, 1)) AS active FROM (
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, ban_length, unban_created FROM `acp_bans_history` WHERE player_ip = '{ip}')
					UNION ALL
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, ban_length, NULL FROM `acp_bans` WHERE player_ip = '{ip}')
				) temp", $args, $config['sql_debug']);

				if( is_array($query) )
				{
					foreach( $query as $obj )
					{
						$dateArray['ip_a'] = $obj->active;
						$dateArray['ip'] = $obj->expired + $dateArray['ip_a'];
					}
				}

				$query = $db->Query("SELECT count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, 1, NULL)) AS expired, count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, NULL, 1)) AS active FROM (
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, ban_length, unban_created FROM `acp_bans_history` WHERE cookie_ip = '{ip}')
					UNION ALL
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, ban_length, NULL FROM `acp_bans` WHERE cookie_ip = '{ip}')
				) temp", $args, $config['sql_debug']);

				if( is_array($query) )
				{
					foreach( $query as $obj )
					{
						$dateArray['cookie_a'] = $obj->active;
						$dateArray['cookie'] = $obj->expired + $dateArray['cookie_a'];
					}
				}
			}

			if( preg_match("/^(STEAM_0)\:([0-1])\:([0-9]{4,8})$/", $_POST['steam']) )
			{
				$query = $db->Query("SELECT count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, 1, NULL)) AS expired, count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, NULL, 1)) AS active FROM (
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, player_id, ban_length, unban_created FROM `acp_bans_history` WHERE player_id = '{steam}')
					UNION ALL
					(SELECT bid, ban_created, player_nick, player_ip, cookie_ip, player_id, ban_length, NULL FROM `acp_bans` WHERE player_id = '{steam}')
				) temp", $args, $config['sql_debug']);

				if( is_array($query) )
				{
					foreach( $query as $obj )
					{
						$dateArray['steam_a'] = $obj->active;
						$dateArray['steam'] = $obj->expired + $dateArray['steam_a'];
					}
				}
			}

			$dateArray['all_a'] = $dateArray['nick_a'] + $dateArray['ip_a'] + $dateArray['cookie_a'] + $dateArray['steam_a'];
			$dateArray['all'] = $dateArray['nick'] + $dateArray['ip'] + $dateArray['cookie'] + $dateArray['steam'];
			if( $dateArray['all'] > 0 )
				$dateArray['str'] = sprintf($translate['possible_bans_yes'], $dateArray['all'], $dateArray['all_a']);

			echo json_encode($dateArray);
			break;

		default:

			die("Hacking Attempt");
	}
}

?>
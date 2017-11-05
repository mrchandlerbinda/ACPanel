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

	if( is_array($array_cfg) )
	{
		foreach( $array_cfg as $obj )
		{
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();
	}

	include(INCLUDE_PATH . 'functions.main.php');
	$langs = create_lang_list();

	unset($translate);
	$arguments = array('lp_name'=>'p_gamebans.tpl','lang'=>get_language(1));
	$tr_result = $db->Query("
		SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`
		LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_name='{lp_name}'
		WHERE acp_lang_pages.lp_id = acp_lang_words.lw_page OR acp_lang_words.lw_page = '0'
	", $arguments, $config['sql_debug']);
	if( is_array($tr_result) )
		foreach ($tr_result as $obj) $translate[$obj->lw_word] = $obj->lw_translate;

	include(INCLUDE_PATH . '_auth.php');

	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create list
	// 2 - add item
	// 3 - del item
	// 4 - multiply del items
	// 5 - edit item
	// 6 - create reasons list
	// 7 - remove reason
	// 8 - edit reason
	// 9 - multiple delete reasons
	// 10 - add reason
	// 11 - create list of subnets
	// 12 - add subnet
	// 13 - del subnet
	// 14 - multiply del subnets
	// 15 - edit subnet
	// 16 - change subnet status
	// 17 - delete search bans
	// 18 - load search bans
	// 19 - public players list
	// 20 - create list of public subnets
	// 21 - create list for stats
	// 22 - top stats
	// 23 - calc subnet
	// 24 - get bans quick stats

	switch($_POST['go'])
	{
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
			$status = $_POST['status'];

			$arguments = array('offset'=>$offset,'limit'=>$limit, 'time' => time());
			if( $status == 1 )
			{
				$result = $db->Query("SELECT * FROM `acp_bans` WHERE {time} < (ban_created+(ban_length*60)) OR ban_length = 0 ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}
			else if( $status == 2 )
			{
				$result = $db->Query("SELECT * FROM (
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, unban_created, unban_admin_uid FROM `acp_bans_history`)
						UNION ALL
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, NULL, NULL FROM `acp_bans` WHERE {time} > (ban_created-1+(ban_length*60)) AND ban_length > 0)
					) temp ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}
			else
			{
				$result = $db->Query("SELECT * FROM (
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, unban_created, unban_admin_uid FROM `acp_bans_history`)
						UNION ALL
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, NULL, NULL FROM `acp_bans`)
					) temp ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}

			if( is_array($result) )
			{
				include(INCLUDE_PATH . 'class.SypexGeo.php');
				$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
				$current_time = time();

				foreach( $result as $obj )
				{
					$player_country_code = strtolower($SxGeo->getCountry($obj->player_ip));
					$obj->country = (file_exists("images/flags/".$player_country_code.".gif")) ? "<img style='vertical-align: middle' src='acpanel/images/flags/".$player_country_code.".gif' alt='' />" : "<img style='vertical-align: middle' src='acpanel/images/flags/err.gif' alt='' />";

					if( isset($obj->unban_admin_uid) && $obj->unban_admin_uid )
					{
						$obj->ban_remain = $translate['ban_removed'];
					}
					else if( $obj->ban_length && ($obj->ban_length*60 + $obj->ban_created - $current_time) <= 0 )
					{
						$obj->ban_remain = $translate['ban_expired'];
					}
					else
					{
						$obj->ban_remain = "";
					}

					$obj->ban_length = ($obj->ban_length == 0) ? $translate['permanent'] : compacttime($obj->ban_length*60, $config['gb_length_format']);
					$obj->ban_created = ($obj->ban_created > 0) ? get_datetime($obj->ban_created, $config['date_format']) : '-';
					$obj->unban_created = (!isset($obj->unban_created)) ? null : $obj->unban_created;
					$obj->unban_created = (!is_null($obj->unban_created)) ? get_datetime($obj->unban_created, $config['date_format']) : 0;
					$bans[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($cat_users)) $smarty->assign("cat_users", $cat_users);
			if(isset($cat_user_edit)) $smarty->assign("cat_user_edit", $cat_user_edit);
			if(isset($bans)) $smarty->assign("bans", $bans);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_players_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_players', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ban_type = $_POST['ban_type'];
				unset($_POST['go'],$_POST['ban_status_new'],$_POST['ban_status_old']);
				$query_string = "";
				if( !trim($_POST['cookie_ip']) )
				{
					$_POST['cookie_ip'] = $_POST['player_ip'];
				}
	
				if( is_array($_POST) )
				{
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "player_nick":
	
								if( $value )
								{
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
									}
	
									if( $ban_type == "N" )
										$search = array('k'=>$var,'v'=>$value,'t'=>'N');
								}
								elseif( $ban_type == "N" )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								break;
	
							case "player_ip":
	
								if( $ban_type == "SI" && $value == '' )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								if( $ban_type == "SI" )
									$search = array('k'=>$var,'v'=>$value,'t'=>'SI');
	
							case "cookie_ip":
	
								if( $value != '' )
								{
									if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value) )
									{
										$error[] = $translate['ip_not_valid'];
									}
								}
	
								break;
	
							case "player_id":
	
								if( $ban_type == "S" && $value == '' )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								if( $ban_type == "S" )
									$search = array('k'=>$var,'v'=>$value,'t'=>'S');
	
								break;
	
							case "ban_length":
	
								if( !is_numeric($value) )
								{
									$value = 0;
								}
	
								break;
	
							case "ban_reason":
	
								if( $value == '' )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								break;
						}
	
						if( isset($_POST[$var]) )
							$query_string .= $var." = '".mysql_real_escape_string($value)."',";
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$query_string = substr($query_string, 0, strlen($query_string)-1);
						$query_string .= ",ban_created = '".time()."',admin_uid = '".$userinfo['uid']."',admin_nick = '{admin}',admin_ip = '{admin_ip}',server_name = 'website'";
						$arguments = array('admin'=>$userinfo['username'],'admin_ip'=>getRealIpAddr());
	
						$check = $db->Query("SELECT bid FROM `acp_bans` WHERE ".$search['k']." = '".$search['v']."' AND ban_type = '".$search['t']."' AND (".time()." < (ban_created+(ban_length*60)) OR ban_length = 0) LIMIT 1", array(), $config['sql_debug']);
	
						if( $check )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_double_error'].'</span>';
						}
						else
						{
							$result = $db->Query("INSERT INTO `acp_bans` SET ".$query_string."", $arguments, $config['sql_debug']);
	
							if( !$result )
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
							}
							else
							{
								if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_ban", "add new ban");
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
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
			$userPerm = $permClass->getPermissions('gb_perm_players', $userinfo['usergroupid']);
			$userPermMy = $permClass->getPermissions('gb_perm_players_my', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' || $userPermMy['delete'] ) 
			{
				$sqlcond = "";

				if( !$userPerm['delete'] && $userPermMy['delete'] && $userinfo['admin_access'] != 'yes' )
					$sqlcond = " AND admin_uid = ".$userinfo['uid'];

				$id = $_POST['id'];
				$arguments = array('id'=>$id);
	
				$result = $db->Query("DELETE FROM `acp_bans` WHERE bid = '{id}'".$sqlcond, $arguments, $config['sql_debug']);
				$one = $db->Affected();
				$result = $db->Query("DELETE FROM `acp_bans_history` WHERE bid = '{id}'".$sqlcond, $arguments, $config['sql_debug']);
				$two = $db->Affected();

				if( ($one + $two) == 0 )
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';
				elseif( $result )
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_bans", "delete ban id: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "4":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_players', $userinfo['usergroupid']);
			$userPermMy = $permClass->getPermissions('gb_perm_players_my', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' || $userPermMy['delete'] ) 
			{
				$sqlcond = "";

				if( !$userPerm['delete'] && $userPermMy['delete'] && $userinfo['admin_access'] != 'yes' )
					$sqlcond = " AND admin_uid = ".$userinfo['uid'];

				$ids = $_POST['marked_word'];
		
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_bans` WHERE bid IN ('{ids}')".$sqlcond, $arguments, $config['sql_debug']);
				$one = $db->Affected();	
				$result = $db->Query("DELETE FROM `acp_bans_history` WHERE bid IN ('{ids}')".$sqlcond, $arguments, $config['sql_debug']);	
				$two = $db->Affected();

				if( ($one + $two) == 0 )
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';
				if( $result )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_bans", "myltiple delete bans: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "5":

			date_default_timezone_set('UTC');
			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_players', $userinfo['usergroupid']);
			$userPermMy = $permClass->getPermissions('gb_perm_players_my', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' || $userPermMy['write'] ) 
			{
				$sqlcond = "";

				if( !$userPerm['write'] && $userPermMy['write'] && $userinfo['admin_access'] != 'yes' )
					$sqlcond = " AND admin_uid = ".$userinfo['uid'];

				$bid = $_POST['bid'];
				$status_new = $_POST['ban_status_new'];
				$status_old = $_POST['ban_status_old'];
				$ban_type = $_POST['ban_type'];
				unset($_POST['go'],$_POST['ban_status_new'],$_POST['ban_status_old']);
				$query_string = "";
	
				if( is_array($_POST) )
				{
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "bid":
	
								if( $status_new == $status_old )
									unset($_POST[$var]);
	
								break;
	
							case "unban_reason":
	
								if( $status_new == 1 )
								{
									unset($_POST[$var]);
								}
								elseif( $value == '' )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								break;
	
							case "player_nick":

								if( $value )
								{
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
									}
								}
								elseif( $ban_type == "N" )
									$error[] = sprintf($translate['empty_field'], $var);

								if( $ban_type == "N" )
									$search = array('k'=>$var,'v'=>mysql_real_escape_string($value),'t'=>'N');

								break;

							case "admin_nick":
	
								if( $value )
								{
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
									}
								}
	
								break;
	
							case "player_ip":
	
								if( $ban_type == "SI" && $value == '' )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}

								if( $ban_type == "SI" )
									$search = array('k'=>$var,'v'=>$value,'t'=>'SI');
	
							case "admin_ip":
							case "cookie_ip":
	
								if( $value != '' )
								{
									if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value) )
									{
										$error[] = $translate['ip_not_valid'];
									}
								}
	
								break;
	
							case "player_id":
	
								if( $ban_type == "S" && $value == '' )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}

								if( $ban_type == "S" )
									$search = array('k'=>$var,'v'=>$value,'t'=>'S');

								break;
	
							case "ban_created":
	
								if( $value != '' )
								{
									$value = strtotime($value);
									$value = get_datetime($value, false, true);
								}
								else
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								break;
	
							case "ban_length":
	
								if( !is_numeric($value) )
								{
									$value = 0;
								}
	
								break;
	
							case "ban_reason":
	
								if( $value == '' )
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								break;
	
							case "server_ip":
	
								if( $value != '' )
								{
									if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\:\d[0-9]+)$/i", $value) )
									{
										$error[] = $translate['serverip_not_valid'];
									}
								}
	
								break;
						}
	
						if( isset($_POST[$var]) )
							$query_string .= $var." = '".mysql_real_escape_string($value)."',";
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$arguments = array('id'=>$bid);
						$query_string = substr($query_string, 0, strlen($query_string)-1);
	
						$check = $db->Query("SELECT bid FROM `acp_bans` WHERE bid != ".$bid." AND ".$search['k']." = '".$search['v']."' AND ban_type = '".$search['t']."' AND (".time()." < (ban_created+(ban_length*60)) OR ban_length = 0) LIMIT 1", array(), $config['sql_debug']);
	
						if( $check )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_double_error'].'</span>';
						}
						else
						{
							if( $status_new != $status_old )
							{
								$select = $db->Query("SELECT ban_created, admin_nick, admin_ip, admin_id, admin_uid 
									FROM `".( ($status_old) ? "acp_bans" : "acp_bans_history" )."` 
									WHERE bid = '{id}'".$sqlcond." LIMIT 1", $arguments, $config['sql_debug']);
		
								if( is_array($select) )
								{
									foreach( $select as $obj )
									{
										$query_string .= "
											,admin_nick = '".mysql_real_escape_string($obj->admin_nick)."'
											,admin_ip = '".mysql_real_escape_string($obj->admin_ip)."'
											,admin_id = '".mysql_real_escape_string($obj->admin_id)."'
											,admin_uid = '".mysql_real_escape_string($obj->admin_uid)."'
										";
									}
	
									if( !$status_new )
									{
										$query_string .= ",unban_created = '".time()."',unban_admin_uid = '".$userinfo['uid']."'";
									}
			
									$result = $db->Query("INSERT INTO `".( ($status_new) ? "acp_bans" : "acp_bans_history" )."` SET ".$query_string."", array(), $config['sql_debug']);
			
									if( $result )
									{
										$result = $db->Query("DELETE FROM `".( ($status_old) ? "acp_bans" : "acp_bans_history" )."` WHERE bid = '{id}'", $arguments, $config['sql_debug']);
									}
									else
									{
										$result = false;
									}
								}
								else
									$affected = 0;
							}
							else
							{
								$result = $db->Query("UPDATE `".( ($status_new) ? "acp_bans" : "acp_bans_history" )."` SET ".$query_string." WHERE bid = '{id}'".$sqlcond, $arguments, $config['sql_debug']);
								$affected = $db->Affected();	
							}
	
							if( isset($affected) && !$affected )
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';
							elseif( !$result )
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
							else
							{
								if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_ban", "edit ban id: ".$bid);
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

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$srv = $_POST['srv'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			if( $srv )
			{
				$result = $db->Query("SELECT * FROM `acp_bans_reasons` WHERE address = '".$srv."' ORDER BY id DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}
			else
			{
				$result = $db->Query("SELECT * FROM `acp_bans_reasons` ORDER BY id DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$reasons[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($reasons)) $smarty->assign("reasons", $reasons);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_reasons_list.tpl');

			break;

		case "7":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_reasons', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
				$arguments = array('id'=>$id);
	
				$result = $db->Query("DELETE FROM `acp_bans_reasons` WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_reasons", "delete reason id: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_reason_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "8":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_reasons', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['editid'];
				$address = trim($_POST['address']);
				$reason = (isset($_POST['reason'])) ? trim($_POST['reason']) : "";
				if ($config['charset'] != 'utf-8')
				{
					$reason = iconv('utf-8', $config['charset'], $reason);
				}
	
				if( $reason == '' || $address == '' )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_empty_field'].'</span>';
				}
				else
				{
					$arguments = array('address'=>$address,'reason'=>$reason,'id'=>$id);
					$result = $db->Query("UPDATE `acp_bans_reasons` SET address = '{address}', reason = '{reason}' WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
					if (!$result)
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_reasons", "edit reason: ".$id);
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_reason_success'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "9":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_reasons', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_bans_reasons` WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_reasons", "myltiple delete reasons: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_reason_multiply_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "10":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_reasons', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$address = $_POST['address'];
				unset($_POST['go']);
				$query_string = "";
	
				if( is_array($_POST) )
				{
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "reason":
	
								if( $value )
								{
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
									}
								}
								else
								{
									$error[] = sprintf($translate['empty_field'], $var);
								}
	
								break;
	
							case "address":
	
								if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\:\d[0-9]+)$/i", $value) )
								{
									$error[] = $translate['serverip_not_valid'];
								}
	
								break;
						}
	
						if( isset($_POST[$var]) )
							$query_string .= $var." = '".mysql_real_escape_string($value)."',";
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$query_string = substr($query_string, 0, strlen($query_string)-1);
	
						$result = $db->Query("INSERT INTO `acp_bans_reasons` SET ".$query_string."", $arguments, $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_reasons", "add new reason for server: ".$address);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_reason_success'].'</span>';
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

		case "11":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$status = $_POST['status'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			if( $status )
			{
				$result = $db->Query("SELECT * FROM `acp_bans_subnets` WHERE approved = ".(($status == 1) ? 1 : 0)." ORDER BY id DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}
			else
			{
				$result = $db->Query("SELECT * FROM `acp_bans_subnets` ORDER BY id DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$subnets[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($subnets)) $smarty->assign("subnets", $subnets);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_subnets_list.tpl');

			break;

		case "12":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_subnets', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				unset($_POST['go']);
				$query_string = "";
	
				if( is_array($_POST) )
				{
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "subipaddr":
	
								if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value) )
								{
									$error[] = $translate['ip_not_valid'];
								}
	
								break;
	
							case "bitmask":
	
								if( !is_numeric($value) )
								{
									$value = 31;
								}
	
								break;
						}
	
						if( isset($_POST[$var]) )
							$query_string .= $var." = '".mysql_real_escape_string($value)."',";
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$query_string = substr($query_string, 0, strlen($query_string)-1);
	
						$result = $db->Query("INSERT INTO `acp_bans_subnets` SET ".$query_string."", array(), $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_ban", "add new ban");
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_subnet_success'].'</span>';
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

		case "13":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_subnets', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
				$arguments = array('id'=>$id);
	
				$result = $db->Query("DELETE FROM `acp_bans_subnets` WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_subnets", "delete subnet id: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_subnet_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "14":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_subnets', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_bans_subnets` WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_subnets", "myltiple delete subnets: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_subnet_multiply_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "15":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_subnets', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
				unset($_POST['go'],$_POST['id']);
				$query_string = "";
	
				if( is_array($_POST) )
				{
					foreach( $_POST as $var => $value )
					{
						$value = trim($value);
	
						switch($var)
						{
							case "subipaddr":
	
								if( !preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value) )
								{
									$error[] = $translate['ip_not_valid'];
								}
	
								break;
	
							case "bitmask":
	
								if( !is_numeric($value) )
								{
									$value = 31;
								}
	
								break;
						}
	
						if( isset($_POST[$var]) )
							$query_string .= $var." = '".mysql_real_escape_string($value)."',";
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$query_string = substr($query_string, 0, strlen($query_string)-1);
						$arguments = array('id'=>$id);
						$result = $db->Query("UPDATE `acp_bans_subnets` SET ".$query_string." WHERE id = '{id}'", $arguments, $config['sql_debug']);		
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
						}
						else
						{
							if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_subnet", "edit subnet id: ".$id);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_subnet_success'].'</span>';
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

		case "16":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_subnets', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST["id"];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("UPDATE `acp_bans_subnets` SET approved = IF(approved = 0, 1, 0) WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_subnet", "change status for ban subnet: ".$id);
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

		case "17":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('gb_perm_subnets', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				// delete search bans
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "18":

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
			$status = $_POST['search'];
			unset($_POST['go'], $_POST['search']);
			$sqlconds = 'WHERE 1=1';
			$arguments = array_merge($_POST, array('offset'=>$offset,'limit'=>$limit));

			foreach( $_POST as $var => $value )
			{
				switch($var)
				{
					case "ban_type":

						$sqlconds .= " AND ban_type IN ('{ban_type}')";	
						break;
	
					case "server_ip":

						if( ($srch = array_search(0, $value)) === FALSE )
							$sqlconds .= " AND server_ip IN ('{server_ip}')";
						else
							$sqlconds .= " AND (server_ip IN ('{server_ip}') OR server_name = 'website')";	

						break;
	
					case "startdate":
	
						$sqlconds .= " AND ban_created >= '{startdate}'";
						break;
	
					case "enddate":
	
						$sqlconds .= " AND ban_created <= '{enddate}'";
						break;

					case "srok_start":
	
						if( !$value )
						{
							$sqlconds .= " AND ban_length = 0";
						}
						else
						{
							$sqlconds .= " AND ban_length >= {srok_start}";
						}
						break;
	
					case "srok_end":
	
						$sqlconds .= " AND ban_length <= {srok_end} AND ban_length != 0";
						break;
	
					case "player_nick":
					case "admin_nick":
	
						if( $config['charset'] != 'utf-8' )
						{
							$value = iconv('utf-8', $config['charset'], $value);
						}

						if( $arguments[$var]{0} != '!' )
							$sqlconds .= " AND ".$var." LIKE '%{".$var."}%'";
						else
						{
							$arguments[$var] = substr($arguments[$var], 1);
							$sqlconds .= " AND ".$var." = '{".$var."}'";
						}
						break;
	
					case "ban_reason":
					case "player_ip":
					case "admin_ip":
					case "cookie_ip":
					case "player_id":
					case "admin_id":
	
						$sqlconds .= " AND ".$var." LIKE '%{".$var."}%'";
						break;
				}
			}

			if( $status )
			{
				$result = $db->Query("SELECT * FROM ".(($status == 1) ? '`acp_bans`' : '`acp_bans_history`')." $sqlconds ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}
			else
			{
				$result = $db->Query("SELECT * FROM (
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, unban_created, unban_admin_uid FROM `acp_bans_history` $sqlconds)
						UNION ALL
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, NULL, NULL FROM `acp_bans` $sqlconds)
					) temp ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}

			if( is_array($result) )
			{
				include(INCLUDE_PATH . 'class.SypexGeo.php');
				$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
				$current_time = time();

				foreach( $result as $obj )
				{
					$player_country_code = strtolower($SxGeo->getCountry($obj->player_ip));
					$obj->country = (file_exists("images/flags/".$player_country_code.".gif")) ? "<img style='vertical-align: middle' src='acpanel/images/flags/".$player_country_code.".gif' alt='' />" : "<img style='vertical-align: middle' src='acpanel/images/flags/err.gif' alt='' />";

					if( isset($obj->unban_admin_uid) && $obj->unban_admin_uid )
					{
						$obj->ban_remain = $translate['ban_removed'];
					}
					else if( $obj->ban_length && ($obj->ban_length*60 + $obj->ban_created - $current_time) <= 0 )
					{
						$obj->ban_remain = $translate['ban_expired'];
					}
					else
					{
						$obj->ban_remain = "";
					}

					$obj->ban_length = ($obj->ban_length == 0) ? $translate['permanent'] : compacttime($obj->ban_length*60, $config['gb_length_format']);
					$obj->ban_created = ($obj->ban_created > 0) ? get_datetime($obj->ban_created, $config['date_format']) : '-';
					$obj->unban_created = (!isset($obj->unban_created)) ? null : $obj->unban_created;
					$obj->unban_created = (!is_null($obj->unban_created)) ? get_datetime($obj->unban_created, $config['date_format']) : 0;
					$bans[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($cat_users)) $smarty->assign("cat_users", $cat_users);
			if(isset($cat_user_edit)) $smarty->assign("cat_user_edit", $cat_user_edit);
			if(isset($bans)) $smarty->assign("bans", $bans);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_search_load.tpl');

			break;

		case "19":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$status = $_POST['status'];
			$search = $_POST['search'];
			$search_type = $_POST['search_type'];
			$server = $_POST['server'];
			$admin = $_POST['admin'];

			$search_cond = "";
		
			if( $search )
			{	
				$search_cond = ((!$search_type) ? "player_nick" : (($search_type == 1) ? "player_ip" : "player_id"))." LIKE '%{search}%'";
			}

			if( $server )
			{
				$search_cond = (!$search_cond) ? "server_ip = '".$server."'" : $search_cond." AND server_ip = '".$server."'";
			}
	
			if( $admin )
			{
				$search_cond = (!$search_cond) ? "admin_uid = ".$admin : $search_cond." AND admin_uid = ".$admin;
			}

			$arguments = array('offset' => $offset, 'limit' => $limit, 'time' => time(), 'search' => $search);
			if( $status == 1 )
			{
				$result = $db->Query("SELECT * FROM `acp_bans` WHERE ({time} < (ban_created+(ban_length*60)) OR ban_length = 0)".(($search_cond) ? " AND ".$search_cond : "")." ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}
			else if( $status == 2 )
			{
				$result = $db->Query("SELECT * FROM (
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, unban_created, unban_admin_uid FROM `acp_bans_history`".(($search_cond) ? " WHERE ".$search_cond : "").")
						UNION ALL
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, NULL, NULL FROM `acp_bans` WHERE {time} > (ban_created-1+(ban_length*60)) AND ban_length > 0".(($search_cond) ? " AND ".$search_cond : "").")
					) temp ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}
			else
			{
				$result = $db->Query("SELECT * FROM (
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, unban_created, unban_admin_uid FROM `acp_bans_history`".(($search_cond) ? " WHERE ".$search_cond : "").")
						UNION ALL
						(SELECT bid, ban_created, player_nick, player_ip, ban_reason, ban_length, admin_nick, admin_uid, NULL, NULL FROM `acp_bans`".(($search_cond) ? " WHERE ".$search_cond : "").")
					) temp ORDER BY ban_created DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
			}

			if( is_array($result) )
			{
				include(INCLUDE_PATH . 'class.SypexGeo.php');
				$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
				$current_time = time();

				foreach( $result as $obj )
				{
					$player_country_code = strtolower($SxGeo->getCountry($obj->player_ip));
					$obj->country = (file_exists("images/flags/".$player_country_code.".gif")) ? "<img style='vertical-align: middle' src='acpanel/images/flags/".$player_country_code.".gif' alt='' />" : "<img style='vertical-align: middle' src='acpanel/images/flags/err.gif' alt='' />";

					if( isset($obj->unban_admin_uid) && $obj->unban_admin_uid )
					{
						$obj->ban_remain = $translate['ban_removed'];
					}
					else if( $obj->ban_length && ($obj->ban_length*60 + $obj->ban_created - $current_time) <= 0 )
					{
						$obj->ban_remain = $translate['ban_expired'];
					}
					else
					{
						$obj->ban_remain = "";
					}

					$obj->ban_length = ($obj->ban_length == 0) ? $translate['permanent'] : compacttime($obj->ban_length*60, $config['gb_length_format']);
					$obj->ban_created = ($obj->ban_created > 0) ? get_datetime($obj->ban_created, $config['date_format']) : '-';
					$obj->unban_created = (!isset($obj->unban_created)) ? null : $obj->unban_created;
					$obj->unban_created = (!is_null($obj->unban_created)) ? get_datetime($obj->unban_created, $config['date_format']) : 0;
					$bans[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("hide_admins", $config['gb_display_admin']);
			if(isset($bans)) $smarty->assign("bans", $bans);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_public_players_list.tpl');

			break;

		case "20":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT * FROM `acp_bans_subnets` WHERE approved = 1 ORDER BY id DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$subnets[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($subnets)) $smarty->assign("subnets", $subnets);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_public_subnets_list.tpl');

			break;

		case "21":

			// 1 - servers stats
			// 2 - reasons stats
			// 3 - length stats
			// 4 - subnets stats
			// 5 - country stats
			// 6 - admins stats

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$type = $_POST['stats'];
			$stats = array();

			$arguments = array('offset'=>$offset,'limit'=>$limit);

			switch($type)
			{
				case 1:
					$field = $translate['bans_server_name'];
					$result = $db->Query("SELECT  count(*) AS count, server_name AS value, server_ip FROM (
						(SELECT server_name, server_ip FROM `acp_bans`) UNION ALL
						(SELECT server_name, server_ip FROM `acp_bans_history`)) temptable GROUP BY server_name ORDER BY count DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
		
					break;
		
				case 2:
					$field = $translate['bans_reason'];
					$result = $db->Query("SELECT  count(*) AS count, ban_reason AS value FROM (
						(SELECT ban_reason FROM `acp_bans`) UNION ALL
						(SELECT ban_reason FROM `acp_bans_history`)) temptable GROUP BY ban_reason ORDER BY count DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
		
					break;
		
				case 3:
					$field = $translate['bans_length'];
					$result = $db->Query("SELECT  count(*) AS count, ban_length AS value FROM (
						(SELECT ban_length FROM `acp_bans`) UNION ALL
						(SELECT ban_length FROM `acp_bans_history`)) temptable GROUP BY ban_length ORDER BY count DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
		
					break;
		
				case 4:
					$field = $translate['bans_subnet'];
					$result = $db->Query("SELECT  count(*) AS count, REPLACE(player_ip,SUBSTRING_INDEX(player_ip,'.',-2),'0.0') AS value FROM (
						(SELECT player_ip FROM `acp_bans`) UNION ALL
						(SELECT player_ip FROM `acp_bans_history`)) temptable GROUP BY value ORDER BY count DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
		
					break;
		
				case 5:
					$field = $translate['bans_country'];
					$result = $db->Query("SELECT  count(*) AS count, CONCAT(SUBSTRING_INDEX(player_ip,'.',2), '.0.0') AS value FROM (
						(SELECT player_ip FROM `acp_bans` WHERE inet_aton(player_ip) is not null) UNION ALL
						(SELECT player_ip FROM `acp_bans_history` WHERE inet_aton(player_ip) is not null)) temptable GROUP BY value ORDER BY count DESC", array(), $config['sql_debug']);
		
					if( is_array($result) )
					{
						function compare($v1, $v2)
						{
							if( $v1['count'] == $v2['count'] ) return 0;
							return ($v1['count'] < $v2['count']) ? 1 : -1;
						}

						include(INCLUDE_PATH . 'class.SypexGeo.php');
						$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
						$geoCountries = file_get_contents(SCRIPT_PATH . 'geoip/CountryNames.txt');
						$geoCountries = unserialize($geoCountries);
						$array_country = array();
		
						foreach( $result as $obj )
						{
							$code = $SxGeo->getCountry($obj->value);

							if( isset($array_country[$code]) )
								$array_country[$code]['count'] = $array_country[$code]['count'] + $obj->count;
							else
							{
								$flag = strtolower($code).".gif";
								$flag = (file_exists("images/flags/".$flag)) ? "<img style='vertical-align: middle' src='acpanel/images/flags/".$flag."' alt='' />" : "<img style='vertical-align: middle' src='acpanel/images/flags/err.gif' alt='' />";
								$countryName = (isset($geoCountries[$code])) ? $geoCountries[$code] : "UNDEFINED";
								$array_country[$code] = array('value'=>$countryName, 'count'=>$obj->count, 'flag'=>$flag);
							}
						}

						usort($array_country, 'compare');
						$stats = array_slice($array_country, $offset, $limit);
					}

					break;
		
				case 6:
					$field = $translate['bans_admin'];
					$result = $db->Query("SELECT  count(*) AS count, temptable.admin_nick, temptable.admin_uid AS val_uid, u.username AS value FROM (
						(SELECT admin_nick, admin_uid FROM `acp_bans` WHERE admin_uid != 0) UNION ALL
						(SELECT admin_nick, admin_uid FROM `acp_bans_history` WHERE admin_uid != 0)) temptable 
						LEFT JOIN `acp_users` u ON u.uid = temptable.admin_uid 
						GROUP BY val_uid ORDER BY count DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
		
					break;
			}

			if( $type != 5 )
			{
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						if( $type == 1 )
						{
							if( !$obj->server_ip && $obj->value == "website" ) $obj->value = $translate['ban_website'];
						}
						elseif( $type == 3 )
						{
							$obj->value = ($obj->value == 0) ? $translate['ban_permanently'] : compacttime($obj->value*60, 'dddd hhhh mmmm');
						}
						elseif( $type == 6 )
						{
							if( !$obj->value ) $obj->value = $obj->admin_nick;
						}
						$stats[] = (array)$obj;
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

			$smarty->assign("field", $field);
			if(isset($stats)) $smarty->assign("stats", $stats);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_public_stats_list.tpl');

			break;

		case "22":

			$cache_prefix = 'bans_topstats';
			$cache_content = false;
			$cache_need_create = false;
			$stats = array();
		
			if( $config['gb_topstats_cache'] )
			{
				include(INCLUDE_PATH . 'functions.servers.php');
				$cache_content = get_cache($cache_prefix, $config['gb_topstats_cache']*60, "");
				$cache_need_create = ($cache_content !== false) ? false : true;
			}
		
			if( $cache_content === FALSE )
			{
				$arguments = array('max' => $config['gb_topstats_max']);
		
				// CREATE SERVERS STATS
				$result = $db->Query("SELECT count(*) AS count, server_name AS value, server_ip FROM (
					(SELECT server_name, server_ip FROM `acp_bans`) UNION ALL
					(SELECT server_name, server_ip FROM `acp_bans_history`)) temptable GROUP BY server_name ORDER BY count DESC LIMIT {max}", $arguments, $config['sql_debug']);
		
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						if( !$obj->server_ip && $obj->value == "website" ) $obj->value = $translate['ban_website'];
						$stats[1][] = (array)$obj;
					}
				}
		
				// CREATE REASONS STATS
				$result = $db->Query("SELECT count(*) AS count, ban_reason AS value FROM (
					(SELECT ban_reason FROM `acp_bans`) UNION ALL
					(SELECT ban_reason FROM `acp_bans_history`)) temptable GROUP BY ban_reason ORDER BY count DESC LIMIT {max}", $arguments, $config['sql_debug']);
		
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						$stats[2][] = (array)$obj;
					}
				}
		
				// CREATE LENGTHS STATS
				$result = $db->Query("SELECT count(*) AS count, ban_length AS value FROM (
					(SELECT ban_length FROM `acp_bans`) UNION ALL
					(SELECT ban_length FROM `acp_bans_history`)) temptable GROUP BY ban_length ORDER BY count DESC LIMIT {max}", $arguments, $config['sql_debug']);
		
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						$obj->value = ($obj->value == 0) ? $translate['ban_permanently'] : compacttime($obj->value*60, 'dddd hhhh mmmm');
						$stats[3][] = (array)$obj;
					}
				}
		
				// CREATE SUBNETS STATS
				$result = $db->Query("SELECT count(*) AS count, REPLACE(player_ip,SUBSTRING_INDEX(player_ip,'.',-2),'0.0') AS value FROM (
					(SELECT player_ip FROM `acp_bans`) UNION ALL
					(SELECT player_ip FROM `acp_bans_history`)) temptable GROUP BY value ORDER BY count DESC LIMIT {max}", $arguments, $config['sql_debug']);
		
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						$stats[4][] = (array)$obj;
					}
				}

				// CREATE COUNTRIES STATS
				$result = $db->Query("SELECT  count(*) AS count, CONCAT(SUBSTRING_INDEX(player_ip,'.',2), '.0.0') AS value FROM (
					(SELECT player_ip FROM `acp_bans`) UNION ALL
					(SELECT player_ip FROM `acp_bans_history`)) temptable GROUP BY value ORDER BY count DESC", array(), $config['sql_debug']);
	
				if( is_array($result) )
				{
					function compare($v1, $v2)
					{
						if( $v1['count'] == $v2['count'] ) return 0;
						return ($v1['count'] < $v2['count']) ? 1 : -1;
					}

					include(INCLUDE_PATH . 'class.SypexGeo.php');
					$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
					$geoCountries = file_get_contents(SCRIPT_PATH . 'geoip/CountryNames.txt');
					$geoCountries = unserialize($geoCountries);
					$array_country = array();
	
					foreach( $result as $obj )
					{
						$code = $SxGeo->getCountry($obj->value);

						if( isset($array_country[$code]) )
							$array_country[$code]['count'] = $array_country[$code]['count'] + $obj->count;
						else
						{
							$flag = strtolower($code).".gif";
							$flag = (file_exists("images/flags/".$flag)) ? "<img style='vertical-align: middle' src='acpanel/images/flags/".$flag."' alt='' />" : "<img style='vertical-align: middle' src='acpanel/images/flags/err.gif' alt='' />";
							$countryName = (isset($geoCountries[$code])) ? $geoCountries[$code] : "UNDEFINED";
							$array_country[$code] = array('value'=>$countryName, 'count'=>$obj->count, 'flag'=>$flag);
						}
					}

					usort($array_country, 'compare');
					$stats[5] = array_slice($array_country, 0, $config['gb_topstats_max']);
				}
		
				// CREATE ADMINS STATS
				$result = $db->Query("SELECT count(*) AS count, temptable.admin_nick, temptable.admin_uid AS val_uid, u.username AS value FROM (
					(SELECT admin_nick, admin_uid FROM `acp_bans` WHERE admin_uid != 0) UNION ALL
					(SELECT admin_nick, admin_uid FROM `acp_bans_history` WHERE admin_uid != 0)) temptable 
					LEFT JOIN `acp_users` u ON u.uid = temptable.admin_uid 
					GROUP BY val_uid ORDER BY count DESC LIMIT {max}", $arguments, $config['sql_debug']);
		
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						if( !$obj->value ) $obj->value = $obj->admin_nick;
						$stats[6][] = (array)$obj;
					}
				}

				if( $cache_need_create )
					create_cache($cache_prefix, serialize($stats), "");
			}
			else
			{
				$stats = unserialize($cache_content);
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($stats)) $smarty->assign("stats", $stats);
			if(isset($error)) $smarty->assign("iserror", $error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gamebans_public_stats_top.tpl');

			break;

		case "23":

			$ip = $_POST['ip'];
			$mask = $_POST['mask'];
			$return = array('range' => '-', 'cnt' => '-');

			if( preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $ip) && is_numeric($mask) && $mask <= 32 && $mask >= 0 )
			{
				include_once(INCLUDE_PATH . 'functions.subnets.php');

				$bin_nmask = cdrtobin($mask);
				$bin_wmask = binnmtowm($bin_nmask);

				$bin_host = dqtobin($ip);
				$bin_bcast = (str_pad(substr($bin_host,0,$mask),32,1)); // last subnet address (reserved for broadcasting)
				$bin_net = (str_pad(substr($bin_host,0,$mask),32,0)); // first subnet address (reserved as a unique address subnet)
				$bin_first = (str_pad(substr($bin_net,0,31),32,1)); // the first usable subnet address
				$bin_last = (str_pad(substr($bin_bcast,0,31),32,0)); // the last usable subnet address
				$host_total = (bindec(str_pad("",(32-$mask),1)) + 1); // the number of available addresses in a subnet

				$return['range'] = bintodq($bin_net).' - '.bintodq($bin_bcast);
				$return['cnt'] = $host_total;
			}

			echo json_encode($return);
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

						$query = $db->Query("SELECT ban_created, time, count(bid) AS cnt 
							FROM (
								(SELECT bid, ban_created, FROM_UNIXTIME(ban_created, '%Y-%m-%d') AS time FROM `acp_bans_history` WHERE ban_created > ".$startTime.")
								UNION ALL
								(SELECT bid, ban_created, FROM_UNIXTIME(ban_created, '%Y-%m-%d') AS time FROM `acp_bans` WHERE ban_created > ".$startTime.")
							) temp GROUP BY time LIMIT 7", array());

						$i = 0;
						while( $i < 7 )
						{
							$index = $startTime."000";
							$arrOut["addban"][$index] = 0;
							$startTime = $startTime + 86400;
							$i++;
						}
						break;

					case "y":

						$currDateString = $currYear."-".$currMonth."-01 00:00:00";
						$startTime = strtotime("1 year ago", strtotime($currDateString));
						$startTime = strtotime("next month", $startTime);

						$query = $db->Query("SELECT ban_created, time, count(bid) AS cnt 
							FROM (
								(SELECT bid, ban_created, FROM_UNIXTIME(ban_created, '%Y-%m') AS time FROM `acp_bans_history` WHERE ban_created > ".$startTime.")
								UNION ALL
								(SELECT bid, ban_created, FROM_UNIXTIME(ban_created, '%Y-%m') AS time FROM `acp_bans` WHERE ban_created > ".$startTime.")
							) temp GROUP BY time LIMIT 12", array());

						$i = 0;
						while( $i < 12 )
						{
							$index = $startTime."000";
							$arrOut["addban"][$index] = 0;
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
						$index = $time."000";

						if( isset($arrOut["addban"][$index]) )
							$arrOut["addban"][$index] = $obj->cnt;
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

		default:

			die("Hacking Attempt");
	}
}

?>
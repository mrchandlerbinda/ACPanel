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

	if(is_array($array_cfg)) {
		foreach ($array_cfg as $obj){
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();
	}

	include(INCLUDE_PATH . 'functions.main.php');
	$langs = create_lang_list();

	unset($translate);
	$arguments = array('lp_name'=>'p_users.tpl','lang'=>get_language(1));
	$tr_result = $db->Query("
		SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`
		LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_name='{lp_name}'
		WHERE acp_lang_pages.lp_id = acp_lang_words.lw_page OR acp_lang_words.lw_page = '0'
	", $arguments, $config['sql_debug']);
	if(is_array($tr_result)) {
		foreach ($tr_result as $obj){
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}

	include(INCLUDE_PATH . '_auth.php');

	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create list
	// 2 - add item
	// 3 - del item
	// 4 - multiply del items
	// 5 - edit item
	// 6 - search results
	// 7 - search to remove

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$group = $_POST['group'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT * FROM `acp_users`".(($group) ? ' WHERE usergroupid = '.$group : '')." LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$obj->reg_date = ($obj->reg_date > 0) ? get_datetime($obj->reg_date, $config['date_format']) : '-';
					$obj->last_visit = ($obj->last_visit > 0) ? get_datetime($obj->last_visit, $config['date_format']) : '-';
					$users[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("colums","6");
			if(isset($users)) $smarty->assign("users",$users);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_users_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_users', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				unset($_POST['go'],$array_keys,$array_values);
				if( $ext_auth_type )
				{
					$updateEXTarray = array();
				}
	
				if( is_array($_POST) )
				{
					date_default_timezone_set('UTC');
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "password":
	
								if( $value == '' )
								{
									$error[] = $translate['pass_not_empty'];
								}
								else
								{
									switch($ext_auth_type)
									{
										case "xf":
											$updateEXTarray['password'] = $value;
									}
									$value = md5($value);
								}
								break;
	
							case "username":
	
								if ($config['charset'] != 'utf-8')
								{
									$value = iconv('utf-8', $config['charset'], $value);
								}
	
								$arguments = array('username'=>$value);
								$result = $db->Query("SELECT uid FROM `acp_users` WHERE username = '{username}'", $arguments, $config['sql_debug']);
								if ($result)
								{
									$error[] = $translate['user_already_exist'];
								}
								else
								{
									switch($ext_auth_type)
									{
										case "xf":
											$updateEXTarray['username'] = $value;
									}
								}
	
								break;
	
							case "reg_date":
	
								if ($value != '')
								{
									$value = strtotime($value);
									$value = get_datetime($value, false, true);
								}
								else
								{
									$value = time();
								}
	
								switch($ext_auth_type)
								{
									case "xf":
										$updateEXTarray['register_date'] = $value;
								}
	
								break;
	
							case "last_visit":
	
								if ($value != '')
								{
									$value = strtotime($value);
									$value = get_datetime($value, false, true);
								}
								break;
	
							case "usergroupid":
	
								switch($ext_auth_type)
								{
									case "xf":
										$updateEXTarray['user_group_id'] = $value;
								}
								break;
	
							case "timezone":
	
								switch($ext_auth_type)
								{
									case "xf":
										$updateEXTarray['timezone'] = $value;
								}
								break;
	
							case "icq":
	
								if( !is_numeric($value) && strlen($value) )
								{
									$error[] = $translate['isq_not_valid'];
								}
								else
								{
									switch($ext_auth_type)
									{
										case "xf":
											$updateEXTarray['icq'] = $value;
									}
								}
								break;
	
							case "mail":
	
								if (!preg_match("/^[0-9a-z_\.\-]+@[0-9a-z_\-^\.]+\.[a-z]{2,6}$/i", $value))
								{
									$error[] = $translate['email_not_valid'];
								}
								else
								{
									$arguments = array('mail'=>$value);
									$result = $db->Query("SELECT uid FROM `acp_users` WHERE mail = '{mail}'", $arguments, $config['sql_debug']);
									if ($result)
									{
										$error[] = $translate['email_already_exists'];
									}
									else
									{
										switch($ext_auth_type)
										{
											case "xf":
												$updateEXTarray['email'] = $value;
										}
									}
								}
								break;
	
							case "ipaddress":
	
								if ($value != '')
								{
									if (!preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value))
									{
										$error[] = $translate['ip_not_valid'];
									}
								}
	
								break;
						}
	
						$array_keys[] = $var;
						$array_values[] = mysql_real_escape_string($value);
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						switch($ext_auth_type)
						{
							case "xf":
								$result = false;
								if( !empty($updateEXTarray) )
								{
									$userArray = $xf->createUser($updateEXTarray['username'], $updateEXTarray['email'], $updateEXTarray['password'], "valid", $updateEXTarray);
									if( $userArray['user_id'] )
									{
										$result = true;
									}
								}
								break;
	
							default:
								$result = $db->Query("INSERT INTO `acp_users` (".implode(',',$array_keys).") VALUES ('".implode('\',\'',$array_values)."')", array(), $config['sql_debug']);
								break;
						}
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_usergroups", "add user: ".$_POST['username']);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
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
			$userPerm = $permClass->getPermissions('general_perm_users', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
				$arguments = array('id'=>$id);
	
				switch($ext_auth_type)
				{
					case "xf":
	
						$result = $xf->deleteUser($id);
						break;
	
					default:
	
						$result = $db->Query("DELETE FROM `acp_users` WHERE uid = '{id}'", $arguments, $config['sql_debug']);		
						break;
				}
	
				if( $result )
				{
					$product_GA = getProduct("gameAccounts");
					if( !empty($product_GA) )
					{
						$result_account = $db->Query("DELETE FROM `acp_players` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
	
						if( $result_account )
						{
							$result_mask = $db->Query("DELETE FROM `acp_access_mask_players` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
						}
					}
	
					if( $server_id = $db->Query("SELECT id FROM `acp_servers` WHERE userid = '{id}'", $arguments, $config['sql_debug']) )
					{
						$result_del = $db->Query("DELETE `acp_servers`, `acp_servers_viewed` FROM `acp_servers` INNER JOIN `acp_servers_viewed` 
							WHERE acp_servers.id = acp_servers_viewed.server_id 
		  					AND acp_servers.userid = '{id}'", $arguments, $config['sql_debug']);
	
						$product = getProduct("ratingServers");
						if( !empty($product) )
						{
							$result = $db->Query("DELETE FROM `acp_servers_rating_temp` WHERE server_id = ".$server_id, array(), $config['sql_debug']);
						}
					}
	
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_users", "delete user id: ".$id);
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
			$userPerm = $permClass->getPermissions('general_perm_users', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				switch($ext_auth_type)
				{
					case "xf":
	
						foreach( $ids as $k => $v )
						{
							if( !$xf->deleteUser($v) )
							{
								unset($ids[$k]);
							}
						}
						$result = true;
	
						break;
	
					default:
	
						$arguments = array('ids'=>$ids);
						$result = $db->Query("DELETE FROM `acp_users` WHERE uid IN ('{ids}')", $arguments, $config['sql_debug']);	
						break;
				}			
	
				if( $result )
				{
					$product_GA = getProduct("gameAccounts");
					if( !empty($product_GA) )
					{
						$arguments = array('ids'=>$ids);
						$result_account = $db->Query("DELETE FROM `acp_players` WHERE userid IN ('{ids}')", $arguments, $config['sql_debug']);
	
						if( $result_account )
						{
							$result_mask = $db->Query("DELETE FROM `acp_access_mask_players` WHERE userid IN ('{ids}')", $arguments, $config['sql_debug']);
						}
					}
	
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_users", "myltiple delete users: ".count($ids));
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

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_users', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['uid'];
				unset($_POST['go'],$_POST['uid']);
				$query_string = "";
				if( $ext_auth_type )
				{
					$updateEXTarray = array();
				}
	
				if( is_array($_POST) )
				{
					date_default_timezone_set('UTC');
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "password":
	
								if ($value != '')
								{
									switch($ext_auth_type)
									{
										case "xf":
											if( $value != '')
												$updateEXTarray['password'] = $value;
									}
									$value = md5($value);
								}
								break;
	
							case "username":
	
								if ($config['charset'] != 'utf-8')
								{
									$value = iconv('utf-8', $config['charset'], $value);
								}
	
								$arguments = array('username'=>$value, 'id'=>$id);
								$result = $db->Query("SELECT uid FROM `acp_users` WHERE username = '{username}' AND uid != '{id}'", $arguments, $config['sql_debug']);
								if ($result)
								{
									$error[] = $translate['user_already_exist'];
								}
								else
								{
									switch($ext_auth_type)
									{
										case "xf":
											$updateEXTarray['username'] = $value;
									}
								}
	
								break;
	
							case "reg_date":
							case "last_visit":
	
								if( $value != '' )
								{
									$value = strtotime($value);
									$value = get_datetime($value, false, true);
								}
								break;
	
							case "mail":
	
								if( !preg_match("/^[0-9a-z_\.\-]+@[0-9a-z_\-^\.]+\.[a-z]{2,6}$/i", $value) )
								{
									$error[] = $translate['email_not_valid'];
								}
								else
								{
									$arguments = array('mail'=>$value, 'id'=>$id);
									$result = $db->Query("SELECT uid FROM `acp_users` WHERE mail = '{mail}' AND uid != '{id}'", $arguments, $config['sql_debug']);
									if( $result )
									{
										$error[] = $translate['email_already_exists'];
									}
									else
									{
										switch($ext_auth_type)
										{
											case "xf":
												$updateEXTarray['email'] = $value;
										}
									}
								}
								break;
	
							case "ipaddress":
	
								if ($value != '')
								{
									if (!preg_match("/^(((0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5]))\.){3}(0?(0?|[1-9])\d|1\d\d|2([0-4]\d|5[0-5])))$/i", $value))
									{
										$error[] = $translate['ip_not_valid'];
									}
								}
								break;
	
							case "usergroupid":
	
								switch($ext_auth_type)
								{
									case "xf":
										$updateEXTarray['user_group_id'] = $value;
								}
								break;
	
							case "timezone":
	
								switch($ext_auth_type)
								{
									case "xf":
										$updateEXTarray['timezone'] = $value;
								}
								break;
	
							case "icq":
	
								if( !is_numeric($value) && strlen($value) )
								{
									$error[] = $translate['isq_not_valid'];
								}
								else
								{
									switch($ext_auth_type)
									{
										case "xf":
											$updateEXTarray['icq'] = $value;
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						switch($ext_auth_type)
						{
							case "xf":
								$result = false;
								if( !empty($updateEXTarray) )
								{
									$userArray = $xf->setUserData($id, $updateEXTarray);
									if( $userArray['user_id'] )
									{
										$result = true;
									}
								}
								break;
	
							default:
								$query_string = substr($query_string, 0, strlen($query_string)-1);
								$arguments = array('id'=>$id);
								$result = $db->Query("UPDATE `acp_users` SET ".$query_string." WHERE uid = '{id}'", $arguments, $config['sql_debug']);
								break;
						}
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_users", "edit user id: ".$id);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
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

			$group = (isset($_POST['user_group'])) ? $_POST['user_group'] : '';
			$reg_date_begin = (isset($_POST['reg_date_begin'])) ? $_POST['reg_date_begin'] : '';
			$reg_date_end = (isset($_POST['reg_date_end'])) ? $_POST['reg_date_end'] : '';
			$last_date_begin = (isset($_POST['last_date_begin'])) ? $_POST['last_date_begin'] : '';
			$last_date_end = (isset($_POST['last_date_end'])) ? $_POST['last_date_end'] : '';
			$user_login = (isset($_POST['user_login'])) ? $_POST['user_login'] : '';
			$reg_ip = (isset($_POST['reg_ip'])) ? $_POST['reg_ip'] : '';
			$user_hid = (isset($_POST['user_hid'])) ? $_POST['user_hid'] : '';
			$user_mail = (isset($_POST['user_mail'])) ? $_POST['user_mail'] : '';
			$user_icq = (isset($_POST['user_icq'])) ? $_POST['user_icq'] : '';

			$sqlconds = 'WHERE 1=1';

			if ($group) { $sqlconds .= " AND usergroupid = '{group}'"; }
			if ($user_login) { $sqlconds .= " AND username LIKE '%{user_login}%'"; }
			if ($user_hid) { $sqlconds .= " AND hid LIKE '%{user_hid}%'"; }
			if ($user_mail) { $sqlconds .= " AND mail LIKE '%{user_mail}%'"; }
			if ($user_icq) { $sqlconds .= " AND icq LIKE '%{user_icq}%'"; }
			if ($reg_ip) { $sqlconds .= " AND ipaddress LIKE '%{reg_ip}%'"; }
			if ($reg_date_begin) { $sqlconds .= " AND reg_date >= '{reg_date_begin}'"; }
			if ($reg_date_end) { $sqlconds .= " AND reg_date <= '{reg_date_end}'"; }
			if ($last_date_begin) { $sqlconds .= " AND last_visit >= '{last_date_begin}'"; }
			if ($last_date_end) { $sqlconds .= " AND last_visit <= '{last_date_end}'"; }

			date_default_timezone_set('UTC');
			$arguments = array('offset'=>$offset,'limit'=>$limit,'group'=>$group,'user_login'=>$user_login,'user_hid'=>$user_hid,'user_mail'=>$user_mail,'user_icq'=>$user_icq,'reg_ip'=>$reg_ip,'reg_date_begin'=>get_datetime(strtotime($reg_date_begin), false, true),'reg_date_end'=>get_datetime(strtotime($reg_date_end), false, true),'last_date_begin'=>get_datetime(strtotime($last_date_begin), false, true),'last_date_end'=>get_datetime(strtotime($last_date_end), false, true));
			$result = $db->Query("SELECT * FROM `acp_users` $sqlconds ORDER BY `uid` DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{					$obj->reg_date = ($obj->reg_date > 0) ? get_datetime($obj->reg_date, $config['date_format']) : '-';
					$obj->last_visit = ($obj->last_visit > 0) ? get_datetime($obj->last_visit, $config['date_format']) : '-';

					$array_users[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($array_users)) $smarty->assign("array_users",$array_users);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_users_search_load.tpl');

			break;

		case "7":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_users', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$group = (isset($_POST['user_group'])) ? $_POST['user_group'] : '';
				$reg_date_begin = (isset($_POST['reg_date_begin'])) ? $_POST['reg_date_begin'] : '';
				$reg_date_end = (isset($_POST['reg_date_end'])) ? $_POST['reg_date_end'] : '';
				$last_date_begin = (isset($_POST['last_date_begin'])) ? $_POST['last_date_begin'] : '';
				$last_date_end = (isset($_POST['last_date_end'])) ? $_POST['last_date_end'] : '';
				$user_login = (isset($_POST['user_login'])) ? $_POST['user_login'] : '';
				$reg_ip = (isset($_POST['reg_ip'])) ? $_POST['reg_ip'] : '';
				$user_hid = (isset($_POST['user_hid'])) ? $_POST['user_hid'] : '';
				$user_mail = (isset($_POST['user_mail'])) ? $_POST['user_mail'] : '';
				$user_icq = (isset($_POST['user_icq'])) ? $_POST['user_icq'] : '';
	
				$sqlconds = 'WHERE 1=1';
	
				if ($group != 'all') { $sqlconds .= " AND usergroupid = '{group}'"; }
				if ($user_login) { $sqlconds .= " AND username LIKE '%{user_login}%'"; }
				if ($user_hid) { $sqlconds .= " AND hid LIKE '%{user_hid}%'"; }
				if ($user_mail) { $sqlconds .= " AND mail LIKE '%{user_mail}%'"; }
				if ($user_icq) { $sqlconds .= " AND icq LIKE '%{user_icq}%'"; }
				if ($reg_ip) { $sqlconds .= " AND ipaddress LIKE '%{reg_ip}%'"; }
				if ($reg_date_begin) { $sqlconds .= " AND reg_date >= '{reg_date_begin}'"; }
				if ($reg_date_end) { $sqlconds .= " AND reg_date <= '{reg_date_end}'"; }
				if ($last_date_begin) { $sqlconds .= " AND last_visit >= '{last_date_begin}'"; }
				if ($last_date_end) { $sqlconds .= " AND last_visit <= '{last_date_end}'"; }

				date_default_timezone_set('UTC');
				$arguments = array('group'=>$group,'user_login'=>$user_login,'user_hid'=>$user_hid,'user_mail'=>$user_mail,'user_icq'=>$user_icq,'reg_ip'=>$reg_ip,'reg_date_begin'=>get_datetime(strtotime($reg_date_begin), false, true),'reg_date_end'=>get_datetime(strtotime($reg_date_end), false, true),'last_date_begin'=>get_datetime(strtotime($last_date_begin), false, true),'last_date_end'=>get_datetime(strtotime($last_date_end), false, true));
				$result = $db->Query("DELETE FROM `acp_users` $sqlconds", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$delnum = $db->Affected();
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_users", "delete users: ".$delnum);
	
					if( $delnum > 0 )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.$delnum.'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_null'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_error'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		default:

			die("Hacking Attempt");
	}
}

?>
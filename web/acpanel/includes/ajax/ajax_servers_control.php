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
	$filter = "lp_name='p_servers_control.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
	$arguments = array('lang'=>get_language(1));
	$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
	if(is_array($tr_result)) {
		foreach ($tr_result as $obj){
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}

	include(INCLUDE_PATH . '_auth.php');

	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create list
	// 2 - add server
	// 3 - del server
	// 4 - multiply del servers
	// 5 - pasted hostname
	// 6 - load server info
	// 7 - edit server
	// 8 - change server status

	switch($_POST['go']) {

		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT s.id, s.active, s.address, s.hostname, s.gametype, s.rating, s.userid, u.username FROM `acp_servers` s, `acp_users` u 
				WHERE u.uid = s.userid ORDER BY s.id LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				include(INCLUDE_PATH . 'class.SypexGeo.php');
				$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');

				foreach ($result as $obj)
				{
					$lists = explode(":", $obj->address);
					$ip = $lists[0];
					$port = $lists[1];

					$server_country_code = strtolower($SxGeo->getCountry($ip));
					clearstatcache();

					if(file_exists(ROOT_PATH . "images/flags/".$server_country_code.".gif"))
					{
						$server_country = "<img style='vertical-align: middle' src='acpanel/images/flags/".$server_country_code.".gif' alt='' />";
					}
					else
					{
						$server_country = "<img style='vertical-align: middle' src='acpanel/images/flags/err.gif' alt='' />";
					}

					$array = array('country' => $server_country);

					$servers[] = array_merge((array)$obj, $array);
				}
			}

			require_once("scripts/smarty/Smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$result_cats = $db->Query("SELECT categoryid, sectionid FROM `acp_category` WHERE link = 'p_users'", array(), $config['sql_debug']);			
			if( is_array($result_cats) )
			{
				foreach ($result_cats as $obj)
				{
					$smarty->assign("cat_users", $obj->sectionid);
					$smarty->assign("cat_user_edit", $obj->categoryid);
				}
			}

			if(isset($servers)) $smarty->assign("servers",$servers);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("current_cat",$_POST['cat']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_servers_control_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('servers_perm_control', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$address = trim($_POST['address']);
				$hostname = trim($_POST['hostname']);
				$type = trim($_POST['gametype']);
	
				if( !$type )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['gametype_not_valid'].'</span>';
				}
				elseif( $address == '' )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['server_address_not_empty'].'</span>';
				}
				else
				{
					$arguments = array('type'=>$type,'address'=>$address,'hostname'=>$hostname,'timestamp'=>time(),'uid'=>$_POST['uid']);
					$check = $db->Query("SELECT * FROM `acp_servers` WHERE address = '{address}'", $arguments, $config['sql_debug']);
	
					if ($check)
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_try'].'</span>';
					}
					else
					{
						$result = $db->Query("INSERT INTO `acp_servers` (address,hostname,timestamp,userid,gametype) VALUES ('{address}','{hostname}','{timestamp}','{uid}','{type}')", $arguments, $config['sql_debug']);
	
						if (!$result)
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("servers_control", "add server: ".$address);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
						}
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "3":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('servers_perm_control', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_servers` WHERE id = '{id}'", $arguments, $config['sql_debug']);
				$result = $db->Query("DELETE FROM `acp_servers_viewed` WHERE server_id = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$product = getProduct("gameAccounts");
					if( !empty($product) )
					{
						$result = $db->Query("DELETE FROM `acp_access_mask_servers` WHERE server_id = '{id}'", $arguments, $config['sql_debug']);
					}
	
					$product = getProduct("multiserverRedirect");
					if( !empty($product) )
					{
						$result = $db->Query("DELETE FROM `acp_servers_redirect` WHERE server_id = '{id}'", $arguments, $config['sql_debug']);
					}
	
					$product = getProduct("ratingServers");
					if( !empty($product) )
					{
						$result = $db->Query("DELETE FROM `acp_servers_rating_temp` WHERE server_id = '{id}'", $arguments, $config['sql_debug']);
					}
	
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("servers_control", "delete server id: ".$id);
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
			$userPerm = $permClass->getPermissions('servers_perm_control', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_servers` WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);
				$result = $db->Query("DELETE FROM `acp_servers_viewed` WHERE server_id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$product = getProduct("gameAccounts");
					if( !empty($product) )
					{
						$result = $db->Query("DELETE FROM `acp_access_mask_servers` WHERE server_id IN ('{ids}')", $arguments, $config['sql_debug']);
					}
	
					$product = getProduct("multiserverRedirect");
					if( !empty($product) )
					{
						$result = $db->Query("DELETE FROM `acp_servers_redirect` WHERE server_id IN ('{ids}')", $arguments, $config['sql_debug']);
					}
	
					$product = getProduct("ratingServers");
					if( !empty($product) )
					{
						$result = $db->Query("DELETE FROM `acp_servers_rating_temp` WHERE server_id IN ('{ids}')", $arguments, $config['sql_debug']);
					}
	
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("servers_control", "multiple delete servers: ".count($ids));
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

			$address = $_POST['address'];
			$type = $_POST['type'];

			if (!empty($address))
			{
				include(INCLUDE_PATH . 'functions.servers.php');

				$lists = explode(":", $address);
				$ip = $lists[0];
				$port = $lists[1];

				$live = server_query_live($type, $ip, $port, "s");

				if( $live['b']['status'] && isset($live['s']['name']) )
				{
					if (!empty($live['s']))
					{
						if ($config['charset'] != 'utf-8')
						{
							$live['s']['name'] = iconv('utf-8', $config['charset'], $live['s']['name']);
						}

						print htmlspecialchars($live['s']['name']);
					}
				}
			}

			break;

		case "6":

			$address = $_POST['address'];
			$type = $_POST['type'];
			$id = $_POST['id'];

			include(INCLUDE_PATH . 'functions.servers.php');
			include(INCLUDE_PATH . 'class.SypexGeo.php');

			// ###############################################################################
			// SERVER INFO
			// ###############################################################################

			$lists = explode(":", $address);
			$ip = $lists[0];
			$port = $lists[1];

			$live = server_query_live($type, $ip, $port, "ep");

			if( $live['b']['status'] && isset($live['s']['name']) )
			{
				$ping = $live['b']['ping']."&nbsp;".$translate['ms'];
				$map = $live['s']['map'];
				$online = "<a href='?server=".$id."&info=players' rel='facebox' title='".$translate['players_info']."'>".$live['s']['players']."/".$live['s']['playersmax']."</a>";
				$os = (isset($live['e']['os'])) ? $live['e']['os'] : "";
				$pass = (isset($live['s']['password'])) ? $live['s']['password'] : "";
				$vac = (isset($live['e']['anticheat'])) ? $live['e']['anticheat'] : "";
				$status = "<img style='vertical-align: middle' src='acpanel/images/status_on.png' title='Online' alt='Online' />";
			}
			else
			{
				$os = "";
				$pass = "";
				$vac = "";
				$ping = "-";
				$map = "-";
				$online = "-/-";
				$status = "<img style='vertical-align: middle' src='acpanel/images/status_off.png' title='Offline' alt='Offline' />";
			}

			if ($os == "w")
				$os = "<img style='vertical-align: middle' src='acpanel/images/windows_logo.png' title='OS: Windows' alt='OS: Windows' />";
			elseif ($os == "l")
				$os = "<img style='vertical-align: middle' src='acpanel/images/linux_logo.png' title='OS: Linux' alt='OS: Linux' />";

			$pass = ($pass == "1") ? $pass = "<img style='vertical-align: middle' src='acpanel/images/locked.png' title='Password protect' alt='Password protect' />" : "";
			if($vac == "1") $vac = "<img style='vertical-align: middle' src='acpanel/images/shield.png' title='VAC Secured' alt='VAC Secured' />";

			clearstatcache();

			if(file_exists(ROOT_PATH . "images/maps/".$type."/".$map.".jpg"))
			{
				$map_info = $map;
				$map_path = "acpanel/images/maps/".$type."/".$map.".jpg";
			}
			else
			{
				$map_info = $translate['no_image'];
				$map_path = "acpanel/images/maps/noimage.jpg";
			}

			$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
			$server_country_code = strtolower($SxGeo->getCountry($ip));

			clearstatcache();

			if(file_exists(ROOT_PATH . "images/flags/".$server_country_code.".gif"))
			{
				$server_country = "<img style='vertical-align: middle' src='acpanel/images/flags/".$server_country_code.".gif' alt='' />";
			}
			else
			{
				$server_country = "<img style='vertical-align: middle' src='acpanel/images/flags/err.gif' alt='' />";
			}

			$server_info = array(
				'os' => $os,
				'online' => $online,
				'pass' => $pass,
				'vac' => $vac,
				'map' => $map,
				'map_info' => $map_info,
				'map_path' => $map_path,
				'status' => $status,
				'ping' => $ping,
				'country' => $server_country
			);

			// ###############################################################################
			// PLAYERS INFO
			// ###############################################################################

			if (!empty($live['p']))
			{
				$player_key = 1;
	
				foreach ($live['p'] as $v)
				{
					$players_info[$player_key] = array($v['pid'], $v['name'], $v['score'], $v['time']);
	
					$player_key++;
				}
			}

			require_once("scripts/smarty/Smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("server_info",$server_info);
			if(isset($players_info)) $smarty->assign("players_info",$players_info);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_servers_control_refresh.tpl');

			break;

		case "7":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('servers_perm_control', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['serverid'];
				unset($_POST['go'],$_POST['serverid'],$_POST['real_address'],$_POST['real_gametype']);
				$query_string = "";

				date_default_timezone_set('UTC');
				foreach ($_POST as $var => $value)
				{
					$value = trim($value);
	
					switch($var)
					{
						case "rating":
	
							if (!is_numeric($value))
							{
								$value = 0;
							}
							break;
	
						case "timestamp":
						case "vip":
	
							if ($value)
							{
								$value = strtotime($value);
								$value = get_datetime($value, false, true);
							}
							else
							{
								$value = 0;
							}
							break;
	
						case "address":
	
							if ($value == "")
							{
								$error[] = $translate['edit_empty_field'];
							}
							else
							{
								$arguments = array('id'=>$id,'address'=>$value);
								$check = $db->Query("SELECT id FROM `acp_servers` WHERE address = '{address}' AND id != '{id}'", $arguments, $config['sql_debug']);
				
								if( $check )
								{
									$error[] = $translate['edit_try'];
								}
							}
							break;
	
						case "hostname":
	
							if ($value == "")
							{
								$error[] = $translate['hostname_empty_field'];
							}
							else
							{
								if ($config['charset'] != 'utf-8')
								{
									$value = iconv('utf-8', $config['charset'], $value);
								}
							}
							break;
	
						case "userid":
	
							if ($value == "")
							{
								$error[] = $translate['username_empty_field'];
							}
							else
							{
								if ($config['charset'] != 'utf-8')
								{
									$value = iconv('utf-8', $config['charset'], $value);
								}
	
								$arguments = array('username'=>$value);
								$check = $db->Query("SELECT uid FROM `acp_users` WHERE username = '{username}'", $arguments, $config['sql_debug']);
				
								if( $check )
								{
									$value = $check;
								}
								else
								{
									$error[] = $translate['user_not_found'];
								}
							}
	
							break;
	
						default:
	
							break;
					}
	
					$query_string .= $var." = '".mysql_real_escape_string($value)."',";
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
					$query_string = substr($query_string, 0, strlen($query_string)-1);
					$arguments = array('id'=>$id);
					$result = $db->Query("UPDATE `acp_servers` SET ".$query_string." WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
					if (!$result)
					{
						print '<div class="message error"><p>'.$translate['edit_error'].'</p></div>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("servers_control", "edit server: ".$id);
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "8":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('servers_perm_control', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST["id"];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("UPDATE `acp_servers` SET active = IF(active = 1, 0, 1) WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("servers_control", "change status for server: ".$id);
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

		default:

			die("Hacking Attempt");
	}
}

?>
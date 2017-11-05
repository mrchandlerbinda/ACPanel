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
	$filter = "lp_name='p_nc_patterns.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
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
	// 6 - multilpy move items
	// 7 - check nick

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$s = $_POST['s'];

			$arguments = array('offset'=>$offset,'limit'=>$limit,'action'=>$s);
			$result = $db->Query("SELECT * FROM `acp_nick_patterns` WHERE action = '{action}' LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$patterns[] = (array)$obj;
				}
			}

			require_once("scripts/smarty/Smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("get_in",$s);
			if(isset($patterns)) $smarty->assign("patterns",$patterns);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_nc_patterns_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('nc_perm_patterns', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$pattern = trim($_POST['pattern']);
				if ($config['charset'] != 'utf-8')
				{
					$pattern = iconv('utf-8', $config['charset'], $pattern);
				}
				$dict = $_POST['dict'];
	
				if ($pattern == '') {
					print $translate['dont_empty'];
				} else {
	
					$arguments = array('pattern'=>$pattern,'dict'=>$dict);
					$check = $db->Query("SELECT * FROM `acp_nick_patterns` WHERE pattern = '{pattern}' AND action = '{dict}'", $arguments, $config['sql_debug']);
	
					if ($check)
					{
						print $translate['add_try'];
					}
					else
					{
						$result = $db->Query("INSERT INTO `acp_nick_patterns` (pattern,action) VALUES ('{pattern}','{dict}')", $arguments, $config['sql_debug']);
	
						if (!$result)
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("nick_control", "add pattern: ".$pattern);
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span class="indent">'.$translate['add_success'].'</span>';
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
			$userPerm = $permClass->getPermissions('nc_perm_patterns', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_nick_patterns` WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("nick_control", "delete pattern id: ".$id);
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
			$userPerm = $permClass->getPermissions('nc_perm_patterns', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_nick_patterns` WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("nick_control", "multiple delete patterns: ".count($ids));
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
			$userPerm = $permClass->getPermissions('nc_perm_patterns', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['editid'];
				$pattern = trim($_POST['pattern']);
				if ($config['charset'] != 'utf-8')
				{
					$pattern = iconv('utf-8', $config['charset'], $pattern);
				}
	
				if ($pattern == '')
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_empty_field'].'</span>';
				}
				else
				{
					$arguments = array('pattern'=>$pattern,'id'=>$id);
					$result = $db->Query("UPDATE `acp_nick_patterns` SET pattern = '{pattern}' WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
					if (!$result)
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("nick_control", "edit pattern: ".$pattern);
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "6":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('nc_perm_patterns', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
				$action = $_POST['action'];
	
				$arguments = array('action'=>$action,'ids'=>$ids);
				$result = $db->Query("UPDATE `acp_nick_patterns` SET action = '{action}' WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("nick_control", "move patterns: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['move_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['move_error'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "7":

			$nick = trim($_POST['checknick']);

			if ($nick == "")
			{
				print '<div class="message errormsg"><p>'.$translate['checknick_empty'].'</p></div>';
			}
			else
			{
				$check = $db->Query("SELECT pattern, action FROM `acp_nick_patterns` ORDER BY action ASC", array(), $config['sql_debug']);

				if( is_array($check) )
				{
					if ($config['charset'] != 'utf-8')
					{
						$nick = iconv('utf-8', $config['charset'], $nick);
					}

					foreach ($check as $obj)
					{						if (preg_match('/'.$obj->pattern.'/', $nick))
						{							$result = ($obj->action != '0') ? '<div class="message warning"><p>'.$translate['checknick_disallow'].'</p></div>' : '<div class="message success"><p>'.$translate['checknick_allow'].'</p></div>';
							break;
						}
					}
				}

				print (!isset($result)) ? '<div class="message success"><p>'.$translate['checknick_allow'].'</p></div>' : $result;

				if (in_array("log_edititing", $config['user_action_log'])) saveLogs("nick_control", "check nick: ".$nick);
			}

			break;

		default:

			die("Hacking Attempt");
	}
}

?>
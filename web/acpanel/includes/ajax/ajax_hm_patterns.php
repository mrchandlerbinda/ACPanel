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
	$filter = "lp_name='p_hm_patterns.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
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

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT * FROM `acp_hud_manager` WHERE hud_id > 0 ORDER BY priority DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

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

			if(isset($patterns)) $smarty->assign("patterns",$patterns);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_hm_patterns_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('perm_hudm', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$name = trim($_POST['name']);
				if ($config['charset'] != 'utf-8')
				{
					$name = iconv('utf-8', $config['charset'], $name);
				}
	
				$priority = $_POST['priority'];
				if( !is_numeric($priority) )
				{
					$priority = 10;
				}
	
				if( $name == '' )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['dont_empty'].'</span>';
				}
				else
				{
					if( isset($_POST['flags']) )
					{
						if( is_array($_POST['flags']) )
						{
							$flags = array_sum($_POST['flags']);
						}
						else
						{
							$flags = $_POST['flags'];
						}
					}
					else
					{
						$flags = "";
					}
	
					if( $flags )
					{
						$arguments = array('name'=>$name,'flags'=>$flags,'priority'=>$priority);
						$result = $db->Query("INSERT INTO `acp_hud_manager` (name, flags, priority) VALUES ('{name}','{flags}','{priority}')", $arguments, $config['sql_debug']);
		
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("hud_manager", "add value: ".$name);
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
						}
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['hm_flags_empty'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "3":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('perm_hudm', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_hud_manager` WHERE hud_id = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("hud_manager", "delete value id: ".$id);
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
			$userPerm = $permClass->getPermissions('perm_hudm', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_hud_manager` WHERE hud_id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("hud_manager", "multiple delete values: ".count($ids));
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
			$userPerm = $permClass->getPermissions('perm_hudm', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['hud_id'];
				$name = trim($_POST['name']);
				if ($config['charset'] != 'utf-8')
				{
					$name = iconv('utf-8', $config['charset'], $name);
				}
	
				$priority = $_POST['priority'];
				if( !is_numeric($priority) )
				{
					$priority = 10;
				}
	
				if ($name == '')
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_empty_field'].'</span>';
				}
				else
				{
					if( isset($_POST['flags']) )
					{
						if( is_array($_POST['flags']) )
						{
							$flags = array_sum($_POST['flags']);
						}
						else
						{
							$flags = $_POST['flags'];
						}
					}
					else
					{
						$flags = "";
					}
	
					if( $flags )
					{
						$arguments = array('name'=>$name,'flags'=>$flags,'priority'=>$priority,'id'=>$id);
						$result = $db->Query("UPDATE `acp_hud_manager` SET name = '{name}', priority = '{priority}', flags = '{flags}' WHERE hud_id = '{id}'", $arguments, $config['sql_debug']);
		
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("hud_manager", "edit value: ".$id);
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
						}
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['hm_flags_empty'].'</span>';
					}
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
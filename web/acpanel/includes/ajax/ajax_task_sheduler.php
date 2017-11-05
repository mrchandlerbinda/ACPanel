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
	$filter = "lp_name='task_sheduler.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
	$arguments = array('lang'=>get_language(1));
	$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
	if(is_array($tr_result)) {
		foreach ($tr_result as $obj){
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}

	require_once(INCLUDE_PATH . '_auth.php');
	date_default_timezone_set('UTC');

	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create list
	// 2 - add item
	// 3 - del item
	// 4 - multiply del items
	// 5 - edit item
	// 6 - change status

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT e.*, (SELECT dateline FROM `acp_cron_log` l WHERE l.entry_id = e.entry_id ORDER BY dateline DESC LIMIT 1) AS last_run FROM `acp_cron_entry` e LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					if( !$obj->active )
					{
						$obj->next_run = '-';
					}
					else
					{
						$nextRun = (!is_null($obj->last_run)) ? $obj->last_run : time();
						$nextRun++;
				
						list($seconds, $minutes, $hours, $days, $months, $years) = unserialize($obj->run_rules);
				
						$arrT = array('seconds'=>'s', 'minutes'=>'i', 'hours'=>'H', 'days'=>'d', 'months'=>'m', 'years'=>'Y');
						foreach( $arrT as $time => $identifier )
						{
							if( $$time != '*' )
							{
								while( $$time != date($identifier, $nextRun) )
								{
									switch( $identifier )
									{
										case 's' : $nextRun += 1; break;
										case 'i' : $nextRun += (60*1); break;
										case 'H' : $nextRun += (60*60); break;
										case 'd' : 
										case 'm' : 
										case 'Y' : $nextRun += (60*60*24); break;
									}
								}
							}
						}
				
						$obj->next_run = (($minus = $nextRun - time()) < 0) ? $nextRun - $minus : $nextRun;
						$obj->next_run = get_datetime($obj->next_run, $config['date_format']);
					}

					$obj->last_run = (!is_null($obj->last_run)) ? get_datetime($obj->last_run, $config['date_format']) : '-';

					$obj->run_rules = unserialize($obj->run_rules);
					$obj->run_rules = implode(' ', $obj->run_rules);

					$array_cron[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($array_cron)) $smarty->assign("tasks",$array_cron);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('task_sheduler_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('tools_perm_cron', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$error = array();
	
				if( ($_POST['days'] == "31" && !in_array($_POST['months'], array("*","01","03","05","07","08","10","12"))) || ($_POST['days'] == "30" && $_POST['months'] == "02") )
				{
					$error[] = sprintf($translate['day_not_month'], $_POST['months'], $_POST['days']);
				}
	
				$rules_string = serialize(array("00", $_POST['minutes'], $_POST['hours'], $_POST['days'], $_POST['months'], "*"));
				$query_string = "run_rules = '".mysql_real_escape_string($rules_string)."',";
				unset($_POST['go'], $_POST['minutes'], $_POST['hours'], $_POST['days'], $_POST['months']);
	
				if( is_array($_POST) )
				{
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "cron_file":
	
								if( !$value )
								{
									$error[] = $translate['file_not_empty'];
								}
								elseif( !file_exists(INCLUDE_PATH . 'cron/'.$value) )
								{
									$error[] = sprintf($translate['file_not_exists'], $value);
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
						$query_string .= ",task_update = '".time()."'";
	
						$result = $db->Query("INSERT INTO `acp_cron_entry` SET ".$query_string."", array(), $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("task_sheduler", "add new task");
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
			$userPerm = $permClass->getPermissions('tools_perm_cron', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
	
				$result = $db->Query("DELETE FROM `acp_cron_entry` WHERE entry_id = '{id}'", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("task_sheduler", "delete task id: ".$id);
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
			$userPerm = $permClass->getPermissions('tools_perm_cron', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_cron_entry` WHERE entry_id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("task_sheduler", "multiple delete tasks: ".count($ids));
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
			$userPerm = $permClass->getPermissions('tools_perm_cron', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$error = array();
				$task_id = $_POST['entry_id'];
	
				if( ($_POST['days'] == "31" && !in_array($_POST['months'], array("*","01","03","05","07","08","10","12"))) || ($_POST['days'] == "30" && $_POST['months'] == "02") )
				{
					$error[] = sprintf($translate['day_not_month'], $_POST['months'], $_POST['days']);
				}
	
				$rules_string = serialize(array("00", $_POST['minutes'], $_POST['hours'], $_POST['days'], $_POST['months'], "*"));
				$query_string = "run_rules = '".mysql_real_escape_string($rules_string)."',";
				unset($_POST['go'], $_POST['minutes'], $_POST['hours'], $_POST['days'], $_POST['months'], $_POST['entry_id']);
	
				if( is_array($_POST) )
				{
					foreach ($_POST as $var => $value)
					{
						$value = trim($value);
	
						switch($var)
						{
							case "cron_file":
	
								if( !$value )
								{
									$error[] = $translate['file_not_empty'];
								}
								elseif( !file_exists(INCLUDE_PATH . 'cron/'.$value) )
								{
									$error[] = sprintf($translate['file_not_exists'], $value);
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
						$query_string .= ",task_update = '".time()."'";
	
						$result = $db->Query("UPDATE `acp_cron_entry` SET ".$query_string." WHERE entry_id = ".$task_id, array(), $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
						}
						else
						{
							if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("task_sheduler", "edit task id: ".$task_id);
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

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('tools_perm_cron', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST["id"];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("UPDATE `acp_cron_entry` SET active = IF(active = 1, 0, 1) WHERE entry_id = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("task_sheduler", "change status for task: ".$id);
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
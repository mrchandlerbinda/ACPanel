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
	$filter = "lp_name='p_usergroups.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
	$arguments = array('lang'=>get_language(1));
	$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
	if(is_array($tr_result))
	{
		foreach ($tr_result as $obj)
		{
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

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT * FROM `acp_usergroups` LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$result_users = $db->Query("SELECT count(*) AS users FROM `acp_users` WHERE usergroupid = {groupid}", array('groupid'=>$obj->usergroupid), $config['sql_debug']);
					$groups[] = array_merge((array)$obj,array('users'=>$result_users));
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("colums","5");
			$smarty->assign("groups",$groups);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_usergroups_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_options', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$error = $permArray = array();
				$uname = $_POST['usergroupname'];
				if( $config['charset'] != 'utf-8' )
				{
					$uname = iconv('utf-8', $config['charset'], $uname);
				}
				unset($_POST['go'], $_POST['usergroupname']);
	
				if( is_array($_POST) )
				{
					$insertGroup = "";
					$argumentsGroup = array();
	
					$result_perm = $db->Query("SELECT id, varname, type FROM `acp_usergroups_permissions` WHERE varname IS NOT NULL", $argumentsGroup, $config['sql_debug']);
	
					if( is_array($result_perm) )
					{
						foreach( $result_perm as $objperm )
						{
							if( $objperm->type == 'bitmask' )
							{
								if( isset($_POST[$objperm->varname]) && is_array($_POST[$objperm->varname]) )
								{
									$bitmask = $permClass->toBitmask($_POST[$objperm->varname]);
								}
								else
								{
									$bitmask = 0;
								}
	
								$permArray[$objperm->id] = array('bitmask' => $bitmask, 'varname' => $objperm->varname);
							}
							else
							{
								if( isset($_POST[$objperm->varname]) )
								{
									if( is_array($_POST[$objperm->varname]) )
									{
										$_POST[$objperm->varname] = implode(",", $_POST[$objperm->varname]);
									}
	
									$insertGroup .= ", ".$objperm->varname." = '{".$objperm->varname."}'";
									$argumentsGroup[$objperm->varname] = $_POST[$objperm->varname];
								}
							}
						}
					}
					else
						$error[] = $translate['error_perm_table_empty'];
	
					if( $insertGroup )
					{
						$argumentsGroup['uname'] = $uname;
						$insertGroup = substr($insertGroup, 1).", usergroupname = '{uname}'";
						$result = $db->Query("INSERT INTO `acp_usergroups` SET".$insertGroup, $argumentsGroup, $config['sql_debug']);
						if( !$result )
							$error[] = $translate['error_group_insert'];
						else
						{
							$groupid = $db->LastInsertID();
	
							foreach($permArray as $k => $v)
							{
								if( !$permClass->setPermissions($k, $groupid, $v['bitmask']) )
									$error[] = sprintf($translate['error_perm_update'], $v['varname']);
							}
						}
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_usergroups", "add group: ".$uname);
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
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
			$userPerm = $permClass->getPermissions('general_perm_options', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE `acp_usergroups`, `acp_permissons_action` FROM `acp_usergroups` INNER JOIN `acp_permissons_action` 
					WHERE acp_usergroups.usergroupid = acp_permissons_action.usergroupid AND acp_usergroups.usergroupid = '{id}'", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_usergroups", "delete usergroup id: ".$id);
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
			$userPerm = $permClass->getPermissions('general_perm_options', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE `acp_usergroups`, `acp_permissons_action` FROM `acp_usergroups` INNER JOIN `acp_permissons_action` 
					WHERE acp_usergroups.usergroupid = acp_permissons_action.usergroupid AND acp_usergroups.usergroupid IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_usergroups", "myltiple delete usergroups: ".count($ids));
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
			$userPerm = $permClass->getPermissions('general_perm_options', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$error = array();
				$id = $_POST['usergroupid'];
				$uname = $_POST['usergroupname'];
				if( $config['charset'] != 'utf-8' )
				{
					$uname = iconv('utf-8', $config['charset'], $uname);
				}
				unset($_POST['go'], $_POST['usergroupid'], $_POST['usergroupname']);
	
				if( is_array($_POST) )
				{
					require_once(INCLUDE_PATH . 'class.Permissions.php');
					$permClass = new Permissions($db);
					$updateGroup = "";
					$argumentsGroup = array('id' => $id);

					$result_perm = $db->Query("SELECT a.id, a.varname, a.type, b.bitmask FROM `acp_usergroups_permissions` a LEFT JOIN 
						(SELECT usergroupid, action, bitmask FROM `acp_permissons_action` WHERE usergroupid = ".$id." ) b ON (b.action = a.id) 
						WHERE a.varname IS NOT NULL ORDER BY a.perm_sort", array(), $config['sql_debug']);
	
					if( is_array($result_perm) )
					{
						foreach( $result_perm as $objperm )
						{
							if( $objperm->type == 'bitmask' )
							{
								if( isset($_POST[$objperm->varname]) && is_array($_POST[$objperm->varname]) )
								{
									if( ($bitmask = $permClass->toBitmask($_POST[$objperm->varname])) != $objperm->bitmask )
										if( !$permClass->setPermissions($objperm->id, $id, $bitmask) )
											$error[] = sprintf($translate['error_perm_update'], $objperm->varname);
								}
								else
								{
									if( !$permClass->setPermissions($objperm->id, $id, 0) )
										$error[] = sprintf($translate['error_perm_update'], $objperm->varname);
								}
							}
							else
							{
								if( isset($_POST[$objperm->varname]) )
								{
									if( is_array($_POST[$objperm->varname]) )
									{
										$_POST[$objperm->varname] = implode(",", $_POST[$objperm->varname]);
									}
	
									$updateGroup .= ", ".$objperm->varname." = '{".$objperm->varname."}'";
									$argumentsGroup[$objperm->varname] = $_POST[$objperm->varname];
								}
							}
						}
					}
					else
						$error[] = $translate['error_perm_table_empty'];
	
					if( $updateGroup )
					{
						$argumentsGroup['uname'] = $uname;
						$updateGroup = substr($updateGroup, 1).", usergroupname = '{uname}'";
						$result = $db->Query("UPDATE `acp_usergroups` SET".$updateGroup." WHERE usergroupid = '{id}'", $argumentsGroup, $config['sql_debug']);
						if( !$result )
							$error[] = $translate['error_group_update'];
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
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_usergroups", "edit usergroup id: ".$id);
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
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

		default:

			die("Hacking Attempt");
	}
}

?>
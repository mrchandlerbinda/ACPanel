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
	$filter = "lp_name='p_general_lang.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
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
	// 2 - add item
	// 3 - del item
	// 4 - multiply del items
	// 5 - edit item
	// 6 - create templates list
	// 7 - add template
	// 8 - del template
	// 9 - multiply del templates
	// 10 - edit template

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT * FROM `acp_lang` LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$array_lang[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("colums","4");
			$smarty->assign("langs",$array_lang);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_general_lang_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$lang_title = trim($_POST['lang_title']);
				$lang_code = trim($_POST['lang_code']);
				$lang_active = $_POST['lang_active'];
	
				if ($lang_code == '')
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['dont_empty'].'</span>';
				}
				else
				{
					if ($config['charset'] != 'utf-8')
					{
						$f = iconv('utf-8', $config['charset'], $lang_title);
					}
					else
					{
						$f = $lang_title;
					}
	
					$arguments = array('title'=>$f,'code'=>$lang_code,'active'=>$lang_active);
					$check = $db->Query("SELECT * FROM `acp_lang` WHERE lang_code = '{code}'", $arguments, $config['sql_debug']);
	
					if ($check)
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_try'].'</span>';
					}
					else
					{
						$result_addlang = $db->Query("INSERT INTO `acp_lang` (lang_title, lang_code, lang_active) VALUES ('{title}', '{code}', '{active}')", $arguments, $config['sql_debug']);
						$result_addwords = $db->Query("ALTER TABLE `acp_lang_words` ADD {code} TEXT", $arguments, $config['sql_debug']);
	
						if (!$result_addlang || !$result_addwords)
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_langs", "add language: ".$lang_title);
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
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$arguments['code'] = $db->Query("SELECT lang_code FROM `acp_lang` WHERE lang_id = '{id}'", $arguments, $config['sql_debug']);
	
				$result_lang = $db->Query("DELETE FROM `acp_lang` WHERE lang_id = '{id}'", $arguments, $config['sql_debug']);
				$result_words = $db->Query("ALTER TABLE `acp_lang_words` DROP {code}", $arguments, $config['sql_debug']);
	
				if ($result_lang && $result_words)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_langs", "delete language id: ".$id);
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
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("SELECT lang_id, lang_code FROM `acp_lang` WHERE lang_id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				unset($drop_string);
				if( is_array($result) )
				{
					foreach ($result as $obj)
					{
						$drop_string .= ", DROP ".$obj->lang_code;
					}
	
					$result_words = $db->Query("ALTER TABLE `acp_lang_words`".substr($drop_string, 1), array(), $config['sql_debug']);
				}
	
				$result_lang = $db->Query("DELETE FROM `acp_lang` WHERE lang_id IN ('{ids}')", $arguments, $config['sql_debug']);
	
				if ($result_lang && $result_words)
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_langs", "multiple delete languages: ".count($ids));
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
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$lang_id = trim($_POST['lang_id']);
				$lang_title = trim($_POST['lang_title']);
				$lang_code = trim($_POST['lang_code']);
				$lang_code_temp = trim($_POST['lang_code_temp']);
				$lang_active = $_POST['lang_active'];
	
				if ($lang_code == '')
				{
					print $translate['dont_empty'];
				}
				else
				{
					if ($config['charset'] != 'utf-8')
					{
						$f = iconv('utf-8', $config['charset'], $lang_title);
					}
					else
					{
						$f = $lang_title;
					}
	
					$arguments = array('title'=>$f,'code'=>$lang_code,'code_temp'=>$lang_code_temp,'active'=>$lang_active,'id'=>$lang_id);
					$check = $db->Query("SELECT * FROM `acp_lang` WHERE lang_code = '{code}' AND lang_id != '{id}'", $arguments, $config['sql_debug']);
	
					if ($check)
					{
						print $translate['add_try'];
					}
					else
					{
						$result_lang = $db->Query("UPDATE `acp_lang` SET lang_title = '{title}', lang_code = '{code}', lang_active = '{active}' WHERE lang_id = '{id}'", $arguments, $config['sql_debug']);
		
						if ($lang_code != $lang_code_temp)
						{
							$result_words = $db->Query("ALTER TABLE `acp_lang_words` CHANGE {code_temp} {code} TEXT", $arguments, $config['sql_debug']);
						}
						else
						{
							$result_words = true;
						}
		
						if ($result_lang && $result_words)
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_langs", "edit language: ".$lang_title);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
						}
						else
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_failed'].'</span>';
						}
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "6":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$where = ($_POST['product']) ? " WHERE a.productid = '".mysql_real_escape_string($_POST['product'])."'" : "";

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT a.lp_id, a.lp_name, a.productid, count(b.lw_id) AS cnt FROM `acp_lang_pages` a 
				LEFT JOIN `acp_lang_words` b ON b.lw_page = a.lp_id".$where." 
				GROUP BY a.lp_id ORDER BY a.lp_name LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$array_lang[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("langs",$array_lang);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_general_phrases_template_list.tpl');

			break;

		case "7":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$productid = trim($_POST['productid']);
				$lp_name = trim($_POST['lp_name']);
	
				if ($lp_name == '')
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['dont_empty_tpl'].'</span>';
				}
				else
				{
					if ($config['charset'] != 'utf-8')
					{
						$f = iconv('utf-8', $config['charset'], $lp_name);
					}
					else
					{
						$f = $lp_name;
					}
	
					$arguments = array('lp_name'=>$f,'productid'=>$productid);
					$check = $db->Query("SELECT lp_id FROM `acp_lang_pages` WHERE lp_name = '{lp_name}'", $arguments, $config['sql_debug']);
	
					if( $check )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_try_tpl'].'</span>';
					}
					else
					{
						$result = $db->Query("INSERT INTO `acp_lang_pages` (lp_name, productid) VALUES ('{lp_name}', '{productid}')", $arguments, $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_phrases_template", "add template: ".$lp_name);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success_tpl'].'</span>';
						}
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "8":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
	
				$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE lp_id = '{id}'", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_phrases_template", "delete template id: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_success_tpl'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "9":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];

				$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE lp_id IN ('{ids}')", array('ids' => $ids), $config['sql_debug']);
	
				if( $result )
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_phrases_template", "multiple delete templates: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success_tpl'].'&nbsp;'.count($ids).'</span>';
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
			$userPerm = $permClass->getPermissions('general_perm_langs', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$lp_id = trim($_POST['lp_id']);
				$lp_name = trim($_POST['lp_name']);
				$productid = trim($_POST['productid']);
	
				if( $lp_name == '' )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['dont_empty_tpl'].'</span>';
				}
				else
				{
					if ($config['charset'] != 'utf-8')
					{
						$f = iconv('utf-8', $config['charset'], $lp_name);
					}
					else
					{
						$f = $lp_name;
					}
	
					$arguments = array('lp_name'=>$f,'productid'=>$productid,'lp_id'=>$lp_id);
					$check = $db->Query("SELECT * FROM `acp_lang_pages` WHERE lp_name = '{lp_name}' AND lp_id != '{lp_id}'", $arguments, $config['sql_debug']);
	
					if( $check )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_try_tpl'].'</span>';
					}
					else
					{
						$result = $db->Query("UPDATE `acp_lang_pages` SET lp_name = '{lp_name}', productid = '{productid}' WHERE lp_id = '{lp_id}'", $arguments, $config['sql_debug']);
		
						if( $result )
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_phrases_template", "edit template: ".$lp_name);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success_tpl'].'</span>';
						}
						else
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_failed'].'</span>';
						}
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
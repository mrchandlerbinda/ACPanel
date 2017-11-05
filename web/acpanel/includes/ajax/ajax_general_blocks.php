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
	$filter = "lp_name='p_general_blocks.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
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
	// 4 - edit item
	// 5 - resort list

	switch($_POST['go'])
	{		case "1":

			$result_node = $db->Query("SELECT * FROM `acp_blocks` WHERE blockid > 0 ORDER BY display_order", array(), $config['sql_debug']);

			if (is_array($result_node))
			{
				foreach ($result_node as $obj)
				{
					$array_blocks[] = (array)$obj;
				}
			}
			else
			{
				$error = $translate['error_empty'];
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($array_blocks)) $smarty->assign("array_blocks",$array_blocks);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_general_blocks_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_blocks', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$block_title = trim($_POST['title']);
	
				if( $block_title == '' )
				{
					print $translate['dont_empty'];
				}
				else
				{
					$block_order = trim($_POST['display_order']);
					if( !is_numeric($block_order) && $block_order != 0 )
					{
						$block_order = 10;
					}
	
					$block_product = trim($_POST['productid']);
					$block_desc = trim($_POST['description']);
					$block_link = trim($_POST['link']);
					$block_execute_code = trim($_POST['execute_code']);
					$block_view = $_POST['view_in_block'];
	
					if( $config['charset'] != 'utf-8' )
						$f = iconv('utf-8', $config['charset'], $block_title);
					else
						$f = $block_title;
	
					$arguments = array('block_view'=>$block_view,'title'=>$f,'link'=>$block_link,'description'=>$block_desc,'productid'=>$block_product,'display_order'=>$block_order,'execute_code'=>$block_execute_code);
					$check = $db->Query("SELECT * FROM `acp_blocks` WHERE title = '{title}'", $arguments, $config['sql_debug']);
	
					if( $check )
					{
						print $translate['add_try'];
					}
					else
					{
						$result = $db->Query("INSERT INTO `acp_blocks` (title,description,link,productid,display_order,execute_code,view_in_block) VALUES ('{title}','{description}','{link}','{productid}','{display_order}','{execute_code}','{block_view}')", $arguments, $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_block", "add block: ".$f);
							print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
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
			$userPerm = $permClass->getPermissions('general_perm_blocks', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_blocks` WHERE blockid = '{id}'", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("edit_blocks", "delete block id: ".$id);
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
			$userPerm = $permClass->getPermissions('general_perm_blocks', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$blockid = $_POST['blockid'];
				$block_title = trim($_POST['title']);
	
				if( $block_title == '' )
				{
					print $translate['dont_empty'];
				}
				else
				{
					$block_order = trim($_POST['display_order']);
					if (!is_numeric($block_order) && $block_order != 0)
					{
						$block_order = 10;
					}
	
					$block_desc = trim($_POST['description']);
					$block_product = trim($_POST['productid']);
					$block_link = trim($_POST['link']);
					$block_execute_code = trim($_POST['execute_code']);
					$block_view = $_POST['view_in_block'];
	
					if( $config['charset'] != 'utf-8' )
						$f = iconv('utf-8', $config['charset'], $block_title);
					else
						$f = $block_title;
	
					$arguments = array('block_view'=>$block_view,'block'=>$blockid,'title'=>$f,'link'=>$block_link,'description'=>$block_desc,'productid'=>$block_product,'display_order'=>$block_order,'execute_code'=>$block_execute_code);
					$result = $db->Query("UPDATE `acp_blocks` SET title = '{title}', description = '{description}', link = '{link}', productid = '{productid}', display_order = '{display_order}', execute_code = '{execute_code}', view_in_block = '{block_view}' WHERE blockid = '{block}'", $arguments, $config['sql_debug']);
	
					if( $result )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_blocks", "edit block: ".$blockid);
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "5":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_blocks', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				unset($_POST['go'], $array_cat);
	
				foreach($_POST as $k => $v)
				{
					$blockid = substr($k, 6);
					$array_cat[$blockid] = $v;
				}
	
				$result = $db->Query("SELECT blockid, display_order FROM `acp_blocks`", array(), $config['sql_debug']);
				if(is_array($result))
				{
					foreach ($result as $obj)
					{
						if($array_cat[$obj->blockid] != $obj->display_order)
						{
							$arguments = array('block'=>$obj->blockid,'display_order'=>$array_cat[$obj->blockid]);
							$result_update = $db->Query("UPDATE `acp_blocks` SET display_order = {display_order} WHERE blockid = {block}", $arguments, $config['sql_debug']);
						}
						else
						{
							unset($array_cat[$obj->blockid]);
						}
					}
				}
				else
				{
					$error = '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
				}
	
				if( !isset($error) )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_blocks", "resort blocks");
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['resort_success'].'</span>';
				}
				else
				{
					print $error;
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
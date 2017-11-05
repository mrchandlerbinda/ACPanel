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
	$filter = "lp_name='p_general_options.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
	$arguments = array('lang'=>get_language(1));
	$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
	if(is_array($tr_result)) {
		foreach ($tr_result as $obj){
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}

	require_once(INCLUDE_PATH . '_auth.php');
	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - update config

	switch($_POST['go'])
	{
		case "1":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_options', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				unset($_POST['go'],$sql_error);
				if( is_array($_POST) )
				{
					foreach ($_POST as $var => $value)
					{
						if( is_array($value) )
						{
							if( count($value) > 1 ) $value = array_diff($value, array(''));
							$value = implode(",", $value);
						}
	
						$arguments = array('value'=>$value,'var'=>$var);
						$result = $db->Query("UPDATE `acp_config` SET value = '{value}' WHERE varname = '{var}'", $arguments, $config['sql_debug']);
	
						if (!$result)
						{
							$sql_error = '&nbsp;'.$var;
						}
					}
	
					if (isset($sql_error))
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].':'.$sql_error.'</span>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_general_options", "update config");
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
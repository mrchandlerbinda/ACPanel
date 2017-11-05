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
	$filter = "lp_name='p_nc_logs.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
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
	// 2 - delete logs

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$srv = (isset($_POST['srv'])) ? $_POST['srv'] : "";
			$reason = (isset($_POST['reason'])) ? $_POST['reason'] : "";
			$point = (isset($_POST['point'])) ? $_POST['point'] : "";
			$startdate = (isset($_POST['startdate'])) ? $_POST['startdate'] : "";
			$enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : "";
			$nick = (isset($_POST['nick'])) ? $_POST['nick'] : "";
			$steam = (isset($_POST['steam'])) ? $_POST['steam'] : "";
			$ip = (isset($_POST['ip'])) ? $_POST['ip'] : "";

			$sqlconds = 'WHERE 1=1 ';

			if ($srv) { $sqlconds .= " AND serverip = '{serverip}' "; }
			if ($reason) { $sqlconds .= " AND pattern = '{pattern}' "; }
			if ($point) { $sqlconds .= " AND action = '{action}' "; }
			if ($nick) { $sqlconds .= " AND name LIKE '%{name}%' "; }
			if ($steam) { $sqlconds .= " AND authid LIKE '%{authid}%' "; }
			if ($ip) { $sqlconds .= " AND ip LIKE '%{ip}%' "; }
			if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
			if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }

			date_default_timezone_set('UTC');
			$arguments = array('offset'=>$offset,'limit'=>$limit,'serverip'=>$srv,'pattern'=>$reason,'action'=>$point,'name'=>$nick,'authid'=>$steam,'ip'=>$ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true));
			$result = $db->Query("SELECT * FROM `acp_nick_logs` $sqlconds ORDER BY `id` DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$messages[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($messages)) $smarty->assign("messages",$messages);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_nc_logs_load.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('nc_perm_patterns', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$srv = (isset($_POST['server'])) ? $_POST['server'] : "";
				$reason = (isset($_POST['reason'])) ? $_POST['reason'] : "";
				$point = (isset($_POST['point'])) ? $_POST['point'] : "";
				$startdate = (isset($_POST['begindate'])) ? $_POST['begindate'] : "";
				$enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : "";
				$nick = (isset($_POST['player_nick'])) ? $_POST['player_nick'] : "";
				$steam = (isset($_POST['player_id'])) ? $_POST['player_id'] : "";
				$ip = (isset($_POST['player_ip'])) ? $_POST['player_ip'] : "";
	
				$sqlconds = 'WHERE 1=1 ';
	
				if ($srv != 'all') { $sqlconds .= " AND serverip = '{serverip}' "; }
				if ($reason != 'all') { $sqlconds .= " AND pattern = '{pattern}' "; }
				if ($point != 'all') { $sqlconds .= " AND action = '{action}' "; }
				if ($nick) { $sqlconds .= " AND name LIKE '%{name}%' "; }
				if ($steam) { $sqlconds .= " AND authid LIKE '%{authid}%' "; }
				if ($ip) { $sqlconds .= " AND ip LIKE '%{ip}%' "; }
				if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
				if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }

				date_default_timezone_set('UTC');
				$arguments = array('serverip'=>$srv,'pattern'=>$reason,'action'=>$point,'name'=>$nick,'authid'=>$steam,'ip'=>$ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true));
				$result = $db->Query("DELETE FROM `acp_nick_logs` ".$sqlconds, $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$delnum = $db->Affected();
	
					if( $delnum > 0 )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("nick_control", "delete logs: ".$delnum);
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_success'].'&nbsp;'.$delnum.'</span>';
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
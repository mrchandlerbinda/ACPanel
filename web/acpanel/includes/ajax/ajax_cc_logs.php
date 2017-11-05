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
	$filter = "lp_name='p_cc_logs.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
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
	// 2 - delete logs
	// 3 - public list

	switch($_POST['go'])
	{
        case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$srv = (isset($_POST['srv'])) ? $_POST['srv'] : '';
			$type = (isset($_POST['type'])) ? $_POST['type'] : '';
			$status = (isset($_POST['status'])) ? $_POST['status'] : '';
			$startdate = (isset($_POST['startdate'])) ? $_POST['startdate'] : '';
			$enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : '';
			$nick = (isset($_POST['nick'])) ? $_POST['nick'] : '';
			$steam = (isset($_POST['steam'])) ? $_POST['steam'] : '';
			$ip = (isset($_POST['ip'])) ? $_POST['ip'] : '';
			$msg = (isset($_POST['msg'])) ? $_POST['msg'] : '';

			$sqlconds = 'WHERE 1=1 ';

			if ($srv) { $sqlconds .= " AND serverip = '{serverip}' "; }
			if ($type) { $sqlconds .= " AND cmd = '{cmd}' "; }
			if ($status) { $sqlconds .= " AND pattern = '{status}' "; }
			if ($nick) { $sqlconds .= " AND name LIKE '%{name}%' "; }
			if ($steam) { $sqlconds .= " AND authid LIKE '%{authid}%' "; }
			if ($ip) { $sqlconds .= " AND ip LIKE '%{ip}%' "; }
			if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
			if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }
			if ($msg) { $sqlconds .= " AND message LIKE '%{message}%' "; }

			date_default_timezone_set('UTC');
			$arguments = array('offset'=>$offset,'limit'=>$limit,'status'=>$status,'serverip'=>$srv,'cmd'=>$type,'name'=>$nick,'authid'=>$steam,'ip'=>$ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true),'message'=>$msg);
			$result = $db->Query("SELECT * FROM `acp_chat_logs` $sqlconds ORDER BY `id` DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$obj->timestamp = get_datetime($obj->timestamp, 'H:i:s d.m.Y');
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
			$smarty->display('p_cc_logs_load.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('cc_perm_patterns', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$srv = (isset($_POST['server'])) ? $_POST['server'] : '';
				$type = (isset($_POST['msgtype'])) ? $_POST['msgtype'] : '';
				$status = (isset($_POST['msgstatus'])) ? $_POST['msgstatus'] : '';
				$startdate = (isset($_POST['begindate'])) ? $_POST['begindate'] : '';
				$enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : '';
				$nick = (isset($_POST['player_nick'])) ? $_POST['player_nick'] : '';
				$steam = (isset($_POST['player_id'])) ? $_POST['player_id'] : '';
				$ip = (isset($_POST['player_ip'])) ? $_POST['player_ip'] : '';
				$msg = (isset($_POST['message'])) ? $_POST['message'] : '';
	
				$sqlconds = 'WHERE 1=1 ';
	
				if ($srv != 'all') { $sqlconds .= " AND serverip = '{serverip}' "; }
				if ($type != 'all') { $sqlconds .= " AND cmd = '{cmd}' "; }
				if ($status != 'all') { $sqlconds .= " AND pattern = '{status}' "; }
				if ($nick) { $sqlconds .= " AND name LIKE '%{name}%' "; }
				if ($steam) { $sqlconds .= " AND authid LIKE '%{authid}%' "; }
				if ($ip) { $sqlconds .= " AND ip LIKE '%{ip}%' "; }
				if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
				if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }
				if ($msg) { $sqlconds .= " AND message LIKE '%{message}%' "; }

				date_default_timezone_set('UTC');
				$arguments = array('status'=>$status,'serverip'=>$srv,'cmd'=>$type,'name'=>$nick,'authid'=>$steam,'ip'=>$ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true),'message'=>$msg);
				$result = $db->Query("DELETE FROM `acp_chat_logs` ".$sqlconds, $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$delnum = $db->Affected();
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("chat_control", "delete logs: ".$delnum);
	
					if( $delnum > 0 )
					{
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

        case "3":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$time_delay = $_POST['delay'];
			$srv = $_POST['srv'];

			$sqlconds = ' WHERE 1=1';

			if ($config['cc_cmd'])
			{
				$cc_cmd = explode(',',$config['cc_cmd']);
				$sqlconds .= " AND cmd IN ('{cmd}')";

				if (is_numeric($config['cc_delay'])) { $sqlconds .= " AND timestamp <= '{time}'"; }
				if (!$config['cc_alive']) { $sqlconds .= " AND alive = '0'"; }
				if (!$config['cc_foradmins']) { $sqlconds .= " AND foradmins = '0'"; }
				if (!$config['cc_block_msg']) { $sqlconds .= " AND pattern IN ('0','-1')"; }
				if ($config['cc_servers'])
				{
					$servers = explode(",",$config['cc_servers']);
					if ($srv == 0)
					{ 
						$sqlconds .= " AND serverip IN ('{serverip}') ORDER BY `id` DESC";
					}
					else
					{
						$arguments = array('srv'=>$srv);
						$servers = $db->Query("SELECT address FROM `acp_servers` WHERE id = '{srv}'", $arguments, $config['sql_debug']);
						$sqlconds .= " AND serverip = '{serverip}' ORDER BY `id` DESC";
					}

					if ($config['cc_limit'] > 0)
					{
						if ($config['cc_limit'] >= $limit)
						{
							$limit = (($config['cc_limit'] - $offset) < $limit) ? $config['cc_limit'] - $offset : $limit;
							$sqlconds .= " LIMIT {offset},{limit}";
						}
						else
						{
							$sqlconds .= " LIMIT {offset},".$config['cc_limit'];
						}
					}
					else
					{
						$sqlconds .= " LIMIT {offset},{limit}";
					}

					$arguments = array('offset'=>$offset,'limit'=>$limit,'serverip'=>$servers,'time'=>$time_delay,'cmd'=>$cc_cmd);
					$result = $db->Query("SELECT * FROM `acp_chat_logs` $sqlconds", $arguments, $config['sql_debug']);

					if( is_array($result) )
					{
						foreach ($result as $obj)
						{
							$obj->timestamp = get_datetime($obj->timestamp, 'H:i:s d.m.Y');
							$messages[] = (array)$obj;
						}
					}
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
			$smarty->display('p_gamechat_list.tpl');

			break;

		default:

			die("Hacking Attempt");
	}
}

?>
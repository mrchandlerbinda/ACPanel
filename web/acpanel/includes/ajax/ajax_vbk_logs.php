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
	$filter = "lp_name='p_vbk_logs.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
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

	switch($_POST['go'])
	{
		case "1":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$server_ip = (isset($_POST['server_ip'])) ? $_POST['server_ip'] : '';
			$vote_type = (isset($_POST['vote_type'])) ? $_POST['vote_type'] : '';
			$vote_result = (isset($_POST['vote_result'])) ? $_POST['vote_result'] : '';
			$startdate = (isset($_POST['startdate'])) ? $_POST['startdate'] : '';
			$enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : '';
			$vote_player_nick = (isset($_POST['vote_player_nick'])) ? $_POST['vote_player_nick'] : '';
			$vote_player_id = (isset($_POST['vote_player_id'])) ? $_POST['vote_player_id'] : '';
			$vote_player_ip = (isset($_POST['vote_player_ip'])) ? $_POST['vote_player_ip'] : '';
			$nom_player_nick = (isset($_POST['nom_player_nick'])) ? $_POST['nom_player_nick'] : '';
			$nom_player_id = (isset($_POST['nom_player_id'])) ? $_POST['nom_player_id'] : '';
			$nom_player_ip = (isset($_POST['nom_player_ip'])) ? $_POST['nom_player_ip'] : '';

			$sqlconds = 'WHERE 1=1 ';

			if ($server_ip) { $sqlconds .= " AND server_ip = '{server_ip}' "; }
			if ($vote_type) { $sqlconds .= " AND vote_type = '{vote_type}' "; }
			if ($vote_result) { $sqlconds .= " AND vote_result = '{vote_result}' "; }
			if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
			if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }
			if ($vote_player_nick) { $sqlconds .= " AND vote_player_nick LIKE '%{vote_player_nick}%' "; }
			if ($vote_player_id) { $sqlconds .= " AND vote_player_id LIKE '%{vote_player_id}%' "; }
			if ($vote_player_ip) { $sqlconds .= " AND vote_player_ip LIKE '%{vote_player_ip}%' "; }
			if ($vote_player_nick) { $sqlconds .= " AND vote_player_nick LIKE '%{vote_player_nick}%' "; }
			if ($vote_player_id) { $sqlconds .= " AND vote_player_id LIKE '%{vote_player_id}%' "; }
			if ($vote_player_ip) { $sqlconds .= " AND vote_player_ip LIKE '%{vote_player_ip}%' "; }

			date_default_timezone_set('UTC');
			$arguments = array('offset'=>$offset, 'limit'=>$limit, 'server_ip'=>$server_ip, 'vote_type'=>$vote_type, 'vote_result'=>$vote_result, 'vote_player_nick'=>$vote_player_nick, 'vote_player_id'=>$vote_player_id, 'vote_player_ip'=>$vote_player_ip, 'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true), 'nom_player_nick'=>$nom_player_nick, 'nom_player_id'=>$nom_player_id, 'nom_player_ip'=>$nom_player_ip);
			$result = $db->Query("SELECT * FROM `acp_vbk_logs` $sqlconds ORDER BY `vote_id` DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$obj->voted_string = sprintf( $translate['voted_string'], $obj->vote_all, $obj->vote_yes, $obj->vote_need);
					$vbk_logs[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			if(isset($vbk_logs)) $smarty->assign("vbk_logs",$vbk_logs);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_vbk_logs_load.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('vbk_perm', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$server_ip = (isset($_POST['server_ip'])) ? $_POST['server_ip'] : '';
				$vote_type = (isset($_POST['vote_type'])) ? $_POST['vote_type'] : '';
				$vote_result = (isset($_POST['vote_result'])) ? $_POST['vote_result'] : '';
				$startdate = (isset($_POST['startdate'])) ? $_POST['startdate'] : '';
				$enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : '';
				$vote_player_nick = (isset($_POST['vote_player_nick'])) ? $_POST['vote_player_nick'] : '';
				$vote_player_id = (isset($_POST['vote_player_id'])) ? $_POST['vote_player_id'] : '';
				$vote_player_ip = (isset($_POST['vote_player_ip'])) ? $_POST['vote_player_ip'] : '';
				$nom_player_nick = (isset($_POST['nom_player_nick'])) ? $_POST['nom_player_nick'] : '';
				$nom_player_id = (isset($_POST['nom_player_id'])) ? $_POST['nom_player_id'] : '';
				$nom_player_ip = (isset($_POST['nom_player_ip'])) ? $_POST['nom_player_ip'] : '';
	
				$sqlconds = 'WHERE 1=1 ';
	
				if ($server_ip != 'all') { $sqlconds .= " AND server_ip = '{server_ip}' "; }
				if ($vote_type != 'all') { $sqlconds .= " AND vote_type = '{vote_type}' "; }
				if ($vote_result != 'all') { $sqlconds .= " AND vote_result = '{vote_result}' "; }
				if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
				if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }
				if ($vote_player_nick) { $sqlconds .= " AND vote_player_nick LIKE '%{vote_player_nick}%' "; }
				if ($vote_player_id) { $sqlconds .= " AND vote_player_id LIKE '%{vote_player_id}%' "; }
				if ($vote_player_ip) { $sqlconds .= " AND vote_player_ip LIKE '%{vote_player_ip}%' "; }
				if ($vote_player_nick) { $sqlconds .= " AND vote_player_nick LIKE '%{vote_player_nick}%' "; }
				if ($vote_player_id) { $sqlconds .= " AND vote_player_id LIKE '%{vote_player_id}%' "; }
				if ($vote_player_ip) { $sqlconds .= " AND vote_player_ip LIKE '%{vote_player_ip}%' "; }

				date_default_timezone_set('UTC');
				$arguments = array('server_ip'=>$server_ip, 'vote_type'=>$vote_type, 'vote_result'=>$vote_result, 'vote_player_nick'=>$vote_player_nick, 'vote_player_id'=>$vote_player_id, 'vote_player_ip'=>$vote_player_ip, 'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true), 'nom_player_nick'=>$nom_player_nick, 'nom_player_id'=>$nom_player_id, 'nom_player_ip'=>$nom_player_ip);
				$result = $db->Query("DELETE FROM `acp_vbk_logs` ".$sqlconds, $arguments, $config['sql_debug']);
	
				if ($result)
				{
					$delnum = $db->Affected();
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("vbk_logs", "delete logs: ".$delnum);
	
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

		default:

			die("Hacking Attempt");
	}
}

?>
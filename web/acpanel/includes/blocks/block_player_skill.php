<?php

$stats = false;
$cache_need_create = false;
$server = (isset($_GET['server'])) ? $_GET['server'] : 0;
$cache_prefix = 'player_skill_'.$server.'_'.$obj->blockid;

if( $server > 0 )
{
	include_once(INCLUDE_PATH . 'functions.servers.php');
	
	if( $config['stats_cache_blocks'] > 0 )
	{
		$stats = get_cache($cache_prefix, $config['stats_cache_blocks']*60);
		$cache_need_create = ($stats !== false) ? false : true;
	}
	
	if( $stats === false )
	{
		$stats = array();
		$limit = ($config['stats_max_top_block'] > 0 && is_numeric($config['stats_max_top_block'])) ? " LIMIT ".$config['stats_max_top_block'] : "";

		$query = $db->Query("SELECT u.username, u.avatar, b.userid, a.server_id, a.streak_kills, a.streak_deaths, a.kills, 
			a.kills_hs, a.deaths, a.kills_ff, a.deaths_ff, a.deaths_suicides, a.team_ct, a.team_t, a.connections, a.wins, a.position, a.skill 
			FROM `acp_stats_players_rank` a
			LEFT JOIN `acp_players` b ON b.userid = a.userid 
			LEFT JOIN `acp_users` u ON u.uid = a.userid
			WHERE a.server_id = '{srv}' ORDER BY a.position, a.skill".$limit, array('srv' => $server), $config['sql_debug']);
	
		if( is_array($query) )
		{
			foreach( $query as $obj )
			{
				switch($ext_auth_type)
				{
					case "xf":
		
						$xfUser = $xf->getUserInfo($obj->userid);
						$obj->avatar = ($obj->avatar) ? $xf->getAvatarFilePath('s', $obj->userid).'?'.$obj->avatar : $config['xfAuth']['forumUrl'].'styles/'.$config["template"].'/xenforo/avatars/avatar_'.( ($xfUser["gender"]) ? $xfUser["gender"]."_" : "" ).'s.png';
						$obj->avatar_date = $xfUser['avatar_date'];
						break;
		
					default:
		
						$obj->avatar_date = (strlen($obj->avatar)) ? 1 : 0;	
						$obj->avatar = ($obj->avatar) ? 'acpanel/images/avatars/s/'.$obj->avatar : 'acpanel/images/noavatar_s.gif';
						break;
				}

				$stats[] = (array)$obj;
			}

			if( $cache_need_create )
				create_cache($cache_prefix, serialize($stats));
		}
		else $blockEXIT = true;
	}
	else $stats = unserialize($stats);
	
	$smarty->assign("sts", $stats);
}
else $blockEXIT = true;

?>
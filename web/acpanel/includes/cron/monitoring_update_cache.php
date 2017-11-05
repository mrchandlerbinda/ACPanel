<?php
 
define("IN_ACP", true);

if( !is_null($this->db()) )
{
	// ###############################################################################
	// LOAD CONFIG
	// ###############################################################################
	
	$array_cfg = $this->db()->Query("SELECT varname, value FROM `acp_config` WHERE varname IS NOT NULL", array());
	
	if( is_array($array_cfg) )
	{
		foreach( $array_cfg as $obj )
		{
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();			
	}

	// ###############################################################################
	// LOAD GEO MODULE
	// ###############################################################################
	
	if( !isset($SxGeo) )
	{
		include_once(INCLUDE_PATH . 'class.SypexGeo.php');
		$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
	}

	include_once(INCLUDE_PATH . 'functions.servers.php');
	date_default_timezone_set('UTC');

	$currTime = time();
	list($currYear, $currMonth, $currDay, $currHour) = explode("-", date('Y-m-d-H', $currTime));
	$currDayString = $currYear."-".$currMonth."-".$currDay." 00:00:00";
	$currDayTime = strtotime($currDayString) - 1;

	$query = $this->db()->Query("SELECT a.id, a.gametype, a.address, a.hostname, a.cache_time, a.statistics, 
		(SELECT count(*) FROM `acp_servers_viewed` b WHERE b.server_id = a.id AND b.timestamp > a.cache_time) AS server_viewed, 
		(SELECT SUM(vote_value) FROM `acp_servers_votes` c WHERE c.item_id = a.id AND c.date > a.cache_time) AS server_votes 
		FROM `acp_servers` a WHERE a.active = 1 AND a.cache_time < (".$currTime." - 1 - ".$config['mon_cache_time'].") GROUP BY a.id", array());

	if( is_array($query) )
	{
		foreach( $query as $obj )
		{			
			$output_array = array('favorite' => false);
			$arguments = array('id' => $obj->id);
			$lists = explode(":", $obj->address);
			$ip = $lists[0];
			$port = $lists[1];
			$arguments['server_viewed'] = $obj->server_viewed;
			$arguments['server_votes'] = (is_null($obj->server_votes)) ? 0 : $obj->server_votes;
	
			$live = server_query_live($obj->gametype, $ip, $port, "ep");
	
			if( $live['b']['status'] && isset($live['s']['name']) )
			{
				$arguments['status'] = 1;
				$output_array['ping'] = (!$live['b']['ping']) ? 1 : $live['b']['ping'];
				$output_array['os'] = (isset($live['e']['os'])) ? $live['e']['os'] : "";
				$output_array['pass'] = (isset($live['s']['password'])) ? $live['s']['password'] : "";
				$output_array['vac'] = (isset($live['e']['anticheat'])) ? $live['e']['anticheat'] : "";
				$output_array['hostname'] = $live['s']['name'];
				$output_array['map'] = $arguments['cache_map'] = $live['s']['map'];
				$output_array['players'] = $arguments['cache_players'] = $live['s']['players'];
				$output_array['playersmax'] = $arguments['cache_playersmax'] = $live['s']['playersmax'];
			}
			else
			{
				$arguments['status'] = 0;
				$output_array['hostname'] = $output_array['map'] = $arguments['cache_map'] = $output_array['vac'] = $output_array['pass'] = $output_array['os'] = $output_array['ping'] = "";
				$output_array['players'] = $arguments['cache_players'] = 0;
				$output_array['playersmax'] = $arguments['cache_playersmax'] = 0;
			}
	
			clearstatcache();
	
			if( file_exists(ROOT_PATH . "images/maps/".$obj->gametype."/".$output_array['map'].".jpg") )
			{
				$output_array['map_path'] = $arguments['cache_map_path'] = 1;
			}
			else
			{
				$output_array['map_path'] = $arguments['cache_map_path'] = 0;
			}
	
			$server_country_code = strtolower($SxGeo->getCountry($ip));
			clearstatcache();
	
			if( file_exists(ROOT_PATH . "images/flags/".$server_country_code.".gif") )
			{
				$output_array['country'] = $arguments['cache_country'] = $server_country_code.".gif";
			}
			else
			{
				$output_array['country'] = $arguments['cache_country'] = "err.gif";
			}

			$arguments['cache'] = serialize($output_array);
			$arguments['cache_time'] = $currTime;
			$arguments['players_percent'] = (!$arguments['cache_playersmax']) ? 0 : round($arguments['cache_players']/$arguments['cache_playersmax']*100);

			$statistics = unserialize($obj->statistics);

			if( !is_array($statistics) )
			{
				$i = 0;
				$currDateString = $currYear."-".$currMonth."-".$currDay." ".$currHour.":00:00";
				$startTime = strtotime($currDateString) - (3600*24);
				while( $i < 24 )
				{
					$index = (string)($startTime*1000);
					$statistics["d"]["players"][$index] = $statistics["d"]["uptime"][$index] = $statistics["d"]["viewed"][$index] = $statistics["d"]["votes"][$index] = 0;
					$startTime = $startTime + 3600;
					$i++;
				}

				$i = 0;
				$currDateString = $currYear."-".$currMonth."-".$currDay." 00:00:00";
				$startTime = strtotime($currDateString) - (3600*24*7);
				while( $i < 7 )
				{
					$index = (string)($startTime*1000);
					$statistics["w"]["players"][$index] = $statistics["w"]["uptime"][$index] = $statistics["w"]["viewed"][$index] = $statistics["w"]["votes"][$index] = 0;
					$startTime = $startTime + 86400;
					$i++;
				}

				$i = 0;
				$currDateString = $currYear."-".$currMonth."-01 00:00:00";
				$startTime = strtotime("1 year ago", strtotime($currDateString));
				while( $i < 12 )
				{
					$index = (string)($startTime*1000);
					$statistics["y"]["players"][$index] = $statistics["y"]["uptime"][$index] = $statistics["y"]["viewed"][$index] = $statistics["y"]["votes"][$index] = 0;
					$startTime = strtotime("next month", $startTime);
					$i++;
				}
			}

			$query_insert = $this->db()->Query("INSERT INTO `acp_servers_statistics` SET serverid = ".$obj->id.", active = '{status}', players = '{players_percent}', map = '{cache_map}', dateline = '{cache_time}', viewed = '{server_viewed}', votes = '{server_votes}'", $arguments);

			$select_statistics = $this->db()->Query("SELECT dateline, FROM_UNIXTIME(dateline, '%Y-%m-%d-%H') AS time, SUM(players) AS sumplayers, SUM(active) AS online, count(statsid) AS cnt, SUM(viewed) AS viewed, SUM(votes) AS votes, GROUP_CONCAT(DISTINCT map SEPARATOR ',') AS maps 
				FROM `acp_servers_statistics` WHERE serverid = ".$obj->id." GROUP BY time", array());

			if( is_array($select_statistics) )
			{
				foreach( $select_statistics as $stats )
				{
					$bd_time = date('Y-m-d-H', $stats->dateline);
					list($bdYear, $bdMonth, $bdDay, $bdHour) = explode("-", $bd_time);

					if( $bdYear.$bdMonth.$bdDay.$bdHour < $currYear.$currMonth.$currDay.$currHour )
					{
						// STATS BY HOURS
						$time = strtotime($bdYear."-".$bdMonth."-".$bdDay." ".$bdHour.":00:00");
						$index = (string)($time*1000);
						$avg_players = round($stats->sumplayers/$stats->cnt, 2);
						$avg_uptime = round($stats->online*100/$stats->cnt, 2);
						if( !isset($statistics["d"]["players"][$index]) )
						{
							reset($statistics["d"]["players"]);
							unset($statistics["d"]["players"][key($statistics["d"]["players"])]);
						}
						$statistics["d"]["players"][$index] = $avg_players;

						if( !isset($statistics["d"]["uptime"][$index]) )
						{
							reset($statistics["d"]["uptime"]);
							unset($statistics["d"]["uptime"][key($statistics["d"]["uptime"])]);
						}
						$statistics["d"]["uptime"][$index] = $avg_uptime;

						if( !isset($statistics["d"]["viewed"][$index]) )
						{
							reset($statistics["d"]["viewed"]);
							unset($statistics["d"]["viewed"][key($statistics["d"]["viewed"])]);
						}
						$statistics["d"]["viewed"][$index] = $stats->viewed;

						if( !isset($statistics["d"]["votes"][$index]) )
						{
							reset($statistics["d"]["votes"]);
							unset($statistics["d"]["votes"][key($statistics["d"]["votes"])]);
						}
						$statistics["d"]["votes"][$index] = $stats->votes;

						// STATS BY DAY
						$time = strtotime($bdYear."-".$bdMonth."-".$bdDay." 00:00:00");
						$index = (string)($time*1000);
						$avg_players_day = round($avg_players/24, 2);
						$avg_uptime_day = round($avg_uptime/24, 2);
						$sum_viewed_day = $stats->viewed;
						$sum_votes_day = $stats->votes;
						if( isset($statistics["w"]["players"][$index]) )
						{
							$avg_players_day += $statistics["w"]["players"][$index];
						
							if( $avg_players_day > 100 )
								$avg_players_day = 100;
						}
						else
						{
							reset($statistics["w"]["players"]);
							unset($statistics["w"]["players"][key($statistics["w"]["players"])]);
						}
						$statistics["w"]["players"][$index] = round($avg_players_day, 2);

						if( isset($statistics["w"]["uptime"][$index]) )
						{
							$avg_uptime_day += $statistics["w"]["uptime"][$index];
						
							if( $avg_uptime_day > 100 )
								$avg_uptime_day = 100;
						}
						else
						{
							reset($statistics["w"]["uptime"]);
							unset($statistics["w"]["uptime"][key($statistics["w"]["uptime"])]);
						}
						$statistics["w"]["uptime"][$index] = round($avg_uptime_day, 2);

						if( !isset($statistics["w"]["viewed"][$index]) )
						{
							reset($statistics["w"]["viewed"]);
							unset($statistics["w"]["viewed"][key($statistics["w"]["viewed"])]);
						}
						else
						{
							$sum_viewed_day += $statistics["w"]["viewed"][$index];
						}
						$statistics["w"]["viewed"][$index] = $sum_viewed_day;

						if( !isset($statistics["w"]["votes"][$index]) )
						{
							reset($statistics["w"]["votes"]);
							unset($statistics["w"]["votes"][key($statistics["w"]["votes"])]);
						}
						else
						{
							$sum_votes_day += $statistics["w"]["votes"][$index];
						}
						$statistics["w"]["votes"][$index] = $sum_votes_day;

						// STATS BY MONTH
						$time = strtotime($bdYear."-".$bdMonth."-01 00:00:00");
						$index = (string)($time*1000);
						$count_days = date('t', $time);
						$avg_players_month = round($avg_players/(24*$count_days), 2);
						$avg_uptime_month = round($avg_uptime/(24*$count_days), 2);
						$sum_viewed_month = $stats->viewed;
						$sum_votes_month = $stats->votes;
						if( isset($statistics["y"]["players"][$index]) )
						{
							$avg_players_month += $statistics["y"]["players"][$index];
						
							if( $avg_players_month > 100 )
								$avg_players_month = 100;
						}
						else
						{
							reset($statistics["y"]["players"]);
							unset($statistics["y"]["players"][key($statistics["y"]["players"])]);
						}
						$statistics["y"]["players"][$index] = round($avg_players_month, 2);

						if( isset($statistics["y"]["uptime"][$index]) )
						{
							$avg_uptime_month += $statistics["y"]["uptime"][$index];
						
							if( $avg_uptime_month > 100 )
								$avg_uptime_month = 100;
						}
						else
						{
							reset($statistics["y"]["uptime"]);
							unset($statistics["y"]["uptime"][key($statistics["y"]["uptime"])]);
						}
						$statistics["y"]["uptime"][$index] = round($avg_uptime_month, 2);

						if( !isset($statistics["y"]["viewed"][$index]) )
						{
							reset($statistics["y"]["viewed"]);
							unset($statistics["y"]["viewed"][key($statistics["y"]["viewed"])]);
						}
						else
						{
							$sum_viewed_month += $statistics["y"]["viewed"][$index];
						}
						$statistics["y"]["viewed"][$index] = $sum_viewed_month;

						if( !isset($statistics["y"]["votes"][$index]) )
						{
							reset($statistics["y"]["votes"]);
							unset($statistics["y"]["votes"][key($statistics["y"]["votes"])]);
						}
						else
						{
							$sum_votes_month += $statistics["y"]["votes"][$index];
						}
						$statistics["y"]["votes"][$index] = $sum_votes_month;
					}
				}
			}
			$arguments['statistics'] = serialize($statistics);

			$query_update = $this->db()->Query("UPDATE `acp_servers` SET status = '{status}', cache = '{cache}', cache_time = '{cache_time}', cache_country = '{cache_country}', cache_map = '{cache_map}', cache_map_path = '{cache_map_path}', cache_players = '{cache_players}', cache_playersmax = '{cache_playersmax}', statistics = '{statistics}' WHERE id = ".$obj->id, $arguments);

			$hour_now = strtotime($currYear."-".$currMonth."-".$currDay." ".$currHour.":00:00");
			$hour_now = (int)$hour_now;
			$query_delete = $this->db()->Query("DELETE FROM `acp_servers_statistics` WHERE serverid = ".$obj->id." AND dateline < ".$hour_now, array());
		}
		$life_time_view = time() - ($config['mon_view_lifetime']*60*60);
		$query_delete = $this->db()->Query("DELETE FROM `acp_servers_viewed` WHERE timestamp < ".$life_time_view, array());

		$life_time_vote = time() - ($config['mon_vote_lifetime']*60);
		$query_delete = $this->db()->Query("DELETE FROM `acp_servers_votes` WHERE date < ".$life_time_vote, array());
	}
}
 
?>
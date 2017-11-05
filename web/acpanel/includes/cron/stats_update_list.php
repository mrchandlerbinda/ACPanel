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

	include_once(INCLUDE_PATH . 'functions.servers.php');
	date_default_timezone_set('UTC');

	$currTime = time();
	list($currYear, $currMonth, $currDay, $currHour) = explode("-", date('Y-m-d-H', $currTime));
	$currDayString = $currYear."-".$currMonth."-".$currDay." 00:00:00";
	$currDayTime = strtotime($currDayString) - 1;

	// CHECK PLAYERS FOR UPDATE
	$arrUpdate = $this->db()->Query("SELECT userid, server_id, kills, kills_hs, kills_ff, deaths, deaths_suicides, 
		deaths_ff, streak_kills, streak_deaths, team_ct, team_t, wins, last_visit, connections, online, skill, history, updated 
		FROM `acp_stats_players_rank` WHERE updated = 0 GROUP BY userid, server_id", array());

	if( is_array($arrUpdate) )
	{
		function convertToRPN($equation)
		{
			$equation = str_replace(' ', '', $equation);
			$tokens = token_get_all('<?php ' . $equation);
		
			$operators = array('*' => 1, '/' => 1, '+' => 2, '-' => 2);
			$rpn = '';
			$stack = array();
		
			for($i = 1; $i < count($tokens); $i++)
			{
				if( is_array($tokens[$i]) )
				{
					$rpn .= $tokens[$i][1] . ' ';
				}
				else
				{
				        if( empty($stack) || $tokens[$i] == '(' )
					{
						$stack[] = $tokens[$i];
				        }
					else
					{
				                if( $tokens[$i] == ')' )
						{
							while( end($stack) != '(' )
								$rpn .= array_pop($stack) . ' ';
							
							array_pop($stack);
				                }
						else
						{
							while( !empty($stack) && end($stack) != '(' && $operators[$tokens[$i]] >= $operators[end($stack)] )
								$rpn .= array_pop($stack) . ' ';
		
							$stack[] = $tokens[$i];
				                }
				        }
				}
			}
			
			while( !empty($stack) )
				$rpn .= array_pop($stack);
			
			return $rpn;
		}

		function polishDistortion($str)
		{
			$stack = explode(' ', trim(preg_replace('/[[:space:]]{2,}/', ' ', $str)));
			
			$cnt = count($stack);
			for( $i=0; $i<$cnt; $i++ )
			{
				if( in_array($stack[$i], array('-', '+', '*', '/')) && !is_numeric($stack[$i]) )
				{
					// for debug
					// echo join(' ',$stack).'<br />';
					
					if( $i < 2 ) { return false; }
					
					eval('$stack[$i]=$stack[($i-2)]'.$stack[$i].'$stack[($i-1)];');
					unset($stack[($i-1)]);
					unset($stack[($i-2)]);
					$stack = array_values($stack);
					
					$i = 0;
					$cnt = count($stack);
				}
			}
			return $stack[0];
		}

		class MyCallback
		{
			private $key = array();
			private $vars = array();
			
			public function setValue($val)
			{
				$this->key = $val;
				$this->vars = array();
			}

			public function getRating()
			{
				return $this->vars;
			}
			
			public function holdersReplace($matches)
			{
				$placeholder = $matches[1];
				$arr = $this->key;
				$return = "";

				if( isset($arr[$placeholder]) )
				{
					$this->vars[$placeholder] = $return = $arr[$placeholder];
				}

				return $return;
			}
		}

		$callback = new MyCallback();

		foreach( $arrUpdate as $obj )
		{
			$statistics = unserialize($obj->history);

			if( !is_array($statistics) )
			{
				$i = 0;
				$currDateString = $currYear."-".$currMonth."-".$currDay." ".$currHour.":00:00";
				$startTime = strtotime($currDateString) - (3600*24);
				while( $i < 24 )
				{
					$index = $startTime*1000;
					$statistics["d"]["skill"][$index] = $statistics["d"]["online"][$index] = 0;
					$startTime = $startTime + 3600;
					$i++;
				}

				$i = 0;
				$currDateString = $currYear."-".$currMonth."-".$currDay." 00:00:00";
				$startTime = strtotime($currDateString) - (3600*24*7);
				while( $i < 7 )
				{
					$index = $startTime*1000;
					$statistics["w"]["skill"][$index] = $statistics["w"]["online"][$index] = 0;
					$startTime = $startTime + 86400;
					$i++;
				}

				$i = 0;
				$currDateString = $currYear."-".$currMonth."-01 00:00:00";
				$startTime = strtotime("1 year ago", strtotime($currDateString));
				while( $i < 12 )
				{
					$index = $startTime*1000;
					$statistics["y"]["skill"][$index] = $statistics["y"]["online"][$index] = 0;
					$startTime = strtotime("next month", $startTime);
					$i++;
				}
			}

			$arrHolders = array(
				'kills' => (!$obj->kills) ? 1 : $obj->kills,
				'deaths' => (!$obj->deaths) ? 1 : $obj->deaths,
				'wins' => (!$obj->wins) ? 1 : $obj->wins,
				'online' => (!$obj->online) ? 1 : $obj->online,
				'team_t' => (!$obj->team_t) ? 1 : $obj->team_t,
				'team_ct' => (!$obj->team_ct) ? 1 : $obj->team_ct,
				'hs' => (!$obj->kills_hs) ? 1 : $obj->kills_hs,
				'streak_kills' => (!$obj->streak_kills) ? 1 : $obj->streak_kills,
				'streak_deaths' => (!$obj->streak_deaths) ? 1 : $obj->streak_deaths,
				'activity' => 0
			);

			if( $config['stats_skill_min_kills'] < 1 ) $config['stats_skill_min_kills'] = 1;
			if( $config['stats_skill_min_kills'] > $obj->kills )
				$skillResult = 1;
			else
			{
				// Result formula component: {activity}
				$nAct = ($currTime - $obj->last_visit)/3600;
				$arrHolders['activity'] = ( $nAct < 24 ) ? 100 : (($nAct > 744) ? 0 : 2400/$nAct);
	
				$callback->setValue($arrHolders);
	
				$skillFormula = preg_replace_callback("#{([^}]+)}#sUi", array($callback, 'holdersReplace'), $config['stats_skill_formula']);
	
				if( ($skillResult = polishDistortion(convertToRPN($skillFormula))) === FALSE )
					$skillResult = 1;
				else
					$skillResult = round($skillResult);
			}

			$select_statistics = $this->db()->Query("SELECT a.date, SUM(a.online) AS online FROM `acp_stats_players` a LEFT JOIN `acp_servers` b ON b.address = a.serverip 
				WHERE a.updated = 1 AND a.dbid = ".$obj->userid." AND b.id = ".$obj->server_id." GROUP BY a.date", array());

			if( is_array($select_statistics) )
			{
				foreach( $select_statistics as $stats )
				{
					list($bdYear, $bdMonth, $bdDay, $bdHour) = explode("-", $stats->date);

					// STATS BY HOURS
					$time = strtotime($bdYear."-".$bdMonth."-".$bdDay." ".$bdHour.":00:00");
					$index = $time*1000;
					if( !isset($statistics["d"]["online"][$index]) )
					{
						reset($statistics["d"]["online"]);
						unset($statistics["d"]["online"][key($statistics["d"]["online"])]);
						$statistics["d"]["online"][$index] = $stats->online;
					}
					else $statistics["d"]["online"][$index] += $stats->online;

					// STATS BY DAY
					$time = strtotime($bdYear."-".$bdMonth."-".$bdDay." 00:00:00");
					$index = $time*1000;
					$online_day = $stats->online;
					if( isset($statistics["w"]["online"][$index]) )
					{
						$online_day += $statistics["w"]["online"][$index];
					}
					else
					{
						reset($statistics["w"]["online"]);
						unset($statistics["w"]["online"][key($statistics["w"]["online"])]);
					}
					$statistics["w"]["online"][$index] = $online_day;

					// STATS BY MONTH
					$time = strtotime($bdYear."-".$bdMonth."-01 00:00:00");
					$index = $time*1000;
					$online_month = $stats->online;
					if( isset($statistics["y"]["online"][$index]) )
					{
						$online_month += $statistics["y"]["online"][$index];
					}
					else
					{
						reset($statistics["y"]["online"]);
						unset($statistics["y"]["online"][key($statistics["y"]["online"])]);
					}
					$statistics["y"]["online"][$index] = $online_month;
				}
			}

			$arguments = array('statistics' => serialize($statistics), 'skill' => $skillResult);

			// UPDATE TEMP TABLE
			$query = $this->db()->Query("UPDATE `acp_stats_players_rank` SET updated = 1, skill = '{skill}', history = '{statistics}' WHERE userid = ".$obj->userid." AND server_id = ".$obj->server_id, $arguments);
		}

		// UPDATE POSITIONS
		$query = $this->db()->Query("UPDATE `acp_stats_players_rank` o JOIN
			(SELECT s.userid, s.server_id,
				IF(@lastRating <> s.skill, IF(@lastServer = s.server_id, @curPosition := @curPosition + @nextPosition, @curPosition := 1), IF(@lastServer = s.server_id, @curPosition, @curPosition := 1)) AS position,  
				IF(@lastRating = s.skill, @nextPosition := @nextPosition + 1, @nextPosition := 1), 
				@lastServer := s.server_id, @lastRating := s.skill  
				FROM `acp_stats_players_rank` s JOIN 
				(SELECT @curPosition := 0, @lastRating := 0, @nextPosition := 1, @lastServer := 0) t ORDER BY s.server_id, s.skill DESC
			) r ON (r.userid = o.userid) SET o.position = r.position WHERE r.server_id = o.server_id", array());
	}
	else
	{
		// UPDATE TEMP TABLE
		$query = $this->db()->Query("UPDATE `acp_stats_players` SET updated = 2 WHERE updated = 1", array());

		// UPDATE PLAYERS LIST FROM TEMP TABLE
		$args = array('time' => ($currTime - 1 - $config['stats_cache_time']));
		$query_insert = $this->db()->Query("INSERT INTO `acp_stats_players_rank` 
			(userid, server_id, kills, kills_hs, kills_ff, deaths, deaths_suicides, deaths_ff, streak_kills, streak_deaths, team_ct, team_t, wins, last_visit, connections, online, updated) 
			(SELECT a.dbid, b.id, SUM(a.kills), SUM(a.headshotkills), SUM(a.ffkills), SUM(a.deaths), SUM(a.suicides), SUM(a.ffdeaths), MAX(a.streak_kills), MAX(a.streak_deaths), 
				SUM(a.ct_team), SUM(a.t_team), SUM(a.wins), MAX(a.last_time), SUM(a.connections), SUM(a.online), 0 
				FROM `acp_stats_players` a LEFT JOIN `acp_servers` b ON b.address = a.serverip 
				WHERE a.last_time < '{time}' AND a.updated = 0 GROUP BY dbid, serverip) 
			ON DUPLICATE KEY UPDATE kills = kills + VALUES(kills), kills_hs = kills_hs + VALUES(kills_hs), kills_ff = kills_ff + VALUES(kills_ff), deaths = deaths + VALUES(deaths), 
			deaths_suicides = deaths_suicides + VALUES(deaths_suicides), deaths_ff = deaths_ff + VALUES(deaths_ff), streak_kills = IF(streak_kills > VALUES(streak_kills), streak_kills, VALUES(streak_kills)), 
			streak_deaths = IF(streak_deaths > VALUES(streak_deaths), streak_deaths, VALUES(streak_deaths)), team_ct = team_ct + VALUES(team_ct), team_t = team_t + VALUES(team_t), 
			wins = wins + VALUES(wins), last_visit = VALUES(last_visit), connections = connections + VALUES(connections), online = online + VALUES(online), updated = 0 
		", $args);

		// UPDATE TEMP TABLE
		if( $query_insert ) $query_update = $this->db()->Query("UPDATE `acp_stats_players` SET updated = 1 WHERE last_time < '{time}' AND updated = 0", $args);
	}
}
 
?>
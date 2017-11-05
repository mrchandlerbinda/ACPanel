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

	// CHECK SERVERS FOR UPDATE RATING
	$arrUpdate = $this->db()->Query("SELECT a.server_id, a.server_votes_up, a.server_votes_down, a.server_rating, a.server_descr, 
		a.server_banner, b.statistics, b.opt_site, a.check_time_prcy, a.check_time_banner, a.vk_likes, a.check_time_vklike 
		FROM `acp_servers_rating_temp` a 
		LEFT JOIN `acp_servers` b ON b.id = a.server_id 
		WHERE a.updated = 0", array());

	$cfg = self::$cfg;

	if( is_array($arrUpdate) )
	{
		include_once(INCLUDE_PATH . 'class.PrCy.php');

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
		$prcy = new PRCY();

		date_default_timezone_set('UTC');

		$currTimestamp = time();
		list($currYear, $currMonth, $currDay, $currHour) = explode("-", date('Y-m-d-H', $currTimestamp));
		$currDateString = $currYear."-".$currMonth."-".$currDay." 00:00:00";
		$currDayTimestamp = (string)(strtotime($currDateString)*1000);

		// GET CATEGORY ID AND SECTION ID FOR SERVER CARD
		$serverCardPage = array();
		$arrCats = $this->db()->Query("SELECT categoryid, sectionid FROM `acp_category` WHERE link = 'p_server_card'", array());
		if( is_array($arrCats) )
		{
			foreach( $arrCats as $cat )
			{
				$serverCardPage = (array)$cat;
			}
		}

		$php_self = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], "/"));
		$php_self = substr($php_self, 0, strrpos($php_self, "/") + 1);

		foreach( $arrUpdate as $obj )
		{
			$arrHolders = array(
				'description' => 0,
				'viewed' => 0,
				'votes' => 0,
				'uptime' => 0,
				'online' => 0,
				'pr' => 0,
				'cy' => 0,
				'banner' => 0,
				'vklikes' => 0
			);

			$statistics = unserialize($obj->statistics);

			// Result formula component: {description}
			$arrHolders['description'] = $obj->server_descr;

			// Result formula component: {viewed}
			$viewed = (isset($statistics['w']['viewed'][$currDayTimestamp])) ? $statistics['w']['viewed'][$currDayTimestamp] : 0;
			$arrHolders['viewed'] = $viewed;

			// Result formula component: {votes}
			$votes = (isset($statistics['w']['votes'][$currDayTimestamp])) ? $statistics['w']['votes'][$currDayTimestamp] : 0;
			$arrHolders['votes'] = $votes;

			// Result formula component: {uptime}
			$uptime = (isset($statistics['w']['uptime'][$currDayTimestamp])) ? $statistics['w']['uptime'][$currDayTimestamp] : 0;
			$arrHolders['uptime'] = $uptime;

			// Result formula component: {online}
			$online = (isset($statistics['w']['players'][$currDayTimestamp])) ? $statistics['w']['players'][$currDayTimestamp] : 0;
			$arrHolders['online'] = $online;

			// Result formula components: {pr}, {cy} and {banner}
			if( $obj->opt_site )
			{
				$needCheckPRCY = ($config['mon_time_prcy'] > 0 && $currTimestamp > ($config['mon_time_prcy'] * 86400 + $obj->check_time_prcy)) ? true : false;
				$gpr = (!is_numeric($obj->server_site_pr) || $needCheckPRCY) ? $prcy->GetPR($obj->opt_site) : $obj->server_site_pr;
				$ycy = (!is_numeric($obj->server_site_cy) || $needCheckPRCY) ? $prcy->GetCY($obj->opt_site) : $obj->server_site_cy;
				$arrHolders['pr'] = $gpr;
				$arrHolders['cy'] = $ycy;
				$time_prcy = ($needCheckPRCY) ? $currTimestamp : $obj->check_time_prcy;

				$needCheckBANNER = ($config['mon_time_site'] > 0 && $currTimestamp > ($config['mon_time_site'] * 3600 + $obj->check_time_banner)) ? true : false;
				$banner = ($needCheckBANNER) ? $prcy->checkBanner($obj->opt_site, $_SERVER['SERVER_NAME']) : $obj->server_banner;
				$arrHolders['banner'] = $banner;

				$time_banner = ($needCheckBANNER) ? $currTimestamp : $obj->check_time_banner;
			}
			else
			{
				$arrHolders['pr'] = $arrHolders['cy'] = $arrHolders['banner'] = 0;
				$gpr = $ycy = $banner = null;
				$time_prcy = $obj->check_time_prcy;
				$time_banner = $obj->check_time_banner;
			}

			// Result VK-Likes
			if( ($currTimestamp > ($config['mon_time_vklike']*60 + $obj->check_time_vklike)) && is_numeric($config['vkid']) && !empty($serverCardPage) )
			{
				$page = 'http://'.$_SERVER['SERVER_NAME'].$php_self.$cfg['acpanel'].'.php?cat='.$serverCardPage['sectionid'].'&do='.$serverCardPage['categoryid'].'&server='.$obj->server_id.'&t=0';
				$obj->vk_likes = $prcy->getVKLIKE($config['vkid'], $page);
				$obj->check_time_vklike = $currTimestamp;
			}
			$arrHolders['vklikes'] = $obj->vk_likes;

			$callback->setValue($arrHolders);

			$ratingFormula = preg_replace_callback("#{([^}]+)}#sUi", array($callback, 'holdersReplace'), $config['rating_formula']);

			if( ($ratingResult = polishDistortion(convertToRPN($ratingFormula))) === FALSE )
				$ratingResult = 1;
			else
				$ratingResult = round($ratingResult);

			$ratingVars = $callback->getRating();

			// UPDATE RATING IN TEMP TABLE
			$args = array('vk_likes' => $obj->vk_likes, 'check_time_vklike' => $obj->check_time_vklike, 'rating' => $ratingResult, 'rating_vars' => serialize($ratingVars), 'pr' => $gpr, 'cy' => $ycy, 'banner' => $banner, 'time_prcy' => $time_prcy, 'time_banner' => $time_banner);
			$query = $this->db()->Query("UPDATE `acp_servers_rating_temp` SET updated = 1, server_rating = '{rating}', server_rating_vars = '{rating_vars}', server_site_pr = '{pr}', 
				server_site_cy = '{cy}', server_banner = '{banner}', check_time_prcy = '{time_prcy}', check_time_banner = '{time_banner}', vk_likes = '{vk_likes}', check_time_vklike = '{check_time_vklike}' 
				WHERE server_id = ".$obj->server_id, $args);
		}
	}
	else
	{
		// UPDATE POSITIONS IN TEMP TABLE
		$query = $this->db()->Query("UPDATE `acp_servers_rating_temp` o JOIN
				(SELECT s.server_id, 
					IF(@lastRating <> s.server_rating, @curPosition := @curPosition + @nextPosition, @curPosition) AS server_position,  
					IF(@lastRating = s.server_rating, @nextPosition := @nextPosition + 1, @nextPosition := 1), @lastRating := s.server_rating  
					FROM `acp_servers_rating_temp` s JOIN 
					(SELECT @curPosition := 0, @lastRating := 0, @nextPosition := 1) t ORDER BY s.server_rating DESC
				) r ON (r.server_id = o.server_id) SET o.server_position = r.server_position", array());

		// UPDATE RATING AND POSITIONS IN GENERAL TABLE
		$query = $this->db()->Query("UPDATE `acp_servers` s JOIN `acp_servers_rating_temp` t ON (t.server_id = s.id) SET s.rating = t.server_rating, s.position = t.server_position, s.rating_vars = t.server_rating_vars WHERE t.updated = 1", array());

		// UPDATE SERVERS LIST IN TEMP TABLE
		$descr_length = (is_numeric($config['mon_descr_length'])) ? abs($config['mon_descr_length']) : 0;
		$query = $this->db()->Query("INSERT INTO `acp_servers_rating_temp` (server_id, server_votes_up, server_votes_down, server_descr) 
			SELECT id, votes_up, votes_down, IF(LENGTH(description) > {descr_length}, 1, 0) FROM `acp_servers` WHERE active = 1 
			ON DUPLICATE KEY UPDATE updated = 0, server_votes_up = VALUES(server_votes_up), server_votes_down = VALUES(server_votes_down), server_descr = VALUES(server_descr)", array('descr_length' => $descr_length));
	}
}
 
?>
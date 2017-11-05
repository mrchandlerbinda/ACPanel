<?php

if (!defined('IN_ACP')) die("Hacking attempt!");

function microtime_float()
{
        list($usec, $sec) = explode(' ', microtime());
        return ((float) $usec + (float) $sec);
}

function server_protocol_list()
{
	return array(
		"cstrike"      => array("code" => "05", "name" => "Counter-Strike 1.6"),
		"css"      => array("code" => "05", "name" => "Counter-Strike Source"),
		"csgo"      => array("code" => "05", "name" => "Counter-Strike GO"),
		"tf2"      => array("code" => "05", "name" => "Team Fortress 2"),
		"callofduty4"    => array("code" => "02", "name" => "Call of Duty 4"),
		"samp"    => array("code" => "12", "name" => "San Andreas Multiplayer"),
		"minecraft"      => array("code" => "99", "name" => "Minecraft")
	);
}

function server_query_live($type, $ip, $port, $request)
{
	$server_protocol_list = server_protocol_list();
	
	if( !isset($server_protocol_list[$type]) )
	{
		return false;
	}

	$query_function = "server_query_{$server_protocol_list[$type]['code']}";
	
	$server = array(
		"b" => array("type" => $type, "ip" => $ip, "port" => $port, "status" => 1, "ping" => 0),
		"s" => array("game" => "", "name" => "", "map" => "", "players" => 0, "playersmax" => 0, "password" => ""),
		"e" => array(),
		"p" => array(),
		"t" => array()
	);

	if ($query_function == "server_query_01")
	{
		$query_need = ""; $query_fp = "";
		$response = call_user_func_array($query_function, array(&$server, &$query_need, &$query_fp));
		return $server;
	}

	if ($query_function == "server_query_30")
	{
		$response = server_query_direct($server, $request, $query_function, "tcp");
	}
	else
	{
		$response = server_query_direct($server, $request, $query_function, "udp");
	}

	if (!$response) // SERVER OFFLINE
	{
		$server['b']['status'] = 0;
	}
	else
	{
		// IF NOT RETURNED USE THE TYPE AS THE GAME
		if (empty($server['s']['game'])) { $server['s']['game'] = $type; }

		// REMOVE FOLDERS FROM MAP NAMES
		if (($pos = strrpos($server['s']['map'], "/"))  !== FALSE) { $server['s']['map'] = substr($server['s']['map'], $pos + 1); }
		if (($pos = strrpos($server['s']['map'], "\\")) !== FALSE) { $server['s']['map'] = substr($server['s']['map'], $pos + 1); }

		// PLAYER COUNT AND PASSWORD STATUS SHOULD BE NUMERIC
		$server['s']['players']    = intval($server['s']['players']);
		$server['s']['playersmax'] = intval($server['s']['playersmax']);

		if (strtolower($server['s']['password']) == "false") { $server['s']['password'] = 0; }
		if (strtolower($server['s']['password']) == "true")  { $server['s']['password'] = 1; }

		$server['s']['password']   = intval($server['s']['password']);

		// REMOVE UN-REQUESTED AND UN-USED ARRAYS

		if (strpos($request, "p") === FALSE && empty($server['p']) && $server['s']['players'] != 0) { unset($server['p']); }
		if (strpos($request, "p") === FALSE && empty($server['t'])) { unset($server['t']); }
		if (strpos($request, "e") === FALSE && empty($server['e'])) { unset($server['e']); }
		if (strpos($request, "s") === FALSE && empty($server['s']['name']) && empty($server['s']['map'])) { unset($server['s']); }
	}

	return $server;
}

function server_query_direct(&$server, $request, $query_function, $scheme)
{
	$query_fp = @fsockopen("{$scheme}://{$server['b']['ip']}", $server['b']['port'], $errno, $errstr, 1);

	if (!$query_fp) { return FALSE; }
	
	stream_set_timeout($query_fp, 1, 0);
	stream_set_blocking($query_fp, TRUE);

	$query_need = array();
	$query_need['s'] = strpos($request, "s") !== FALSE ? TRUE : FALSE;
	$query_need['e'] = strpos($request, "e") !== FALSE ? TRUE : FALSE;
	$query_need['p'] = strpos($request, "p") !== FALSE ? TRUE : FALSE;

	// ChANGE [e] TO [s][e] AS BASIC QUERIES OFTEN RETURN EXTRA INFO
	if ($query_need['e'] && !$query_need['s']) { $query_need['s'] = TRUE; }

	do
	{
		$query_need_check = $query_need;
		
		// CALL FUNCTION REQUIRES '&$variable' TO PASS 'BY REFERENCE'
		$response = call_user_func_array($query_function, array(&$server, &$query_need, &$query_fp));
		
		// CHECK IF SERVER IS OFFLINE
		if (!$response) { break; }
		
		// CHECK IF NEED HAS NOT CHANGED - THIS SERVES TWO PURPOSES - TO PREVENT INFINITE LOOPS - AND TO
		// AVOID WRITING $query_need = FALSE FALSE FALSE FOR GAMES THAT RETURN ALL DATA IN ONE RESPONSE
		if ($query_need_check == $query_need) { break; }
		
		// OPTIMIZATION THAT SKIPS REQUEST FOR PLAYER DETAILS WHEN THE SERVER IS KNOWN TO BE EMPTY
		if ($query_need['p'] && $server['s']['players'] == "0") { $query_need['p'] = FALSE; }
	}
	while ($query_need['s'] == TRUE || $query_need['e'] == TRUE || $query_need['p'] == TRUE);

	@fclose($query_fp);

	return $response;
}

function parse_color($string, $type)
{
	switch($type)
	{
		case "1":
			$string = preg_replace("/\^x.../", "", $string);
			$string = preg_replace("/\^./",    "", $string);
	
			$string_length = strlen($string);
			for( $i=0; $i<$string_length; $i++ )
			{
				$char = ord($string[$i]);
				if ($char > 160) { $char = $char - 128; }
				if ($char > 126) { $char = 46; }
				if ($char == 16) { $char = 91; }
				if ($char == 17) { $char = 93; }
				if ($char  < 32) { $char = 46; }
				$string[$i] = chr($char);
			}
			break;
	
		case "2":
			$string = preg_replace("/\^[\x20-\x7E]/", "", $string);
			break;
	}

	return $string;
}

function server_query_02(&$server, &$query_need, &$query_fp)
{
	fwrite($query_fp, "\xFF\xFF\xFF\xFFgetstatus");
	$buffer = fread($query_fp, 4096);

	if( !$buffer ) { return FALSE; }

	$part = explode("\n", $buffer); // SPLIT INTO PARTS: HEADER/SETTINGS/PLAYERS/FOOTER
	array_pop($part); // REMOVE FOOTER WHICH IS EITHER NULL OR "\challenge\"
	$item = explode("\\", $part[1]); // SPLIT PART INTO ITEMS

	foreach( $item as $item_key => $data_key) 
	{
		if (!($item_key % 2)) { continue; } // SKIP EVEN KEYS
		
		$data_key = strtolower(parse_color($data_key, "1"));
		$server['e'][$data_key] = parse_color($item[$item_key+1], "1");
	}

	if (!empty($server['e']['hostname'])) { $server['s']['name'] = $server['e']['hostname']; }
	if (!empty($server['e']['sv_hostname'])) { $server['s']['name'] = $server['e']['sv_hostname']; }
	
	if (isset($server['e']['gamename'])) { $server['s']['game'] = $server['e']['gamename']; }
	if (isset($server['e']['mapname'])) { $server['s']['map']  = $server['e']['mapname']; }
	
	$server['s']['players'] = empty($part['2']) ? 0 : count($part) - 2;
	
	if (isset($server['e']['sv_maxclients'])) { $server['s']['playersmax'] = $server['e']['sv_maxclients']; }
	
	if (isset($server['e']['pswrd'])) { $server['s']['password'] = $server['e']['pswrd']; }
	if (isset($server['e']['g_needpass'])) { $server['s']['password'] = $server['e']['g_needpass']; }
	
	array_shift($part); // REMOVE HEADER
	array_shift($part); // REMOVE SETTING
	
	$pattern = "/(.*) (.*) \"(.*)\"/";
	$fields = array(1=>"score", 2=>"ping", 3=>"name");

	$i = 0;	
	foreach( $part as $player_key => $data )
	{
		if (!$data) { continue; }
	
		preg_match($pattern, $data, $match);
	
		foreach( $fields as $match_key => $field_name )
		{
			if (isset($match[$match_key])) { $server['p'][$player_key][$field_name] = trim($match[$match_key]); }
		}

		$server['p'][$player_key]['pid'] = $i;
		$server['p'][$player_key]['name'] = parse_color($server['p'][$player_key]['name'], "1");
	
		if( isset($server['p'][$player_key]['time']) )
		{
			$server['p'][$player_key]['time'] = player_time($server['p'][$player_key]['time']);
		}
		else
		{
			$server['p'][$player_key]['time'] = '-';
		}
		$i++;
	}
	
	return TRUE;
}

function server_query_05(&$server, &$query_need, &$query_fp)
{
	if ($server['b']['type'] == "halflifewon")
	{
		if ($query_need['s']) { $time_in = microtime_float(); fwrite($query_fp, "\xFF\xFF\xFF\xFFdetails\x00"); }
		elseif ($query_need['p']) { fwrite($query_fp, "\xFF\xFF\xFF\xFFplayers\x00"); }
		elseif ($query_need['e']) { fwrite($query_fp, "\xFF\xFF\xFF\xFFrules\x00");   }
	}
	else
	{
		$challenge_code = isset($query_need['challenge']) ? $query_need['challenge'] : "\x00\x00\x00\x00";
		
		if ($query_need['s']) { $time_in = microtime_float(); fwrite($query_fp, "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00"); }
		elseif ($query_need['p']) { fwrite($query_fp, "\xFF\xFF\xFF\xFF\x55{$challenge_code}");       }
		elseif ($query_need['e']) { fwrite($query_fp, "\xFF\xFF\xFF\xFF\x56{$challenge_code}");       }
	}

	$packet_temp  = array();
	$packet_type  = 0;
	$packet_count = 0;
	$packet_total = 4;

	while ( ($packet = fread($query_fp, 4096)) && ($packet_count < $packet_total) )
	{
		if ($query_need['s']) { if ($packet[4] == "D") { continue; } }
		elseif ($query_need['p']) { if ($packet[4] == "m" || $packet[4] == "I") { continue; } }
		elseif ($query_need['e']) { if ($packet[4] == "m" || $packet[4] == "I" || $packet[4] == "D") { continue; } }
		
		if (substr($packet, 0,  5) == "\xFF\xFF\xFF\xFF\x41") { $query_need['challenge'] = substr($packet, 5,  4); return TRUE; } // REPEAT WITH GIVEN CHALLENGE CODE
		elseif (substr($packet, 0,  4) == "\xFF\xFF\xFF\xFF") { $packet_total = 1;                     $packet_type = 1;       } // SINGLE PACKET - HL1 OR HL2
		elseif (substr($packet, 9,  4) == "\xFF\xFF\xFF\xFF") { $packet_total = ord($packet[8]) & 0xF; $packet_type = 2;       } // MULTI PACKET  - HL1 ( TOTAL IS LOWER NIBBLE OF BYTE )
		elseif (substr($packet, 12, 4) == "\xFF\xFF\xFF\xFF") { $packet_total = ord($packet[8]);       $packet_type = 3;       } // MULTI PACKET  - HL2
		elseif (substr($packet, 18, 2) == "BZ") { $packet_total = ord($packet[8]);       $packet_type = 4;       } // BZIP PACKET   - HL2
		
		$packet_count ++;
		$packet_temp[] = $packet;
	}

	if ($query_need['s']) { $server['b']['ping'] = (int)((microtime_float()-$time_in)*1000); }
	
	if ($packet_type == 0) { return !empty($server['s']) ? TRUE : FALSE; } // UNKNOWN RESPONSE

	$buffer = array();
	
	foreach ($packet_temp as $packet)
	{
		if ($packet_type == 1) { $packet_order = 0; }
		elseif ($packet_type == 2) { $packet_order = ord($packet[8]) >> 4; $packet = substr($packet, 9);  } // ( INDEX IS UPPER NIBBLE OF BYTE )
		elseif ($packet_type == 3) { $packet_order = ord($packet[9]);      $packet = substr($packet, 12); }
		elseif ($packet_type == 4) { $packet_order = ord($packet[9]);      $packet = substr($packet, 18); }
		
		$buffer[$packet_order] = $packet;
	}
	
	ksort($buffer);
	
	$buffer = implode("", $buffer);
	
	if ($packet_type == 4)
	{
		if (!function_exists("bzdecompress")) // REQUIRES http://php.net/bzip2
		{
			$server['e']['bzip2'] = "unavailable"; $query_need['e'] = FALSE;
			return TRUE;
		}
	
		$buffer = bzdecompress($buffer);
	}
	
	$header = cut_byte($buffer, 4);
	
	if ($header != "\xFF\xFF\xFF\xFF") { return FALSE; } // SOMETHING WENT WRONG
	
	$response_type = cut_byte($buffer, 1);
	
	if ($response_type == "I") // SOURCE INFO ( HALF-LIFE 2 )
	{
		$server['e']['netcode'] = ord(cut_byte($buffer, 1));
		$server['s']['name'] = cut_string($buffer);
		$server['s']['map'] = cut_string($buffer);
		$server['s']['game'] = cut_string($buffer);
		$server['e']['description'] = cut_string($buffer);
		$server['e']['appid'] = string_unpack(cut_byte($buffer, 2), "S");
		$server['s']['players'] = ord(cut_byte($buffer, 1));
		$server['s']['playersmax'] = ord(cut_byte($buffer, 1));
		$server['e']['bots'] = ord(cut_byte($buffer, 1));
		$server['e']['dedicated'] = cut_byte($buffer, 1);
		$server['e']['os'] = cut_byte($buffer, 1);
		$server['s']['password'] = ord(cut_byte($buffer, 1));
		$server['e']['anticheat'] = ord(cut_byte($buffer, 1));
		$server['e']['version'] = cut_string($buffer);
	}	
	elseif ($response_type == "m") // HALF-LIFE 1 INFO
	{
		$server_ip = cut_string($buffer);
		$server['s']['name'] = cut_string($buffer);
		$server['s']['map'] = cut_string($buffer);
		$server['s']['game'] = cut_string($buffer);
		$server['e']['description'] = cut_string($buffer);
		$server['s']['players'] = ord(cut_byte($buffer, 1));
		$server['s']['playersmax'] = ord(cut_byte($buffer, 1));
		$server['e']['netcode'] = ord(cut_byte($buffer, 1));
		$server['e']['dedicated'] = cut_byte($buffer, 1);
		$server['e']['os'] = cut_byte($buffer, 1);
		$server['s']['password'] = ord(cut_byte($buffer, 1));
	
		if (ord(cut_byte($buffer, 1))) // MOD FIELDS ( OFF FOR SOME HALFLIFEWON-VALVE SERVERS )
		{
			$server['e']['mod_url_info'] = cut_string($buffer);
			$server['e']['mod_url_download'] = cut_string($buffer);
			$buffer = substr($buffer, 1);
			$server['e']['mod_version'] = string_unpack(cut_byte($buffer, 4), "l");
			$server['e']['mod_size'] = string_unpack(cut_byte($buffer, 4), "l");
			$server['e']['mod_server_side'] = ord(cut_byte($buffer, 1));
			$server['e']['mod_custom_dll'] = ord(cut_byte($buffer, 1));
		}
	
		$server['e']['anticheat'] = ord(cut_byte($buffer, 1));
		$server['e']['bots'] = ord(cut_byte($buffer, 1));
	}	
	elseif ($response_type == "D") // SOURCE AND HALF-LIFE 1 PLAYERS
	{
		$returned = ord(cut_byte($buffer, 1));
		
		$player_key = 0;
		
		while ($buffer)
		{
			$server['p'][$player_key]['pid']   = ord(cut_byte($buffer, 1));
			$server['p'][$player_key]['name']  = cut_string($buffer);
			$server['p'][$player_key]['score'] = string_unpack(cut_byte($buffer, 4), "l");
			$server['p'][$player_key]['time']  = player_time(string_unpack(cut_byte($buffer, 4), "f"));
			
			$player_key ++;
		}
	}	
	elseif ($response_type == "E") // SOURCE AND HALF-LIFE 1 RULES
	{
		$returned = string_unpack(cut_byte($buffer, 2), "S");
	
		while ($buffer)
		{
			$item_key   = strtolower(cut_string($buffer));
			$item_value = cut_string($buffer);
			
			$server['e'][$item_key] = $item_value;
		}
	}
	
	// IF ONLY [s] WAS REQUESTED THEN REMOVE INCOMPLETE [e]
	if ($query_need['s'] && !$query_need['e']) { $server['e'] = array(); }
	
	if ($query_need['s']) { $query_need['s'] = FALSE; }
	elseif ($query_need['p']) { $query_need['p'] = FALSE; }
	elseif ($query_need['e']) { $query_need['e'] = FALSE; }
	
	return TRUE;
}

function writeData($socket, $command, $append = "") 
{ 
	$signal  = $command[0]; 
	$command = "\xFE\xFD" . $command . "\x01\x02\x03\x04" . $append; 
	$length  = strlen($command);
	
	if( $length !== fwrite($socket, $command, $length) ) 
		return false;
	
	$data = fread($socket, 1440); 
	
	if( strlen($data) < 5 || $data[0] != $signal ) 
		return false;
	
	return substr($data, 5); 
}

function server_query_99(&$server, &$query_need, &$query_fp) 
{ 
	if( !($data = writeData($query_fp, "\x09" )) )
		return FALSE;

	$challenge = pack('N', $data);
	
	$status = writeData($query_fp, "\x00", $challenge . "\x01\x02\x03\x04"); 
	$status = substr($status, 11); 
	$status = explode("\x00\x00\x01player_\x00\x00", $status); 
	$players = substr($status[1], 0, -2); 
	$players = explode("\x00", $players); 
	$status = explode("\x00", $status[0]); 
	$data = array("general" => $status, "plugins" => explode(",", $status[9]), "players" => $players); 
	
	$server['b']['status'] = 1; 
	$server['s']['map'] = $data['general'][11]; 
	
	$server['s']['game'] = $data['general'][3]; 
	$server['s']['name'] = $data['general'][1]; 
	$server['s']['map'] = $data['general'][11]; 
	$server['s']['players'] = $data['general'][13]; 
	$server['s']['playersmax'] = $data['general'][15]; 
	
	$server['e']['version'] = $data['general'][7]; 
	$server['e'] = $data['plugins']; 
	
	$players = array();
	if( ($cnt = count($data['players'])) > 0 )
	{
		for( $i=0; $i<$cnt; $i++ ) 
		{
			if( $data['players'][$i] )
			{
				$add = array("pid" => $i , "name" => $data['players'][$i], "score" => "-", "time" => "-"); 
				array_push($players, $add);
			}
		}
	}
	$server['p'] = $players; 
	return TRUE;
}

function server_query_12(&$server, &$query_need, &$query_fp)
{
	$challenge_packet = "SAMP\x21\x21\x21\x21\x00\x00";
	
	if ($query_need['s']) { $challenge_packet .= "i"; }
	elseif ($query_need['e']) { $challenge_packet .= "r"; }
	elseif ($query_need['p']) { $challenge_packet .= "d"; }
	
	fwrite($query_fp, $challenge_packet);
	
	$buffer = fread($query_fp, 4096);
	
	if (!$buffer) { return FALSE; }
	$buffer = substr($buffer, 10);
	
	$response_type = cut_byte($buffer, 1);
	
	if( $response_type == "i" )
	{
		$query_need['s'] = FALSE;
		
		$server['s']['password'] = ord(cut_byte($buffer, 1));
		$server['s']['players'] = string_unpack(cut_byte($buffer, 2), "S");
		$server['s']['playersmax'] = string_unpack(cut_byte($buffer, 2), "S");
		$server['s']['name'] = cut_pascal($buffer, 4);
		$server['e']['gamemode'] = cut_pascal($buffer, 4);
		$server['s']['map'] = cut_pascal($buffer, 4);
	}
	elseif( $response_type == "r" )
	{
		$query_need['e'] = FALSE;
		
		$item_total = string_unpack(cut_byte($buffer, 2), "S");
		
		for( $i=0; $i<$item_total; $i++ )
		{
			if (!$buffer) { return FALSE; }
			
			$data_key   = strtolower(cut_pascal($buffer));
			$data_value = cut_pascal($buffer);
			
			$server['e'][$data_key] = $data_value;
		}
	}	
	elseif( $response_type == "d" )
	{
		$query_need['p'] = FALSE;
		
		$player_total = string_unpack(cut_byte($buffer, 2), "S");
		
		for( $i=0; $i<$player_total; $i++ )
		{
			if( !$buffer ) { return FALSE; }
			
			$server['p'][$i]['pid'] = ord(cut_byte($buffer, 1));
			$server['p'][$i]['name'] = cut_pascal($buffer);
			$server['p'][$i]['score'] = string_unpack(cut_byte($buffer, 4), "S");
			$server['p'][$i]['ping'] = string_unpack(cut_byte($buffer, 4), "S");
			$server['p'][$i]['time'] = "-";
		}
	}
	
	return TRUE;
}

function cut_pascal(&$buffer, $start_byte = 1, $length_adjust = 0, $end_byte = 0)
{
	$length = ord(substr($buffer, 0, $start_byte)) + $length_adjust;
	$string = substr($buffer, $start_byte, $length);
	$buffer = substr($buffer, $start_byte + $length + $end_byte);
	
	return $string;
}

function player_time($seconds)
{
    if ($seconds === "") { return ""; }

    $n = $seconds < 0 ? "-" : "";

    $seconds = abs($seconds);

    $h = intval($seconds/3600);
    $m = intval($seconds/60)%60;
    $s = intval($seconds)%60;

    $h = str_pad($h, "2", "0", STR_PAD_LEFT);
    $m = str_pad($m, "2", "0", STR_PAD_LEFT);
    $s = str_pad($s, "2", "0", STR_PAD_LEFT);

    return "{$n}{$h}:{$m}:{$s}";
}

function string_unpack($string, $format)
{
    list(,$string) = @unpack($format, $string);

    return $string;
}

function cut_byte(&$buffer, $length)
{
	$ret = substr($buffer, 0, $length);
	$buffer = substr($buffer, $length);

	return $ret;
}

function cut_string(&$buffer, $start_byte = 0, $end_marker = "\x00")
{
	$buffer = substr($buffer, $start_byte);
	$length = strpos($buffer, $end_marker);

	if ($length === FALSE) { $length = strlen($buffer); }

	$ret = substr($buffer, 0, $length);
	$buffer = substr($buffer, $length + strlen($end_marker));

	return $ret;
}

function create_cache($prefix, $cache_text, $root_path = "acpanel/")
{
	$filename = $root_path.'templates/_cache/' . $prefix . '.tmp';

	@file_put_contents($filename, $cache_text, LOCK_EX);
	@chmod($filename, 0666);
}

function get_cache($prefix, $cache_time, $root_path = "acpanel/")
{
	$filename = $root_path.'templates/_cache/' . $prefix . '.tmp';
	clearstatcache();
	if( is_file($filename) )
	{
		if( @filemtime($filename) + $cache_time > time() )
		{
			$fp = fopen($filename, 'r');
			flock($fp, LOCK_SH);
			$data = stream_get_contents($fp);
			flock($fp, LOCK_UN);
			fclose($fp);
	
			return $data;
		}
	}

	return false;
}

function getMonitoringCache($offset, $limit, $sorting, $filter = array())
{
	global $config, $db;

	$servers = array();
	$current_time = time();
	$arguments = array('offset'=>$offset,'limit'=>$limit);
	$where = " WHERE a.active = 1";
	if( !empty($filter) )
	{
		foreach($filter as $k => $v)
		{
			if( ($k == "status" && $v == 1) || $k != "status" )
			{
				$arguments[$k] = $v;
				$where .= " AND a.".$k." = '{".$k."}'";
			}
		}
	}

	$result = $db->Query("SELECT a.id, a.gametype, a.address, a.hostname, a.description, a.rating, a.position, a.vip, a.votes_up, a.votes_down, a.status, a.cache_time, a.opt_mode AS mode_id, a.opt_city AS city_id, 
		a.cache_country AS country, a.cache_map_path AS map_path, a.cache_map AS map, a.cache_players AS players, a.cache_playersmax AS playersmax, b.name AS mode, c.name AS city
		FROM `acp_servers` a 
		LEFT JOIN `acp_servers_modes` b ON b.id = a.opt_mode 
		LEFT JOIN `acp_servers_cities` c ON c.id = a.opt_city".$where." ORDER BY a.".$sorting." LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$obj->favorite = false;
			$servers[$obj->id] = (array)$obj;
		}
	}

	return $servers;
}

function create_monitoring_list($offset, $limit, $sorting, $total = 0, $recurse = true, $root_path = "acpanel/", $filter = array())
{
	global $config, $db, $SxGeo;

	$minus = 0;
	$servers = array();
	$current_time = time();
	$arguments = array('offset'=>$offset,'limit'=>$limit);
	$where = " WHERE a.active = 1";
	if( !empty($filter) )
	{
		foreach($filter as $k => $v)
		{
			$arguments[$k] = $v;
			$where .= " AND a.".$k." = '{".$k."}'";
		}
	}

	$result = $db->Query("SELECT a.id, a.userid, a.address, a.hostname, a.description, a.rating, a.position, a.vip, a.gametype, b.name AS mode, c.name AS city, a.opt_mode AS mode_id, a.opt_city AS city_id 
		FROM `acp_servers` a 
		LEFT JOIN `acp_servers_modes` b ON b.id = a.opt_mode 
		LEFT JOIN `acp_servers_cities` c ON c.id = a.opt_city".$where." 
		ORDER BY a.".$sorting." LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach ($result as $obj)
		{
			$lists = explode(":", $obj->address);
			$ip = $lists[0];
			$port = $lists[1];

			$live = server_query_live($obj->gametype, $ip, $port, "s");

			if( $live['b']['status'] && isset($live['s']['name']) )
			{
				$status = true;
				$hostname = (strlen($obj->hostname) > 0) ? $obj->hostname : $live['s']['name'];
				$map = $live['s']['map'];
				$players = $live['s']['players'];
				$playersmax = $live['s']['playersmax'];
			}
			else
			{
				if( $config['mon_hide_offline'] == 1 )
				{
					$minus++;
					continue;
				}

				$status = false;
				$hostname = (strlen($obj->hostname) > 0) ? $obj->hostname : "";
				$map = "";
				$players = $playersmax = 0;
			}

			clearstatcache();

			if(file_exists($root_path."images/maps/".$obj->gametype."/".$map.".jpg"))
			{
				$map_path = 1;
			}
			else
			{
				$map_path = 0;
			}

			$server_country_code = strtolower($SxGeo->getCountry($ip));
			clearstatcache();

			if(file_exists($root_path."images/flags/".$server_country_code.".gif"))
			{
				$server_country = $server_country_code.".gif";
			}
			else
			{
				$server_country = "err.gif";
			}

			$obj->vip = ($obj->vip > $current_time) ? 1 : 0;

			$output_array = array(
				'id' => $obj->id,
				'map' => $map,
				'map_path' => $map_path,
				'players' => $players,
				'playersmax' => $playersmax,
				'address' => $obj->address,
				'hostname' => $hostname,
				'description' => $obj->description,
				'rating' => $obj->rating,
				'position' => $obj->position,
				'status' => $status,
				'country' => $server_country,
				'gametype' => $obj->gametype,
				'vip' => $obj->vip,
				'mode' => $obj->mode,
				'city' => $obj->city,
				'mode_id' => $obj->mode_id,
				'city_id' => $obj->city_id,
				'favorite' => false
			);

			$servers[$obj->id] = $output_array;
		}
	}

	$servers['total'] = $total - $minus;

	if( $minus && $recurse && $total > $limit )
	{
		$offset = $offset + $limit + 1;
		$limit = $minus;
		$servers = array_merge($servers, create_monitoring_list($offset, $limit, $sorting, $servers['total'], $recurse, $root_path));
	}

	return $servers;
}

function favoritesGet()
{
	global $config, $userinfo;

	$output = array();

	if ( isset($_COOKIE[$config['mon_servers_favorites']]) )
	{
		$cookie = (string) $_COOKIE[$config['mon_servers_favorites']];

		if ( !preg_match('~^(?:\d+\.)*+\d+$~D', $cookie) )
		{
			// Delete invalid cookie
			favoritesDelete();
		}
		else
		{
			$output = explode('.', $cookie);

			if( count($output) > $userinfo['mon_favorites_limit'] )
			{
				// Delete invalid cookie
				favoritesDelete();

				$output = array();
			}
		}
	}

	return $output;
}

function favoritesFind($id)
{
	$cookie = favoritesGet();

	if( !empty($cookie) )
	{
		if( in_array($id, $cookie) )
			return TRUE;
	}

	return FALSE;
}

function favoritesAdd($id)
{
	global $config, $userinfo;

	// Don't add double ids
	if( favoritesFind($id) )
		return TRUE;

	// Add the id to the cookie string.
	// The trim is needed for when adding the first id.
	$temp = favoritesGet();

	if( count($temp) >= $userinfo['mon_favorites_limit'] )
		return FALSE;

	$temp[] = $id;
	$cookie = implode('.', $temp);

	// A cookie lifetime of 0 will keep the cookie until the session ends
	$expire = time() + (3600 * 24 * 365);

	// Should return TRUE; does not necessarily mean the user accepted the cookie, though
	return setcookie($config['mon_servers_favorites'], $cookie, $expire, "/");
}

function favoritesDeleteID($id)
{
	global $config;

	$temp = favoritesGet();
	if( ($key = array_search($id, $temp)) !== FALSE )
		unset($temp[$key]);

	$cookie = implode('.', $temp);

	// A cookie lifetime of 0 will keep the cookie until the session ends
	$expire = time() + (3600 * 24 * 365);

	// Should return TRUE; does not necessarily mean the user accepted the cookie, though
	return setcookie($config['mon_servers_favorites'], $cookie, $expire, "/");
}

function favoritesDelete()
{
	global $config;

	unset($_COOKIE[$config['mon_servers_favorites']]);

	return setcookie($config['mon_servers_favorites'], FALSE, time() - 86400, "/");
}

function favoritesGetServers($favorites = array(), $sorting, $root_path = "acpanel/")
{
	global $config, $db, $SxGeo;

	$servers = array();
	$current_time = time();
	$arguments = array('fav' => $favorites);
	$select = "a.id, a.gametype, a.address, a.hostname, a.description, a.rating, a.position, a.vip, a.votes_up, a.votes_down, b.name AS mode, c.name AS city, a.opt_mode AS mode_id, a.opt_city AS city_id";
	if( $config['mon_cache'] )
		$select .= ", a.status, a.cache_time, a.cache_country AS country, a.cache_map_path AS map_path, a.cache_map AS map, a.cache_players AS players, a.cache_playersmax AS playersmax";

	$result = $db->Query("SELECT ".$select." FROM `acp_servers` a 
		LEFT JOIN `acp_servers_modes` b ON b.id = a.opt_mode 
		LEFT JOIN `acp_servers_cities` c ON c.id = a.opt_city WHERE a.id IN ('{fav}') ORDER BY a.".$sorting."", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach ($result as $obj)
		{
			if( $config['mon_cache'] )
			{
				$obj->favorite = true;
				$servers[$obj->id] = (array)$obj;		
			}
			else
			{
				$lists = explode(":", $obj->address);
				$ip = $lists[0];
				$port = $lists[1];
	
				$live = server_query_live($obj->gametype, $ip, $port, "s");
	
				if( $live['b']['status'] && isset($live['s']['name']) )
				{
					$status = 1;
					$hostname = (strlen($obj->hostname) > 0) ? $obj->hostname : $live['s']['name'];
					$map = $live['s']['map'];
					$players = $live['s']['players'];
					$playersmax = $live['s']['playersmax'];
				}
				else
				{
					$status = 0;
					$hostname = (strlen($obj->hostname) > 0) ? $obj->hostname : "";
					$map = "";
					$players = $playersmax = 0;
				}
	
				clearstatcache();
	
				if(file_exists($root_path."images/maps/".$obj->gametype."/".$map.".jpg"))
				{
					$map_path = 1;
				}
				else
				{
					$map_path = 0;
				}
	
				$server_country_code = strtolower($SxGeo->getCountry($ip));
				clearstatcache();
	
				if(file_exists($root_path."images/flags/".$server_country_code.".gif"))
				{
					$server_country = $server_country_code.".gif";
				}
				else
				{
					$server_country = "err.gif";
				}
	
				$obj->vip = ($obj->vip > $current_time) ? 1 : 0;
	
				$output_array = array(
					'id' => $obj->id,
					'map' => $map,
					'map_path' => $map_path,
					'players' => $players,
					'playersmax' => $playersmax,
					'address' => $obj->address,
					'hostname' => $hostname,
					'description' => $obj->description,
					'rating' => $obj->rating,
					'position' => $obj->position,
					'status' => $status,
					'country' => $server_country,
					'gametype' => $obj->gametype,
					'vip' => $obj->vip,
					'mode' => $obj->mode,
					'city' => $obj->city,
					'mode_id' => $obj->mode_id,
					'city_id' => $obj->city_id,
					'favorite' => true
				);
	
				$servers[$obj->id] = $output_array;
			}
		}
	}

	return $servers;
}

?>
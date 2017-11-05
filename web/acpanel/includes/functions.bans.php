<?php

if (!defined('IN_ACP')) die("Hacking attempt!");

function net_match($network, $ip)
{
	// determines if a network in the form of 192.168.17.1/16 or
	// 127.0.0.1/255.255.255.255 or 10.0.0.1 matches a given ip

	$network = trim($network);
	$ip = trim($ip);
	$d = strpos($network,"-");

	if( $d === false )
	{
		$ip_arr = explode('/', $network);
		
		if( !preg_match("@\d*\.\d*\.\d*\.\d*@",$ip_arr[0],$matches) )
		{
			$ip_arr[0] .= ".0";    // Alternate form õ.õ.õ/24
		}

	        $network_long = ip2long($ip_arr[0]);
	        $x = ip2long($ip_arr[1]);

	        $mask = long2ip($x) == $ip_arr[1] ? $x : (0xffffffff << (32 - $ip_arr[1]));
	        $ip_long = ip2long($ip);

	        return ($ip_long & $mask) == ($network_long & $mask);
	}
	else
	{
		$from = ip2long(trim(substr($network,0,$d)));
	        $to = ip2long(trim(substr($network,$d+1)));

	        $ip = ip2long($ip);

	        return ($ip >= $from AND $ip <= $to);
	}
}

?>
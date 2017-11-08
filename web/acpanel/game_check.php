<?php

define("IN_ACP", true);
define('ROOT_PATH', './');
define('INCLUDE_PATH', ROOT_PATH . 'includes/');

unset($config); // for security
require(INCLUDE_PATH . '_cfg.php');
include(INCLUDE_PATH . 'functions.main.php');

$pl_cookie = (isset($_COOKIE["IDMemory"])) ? $_COOKIE["IDMemory"] : "";
$pl_ip = getRealIpAddr();

if( $pl_cookie )
{
	if(mysql_connect($config['hostname'],$config['username'],$config['password']))
	{
		if(mysql_select_db($config['dbname']))
		{
			$now = time();

			$resource = mysql_query("SELECT bid FROM `acp_bans` WHERE player_ip = '".mysql_escape_string($pl_cookie)."' AND ($now < (ban_created+(ban_length*60)) OR ban_length = '0') ORDER BY bid DESC LIMIT 1");
		
			if($resource)
			{
				$result = mysql_fetch_row($resource);

				if($result)
				{
					mysql_query("UPDATE `acp_bans` SET cookie_ip = '".$pl_ip."' WHERE bid = '".$result[0]."'");
				}
				else
				{
					setcookie("IDMemory", $pl_ip, time()+157680000);
				}
			}
		}
	}
}
else
{
	setcookie("IDMemory", $pl_ip, time()+157680000);
}

?>

<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>Cstrike MOTD</title>
<style type="text/css">
       body {
            background: #000;
            margin: 8px;
            color: #FFB000;
            font: normal 16px/20px Verdana, Tahoma, sans-serif;
        }

        a {
            color: #FFF;
            text-decoration: underline;
        }

        a:hover {
            color: #EEE;
            text-decoration: none;
        }
</style>
</head>
<body>
You are playing Counter-Strike v1.6<br>
Visit the official CS web site @<br>
www.counter-strike.net<br>
<a href="http://www.counter-strike.net">Visit Counter-Strike.net</a>
</body>
</html>

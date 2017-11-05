<?php

define("IN_ACP", true);
define('ROOT_PATH', './');
define('INCLUDE_PATH', ROOT_PATH . 'includes/');

unset($config); // for security
require(INCLUDE_PATH . '_cfg.php');
include(INCLUDE_PATH . 'functions.main.php');

print '
	<html>
	<head>
		<title>HISTORY BAN&#039;S!</title>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<link href="templates/default/css/style_forgame.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
';

$search=$_GET["player"];

if( isset($search) )
{
	mysql_connect($config['hostname'],$config['username'],$config['password']) or die("Невозможно подключиться к базе данных");
	mysql_select_db($config['dbname']) or die("Невозможно выбрать базу данных");

	if( preg_match("/^STEAM\_0\:[0-1]\:\d+$/", $search) == 1 )
	{
		$resource = mysql_query("SELECT bid, player_nick, admin_nick, ban_length, ban_created, player_id, ban_reason FROM `acp_bans_history` WHERE player_id = '".mysql_escape_string($search)."' AND ban_type = 'S' ORDER BY ban_created DESC") or die(mysql_error());
	}
	else if( preg_match("/^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/", $search) == 1 )
	{
		$resource = mysql_query("SELECT bid, player_nick, admin_nick, ban_length, ban_created, player_id, ban_reason FROM `acp_bans_history` WHERE player_id = '".mysql_escape_string($search)."' ORDER BY ban_created DESC") or die(mysql_error());
	}
	else
	{
		$resource = mysql_query("SELECT bid, player_nick, admin_nick, ban_length, ban_created, player_id, ban_reason FROM `acp_bans_history` WHERE player_nick = '".mysql_escape_string($search)."' AND ban_type = 'SI' ORDER BY ban_created DESC") or die(mysql_error());
	}

	echo "
	<table width='100%' cellpadding='3' cellspacing='0' border='0'><tr><td class='h'>&nbsp;© История банов</td></tr></table>
		<table width='100%' cellpadding='5' cellspacing='10' align='center'>
			<tr>
				<td class='q'>
					<table width='100%' border='0' cellpadding='10' cellspacing='0'>
						<tr class='c'>
							<td height='10' width='10%'><b>Дата</b></td>
				            <td height='10' width='20%'><b>Игрок</b></td>
							<td height='10' width='20%'><b>Steam ID</b></td>
							<td height='10' width='10%'><b>Время</b></td>
							<td height='10' width='20%'><b>Причина</b></td>
							<td height='10' width='20%'><b>Администратор</b></td>
						</tr>
	";

	if(mysql_num_rows($resource) == 0)
	{
		echo "
			<tr>
				<td align='center' colspan='6'>- история банов отсутствует -</td>
			</tr>
		";
	}
	else
	{		
		while($result = mysql_fetch_assoc($resource))
		{
			$date = date( 'd-m-y - H:i', $result['ban_created'] );
			$player = htmlspecialchars($result['player_nick'],ENT_QUOTES);
			$player_id = htmlspecialchars($result['player_id'],ENT_QUOTES);
			if($result['ban_length'] == '0')
			{
				$duration = "permanent";
			}
			else
			{
				if ($result['ban_length'] >= 1440)
				{
					$result['ban_length'] = round($result['ban_length'] / 1440);
					if ($result['ban_length'] == 1)
						$duration = $result['ban_length']." день";
					else
						$duration = $result['ban_length']." дней";
				}
				else
				{
					$duration = $result['ban_length']." минут";
				}
			}
			$reason = htmlspecialchars($result['ban_reason'],ENT_QUOTES);
			$admin = htmlspecialchars($result['admin_nick'],ENT_QUOTES);	
				
			echo "
				<tr class='ci'>
					<td height='16' width='10%'>$date</td>
		            <td height='16' width='20%'>$player</td>
					<td height='16' width='20%'>$player_id</td>
					<td height='16' width='10%'>$duration</td>
					<td height='16' width='20%'>$reason</td>
					<td height='16' width='20%'>$admin</td>
				</tr>
			";
		}
	}

	echo "
		</table></td></tr></table>
	";
}
else
{
	print "HISTORY BAN ERROR";
}

print '
	</body>
	</html>
';

?>
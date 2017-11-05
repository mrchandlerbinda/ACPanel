<?php

$bans = false;
$cache_need_create = false;
$cache_prefix = 'blockbans_'.$obj->blockid;

include_once(INCLUDE_PATH . 'functions.servers.php');
include_once(INCLUDE_PATH . 'functions.bans.php');

if( $config['gb_cache_block_stats'] > 0 )
{
	$bans = get_cache($cache_prefix, $config['gb_cache_block_stats']);
	$cache_need_create = ($bans !== false) ? false : true;
}

if( $bans === false )
{
	$bans[0] = $bans[1] = $bans[2] = $bans[3] = $bans[4] = $bans[5] = $bans[6] = 0;

	$bans[0] = $db->Query("SELECT count(*) FROM `acp_bans_subnets` WHERE approved = 1", array(), $config['sql_debug']);

	if( ($bans[1] = $db->Query("SELECT count(*) FROM (
			(SELECT bid FROM `acp_bans_history`)
			UNION ALL
			(SELECT bid FROM `acp_bans`)
		) temp", array(), $config['sql_debug'])) > 0 )
	{
		$bans[5] = $db->Query("SELECT count(*) FROM (
				(SELECT bid FROM `acp_bans_history` WHERE ban_length = 0)
				UNION ALL
				(SELECT bid FROM `acp_bans` WHERE ban_length = 0)
			) temp", array(), $config['sql_debug']);

		$bans[6] = $bans[1] - $bans[5];

		$result_types = $db->Query("SELECT count(*) AS count, ban_type FROM (
				(SELECT bid, ban_type FROM `acp_bans_history`)
				UNION ALL
				(SELECT bid, ban_type FROM `acp_bans`)
			) temp GROUP BY ban_type", array(), $config['sql_debug']);

		if( is_array($result_types) )
		{
			foreach( $result_types as $obj )
			{
				switch($obj->ban_type)
				{
					case "SI":
						$bans[2] = $obj->count;
						break;

					case "S":
						$bans[3] = $obj->count;
						break;

					case "N":
						$bans[4] = $obj->count;
						break;
				}
			}
		}
	}

	if( $cache_need_create )
		create_cache($cache_prefix, serialize($bans));
}
else
{
	$bans = unserialize($bans);
}

$smarty->assign("bs", $bans);

?>
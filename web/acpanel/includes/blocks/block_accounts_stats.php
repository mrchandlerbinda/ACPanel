<?php

// 0 - all accounts
// 1 - active
// 2 - by nick
// 3 - by ip
// 4 - by steam

$accounts = false;
$cache_need_create = false;
$cache_prefix = 'blockaccounts_'.$obj->blockid;

include_once(INCLUDE_PATH . 'functions.servers.php');

if( $config['ga_cache_block_accounts'] > 0 )
{
	$accounts = get_cache($cache_prefix, $config['ga_cache_block_accounts']*60);
	$cache_need_create = ($accounts !== false) ? false : true;
}

if( $accounts === false )
{
	$accounts[0] = $accounts[1] = $accounts[2] = $accounts[3] = $accounts[4] = 0;

	if( ($accounts[0] = $db->Query("SELECT count(*) FROM `acp_players`", array(), $config['sql_debug'])) > 0 )
	{
		$accounts[2] = $db->Query("SELECT count(*) FROM `acp_players` WHERE flag = 1", array(), $config['sql_debug']);
		$accounts[3] = $db->Query("SELECT count(*) FROM `acp_players` WHERE flag = 2", array(), $config['sql_debug']);
		$accounts[4] = $accounts[0] - $accounts[2] - $accounts[3];

		$active = time() - ($config['ga_active_time']*60*60*24);
		$arguments = array('active' => $active);
		$accounts[1] = $db->Query("SELECT count(*) FROM `acp_players` WHERE last_time > {active}", $arguments, $config['sql_debug']);
	}

	if( $cache_need_create )
		create_cache($cache_prefix, serialize($accounts));
}
else
{
	$accounts = unserialize($accounts);
}

$smarty->assign("as", $accounts);

?>
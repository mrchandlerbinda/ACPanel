<?php

	$result_servers = $db->Query("SELECT id, hostname FROM `acp_servers` WHERE opt_accounts = 1", array(), $config['sql_debug']);
	
	if( is_array($result_servers) )
	{
		foreach ($result_servers as $obj)
		{
			$array_servers[$obj->id] = $obj->hostname;
		}
	}

	header('Content-type: text/html; charset='.$config['charset']);

	$mask_id = trim($_GET['s']);
	if( !is_numeric($mask_id) ) die("Hacking Attempt");

	$arguments = array('mask_id'=>$mask_id);
	$result = $db->Query("SELECT * FROM `acp_access_mask` WHERE mask_id = '{mask_id}'", $arguments, $config['sql_debug']);
	if( is_array($result) )
	{
		foreach ($result as $obj)
		{
			$array_mask = (array)$obj;
		}
	}

	$access_servers = array();
	$result_sync = $db->Query("SELECT mask_id, server_id FROM `acp_access_mask_servers` WHERE mask_id = '{mask_id}'", $arguments, $config['sql_debug']);
	if( is_array($result_sync) )
	{
		foreach ($result_sync as $obj_sync)
		{
			$access_servers[] = $obj_sync->server_id;
		}
	}

	$smarty->assign("mask_edit",$array_mask);
	if(isset($array_servers)) $smarty->assign("array_servers",$array_servers);
	$smarty->assign("access_servers",$access_servers);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamecp_mask_edit.tpl');

	exit;

?>
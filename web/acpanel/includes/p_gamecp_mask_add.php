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

	if(isset($array_servers)) $smarty->assign("array_servers",$array_servers);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamecp_mask_add.tpl');

	exit;

?>
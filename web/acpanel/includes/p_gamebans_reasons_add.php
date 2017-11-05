<?php

	$result_servers = $db->Query("SELECT address, hostname FROM `acp_servers` WHERE active = 1", array(), $config['sql_debug']);
	
	if( is_array($result_servers) )
	{
		foreach ($result_servers as $obj)
		{
			$array_servers[$obj->address] = $obj->hostname;
		}
	}

	header('Content-type: text/html; charset='.$config['charset']);

	if(isset($array_servers)) $smarty->assign("array_servers",$array_servers);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamebans_reasons_add.tpl');

	exit;

?>
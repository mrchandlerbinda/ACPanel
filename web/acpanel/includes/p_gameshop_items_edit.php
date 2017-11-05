<?php

	$id = trim($_GET['id']);
	if( !is_numeric($id) ) die("Hacking Attempt");

	$arguments = array('id'=>$id);
	$result = $db->Query("SELECT a.id, a.game_descr, a.web_descr, a.cost, a.duration, a.cmd, a.active FROM `acp_gameshop` a
		WHERE a.id = '{id}' LIMIT 1", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$result_srv = $db->Query("SELECT * FROM `acp_gameshop_servers` WHERE item_id = '{id}'", $arguments, $config['sql_debug']);
			if( is_array($result_srv) )
			{
				foreach ($result_srv as $objsrv)
				{
					$arrSRV[] = $objsrv->server_id;
				}
			}

			$obj->servers = (isset($arrSRV)) ? $arrSRV : array();
			$edit = (array)$obj;
		}
	}

	$result_servers = $db->Query("SELECT id, hostname FROM `acp_servers` WHERE id > 0", array(), $config['sql_debug']);
	
	if( is_array($result_servers) )
	{
		foreach ($result_servers as $obj)
		{
			$array_servers[$obj->id] = $obj->hostname;
		}
	}

	header('Content-type: text/html; charset='.$config['charset']);

	if(isset($edit)) $smarty->assign("item",$edit);
	if(isset($array_servers)) $smarty->assign("array_servers",$array_servers);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gameshop_items_edit.tpl');

	exit;

?>
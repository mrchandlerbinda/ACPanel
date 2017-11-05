<?php

if( isset($_GET['id']) && is_numeric($_GET['id']) )
{
	$id = $_GET['id'];
	
	$result = $db->Query("SELECT a.id, a.web_descr, b.server_id, c.hostname, c.address FROM `acp_gameshop` a
		LEFT JOIN `acp_gameshop_servers` b ON b.item_id = a.id 
		LEFT JOIN `acp_servers` c ON c.id = b.server_id 
		WHERE a.id = ".$id, array(), $config['sql_debug']);

	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$servers['name'] = $obj->web_descr;
			if( $obj->server_id == 0 )
			{
				$servers['servers'][] = "@@item_all_servers@@";
			}
			elseif( !is_null($obj->server_id) )
			{
				$obj->hostname = ($obj->hostname) ? $obj->hostname : (($obj->address) ? $obj->address : "@@server_not_found@@");
				$servers['servers'][] = $obj->hostname;
			}
		}
	}
	else
	{	
		$error = "@@servers_not_found@@";
	}
}
else
{
	$error = "@@item_not_found@@";
}

if(isset($error)) $smarty->assign("iserror", $error);
if(isset($servers)) $smarty->assign("servers", $servers);

header('Content-type: text/html; charset='.$config['charset']);

$smarty->registerFilter("output","translate_template");
$smarty->display('p_gameshop_items_servers.tpl');

exit;

?>
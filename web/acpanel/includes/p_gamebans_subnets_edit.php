<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$id = trim($_GET['id']);
	if( !is_numeric($id) ) die("Hacking Attempt");

	$arguments = array('id'=>$id);
	$result = $db->Query("SELECT * FROM `acp_bans_subnets` WHERE id = '{id}' LIMIT 1", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$array_subnet = (array)$obj;
		}
	}

	$smarty->assign("subnet_edit",$array_subnet);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamebans_subnets_edit.tpl');

	exit;

?>
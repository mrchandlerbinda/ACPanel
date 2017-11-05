<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$blockid = trim($_GET['id']);
	if( !is_numeric($blockid) ) die("Hacking Attempt");

	$arguments = array('block'=>$blockid);
	$result = $db->Query("SELECT * FROM `acp_blocks` WHERE blockid = '{block}'", $arguments, $config['sql_debug']);
	if( is_array($result) )
	{
		foreach ($result as $obj)
		{			$block_info = (array)$obj;
		}
	}

	$array_product = array();
	$result = $db->Query("SELECT productid, title FROM `acp_products` WHERE productid IS NOT NULL ORDER BY productid", array(), $config['sql_debug']);
	
	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$array_product[$obj->productid] = $obj->title;
		}
	}

	$smarty->assign("array_product", $array_product);
	$smarty->assign("blockedit", $block_info);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_general_blocks_edit.tpl');

	exit;

?>
<?php

	header('Content-type: text/html; charset='.$config['charset']);

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
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_general_blocks_add.tpl');

	exit;

?>
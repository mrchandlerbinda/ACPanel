<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$lp_id = trim($_GET['s']);
	if( !is_numeric($lp_id) ) die("Hacking Attempt");

	$arguments = array('lp_id'=>$lp_id);
	$result = $db->Query("SELECT * FROM `acp_lang_pages` WHERE lp_id = '{lp_id}'", $arguments, $config['sql_debug']);
	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$array_lang = (array)$obj;
		}
	}

	$smarty->assign("lang_edit",$array_lang);

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
	$smarty->display('p_general_phrases_template_edit.tpl');

	exit;

?>
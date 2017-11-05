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

	$result_lang = $db->Query("SELECT * FROM `acp_lang` WHERE lang_id IS NOT NULL", array(), $config['sql_debug']);

	if( is_array($result_lang) )
	{
		foreach ($result_lang as $obj)
		{
			$array_lang[] = (array)$obj;
		}
	}

	$result_tpl = $db->Query("SELECT * FROM `acp_lang_pages` WHERE lp_id IS NOT NULL", array(), $config['sql_debug']);

	if( is_array($result_tpl) )
	{
		foreach ($result_tpl as $obj)
		{
			$array_tpl[] = (array)$obj;
		}
	}

	$smarty->assign("array_tpl", $array_tpl);
	$smarty->assign("array_lang", $array_lang);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_general_phrase_add.tpl');

	exit;

?>
<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$result_node = $db->Query("SELECT * FROM `acp_category` WHERE catlevel = 0 ORDER BY display_order", array(), $config['sql_debug']);

	if (is_array($result_node))
	{
		foreach ($result_node as $obj)
		{
			$arguments = array('lang'=>get_language(1),'catleft'=>$obj->catleft,'catright'=>$obj->catright,'category'=>$obj->categoryid);
			$result_cat = $db->Query("SELECT a.title, a.categoryid, a.catlevel, b.lw_page, b.{lang} AS translate FROM `acp_category` AS a LEFT JOIN `acp_lang_words` AS b ON REPLACE(a.title,'@@','') = b.lw_word AND b.lw_page = 45 WHERE a.catleft >= {catleft} AND a.catright <= {catright} AND a.sectionid = '{category}' OR a.categoryid = '{category}' ORDER BY a.catleft", $arguments, $config['sql_debug']);

			if (is_array($result_cat))
			{
				foreach ($result_cat as $cat)
				{
					$array_cats[] = (array)$cat;
				}
			}
		}
	}

	$arguments = array('lang'=>get_language(1));
	$result_blocks = $db->Query("SELECT a.title, a.blockid, b.lw_page, b.{lang} AS translate FROM `acp_blocks` AS a LEFT JOIN `acp_lang_words` AS b ON REPLACE(a.title,'@@','') = b.lw_word AND b.lw_page = 47", $arguments, $config['sql_debug']);

	if( is_array($result_blocks) )
	{
		foreach ($result_blocks as $obj)
		{
			$array_blocks[] = (array)$obj;
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

	$smarty->assign("array_cats", $array_cats);
	$smarty->assign("array_blocks", $array_blocks);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_general_categories_add.tpl');

	exit;

?>
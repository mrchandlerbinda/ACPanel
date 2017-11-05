<?php

if( !isset($_GET['id']) || !is_numeric($_GET['id']) )
{
	$error = "@@id_incorrect@@";
}
else
{
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

	$arguments = array('id'=>$_GET['id']);	$result_phrase = $db->Query("SELECT * FROM `acp_lang_words` WHERE lw_id = {id}", $arguments, $config['sql_debug']);

	if( is_array($result_phrase) )
	{
		foreach ($result_phrase as $obj)
		{
			$array_phrase = (array)$obj;
		}
	}

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

	$smarty->assign("phrase", $array_phrase);
	$smarty->assign("array_lang", $array_lang);
	$smarty->assign("array_tpl", $array_tpl);

	unset($cat_phrases);
	foreach ($all_categories as $key => $value)
	{
		$search = array_search("p_general_phrases", $value);
		if ($search)
		{			$cat_phrases = $key;
			$smarty->assign("cat_phrases", $key);
			break;
		}
	}

	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".(($cat_phrases) ? $cat_phrases : '').((isset($_GET['s'])) ? '&s='.$_GET['s'] : '').((isset($_GET['s'])) ? '&t='.$_GET['t'] : '');
	$smarty->assign("action_uri", $action_uri);
}

if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("head_title","@@head_general_phrase_edit@@");

?>
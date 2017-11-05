<?php

if( !isset($_GET['tpl']) )
{
	$smarty->assign("action",$_SERVER['PHP_SELF']);

	$result = $db->Query("SELECT lp_id, lp_name FROM `acp_lang_pages`", array(), $config['sql_debug']);

	if( is_array($result) )
	{
		foreach ($result as $obj)
		{
			$tpls[] = (array)$obj;
		}
	}

	if( isset($tpls) ) $smarty->assign("tpls",$tpls);

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
}
else
{
	$go_page = "p_general_phrase_search_result";

	$tpl = $_GET['tpl'];
	$word = ( isset($_GET['word']) ) ? trim($_GET['word']) : "";
	$product = ( isset($_GET['productid']) ) ? trim($_GET['productid']) : "";
	$code = ( isset($_GET['code']) ) ? trim($_GET['code']) : "";

	$sqlconds = '';
	$postout = '';

	$postout .= "&tpl=".$tpl."&product=".$product."&code=".$code;
	$total_phrases = 0;

	if( $word )
	{
		$postout .= "&word=".$word;

		$result = $db->Query("SELECT lang_code FROM `acp_lang`", array(), $config['sql_debug']);
		if( is_array($result) )
		{
			foreach( $result as $obj )
			{
				$arrLang[] = $obj->lang_code;
			}
		}
		else
		{
			$arrLang[] = $result;
		}

		if( isset($arrLang) )
		{
			foreach( $arrLang as $k => $v )
			{
				$sqlconds = "AND acp_lang_words.".$v." LIKE '%{word}%'";
				if( $product ) $sqlconds .= " AND acp_lang_words.productid = '{product}'";
				if( $code ) $sqlconds .= " AND acp_lang_words.lw_word LIKE '%{code}%'";
				$arguments = array('id'=>$tpl, 'word'=>$word, 'product'=>$product, 'code'=>$code);
				if( $tpl == "-1" )
					$total_phrases += $db->Query("SELECT count(*) FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0',acp_lang_words.lw_page) WHERE 1 = 1 ".$sqlconds." ORDER BY lp_name DESC", $arguments, $config['sql_debug']);
				else
					$total_phrases += $db->Query("SELECT count(*) FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0','{id}') WHERE acp_lang_pages.lp_id = acp_lang_words.lw_page OR acp_lang_words.lw_page = '{id}' ".$sqlconds." ORDER BY lp_name DESC", $arguments, $config['sql_debug']);
			}
		}
	}
	else
	{
		if( $product ) $sqlconds = " AND acp_lang_words.productid = '{product}'";
		if( $code ) $sqlconds .= " AND acp_lang_words.lw_word LIKE '%{code}%'";
		$arguments = array('id'=>$tpl, 'product'=>$product, 'code'=>$code);
		if( $tpl == "-1" )
			$total_phrases = $db->Query("SELECT count(*) FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0',acp_lang_words.lw_page) WHERE 1 = 1".$sqlconds." ORDER BY lp_name DESC", $arguments, $config['sql_debug']);
		else
			$total_phrases = $db->Query("SELECT count(*) FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0','{id}') WHERE (acp_lang_pages.lp_id = acp_lang_words.lw_page OR acp_lang_words.lw_page = '{id}')".$sqlconds." ORDER BY lp_name DESC", $arguments, $config['sql_debug']);
	}

	$cat_edit_id = false;
	foreach( $all_categories as $key => $value )
	{
		$search_edit_id = array_search("p_general_phrase_edit", $value);
		if( $search_edit_id )
		{
			$cat_edit_id = $key;
			break;
		}
	}

	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>

			function pageselectCallback(page_id, total, jq) {
				var pg_size = ".$config['pagesize'].";
				var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
				var cat_current = ".$current_section_id.";
				var cat_edit_id = ".$cat_edit_id.";

				if(total < second)
				{
					second = total;
				}

				if(!total)
				{
					jQuery('#Searchresult').html('@@showing@@ 0 @@to@@ 0 @@of@@ <span>0</span>');
				}
				else
				{
					jQuery('#Searchresult').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
				}

				jQuery.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_general_phrases',
					data:'go=6&cat_current=' + cat_current + '&edit_id=' + cat_edit_id + '&offset=' + first + '&limit=' + pg_size + '".$postout."',
					success:function(result) {
						jQuery('#ajaxContent').html(result);
					}
				});

				return false;
			}

			jQuery(document).ready(function($) {
				$('#Pagination').pagination( ".$total_phrases.", {
					num_edge_entries: 2,
					num_display_entries: 8,
					callback: pageselectCallback,
					items_per_page: ".$config['pagesize']."
				});
			});
		</script>
	";

	if(!$total_phrases) {
		$error = '@@search_empty@@';
	}
}

if(isset($error)) $smarty->assign("iserror",$error);

?>
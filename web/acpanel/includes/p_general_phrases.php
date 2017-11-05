<?php

$result = $db->Query("SELECT * FROM `acp_lang` WHERE lang_id IS NOT NULL", array(), $config['sql_debug']);

if( is_array($result) )
{
	foreach ($result as $obj)
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

if( !isset($_GET['s']) || !in_multi_array($_GET['s'], $array_lang) )
{
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&s=lw_en');
	exit;
}

$s = $_GET['s'];

if( !isset($_GET['t']) || !is_numeric($_GET['t']) || (!in_multi_array($_GET['t'], $array_tpl) &&  ($_GET['t'] != '0')) )
{	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&s='.$s.'&t=0');
	exit;
}

$t = $_GET['t'];

$search_addphrase = $cat_edit_id = false;
foreach ($all_categories as $key => $value)
{
	$search_addphrase_id = array_search("p_general_phrase_add", $value);
	$search_edit_id = array_search("p_general_phrase_edit", $value);
	if ($search_addphrase_id)
	{
		$smarty->assign("cat_addphrase_id", $key);
		$search_addphrase = true;
	}
	elseif ($search_edit_id)
	{
		$cat_edit_id = $key;
	}

	if ($search_addphrase && $cat_edit_id)
	{
		break;
	}
}

$arguments = array('lp_id'=>$t);
$where = (!$t) ? " OR acp_lang_words.lw_page = '0'" : "";
$total_items = $db->Query("
	SELECT count(*) FROM `acp_lang_words` 
	LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id = IF(acp_lang_words.lw_page = '0','0','{lp_id}') 
	WHERE acp_lang_pages.lp_id = acp_lang_words.lw_page".$where, $arguments, $config['sql_debug']);

if(!$total_items) {
	$error = "@@empty_table@@";
}

$smarty->assign("array_lang", $array_lang);
$smarty->assign("array_tpl", $array_tpl);

$headinclude = "
	<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
	<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
	<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$('a[rel*=facebox]').facebox()
			});
		})(jQuery);

		function pageselectCallback(page_id, total, jq) {
			jQuery('#ajaxContent').html(
				jQuery('<div>')
				.addClass('center-img-block')
				.append(
					jQuery('<img>')
					.attr('src','acpanel/images/ajax-big-loader.gif')
					.attr('alt','@@refreshing@@')
				)
			); 

			var pg_size = ".$config['pagesize'].";
			var lw_code = '".$s."';
			var lp_id = '".$t."';
			var cat_current = ".$current_section_id.";
			var cat_edit_id = ".$cat_edit_id.";

			var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;

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
				data:'go=1&cat_current=' + cat_current + '&edit_id=' + cat_edit_id + '&code=' + lw_code + '&lp_id=' + lp_id + '&offset=' + first + '&limit=' + pg_size,
				success:function(result) {
					jQuery('#ajaxContent').html(result);
				}
			});

			return false;
		}

		function rePagination(diff) {
			var total = parseInt(jQuery('#Searchresult span').text()) + diff;

			if(total == 0)
			{
				jQuery('.tablesorter').append(jQuery('<tfoot>')
					.append(jQuery('<tr>').addClass('emptydata')
						.append(jQuery('<td>').attr('colspan', '4').html('@@empty_data@@'))
					)
				);
			}

			var pg_size = ".$config['pagesize'].";
			var set_page = parseInt(jQuery('.pagination span.active').not('.prev, .next').text()) - 1;
			var count_row = jQuery('.tablesorter tbody tr').length + diff;

			if(count_row <= 0 && diff < 0 && total && set_page)
			{
				set_page = set_page - 1;
			}

			jQuery('#Pagination').pagination( total, {
				num_edge_entries: 2,
				num_display_entries: 8,
				callback: pageselectCallback,
				items_per_page: pg_size,
				current_page: set_page
			});
		}

		jQuery(document).ready(function($) {			$('#forma-select select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&s=' + $('option:selected', this).val() + '&t=".$_GET['t']."';
			});

			$('#forma-tpl select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&s=".$_GET['s']."&t=' + $('option:selected', this).val();
			});

			$('#Pagination').pagination( ".$total_items.", {
				num_edge_entries: 2,
				num_display_entries: 8,
				callback: pageselectCallback,
				items_per_page: ".$config['pagesize']."
			});
		});
	</script>
";

$smarty->assign("get_in",$s);
$smarty->assign("get_tpl",$t);
if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("head_title","@@head_general_phrases@@");

?>
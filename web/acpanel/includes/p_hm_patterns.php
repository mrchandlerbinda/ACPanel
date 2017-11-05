<?php

if(!isset($_GET['id']))
{
	$total_items = $db->Query("SELECT count(*) FROM `acp_hud_manager` WHERE hud_id > 0 ORDER BY priority DESC", array(), $config['sql_debug']);
	
	if( !$total_items )
	{
		$error = "@@empty_table@@";
	}

	foreach( $all_categories as $key => $value )
	{
		$search = array_search("p_hm_patterns_add", $value);
		if( $search )
		{
			$smarty->assign("cat_addptrn_id", $key);
			break;
		}
	}
	
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
				var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
				var cat_current = ".$current_section_id.";
				var cat_edit_id = ".$cat_current['id'].";
	
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
					url:'acpanel/ajax.php?do=ajax_hm_patterns',
					data:'go=1&cat_edit=' + cat_edit_id + '&cat_current=' + cat_current + '&offset=' + first + '&limit=' + pg_size,
					success:function(result) {
						jQuery('#ajaxContent').html(result);
					}
				});
	
				return false;
			}
	
			function rePagination(diff) {
				var total = parseInt(jQuery('#Searchresult span').text()) + diff;
				var column = jQuery('.tablesorter thead th').length + 1;
	
				if(total == 0)
				{
					jQuery('.tablesorter').append(jQuery('<tfoot>')
						.append($('<tr>').addClass('emptydata')
							.append($('<td>').attr('colspan', column).html('@@empty_data@@'))
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
	
			jQuery(document).ready(function($) {
				$('#Pagination').pagination( ".$total_items.", {
					num_edge_entries: 2,
					num_display_entries: 8,
					callback: pageselectCallback,
					items_per_page: ".$config['pagesize']."
				});
			});
		</script>
	";
	
	if(isset($error)) $smarty->assign("iserror", $error);
}
else
{
	header('Content-type: text/html; charset='.$config['charset']);

	$hud_id = trim($_GET['id']);
	if( !is_numeric($hud_id) ) die("Hacking Attempt");

	function parseByteNumber($num, $diff = 0)
	{
		$result = array();
	
		if( $num )
		{
			if( in_array($num, array(1,2,4,8,16,32,64,128)) )
			{
				array_push($result, $num);
				if( $diff )
				{
					$result = array_values(array_merge($result, parseByteNumber($diff)));
				}
			}
			else
			{
				$result = array_values(array_merge($result, parseByteNumber($num-1, $diff+1)));
			}
		}
	
		return $result;
	}

	$arguments = array('hud_id'=>$hud_id);
	$result = $db->Query("SELECT * FROM `acp_hud_manager` WHERE hud_id = '{hud_id}'", $arguments, $config['sql_debug']);
	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$obj->flags = parseByteNumber($obj->flags);
			$array_hud = (array)$obj;
		}
	}

	$smarty->assign("hud_edit", $array_hud);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_hm_patterns_edit.tpl');

	exit;
}

?>
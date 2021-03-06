<?php

$total_items = $db->Query("SELECT count(*) FROM `acp_access_mask`", array(), $config['sql_debug']);

if(!$total_items)
{
	$error = "@@empty_table@@";
}

$search_addmask = $search_editmask = $search_search = false;
foreach( $all_categories as $key => $value )
{
	$search_addmask_id = array_search("p_gamecp_mask_add", $value);
	$search_editmask_id = array_search("p_gamecp_mask_edit", $value);
	$search_search_id = array_search("p_gamecp_search", $value);
	if( $search_addmask_id )
	{
		$search_addmask = $key;
		$smarty->assign("cat_addmask_id", $search_addmask);
	}
	elseif( $search_editmask_id )
	{
		$search_editmask = $key;
		$smarty->assign("cat_editmask_id", $search_editmask);
	}
	elseif( $search_search_id )
	{
		$search_search = $key;
		$smarty->assign("cat_search", $search_search);
	}

	if( $search_addmask && $search_editmask && $search_search )
	{
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
				$('.block_head a[rel*=facebox]').facebox()
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
			var cat_current = ".$current_section_id.";
			var cat_edit_id = ".$search_editmask.";
			var cat_search = ".$search_search.";

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
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:'go=12&cat_search=' + cat_search + '&cat_edit=' + cat_edit_id + '&cat_current=' + cat_current + '&offset=' + first + '&limit=' + pg_size,
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

if(isset($error)) $smarty->assign("iserror",$error);

?>
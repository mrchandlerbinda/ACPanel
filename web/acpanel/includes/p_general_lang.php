<?php

$total_items = $db->Query("SELECT count(*) FROM `acp_lang`", array(), $config['sql_debug']);

if(!$total_items) {
	$error = "@@empty_table@@";
}

$search_addlang = $cat_phrases_id = $cat_edit_id = false;
foreach ($all_categories as $key => $value)
{	$search_addlang_id = array_search("p_general_lang_add", $value);
	$search_phrases_id = array_search("p_general_phrases", $value);
	$search_edit_id = array_search("p_general_lang_edit", $value);
	if ($search_addlang_id)
	{
		$smarty->assign("cat_addlang_id", $key);
		$search_addlang = true;
	}
	elseif ($search_phrases_id)
	{
		$cat_phrases_id = $key;
	}
	elseif ($search_edit_id)
	{
		$cat_edit_id = $key;
	}

	if ($search_addlang && $cat_phrases_id && $cat_edit_id)
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
			var pg_size = ".$config['pagesize'].";
			var cat_phrases_id = ".$cat_phrases_id.";
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
				url:'acpanel/ajax.php?do=ajax_general_lang',
				data:'go=1&cat_current=' + cat_current + '&phrases_id=' + cat_phrases_id + '&edit_id=' + cat_edit_id + '&offset=' + first + '&limit=' + pg_size,
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
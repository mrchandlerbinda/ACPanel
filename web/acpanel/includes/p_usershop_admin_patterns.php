<?php

$array_groups = array();
$result = $db->Query("SELECT * FROM `acp_payment_groups` WHERE gid IS NOT NULL", array(), $config['sql_debug']);

if( is_array($result) )
{
	foreach( $result as $obj )
	{
		$array_groups[$obj->gid] = $obj->name;
	}
}

if( !isset($_GET['t']) || !is_numeric($_GET['t']) || (!in_array($_GET['t'], array_keys($array_groups)) && $_GET['t'] != '0') )
{
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&t=0');
	exit;
}

$t = $_GET['t'];

$search_addcat = $search_editcat = false;
foreach( $all_categories as $key => $value )
{
	$search_addcat_id = array_search("p_usershop_admin_patterns_add", $value);
	$search_editcat_id = array_search("p_usershop_admin_patterns_edit", $value);
	if( $search_addcat_id )
	{
		$search_addcat = $key;
		$smarty->assign("cat_addcat_id", $key);
	}
	elseif( $search_editcat_id )
	{
		$search_editcat = $key;
		$smarty->assign("cat_editcat_id", $search_editcat);
	}

	if( $search_addcat && $search_editcat )
	{
		break;
	}
}

$sqlconds = " WHERE 1 = 1";

if( $t != 0 )
{
	$sqlconds .= " AND b.gid = ".$t;
}

if( isset($array_groups) ) $smarty->assign("array_groups", $array_groups);

$total_items = $db->Query("SELECT count(a.id) FROM `acp_payment_patterns` a
	LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
	".$sqlconds, array(), $config['sql_debug']);

if( !$total_items )
{
	if( is_null($total_items) ) $total_items = 0;
	$error = "@@empty_table@@";
}

$headinclude = "
	<link href='acpanel/templates/".$config['template']."/css/usershop.css' rel='stylesheet' type='text/css' />
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
			var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
			var cat_current = ".$current_section_id.";
			var cat_edit = ".$search_editcat.";
			var grupa = '".$t."';

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
				url:'acpanel/ajax.php?do=ajax_payment',
				data:'go=12&grupa=' + grupa + '&cat_edit=' + cat_edit + '&cat_current=' + cat_current + '&offset=' + first + '&limit=' + pg_size,
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
						.append(jQuery('<td>').attr('colspan', '6').html('@@empty_data@@'))
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
			$('#forma-select select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&t=' + $('option:selected', this).val();
			});

			$('#Pagination').pagination( ".$total_items.", {
				num_edge_entries: 2,
				num_display_entries: 8,
				callback: pageselectCallback,
				items_per_page: ".$config['gb_view_per_page']."
			});
		});
	</script>
";

$smarty->assign("get_group", $t);
if(isset($error)) $smarty->assign("iserror", $error);

?>
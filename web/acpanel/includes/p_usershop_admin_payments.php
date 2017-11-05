<?php

if( !isset($_GET['s']) || !is_numeric($_GET['s']) || !in_array($_GET['s'], array('-2','-1','0')) ) {
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&s=-2');
	exit;
}

$s = $_GET['s'];
$sqlconds = " WHERE 1 = 1";
$arguments = array();

if( $s != -2 )
{
	$sqlconds .= " AND pattern = ".$s;
}

$total_items = $db->Query("SELECT count(*) FROM `acp_payment`".$sqlconds, $arguments, $config['sql_debug']);

if( !$total_items )
{
	$error = "@@empty_table@@";
}

$headinclude = "
	<link href='acpanel/templates/".$config['template']."/css/usershop.css' rel='stylesheet' type='text/css' />
	<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
	<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
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

			var cat_current = ".$current_section_id.";
			var pg_size = ".$config['pagesize'].";
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
				url:'acpanel/ajax.php?do=ajax_payment',
				data:'go=10&username=".$userinfo['username']."&cat_current=' + cat_current + '&offset=' + first + '&limit=' + pg_size + '&s=".$s."',
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
					.append(jQuery('<tr>').addClass('emptydata')
						.append(jQuery('<td>').attr('colspan', column).html('@@empty_data@@'))
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

			$('#forma-select select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&s=' + $('option:selected', this).val();
			});
		});
	</script>
";

$smarty->assign("get_pattern",$s);
if(isset($error)) $smarty->assign("iserror",$error);

?>
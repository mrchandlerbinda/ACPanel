<?php

$array_servers = array();
$result_servers = $db->Query("SELECT id, hostname, address FROM `acp_servers` WHERE active = 1", array(), $config['sql_debug']);
if( is_array($result_servers) )
{
	foreach( $result_servers as $obj )
	{
		$array_servers[$obj->id] = array($obj->address, $obj->hostname);
	}
}

if( !isset($_GET['t']) || !is_numeric($_GET['t']) || (!in_array($_GET['t'], array_keys($array_servers)) && $_GET['t'] != '0') )
{
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&t=0');
	exit;
}

$t = $_GET['t'];

if( !isset($_GET['s']) || !is_numeric($_GET['s']) || !in_array($_GET['s'], array('0','1','2')) ) {
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&t='.$t.'&s=0');
	exit;
}

$s = $_GET['s'];

$search_addcat = $search_editcat = $search_srvcat = false;
foreach( $all_categories as $key => $value )
{
	$search_addcat_id = array_search("p_gameshop_items_add", $value);
	$search_editcat_id = array_search("p_gameshop_items_edit", $value);
	$search_srvcat_id = array_search("p_gameshop_items_servers", $value);
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
	elseif( $search_srvcat_id )
	{
		$search_srvcat = $key;
		$smarty->assign("cat_srvcat_id", $search_srvcat);
	}

	if( $search_addcat && $search_editcat && $search_srvcat )
	{
		break;
	}
}

$sqlconds = " WHERE 1 = 1";
$arguments = array();

if( $s )
{
	$active = ($s == 1) ? 1 : 0;
	$sqlconds .= " AND a.active = ".$active;
}

if( $t )
{
	$sqlconds .= " AND b.server_id = ".$t;
}

if( isset($array_servers) ) $smarty->assign("array_servers", $array_servers);

$total_items = $db->Query("SELECT SQL_CALC_FOUND_ROWS a.id FROM `acp_gameshop` a
	LEFT JOIN `acp_gameshop_servers` b ON b.item_id = a.id
	".$sqlconds." GROUP BY a.id", array(), $config['sql_debug']);

if( !$total_items )
{
	if( is_null($total_items) ) $total_items = 0;
	$error = "@@empty_table@@";
}
else $total_items = $db->Query("SELECT FOUND_ROWS()", array(), $config['sql_debug']);

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
			var cat_srv = ".$search_srvcat.";
			var srv = '".$t."';
			var status = '".$s."';

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
				data:'go=24&srv=' + srv + '&status=' + status + '&cat_edit=' + cat_edit + '&cat_srv=' + cat_srv + '&cat_current=' + cat_current + '&offset=' + first + '&limit=' + pg_size,
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
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&t=' + $('option:selected', this).val() + '&s=' + $('#forma-tpl select').val();
			});

			$('#forma-tpl select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&t=' + $('#forma-select select').val() + '&s=' + $('option:selected', this).val();
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

$smarty->assign("get_srv", $t);
$smarty->assign("get_status", $s);
if(isset($error)) $smarty->assign("iserror", $error);

?>
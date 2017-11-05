<?php

// 0 - top stats
// 1 - servers stats
// 2 - reasons stats
// 3 - length stats
// 4 - subnets stats
// 5 - country stats
// 6 - admins stats

if( !isset($_GET['t']) || !is_numeric($_GET['t']) || !in_array($_GET['t'], array(0,1,2,3,4,5,6)) )
{
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&t=0');
	exit;
}

if( $t = $_GET['t'] )
{
	switch($t)
	{
		case 1:
			$result = $db->Query("SELECT  count(*) AS count, server_name FROM (
				(SELECT server_name FROM `acp_bans`) UNION ALL
				(SELECT server_name FROM `acp_bans_history`)) temptable GROUP BY server_name", array(), $config['sql_debug']);

			$total_items = count($result);

			break;

		case 2:
			$result = $db->Query("SELECT  count(*) AS count, ban_reason FROM (
				(SELECT ban_reason FROM `acp_bans`) UNION ALL
				(SELECT ban_reason FROM `acp_bans_history`)) temptable GROUP BY ban_reason", array(), $config['sql_debug']);

			$total_items = count($result);

			break;

		case 3:
			$result = $db->Query("SELECT  count(*) AS count, ban_length FROM (
				(SELECT ban_length FROM `acp_bans`) UNION ALL
				(SELECT ban_length FROM `acp_bans_history`)) temptable GROUP BY ban_length", array(), $config['sql_debug']);

			$total_items = count($result);

			break;

		case 4:
			$result = $db->Query("SELECT  count(*) AS count, REPLACE(player_ip,SUBSTRING_INDEX(player_ip,'.',-2),'0.0') AS value FROM (
				(SELECT player_ip FROM `acp_bans`) UNION ALL
				(SELECT player_ip FROM `acp_bans_history`)) temptable GROUP BY value", array(), $config['sql_debug']);

			$total_items = count($result);

			break;

		case 5:
			$result = $db->Query("SELECT  count(*) AS count, REPLACE(player_ip,SUBSTRING_INDEX(player_ip,'.',-2),'0.0') AS value FROM (
				(SELECT player_ip FROM `acp_bans`) UNION ALL
				(SELECT player_ip FROM `acp_bans_history`)) temptable GROUP BY value", array(), $config['sql_debug']);

			if( is_array($result) )
			{
				include(INCLUDE_PATH . 'class.SypexGeo.php');
				$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
				$array_country = array();

				foreach( $result as $obj )
				{
					$code = $SxGeo->getCountry($obj->value);
					if( !isset($array_country[$code]) )
						$array_country[$code] = $obj->count;
				}
			}
			$total_items = count($array_country);

			break;

		case 6:
			$result = $db->Query("SELECT  count(*) AS count, admin_uid FROM (
				(SELECT admin_uid FROM `acp_bans` WHERE admin_uid != 0) UNION ALL
				(SELECT admin_uid FROM `acp_bans_history` WHERE admin_uid != 0)) temptable GROUP BY admin_uid", array(), $config['sql_debug']);

			$total_items = count($result);

			break;
	}

	if( !$total_items )
	{
		$error = '@@empty_table@@';
	}
	
	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>
	
			function pageselectCallback(page_id, total, jq) {
				var pg_size = ".$config['gb_view_per_page'].";
				var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
				var stats = '".$t."';
	
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
					url:'acpanel/ajax.php?do=ajax_gamebans',
					data:'go=21&stats=' + stats + '&offset=' + first + '&limit=' + pg_size,
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
				$('#Pagination').pagination( ".$total_items.", {
					num_edge_entries: 2,
					num_display_entries: 8,
					callback: pageselectCallback,
					items_per_page: ".$config['gb_view_per_page']."
				});
			});
		</script>
	";
}
else
{
	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>
			jQuery(document).ready(function($) {
				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_gamebans',
					data:{go: 22,catid: ".$current_section_id.",doid: ".$_GET['do']."},
					success:function(result) {
						$('#ajaxContent').html(result);
					}
				});
			});
		</script>
	";
}

$headinclude .= "
	<script type='text/javascript'>
		jQuery(document).ready(function($) {
			$('#forma-select select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&t=' + $('option:selected', this).val();
			});
		});
	</script>
";

$smarty->assign("get_stats", $t);
if(isset($error)) $smarty->assign("iserror", $error);

?>
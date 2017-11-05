<?php

$array_servers = array();

if( $config['cc_servers'] )
{
	$servers = explode(",",$config['cc_servers']);
	$arguments = array('serverip'=>$servers);
	$result_servers = $db->Query("SELECT id, address FROM `acp_servers` WHERE address IN ('{serverip}')", $arguments, $config['sql_debug']);
	
	if( is_array($result_servers) )
	{
		foreach ($result_servers as $obj)
		{
			$array_servers[$obj->id] = $obj->address;
		}
	}
}

if( !isset($_GET['t']) || !is_numeric($_GET['t']) || (!in_array($_GET['t'], array_keys($array_servers)) &&  ($_GET['t'] != '0')) )
{
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&t=0');
	exit;
}

$t = $_GET['t'];
$sqlconds = ' WHERE 1=1';

if ($config['cc_cmd'])
{	$cc_cmd = explode(',',$config['cc_cmd']);	$sqlconds .= " AND cmd IN ('{cmd}')";

	$time_delay = $config['cc_delay']*60;
	if (is_numeric($config['cc_delay'])) { $sqlconds .= " AND timestamp <= '{time}'"; }
	if (!$config['cc_alive']) { $sqlconds .= " AND alive = '0'"; }
	if (!$config['cc_foradmins']) { $sqlconds .= " AND foradmins = '0'"; }
	if (!$config['cc_block_msg']) { $sqlconds .= " AND pattern IN ('0','-1')"; }
	if (!empty($array_servers))
	{
		if ($t == 0)
		{ 
			$sqlconds .= " AND serverip IN ('{serverip}')";
		}
		else
		{
			$servers = $array_servers[$t];
			$sqlconds .= " AND serverip = '{serverip}'";
		}

		if ($config['cc_limit'] > 0) { $sqlconds .= " LIMIT {limit}"; }

		$arguments = array('serverip'=>$servers,'time'=>time()-$time_delay,'limit'=>$config['cc_limit'],'cmd'=>$cc_cmd);
		$result_total = $db->Query("SELECT id FROM `acp_chat_logs`".$sqlconds, $arguments, $config['sql_debug']);
		$total_items = count($result_total);

		if(!$total_items) {
			$error = "@@empty_table@@";
		}
	}
	else
	{		$total_items = 0;
		$error = "@@chat_disabled@@";
	}
}
else
{
	$total_items = 0;	$error = "@@chat_disabled@@";
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
			startUpdateTimer(2);

			var pg_size = ".$config['pagesize'].";
			var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
			var delay = new Date().getTime() - ".$time_delay.";
			var srv = '".$t."';

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
				url:'acpanel/ajax.php?do=ajax_cc_logs',
				data:'go=3&srv=' + srv + '&delay=' + delay + '&offset=' + first + '&limit=' + pg_size,
				success:function(result) {
					startUpdateTimer(1);
					jQuery('#ajaxContent').html(result);
				}
			});

			return false;
		}

		function rePagination(diff) {
			jQuery('#ajaxContent').html(
				jQuery('<div>')
				.addClass('center-img-block')
				.append(
					jQuery('<img>')
					.attr('src','acpanel/images/ajax-big-loader.gif')
					.attr('alt','@@refreshing@@')
				)
			);

			var total = parseInt(jQuery('#Searchresult span').text()) + diff;

			if(total == 0)
			{
				jQuery('.tablesorter').append(jQuery('<tfoot>')
					.append(jQuery('<tr>').addClass('emptydata')
						.append(jQuery('<td>').attr('colspan', '3').html('@@empty_data@@'))
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

		var intervalID;

		function startUpdateTimer(v) {
			if(v == 0)
			{
				window.clearInterval(intervalID);
				jQuery('.block_head a').text('@@refreshing@@').unbind('click').click(function() {return false;});

				rePagination(0);
			}
			else if(v == 1)
			{
				jQuery('.block_head a').unbind('click').click(function() {
					startUpdateTimer(0);
					return false;
				});
				var timetogo = ".$config['cc_refresh'].";
				if(timetogo)
				{
					intervalID = window.setInterval(function() {
						jQuery('.block_head a').text(timetogo + ' @@to_refresh@@');
						if (timetogo <= 0)
						{
							window.clearInterval(intervalID);
							jQuery('.block_head a').text('@@refreshing@@').unbind('click').click(function() {return false;});
							rePagination(0);
						}
						timetogo--;
					}, 1000);
				}
				else
				{
					jQuery('.block_head a').text('@@refresh@@');
				}
			}
			else
			{
				window.clearInterval(intervalID);
				jQuery('.block_head a').text('@@refreshing@@').unbind('click').click(function() {return false;});
			}
		}

		jQuery(document).ready(function($) {
			$('#forma-select select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&t=' + $('option:selected', this).val();
			});

			$('#Pagination').pagination( ".$total_items.", {
				num_edge_entries: 2,
				num_display_entries: 8,
				callback: pageselectCallback,
				items_per_page: ".$config['pagesize']."
			});

			$('.block_head a').click(function() {
				startUpdateTimer(0);
				return false;
			});
		});
	</script>
";

$smarty->assign("get_server",$t);
if(isset($error)) $smarty->assign("iserror",$error);
if(isset($array_servers)) $smarty->assign("array_servers",$array_servers);

?>
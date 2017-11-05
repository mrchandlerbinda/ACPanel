<?php

if (!isset($_GET['server']))
{
	$smarty->assign("action",$_SERVER['PHP_SELF']);

	$sub = $db->Query("SELECT b.id, b.hostname, b.address FROM `acp_nick_logs` a 
		LEFT JOIN `acp_servers` b ON b.address = a.serverip GROUP BY b.address", array(), $config['sql_debug']);

	if( FALSE === $sub )
	{
		$error = '@@tbl_srv_error@@';
	}
	else
	{
		if( is_null($sub) )
		{
			$error = '@@tbl_srv_empty@@';
		}
		else
		{
			foreach( $sub as $obj )
			{
				if( is_null($obj->id) )
					continue;

				$servers[] = (array)$obj;
			}

			if( isset($servers) )
			{
				$smarty->assign("servers",$servers);
	
				$headinclude = "
					<link href='acpanel/templates/".$config['template']."/css/date_input.css' rel='stylesheet' type='text/css' />
					<script type='text/javascript' src='acpanel/scripts/js/jquery.date_input.js'></script>
					<script type='text/javascript'>
						(function ($) {
							$(function () {
								// Date picker
								$('input.date_picker').date_input();
							});
						})(jQuery);
	
						jQuery(document).ready(function($) {
							$('input:button').click(function() {
								var data = $('#forma-search').serialize();
	
								$.ajax({
									type:'POST',
									url:'acpanel/ajax.php?do=ajax_nc_logs',
									data:data + '&go=2',
									success:function(result) {
										if( result.indexOf('id=\"success\"') + 1)
										{
											$('.accessMessage').html('');
											humanMsg.displayMsg(result,'success');
											$('#forma-search').get(0).reset();
											$('#forma-search .cmf-skinned-select').each(function() {
												$('.cmf-skinned-text',this).text($('option:selected',this).text());
											});
										}
										else
										{
											$('.accessMessage').html('');
											humanMsg.displayMsg(result,'error');
										}
									}
								});
	
								return false;
							});
						});
					</script>
				";
			}
			else
			{
				$error = '@@tbl_srv_empty@@';
			}
		}
	}
}
else
{
	$go_page = "p_nc_logs_result";

	$srv = trim($_GET['server']);
	$reason = $_GET['reason'];
	$point = $_GET['point'];
	$startdate = trim($_GET['begindate']);
	$enddate = trim($_GET['enddate']);
	$pl_nick = trim($_GET['player_nick']);
	$pl_id = trim($_GET['player_id']);
	$pl_ip = trim($_GET['player_ip']);

	$sqlconds = 'WHERE 1=1 ';
	$postout = '';

	if ($srv != "all") { $postout .= "&srv=".$srv; $sqlconds .= " AND serverip = '{serverip}' "; }
	if ($reason != "all") { $postout .= "&reason=".$reason; $sqlconds .= " AND pattern = '{pattern}' "; }
	if ($point != "all") { $postout .= "&point=".$point; $sqlconds .= " AND action = '{action}' "; }
	if ($pl_nick) { $postout .= "&nick=".$pl_nick; $sqlconds .= " AND name LIKE '%{name}%' "; }
	if ($pl_id) { $postout .= "&steam=".$pl_id; $sqlconds .= " AND authid LIKE '%{authid}%' "; }
	if ($pl_ip) { $postout .= "&ip=".$pl_ip; $sqlconds .= " AND ip LIKE '%{ip}%' "; }
	if ($startdate) { $postout .= "&startdate=".$startdate; $sqlconds .= " AND timestamp >= '{startdate}' "; }
	if ($enddate) { $postout .= "&enddate=".$enddate; $sqlconds .= " AND timestamp <= '{enddate}' "; }

	date_default_timezone_set('UTC');
	$arguments = array('serverip'=>$srv,'pattern'=>$reason,'action'=>$point,'name'=>$pl_nick,'authid'=>$pl_id,'ip'=>$pl_ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true));
	$total_items = $db->Query("SELECT count(*) FROM `acp_nick_logs` ".$sqlconds, $arguments, $config['sql_debug']);

	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>

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
					url:'acpanel/ajax.php?do=ajax_nc_logs',
					data:'go=1&offset=' + first + '&limit=' + pg_size + '".$postout."',
					success:function(result) {
						jQuery('#ajaxContent').html(result);
					}
				});

				return false;
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

	if(!$total_items) {
		$error = '@@search_empty@@';
	}
}

if(isset($error)) $smarty->assign("iserror",$error);

?>
<?php

if (!isset($_GET['server_ip']))
{
	$smarty->assign("action",$_SERVER['PHP_SELF']);

	$sub = $db->Query("SELECT b.id, b.hostname, b.address FROM `acp_vbk_logs` a 
		LEFT JOIN `acp_servers` b ON b.address = a.server_ip GROUP BY b.address", array(), $config['sql_debug']);

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
		{			foreach( $sub as $obj )
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
									url:'acpanel/ajax.php?do=ajax_vbk_logs',
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
	$go_page = "p_vbk_logs_result";

	$server_ip = (isset($_GET['server_ip'])) ? $_GET['server_ip'] : '';
	$vote_type = (isset($_GET['vote_type'])) ? $_GET['vote_type'] : '';
	$vote_result = (isset($_GET['vote_result'])) ? $_GET['vote_result'] : '';
	$startdate = (isset($_GET['startdate'])) ? $_GET['startdate'] : '';
	$enddate = (isset($_GET['enddate'])) ? $_GET['enddate'] : '';
	$vote_player_nick = (isset($_GET['vote_player_nick'])) ? trim($_GET['vote_player_nick']) : '';
	$vote_player_id = (isset($_GET['vote_player_id'])) ? trim($_GET['vote_player_id']) : '';
	$vote_player_ip = (isset($_GET['vote_player_ip'])) ? trim($_GET['vote_player_ip']) : '';
	$nom_player_nick = (isset($_GET['nom_player_nick'])) ? trim($_GET['nom_player_nick']) : '';
	$nom_player_id = (isset($_GET['nom_player_id'])) ? trim($_GET['nom_player_id']) : '';
	$nom_player_ip = (isset($_GET['nom_player_ip'])) ? trim($_GET['nom_player_ip']) : '';

	$sqlconds = 'WHERE 1=1 ';
	$postout = '';

	if ($server_ip != "all") { $postout .= "&server_ip=".$server_ip; $sqlconds .= " AND server_ip = '{server_ip}' "; }
	if ($vote_type != "all") { $postout .= "&vote_type=".$vote_type; $sqlconds .= " AND vote_type = '{vote_type}' "; }
	if ($vote_result != "all") { $postout .= "&vote_result=".$vote_result; $sqlconds .= " AND vote_result = '{vote_result}' "; }
	if ($startdate) { $postout .= "&startdate=".$startdate; $sqlconds .= " AND timestamp >= '{startdate}' "; }
	if ($enddate) { $postout .= "&enddate=".$enddate; $sqlconds .= " AND timestamp <= '{enddate}' "; }
	if ($vote_player_nick) { $postout .= "&vote_player_nick=".$vote_player_nick; $sqlconds .= " AND vote_player_nick LIKE '%{vote_player_nick}%' "; }
	if ($vote_player_id) { $postout .= "&vote_player_id=".$vote_player_id; $sqlconds .= " AND vote_player_id LIKE '%{vote_player_id}%' "; }
	if ($vote_player_ip) { $postout .= "&vote_player_ip=".$vote_player_ip; $sqlconds .= " AND vote_player_ip LIKE '%{vote_player_ip}%' "; }
	if ($nom_player_nick) { $postout .= "&nom_player_nick=".$nom_player_nick; $sqlconds .= " AND nom_player_nick LIKE '%{nom_player_nick}%' "; }
	if ($nom_player_id) { $postout .= "&nom_player_id=".$nom_player_id; $sqlconds .= " AND nom_player_id LIKE '%{nom_player_id}%' "; }
	if ($nom_player_ip) { $postout .= "&nom_player_ip=".$nom_player_ip; $sqlconds .= " AND nom_player_ip LIKE '%{nom_player_ip}%' "; }

	date_default_timezone_set('UTC');
	$arguments = array('server_ip'=>$server_ip, 'vote_type'=>$vote_type, 'vote_result'=>$vote_result, 'vote_player_nick'=>$vote_player_nick, 'vote_player_id'=>$vote_player_id, 'vote_player_ip'=>$vote_player_ip, 'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true), 'nom_player_nick'=>$nom_player_nick, 'nom_player_id'=>$nom_player_id, 'nom_player_ip'=>$nom_player_ip);
	$total_items = $db->Query("SELECT count(*) FROM `acp_vbk_logs` $sqlconds", $arguments, $config['sql_debug']);

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
					url:'acpanel/ajax.php?do=ajax_vbk_logs',
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
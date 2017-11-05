<?php

if( !isset($_GET['mask']) )
{
	$stats = array(
		'ga_total' => 0,
		'ga_by_nick' => 0,
		'ga_by_ip' => 0,
		'ga_by_steam' => 0,
		'ga_blocked' => 0,
		't_total' => 0,
		't_approved' => 0,
		't_rejected' => 0,
		't_open' => 0,
		'masks' => 0
	);

	// accounts stats
	if( $stats['ga_total'] = $db->Query("SELECT count(*) FROM `acp_players`", array(), $config['sql_debug']) )
	{
		$stats['ga_by_nick'] = $db->Query("SELECT count(*) FROM `acp_players` WHERE flag = 1", array(), $config['sql_debug']);
		$stats['ga_by_ip'] = $db->Query("SELECT count(*) FROM `acp_players` WHERE flag = 2", array(), $config['sql_debug']);
		$stats['ga_by_steam'] = $stats['ga_total'] - $stats['ga_by_nick'] - $stats['ga_by_ip'];
		$stats['ga_blocked'] = $db->Query("SELECT count(*) FROM `acp_players` WHERE approved = 'no'", array(), $config['sql_debug']);
	}

	// tickets stats
	if( $stats['t_total'] = $db->Query("SELECT count(*) FROM `acp_players_requests`", array(), $config['sql_debug']) )
	{
		$stats['t_approved'] = $db->Query("SELECT count(*) FROM `acp_players_requests` WHERE ticket_status = 1", array(), $config['sql_debug']);
		$stats['t_rejected'] = $db->Query("SELECT count(*) FROM `acp_players_requests` WHERE ticket_status = 2", array(), $config['sql_debug']);
		$stats['t_open'] = $db->Query("SELECT count(*) FROM `acp_players_requests` WHERE ticket_status = 0", array(), $config['sql_debug']);
	}

	// active masks
	$stats['masks'] = $db->Query("SELECT count(*) FROM `acp_access_mask`", array(), $config['sql_debug']);

	if( !$stats['ga_total'] && !$stats['t_total'] )
		$error = "@@not_accounts_and_tickets@@";
	else
	{
		$headinclude = "
			<script type='text/javascript' src='acpanel/scripts/js/highcharts.js'></script>
			<script type='text/javascript'>			
				(function ($) {
					$(function () {
						Highcharts.setOptions({
							global: {
								useUTC: false
							}
						});

						var chart;
						$(document).ready(function() {
					
							// define the options
							var options = {

								credits: {
									enabled: false
								},

								chart: {
									renderTo: 'chart-regaccounts-box',
									type: 'column'
								},
					
								title: {
									text: '@@accounts_reg_stats@@'
								},
								
								subtitle: {
									text: '@@accounts_reg_stats_by_week@@'
								},
					
								xAxis: [{
									type: 'datetime',
									tickWidth: 1
								}],
					
								yAxis: [{
									min: 0,
									title: {
										text: null
									},
									showFirstLabel: false
								}],
								
								tooltip: {
									shared: true,
									crosshairs: true
								},
					
								series: [{
									name: '@@total_reg_accounts@@',
									lineWidth: 2,
									marker: {
										radius: 2
									},
									data: []
								}]
							};

							$.ajax({
								type:'POST',
								url:'acpanel/ajax.php?do=ajax_gamecp',
								data:'go=24&action=w',
								dataType: 'json',
								success:function(result) {
									$.each(result.accreg, function(key, val) {
										options.series[0].data.push([parseInt(key), parseInt(val)]);
									});
									chart = new Highcharts.Chart(options, function(chart) {
										$('#' + options.chart.renderTo).append(
											$('<div>').addClass('time-selector').html('<ul><li id=\"sel-w\" class=\"nobg selected\"><span>@@one_week@@</span></li><li id=\"sel-y\"><span>@@one_year@@</span></li></ul>')
										);
									});
								}
							});

							$('.time-selector li span').live('click', function() {
								var item = $(this).parent();
								var arrItem = item.attr('id').split('-');

								if( !item.hasClass('selected') )
								{
									item.parents().eq(1).find('.selected').removeClass('selected');
									item.addClass('selected');

									chart.showLoading('@@loading@@');

									var subttl = {'w':'@@accounts_reg_stats_by_week@@', 'y':'@@accounts_reg_stats_by_year@@'};

									$.ajax({
										type:'POST',
										url:'acpanel/ajax.php?do=ajax_gamecp',
										data:'go=24&action=' + arrItem[1],
										dataType: 'json',
										success:function(result) {
											newdata = [];
											$.each(result.accreg, function(key, val) {
												newdata.push([parseInt(key), parseInt(val)]);
											});
											chart.series[0].setData(newdata, false);
											chart.setTitle(null, { text: subttl[arrItem[1]] });

											chart.redraw();
											chart.hideLoading();
										}
									});
								}
							});
						});
					});
				})(jQuery);
			</script>
		";

		$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_gamecp_accounts' OR link = 'p_gamecp_requests' OR link = 'p_gamecp_mask'", array(), $config['sql_debug']);
		
		if( is_array($result_cats) )
		{
			foreach ($result_cats as $obj)
			{
				$cats[$obj->link] = $obj->categoryid;	
			}

			$smarty->assign("cats", $cats);
		}
	}

	if(isset($error)) $smarty->assign("iserror",$error);
	$smarty->assign("stats",$stats);
}
else
{
	$mask = $_GET['mask'];

	if( !is_numeric($mask) )
	{
		$error = "@@not_mask@@";
	}
	else
	{
		$mask_servers = array();
		$result = $db->Query("SELECT s.hostname, s.address, m.mask_id, m.server_id FROM `acp_access_mask_servers` m 
			LEFT JOIN `acp_servers` s ON m.server_id = s.id WHERE m.mask_id = ".$mask, array(), $config['sql_debug']);

		if( is_array($result) )
		{
			foreach( $result as $obj )
			{
				$name = ( $obj->server_id === 0 ) ? "@@all_servers@@" : $obj->address.' - '.$obj->hostname;
				$mask_servers[] = $name;
			}
		}
		else
		{
			$error = "@@not_mask@@";
		}
	}

	if(isset($error)) $smarty->assign("iserror",$error);
	if(isset($mask_servers)) $smarty->assign("mask_servers",$mask_servers);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamecp_mask_serverlist.tpl');

	exit;
}

?>
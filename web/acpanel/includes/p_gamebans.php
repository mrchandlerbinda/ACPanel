<?php

$stats = array(
	'gb_sub_total' => 0,
	'gb_pl_total' => 0,
	'gb_by_nick' => 0,
	'gb_by_ip' => 0,
	'gb_by_steam' => 0
);

// ban subnets stats
$stats['gb_sub_total'] = $db->Query("SELECT count(*) FROM `acp_bans_subnets` WHERE approved = 1", array(), $config['sql_debug']);

// bans stats
$query = $db->Query("SELECT 
	count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, 1, NULL)) as expired, 
	count(IF((UNIX_TIMESTAMP() > (ban_created-1+(ban_length*60)) AND ban_length > 0 AND unban_created IS NULL) OR unban_created IS NOT NULL, NULL, 1)) as active, 
	count(IF(ban_type = 'N', 1, NULL)) as nick, count(IF(ban_type = 'SI', 1, NULL)) as ip, count(IF(ban_type = 'S', 1, NULL)) as steam
	FROM (
		(SELECT bid, ban_created, player_nick, player_ip, ban_type, ban_length, unban_created FROM `acp_bans_history`)
		UNION ALL
		(SELECT bid, ban_created, player_nick, player_ip, ban_type, ban_length, NULL FROM `acp_bans`)
	) temp", array(), $config['sql_debug']);

if( is_array($query) )
{
	foreach( $query as $obj )
	{
		$stats['gb_pl_total'] = $obj->expired + $obj->active;
		$stats['gb_by_nick'] = $obj->nick;
		$stats['gb_by_ip'] = $obj->ip;
		$stats['gb_by_steam'] = $obj->steam;
	}
}

if( !$stats['gb_sub_total'] && !$stats['gb_pl_total'] )
	$error = "@@not_bans@@";
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
								renderTo: 'chart-addbans-box',
								type: 'column'
							},
				
							title: {
								text: '@@add_bans_stats@@'
							},
							
							subtitle: {
								text: '@@add_bans_stats_by_week@@'
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
								name: '@@total_add_bans@@',
								lineWidth: 2,
								marker: {
									radius: 2
								},
								data: []
							}]
						};

						$.ajax({
							type:'POST',
							url:'acpanel/ajax.php?do=ajax_gamebans',
							data:'go=24&action=w',
							dataType: 'json',
							success:function(result) {
								$.each(result.addban, function(key, val) {
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

								var subttl = {'w':'@@add_bans_stats_by_week@@', 'y':'@@add_bans_stats_by_year@@'};

								$.ajax({
									type:'POST',
									url:'acpanel/ajax.php?do=ajax_gamebans',
									data:'go=24&action=' + arrItem[1],
									dataType: 'json',
									success:function(result) {
										newdata = [];
										$.each(result.addban, function(key, val) {
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

	$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_gamebans_subnets' OR link = 'p_gamebans_players' OR link = 'p_gamebans_search'", array(), $config['sql_debug']);
	
	if( is_array($result_cats) )
	{
		foreach( $result_cats as $obj )
		{
			$cats[$obj->link] = $obj->categoryid;	
		}

		$smarty->assign("cats", $cats);
	}
}

if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("stats",$stats);

?>
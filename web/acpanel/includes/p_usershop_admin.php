<?php

$stats = array(
	'ub_buy_mm' => 0,
	'ub_buy_pt' => 0
);

// buy MM stats
$sum = $db->Query("SELECT SUM(amount) FROM `acp_payment` WHERE pattern = '-1' AND enrolled > 0", array(), $config['sql_debug']);
$stats['ub_buy_mm'] = ( is_null($sum) ) ? 0 : round($sum, 2);

// exchange MM to PT stats
$sum = $db->Query("SELECT SUM(amount) FROM `acp_payment` WHERE pattern = 0 AND currency = 'points' AND enrolled > 0 AND amount > 0", array(), $config['sql_debug']);
$stats['ub_buy_pt'] = ( is_null($sum) ) ? 0 : round($sum);

$headinclude = "
	<link href='acpanel/templates/".$config['template']."/css/usershop.css' rel='stylesheet' type='text/css' />
";

if( !$stats['ub_buy_mm'] && !$stats['ub_buy_pt'] )
	$error = "@@not_payments@@";
else
{
	$headinclude .= "
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
								renderTo: 'chart-payment-box',
								type: 'column'
							},
				
							title: {
								text: '@@payment_stats@@'
							},
							
							subtitle: {
								text: '@@payment_stats_by_week@@'
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
								name: '@@total_payment@@',
								lineWidth: 2,
								marker: {
									radius: 2
								},
								data: []
							}]
						};

						$.ajax({
							type:'POST',
							url:'acpanel/ajax.php?do=ajax_payment',
							data:'go=4&action=w',
							dataType: 'json',
							success:function(result) {
								$.each(result.pay, function(key, val) {
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

								var subttl = {'w':'@@payment_stats_by_week@@', 'y':'@@payment_stats_by_year@@'};

								$.ajax({
									type:'POST',
									url:'acpanel/ajax.php?do=ajax_payment',
									data:'go=4&action=' + arrItem[1],
									dataType: 'json',
									success:function(result) {
										newdata = [];
										$.each(result.pay, function(key, val) {
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
}

if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("stats",$stats);

?>
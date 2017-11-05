<?php

if( isset($_GET['server']) && is_numeric($_GET['server']) )
{
	// 0 - general info
	// 1 - userbars
	// 2 - rating
	// 3 - stats
	
	if( !isset($_GET['t']) || !is_numeric($_GET['t']) || !in_array($_GET['t'], array(0,1,2,3)) )
	{
		header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&server='.$_GET["server"].'&t=0');
		exit;
	}
	
	$t = $_GET['t'];

	$id = $_GET['server'];
	$addHead = "";
	$productID = getProduct("ratingServers");
	$ratingServers = (!empty($productID)) ? TRUE : FALSE;

	$arguments = array('id'=>$id);
	$select = "a.id, a.userid, a.timestamp, a.gametype, a.address, a.hostname, a.description, a.opt_site AS site";
	$join = "";
	if( $ratingServers )
	{
		$select .= ", d.server_banner, d.server_descr, a.rating, a.position, a.vip, a.votes_up, a.votes_down, b.name AS mode, c.name AS city, a.opt_mode AS mode_id, a.opt_city AS city_id";
		$join = "
			LEFT JOIN `acp_servers_rating_temp` d ON d.server_id = a.id 
			LEFT JOIN `acp_servers_modes` b ON b.id = a.opt_mode 
			LEFT JOIN `acp_servers_cities` c ON c.id = a.opt_city 
		";
	}
	if( $config['mon_cache'] )
	{
		$select .= ", a.status, a.cache";
		if( $t == 2 )
			$select .= ", a.rating_vars";
	}

	$result = $db->Query("SELECT ".$select." FROM `acp_servers` a ".$join." WHERE a.id = '{id}' LIMIT 1", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, 'd-m-Y, H:i') : '';

			if( $config['mon_cache'] )
			{
				if( !isset($obj->server_banner) || $obj->server_banner == 0 )
					$obj->site = "";
				$cached_info = unserialize($obj->cache);
				unset($obj->cache);
	        		$server_fields = (array)$obj;
				if( $server_fields['hostname'] ) unset($cached_info['hostname']); 
				$server_fields = array_merge($server_fields, $cached_info);
			}
			else
			{
	        		$server_fields = (array)$obj;
			}
		}

		if( $ratingServers && !$t )
		{
			// ###############################################################################
			// Start load the required ThumbsUp classes
			// ###############################################################################
		
			define('THUMBSUP_DOCROOT', SCRIPT_PATH . 'thumbsup/');
			require THUMBSUP_DOCROOT.'classes/thumbsup.php';
			$tUP = new ThumbsUp($config, $db, $userinfo['uid']);
			require THUMBSUP_DOCROOT.'classes/thumbsup_cookie.php';
			require THUMBSUP_DOCROOT.'classes/thumbsup_item.php';
			require THUMBSUP_DOCROOT.'classes/thumbsup_template.php';
		
			// Debug mode is enabled
			if( ThumbsUp::config('sql_debug') )
			{
				// Enable all error reporting
				ThumbsUp::debug_mode();
			
				// Show an error if the headers are already sent
				if( headers_sent() )
				{
					trigger_error('thumbsup/init.php must be included before any output has been sent. Include it at the very top of your page.');
				}
			}
			
			// Enable support for json functions
			ThumbsUp::json_support();
			
			// Register new votes if any
			ThumbsUp::catch_vote();
	
			$addHead .= ThumbsUp::css()."
				<script type='text/javascript'>
					(function($) {
						$(function () {
							var forms = $('form.thumbsup');
			
							forms.live('submit', function() {
								return false;
							});
			
							forms.find(':input').live('click', function() {
								var th = $(this);
								th.closest('form').trigger('thumsup_vote', [th.val()]);
							});
			
							forms.live('thumsup_vote', function(event, vote) {
						
								var form = $(this),	template = form.attr('name');
			
								if (form.hasClass('busy') || form.hasClass('disabled')) return;
						
								// Prevent double votes
								form.addClass('busy');
						
								// Spinners
								var spinner = {
									small:       '<img class=\"spinner\" alt=\"···\" src=\"acpanel/scripts/thumbsup/images/spinner_small.gif\" />',
									large:       '<img class=\"spinner\" alt=\"···\" src=\"acpanel/scripts/thumbsup/images/spinner_large.gif\" />',
									large_green: '<img class=\"spinner\" alt=\"···\" src=\"acpanel/scripts/thumbsup/images/spinner_large_green.gif\" />',
									large_red:   '<img class=\"spinner\" alt=\"···\" src=\"acpanel/scripts/thumbsup/images/spinner_large_red.gif\" />',
								};
								var old = form.find('.result1').text();
			
								switch (template) {
						
									case 'buttons':
										form.find('.question, :button').remove();
										form.find('.result1').after(' ' + spinner.small);
										break;
						
									case 'mini_poll':
										form.find('.result1, .result2').html(spinner.small);
										break;
						
									case 'mini_thumbs':
										form.find('.result1').html(spinner.small);
										break;
						
									case 'thumbs_up_down':
										form.find('.result2').html(spinner.large_red);
									case 'thumbs_up':
										form.find('.result1').html(spinner.large_green);
										break;
						
									case 'up_down':
										form.find('.result1').html(spinner.large);
										break;
								}
			
								$.ajax({
									type: 'POST',
									url: 'acpanel/ajax.php?do=ajax_homepage',
									cache: false,
									dataType: 'json',
									timeout: 15000,
									data: {
										go: 9,
										thumbsup_id: form.find('input[name=thumbsup_id]').val(),
										thumbsup_format: form.find('input[name=thumbsup_format]').val(),
						
										thumbsup_vote: vote
									},			
									error: function(XMLHttpRequest, textStatus) {
										form.find('.error').text(textStatus);
									},
									success: function(data) {
			
										if ('error' in data) {
											switch (data.error) {
						
												case 'invalid_id':
													form.css('visibility', 'hidden');
													alert('@@vote_error@@');
													break;
						
												case 'closed':
													form.addClass('closed disabled');
													alert('@@vote_closed@@');
													break;
						
												case 'already_voted':
													form.addClass('user_voted disabled');
													alert('@@vote_already@@');
													break;
						
												case 'login_required':
													alert('@@vote_need_login@@');
													break;
						
												default:
													alert(data.error);
											}
						
											if( template === 'mini_thumbs' ) {
												form.find('.result1').text(old).fadeTo(0, 0.01).fadeTo('slow', 1);
											}
			
											return;
										}
						
										form.addClass('user_voted disabled');
			
										for (var i = 0; i < data.item.result.length; i++) {
											form.find('.result' + (i+1)).text(data.item.result[i]).fadeTo(0, 0.01).fadeTo('slow', 1);
										}
						
										if (template === 'mini_poll') {
											form.find('.graph').css({ opacity:0, width:0 }).show()
												.filter('.up')  .animate({ opacity:1, width:data.item.votes_pct_up   + '%' }).end()
												.filter('.down').animate({ opacity:1, width:data.item.votes_pct_down + '%' });
										}
									},
						
									complete: function() {
										form.find('.spinner').remove();
						
										form.removeClass('busy');
									}
								});
							});
						
							$(window).load(function() {		
								if (forms.filter('.thumbs_up').length) {
									var img = new Image; img.src = 'acpanel/scripts/thumbsup/images/spinner_small.gif';
									var img = new Image; img.src = 'acpanel/scripts/thumbsup/images/spinner_large.gif';
									var img = new Image; img.src = 'acpanel/scripts/thumbsup/images/spinner_large_green.gif';
									var img = new Image; img.src = 'acpanel/scripts/thumbsup/images/spinner_large_red.gif';
								}					
							});
						});				
					})(jQuery);
				</script>
			";
		
			// ###############################################################################
			// End load ThumbsUp classes
			// ###############################################################################
		}

		switch($t)
		{
			case "1":


				break;

			case "2":

				function convertToRPN($equation)
				{
					$equation = str_replace(' ', '', $equation);
					$tokens = token_get_all('<?php ' . $equation);
				
					$operators = array('*' => 1, '/' => 1, '+' => 2, '-' => 2);
					$rpn = '';
					$stack = array();
				
					for($i = 1; $i < count($tokens); $i++)
					{
						if( is_array($tokens[$i]) )
						{
							$rpn .= $tokens[$i][1] . ' ';
						}
						else
						{
						        if( empty($stack) || $tokens[$i] == '(' )
							{
								$stack[] = $tokens[$i];
						        }
							else
							{
						                if( $tokens[$i] == ')' )
								{
									while( end($stack) != '(' )
										$rpn .= array_pop($stack) . ' ';
									
									array_pop($stack);
						                }
								else
								{
									while( !empty($stack) && end($stack) != '(' && $operators[$tokens[$i]] >= $operators[end($stack)] )
										$rpn .= array_pop($stack) . ' ';
				
									$stack[] = $tokens[$i];
						                }
						        }
						}
					}
					
					while( !empty($stack) )
						$rpn .= array_pop($stack);
					
					return $rpn;
				}

				function polishDistortion($str)
				{
					$stack = explode(' ', trim(preg_replace('/[[:space:]]{2,}/', ' ', $str)));
					
					$cnt = count($stack);
					for( $i=0; $i<$cnt; $i++ )
					{
						if( in_array($stack[$i], array('-', '+', '*', '/')) && !is_numeric($stack[$i]) )
						{
							// for debug
							// echo join(' ',$stack).'<br />';
							
							if( $i < 2 ) { return false; }
							
							eval('$stack[$i]=$stack[($i-2)]'.$stack[$i].'$stack[($i-1)];');
							unset($stack[($i-1)]);
							unset($stack[($i-2)]);
							$stack = array_values($stack);
							
							$i = 0;
							$cnt = count($stack);
						}
					}
					return $stack[0];
				}
		
				class MyCallback
				{
					private $key = array();
					
					public function setValue($val)
					{
						$this->key = $val;
					}
					
					public function holdersReplace($matches)
					{
						$placeholder = $matches[1];
						$arr = $this->key;
		
						return (isset($arr[$placeholder])) ? $arr[$placeholder] : "";
					}
				}
		
				$callback = new MyCallback();

				$ratingVars = unserialize($server_fields['rating_vars']);
				$arrayRatingOptions = array();
				if( is_array($ratingVars) )
				{	
					$callback->setValue($ratingVars);
					$formulaVars = explode("+", $config['rating_formula']);
					foreach($formulaVars as $v)
					{
						if( preg_match("#{([^}]+)}#sUi", $v, $matches) )
						{
							if( isset($ratingVars[$matches[1]]) )
							{
								switch($matches[1])
								{
									case "online":
									case "uptime":
										$value = round($ratingVars[$matches[1]]).'%';
										break;
									case "pr":
									case "cy":
										$value = (!$server_fields['server_banner']) ? '-' : round($ratingVars[$matches[1]]);
										break;
									case "description":
									case "banner":
										$value = (!$ratingVars[$matches[1]]) ? '@@no@@' : '@@yes@@';
										break;
									default:
										$value = round($ratingVars[$matches[1]]);
										break;

								}
								$ratingFormula = preg_replace_callback("#{([^}]+)}#sUi", array($callback, 'holdersReplace'), $v);
								$arrayRatingOptions[] = array('option' => '@@rating_var_'.$matches[1].'@@', 'value' => $value, 'result' => round(polishDistortion(convertToRPN($ratingFormula))));
							}
						}
					}
				}					

				$smarty->assign("arrayRatingOptions", $arrayRatingOptions);
				break;

			case "3":

				$addHead .= "
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
											renderTo: 'chart-online-box'
										},
							
										title: {
											text: '@@server_statistics@@'
										},
										
										subtitle: {
											text: '@@monitoring_stats_by_day@@'
										},
							
										xAxis: [{
											type: 'datetime',
											tickWidth: 1
										}],
							
										yAxis: [{
											min: 0,
											max: 100,
											tickInterval: 20,
											title: {
												text: null
											},
											showFirstLabel: false
										}],
										
										tooltip: {
											shared: true,
											crosshairs: true
										},

										plotOptions: {
											area: {
												fillColor: {
													linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
													stops: [
														[0, Highcharts.getOptions().colors[0]],
														[1, 'rgba(2,0,0,0)']
													]
												},
												lineWidth: 1,
												marker: {
													enabled: false,
													states: {
														hover: {
															enabled: true,
															radius: 5
														}
													}
												},
												shadow: false,
												states: {
													hover: {
														lineWidth: 1
													}
												}
											}
										},
							
										series: [{
											type: 'area',
											name: '@@percent_uptime@@',
											lineWidth: 2,
											marker: {
												radius: 2
											},
											data: []
										},{
											name: '@@percent_players@@',
											lineWidth: 2,
											marker: {
												radius: 2
											},
											data: []
										}]
									};

									$.ajax({
										type:'POST',
										url:'acpanel/ajax.php?do=ajax_homepage',
										data:'go=15&action=d&server=".$id."',
										dataType: 'json',
										success:function(result) {
											$.each(result.uptime, function(key, val) {
												options.series[0].data.push([parseInt(key), val]);
											});
											$.each(result.players, function(key, val) {
												options.series[1].data.push([parseInt(key), val]);
											});
											chart = new Highcharts.Chart(options, function(chart) {
												$('#' + options.chart.renderTo).append(
													$('<div>').addClass('time-selector').html('<ul><li id=\"sel-d\" class=\"nobg selected\"><span>@@24_hours@@</span></li><li id=\"sel-w\"><span>@@one_week@@</span></li><li id=\"sel-y\"><span>@@one_year@@</span></li></ul>')
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
	
											var subttl = {'d':'@@monitoring_stats_by_day@@', 'w':'@@monitoring_stats_by_week@@', 'y':'@@monitoring_stats_by_year@@'};
	
											$.ajax({
												type:'POST',
												url:'acpanel/ajax.php?do=ajax_homepage',
												data:'go=15&action=' + arrItem[1] + '&server=".$id."',
												dataType: 'json',
												success:function(result) {
													newdata = [];
													$.each(result.uptime, function(key, val) {
														newdata.push([parseInt(key), val]);
													});
													chart.series[0].setData(newdata, false);
	
													newdata = [];
													$.each(result.players, function(key, val) {
														newdata.push([parseInt(key), val]);
													});
													chart.series[1].setData(newdata, false);
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

				break;

			default:
				$addHead .= "
					<script type='text/javascript' src='http://vkontakte.ru/js/api/openapi.js' charset='UTF-8'></script>
					<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
					<script type='text/javascript'>
			
						jQuery(document).ready(function($) {
							$('.steam-connect span, .steam-connect a').hover(
								function(e) {
									$(this).parent().addClass('active');
								},
								function() {
									$(this).parent().removeClass('active');
								}
							).click(function() {
								var par = $(this).parent('p');
								window.location.href = $('a', par).attr('href');
								return false;
							});
						});
			
					</script>
				";

				$username = $db->Query("SELECT username FROM `acp_users` WHERE uid = ".$server_fields['userid']." LIMIT 1", array(), $config['sql_debug']);
				if( $username )
				{
					$server_fields = array_merge($server_fields, array('username' => $username));
				}
		
				if( $ratingServers ) $smarty->assign("voted", ThumbsUp::item($id)->template('mini_thumbs')->format($config['mon_vote_format'])->options('align=left'));
		}

		include(INCLUDE_PATH . 'functions.servers.php');
		include(INCLUDE_PATH . 'class.SypexGeo.php');

		$server_protocol_list = server_protocol_list();
		$server_fields['gametype'] = array_merge(array('id' => $server_fields['gametype']), $server_protocol_list[$server_fields['gametype']]);
		$server_fields['favorite'] = favoritesFind($id);
		$server_fields['vkid'] = $config['vkid'];

		if( !$config['mon_cache'] )
		{
			// ###############################################################################
			// SERVER INFO
			// ###############################################################################
	
			$lists = explode(":", $server_fields['address']);
			$ip = $lists[0];
			$port = $lists[1];
	
			$live = server_query_live($server_fields['gametype']['id'], $ip, $port, "ep");
	
			if( $live['b']['status'] && isset($live['s']['name']) )
			{
				$live['b']['ping'] = (!$live['b']['ping']) ? 1 : $live['b']['ping'];
				$ping = $live['b']['ping']."&nbsp;@@ms@@";
				$map = $live['s']['map'];
				$players = $live['s']['players'];
				$playersmax = $live['s']['playersmax'];
				$os = (isset($live['e']['os'])) ? $live['e']['os'] : "";
				$pass = (isset($live['s']['password'])) ? $live['s']['password'] : "";
				$vac = (isset($live['e']['anticheat'])) ? $live['e']['anticheat'] : "";
				$status = 1;
			}
			else
			{
				$os = $pass = $vac = $ping = $map = "";
				$players = $playersmax = 0;
				$status = 0;
			}
	
			clearstatcache();
	
			if(file_exists(ROOT_PATH . "acpanel/images/maps/".$server_fields['gametype']['id']."/".$map.".jpg"))
			{
				$map_path = 1;
			}
			else
			{
				$map_path = 0;
			}
	
			$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
			$server_country_code = strtolower($SxGeo->getCountry($ip));
	
			clearstatcache();
	
			if(file_exists(ROOT_PATH . "acpanel/images/flags/".$server_country_code.".gif"))
			{
				$server_country = $server_country_code.".gif";
			}
			else
			{
				$server_country = "err.gif";
			}

			$server_info = array_merge($server_fields, array(
				'os' => $os,
				'players' => $players,
				'playersmax' => $playersmax,
				'pass' => $pass,
				'vac' => $vac,
				'map' => $map,
				'map_path' => $map_path,
				'status' => $status,
				'ping' => $ping,
				'country' => $server_country
			));
		}
		else
		{
			$server_info = $server_fields;			
		}

		unset($server_fields);
	}
	else
	{
		$error = '@@not_valid_server@@';
	}

	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		".$addHead."
		<script type='text/javascript'>
			(function ($) {
				$(function () {
					$('a[rel*=facebox]').facebox()
				});
			})(jQuery);

			jQuery(document).ready(function($) {
				$('#forma-select select').change(function () {
					window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&server=".$_GET['server']."&t=' + $('option:selected', this).val();
				});

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_homepage',
					data:'go=14&id=".$id."'
				});
			});
		</script>
	";

	$smarty->assign("get_in", $t);
}
else
{
	$error = '@@not_valid_server@@';
}

if( isset($server_info) )
{
	$cat_current['title'] = $server_info['hostname'];
}
else
{
	$cat_current['title'] = $server_info['hostname'] = "@@undefined_server@@";;
}
$smarty->assign("server_info",$server_info);
if(isset($error)) $smarty->assign("iserror",$error);

?>
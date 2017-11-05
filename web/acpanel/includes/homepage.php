<?php

include_once(INCLUDE_PATH . 'functions.servers.php');

if( isset($_GET['server']) && is_numeric($_GET['server']) && isset($_GET['info']) && $_GET['info'] == 'players' )
{
	$arguments = array('server'=>$_GET["server"]);
	$result = $db->Query("SELECT * FROM `acp_servers` WHERE id = '{server}' LIMIT 1", $arguments, $config['sql_debug']);

	if (is_array($result))
	{
		foreach ($result as $obj)
		{
			$server_fields = (array)$obj;
		}

		// ###############################################################################
		// SERVER INFO
		// ###############################################################################

		$lists = explode(":", $server_fields['address']);
		$ip = $lists[0];
		$port = $lists[1];

		$live = server_query_live($server_fields['gametype'], $ip, $port, "sp");

		if( $live['b']['status'] && isset($live['s']['name']) )
		{
			$hostname = (strlen($server_fields['hostname']) > 0) ? $server_fields['hostname'] : $live['s']['name'];
		}
		else
		{
			$hostname = (strlen($server_fields['hostname']) > 0) ? $server_fields['hostname'] : "-";
		}

		// ###############################################################################
		// PLAYERS INFO
		// ###############################################################################

		if (!empty($live['p']))
		{
			$player_key = 1;

			foreach ($live['p'] as $v)
			{
				$players_info[$player_key] = array($v['pid'], $v['name'], $v['score'], $v['time']);

				$player_key ++;
			}
		}
	}
	else
	{
		$error = '@@not_valid_server@@';
	}

	$smarty->assign("cat_current",array('title'=>'@@server@@ #'.$server_fields['id']));
	$smarty->assign("server_info",array('ip' => $server_fields['address'], 'hostname' => $hostname, 'type' => $server_fields['gametype']));
	if(isset($players_info)) $smarty->assign("players_info",$players_info);
	if(isset($error)) $smarty->assign("iserror",$error);

	header('Content-type: text/html; charset='.$config['charset']);

	$smarty->registerFilter("output","translate_template");
	$smarty->display("homepage_info.tpl");

	exit;
}
else
{
	$productID = getProduct("ratingServers");
	$ratingServers = (!empty($productID)) ? TRUE : FALSE;
	$addHead = "";

	if( $ratingServers )
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

		$addHead = ThumbsUp::css()."
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
				}(jQuery));
			</script>
		";
	
		// ###############################################################################
		// End load ThumbsUp classes
		// ###############################################################################
	}

	$sorting = ($ratingServers) ? "position ASC" : "rating DESC";
	$where = " WHERE active = 1";
	$arguments = $filter = array();

	if( $filter['status'] = $config['mon_hide_offline'] )
	{
		$where .= " AND status = 1";
	}
	else
		unset($filter['status']);

	include(INCLUDE_PATH . 'class.SypexGeo.php');
	$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');

	if( $config['mon_cache'] && $ratingServers )
	{
		if( $total_items = $db->Query("SELECT count(*) FROM `acp_servers`".$where, $arguments, $config['sql_debug']) )
			$servers = getMonitoringCache(0, $config['mon_view_per_page'], $sorting, $filter);
		else
			$servers = array();
	}
	else
	{
		if( $total_items = $db->Query("SELECT count(*) FROM `acp_servers` WHERE active = 1", array(), $config['sql_debug']) )
		{
			$servers = create_monitoring_list(0, $total_items, $sorting, $total_items);

			if( count($servers) > 1 )
			{
				$total_items = $servers['total'];
			}
			else
				$total_items = 0;

			unset($servers['total']);
		}
		else
		{
			$servers = array();
		}
	}

	$cache_content = $favorites_content = "";
	$favorites = array();

	$server_card_id = 0;
	if( $ratingServers )
	{
		foreach( $all_categories as $key => $value )
		{
			if( $server_search = array_search("p_server_card", $value) )
			{
				$server_card_id = $key;
				break;
			}
		}
	}

	if( !empty($servers) )
	{
		// GET FAVORITES START
		$favorites = favoritesGet();

		if( !empty($favorites) )
		{
			foreach($favorites as $k => $v)
			{
				if( isset($servers[$v]) )
				{
					$servers[$v]['favorite'] = true;
					$tmp[$v] = $servers[$v];
					unset($favorites[$k]);
				}
			}

			if( !empty($favorites) )
			{
				$favorites = favoritesGetServers($favorites, $sorting);

				if( isset($tmp) )
					$favorites = array_merge($tmp, $favorites);
			}
			else
			{
				$favorites = $tmp;
			}
		}
		// GET FAVORITES END

		// GET SERVERS FILTER START

		$filterTypes = $filterModes = $filterCities = array();

		$types_result = $db->Query("SELECT gametype, count(gametype) AS cnt FROM `acp_servers` WHERE active = 1 GROUP BY gametype ORDER BY cnt DESC", array(), $config['sql_debug']);
		if( is_array($types_result) )
		{
			$protocolList = server_protocol_list();
			foreach( $types_result as $obj )
			{
				$filterTypes[$obj->gametype] = $obj->cnt;
				$protocolList[$obj->gametype]['cnt'] = $obj->cnt;
			}

			$filterTypes = array_intersect_key($protocolList, $filterTypes);
		}

		$modes_result = $db->Query("SELECT m.id, m.name, count(m.name) AS cnt FROM `acp_servers_modes` m LEFT JOIN `acp_servers` s ON s.opt_mode = m.id WHERE s.id IS NOT NULL AND s.active = 1 GROUP BY m.name ORDER BY cnt DESC", array(), $config['sql_debug']);
		if( is_array($modes_result) )
		{
			foreach( $modes_result as $obj )
			{
				$filterModes[] = (array)$obj;
			}
		}

		$cities_result = $db->Query("SELECT c.id, c.name, count(c.name) AS cnt FROM `acp_servers_cities` c LEFT JOIN `acp_servers` s ON s.opt_city = c.id WHERE s.id IS NOT NULL AND s.active = 1 GROUP BY c.name ORDER BY cnt DESC", array(), $config['sql_debug']);
		if( is_array($cities_result) )
		{
			foreach( $cities_result as $obj )
			{
				$filterCities[] = (array)$obj;
			}
		}

		$smarty->assign("filter_types", $filterTypes);
		$smarty->assign("filter_modes", $filterModes);
		$smarty->assign("filter_cities", $filterCities);

		// GET SERVERS FILTER END

		$cache_content .= "
			<script type='text/javascript'>
				function previewScreenshot() {
					var xoffset = 20;
					var yoffset = 22;
			
					jQuery('a.screenshot').live(
						'mouseout mousemove mouseover',
						function(e) {
							if (e.type == 'mouseover') {
								this.t = this.title;
								this.title = '';
				
								jQuery('body').append(\"<p id='screenshot'><img src='\"+ this.rel +\"' alt='\"+ this.t +\"' /></p>\");
								jQuery('#screenshot')
									.css('top',(e.pageY - xoffset) + 'px')
									.css('left',(e.pageX + yoffset) + 'px')
									.fadeIn('fast');
							}
							
							if (e.type == 'mouseout') {
								this.title = this.t;
								jQuery('#screenshot').remove();
							}

							if (e.type == 'mousemove') {
								jQuery('#screenshot')
									.css('top',(e.pageY - xoffset) + 'px')
									.css('left',(e.pageX + yoffset) + 'px');
							}
						}
					).live('click', function() {
						return false;
					});
				};
			
				jQuery(document).ready(function($) {			
					previewScreenshot();
			
					$('.steam-connect span, .steam-connect a').live(
						'mouseout mouseover',
						function(e) {
							if (e.type == 'mouseover') {
								$(this).parent().addClass('active');
							}
							
							if (e.type == 'mouseout') {
								$(this).parent().removeClass('active');
							}
						}
					).click(function() {
						var par = $(this).parent('td');
						window.location.href = $('a', par).attr('href');
						return false;
					});
			
				});
			</script>
		";

		$cache_content .= '
			<table class="tablesorter monitoring" cellpadding="0" cellspacing="0" width="100%">
				<tbody>
		';

		$page_limit = $config['mon_view_per_page'];
	
		foreach( $servers as $server )
		{
			$page_limit = $page_limit - 1;
			$descr_class = $descr_row = $descr_tr = "";
			if( $server['description'] )
			{
				$descr_row = " rowspan='2'";
				$descr_tr = "<tr class='descr".(($server['vip']) ? ' vip' : '')."'><td colspan='3'>".htmlspecialchars($server['description'], ENT_QUOTES)."</td></tr>";
	
				if( $server['vip'] )
					$descr_class = ' class="vip"';
			}
			else
			{
				$descr_class = ' class="descr_no'.(($server['vip']) ? ' vip' : '').'"';
			}
	
			$cache_content .= '
				<tr'.(($server['vip']) ? ' class="vip"' : '').'>
					<td class="pos"><span title="'.$server['position'].' @@position_in_rating@@">'.(($server['position']) ? '#'.$server['position'] : '').'</span></td>
					<td class="server-name">
						<div class="add-favorite"><img src="acpanel/images/favorites'.(($server['favorite']) ? "_s" : "").'.png" alt="@@add_favorite@@" title="@@add_favorite@@" /></div>
						<img src="acpanel/images/flags/'.$server['country'].'" alt="" />&nbsp;<a href="'.((!$server_card_id) ? '#' : $config['acpanel'].'.php?cat='.$current_section_id.'&do='.$server_card_id.'&server='.$server['id']).'">'.htmlspecialchars($server['hostname'], ENT_QUOTES).'</a>
					</td>
					<td class="steam-connect"><span></span><a rel="nofollow" href="steam://connect/'.$server['address'].'" title="@@connect@@">'.$server['address'].'</a></td>
					<td class="rating">@@rating@@ '.$server['rating'].'</td>
				</tr>
				<tr id="server_'.$server['id'].'"'.$descr_class.'>
					<td class="gtype"'.$descr_row.'><img src="acpanel/images/games/'.$server['gametype'].'.png" alt="" /></td>
					<td colspan="3" class="server-online">
			';

			$cache_content .= '.: <a style="color: #444444; text-decoration: underline;" href="'.$config['acpanel'].'.php#mod='.$server['mode_id'].'">'.$server['mode'].'</a> .: <a style="color: #444444; text-decoration: underline;" href="'.$config['acpanel'].'.php#city='.$server['mode_id'].'">'.$server['city'].'</a> .: ';

			if( !$server['status'] )
			{
				$cache_content .= '
						@@srv_not_resp@@
				';
			}
			else
			{
				$cache_content .= '
						@@online@@ <a href="'.$config['acpanel'].'.php?server='.$server['id'].'&info=players" rel="facebox nofollow" title="@@players_info@@">'.$server['players'].'/'.$server['playersmax'].'</a>
				';
	
				if( $server['gametype'] != 'minecraft' )
				{
					$cache_content .= '
							@@map@@
					';
		
					if( $server['map_path'] )
					{
						$cache_content .= '
							<a style="color: #444444;" href="#" class="screenshot" rel="acpanel/images/maps/'.$server['gametype'].'/'.$server['map'].'.jpg" title="'.$server['map'].'"><u>'.$server['map'].'</u></a>
						';
					}
					else
					{
						$cache_content .= '
							'.$server['map'].'
						';
					}
				}
			}
	
			$cache_content .= '
						<div class="voted">'.(($ratingServers) ? ThumbsUp::item($server['id'])->template('mini_thumbs')->format($config['mon_vote_format'])->options('align=right') : "").'</div>
					</td>
			';
	
			$cache_content .= '
				</tr>
				'.$descr_tr.'
			';
	
			if( $page_limit == 0 )
				break;
		}
	
		$cache_content .= '
				</tbody>
			</table>
		';

		if( empty($favorites) )
		{
			$favorites_content .= '<div class="message warning"><p>@@no_favorites@@</p></div>';
		}
		else
		{
			$favorites_content .= '
				<table class="tablesorter monitoring" cellpadding="0" cellspacing="0" width="100%">
					<tbody>
			';
	
			foreach( $favorites as $server )
			{
				$descr_class = $descr_row = $descr_tr = "";
				if( $server['description'] )
				{
					$descr_row = " rowspan='2'";
					$descr_tr = "<tr class='descr".(($server['vip']) ? ' vip' : '')."'><td colspan='3'>".htmlspecialchars($server['description'], ENT_QUOTES)."</td></tr>";
		
					if( $server['vip'] )
						$descr_class = ' class="vip"';
				}
				else
				{
					$descr_class = ' class="descr_no'.(($server['vip']) ? ' vip' : '').'"';
				}
		
				$favorites_content .= '
					<tr'.(($server['vip']) ? ' class="vip"' : '').'>
						<td class="pos"><span title="'.$server['position'].' @@position_in_rating@@">#'.$server['position'].'</span></td>
						<td class="server-name">
							<div class="add-favorite"><img src="acpanel/images/favorites'.(($server['favorite']) ? "_s" : "").'.png" alt="@@add_favorite@@" title="@@add_favorite@@" /></div>
							<img src="acpanel/images/flags/'.$server['country'].'" alt="" />&nbsp;<a href="'.((!$server_card_id) ? '#' : $config['acpanel'].'.php?cat='.$current_section_id.'&do='.$server_card_id.'&server='.$server['id']).'">'.htmlspecialchars($server['hostname'], ENT_QUOTES).'</a>
						</td>
						<td class="steam-connect"><span></span><a rel="nofollow" href="steam://connect/'.$server['address'].'" title="@@connect@@">'.$server['address'].'</a></td>
						<td class="rating">@@rating@@ '.$server['rating'].'</td>
					</tr>
					<tr id="favorite_'.$server['id'].'"'.$descr_class.'>
						<td class="gtype"'.$descr_row.'><img src="acpanel/images/games/'.$server['gametype'].'.png" alt="" /></td>
						<td colspan="3" class="server-online">
				';

				$favorites_content .= '.: <a style="color: #444444; text-decoration: underline;" href="'.$config['acpanel'].'.php#mod='.$server['mode_id'].'">'.$server['mode'].'</a> .: <a style="color: #444444; text-decoration: underline;" href="'.$config['acpanel'].'.php#city='.$server['city_id'].'">'.$server['city'].'</a> .: ';

				if( !$server['status'] )
				{
					$favorites_content .= '
							@@srv_not_resp@@
					';
				}
				else
				{
					$favorites_content .= '
							@@online@@ <a href="'.$config['acpanel'].'.php?server='.$server['id'].'&info=players" rel="facebox nofollow" title="@@players_info@@">'.$server['players'].'/'.$server['playersmax'].'</a>
					';
		
					if( $server['gametype'] != 'minecraft' )
					{
						$favorites_content .= '
								@@map@@
						';

						if( $server['map_path'] )
						{
							$favorites_content .= '
								<a style="color: #444444;" href="#" class="screenshot" rel="acpanel/images/maps/'.$server['gametype'].'/'.$server['map'].'.jpg" title="'.$server['map'].'"><u>'.$server['map'].'</u></a>
							';
						}
						else
						{
							$favorites_content .= '
								'.$server['map'].'
							';
						}
					}
				}
		
				$favorites_content .= '
							<div class="voted">'.(($ratingServers) ? ThumbsUp::item($server['id'])->template('mini_thumbs')->format($config['mon_vote_format'])->options('align=right') : "").'</div>
						</td>
				';
		
				$favorites_content .= '
					</tr>
					'.$descr_tr.'
				';
			}
	
			$favorites_content .= '
					</tbody>
				</table>
			';
		}
	}
	else
	{
		$cache_content .= '<div class="message errormsg"><p>@@empty_data_servers@@</p></div>';
		$favorites_content .= '<div class="message warning"><p>@@no_favorites@@</p></div>';
		$smarty->assign("filter_types", array());
		$smarty->assign("filter_modes", array());
		$smarty->assign("filter_cities", array());
	}

	$smarty->assign("servers_list", $cache_content);
	$smarty->assign("favorites_list", $favorites_content);

	 $headinclude = "
        ".$addHead."
        <script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
        <script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
        <script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
        <script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
        <script type='text/javascript' src='acpanel/scripts/js/jquery.hashhistory.js'></script>
        <script type='text/javascript' src='acpanel/scripts/js/jquery.chosen.js'></script>
        <link href='acpanel/templates/".$config['template']."/css/chosen.css' rel='stylesheet' type='text/css' />
        <script type='text/javascript'>
 
            jQuery.blockUI.defaults.overlayCSS.opacity = .2;
            var filterSelected = null;
 
            (function ($) {
                $(function() {
                    $('a[rel*=facebox]').facebox();
                    $('.chosen').chosen({allow_single_deselect:true}).live('change', function() {
                        filterSelected = $(this).attr('name');
                        var str = '', sSRV = $('[name=\"srv\"]').val(), sMOD = $('[name=\"mod\"]').val(), sCITY = $('[name=\"city\"]').val();
                        if(sSRV != 0 && sSRV != undefined) str = str + '&srv=' + sSRV;
                        if(sMOD != 0 && sMOD != undefined) str = str + '&mod=' + sMOD;
                        if(sCITY != 0 && sCITY != undefined) str = str + '&city=' + sCITY;
                        str = (!str) ? '#all' : '#' + str.slice(1);
                        window.location.hash = str;
                    });
                });
            })(jQuery);
 
            function pageselectCallback(page_id, total, jq)
            {
                jQuery('html, body').animate({ scrollTop: 220 }, 'slow');
 
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
 
                var pg_size = ".$config['mon_view_per_page'].";
                var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
 
                if(total < second)
                {
                    second = total;
                }
 
                if(!total)
                {
                    jQuery('#Searchresult').remove();
                    jQuery('#Pagination').html('');
                    startUpdateTimer(1);
                }
                else
                {
                    if( jQuery('#Searchresult').length == 0 )
                    {
                        jQuery('#ajaxContent').parent().append(jQuery('<div>').attr('id','Searchresult'));
                    }
                    jQuery('#Searchresult').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
 
                    jQuery.ajax({
                        type:'POST',
                        url:'acpanel/ajax.php?do=ajax_homepage',
                        data:({'go' : 1, 'srv' : jQuery('[name=\"srv\"]').val(),'mod' : jQuery('[name=\"mod\"]').val(),'city' : jQuery('[name=\"city\"]').val(), 'cat' : ".$section_current['id'].", 'srv_cat' : ".$server_card_id.", 'offset' : first, 'limit' : pg_size}),
                        success:function(result) {
                            startUpdateTimer(1);
                            jQuery('#ajaxContent').html(result);
                        }
                    });
                }
 
                return false;
            }
 
            function loadFirstPageNote()
            {
                var total = ".$total_items.";
                var pg_size = ".$config['mon_view_per_page'].";
                var first = 1, second = pg_size;
 
                if(total < second)
                {
                    second = total;
                }
 
                if(!total)
                {
                    jQuery('#Searchresult').remove();
                    jQuery('#Pagination').html('');
                }
                else
                {
                    if( jQuery('#Searchresult').length == 0 )
                    {
                        jQuery('#ajaxContent').parent().append(jQuery('<div>').attr('id','Searchresult'));
                    }
                    jQuery('#Searchresult').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
                }
 
                startUpdateTimer(1);
            }
 
            function rePagination() {
                jQuery('#ajaxContent').html(
                    jQuery('<div>')
                    .addClass('center-img-block')
                    .append(
                        jQuery('<img>')
                        .attr('src','acpanel/images/ajax-big-loader.gif')
                        .attr('alt','@@refreshing@@')
                    )
                );
 
                var total = (jQuery('#Searchresult span').length == 0) ? 0 : parseInt(jQuery('#Searchresult span').text());
 
                if(total == 0)
                {
                    jQuery('#ajaxContent').html(
                        jQuery('<div>')
                        .addClass('message')
                        .addClass('errormsg')
                        .append(
                            jQuery('<p>')
                            .html('@@empty_data_servers@@')
                        )
                    );
 
                    startUpdateTimer(1);
                }
                else
                {
                    var pg_size = ".$config['mon_view_per_page'].";
                    var set_page = (jQuery('.pagination span.active').not('.prev, .next').length > 0) ? parseInt(jQuery('.pagination span.active').not('.prev, .next').text()) - 1 : 0;
   
                    jQuery('#Pagination').pagination( total, {
                        num_edge_entries: 2,
                        num_display_entries: 8,
                        callback: pageselectCallback,
                        items_per_page: pg_size,
                        current_page: set_page
                    });
                }
            }
 
            var intervalID;
 
            function startUpdateTimer(v) {
                if(v == 0)
                {
                    window.clearInterval(intervalID);
                    jQuery('.block_head a').text('@@refreshing@@').unbind('click').click(function() {return false;});
                    rePagination();
                }
                else if(v == 1)
                {
                    jQuery('.block_head a').unbind('click').click(function() {
                        if( !jQuery('#monitoring-tab ul li:first-child').hasClass('selected') )
                        {
                            jQuery('#monitoring-tab ul li:first-child').click();
                        }
                        startUpdateTimer(0);
                        return false;
                    });
                    var timetogo = ".$config['home_refresh_time'].";
                    if(timetogo)
                    {
                        intervalID = window.setInterval(function() {
                            jQuery('.block_head a').text(timetogo + ' @@to_refresh@@');
                            if (timetogo <= 0)
                            {
                                window.clearInterval(intervalID);
                                jQuery('.block_head a').text('@@refreshing@@').unbind('click').click(function() {return false;});
                                rePagination();
                            }
                            timetogo--;
                        }, 1000);
                    }
                    else
                    {
                        jQuery('.block_head a').text('@@refresh@@');
                    }
                    jQuery.unblockUI();
                }
                else
                {
                    window.clearInterval(intervalID);
                    jQuery('.block_head a').text('@@refreshing@@').unbind('click').click(function() {return false;});
                    jQuery.blockUI({ message: null });
                }
            }
 
            function changeHash(newHash)
            {
                if(jQuery('.center-img-block').length == 0)
                {
                    jQuery('#ajaxContent').html(
                        jQuery('<div>')
                        .addClass('center-img-block')
                        .append(
                            jQuery('<img>')
                            .attr('src','acpanel/images/ajax-big-loader.gif')
                            .attr('alt','@@refreshing@@')
                        )
                    );
                }
 
                startUpdateTimer(2);
 
                var hh = newHash.toString().substring(1);
                var arrHH = hh.split('&');
                var arrSTR = '';
                var arrF = [0,0,0];
                var fError = null;
 
                for( var i=0,len=arrHH.length;i<len;i++ )
                {
                    arrSTR = arrHH[i].split('=');
 
                    switch(arrSTR[0])
                    {
                        case 'srv':
                            arrF[0] = arrSTR[1];
                            break;
                        case 'mod':
                            arrF[1] = arrSTR[1];
                            break;
                        case 'city':
                            arrF[2] = arrSTR[1];
                            break;
                    }
                }
 
                jQuery('#filters .filters-item').each(function(e) {
                    if(fError) return false;
 
                    var th = this;
                    var name = jQuery('.chosen', th).attr('name');
 
                    switch(name)
                    {
                        case 'srv':
                            t = arrF[0]
                            break;
                        case 'mod':
                            t = arrF[1]
                            break;
                        case 'city':
                            t = arrF[2]
                            break;
                    }
 
                    if(jQuery('.chosen', th).attr('name') == filterSelected)
                    {
                        jQuery('.chosen', th).val(t);
                        jQuery('.chosen', th).trigger('liszt:updated');
                        return true;
                    }
                    jQuery('.chzn-container', th).hide();
                    jQuery(th).css('background', 'url(acpanel/images/ajax-loader-filter.gif) center center no-repeat');
 
                    jQuery.ajax({
                        type:'POST',
                        url:'acpanel/ajax.php?do=ajax_homepage',
                        async:false,
                        data:({'go' : 13, 'filters[]' : arrF, 'current_name' : name, 'current_val' : t}),
                        success:function(result) {
                            if(!result)
                            {
                                if( !fError )
                                {
                                    fError = true;
                                    window.location.hash = '#all';
                                }
                            }
                            else
                            {
                                jQuery(th).css('background', '#ffffff').html(result);
                                jQuery('.chosen', th).chosen({allow_single_deselect:true});
                            }
                        }
                    });
                });
 
                if( !fError )
                {
                    jQuery.ajax({
                        type:'POST',
                        url:'acpanel/ajax.php?do=ajax_homepage',
                        async:false,
                        data:({'go' : 12, 'srv' : arrF[0], 'mod' : arrF[1], 'city' : arrF[2]}),
                        success:function(result) {
                            var pg_size = ".$config['mon_view_per_page'].";
                            var total = result, first = 1, second = pg_size;
           
                            if(total < second)
                            {
                                second = total;
                            }
           
                            if(!parseInt(total))
                            {
                                jQuery('#Searchresult').remove();
                                jQuery('#Pagination').html('');
   
                                jQuery('#ajaxContent').html(
                                    jQuery('<div>')
                                    .addClass('message')
                                    .addClass('errormsg')
                                    .append(
                                        jQuery('<p>')
                                        .html('@@empty_data_servers@@')
                                    )
                                );
                                startUpdateTimer(1);
                            }
                            else
                            {
                                if( jQuery('#Searchresult').length == 0 )
                                {
                                    jQuery('#ajaxContent').parent().append(jQuery('<div>').attr('id','Searchresult'));
                                }
                                jQuery('#Searchresult').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
                                jQuery('#Pagination').pagination( total, {
                                    num_edge_entries: 2,
                                    num_display_entries: 8,
                                    callback: pageselectCallback,
                                    items_per_page: pg_size,
                                    current_page: 0
                                });
                            }
                        }
                    });
                }
            }
 
            jQuery(document).bind('hashChange', function(e, newHash) {
                if( !jQuery('#monitoring-tab ul li:first-child').hasClass('selected') )
                {
                    jQuery('#monitoring-tab ul li:first-child').click();
                }
                newHash = (newHash != '') ? newHash : '#all';
                changeHash(newHash);
            });
 
            jQuery(document).ready(function($) {
                loadFirstPageNote();
 
                $('#Pagination').pagination( ".$total_items.", {
                    num_edge_entries: 2,
                    num_display_entries: 8,
                    callback: pageselectCallback,
                    load_first_page: false,
                    show_if_no_items: false,
                    items_per_page: ".$config['mon_view_per_page']."
                });
 
                $('.block_head a').click(function() {
                    if( !$('#monitoring-tab ul li:first-child').hasClass('selected') )
                    {
                        $('#monitoring-tab ul li:first-child').click();
                    }
 
                    startUpdateTimer(0);
                    return false;
                });
 
                $('.tabmenu li').live('click', function() {
                    if( !$(this).hasClass('selected') )
                    {
                        if( $(this).hasClass('view_favorites') )
                        {
                            $('#filters').hide();
                            $('#servers-list').fadeOut();
                            $('#my-favorites').fadeIn();
                            $(this).parent().find('.selected').removeClass('selected');
                            $(this).addClass('selected');
                        }
                        else
                        {
                            $('#my-favorites').fadeOut();
                            $('#servers-list').fadeIn();
                            $(this).parent().find('.selected').removeClass('selected');
                            $(this).addClass('selected');
                            $('#filters').show();
                        }
                    }
                });
 
                $('.add-favorite img').live('click', function() {
                    $.blockUI({ message: null });
                    var th = $(this);
                    var action = (th.attr('src').indexOf('favorites_s.png') + 1) ? 0 : 1;
                    var serverSplitter = th.parents().eq(2).next('tr').attr('id').split('_');
                    var serverID = serverSplitter[1];
 
                    $.ajax({
                        type:'POST',
                        url:'acpanel/ajax.php?do=ajax_homepage',
                        data:'go=11&action=' + action + '&server=' + serverID,
                        success:function(result) {
                            $.unblockUI({
                                onUnblock: function() {
                                    if(result.indexOf('id=\"success\"') + 1)
                                    {
                                        if($('tr#server_' + serverID).length > 0)
                                        {
                                            $('tr#server_' + serverID).prev('tr').find('.add-favorite img').attr('src', th.attr('src').replace('/favorites' + ((action) ? '' : '_s') + '.png', '/favorites' + ((action) ? '_s' : '') + '.png'));
                                        }
                                        if(!action)
                                        {
                                            $('tr#favorite_' + serverID).prev('tr').remove();
                                            if( !$('tr#favorite_' + serverID).hasClass('descr_no') )
                                                $('tr#favorite_' + serverID).next('tr').remove();
                                            $('tr#favorite_' + serverID).remove();
                                            if( $('#my-favorites tr').length == 0 )
                                            {
                                                $('#my-favorites').html($('<div>').addClass('message warning').append('<p>@@no_favorites@@</p>'));
                                            }
                                        }
                                        else
                                        {
                                            if( $('#my-favorites tr').length == 0 )
                                            {
                                                $('#my-favorites').html('<table class=\"tablesorter monitoring\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody></tbody></table>');
                                            }
                                            $('tr#server_' + serverID).prev('tr').clone().appendTo('#my-favorites tbody');
                                            $('tr#server_' + serverID).clone().attr('id','favorite_' + serverID).appendTo('#my-favorites tbody');
                                            if( !$('tr#server_' + serverID).hasClass('descr_no') )
                                                $('tr#server_' + serverID).next('tr').clone().appendTo('#my-favorites tbody');
 
                                            $('a[rel*=facebox]', 'tr#favorite_' + serverID).facebox();
                                        }
                                        humanMsg.displayMsg(result, 'success');
                                    }
                                    else
                                    {
                                        humanMsg.displayMsg(result, 'error');
                                    }
                                }
                            });
                        }
                    });
                });
            });
        </script>
    ";
 
    if(isset($error)) $smarty->assign("iserror",$error);
}
 
?>
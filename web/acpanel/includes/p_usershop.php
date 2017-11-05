<?php

$where = " WHERE a.active = 1 AND (a.duration_type != 'date' OR a.item_duration > UNIX_TIMESTAMP() OR a.item_duration = 0) AND (a.max_sale_items_duration != 'total' OR a.max_sale_items = 0 OR (a.max_sale_items_duration = 'total' AND a.max_sale_items > a.purchased))";

$total_items = $db->Query("SELECT count(a.id) FROM `acp_payment_patterns` a
	LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
	".$where, array(), $config['sql_debug']);

if( !$total_items )
{
	if( is_null($total_items) ) $total_items = 0;
	$error = "@@active_privileges_not_found@@";
}
else
{
	$result = $db->Query("SELECT a.id, a.name, a.description, a.price_mm, a.price_points, a.duration_type, a.item_duration, a.item_duration_select, 
		a.max_sale_items, a.max_sale_items_duration, a.max_sale_for_user, a.max_sale_for_user_duration, a.image 
		FROM `acp_payment_patterns` a
		LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
		".$where." GROUP BY a.id ORDER BY a.id DESC LIMIT 0,{pagesize}
	", array('pagesize' => $config['ub_pagesize']), $config['sql_debug']);

	if( is_array($result) )
	{
		$search_editcat = false;
		foreach( $all_categories as $key => $value )
		{
			$search_editcat_id = array_search("p_usershop_buywindow", $value);
			if( $search_editcat_id )
			{
				$search_editcat = $key;
				$smarty->assign("cat_buy", $search_editcat);
				break;
			}
		}

		foreach( $result as $obj )
		{
			switch($obj->duration_type)
			{
				case "date":

					$obj->item_duration = ($obj->item_duration > 0) ? get_datetime($obj->item_duration, $config['date_format']) : "@@all_duration@@";
					break;

				case "year":
				case "month":
				case "day":

					$obj->item_duration = get_correct_str($obj->item_duration, "@@time_".$obj->duration_type."_one@@", "@@time_".$obj->duration_type."_several@@", "@@time_".$obj->duration_type."_many@@");
					break;
			}
			if( $obj->price_mm > 0 ) $obj->price_mm_info = $obj->price_mm.$config['ub_currency_suffix'];
			if( $obj->price_points > 0 ) $obj->price_points_info = $obj->price_points."@@points_suffix@@";
			if( $obj->max_sale_items > 0 )
			{
				if( $obj->max_sale_items_duration )
				{
					$obj->max_sale_items = $obj->max_sale_items.(($obj->max_sale_items_duration == 'total') ? "" : "/@@".$obj->max_sale_items_duration."@@");
				}
			}
			if( $obj->max_sale_for_user > 0 )
			{
				if( $obj->max_sale_for_user_duration )
				{
					$obj->max_sale_for_user = $obj->max_sale_for_user.(($obj->max_sale_for_user_duration == 'total') ? "" : "/@@".$obj->max_sale_for_user_duration."@@");
				}
			}

			$patterns[] = (array)$obj;
		}
	}

	$headinclude = "
		<link href='acpanel/templates/".$config['template']."/css/usershop.css' rel='stylesheet' type='text/css' />
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
					$('button[rel*=facebox]').facebox();
					$('#filters .chosen').chosen({allow_single_deselect:true}).live('change', function() {
						filterSelected = $(this).attr('name');
						var str = '', sSRV = $('[name=\"srv\"]').val(), sGROUP = $('[name=\"group\"]').val();
						if(sSRV != 0 && sSRV != undefined) str = str + '&srv=' + sSRV;
						if(sGROUP != 0 && sGROUP != undefined) str = str + '&group=' + sGROUP;
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
	
				var pg_size = ".$config['ub_pagesize'].";
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
						url:'acpanel/ajax.php?do=ajax_payment',
						data:({'go' : 19, 'srv' : jQuery('[name=\"srv\"]').val(),'group' : jQuery('[name=\"group\"]').val(),'cat' : ".$section_current['id'].", 'offset' : first, 'limit' : pg_size}),
						success:function(result) {
							jQuery('#ajaxContent').html(result);
						}
					});
				}
	
				return false;
			}
	
			function loadFirstPageNote()
			{
				var total = ".$total_items.";
				var pg_size = ".$config['ub_pagesize'].";
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
	
				var hh = newHash.toString().substring(1);
				var arrHH = hh.split('&');
				var arrSTR = '';
				var arrF = [0,0];
				var fError = null;
	
				for( var i=0,len=arrHH.length;i<len;i++ )
				{
					arrSTR = arrHH[i].split('=');
	
					switch(arrSTR[0])
					{
						case 'srv':
							arrF[0] = arrSTR[1];
							break;
						case 'group':
							arrF[1] = arrSTR[1];
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
						case 'group':
							t = arrF[1]
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
						url:'acpanel/ajax.php?do=ajax_payment',
						async:false,
						data:({'go' : 20, 'filters[]' : arrF, 'current_name' : name, 'current_val' : t}),
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
						url:'acpanel/ajax.php?do=ajax_payment',
						async:false,
						data:({'go' : 21, 'srv' : arrF[0], 'group' : arrF[1]}),
						success:function(result) {
							var pg_size = ".$config['ub_pagesize'].";
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
					items_per_page: ".$config['ub_pagesize']."
				});
			});
		</script>
	";
}

if(isset($patterns)) $smarty->assign("patterns", $patterns);
if(isset($error)) $smarty->assign("iserror", $error);
$smarty->assign("home",$config['acpanel'].'.php');

?>
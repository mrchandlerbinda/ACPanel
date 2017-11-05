<?php

if( !isset($_GET['username']) )
{
	$smarty->assign("action",$_SERVER['PHP_SELF']);

	$result_servers = $db->Query("SELECT a.id, a.hostname, a.address FROM `acp_servers` a LEFT JOIN `acp_access_mask_servers` b ON b.server_id = a.id WHERE a.opt_accounts = 1 GROUP BY a.id ORDER BY a.id", array(), $config['sql_debug']);
	if( is_array($result_servers) )
	{
		foreach( $result_servers as $obj )
		{
			$servers[$obj->id] = $obj->hostname;
		}

		$smarty->assign("array_servers", $servers);
	}

	$result_mask = $db->Query("SELECT mask_id, access_flags FROM `acp_access_mask` ORDER BY mask_id", array(), $config['sql_debug']);
	
	if( is_array($result_mask) )
	{
		foreach( $result_mask as $obj )
		{
			$masks[$obj->mask_id] = $obj->access_flags;
		}

		$smarty->assign("array_masks", $masks);
	}

	$headinclude = "
		<script type='text/javascript'>
			jQuery(document).ready(function($) {
				$('input:button').click(function() {
					var data = $('#forma-search').serialize();

					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_gamecp',
						data:data + '&go=25',
						success:function(result) {
							if( result.indexOf('id=\"success\"') + 1 )
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
	$go_page = "p_gamecp_search_result";
	$sqlconds = 'WHERE 1=1';
	$postout = array();
	date_default_timezone_set('UTC');

	if( is_array($_GET) )
	{
		foreach( $_GET as $var => $value )
		{
			switch($var)
			{
				case "type_all":

					if( $value != 'yes' && isset($_GET['flag']) )
					{
						$sqlconds .= " AND a.flag IN ('{flag}')";						
						$postout['flag'] = $_GET['flag'];
					}

					break;

				case "server_all":

					if( $value != 'yes' && isset($_GET['server_id']) )
					{
						$sqlconds .= " AND e.server_id IN ('{server_id}')";
						$postout['server_id'] = $_GET['server_id'];
					}

					break;

				case "startdate":

					$value = trim($value);
					if( $value )
					{
						$sqlconds .= " AND a.last_time >= '{startdate}'";
						$value = get_datetime(strtotime($value), false, true);
						$postout[$var] = $value;
					}
					break;

				case "enddate":

					$value = trim($value);
					if( $value )
					{
						$sqlconds .= " AND a.last_time <= '{enddate}'";
						$value = get_datetime(strtotime($value), false, true);
						$postout[$var] = $value;
					}
					break;

				case "player_nick":

					$value = trim($value);
					if( $value )
					{
						if( $config['charset'] != 'utf-8' )
						{
							$value = iconv('utf-8', $config['charset'], $value);
						}

						$sqlconds .= " AND a.".$var." LIKE '%{".$var."}%'";
						$postout[$var] = $value;
					}

					break;

				case "player_ip":
				case "steamid":

					$value = trim($value);
					if( $value )
					{
						$sqlconds .= " AND a.".$var." LIKE '%{".$var."}%'";
						$postout[$var] = $value;
					}

					break;

				case "mask":

					if( $value )
					{
						$sqlconds .= " AND d.mask_id = '{mask_id}'";
						$postout['mask_id'] = $_GET['mask'];
					}
					break;

				case "access_flags":

					$value = trim($value);
					if( $value )
					{
						$sqlconds .= " AND d.".$var." LIKE '%{".$var."}%'";
						$postout[$var] = $value;
					}
					break;

				case "access_expired":

					$value = trim($value);
					if( $value )
					{
						$sqlconds .= " AND c.".$var." < '{".$var."}' AND c.".$var." > 0";
						$value = get_datetime(strtotime($value), false, true);
						$postout[$var] = $value;
					}
					break;

				case "username":

					$value = trim($value);
					if( $value )
					{
						if( $config['charset'] != 'utf-8' )
						{
							$value = iconv('utf-8', $config['charset'], $value);
						}

						$sqlconds .= " AND b.".$var." LIKE '%{".$var."}%'";
						$postout[$var] = $value;
					}
					break;
			}
		}
	}

	$total_items = $db->Query("SELECT COUNT(a.userid) 
		FROM `acp_players` a LEFT JOIN `acp_users` b ON b.uid = a.userid 
		LEFT JOIN `acp_access_mask_players` c ON c.userid = a.userid 
		LEFT JOIN `acp_access_mask` d ON d.mask_id = c.mask_id 
		LEFT JOIN `acp_access_mask_servers` e ON e.mask_id = c.mask_id 
		".$sqlconds." 
		GROUP BY a.userid", $postout, $config['sql_debug']);

	$total_items = count($total_items);

	foreach( $all_categories as $key => $value )
	{
		if( $search_editcat_id = array_search("p_gamecp_accounts", $value) )
		{
			$postout['cat_edit'] = $key;
			break;
		}
	}

	$postout['go'] = 26;
	$postout['cat_current'] = $current_section_id;
	$postout = json_encode($postout);

	$headinclude = "
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
					url:'acpanel/ajax.php?do=ajax_gamecp&offset=' + first + '&limit=' + pg_size,
					data:".$postout.",
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
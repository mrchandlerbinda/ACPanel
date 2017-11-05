<?php

if( !isset($_GET['bid']) )
{
	// 0 - all bans
	// 1 - active bans
	// 2 - passed bans
	
	$select_low = $config['gb_bans_select'];
	$select_array =( !$select_low ) ? array(0,1,2) : array($select_low);
	
	if( !isset($_GET['t']) || !is_numeric($_GET['t']) || (!in_array($_GET['t'], $select_array) && !isset($_GET['search'])) || !in_array($_GET['t'], array(0,1,2)) )
	{
		header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&t='.$select_low);
		exit;
	}

	// select servers
	$result_servers = $db->Query("SELECT id, hostname, address FROM `acp_servers` WHERE active = 1", array(), $config['sql_debug']);
	if( is_array($result_servers) )
	{
		foreach( $result_servers as $obj )
		{
			$servers[$obj->id] = array('name' => $obj->hostname, 'ip' => $obj->address);
		}

		$smarty->assign("servers", $servers);
	}

	// select admins
	$result_admins = $db->Query("SELECT DISTINCT u.uid, u.username FROM `acp_users` u 
		LEFT JOIN `acp_bans` b ON b.admin_uid = u.uid 
		WHERE b.admin_uid > 0 ORDER BY u.username", array(), $config['sql_debug']);

	if( is_array($result_admins) )
	{
		foreach( $result_admins as $obj )
		{
			$admins[$obj->uid] = $obj->username;
		}

		$smarty->assign("admins", $admins);
	}

	$t = $_GET['t'];
	$search_type = $server = $admin = 0;
	$search_cond = $infomsg = $search = "";
	$bSearch_type = false;

	if( isset($_GET['search']) )
	{
		$search = trim($_GET['search']);
		if( $search )
		{	
			if( isset($_GET['search_type']) && in_array($_GET['search_type'], array(1,2)) )
			{
				$search_type = $_GET['search_type'];
			}

			$bSearch_type = $search_type;

			$search_cond = ((!$search_type) ? "player_nick" : (($search_type == 1) ? "player_ip" : "player_id"))." LIKE '%{search}%'";
		}

		if( isset($_GET['s']) && is_numeric($_GET['s']) && $_GET['s'] > 0 && isset($servers) )
		{
			if( array_key_exists($_GET['s'], $servers) )
			{
				$search_cond = (!$search_cond) ? "server_ip = '".$servers[$_GET['s']]['ip']."'" : $search_cond." AND server_ip = '".$servers[$_GET['s']]['ip']."'";
				$server = $servers[$_GET['s']]['ip'];
			}
		}

		if( isset($_GET['a']) && is_numeric($_GET['a']) && $_GET['a'] > 0 && isset($admins) )
		{
			if( array_key_exists($_GET['a'], $admins) )
			{
				$search_cond = (!$search_cond) ? "admin_uid = ".$_GET['a'] : $search_cond." AND admin_uid = ".$_GET['a'];
				$admin = $_GET['a'];
			}
		}
	}
	
	$arguments = array('search' => $search);
	if( $t == 1 )
	{
		$total_items = $db->Query("SELECT count(*) FROM `acp_bans` WHERE (".time()." < (ban_created+(ban_length*60)) OR ban_length = 0)".(($search_cond) ? " AND ".$search_cond : ""), $arguments, $config['sql_debug']);
	}
	else if( $t == 2 )
	{
		$total_items = $db->Query("SELECT count(*) FROM (
				(SELECT bid FROM `acp_bans_history`".(($search_cond) ? " WHERE ".$search_cond : "").")
				UNION ALL
				(SELECT bid FROM `acp_bans` WHERE ".time()." > (ban_created-1+(ban_length*60)) AND ban_length > 0".(($search_cond) ? " AND ".$search_cond : "").")
			) temp", $arguments, $config['sql_debug']);
	}
	else
	{
		$total_items = $db->Query("SELECT count(*) FROM (
				(SELECT bid FROM `acp_bans_history`".(($search_cond) ? " WHERE ".$search_cond : "").")
				UNION ALL
				(SELECT bid FROM `acp_bans`".(($search_cond) ? " WHERE ".$search_cond : "").")
			) temp", $arguments, $config['sql_debug']);
	}

	if( !$total_items )
	{
		if( $search_cond )
		{
			$infomsg = '<div class="message warning"><p>@@bans_not_found@@'.
				(($bSearch_type !== false) ? "<br />&raquo;&raquo;&raquo;&nbsp;@@bans_search_".((!$search_type) ? "nick" : (($search_type == 1) ? "ip" : "id"))."@@ '".htmlspecialchars($search, ENT_QUOTES)."'" : "").
				(($server) ? "<br />&raquo;&raquo;&raquo;&nbsp;@@bans_search_server@@ '".htmlspecialchars($servers[$_GET['s']]['name'], ENT_QUOTES)."'" : "").
				(($admin) ? "<br />&raquo;&raquo;&raquo;&nbsp;@@bans_search_admin@@ '".htmlspecialchars($admins[$admin], ENT_QUOTES)."'" : "").'</p></div>';
		}
		else
		{
			$error = '<div class="message warning"><p>@@empty_table@@</p></div>';
		}
	}
	elseif( $search_cond )
	{
		$infomsg = '<div class="message success"><p>@@bans_found@@ '.$total_items.
			(($bSearch_type !== false) ? "<br />&raquo;&raquo;&raquo;&nbsp;@@bans_search_".((!$search_type) ? "nick" : (($search_type == 1) ? "ip" : "id"))."@@ '".htmlspecialchars($search, ENT_QUOTES)."'" : "").
			(($server) ? "<br />&raquo;&raquo;&raquo;&nbsp;@@bans_search_server@@ '".htmlspecialchars($servers[$_GET['s']]['name'], ENT_QUOTES)."'" : "").
			(($admin) ? "<br />&raquo;&raquo;&raquo;&nbsp;@@bans_search_admin@@ '".htmlspecialchars($admins[$admin], ENT_QUOTES)."'" : "").'</p></div>';
	}

	if( !isset($error) && !$infomsg )
	{
		// ###############################################################################
		// CHECK GUEST IP
		// ###############################################################################
		$guest_ip = getRealIpAddr();
		$arguments = array('guest_ip'=>$guest_ip, 'time'=>time());
		$result_ip = $db->Query("SELECT bid FROM `acp_bans` WHERE (player_ip = '{guest_ip}' OR cookie_ip = '{guest_ip}') AND (ban_length = 0 OR {time} < (ban_created+(ban_length*60))) AND ban_type = 'SI' LIMIT 1", $arguments, $config['sql_debug']);
		if( $result_ip )
		{
			$guest_info = '@@your_ip@@ <span class="red">'.$guest_ip.'</span> @@ip_banned@@';
		}
		else
		{
			$guest_info = '@@your_ip@@ <span class="green">'.$guest_ip.'</span> @@ip_not_banned@@';
		}
	
		$subnet_info = '@@your_subnet_not_banned@@';
		$result_subnet = $db->Query("SELECT * FROM `acp_bans_subnets` WHERE approved = 1", array(), $config['sql_debug']);
		if( is_array($result_subnet) )
		{
			require_once(INCLUDE_PATH . 'functions.bans.php');
			foreach( $result_subnet as $obj )
			{
				$network = $obj->subipaddr."/".$obj->bitmask;
				if( net_match($network, $guest_ip) )
				{
					$subnet_info = '@@your_subnet@@'.( ($obj->comment) ? ' <span class="red">['.htmlspecialchars($obj->comment,ENT_QUOTES).']</span>' : '' ).' @@subnet_banned_post@@';
				}
			}
		}
		$infomsg = '<div class="message info"><p>'.$guest_info.' '.$subnet_info.'</p></div>';
		// ###############################################################################
	}

	if( isset($infomsg) ) $smarty->assign("infomsg", $infomsg);
	
	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>
			(function ($) {
				$(function () {
					$('.block_head a[rel*=facebox]').facebox()
				});
			})(jQuery);

			function decodeHtml(txt) {
				return txt.replace(/&amp;/g,'&').replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&#039;/g,'\'').replace(/&quot;/g,'\"');
			}
	
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

				var pg_size = ".$config['gb_view_per_page'].";
				var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
				var cat_current = ".$current_section_id.";
				var section_current = ".$cat_current['id'].";
				var status = '".$t."';
				var search = '".htmlspecialchars($search, ENT_QUOTES)."';
				var search_type = '".$search_type."';
	
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
					url:'acpanel/ajax.php?do=ajax_gamebans',
					data: {go: 19,status: status,search: decodeHtml(search),search_type: search_type,section_current: section_current,cat_current: cat_current,offset: first,limit: pg_size,server: '".$server."',admin: ".$admin."},
					success:function(result) {
						jQuery('#ajaxContent').html(result);
					}
				});
	
				return false;
			}
	
			function rePagination(diff) {
				var total = parseInt(jQuery('#Searchresult span').text()) + diff;
	
				if(total == 0)
				{
					jQuery('.tablesorter').append(jQuery('<tfoot>')
						.append(jQuery('<tr>').addClass('emptydata')
							.append(jQuery('<td>').attr('colspan', '6').html('@@empty_data@@'))
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
	
			jQuery(document).ready(function($) {
				$('#forma-select select').change(function () {
					window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&t=' + $('option:selected', this).val();
				});
	
				$('#Pagination').pagination( ".$total_items.", {
					num_edge_entries: 2,
					num_display_entries: 8,
					callback: pageselectCallback,
					items_per_page: ".$config['gb_view_per_page']."
				});
			});
		</script>
	";
	
	$smarty->assign("get_status", $t);
	$smarty->assign("select_low", $select_low);
	$smarty->assign("action",$_SERVER['PHP_SELF']);
	if(isset($error)) $smarty->assign("iserror", $error);
}
else
{
	$bid = trim($_GET['bid']);

	if( !is_numeric($bid) )
	{
		$error = "@@ban_not_found@@";
	}
	else
	{
		$arguments = array('bid'=>$bid);
		$result = $db->Query("SELECT * FROM (
				(SELECT bid, server_name, ban_created, ban_type, player_nick, player_ip, player_id, ban_reason, ban_length, admin_nick, admin_uid, unban_created, unban_admin_uid FROM `acp_bans_history` WHERE bid = {bid})
				UNION ALL
				(SELECT bid, server_name, ban_created, ban_type, player_nick, player_ip, player_id, ban_reason, ban_length, admin_nick, admin_uid, NULL, NULL FROM `acp_bans` WHERE bid = {bid})
			) temp LIMIT 1", $arguments, $config['sql_debug']);

		if( is_array($result) )
		{
			foreach( $result as $obj )
			{
				if( isset($obj->unban_admin_uid) && $obj->unban_admin_uid )
				{
					$obj->ban_remain = "@@ban_removed@@";
				}
				else if( $obj->ban_length && ($obj->ban_length*60 + $obj->ban_created - time()) <= 0 )
				{
					$obj->ban_remain = "@@ban_expired@@";
				}
				else
				{
					$obj->ban_remain = "";
				}

				$obj->ban_length = ($obj->ban_length == 0) ? "@@permanent@@" : compacttime($obj->ban_length*60, 'mmmm');
				$obj->ban_created = ($obj->ban_created > 0) ? get_datetime($obj->ban_created, $config['date_format']) : '-';
				$obj->ban_type = ($obj->ban_type == "N") ? "@@ban_by_nick@@" : (($obj->ban_type == "S") ? "@@ban_by_steam@@" : "@@ban_by_ip@@");

				$ban = (array)$obj;
			}
		}
		else
		{
			$error = "@@ban_not_found@@";
		}
	}

	header('Content-type: text/html; charset='.$config['charset']);

	$smarty->assign("hide_admins", $config['gb_display_admin']);
	if(isset($ban)) $smarty->assign("ban", $ban);
	if(isset($error)) $smarty->assign("iserror", $error);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_gamebans_public_players_view.tpl');

	exit;
}

?>
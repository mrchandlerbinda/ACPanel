<?php

if( !isset($_GET['id']) )
{
	if( !isset($_GET['s']) || !is_numeric($_GET['s']) || !in_array($_GET['s'], array('0','1','2')) ) {
		header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&s=0');
		exit;
	}
	
	$s = $_GET['s'];
	
	if( !isset($_GET['t']) || !is_numeric($_GET['t']) || !in_array($_GET['t'], array('0','1','2','3')) ) {
		header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&s='.$s.'&t=0');
		exit;
	}
	
	$t = $_GET['t'];

	foreach ($all_categories as $key => $value)
	{
		$search = array_search("p_gamecp_accounts_add", $value);
		if ($search)
		{
			$smarty->assign("cat_addacc_id", $key);
			break;
		}
	}
	
	$sqlconds = " WHERE 1 = 1";
	$arguments = array();
	
	if( $s )
	{
		$approved = ($s == 1) ? "yes" : "no";
		$arguments['approved'] = $approved;
		$sqlconds .= " AND approved = '{approved}'";
	}
	
	if( $t )
	{
		$arguments['flag'] = $t;
		$sqlconds .= " AND flag = '{flag}'";
	}
	
	$total_items = $db->Query("SELECT count(*) FROM `acp_players`".$sqlconds, $arguments, $config['sql_debug']);
	
	if(!$total_items) {
		$error = "@@empty_table@@";
	}
	
	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>
			(function ($) {
				$(function () {
					$('a[rel*=facebox]').facebox()
				});
			})(jQuery);

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

				var edit_id = ".$_GET['do'].";
				var cat_current = ".$current_section_id.";
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
					url:'acpanel/ajax.php?do=ajax_gamecp',
					data:'go=1&edit_id=' + edit_id + '&cat_current=' + cat_current + '&offset=' + first + '&limit=' + pg_size + '&s=".$s."&t=".$t."',
					success:function(result) {
						jQuery('#ajaxContent').html(result);
					}
				});
	
				return false;
			}
	
			function rePagination(diff) {
				var total = parseInt(jQuery('#Searchresult span').text()) + diff;
				var column = jQuery('.tablesorter thead th').length + 1;
	
				if(total == 0)
				{
					jQuery('.tablesorter').append(jQuery('<tfoot>')
						.append(jQuery('<tr>').addClass('emptydata')
							.append(jQuery('<td>').attr('colspan', column).html('@@empty_data@@'))
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
				$('#Pagination').pagination( ".$total_items.", {
					num_edge_entries: 2,
					num_display_entries: 8,
					callback: pageselectCallback,
					items_per_page: ".$config['pagesize']."
				});
	
				$('#forma-select select').change(function () {
					window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&s=' + $('option:selected', this).val() + '&t=".$_GET['t']."';
				});
	
				$('#forma-tpl select').change(function () {
					window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&s=".$_GET['s']."&t=' + $('option:selected', this).val();
				});
			});
		</script>
	";
	
	$smarty->assign("get_status",$s);
	$smarty->assign("get_type",$t);
}
else
{
	$userid = trim($_GET['id']);
	$cat_where = "";

	if( !is_numeric($userid) )
	{
		$error = "@@not_account@@";
	}
	else
	{
		$arguments = array('id'=>$userid);
		$result_user = $db->Query("SELECT 
			acp_players.userid AS userid,
			acp_players.flag AS flag,
			acp_players.player_nick AS player_nick,
			acp_players.player_ip AS player_ip,
			acp_players.steamid AS steamid,
			acp_players.timestamp AS timestamp,
			acp_players.last_time AS last_time,
			acp_players.approved AS approved,
			acp_players.online AS online,
			acp_players.points AS points,
			acp_users.mail AS mail,
			acp_users.icq AS icq,
			acp_users.hid AS hid,
			acp_users.reg_date AS reg_date,
			acp_users.last_visit AS last_visit,
			acp_users.ipaddress AS ipaddress,
			acp_users.username AS username,
			acp_users.avatar AS avatar,
			(SELECT COUNT(u.hid) FROM `acp_users` u WHERE u.hid = acp_users.hid AND u.hid IS NOT NULL GROUP BY u.hid) AS cnt_hid,
			acp_usergroups.usergroupname AS usergroupname, 
			CAST(GROUP_CONCAT(acp_access_mask_players.mask_id) AS char) AS mask, 
 			CAST(GROUP_CONCAT(acp_access_mask_players.access_expired) AS char) AS mask_expired 
			FROM `acp_players` LEFT JOIN `acp_users` ON acp_users.uid = acp_players.userid 
			LEFT JOIN `acp_usergroups` ON acp_users.usergroupid = acp_usergroups.usergroupid 
			LEFT JOIN `acp_access_mask_players` ON acp_access_mask_players.userid = acp_players.userid
			WHERE acp_players.userid = '{id}' GROUP BY acp_players.userid", $arguments, $config['sql_debug']);

		if( is_array($result_user) )
		{
			foreach( $result_user as $obj )
			{
				$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, 'd-m-Y, H:i') : '-';
				$obj->last_time = ($obj->last_time > 0) ? get_datetime($obj->last_time, 'd-m-Y, H:i') : '-';
				$obj->reg_date = ($obj->reg_date > 0) ? get_datetime($obj->reg_date, 'd-m-Y, H:i') : '-';
				$obj->last_visit = ($obj->last_visit > 0) ? get_datetime($obj->last_visit, 'd-m-Y, H:i') : '-';

				$obj->mask = (strlen($obj->mask) > 0) ? explode(',', $obj->mask) : array();
				$obj->mask_expired = (strlen($obj->mask_expired) > 0) ? explode(',', $obj->mask_expired) : array();

				function getCustomTimestamp($n)
				{
					return (!$n) ? "" : get_datetime($n, 'd-m-Y, H:i');
				}

				$obj->mask_expired = array_map("getCustomTimestamp", $obj->mask_expired);

				switch($ext_auth_type)
				{
					case "xf":

						$xfUser = $xf->getUserInfo($obj->userid);
						$obj->avatar = ($obj->avatar) ? $xf->getAvatarFilePath("s", $obj->userid).'?'.$obj->avatar : $config['xfAuth']['forumUrl'].'styles/'.$config["template"].'/xenforo/avatars/avatar_'.( ($xfUser["gender"]) ? $xfUser["gender"]."_" : "" ).'s.png';
						$obj->avatar_date = $xfUser['avatar_date'];
						break;
		
					default:
	
						$obj->avatar_date = (strlen($obj->avatar)) ? 1 : 0;	
						$obj->avatar = ($obj->avatar) ? 'acpanel/images/avatars/s/'.$obj->avatar : 'acpanel/images/noavatar_s.gif';
						break;
				}

				$array_user = (array)$obj;

				$result_mask = $db->Query("SELECT a.mask_id, a.access_flags, IF(MIN(b.server_id) > 0, COUNT(b.server_id), 0) AS servers FROM `acp_access_mask` a 
					LEFT JOIN `acp_access_mask_servers` b ON a.mask_id = b.mask_id GROUP BY a.mask_id", array(), $config['sql_debug']);

				if( is_array($result_mask) )
				{
					foreach( $result_mask as $obj_mask )
					{
						if( $obj_mask->servers == 0 ) $obj_mask->servers = '@@ga_all_servers@@';
						$array_mask[$obj_mask->mask_id] = array('flags'=>$obj_mask->access_flags, 'servers'=>$obj_mask->servers);
					}

					$smarty->assign("array_masks",$array_mask);
				}
			}

			$smarty->assign("account", $array_user);

			$productID = getProduct("gameBans");
			if( !empty($productID) )
			{
				$smarty->assign("acp_bans", true);
				$cat_where .= " OR link = 'p_gamebans_search'";
			}
		}
		else
		{
			$error = "@@not_account@@";
		}
	}

	$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_users' OR link = 'p_users_search'".$cat_where, array(), $config['sql_debug']);
	
	if( is_array($result_cats) )
	{
		foreach ($result_cats as $obj)
		{
			if( $obj->link == 'p_users' )
			{
				$smarty->assign("cat_users", $obj->sectionid);
				$smarty->assign("cat_user_edit", $obj->categoryid);
			}
			elseif( $obj->link == 'p_users_search' )
			{
				$smarty->assign("cat_user_search", $obj->categoryid);
			}
			else
				$smarty->assign("cat_bans_search", array('cat' => $obj->sectionid, 'do' => $obj->categoryid));
		}
	}

	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".$_GET['do'];

	$smarty->assign("action_uri", $action_uri);
	$smarty->assign("head_title","@@edit_account@@");
	$def_time = (!$config['default_access_time']) ? "" :  get_datetime((time() + ($config['default_access_time']*3600)), 'd-m-Y, H:i');
	$smarty->assign("default_mask",array('mask' => $config['default_access'], 'expired' => $def_time));

	$go_page = "p_gamecp_accounts_edit";
	$cat_current['title'] = "@@edit_account@@";
}

if(isset($error)) $smarty->assign("iserror",$error);

?>
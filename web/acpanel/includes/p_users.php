<?php

$result_group = $db->Query("SELECT usergroupid, usergroupname FROM `acp_usergroups` WHERE usergroupid IS NOT NULL", array(), $config['sql_debug']);

if( is_array($result_group) )
{
	foreach ($result_group as $obj)
	{
		$array_groups[] = (array)$obj;
	}
}

if( !isset($_GET['t']) || !is_numeric($_GET['t']) || (!in_multi_array($_GET['t'], $array_groups) &&  ($_GET['t'] != '0')) )
{	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&t=0');
	exit;
}

$t = $_GET['t'];

if(!isset($_GET['id']))
{	foreach ($all_categories as $key => $value)
	{
		$search_addcat_id = array_search("p_users_add", $value);
		if ($search_addcat_id)
		{
			$smarty->assign("cat_addcat_id", $key);
			break;
		}
	}

	$total_items = $db->Query("SELECT count(*) FROM `acp_users`".(($t) ? ' WHERE usergroupid = '.$t : ''), array(), $config['sql_debug']);

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

				var pg_size = ".$config['pagesize'].";
				var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
				var section_current = ".$cat_current['id'].";
				var cat_current = ".$current_section_id.";
				var group = '".$t."';

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
					url:'acpanel/ajax.php?do=ajax_users',
					data:'go=1&group=' + group + '&section_current=' + section_current + '&cat_current=' + cat_current + '&offset=' + first + '&limit=' + pg_size,
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
					items_per_page: ".$config['pagesize']."
				});
			});
		</script>
	";
}
else
{
	$userid = trim($_GET['id']);
	if(!is_numeric($userid))
	{		$error = "@@not_user@@";
	}
	else
	{		$result_tz = $db->Query("SELECT type, options FROM `acp_config` WHERE varname = 'timezone' LIMIT 1", array(), $config['sql_debug']);
		if (is_array($result_tz))
		{
			foreach ($result_tz as $obj)
			{				$box = explode("\n", $obj->options);
				foreach($box as $b) {
					$box_value = explode("|", $b);
					$array_tz[$box_value[0]] = $box_value[1];
				}
			}

			$smarty->assign("array_tz", $array_tz);
		}

		$arguments = array('id'=>$userid);
		$result_user = $db->Query("SELECT * FROM `acp_users` WHERE uid = '{id}'", $arguments, $config['sql_debug']);
		if (is_array($result_user))
		{
			foreach ($result_user as $obj)
			{				$obj->reg_date = ($obj->reg_date > 0) ? get_datetime($obj->reg_date, 'd-m-Y, H:i') : '';
				$obj->last_visit = ($obj->last_visit > 0) ? get_datetime($obj->last_visit, 'd-m-Y, H:i') : '';

				switch($ext_auth_type)
				{
					case "xf":

						$xfUser = $xf->getUserInfo($obj->uid);
						$obj->avatar = ($obj->avatar) ? $xf->getAvatarFilePath("m", $obj->uid).'?'.$obj->avatar : $config['xfAuth']['forumUrl'].'styles/'.$config["template"].'/xenforo/avatars/avatar_'.( ($xfUser["gender"]) ? $xfUser["gender"]."_" : "" ).'m.png';
						$obj->avatar_date = $xfUser['avatar_date'];
						break;
		
					default:
	
						$obj->avatar_date = (strlen($obj->avatar)) ? 1 : 0;	
						$obj->avatar = ($obj->avatar) ? 'acpanel/images/avatars/m/'.$obj->avatar : 'acpanel/images/noavatar_m.gif';
						break;
				}

				$array_user = (array)$obj;
			}

			$smarty->assign("array_user", $array_user);

			$product_Accounts = getProduct("gameAccounts");
			if( !empty($product_Accounts) )
			{
				$result_account = $db->Query("SELECT * FROM `acp_players` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
				if (is_array($result_account))
				{
					foreach ($result_account as $obj)
					{
						$obj->last_time = ($obj->last_time > 0) ? get_datetime($obj->last_time, 'd-m-Y, H:i') : '-';
						$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, 'd-m-Y, H:i') : '-';
						$smarty->assign("account", (array)$obj);
					}
				}

				$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_gamecp_accounts_add' OR link = 'p_gamecp_accounts'", array(), $config['sql_debug']);
				
				if( is_array($result_cats) )
				{
					foreach ($result_cats as $obj)
					{
						if( $obj->link == 'p_gamecp_accounts' )
						{
							$smarty->assign("cat_accounts", $obj->sectionid);
							$smarty->assign("cat_account_edit", $obj->categoryid);
						}
						else
						{
							$smarty->assign("cat_account_add", $obj->categoryid);
						}
					}
				}
			}
			else
			{
				$smarty->assign("ga_false", true);
			}
		}
		else
		{
			$error = "@@not_user@@";
		}
	}

	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".$_GET['do']."&t=".$t;

	$smarty->assign("action_uri", $action_uri);
	$smarty->assign("head_title","@@edit_user@@");

	$go_page = "p_users_edit";
	$cat_current['title'] = "@@edit_user@@";

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
		</script>
	";
}

$smarty->assign("array_groups", $array_groups);
$smarty->assign("get_group",$t);
if(isset($error)) $smarty->assign("iserror",$error);

?>
<?php

if( !isset($_GET['user_group']) && !isset($_GET['user_hid']) && !isset($_GET['user_mail']) && !isset($_GET['user_icq']) && !isset($_GET['reg_ip']) && !isset($_GET['user_login']) )
{
	$smarty->assign("action",$_SERVER['PHP_SELF']);

	$result_group = $db->Query("SELECT usergroupid, usergroupname FROM `acp_usergroups` WHERE usergroupid IS NOT NULL", array(), $config['sql_debug']);

	if( is_array($result_group) )
	{
		foreach ($result_group as $obj)
		{
			$array_groups[] = (array)$obj;
		}
	}

	if(isset($array_groups)) $smarty->assign("groups",$array_groups);

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

			jQuery(document).ready(function($) {
				$('input:button').click(function() {
					var data = $('#forma-search').serialize();

					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_users',
						data:data + '&go=7',
						success:function(result) {
							if( result.indexOf('id=\"success\"') + 1)
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
	foreach ($all_categories as $key => $value)
	{
		$search_editcat_id = array_search("p_users", $value);
		if ($search_editcat_id)
		{
		    $search_editcat_id = $key;
			break;
		}
	}

	$go_page = "p_users_search_result";

	$group = (isset($_GET['user_group'])) ? $_GET['user_group'] : "all";
	$reg_date_begin = (isset($_GET['reg_date_begin'])) ? trim($_GET['reg_date_begin']) : "";
	$reg_date_end = (isset($_GET['reg_date_end'])) ? trim($_GET['reg_date_end']) : "";
	$last_date_begin = (isset($_GET['last_date_begin'])) ? trim($_GET['last_date_begin']) : "";
	$last_date_end = (isset($_GET['last_date_end'])) ? trim($_GET['last_date_end']) : "";
	$user_login = (isset($_GET['user_login'])) ? trim($_GET['user_login']) : "";
	$reg_ip = (isset($_GET['reg_ip'])) ? trim($_GET['reg_ip']) : "";
	$user_hid = (isset($_GET['user_hid'])) ? trim($_GET['user_hid']) : "";
	$user_mail = (isset($_GET['user_mail'])) ? trim($_GET['user_mail']) : "";
	$user_icq = (isset($_GET['user_icq'])) ? trim($_GET['user_icq']) : "";

	$sqlconds = 'WHERE 1=1';
	$postout = '';

	if ($group != "all") { $postout .= "&user_group=".$group; $sqlconds .= " AND usergroupid = '{group}'"; }
	if ($user_login) { $postout .= "&user_login=".$user_login; $sqlconds .= " AND username LIKE '%{user_login}%'"; }
	if ($user_hid) { $postout .= "&user_hid=".$user_hid; $sqlconds .= " AND hid LIKE '%{user_hid}%'"; }
	if ($user_mail) { $postout .= "&user_mail=".$user_mail; $sqlconds .= " AND mail LIKE '%{user_mail}%'"; }
	if ($user_icq) { $postout .= "&user_icq=".$user_icq; $sqlconds .= " AND icq LIKE '%{user_icq}%'"; }
	if ($reg_ip) { $postout .= "&reg_ip=".$reg_ip; $sqlconds .= " AND ipaddress LIKE '%{reg_ip}%'"; }
	if ($reg_date_begin) { $postout .= "&reg_date_begin=".$reg_date_begin; $sqlconds .= " AND reg_date >= '{reg_date_begin}'"; }
	if ($reg_date_end) { $postout .= "&reg_date_end=".$reg_date_end; $sqlconds .= " AND reg_date <= '{reg_date_end}'"; }
	if ($last_date_begin) { $postout .= "&last_date_begin=".$last_date_begin; $sqlconds .= " AND last_visit >= '{last_date_begin}'"; }
	if ($last_date_end) { $postout .= "&last_date_end=".$last_date_end; $sqlconds .= " AND last_visit <= '{last_date_end}'"; }

	date_default_timezone_set('UTC');
	$arguments = array('group'=>$group,'user_login'=>$user_login,'user_hid'=>$user_hid,'user_mail'=>$user_mail,'user_icq'=>$user_icq,'reg_ip'=>$reg_ip,'reg_date_begin'=>get_datetime(strtotime($reg_date_begin), false, true),'reg_date_end'=>get_datetime(strtotime($reg_date_end), false, true),'last_date_begin'=>get_datetime(strtotime($last_date_begin), false, true),'last_date_end'=>get_datetime(strtotime($last_date_end), false, true));
	$total_items = $db->Query("SELECT count(*) FROM `acp_users` ".$sqlconds, $arguments, $config['sql_debug']);

	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>

			function pageselectCallback(page_id, total, jq) {
				var pg_size = ".$config['pagesize'].";
				var edit_cat = ".$search_editcat_id.";
				var section_current = ".$current_section_id.";
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
					url:'acpanel/ajax.php?do=ajax_users',
					data:'go=6&section_current=' + section_current + '&edit_cat=' + edit_cat + '&offset=' + first + '&limit=' + pg_size + '".$postout."',
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
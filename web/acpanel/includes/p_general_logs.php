<?php

if (!isset($_GET['action']))
{
	$smarty->assign("action",$_SERVER['PHP_SELF']);

	$result = $db->Query("SELECT DISTINCT action FROM `acp_logs` ORDER BY `action`", array(), $config['sql_debug']);

	if(is_array($result))
	{
		foreach ($result as $obj)
		{
			$actions[] = $obj->action;
		}
	}
	else
	{		if(is_null($result))
		{
			$error = '@@tbl_empty@@';
		}
		else
		{			$actions[] = $result;
		}
	}

	if(!isset($error))
	{
		$smarty->assign("actions",$actions);

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
							url:'acpanel/ajax.php?do=ajax_general_logs',
							data:data + '&go=2',
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
}
else
{
	$go_page = "p_general_logs_result";

	$action = $_GET['action'];
	$startdate = trim($_GET['begindate']);
	$enddate = trim($_GET['enddate']);
	$user_login = trim($_GET['user_login']);
	$user_ip = trim($_GET['user_ip']);

	$sqlconds = 'WHERE 1=1 ';
	$postout = '';

	if ($action != "all") { $postout .= "&action=".$action; $sqlconds .= " AND action = '{action}' "; }
	if ($user_login) { $postout .= "&user_login=".$user_login; $sqlconds .= " AND username LIKE '%{user_login}%' "; }
	if ($user_ip) { $postout .= "&user_ip=".$user_ip; $sqlconds .= " AND ip LIKE '%{user_ip}%' "; }
	if ($startdate) { $postout .= "&startdate=".$startdate; $sqlconds .= " AND timestamp >= '{startdate}' "; }
	if ($enddate) { $postout .= "&enddate=".$enddate; $sqlconds .= " AND timestamp <= '{enddate}' "; }

	date_default_timezone_set('UTC');
	$arguments = array('action'=>$action,'user_login'=>$user_login,'user_ip'=>$user_ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true));
	$total_items = $db->Query("SELECT count(*) FROM `acp_logs` ".$sqlconds, $arguments, $config['sql_debug']);

	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>

			function pageselectCallback(page_id, total, jq) {
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
					url:'acpanel/ajax.php?do=ajax_general_logs',
					data:'go=1&offset=' + first + '&limit=' + pg_size + '".$postout."',
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
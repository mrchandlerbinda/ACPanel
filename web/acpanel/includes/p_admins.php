<?php

$array_servers = array();

$admin_groups = strlen($config['admin_groups']) ? explode(',', $config['admin_groups']) : array();

if ( empty($admin_groups) )
{
	$error = "@@not_groups_in_cfg@@";
}
else
{
	$productID = getProduct("gameAccounts");
	if( !empty($productID) )
	{
		$result_servers = $db->Query("SELECT id, hostname FROM `acp_servers` WHERE active = 1 AND opt_accounts = 1", array(), $config['sql_debug']);
		if( is_array($result_servers) )
		{
			foreach( $result_servers as $obj )
			{
				$array_servers[$obj->id] = $obj->hostname;
			}

			$smarty->assign("array_servers",$array_servers);
		}
		
		if( !isset($_GET['s']) || !is_numeric($_GET['s']) || (!in_array($_GET['s'], array_keys($array_servers)) && $_GET['s'] != '0') )
		{
			header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&s=0');
			exit;
		}
		
		$t = $_GET['s'];

		$smarty->assign("get_srv",$t);
	}

	$arguments = array('usergroups'=>$admin_groups);
	$sqlcond_join = "";
	$sqlcond_where = "";

	if( isset($t) )
	{
		$sqlcond_join = " LEFT JOIN `acp_access_mask_players` m_p ON m_p.userid = u.uid LEFT JOIN `acp_access_mask` m ON m.mask_id = m_p.mask_id";
		if( $t > 0 )
		{
			$sqlcond_join .= " LEFT JOIN `acp_access_mask_servers` m_s ON m_s.mask_id = m_p.mask_id";
			$sqlcond_where .= " AND (m_s.server_id = {srv} OR m_s.server_id = 0)";
			$arguments['srv'] = $t;
		}
		$arguments['time'] = time();

		if( $config['ga_admin_flag'] )
		{
			$sqlcond_where .= " AND (m_p.access_expired > {time} OR m_p.access_expired = 0)";
			$sqlcond_where .= " AND INSTR(m.access_flags,'{admin_flag}') > 0";
			$arguments['admin_flag'] = $config['ga_admin_flag'];
		}
	}
	else
	{
		$t = 0;
	}
	$total_admins = $db->Query("SELECT DISTINCT u.uid FROM `acp_users` u".$sqlcond_join." WHERE u.usergroupid IN ('{usergroups}')".$sqlcond_where, $arguments, $config['sql_debug']);

	if( is_array($total_admins) )
		$total_admins = count($total_admins);
	elseif( !is_null($total_admins) )
		$total_admins = 1;
	else
		$total_admins = 0;

	if( !$total_admins )
		$error = "@@empty_table@@";

	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
		<script type='text/javascript'>

			function pageselectCallback(page_id, total, jq) {
				var pg_size = ".$config['pagesize'].";
				var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
				var t = '".$t."';

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
					url:'acpanel/ajax.php?do=ajax_homepage',
					data:({'t' : t,'go' : 3,'offset' : first,'limit' : pg_size}),
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
							.append(jQuery('<td>').attr('colspan', '3').html('@@empty_data@@'))
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
				$('#Pagination').pagination( ".$total_admins.", {
					num_edge_entries: 2,
					num_display_entries: 8,
					callback: pageselectCallback,
					items_per_page: ".$config['pagesize']."
				});

				$('#forma-select select').change(function () {
					window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&s=' + $('option:selected', this).val();
				});
			});
		</script>
	";
}

if(isset($error)) $smarty->assign("iserror",$error);

?>
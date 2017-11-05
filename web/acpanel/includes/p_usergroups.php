<?php

if( !isset($_GET['id']) )
{
	$search_addcat = false;
	$cat_users_id = false;
	foreach ($all_categories as $key => $value)
	{
		$search_addcat_id = array_search("p_usergroups_add", $value);
		$search_users_id = array_search("p_users", $value);
		if ($search_addcat_id)
		{
			$smarty->assign("cat_addcat_id", $key);
			$search_addcat = true;
		}
		elseif ($search_users_id)
		{
			$cat_users_id = $key;
		}
	
		if ($search_addcat && $cat_users_id)
		{
			break;
		}
	}

	$total_items = $db->Query("SELECT count(*) FROM `acp_usergroups`", array(), $config['sql_debug']);

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
				var cat_users_id = ".$cat_users_id.";

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
					url:'acpanel/ajax.php?do=ajax_usergroups',
					data:'go=1&section_current=' + section_current + '&cat_current=' + cat_current + '&section_users=' + cat_users_id + '&offset=' + first + '&limit=' + pg_size,
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
	$usergroupid = trim($_GET['id']);
	if( !is_numeric($usergroupid) )
	{		$error = "@@not_group@@";
	}
	else
	{		$arguments = array('id'=>$usergroupid);
		$result_group = $db->Query("SELECT * FROM `acp_usergroups` WHERE usergroupid = '{id}'", $arguments, $config['sql_debug']);
		if( is_array($result_group) )
		{
			foreach( $result_group as $obj )
			{
				$array_group = (array)$obj;
			}
			$group_general = array('usergroupid'=>$array_group['usergroupid'], 'usergroupname'=>$array_group['usergroupname']);

			$result_perm = $db->Query("SELECT * FROM `acp_usergroups_permissions` a LEFT JOIN 
				(SELECT usergroupid, action, bitmask FROM `acp_permissons_action` WHERE usergroupid = ".$usergroupid." ) b ON (b.action = a.id) 
				ORDER BY a.perm_sort", array(), $config['sql_debug']);

			if( is_array($result_perm) )
			{
				require_once(INCLUDE_PATH . 'class.Permissions.php');
				$permClass = new Permissions($db);

				foreach( $result_perm as $objperm )
				{
					if( !$objperm->varname )
					{
						$group_edit[$objperm->section]['desc'] = $objperm->description;
					}
					else
					{
						switch($objperm->type)
						{
							case "bitmask":
								$actionRules = array("read" => false, "add" => false, "write" => false, "delete" => false);
								if( !is_null($objperm->action) )
									$actionRules = $permClass->toPermission($objperm->bitmask);
								$general_editable = "";
								foreach( $actionRules as $a => $b )
								{
									$general_editable .= "
										<span class='input-perm'><input class='checkbox' type='checkbox' name='{$objperm->varname}[]' value='{$a}' ".(($b) ? "checked=\"checked\"" : "" )." /> @@perm_{$a}@@</span> 
									";
								}
								break;
							case "textarea":
								$general_editable = "
									<textarea name='{$objperm->varname}' rows='5' cols='30' />".$array_group[$objperm->varname]."</textarea>
								";
								break;
							case "checkbox":
								$box = explode("\n", $objperm->options);
								$general_editable = "";
								foreach($box as $b)
								{
									$box_value = explode("|", $b);
									$var_array = (is_array($array_group[$objperm->varname])) ? $array_group[$objperm->varname] : explode(",", $array_group[$objperm->varname]);
									$general_editable .= "
										<input class='checkbox' type='checkbox' name='{$objperm->varname}[]' value='{$box_value[0]}' ".((in_array($box_value[0], $var_array)) ? "checked=\"checked\"" : "" )." /> ".$box_value[1]."<br />
									";
								}
								break;
							case "select":
								$general_editable = "
									<select name='{$objperm->varname}".( ($objperm->verifycodes == 'multiple') ? "[]" : "" )."'".( ($objperm->verifycodes == 'multiple') ? " multiple='multiple'" : " class='styled'" ).">
								";
								if( $objperm->verifycodes == 'multiple' )
								{
									$box_value = explode("|", $objperm->options);
									$var_array = explode(",", $array_group[$objperm->varname]);
									if( $box_value[0] == 'acp_category' )
									{										$result_node = $db->Query("SELECT * FROM `acp_category` WHERE catlevel = 0 ORDER BY display_order", array(), $config['sql_debug']);

										if (is_array($result_node))
										{
											foreach ($result_node as $objnode)
											{
												$arguments = array('catleft'=>$objnode->catleft,'catright'=>$objnode->catright,'category'=>$objnode->categoryid);
												$res = $db->Query("SELECT * FROM `acp_category` WHERE catleft >= {catleft} AND catright <= {catright} AND sectionid = '{category}' OR categoryid = '{category}' ORDER BY catleft", $arguments, $config['sql_debug']);

												if (is_array($res))
												{
													foreach ($res as $obj_new)
													{														$general_editable .= "
																<option value='{$obj_new->categoryid}'".((in_array($obj_new->categoryid, $var_array)) ? " selected='selected'" : "" ).">".(($obj_new->catlevel > 0) ? '&brvbar; '.str_repeat('- - - ', $obj_new->catlevel).$obj_new->title : $obj_new->title)."</option>
														";
													}
												}
											}
										}									}
									else
									{										$arguments = array('table'=>$box_value[0],'field1'=>$box_value[1],'field2'=>$box_value[2],'field3'=>$box_value[3]);										$res = $db->Query("SELECT {field1} AS one_field, {field2} AS two_field FROM `{table}`", $arguments, $config['sql_debug']);
										if( is_array($res) )
										{
											foreach ($res as $obj_new)
											{
												$general_editable .= "
														<option value='{$obj_new->one_field}'".((in_array($obj_new->one_field, $var_array)) ? " selected='selected'" : "" ).">".$obj_new->two_field."</option>
												";
											}
										}
									}
								}
								else
								{
									$box = explode("\n", $objperm->options);
									foreach($box as $b) {
										$box_value = explode("|", $b);
										$general_editable .= "
												<option value='{$box_value[0]}' ".(($array_group[$objperm->varname] == $box_value[0]) ? "selected" : "" ).">".$box_value[1]."</option>
										";
									}
								}

								$general_editable .= "
									</select>
								";
								break;
							case "boolean":
								$general_editable = "
									<input class='radio' type='radio' name='{$objperm->varname}' value='yes' ".(($array_group[$objperm->varname] == 'yes') ? "checked=\"checked\"" : "" )." /> @@yes@@&nbsp;
									<input class='radio' type='radio' name='{$objperm->varname}' value='no' ".(($array_group[$objperm->varname] == 'no') ? "checked=\"checked\"" : "" )." /> @@no@@
								";
								break;
							default:
								$general_editable = "
									<input class='text small' type='text' name='{$objperm->varname}' value='{$array_group[$objperm->varname]}' size='30'>
								";
						}

						$group_edit[$objperm->section]['options'][] = array('description' => $objperm->description, 'content' => $general_editable);
					}
				}
			}

			$smarty->assign("group_edit", $group_edit);
			$smarty->assign("array_group", $group_general);
		}
		else
		{
			$error = "@@not_group@@";
		}
	}

	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".$_GET['do'];

	$smarty->assign("action_uri", $action_uri);
	$smarty->assign("head_title","@@edit_group@@");

	$go_page = "p_usergroups_edit";
	$cat_current['title'] = "@@edit_group@@";
}

if(isset($error)) $smarty->assign("iserror",$error);

?>
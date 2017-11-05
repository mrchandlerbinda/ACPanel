<?php

if( isset($_GET['server']) && is_numeric($_GET['server']) )
{
	$id = $_GET['server'];
	$arguments = array('id'=>$id);	$result = $db->Query("SELECT * FROM `acp_servers` WHERE id = '{id}' LIMIT 1", $arguments, $config['sql_debug']);

	if (is_array($result))
	{
		foreach ($result as $obj)
		{
			$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, 'd-m-Y, H:i') : '';
			$obj->vip = ($obj->vip > 0) ? get_datetime($obj->vip, 'd-m-Y, H:i') : '';        		$server_fields = (array)$obj;
		}

		$username = $db->Query("SELECT username FROM `acp_users` WHERE uid = ".$server_fields['userid']." LIMIT 1", array(), $config['sql_debug']);
		if( $username )
		{
			$server_fields = array_merge($server_fields, array('username' => $username));
		}

		$result_options = $db->Query("SELECT * FROM `acp_servers_options` WHERE id <> 0 ORDER BY sortnum", array(), $config['sql_debug']);
		if (is_array($result_options))
		{
			foreach ($result_options as $objperm)
			{
				switch($objperm->type)
				{
					case "textarea":
						$general_editable = "
							<textarea name='{$objperm->varname}' rows='5' cols='30' />".$server_fields[$objperm->varname]."</textarea>
						";
						break;
					case "checkbox":
						$box = explode("\n", $objperm->options);
						unset($general_editable);
						foreach($box as $b) {
							$box_value = explode("|", $b);
							$var_array = (is_array($server_fields[$objperm->varname])) ? $server_fields[$objperm->varname] : explode(",", $server_fields[$objperm->varname]);
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
							$var_array = explode(",", $server_fields[$objperm->varname]);
							if( $box_value[0] == 'acp_category' )
							{
								$result_node = $db->Query("SELECT * FROM `acp_category` WHERE catlevel = 0 ORDER BY display_order", array(), $config['sql_debug']);

								if (is_array($result_node))
								{
									foreach ($result_node as $objnode)
									{
										$arguments = array('catleft'=>$objnode->catleft,'catright'=>$objnode->catright,'category'=>$objnode->categoryid);
										$res = $db->Query("SELECT * FROM `acp_category` WHERE catleft >= {catleft} AND catright <= {catright} AND sectionid = '{category}' OR categoryid = '{category}' ORDER BY catleft", $arguments, $config['sql_debug']);

										if (is_array($res))
										{
											foreach ($res as $obj_new)
											{
												$general_editable .= "
														<option value='{$obj_new->categoryid}'".((in_array($obj_new->categoryid, $var_array)) ? " selected='selected'" : "" ).">".(($obj_new->catlevel > 0) ? '&brvbar; '.str_repeat('- - - ', $obj_new->catlevel).$obj_new->title : $obj_new->title)."</option>
												";
											}
										}
									}
								}
							}
							else
							{
								$arguments = array('table'=>$box_value[0],'field1'=>$box_value[1],'field2'=>$box_value[2],'field3'=>$box_value[3]);
								$res = $db->Query("SELECT {field1} AS one_field, {field2} AS two_field FROM `{table}`", $arguments, $config['sql_debug']);
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
						elseif( $objperm->verifycodes == 'select' )
						{
							$box_value = explode("|", $objperm->options);
							$var_array = explode(",", $server_fields[$objperm->varname]);
							$arguments = array('table'=>$box_value[0],'field1'=>$box_value[1],'field2'=>$box_value[2]);

							$res = $db->Query("SELECT {field1} AS one_field, {field2} AS two_field FROM `{table}`", $arguments, $config['sql_debug']);
							if( is_array($res) )
							{
								foreach( $res as $obj_new )
								{
									$general_editable .= "
											<option value='{$obj_new->one_field}'".((in_array($obj_new->one_field, $var_array)) ? " selected='selected'" : "" ).">".$obj_new->two_field."</option>
									";
								}
							}
						}
						else
						{
							$box = explode("\n", $objperm->options);
							foreach($box as $b) {
								$box_value = explode("|", $b);
								$general_editable .= "
										<option value='{$box_value[0]}' ".(($server_fields[$objperm->varname] == $box_value[0]) ? "selected" : "" ).">".$box_value[1]."</option>
								";
							}
						}

						$general_editable .= "
							</select>
						";
						break;
					case "boolean":
						$general_editable = "
							<input class='radio' type='radio' name='{$objperm->varname}' value='1' ".(($server_fields[$objperm->varname]) ? "checked=\"checked\"" : "" )." /> @@yes@@&nbsp;
							<input class='radio' type='radio' name='{$objperm->varname}' value='0' ".((!$server_fields[$objperm->varname]) ? "checked=\"checked\"" : "" )." /> @@no@@
						";
						break;
					default:
						$general_editable = "
							<input class='text small' type='text' name='{$objperm->varname}' value='{$server_fields[$objperm->varname]}' size='30'>
						";
				}

				$server_options[] = array('description' => $objperm->label, 'content' => $general_editable);
			}
		}
	}
	else
	{		$error = '@@not_valid_server@@';
	}

	$headinclude = "
		<link href='acpanel/templates/".$config['template']."/css/date_input.css' rel='stylesheet' type='text/css' />
		<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.ajaxupload.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.date_input.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.stickysidebar.js'></script>
		<script type='text/javascript'>
			(function ($) {
				$(function () {
					$('a[rel*=facebox]').facebox()
	
					// Date picker
					$('input.date_picker').date_input();
				});
			})(jQuery);

			function useSticky()
			{
				jQuery('.block.middle.left').stickySidebar({speed: 400, padding: 0, constrain: true});
			}

			function refreshServerInfo(address, tp) {				jQuery.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_servers_control',
					data:'go=6&id=".$id."&address=' + address + '&type=' + tp,
					success:function(result) {						setTimeout(function() {
							jQuery('#ajaxContent').html(result);
							jQuery('#forma-edit input[type=\"submit\"]').removeAttr('disabled');
						}, 1000);
					}
				});
			}

			jQuery(document).ready(function($) {
				$('select[name=\"gametype\"]').change(function () {
					$('.gtype-img').attr('src','acpanel/images/games/' + $(this).val() + '.png');
				});

				refreshServerInfo('".$server_fields['address']."', '".$server_fields['gametype']."');

				$('.ajaxImg').click(function() {
					if (!$(this).hasClass('load')) {
						$(this).addClass('load');
						var data = $('#forma-edit input[name=\"address\"]').val();
						var tp = $('[name=\"gametype\"]').val();

						$.ajax({
							type:'POST',
							url:'acpanel/ajax.php?do=ajax_servers_control',
							data:({'address' : data,'go' : 5,'type' : tp}),
							success:function(result) {
								setTimeout(function() {
									$('#forma-edit input[name=\"hostname\"]').val(result);
									$('.ajaxImg').removeClass('load');
								}, 1000);
							}
						});
					}
				});

				$('#forma-edit').submit(function() {
					$.blockUI({ message: null });
					var data = $(this).serialize();
					var address = $('input[name=\"address\"]', this).val();
					var real_address = $('input[name=\"real_address\"]', this).val();
					var gametype = $('[name=\"gametype\"]', this).val();
					var real_gametype = $('[name=\"real_gametype\"]', this).val();

					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_servers_control',
						data:data + '&go=7',
						success:function(result) {
							$('.accessMessage').html('');
		
							if(result.indexOf('id=\"success\"') + 1)
							{
								if( (address != real_address) || (gametype != real_gametype) )
								{
									if( address != real_address )
										$('#forma-edit input[name=\"real_address\"]').val(address);
	
									if( gametype != real_gametype )
										$('#forma-edit input[name=\"real_gametype\"]').val(gametype);

									$('.refresh').click();
								}

								humanMsg.displayMsg(result,'success');
							}
							else
							{
								humanMsg.displayMsg(result,'error');
							}
						},
						complete:function() {
							$.unblockUI();
						}
					});

					return false;
				});

				useSticky();
			});
		</script>
	";

	$smarty->assign("cat_current",array('title'=>'@@server@@ #'.$server_fields['id']));
	if(isset($server_fields)) $smarty->assign("server_fields",$server_fields);
	if(isset($server_options)) $smarty->assign("server_options",$server_options);
	if(isset($error)) $smarty->assign("iserror",$error);

	include_once(INCLUDE_PATH . 'functions.servers.php');
	$smarty->assign("gtypes",server_protocol_list());

	$go_page = 'p_servers_control_edit';
}
else
{
	$total_items = $db->Query("SELECT count(*) FROM `acp_servers` ORDER BY id", array(), $config['sql_debug']);

	if (!$total_items) {
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
					url:'acpanel/ajax.php?do=ajax_servers_control',
					data:'go=1&cat=".$section_current['id']."&cat_edit=".$cat_current['id']."&offset=' + first + '&limit=' + pg_size,
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
			});
		</script>
	";

	if(isset($error)) $smarty->assign("iserror",$error);

	foreach ($all_categories as $key => $value)
	{		$search = array_search("p_servers_control_add", $value);
		if ($search)
		{			$smarty->assign("cat_addsrv_id", $key);
			break;
		}
	}
}

?>
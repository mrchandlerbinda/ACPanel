<?php

	$result_perm = $db->Query("SELECT * FROM `acp_usergroups_permissions` WHERE id <> 0 ORDER BY perm_sort", array(), $config['sql_debug']);
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
						$general_editable = "";
						foreach( $actionRules as $a => $b )
						{
							$general_editable .= "
								<span class='input-perm'><input class='checkbox' type='checkbox' name='{$objperm->varname}[]' value='{$a}' /> @@perm_{$a}@@</span> 
							";
						}
						break;
					case "textarea":
						$general_editable = "
							<textarea name='{$objperm->varname}' rows='5' cols='30' /></textarea>
						";
						break;
					case "checkbox":
						$box = explode("\n", $objperm->options);
						unset($general_editable);
						foreach($box as $b) {
							$box_value = explode("|", $b);
							$general_editable .= "
								<input class='checkbox' type='checkbox' name='{$objperm->varname}[]' value='{$box_value[0]}' /> ".$box_value[1]."<br />
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
														<option value='{$obj_new->categoryid}'>".(($obj_new->catlevel > 0) ? '&brvbar; '.str_repeat('- - - ', $obj_new->catlevel).$obj_new->title : $obj_new->title)."</option>
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
												<option value='{$obj_new->one_field}'>".$obj_new->two_field."</option>
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
										<option value='{$box_value[0]}'>".$box_value[1]."</option>
								";
							}
						}

						$general_editable .= "
							</select>
						";
						break;
					case "boolean":
						$general_editable = "
							<input class='radio' type='radio' name='{$objperm->varname}' value='yes' /> @@yes@@&nbsp;
							<input class='radio' type='radio' name='{$objperm->varname}' value='no' checked='checked' /> @@no@@
						";
						break;
					default:
						$general_editable = "
							<input class='text small' type='text' name='{$objperm->varname}' value='' size='30'>
						";
				}

				$group_edit[$objperm->section]['options'][] = array('description' => $objperm->description, 'content' => $general_editable);
			}
		}
	}

	$smarty->assign("group_edit", $group_edit);

	unset($cat_groups_list);
	foreach ($all_categories as $key => $value)
	{
		$search = array_search("p_usergroups", $value);
		if ($search)
		{			$cat_groups_list = $key;
			break;
		}
	}
	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".$cat_groups_list;

	$smarty->assign("action_uri", $action_uri);
	$smarty->assign("head_title","@@add_group@@");

	if(isset($error)) $smarty->assign("iserror",$error);

?>
<?php

if(!isset($_POST['go']))
{
	die("Hacking Attempt");
}
else
{
	require_once(INCLUDE_PATH . 'class.mysql.php');

	try {
		$db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
	} catch (Exception $e) {
		die($e->getMessage());
	}

	$array_cfg = $db->Query("SELECT varname, value FROM `acp_config` WHERE varname IS NOT NULL", array(), true);

	if(is_array($array_cfg)) {
		foreach ($array_cfg as $obj){
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();
	}

	include(INCLUDE_PATH . 'functions.main.php');
	$langs = create_lang_list();

	unset($translate);
	$filter = "lp_name='p_general_categories.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
	$arguments = array('lang'=>get_language(1));
	$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
	if(is_array($tr_result)) {
		foreach ($tr_result as $obj){
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}

	include(INCLUDE_PATH . '_auth.php');
	
	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create list
	// 2 - add item
	// 3 - del item
	// 4 - edit item
	// 5 - resort list

	switch($_POST['go'])
	{		case "1":

			$result_node = $db->Query("SELECT * FROM `acp_category` WHERE catlevel = 0 ORDER BY display_order", array(), $config['sql_debug']);

			if (is_array($result_node))
			{
				foreach ($result_node as $obj)
				{
					$arguments = array('catleft'=>$obj->catleft,'catright'=>$obj->catright,'category'=>$obj->categoryid);
					$result_list = $db->Query("SELECT * FROM `acp_category` WHERE catleft >= {catleft} AND catright <= {catright} AND sectionid = '{category}' OR categoryid = '{category}' ORDER BY catleft", $arguments, $config['sql_debug']);

					if (is_array($result_list))
					{
						foreach ($result_list as $cat)
						{
							$array_category[] = (array)$cat;
						}
					}
					else
					{
						$error = $translate['error_select_list'];
					}
				}
			}
			else
			{
				$error = $translate['error_select_section'];
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("array_category",$array_category);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_general_categories_list.tpl');

			break;

		case "2":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_categories', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$section_title = trim($_POST['title']);
	
				if ($section_title == '')
				{
					print $translate['dont_empty'];
				}
				else
				{
					$section_order = trim($_POST['order']);
					if (!is_numeric($section_order))
					{
						$section_order = 10;
					}
	
					if( isset($_POST['blocks']) )
					{
						if( is_array($_POST['blocks']) )
						{
							$section_blocks = implode(",", $_POST['blocks']);
						}
						else
						{
							$section_blocks = $_POST['blocks'];
						}
					}
					else
					{
						$section_blocks = "";
					}
	
					$section_product = trim($_POST['productid']);
					$section_parent = trim($_POST['parent']);
					$section_link = trim($_POST['link']);
					$section_url = trim($_POST['url']);
					$section_desc = trim($_POST['description']);
	
					if ($config['charset'] != 'utf-8')
					{
						$f = iconv('utf-8', $config['charset'], $section_title);
						$section_desc = iconv('utf-8', $config['charset'], $section_desc);
					}
					else
					{
						$f = $section_title;
					}
	
					if( category_add($section_parent, $section_order, $section_link, $f, $section_blocks, $section_product, true, $section_url, $section_desc) )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_categories", "add category: ".$f);
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "3":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_categories', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				if(category_delete($id))
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_categories", "delete category id: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "4":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_categories', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$categoryid = $_POST['categoryid'];
				$section_title = trim($_POST['title']);
	
				if( $section_title == '' )
				{
					print $translate['dont_empty'];
				}
				else
				{
					$section_order = trim($_POST['order']);
					if (!is_numeric($section_order))
					{
						$section_order = 10;
					}
	
					if( isset($_POST['blocks']) )
					{
						if( is_array($_POST['blocks']) )
						{
							$section_blocks = implode(",", $_POST['blocks']);
						}
						else
						{
							$section_blocks = $_POST['blocks'];
						}
					}
					else
					{
						$section_blocks = "";
					}
	
					$section_product = trim($_POST['productid']);
					$section_parent = trim($_POST['parent']);
					$section_link = trim($_POST['link']);
					$section_url = trim($_POST['url']);
					$section_desc = trim($_POST['description']);
	
					if ($config['charset'] != 'utf-8')
					{
						$f = iconv('utf-8', $config['charset'], $section_title);
						$section_desc = iconv('utf-8', $config['charset'], $section_desc);
					}
					else
					{
						$f = $section_title;
					}
	
					$arguments = array('category'=>$categoryid);
					$result = $db->Query("SELECT * FROM `acp_category` WHERE categoryid = {category}", $arguments, $config['sql_debug']);
					if( is_array($result) )
					{
						foreach( $result as $obj )
						{
							if( preg_match("/^@@+[a-z_]+@@$/i", $section_title) )
							{
								$section_title_rewrite = "title = '{title}', ";
							}
							else
							{
								$section_title_rewrite = "";
							}
	
							if( $section_parent == $obj->parentid )
							{
								$arguments = array('title'=>$f,'productid'=>$section_product,'link'=>$section_link,'url'=>$section_url,'show_blocks'=>$section_blocks,'display_order'=>$section_order,'category'=>$categoryid,'description'=>$section_desc);
								$result_update = $db->Query("UPDATE `acp_category` SET ".$section_title_rewrite."productid = '{productid}', link= '{link}', url= '{url}', show_blocks = '{show_blocks}', display_order = '{display_order}', description = '{description}' WHERE categoryid = {category}", $arguments, $config['sql_debug']);

								if( $result_update && $section_link != $obj->link )
								{
									if( $obj->link == "custom_page" )
										$result_page = $db->Query("DELETE FROM `acp_pages` WHERE catid = ".$obj->categoryid, array(), $config['sql_debug']);
									elseif( $section_link == "custom_page" )
										$result_page = $db->Query("INSERT INTO `acp_pages` SET catid = ".$obj->categoryid, array(), $config['sql_debug']);
								}

								if( ($section_order != $obj->display_order) && $section_parent )
								{
									if( !category_resort($section_parent) )
									{
										$error = '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['resort_error'].'</span>';
									}
								}
							}
							else
							{
								if( !$section_parent )
								{
									$diff = $obj->catleft - 1;
									$diff_lvl = $obj->catlevel;
									$arguments = array('title'=>$f,'productid'=>$section_product,'link'=>$section_link,'url'=>$section_url,'show_blocks'=>$section_blocks,'display_order'=>$section_order,'category'=>$categoryid,'diff'=>$diff,'diff_lvl'=>$diff_lvl,'description'=>$section_desc);
									$result_update1 = $db->Query("UPDATE `acp_category` SET ".$section_title_rewrite."catleft = '1', catright = catright - {diff}, catlevel = '0', sectionid = NULL, parentid = NULL, productid = '{productid}', link= '{link}', url= '{url}', show_blocks = '{show_blocks}', display_order = '{display_order}', description = '{description}' WHERE categoryid = {category}", $arguments, $config['sql_debug']);

									if( $result_update1 && $section_link != $obj->link )
									{
										if( $obj->link == "custom_page" )
											$result_page = $db->Query("DELETE FROM `acp_pages` WHERE catid = ".$obj->categoryid, array(), $config['sql_debug']);
										elseif( $section_link == "custom_page" )
											$result_page = $db->Query("INSERT INTO `acp_pages` SET catid = ".$obj->categoryid, array(), $config['sql_debug']);
									}
	
									if( $obj->catright - $obj->catleft > 1 )
									{
										$arguments['section'] = ($obj->sectionid) ? $obj->sectionid : $categoryid;
										$arguments['catleft'] = $obj->catleft;
										$arguments['catright'] = $obj->catright;
	
										$result_update2 = $db->Query("UPDATE `acp_category` SET catleft = catleft - {diff}, catright = catright - {diff}, catlevel = catlevel - {diff_lvl}, sectionid = '{category}' WHERE catleft > {catleft} AND catright < {catright} AND sectionid = {section}", $arguments, $config['sql_debug']);
					                		}
								}
								else
								{
									$arguments = array('parent'=>$section_parent);
									$result_new = $db->Query("SELECT * FROM `acp_category` WHERE categoryid = {parent}", $arguments, $config['sql_debug']);
									if( is_array($result_new) )
									{
										foreach( $result_new as $newobj )
										{
											if( ($obj->sectionid && ($newobj->sectionid == $obj->sectionid)) || (!$newobj->sectionid && ($obj->sectionid == $newobj->categoryid)) )
											{
												if( ($newobj->catleft > $obj->catleft) && ($newobj->catright < $obj->catright) )
												{
		 											$error = '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['parent_error'].'</span>';
		 										}
		 										else
		 										{
													$diff_editing = $obj->catright - $obj->catleft + 1;
													$section_editing = (!$newobj->sectionid) ? $newobj->categoryid : $newobj->sectionid;
													$diff_lvl = $obj->catlevel - $newobj->catlevel - 1;
	
													$arguments = array('parent'=>$section_parent,'catright'=>$obj->catright,'catleft'=>$obj->catleft,'section'=>$section_editing);
													$result_child = $db->Query("SELECT categoryid FROM `acp_category` WHERE catright <= {catright} AND catleft >= {catleft} AND sectionid = '{section}'", $arguments, $config['sql_debug']);
													if( is_array($result_child) )
													{
														foreach( $result_child as $childobj )
														{
															$child_id[] = $childobj->categoryid;
														}
													}
													else
													{
														$child_id[0] = $result_child;
													}
	
													// update new section element
													$arguments = array('catright_old'=>$obj->catright,'catright_new'=>$newobj->catright,'catleft_old'=>$obj->catleft,'catleft_new'=>$newobj->catleft,'catlevel_old'=>$obj->catlevel,'catlevel_new'=>$newobj->catlevel,'section'=>$section_editing);
													if($newobj->catright > $obj->catright && $newobj->catleft < $obj->catleft)
													{
														$result_update1 = $db->Query("UPDATE `acp_category` SET
															catright = IF(catright BETWEEN ({catright_old} + 1) AND ({catright_new} - 1), catright - ({catright_old} - {catleft_old} + 1), IF(catleft BETWEEN {catleft_old} AND {catright_old}, catright + ((({catright_new}-{catright_old}-{catlevel_old}+{catlevel_new})/2)*2+{catlevel_old}-{catlevel_new}-1), catright)),
															catleft = IF(catleft BETWEEN ({catright_old} + 1) AND ({catright_new} - 1), catleft - ({catright_old} - {catleft_old} + 1), IF(catleft BETWEEN {catleft_old} AND {catright_old}, catleft + ((({catright_new}-{catright_old}-{catlevel_old}+{catlevel_new})/2)*2+{catlevel_old}-{catlevel_new}-1), catleft))
															WHERE catleft BETWEEN ({catleft_new}+1) AND ({catright_new}-1) AND (categoryid = '{section}' OR sectionid = '{section}')", $arguments, $config['sql_debug']);
													}
													elseif ($newobj->catleft < $obj->catleft)
													{
														$result_update1 = $db->Query("UPDATE `acp_category` SET
															catleft = IF(catleft BETWEEN {catright_new} AND ({catleft_old} - 1), catleft + ({catright_old} - {catleft_old} + 1), IF(catleft BETWEEN {catleft_old} AND {catright_old}, catleft - ({catleft_old} - {catright_new}), catleft)),
															catright = IF(catright BETWEEN {catright_new} AND {catleft_old}, catright + ({catright_old} - {catleft_old} + 1), IF(catright BETWEEN {catleft_old} AND {catright_old}, catright - ({catleft_old} - {catright_new}), catright))
															WHERE (catleft BETWEEN {catleft_new} AND {catright_old} OR catright BETWEEN {catleft_new} AND {catright_old}) AND (categoryid = '{section}' OR sectionid = '{section}')", $arguments, $config['sql_debug']);
													}
													else
													{
														$result_update1 = $db->Query("UPDATE `acp_category` SET
															catleft = IF(catleft BETWEEN {catright_old} AND {catright_new}, catleft - ({catright_old} - {catleft_old} + 1), IF(catleft BETWEEN {catleft_old} AND {catright_old}, catleft + ({catright_new} - 1 - {catright_old}), catleft)),
															catright = IF(catright BETWEEN ({catright_old} + 1) AND ({catright_new} - 1), catright - ({catright_old} - {catleft_old} + 1), IF(catright BETWEEN {catleft_old} AND {catright_old}, catright + ({catright_new} - 1 - {catright_old}), catright))
															WHERE (catleft BETWEEN {catleft_old} AND {catright_new} OR catright BETWEEN {catleft_old} AND {catright_new}) AND (categoryid = '{section}' OR sectionid = '{section}')", $arguments, $config['sql_debug']);
													}
	
													if(!empty($child_id))
													{
														// update the children of the edited category
														$arguments = array('diff_lvl'=>$diff_lvl,'child_id'=>$child_id);
														$result_update2 = $db->Query("UPDATE `acp_category` SET catlevel = catlevel - {diff_lvl} WHERE categoryid IN ('{child_id}')", $arguments, $config['sql_debug']);
													}
	
													// update the edited category
													$arguments = array('title'=>$f,'productid'=>$section_product,'link'=>$section_link,'url'=>$section_url,'show_blocks'=>$section_blocks,'display_order'=>$section_order,'category'=>$categoryid,'parentid'=>$section_parent,'section'=>$section_editing,'description'=>$section_desc);
										                        $result_update3 = $db->Query("UPDATE `acp_category` SET ".$section_title_rewrite."sectionid = '{section}', productid = '{productid}', link = '{link}', url = '{url}', show_blocks = '{show_blocks}', display_order = '{display_order}', parentid = '{parentid}', description = '{description}' WHERE categoryid = {category}", $arguments, $config['sql_debug']);

													if( $result_update3 && $section_link != $obj->link )
													{
														if( $obj->link == "custom_page" )
															$result_page = $db->Query("DELETE FROM `acp_pages` WHERE catid = ".$obj->categoryid, array(), $config['sql_debug']);
														elseif( $section_link == "custom_page" )
															$result_page = $db->Query("INSERT INTO `acp_pages` SET catid = ".$obj->categoryid, array(), $config['sql_debug']);
													}
				
									                        	if( !category_resort($section_parent) )
									                        	{
														$error = '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['resort_error'].'</span>';
									                        	}
		 										}
											}
											else
											{
												$diff_editing = $obj->catright - $obj->catleft + 1;
												$section_editing = (!$obj->sectionid) ? $obj->categoryid : $obj->sectionid;
												$section_id = (!$newobj->sectionid) ? $newobj->categoryid : $newobj->sectionid;
	
												if(($newobj->catright - $newobj->catleft) == 1)
												{
													$catleft = $newobj->catright;
												}
												else
												{
													$arguments = array('sectionid'=>$section_id,'catright'=>$newobj->catright,'display_order'=>$section_order,'parentid'=>$section_parent);
													$catleft = $db->Query("SELECT MIN(catleft) FROM `acp_category` WHERE sectionid = '{sectionid}' AND catright < {catright} AND display_order >= {display_order} AND parentid = '{parentid}'", $arguments, $config['sql_debug']);
													if(!$catleft)
													{
														$catleft = $newobj->catright;
													}
												}
	
												$diff = $obj->catleft - $catleft;
												$diff_lvl = $obj->catlevel - $newobj->catlevel - 1;
	
												// update new section element
												$arguments = array('sectionid'=>$section_id,'catleft'=>$catleft,'diff_editing'=>$diff_editing);
												$result_update1 = $db->Query("UPDATE `acp_category` SET catleft = IF(catleft < {catleft}, catleft, catleft + {diff_editing}), catright = IF(catright < {catleft}, catright, catright + {diff_editing}) WHERE categoryid = {sectionid} OR sectionid = '{sectionid}'", $arguments, $config['sql_debug']);
												if( $diff_editing > 2 )
												{
													// update the children of the edited category
													$arguments = array('sectionid'=>$section_id,'editing'=>$section_editing,'catleft'=>$obj->catleft,'catright'=>$obj->catright,'diff'=>$diff,'diff_lvl'=>$diff_lvl);
													$result_update2 = $db->Query("UPDATE `acp_category` SET catleft = catleft - {diff}, catright = catright - {diff}, catlevel = catlevel - {diff_lvl}, sectionid = '{sectionid}' WHERE catright < {catright} AND catleft > {catleft} AND sectionid = '{editing}'", $arguments, $config['sql_debug']);
												}
												// update the edited category
												$arguments = array('category'=>$categoryid,'sectionid'=>$section_id,'parent'=>$section_parent,'diff'=>$diff,'diff_lvl'=>$diff_lvl,'title'=>$f,'productid'=>$section_product,'link'=>$section_link,'url'=>$section_url,'show_blocks'=>$section_blocks,'display_order'=>$section_order,'description'=>$section_desc);
												$result_update3 = $db->Query("UPDATE `acp_category` SET ".$section_title_rewrite."catleft = catleft - {diff}, catright = catright - {diff}, catlevel = catlevel - {diff_lvl}, sectionid = '{sectionid}', productid = '{productid}', link = '{link}', url = '{url}', show_blocks = '{show_blocks}', display_order = '{display_order}', parentid = '{parent}', description = '{description}' WHERE categoryid = {category}", $arguments, $config['sql_debug']);

												if( $result_update3 && $section_link != $obj->link )
												{
													if( $obj->link == "custom_page" )
														$result_page = $db->Query("DELETE FROM `acp_pages` WHERE catid = ".$obj->categoryid, array(), $config['sql_debug']);
													elseif( $section_link == "custom_page" )
														$result_page = $db->Query("INSERT INTO `acp_pages` SET catid = ".$obj->categoryid, array(), $config['sql_debug']);
												}

												if( $obj->sectionid )
												{
													// update the elements of the old section
													$arguments = array('category'=>$obj->sectionid,'catleft'=>$obj->catleft,'catright'=>$obj->catright);
													$result_update4 = $db->Query("UPDATE `acp_category` SET catleft = IF(catleft < {catleft}, catleft, catleft - ({catright} - {catleft} + 1)), catright = IF(catright < {catleft}, catright, catright - ({catright} - {catleft} + 1)) WHERE categoryid = {category} OR sectionid = {category}", $arguments, $config['sql_debug']);
							   					}
											}
										}
									}
									else
									{
										$error = '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['parent_error'].'</span>';
									}
								}
							}
						}
					}
	
					if( !isset($error) )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_categories", "edit category: ".$categoryid);
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
					}
					else
					{
						print $error;
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "5":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('general_perm_categories', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				unset($_POST['go'], $array_cat);
	
				foreach($_POST as $k => $v)
				{
					$categoryid = substr($k, 6);
					$array_cat[$categoryid] = $v;
				}
	
				$result = $db->Query("SELECT categoryid, display_order FROM `acp_category`", array(), $config['sql_debug']);
				if(is_array($result))
				{
					foreach ($result as $obj)
					{
						if($array_cat[$obj->categoryid] != $obj->display_order)
						{
							$arguments = array('category'=>$obj->categoryid,'display_order'=>$array_cat[$obj->categoryid]);
							$result_update = $db->Query("UPDATE `acp_category` SET display_order = {display_order} WHERE categoryid = {category}", $arguments, $config['sql_debug']);
						}
						else
						{
							unset($array_cat[$obj->categoryid]);
						}
					}
				}
	
				if(!empty($array_cat))
				{
					$arguments = array('edited_cat'=>array_keys($array_cat));
					$result_parents = $db->Query("SELECT parentid FROM `acp_category` WHERE categoryid IN ('{edited_cat}') AND parentid IS NOT NULL GROUP BY parentid", $arguments, $config['sql_debug']);
	
					if(is_array($result_parents))
					{
						foreach ($result_parents as $obj)
						{
				           	if( !category_resort($obj->parentid) )
				           	{
								$error = '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['resort_error'].'</span>';
				           	}
						}
					}
					else
					{
				           	if( !category_resort($result_parents) )
				           	{
			           			$error = '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['resort_error'].'</span>';
				           	}
					}
				}
	
				if( !isset($error) )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_categories", "resort categories");
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['resort_success'].'</span>';
				}
				else
				{
					print $error;
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		default:

			die("Hacking Attempt");
	}
}

?>
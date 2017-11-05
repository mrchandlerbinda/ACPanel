<?php

// ###############################################################################
// DEFINE CONSTANTS
// ###############################################################################

define("MAIN_INDEX", true);

define("IN_ACP", true);
define('ROOT_PATH', './');
define('SCRIPT_PATH', ROOT_PATH . 'acpanel/scripts/');
define('INCLUDE_PATH', ROOT_PATH . 'acpanel/includes/');
define('TEMPLATE_PATH', ROOT_PATH . 'acpanel/templates/');

// ###############################################################################
// INIT SCRIPT
// ###############################################################################

include(INCLUDE_PATH . '_init.php');
$debug_info = false;
if( $userinfo['admin_access'] == 'yes' && $config['sql_debug'] )
{
	$debug_info = true;
}

$smarty->assign("home",$config['acpanel'].'.php');

// ###############################################################################
// SITE OFFLINE?
// ###############################################################################

if( $config['site_offline'] )
{
	$login_page = false;
	if( isset($_GET['do']) )
	{
		if( $_GET['do'] == 'login' )
		{
			$login_page = true;
		}
	}

	if( $userinfo['admin_access'] == 'no' && !$login_page )
	{
		$smarty->assign("offline_info", $config['site_offline_text']);
		$smarty->assign("site_name",$config['site_name']);

		$smarty->registerFilter("output","translate_template");
		$smarty->display('under_constructions.tpl');
		exit;
	}
	else
	{
		$smarty->assign("site_disabled", true);
	}
}

// ###############################################################################
// BUILD CATEGORIES FOR MENU
// ###############################################################################

if( isset($_GET['cat']) && is_numeric($_GET['cat']) )
{
	$current_section_id = $_GET['cat'];
}
else
{
	$current_section_id = 1;
}

unset($section_current, $cat_current, $menu_sections, $menu_categories, $all_categories, $build_categories, $blocks);
$arrNoAccess = array();

$arguments = array('curent_section_id'=>$current_section_id);
$array_cat = $db->Query("SELECT * FROM `acp_category` WHERE sectionid IS NULL OR sectionid = '{curent_section_id}' ORDER BY catleft, display_order", $arguments, $config['sql_debug']);

if( is_array($array_cat) )
{
	foreach( $array_cat as $obj )
	{
		if( in_array($obj->sectionid, $arrNoAccess) ) continue;

		if( isset($childs) )
		{
			if( $childs )
			{
				--$childs;
				continue;
			}
		}

		if( !in_array($obj->categoryid, $userinfo['read_category']) )
		{
			$childs = ($obj->catright - $obj->catleft - 1)/2;
			if( $childs && $obj->catlevel == 0 )
			{
				$childs = 0;
				$arrNoAccess[] = $obj->categoryid;
			}
			continue;
		}

		if( $obj->categoryid == $current_section_id )
		{
			$section_current = array('id' => $obj->categoryid, 'title' => $obj->title, 'description' => $obj->description, 'link' => $obj->link, 'blocks' => $obj->show_blocks, 'url' => $obj->url);
			$menu_sections[$obj->categoryid] = array('title' => $obj->title, 'description' => $obj->description, 'link' => $obj->link, 'url' => $obj->url);
		}
		elseif( !$obj->sectionid )
		{
			$menu_sections[$obj->categoryid] = array('title' => $obj->title, 'description' => $obj->description, 'link' => $obj->link, 'url' => $obj->url);
		}
		else
		{
			if( $obj->display_order != 0 )
			{
				$menu_categories[$obj->categoryid] = array('title' => $obj->title, 'description' => $obj->description, 'link' => $obj->link, 'level' => $obj->catlevel, 'left' => $obj->catleft, 'right' => $obj->catright, 'url' => $obj->url);
			}

			$all_categories[$obj->categoryid] = array('title' => $obj->title, 'description' => $obj->description, 'link' => $obj->link, 'left' => $obj->catleft, 'blocks' => $obj->show_blocks, 'url' => $obj->url);
		}
	}
}

if( !isset($section_current) )
{
	include(ROOT_PATH . 'acpanel/404.php');
}

$smarty->assign("section_current", $section_current);
if( isset($menu_sections) ) $smarty->assign("menu_sections", $menu_sections);
if( isset($menu_categories) ) $smarty->assign("menu_categories", $menu_categories);

// ###############################################################################
// GENERATE CONTENT
// ###############################################################################

if( isset($_GET["do"]) )
{
	if( $_GET["do"] == "login" )
	{
		$cat_current = array('title'=>'@@login_title@@');
		$smarty->assign("cat_current",$cat_current);
		$go_page = 'login';
		include("acpanel/login.php");
	}
	else if( $_GET["do"] == "register" )
	{
		$cat_current = array('title'=>'@@register_title@@');
		$smarty->assign("cat_current",$cat_current);
		$go_page = 'register';
		include("acpanel/register.php");
	}
	else if( $_GET["do"] == "profile" )
	{
		if( !$userinfo['uid'] && !isset($_GET["u"]) && !isset($_GET["c"]) && !isset($_GET["pay"]) && !isset($_POST["recovery"]) )
		{
			header('Location: '.$config['acpanel'].'.php?do=login');
			exit;
		}
		$cat_current = array('title'=>'@@profile_title@@');
		$smarty->assign("cat_current",$cat_current);
		$go_page = 'profile';
		include("acpanel/profile.php");
	}
	else if( is_array($all_categories) && is_numeric($_GET['do']) )
	{
		if( array_key_exists($_GET['do'], $all_categories) )
		{
			$filepath = INCLUDE_PATH . $all_categories[$_GET['do']]['link'] . ".php";
			if( file_exists($filepath) )
			{
				if( strlen($all_categories[$_GET['do']]['blocks']) ) $blocks = $all_categories[$_GET['do']]['blocks'];

				$go_page = $all_categories[$_GET['do']]['link'];
				$cat_current = array('id'=>$_GET['do'],'title'=>$all_categories[$_GET['do']]['title'],'description'=>$all_categories[$_GET['do']]['description'],'left'=>$all_categories[$_GET['do']]['left'],'blocks'=>$all_categories[$_GET['do']]['blocks']);
				include($filepath);
				$smarty->assign("cat_current",$cat_current);
			}
			else
			{
				if( $current_section_id == '1' )
				{
					header('Location: '.$config['acpanel'].'.php');
				}
				else
				{
					header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id);
				}

				exit;
			}
		}
		else
		{
			include(ROOT_PATH . 'acpanel/404.php');
		}
	}
	else
	{
		header('Location: '.$config['acpanel'].'.php');
		exit;
	}
}
else
{
	if( strlen($section_current['blocks']) ) $blocks = $section_current['blocks'];

	$go_page = $section_current['link'];
	include(INCLUDE_PATH . $go_page . ".php");
}

// ###############################################################################
// LOAD BLOCKS
// ###############################################################################

unset($blocks_out);

if( isset($blocks) )
{
	$arguments = array('blocks'=>$blocks);
	$array_blocks = $db->Query("SELECT * FROM `acp_blocks` WHERE blockid IN ({blocks}) AND display_order != 0 ORDER BY display_order", $arguments, $config['sql_debug']);

	if( is_array($array_blocks) )
	{
		foreach( $array_blocks as $obj )
		{
			unset($blockEXIT);
			if( $obj->link )
			{
				$blockpath = INCLUDE_PATH . "blocks/block_".$obj->link.".php";
				$blockLINK = $obj->link;
				$blockVIEW = ($obj->view_in_block == "yes") ? false : true;
				if( file_exists($blockpath) )
				{
					include_once($blockpath);
				}
				if( !isset($blockEXIT) ) $blocks_out[] = array('link' => "blocks/block_".$blockLINK.".tpl", 'no_decor' => $blockVIEW);
			}
			elseif( $obj->execute_code )
			{
				$blocks_out[] = array('block_head' => $obj->title, 'block_content' => $obj->execute_code, 'no_decor' => ($obj->view_in_block == "yes") ? false : true);
			}
		}
	}
}

// ###############################################################################
// OUTPUT CONTENT
// ###############################################################################

$smarty->assign("site_name",$config['site_name']);
$smarty->assign("site_description",$config['site_description']);
$smarty->assign("home_title",$config['home_title']);
if( isset($headinclude) ) $smarty->assign("headinclude",$headinclude);
if( isset($error) ) $smarty->assign("iserror",$error);

$smarty->registerFilter("output","translate_template");
$smarty->display('header.tpl');

if( isset($blocks_out) )
{
	$smarty->display('content_pre.tpl');
	$smarty->display($go_page.'.tpl');
	$smarty->display('content_mid.tpl');
	foreach( $blocks_out as $key => $val )
	{
		$smarty->assign("no_decor", $val['no_decor']);

		if( isset($val['link']) )
		{
			$smarty->display($val['link']);
		}
		else
		{
			$smarty->assign("block_head", $val['block_head']);
			$smarty->assign("block_content", block_output_content($val['block_content']));
			$smarty->display("blocks/custom.tpl");
		}
	}
	$smarty->display('content_post.tpl');
}
else
{
	$smarty->display($go_page.'.tpl');
}

if( isset($config['ext_auth_type']) )
{
	if( $config['ext_auth_type'] == "xf" && isset($config['xfAuth']) )
	{
		$smarty->assign("rightContent", $xf->getRightContent());
	}
}

// ###############################################################################
// SCRIPT END TIME
// ###############################################################################

$end_time = microtime();
$end_array = explode(" ",$end_time);
$end_time = $end_array[1] + $end_array[0];
$time = round($end_time - $start_time, 5);

$php_gen = round(($time - $db->DeltaQuery())*100/$time);
$sql_gen = 100 - $php_gen;

$smarty->assign("genpage",$time);
$smarty->assign("php_gen",$php_gen);
$smarty->assign("sql_gen",$sql_gen);
$smarty->assign("count_query",$db->CountQuery());

// ###############################################################################
// LOAD FOOTER
// ###############################################################################

$smarty->assign("debug_info",$debug_info);
$smarty->assign("sql_debug",$db->ShowDebugInfo());
$smarty->display('footer.tpl');

// ###############################################################################
// CLOSE CONNECTION
// ###############################################################################

if( isset($db) && $db->IsConnected() )
{
	$db->Close();
}

?>

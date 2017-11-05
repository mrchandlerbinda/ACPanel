<?php

$headinclude = ( isset($headinclude) ) ? $headinclude."<script type='text/javascript' src='acpanel/scripts/flashtags/swfobject.js'></script>" : "<script type='text/javascript' src='acpanel/scripts/flashtags/swfobject.js'></script>";

$tpl_tags_flash = false;
$cache_need_create = false;
$cache_prefix = 'flashtags_'.$obj->blockid;

include_once(INCLUDE_PATH . 'functions.servers.php');

if( $config['cloud_cache'] )
{
	$tpl_tags_flash = get_cache($cache_prefix, $config['cloud_cache_time']);
	$cache_need_create = ($tpl_tags_flash !== false) ? false : true;
}

if( $tpl_tags_flash === false )
{
	$counts = array();
	$tags = array();
	$list = array();
	$sizes = array("8pt", "11.5pt", "15pt", "18.5pt", "22pt");
	$min   = 1;
	$max   = 1;
	$range = 1;
	$order = "";

	$arguments = array('cloud_limit'=>$config['cloud_limit']);
	$productID = getProduct("ratingServers");
	if( !empty($productID) )
	{
		$order = " ORDER BY rating DESC";
	}
	$array_servers = $db->Query("SELECT hostname, address, id FROM `acp_servers`".$order." LIMIT {cloud_limit}", $arguments, $config['sql_debug']);

	if( is_array($array_servers) )
	{
		foreach( $array_servers as $obj )
		{			$tagname = ($config['cloud_erase']) ? trim(preg_replace("/".$config['cloud_erase']."/", "", $obj->hostname)) : $obj->hostname;			$r = rand();			$tags[$obj->id] = array($obj->address, $tagname, $r);
			$counts[] = $r;
		}

		if( count($counts) )
		{
			$min = min($counts);
			$max = max($counts);
			$range = (($max-$min) == 0) ? $range : ($max-$min);
		}

		foreach( $tags as $tag => $value )
		{
			$list[$tag]['tag'] = $value[0];
			$list[$tag]['name'] = $value[1];
			$list[$tag]['size'] = $sizes[sprintf("%d", ($value[2]-$min)/$range*4 )];
		}

		$tags = array();

		if( !empty($productID) )
		{
			$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_server_card' LIMIT 1", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach( $result_cats as $cat )
				{
					$cat_server = array('cat' => $cat->sectionid, 'do' => $cat->categoryid);
				}
			}
		}

		foreach( $list as $k => $value )
		{
			$href = (isset($cat_server)) ? $config['acpanel'].".php?cat=".$cat_server['cat']."&do=".$cat_server['do']."&server=".$k : "steam://connect/".$value['tag'];
			$tags[] = "<a href='".$href."' style='font-size:{$value['size']};'>".$value['name']."</a>";
		}

		$tpl_tags_flash = implode("", $tags);
	}
	else
	{
		$tpl_tags_flash = "<a href='#' style='font-size:10pt;'>no_servers</a>";
	}

	if( $cache_need_create )
		create_cache($cache_prefix, $tpl_tags_flash);
}

$smarty->assign("tpl_tags_flash",$tpl_tags_flash);
$smarty->assign("cloud_width",$config['cloud_width']);
$smarty->assign("cloud_height",$config['cloud_height']);
$smarty->assign("cloud_speed",$config['cloud_speed']);


?>
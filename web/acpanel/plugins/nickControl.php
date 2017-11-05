<?php

// ###############################################################################
// Nick Control version 2.0
// ###############################################################################

if($product_install)
{
	$result_select = $db->Query("SELECT productid, version FROM `acp_products` WHERE productid = '$prd_id' LIMIT 1");

	if(is_array($result_select))
	{
		foreach ($result_select as $obj)
		{
			$old_version = str_replace(".", "", $obj->version);
			$new_version = str_replace(".", "", $prd_version);
	
			if($old_version < 14)
			{
				$error[] = "please update acpanel and try again";
			}
			elseif($old_version > $new_version)
			{
				$error[] = "the product is installed and its version is newer or equal that you are installing";
			}
		}
	}
	else
	{
		$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'p_servers' LIMIT 1");
		
		if($result_category)
		{
			if( ($parentid = category_add($result_category, 10, "", "@@nick_control@@", "", "nickControl")) == 0 )
			{
				$error[] = "failed to add a category: @@nick_control@@";
			}
			else
			{
				if( ($parentid2 = category_add($parentid, 10, "p_nc_patterns", "@@nick_patterns@@", "", "nickControl")) == 0 )
				{
					$error[] = "failed to add a category: @@nick_patterns@@";
				}
				else
				{
					if( category_add($parentid2, 0, "p_nc_patterns_add", "@@nick_add_pattern@@", "", "nickControl") == 0 )
					{
						$error[] = "failed to add a category: @@nick_add_pattern@@";
					}
				}
			
				if( category_add($parentid, 30, "p_nc_logs", "@@nick_logs@@", "", "nickControl") == 0 )
				{
					$error[] = "failed to add a category: @@nick_logs@@";
				}
			}
		}
		else
		{
			$error[] = "the product of 'srvControl' is out";
		}
	
		if(empty($error))
		{
			$result = $db->Query("
			CREATE TABLE IF NOT EXISTS `acp_nick_patterns` (
				`id` int(11) NOT NULL auto_increment,
				`pattern` varchar(100) NOT NULL,
				`action` tinyint(1) NOT NULL default '0',
				PRIMARY KEY  (`id`),
				FULLTEXT KEY `pattern` (`pattern`)
			) ENGINE=MyISAM");
			
			$result = $db->Query("
			CREATE TABLE IF NOT EXISTS `acp_nick_logs` (
				`id` int(11) NOT NULL auto_increment,
				`serverip` varchar(32) NOT NULL default '',
				`timestamp` int(1) NOT NULL default '0',
				`name` varchar(100) NOT NULL default '',
				`authid` varchar(100) NOT NULL default '',
				`ip` varchar(100) NOT NULL default '',
				`pattern` varchar(4) NOT NULL default '',
				`action` varchar(4) NOT NULL default '',
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM");

			$result = $db->Query("INSERT INTO `acp_blocks` (`productid`, `title`, `description`, `link`, `display_order`) VALUES ('nickControl', '@@checknick_head@@', 'Verification tool nickname based on regular expressions that are defined in the admin', 'check_nick', '30')");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('nick_control', null, '@@help_chat_control@@', 'text', 'nickControl', '0')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('nick_control', 'nc_perm_patterns', '@@help_nc_perm_patterns@@', 'bitmask', 'nickControl', '10')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'nickControl' ORDER BY catleft LIMIT 1");
	
	if(!category_delete($result_category))
	{
		$error = "Error deleting product categories.";
	}
	else
	{
		$result = $db->Query("DELETE FROM `acp_blocks` WHERE productid = 'nickControl'");

		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'nickControl'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'nickControl'");
		$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
			LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'nickControl'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_nick_patterns`"); 
		$result = $db->Query("DROP TABLE IF EXISTS `acp_nick_logs`");
	}
}

?>
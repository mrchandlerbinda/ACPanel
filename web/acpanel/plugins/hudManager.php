<?php

// ###############################################################################
// Hud Manager version 1.2
// ###############################################################################

if( $product_install )
{
	$result_select = $db->Query("SELECT productid, version FROM `acp_products` WHERE productid = '$prd_id' LIMIT 1");

	if( is_array($result_select) )
	{
		foreach( $result_select as $obj )
		{
			$old_version = str_replace(".", "", $obj->version);
			$new_version = str_replace(".", "", $prd_version);
	
			if( $old_version > $new_version )
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
			if( ($parentid = category_add($result_category, 40, "p_hm_patterns", "@@hud_manager@@", "", "hudManager")) == 0 )
			{
				$error[] = "failed to add a category: @@hud_manager@@";
			}
			else
			{
				if( category_add($parentid, 0, "p_hm_patterns_add", "@@hud_add_pattern@@", "", "hudManager") == 0 )
				{
					$error[] = "failed to add a category: @@hud_add_pattern@@";
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
			CREATE TABLE IF NOT EXISTS `acp_hud_manager` (
				`hud_id` int(11) NOT NULL auto_increment,
				`name` varchar(64) NOT NULL,
				`flags` smallint(4) NOT NULL,
				`priority` int(11) NOT NULL,
				PRIMARY KEY  (`hud_id`)
			) ENGINE=MyISAM");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('hudm', null, '@@help_hudm@@', 'text', 'hudManager', '0')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('hudm', 'perm_hudm', '@@help_perm_hudm@@', 'bitmask', 'hudManager', '10')");

			if( $result_alter = $db->Query("ALTER TABLE `acp_servers` ADD `opt_hudmanager` TINYINT( 1 ) NOT NULL default '0'") )
				$result_insert = $db->Query("INSERT INTO `acp_servers_options` (varname, label, type, productid) VALUES ('opt_hudmanager', '@@opt_hudmanager@@', 'boolean', 'hudManager')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'hudManager' ORDER BY catleft LIMIT 1");
	
	if(!category_delete($result_category))
	{
		$error = "Error deleting product categories.";
	}
	else
	{
		if( $result = $db->Query("ALTER TABLE `acp_servers` DROP `opt_hudmanager`") )
			$result_delete = $db->Query("DELETE FROM `acp_servers_options` WHERE varname = 'opt_hudmanager'");

		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'hudManager'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'hudManager'");
		$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
			LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'hudManager'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_hud_manager`");
	}
}

?>
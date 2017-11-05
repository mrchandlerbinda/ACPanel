<?php

// ###############################################################################
// Task Sheduler version 1.3
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
	
			if( $old_version < 10 )
			{
				$error[] = "please update acpanel and try again";
			}
			elseif( $old_version > $new_version )
			{
				$error[] = "the product is installed and its version is newer or equal that you are installing";
			}
			else
			{

			}
		}
	}
	else
	{
		$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE title = '@@tools@@' LIMIT 1");

		if( $result_category )
		{
			if( ($parentid = category_add($result_category, 10, "task_sheduler", "@@task_sheduler@@", "", "taskSheduler")) == 0 )
			{
				$error[] = "failed to add a category: @@task_sheduler@@";
			}
			else
			{
				if( category_add($parentid, 0, "task_sheduler_add", "@@add_new_task@@", "", "taskSheduler") == 0 )
				{
					$error[] = "failed to add a category: @@add_new_task@@";
				}

				if( category_add($parentid, 0, "task_sheduler_edit", "@@edit_task@@", "", "taskSheduler") == 0 )
				{
					$error[] = "failed to add a category: @@edit_task@@";
				}
			}
		}
		else
		{
			$error[] = "the Tools is out";
		}

		if( empty($error) )
		{
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_cron_entry` (
				`entry_id` int(10) NOT NULL AUTO_INCREMENT,
				`cron_file` varchar(75) NOT NULL,
				`run_rules` varchar(255) NOT NULL,
				`active` tinyint(3) unsigned NOT NULL,
				`product_id` varchar(25) NOT NULL,
				`task_update` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`entry_id`),
				KEY `active_next_run` (`active`)
			) ENGINE=InnoDB");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_cron_log` (
				`logid` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`entry_id` int(10) NOT NULL DEFAULT '0',
				`dateline` int(10) unsigned NOT NULL DEFAULT '0',
				PRIMARY KEY (`logid`),
				KEY `entry_id` (`entry_id`)
			) ENGINE=InnoDB");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('tools', 'tools_perm_cron', '@@help_tools_perm_cron@@', 'bitmask', 'taskSheduler', '10')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'taskSheduler' ORDER BY catleft LIMIT 1");
	
	if( !category_delete($result_category) )
	{
		$error = "Error deleting product categories.";
	}
	else
	{
		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'taskSheduler'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'taskSheduler'");
		$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
			LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'taskSheduler'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_cron_entry`"); 
		$result = $db->Query("DROP TABLE IF EXISTS `acp_cron_log`"); 
	}
}

?>
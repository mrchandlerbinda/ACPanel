<?php

// ###############################################################################
// Chat Control version 3.6
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
	
			if($old_version < 26)
			{
				$error[] = "please update acpanel and try again";
			}
			elseif($old_version > $new_version)
			{
				$error[] = "the product is installed and its version is newer or equal that you are installing";
			}
			else
			{
				if( $old_version < 35 )
					$query = $db->Query("ALTER TABLE `acp_chat_logs` ADD uid INT(11) NOT NULL DEFAULT '0'");
			}
		}
	}
	else
	{
		$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'p_servers' LIMIT 1");
		
		if($result_category)
		{
			if( ($parentid = category_add($result_category, 10, "", "@@chat_control@@", "", "chatControl")) == 0 )
			{
				$error[] = "failed to add a category: @@chat_control@@";
			}
			else
			{
				if( ($parentid2 = category_add($parentid, 10, "p_cc_patterns", "@@chat_patterns@@", "", "chatControl")) == 0 )
				{
					$error[] = "failed to add a category: @@chat_patterns@@";
				}
				else
				{
					if( category_add($parentid2, 0, "p_cc_patterns_add", "@@chat_add_pattern@@", "", "chatControl") == 0 )
					{
						$error[] = "failed to add a category: @@chat_add_pattern@@";
					}
				}
			
				if( ($parentid2 = category_add($parentid, 20, "p_cc_commands", "@@chat_commands@@", "", "chatControl")) == 0 )
				{
					$error[] = "failed to add a category: @@chat_commands@@";
				}
				else
				{
					if( category_add($parentid2, 0, "p_cc_commands_add", "@@chat_add_command@@", "", "chatControl") == 0 )
					{
						$error[] = "failed to add a category: @@chat_add_command@@";
					}
				}
			
				if( category_add($parentid, 30, "p_cc_logs", "@@chat_logs@@", "", "chatControl") == 0 )
				{
					$error[] = "failed to add a category: @@chat_logs@@";
				}

				$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'homepage' LIMIT 1");
				if($result_category)
				{
					if( category_add($result_category, 30, "p_gamechat", "@@gamechat@@", "", "chatControl") == 0 )
					{
						$error[] = "failed to add a category: @@gamechat@@";
					}
				}
			}
		}
		else
		{
			$error[] = "the product of 'srvControl' is out";
		}
	
		if(empty($error))
		{
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_chat_patterns` (
				`id` int(11) NOT NULL auto_increment,
				`pattern` text NOT NULL,
				`action` tinyint(1) NOT NULL default '0',
				`reason` varchar(255) NOT NULL,
				`length` varchar(100) NOT NULL default '',
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM");
			
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_chat_nswords` (
				`id` int(11) NOT NULL auto_increment,
				`value` varchar(100) NOT NULL default '',
				PRIMARY KEY  (`id`),
				FULLTEXT KEY `value` (`value`)
			) ENGINE=MyISAM");
			
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_chat_logs` (
				`id` int(11) NOT NULL auto_increment,
				`serverip` varchar(32) NOT NULL default '',
				`name` varchar(100) NOT NULL default '',
				`authid` varchar(100) NOT NULL default '',
				`ip` varchar(100) NOT NULL default '',
				`alive` int(11) NOT NULL default '0',
				`team` varchar(100) NOT NULL default '',
				`timestamp` int(1) NOT NULL default '0',
				`cmd` varchar(100) NOT NULL default '',
				`foradmins` int(11) NOT NULL default '0',
				`pattern` int(11) NOT NULL default '0',
				`message` text NOT NULL,
				`uid` int(11) NOT NULL default '0',
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_cmd', 'say,say_team', '@@cc_cmd@@', 'checkbox', 'say|@@say@@\r\nsay_team|@@say_team@@\r\namx_chat|@@amx_chat@@', null, '@@help_cc_cmd@@', 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', null, '', '@@chat_control@@', 'text', '30', null, null, 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_foradmins', '0', '@@cc_foradmins@@', 'boolean', null, null, '@@help_cc_foradmins@@', 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_alive', '1', '@@cc_alive@@', 'boolean', null, null, '@@help_cc_alive@@', 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_servers', '', '@@cc_servers@@', 'select', 'acp_servers|address|hostname', 'multiple', '@@help_cc_servers@@', 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_limit', '0', '@@cc_limit@@', 'text', 'size=5', 'numeric', '@@help_cc_limit@@', 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_delay', '20', '@@cc_delay@@', 'text', 'size=5', 'numeric', '@@help_cc_delay@@', 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_block_msg', '0', '@@cc_block_msg@@', 'boolean', null, null, '@@help_cc_block_msg@@', 'chatControl')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('chat_control', 'cc_refresh', '0', '@@cc_refresh@@', 'text', null, null, '@@help_cc_refresh@@', 'chatControl')");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('chat_control', null, '@@help_chat_control@@', 'text', 'chatControl', '0')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('chat_control', 'cc_perm_patterns', '@@help_cc_perm_patterns@@', 'bitmask', 'chatControl', '10')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('chat_control', 'cc_perm_commands', '@@help_cc_perm_commands@@', 'bitmask', 'chatControl', '20')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'chatControl' ORDER BY catleft LIMIT 1");
	
	if(!category_delete($result_category))
	{
		$error = "Error deleting product categories.";
	}
	else
	{
		$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'p_gamechat'");
		if($result_category) category_delete($result_category);

		$result = $db->Query("DELETE FROM `acp_config` WHERE productid = 'chatControl'");
		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'chatControl'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'chatControl'");
		$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
			LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'chatControl'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_chat_patterns`"); 
		$result = $db->Query("DROP TABLE IF EXISTS `acp_chat_nswords`"); 
		$result = $db->Query("DROP TABLE IF EXISTS `acp_chat_logs`"); 
	}
}

?>
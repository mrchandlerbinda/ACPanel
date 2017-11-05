<?php

// ###############################################################################
// Game Bans version 1.2
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
		if( ($parentid = category_add(0, 50, "p_gamebans", "@@gamebans@@", "", "gameBans")) == 0 )
		{
			$error[] = "failed to add a category: @@gamebans@@";
		}
		else
		{
			if( ($parentid2 = category_add($parentid, 10, "p_gamebans_players", "@@gamebans_players@@", "", "gameBans")) == 0 )
			{
				$error[] = "failed to add a category: @@gamebans_players@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 0, "p_gamebans_players_add", "@@ban_edit@@", "", "gameBans")) == 0 )
				{
					$error[] = "failed to add a category: @@ban_edit@@";
				}

				if( ($parentid3 = category_add($parentid2, 0, "p_gamebans_players_edit", "@@add_ban@@", "", "gameBans")) == 0 )
				{
					$error[] = "failed to add a category: @@add_ban@@";
				}
			}

			if( ($parentid2 = category_add($parentid, 20, "p_gamebans_reasons", "@@ban_reasons@@", "", "gameBans")) == 0 )
			{
				$error[] = "failed to add a category: @@ban_reasons@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 0, "p_gamebans_reasons_add", "@@add_ban_reason@@", "", "gameBans")) == 0 )
				{
					$error[] = "failed to add a category: @@add_ban_reason@@";
				}
			}

			if( ($parentid2 = category_add($parentid, 30, "p_gamebans_subnets", "@@gamebans_subnets@@", "", "gameBans")) == 0 )
			{
				$error[] = "failed to add a category: @@gamebans_subnets@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 0, "p_gamebans_subnets_add", "@@add_subnet@@", "", "gameBans")) == 0 )
				{
					$error[] = "failed to add a category: @@add_subnet@@";
				}

				if( ($parentid3 = category_add($parentid2, 0, "p_gamebans_subnets_edit", "@@edit_subnet@@", "", "gameBans")) == 0 )
				{
					$error[] = "failed to add a category: @@edit_subnet@@";
				}
			}

			if( ($parentid2 = category_add($parentid, 40, "p_gamebans_search", "@@bans_search@@", "", "gameBans")) == 0 )
			{
				$error[] = "failed to add a category: @@bans_search@@";
			}

			$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'homepage' LIMIT 1");
			if( $result_category )
			{
				if( ($parentid2 = category_add($result_category, 30, "", "@@gb_banlist@@", "", "gameBans")) == 0 )
				{
					$error[] = "failed to add a category: @@gb_banlist@@";
				}
				else
				{
					if( ($parentid3 = category_add($parentid2, 10, "p_gamebans_public_players", "@@gamebans_players@@", "", "gameBans")) == 0 )
					{
						$error[] = "failed to add a category: @@gamebans_players@@";
					}
	
					if( ($parentid3 = category_add($parentid2, 20, "p_gamebans_public_subnets", "@@gamebans_subnets@@", "", "gameBans")) == 0 )
					{
						$error[] = "failed to add a category: @@gamebans_subnets@@";
					}

					if( ($parentid3 = category_add($parentid2, 30, "p_gamebans_public_stats", "@@gb_banlist_stats@@", "", "gameBans")) == 0 )
					{
						$error[] = "failed to add a category: @@gb_banlist_stats@@";
					}
				}
			}

			if( $result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'p_optimization' LIMIT 1") )
			{
				if( ($parentid2 = category_add($result_category, 0, "optimization/bans_prune", "@@bans_prune@@", "", "gameBans")) == 0 )
				{
					$error[] = "failed to add a category: @@bans_prune@@";
				}
			}
		}

		if( empty($error) )
		{
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_bans` (
				`bid` int(11) NOT NULL AUTO_INCREMENT,
				`player_ip` varchar(100) CHARACTER SET cp1251 DEFAULT NULL,
				`player_id` varchar(50) CHARACTER SET cp1251 NOT NULL DEFAULT '0',
				`player_nick` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT 'Unknown',
				`cookie_ip` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`admin_ip` varchar(100) CHARACTER SET cp1251 DEFAULT NULL,
				`admin_id` varchar(50) CHARACTER SET cp1251 NOT NULL DEFAULT '0',
				`admin_nick` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT 'Unknown',
				`admin_uid` int(11) NOT NULL,
				`ban_type` varchar(10) CHARACTER SET cp1251 NOT NULL DEFAULT 'S',
				`ban_reason` varchar(255) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`ban_created` int(11) NOT NULL DEFAULT '0',
				`ban_length` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`server_ip` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`server_name` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT 'Unknown',
				PRIMARY KEY (`bid`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_bans_history` (
				`bid` int(11) NOT NULL,
				`player_ip` varchar(100) CHARACTER SET cp1251 DEFAULT NULL,
				`player_id` varchar(50) CHARACTER SET cp1251 NOT NULL DEFAULT '0',
				`player_nick` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT 'Unknown',
				`cookie_ip` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`admin_ip` varchar(100) CHARACTER SET cp1251 DEFAULT NULL,
				`admin_id` varchar(50) CHARACTER SET cp1251 NOT NULL DEFAULT '0',
				`admin_nick` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT 'Unknown',
				`admin_uid` int(11) NOT NULL DEFAULT '0',
				`ban_type` varchar(10) CHARACTER SET cp1251 NOT NULL DEFAULT 'S',
				`ban_reason` varchar(255) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`ban_created` int(11) NOT NULL DEFAULT '0',
				`ban_length` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`server_ip` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT '',
				`server_name` varchar(100) CHARACTER SET cp1251 NOT NULL DEFAULT 'Unknown',
				`unban_created` int(11) NOT NULL DEFAULT '0',
				`unban_reason` varchar(255) NOT NULL DEFAULT 'prune',
				`unban_admin_uid` int(11) NOT NULL DEFAULT '0',
				UNIQUE KEY `bid` (`bid`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_bans_reasons` (
				`id` int(12) NOT NULL auto_increment,
				`address` varchar(32) NOT NULL,
				`reason` varchar(250) NOT NULL,
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM");

			$result = $db->Query("
			CREATE TABLE IF NOT EXISTS `acp_bans_subnets` (
				`id` int(11) NOT NULL auto_increment,
				`subipaddr` varchar(32) NOT NULL,
				`bitmask` varchar(2) NOT NULL,
				`comment` text NOT NULL,
				`approved` tinyint(1) NOT NULL default '0',
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', null, '', '@@gamebans@@', 'text', '80', null, null, 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_length_format', 'mmmm', '@@gb_length_format@@', 'text', '', '', '@@help_gb_length_format@@', 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_view_per_page', '30', '@@gb_view_per_page@@', 'text', 'size=5', 'numeric', '@@help_gb_view_per_page@@', 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_display_admin', '0', '@@gb_display_admin@@', 'boolean', null, null, '@@help_gb_display_admin@@', 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_bans_select', '0', '@@gb_bans_select@@', 'select', '0|@@gb_bans_all@@\r\n1|@@gb_bans_active@@\r\n2|@@gb_bans_passed@@', '', '@@help_gb_bans_select@@', 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_cache_block_stats', '600', '@@gb_cache_block_stats@@', 'text', 'size=5', 'numeric', '@@help_gb_cache_block_stats@@', 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_topstats_max', '5', '@@gb_topstats_max@@', 'text', 'size=5', 'numeric', '@@help_gb_topstats_max@@', 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_topstats_cache', '60', '@@gb_topstats_cache@@', 'text', 'size=5', 'numeric', '@@help_gb_topstats_cache@@', 'gameBans')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_bans', 'gb_block_admins_max', '5', '@@gb_block_admins_max@@', 'text', 'size=5', 'numeric', '@@help_gb_block_admins_max@@', 'gameBans')");

			$result = $db->Query("INSERT INTO `acp_blocks` (`productid`, `title`, `description`, `link`, `display_order`) VALUES ('gameBans', '@@block_bans_stats@@', 'Summary statistics for bans', 'bans_stats', '10')");
			$result = $db->Query("INSERT INTO `acp_blocks` (`productid`, `title`, `description`, `link`, `display_order`) VALUES ('gameBans', '@@block_bans_best_admin@@', 'List of the best admins today', 'bans_best_admin', '20')");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_bans', null, '@@help_game_bans@@', 'text', 'gameBans', '0')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_bans', 'gb_perm_players', '@@help_gb_perm_players@@', 'bitmask', 'gameBans', '10')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_bans', 'gb_perm_players_my', '@@help_gb_perm_players_my@@', 'bitmask', 'gameBans', '20')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_bans', 'gb_perm_reasons', '@@help_gb_perm_reasons@@', 'bitmask', 'gameBans', '30')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_bans', 'gb_perm_subnets', '@@help_gb_perm_subnets@@', 'bitmask', 'gameBans', '40')");

			if( $result_alter = $db->Query("ALTER TABLE `acp_servers` ADD `opt_bansubnets` TINYINT( 1 ) NOT NULL default '0'") )
				$result_insert = $db->Query("INSERT INTO `acp_servers_options` (varname, label, type, productid) VALUES ('opt_bansubnets', '@@opt_bansubnets@@', 'boolean', 'gameBans')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'gameBans' ORDER BY catleft LIMIT 1");
	
	if( !category_delete($result_category) )
	{
		$error = "Error deleting product categories.";
	}
	else
	{
		if( $result = $db->Query("ALTER TABLE `acp_servers` DROP `opt_bansubnets`") )
			$result_delete = $db->Query("DELETE FROM `acp_servers_options` WHERE varname = 'opt_bansubnets'");

		$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'gameBans' AND sectionid = 1 ORDER BY catleft LIMIT 1");
		if( $result_category ) category_delete($result_category);

		$result = $db->Query("DELETE FROM `acp_config` WHERE productid = 'gameBans'");
		$result = $db->Query("DELETE FROM `acp_blocks` WHERE productid = 'gameBans'");
		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'gameBans'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'gameBans'");
		$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
			LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'gameBans'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_bans`"); 
		$result = $db->Query("DROP TABLE IF EXISTS `acp_bans_history`"); 
		$result = $db->Query("DROP TABLE IF EXISTS `acp_bans_reasons`"); 
		$result = $db->Query("DROP TABLE IF EXISTS `acp_bans_subnets`"); 
	}
}

?>
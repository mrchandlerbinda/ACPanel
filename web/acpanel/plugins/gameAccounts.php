<?php

// ###############################################################################
// Game Accounts version 1.3
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
		if( ($parentid = category_add(0, 40, "p_gamecp", "@@game_accounts@@", "", "gameAccounts")) == 0 )
		{
			$error[] = "failed to add a category: @@game_accounts@@";
		}
		else
		{
			if( ($parentid2 = category_add($parentid, 10, "p_gamecp_accounts", "@@user_accounts@@", "", "gameAccounts")) == 0 )
			{
				$error[] = "failed to add a category: @@user_accounts@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 0, "p_gamecp_accounts_add", "@@add_account@@", "", "gameAccounts")) == 0 )
				{
					$error[] = "failed to add a category: @@add_account@@";
				}

				if( ($parentid3 = category_add($parentid2, 0, "p_gamecp_accounts_edit", "@@edit_account@@", "", "gameAccounts")) == 0 )
				{
					$error[] = "failed to add a category: @@edit_account@@";
				}
			}

			if( ($parentid2 = category_add($parentid, 20, "p_gamecp_requests", "@@user_requests@@", "", "gameAccounts")) == 0 )
			{
				$error[] = "failed to add a category: @@user_requests@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 0, "p_gamecp_requests_edit", "@@user_requests_edit@@", "", "gameAccounts")) == 0 )
				{
					$error[] = "failed to add a category: @@user_requests_edit@@";
				}
			}

			if( ($parentid2 = category_add($parentid, 30, "p_gamecp_mask", "@@access_mask@@", "", "gameAccounts")) == 0 )
			{
				$error[] = "failed to add a category: @@access_mask@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 0, "p_gamecp_mask_add", "@@access_mask_add@@", "", "gameAccounts")) == 0 )
				{
					$error[] = "failed to add a category: @@access_mask_add@@";
				}

				if( ($parentid3 = category_add($parentid2, 0, "p_gamecp_mask_edit", "@@access_mask_edit@@", "", "gameAccounts")) == 0 )
				{
					$error[] = "failed to add a category: @@access_mask_edit@@";
				}
			}

			if( category_add($parentid, 40, "p_gamecp_search", "@@ga_search@@", "", "gameAccounts") == 0 )
			{
				$error[] = "failed to add a category: @@ga_search@@";
			}
		}
	
		if( empty($error) )
		{
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_players` (
				`userid` int(12) NOT NULL,
				`flag` tinyint(4) NOT NULL DEFAULT '0',
				`player_nick` varchar(32) NOT NULL DEFAULT '',
				`password` varchar(60) NOT NULL DEFAULT '',
				`player_ip` varchar(60) NOT NULL DEFAULT '',
				`steamid` varchar(32) NOT NULL DEFAULT '',
				`timestamp` int(1) NOT NULL DEFAULT '0',
				`last_time` int(1) NOT NULL DEFAULT '0',
				`approved` enum('yes','no') NOT NULL DEFAULT 'yes',
				`online` int(11) NOT NULL DEFAULT '0',
				`points` int(11) NOT NULL DEFAULT '0',
				UNIQUE KEY `userid` (`userid`),
				KEY `flag` (`flag`),
				FULLTEXT KEY `player_nick` (`player_nick`),
				FULLTEXT KEY `password` (`password`),
				FULLTEXT KEY `player_ip` (`player_ip`),
				FULLTEXT KEY `steamid` (`steamid`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_players_requests` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`userid` int(12) NOT NULL,
				`timestamp` int(11) NOT NULL DEFAULT '0',
				`ticket_type` tinyint(4) NOT NULL,
				`productid` varchar(25) NOT NULL,
				`fields_update` text NOT NULL,
				`ticket_status` tinyint(4) NOT NULL DEFAULT '0',
				`closed_time` int(11) NOT NULL DEFAULT '0',
				`closed_admin` varchar(32) NOT NULL DEFAULT '',
				`comment` varchar(255) NOT NULL,
				PRIMARY KEY (`id`),
				KEY `ticket_status` (`ticket_status`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_ticket_type` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`label` varchar(100) NOT NULL,
				`varname` varchar(64) NOT NULL DEFAULT '',
				`productid` varchar(25) NOT NULL DEFAULT '',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_access_mask` (
				`mask_id` int(11) NOT NULL AUTO_INCREMENT,
				`access_flags` varchar(128) NOT NULL DEFAULT '',
				PRIMARY KEY (`mask_id`),
				FULLTEXT KEY `access` (`access_flags`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_access_mask_players` (
				`userid` int(11) NOT NULL,
				`mask_id` int(11) NOT NULL,
				`access_expired` int(11) NOT NULL DEFAULT '0',
				UNIQUE KEY `userid` (`userid`,`mask_id`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_access_mask_servers` (
				`mask_id` int(11) NOT NULL,
				`server_id` int(11) NOT NULL,
				UNIQUE KEY `mask` (`mask_id`,`server_id`)
			) ENGINE=MyISAM");

			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('player_nick', '@@t_create_acc_by_nick@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('player_ip', '@@t_create_acc_by_ip@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('steamid', '@@t_create_acc_by_steam@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('player_nick', '@@t_change_nick@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('player_ip', '@@t_change_ip@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('steamid', '@@t_change_steam@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('player_nick', '@@t_change_auth_nick@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('player_ip', '@@t_change_auth_ip@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_ticket_type` (varname, label, productid) VALUES ('steamid', '@@t_change_auth_steam@@', 'gameAccounts')");

			$result = $db->Query("INSERT INTO `acp_access_mask` (mask_id, access_flags) VALUES ('1', 't')");
			$result = $db->Query("INSERT INTO `acp_access_mask_servers` (mask_id, server_id) VALUES (".$db->LastInsertID().", '0')");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', null, '', '@@game_accounts@@', 'text', '60', null, null, 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'default_access', '1', '@@default_access@@', 'select', 'acp_access_mask|mask_id|access_flags', 'select', '@@help_default_access@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_nicklen_max', '25', '@@ga_nicklen_max@@', 'text', 'size=5', 'numeric', '@@help_ga_nicklen_max@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_nicklen_min', '3', '@@ga_nicklen_min@@', 'size=5', '', 'numeric', '@@help_ga_nicklen_min@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ticket_moderate', '1', '@@ticket_moderate@@', 'boolean', null, null, '@@help_ticket_moderate@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_time_format', 'dddd hhhh mmmm ssss', '@@ga_time_format@@', 'text', null, 'numeric', '@@help_ga_time_format@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_access_type', 'by_nick,by_ip,by_steam', '@@ga_access_type@@', 'checkbox', 'by_nick|@@type_by_nick@@\r\nby_ip|@@type_by_ip@@\r\nby_steam|@@type_by_steam@@', null, '@@help_ga_access_type@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_registration', '2', '@@ga_registration@@', 'select', '1|@@ga_reg_closed@@\r\n2|@@ga_reg_site@@\r\n3|@@ga_reg_soft@@', null, '@@help_ga_registration@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_cache_block_accounts', '60', '@@ga_cache_block_accounts@@', 'text', 'size=5', 'numeric', '@@help_ga_cache_block_accounts@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_active_time', '7', '@@ga_active_time@@', 'text', 'size=5', 'numeric', '@@help_ga_active_time@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_admin_flag', 'a', '@@ga_admin_flag@@', 'select', '|@@ga_flag_ignore@@\r\na|Flag \"a\", immunity\r\nb|Flag \"b\", reservation\r\nc|Flag \"c\", kick\r\nd|Flag \"d\", ban\r\ne|Flag \"e\", slay\r\nf|Flag \"f\", map change\r\ng|Flag \"g\", cvar change\r\nh|Flag \"h\", config execution\r\ni|Flag \"i\", chat\r\nj|Flag \"j\", vote\r\nk|Flag \"k\", sv_password\r\nl|Flag \"l\", rcon access\r\nm|Flag \"m\", custom\r\nn|Flag \"n\", custom\r\no|Flag \"o\", custom\r\np|Flag \"p\", custom\r\nq|Flag \"q\", custom\r\nr|Flag \"r\", custom\r\ns|Flag \"s\", custom\r\nt|Flag \"t\", custom\r\nu|Flag \"u\", custom', null, '@@help_ga_admin_flag@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_steam_validate', '', '@@ga_steam_validate@@', 'text', null, null, '@@help_ga_steam_validate@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'default_access_time', '', '@@default_access_time@@', 'text', 'size=5', 'numeric', '@@help_default_access_time@@', 'gameAccounts')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_accounts', 'ga_password_validate', '', '@@ga_password_validate@@', 'text', null, null, '@@help_ga_password_validate@@', 'gameAccounts')");

			$result = $db->Query("INSERT INTO `acp_blocks` (`productid`, `title`, `description`, `link`, `display_order`) VALUES ('gameAccounts', '@@block_accounts_stats@@', 'Summary statistics for game accounts', 'accounts_stats', '20')");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_accounts', null, '@@help_game_accounts@@', 'text', 'gameAccounts', '0')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_accounts', 'ga_perm_players', '@@help_ga_perm_players@@', 'bitmask', 'gameAccounts', '10')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('game_accounts', 'ga_perm_masks', '@@help_ga_perm_masks@@', 'bitmask', 'gameAccounts', '20')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('tickets', '', '@@help_tickets@@', 'text', 'ACPanel', '0') ON DUPLICATE KEY UPDATE description = '@@help_tickets@@'");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('tickets', 'perm_tickets', '@@help_perm_tickets@@', 'bitmask', 'ACPanel', '10') ON DUPLICATE KEY UPDATE description = '@@help_perm_tickets@@'");

			if( $result_alter = $db->Query("ALTER TABLE `acp_servers` ADD `opt_accounts` TINYINT( 1 ) NOT NULL default '0'") )
				$result_insert = $db->Query("INSERT INTO `acp_servers_options` (varname, label, type, productid) VALUES ('opt_accounts', '@@opt_accounts@@', 'boolean', 'gameAccounts')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'gameAccounts' ORDER BY catleft LIMIT 1");
	
	if( !category_delete($result_category) )
	{
		$error = "Error deleting product categories.";
	}
	else
	{
		if( $result = $db->Query("ALTER TABLE `acp_servers` DROP `opt_accounts`") )
			$result_delete = $db->Query("DELETE FROM `acp_servers_options` WHERE varname = 'opt_accounts'");
	
		$result = $db->Query("DELETE FROM `acp_config` WHERE productid = 'gameAccounts'");
		$result = $db->Query("DELETE FROM `acp_blocks` WHERE productid = 'gameAccounts'");
		$result = $db->Query("DELETE FROM `acp_ticket_type` WHERE productid = 'gameAccounts'");
		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'gameAccounts'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'gameAccounts'");
		$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
			LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'gameAccounts'	");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_players`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_players_requests`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_access_mask`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_access_mask_players`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_access_mask_servers`");
	}
}

?>
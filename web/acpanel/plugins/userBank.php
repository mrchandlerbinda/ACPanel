<?php

// ###############################################################################
// ACP Shop version 1.2
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
			else
			{
				$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
					VALUES ('user_bank', 'ub_methods', 'robokassa', '@@ub_methods@@', 'checkbox', 'robokassa|@@method_robokassa@@\r\na1pay|@@method_apay@@', '', '@@help_ub_methods@@', 'userBank') 
					ON DUPLICATE KEY UPDATE options = 'robokassa|@@method_robokassa@@\r\na1pay|@@method_apay@@'
				");

				$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
					VALUES ('user_bank', 'ub_apay_memo', '', '@@ub_apay_memo@@', 'text', null, null, '@@help_ub_apay_memo@@', 'userBank')
					ON DUPLICATE KEY UPDATE productid = 'userBank'
				");

				$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
					VALUES ('user_bank', 'ub_apay_key', '', '@@ub_apay_key@@', 'text', null, null, '@@help_ub_apay_key@@', 'userBank')
					ON DUPLICATE KEY UPDATE productid = 'userBank'
				");

				$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
					VALUES ('user_bank', 'ub_apay_secretkey', '', '@@ub_apay_secretkey@@', 'text', null, null, '@@help_ub_apay_secretkey@@', 'userBank')
					ON DUPLICATE KEY UPDATE productid = 'userBank'
				");

				$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
					VALUES ('user_bank', 'ub_apay_merchant_url', 'https://partner.a1pay.ru/a1lite/input/', '@@ub_apay_merchant_url@@', 'text', null, null, '@@help_ub_apay_merchant_url@@', 'userBank')
					ON DUPLICATE KEY UPDATE productid = 'userBank'
				");

				$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'p_gameshop_items' LIMIT 1");
				
				if(!$result_category)
				{
					$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'p_usershop_admin' LIMIT 1");

					if($result_category)
					{
						if( ($parentid = category_add($result_category, 30, "p_gameshop_items", "@@game_shop@@", "", "userBank")) == 0 )
						{
							$error[] = "failed to add a category: @@game_shop@@";
						}
						else
						{
							if( ($parentid2 = category_add($parentid, 0, "p_gameshop_items_servers", "@@game_shop_servers@@", "", "userBank")) == 0 )
							{
								$error[] = "failed to add a category: @@game_shop_servers@@";
							}

							if( ($parentid2 = category_add($parentid, 0, "p_gameshop_items_edit", "@@gameshop_items_edit@@", "", "userBank")) == 0 )
							{
								$error[] = "failed to add a category: @@gameshop_items_edit@@";
							}

							if( ($parentid2 = category_add($parentid, 0, "p_gameshop_items_add", "@@gameshop_items_add@@", "", "userBank")) == 0 )
							{
								$error[] = "failed to add a category: @@gameshop_items_add@@";
							}
						}
					}
				}
			}
		}
	}
	else
	{
		if( ($parentid = category_add(0, 60, "p_usershop_admin", "@@usershop_manage@@", "", "userBank")) == 0 )
		{
			$error[] = "failed to add a category: @@usershop_manage@@";
		}
		else
		{
			if( ($parentid2 = category_add($parentid, 10, "p_usershop_admin_payments", "@@payment_privileges@@", "", "userBank")) == 0 )
			{
				$error[] = "failed to add a category: @@payment_privileges@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 20, "p_usershop_admin_patterns", "@@usershop_admin_patterns@@", "", "userBank")) == 0 )
				{
					$error[] = "failed to add a category: @@usershop_admin_patterns@@";
				}
				else
				{
					if( ($parentid4 = category_add($parentid3, 0, "p_usershop_admin_patterns_add", "@@usershop_admin_patterns_add@@", "", "userBank")) == 0 )
					{
						$error[] = "failed to add a category: @@usershop_admin_patterns_add@@";
					}

					if( ($parentid4 = category_add($parentid3, 0, "p_usershop_admin_patterns_edit", "@@usershop_admin_patterns_edit@@", "", "userBank")) == 0 )
					{
						$error[] = "failed to add a category: @@usershop_admin_patterns_edit@@";
					}
				}

				if( ($parentid3 = category_add($parentid2, 10, "p_usershop_admin_groups", "@@usershop_admin_groups@@", "", "userBank")) == 0 )
				{
					$error[] = "failed to add a category: @@usershop_admin_groups@@";
				}
				else
				{
					if( ($parentid4 = category_add($parentid3, 0, "p_usershop_admin_groups_add", "@@usershop_admin_groups_add@@", "", "userBank")) == 0 )
					{
						$error[] = "failed to add a category: @@usershop_admin_groups_add@@";
					}

					if( ($parentid4 = category_add($parentid3, 0, "p_usershop_admin_groups_edit", "@@usershop_admin_groups_edit@@", "", "userBank")) == 0 )
					{
						$error[] = "failed to add a category: @@usershop_admin_groups_edit@@";
					}
				}

				if( ($parentid3 = category_add($parentid2, 30, "p_usershop_admin_patterns_user", "@@payment_user_privileges@@", "", "userBank")) == 0 )
				{
					$error[] = "failed to add a category: @@payment_user_privileges@@";
				}
				else
				{
					if( ($parentid4 = category_add($parentid3, 0, "p_usershop_admin_patterns_user_detail", "@@usershop_admin_patterns_user_detail@@", "", "userBank")) == 0 )
					{
						$error[] = "failed to add a category: @@usershop_admin_patterns_user_detail@@";
					}

					if( ($parentid4 = category_add($parentid3, 0, "p_usershop_profile_privilege_detail", "@@usershop_profile_privilege_detail@@", "", "userBank")) == 0 )
					{
						$error[] = "failed to add a category: @@usershop_profile_privilege_detail@@";
					}
				}
			}

			if( ($parentid2 = category_add($parentid, 20, "p_usershop_admin_payments", "@@usershop_admin_payments@@", "", "userBank")) == 0 )
			{
				$error[] = "failed to add a category: @@usershop_admin_payments@@";
			}

			if( ($parentid2 = category_add($parentid, 30, "p_gameshop_items", "@@game_shop@@", "", "userBank")) == 0 )
			{
				$error[] = "failed to add a category: @@game_shop@@";
			}
			else
			{
				if( ($parentid3 = category_add($parentid2, 0, "p_gameshop_items_servers", "@@game_shop_servers@@", "", "userBank")) == 0 )
				{
					$error[] = "failed to add a category: @@game_shop_servers@@";
				}

				if( ($parentid3 = category_add($parentid2, 0, "p_gameshop_items_edit", "@@gameshop_items_edit@@", "", "userBank")) == 0 )
				{
					$error[] = "failed to add a category: @@gameshop_items_edit@@";
				}

				if( ($parentid3 = category_add($parentid2, 0, "p_gameshop_items_add", "@@gameshop_items_add@@", "", "userBank")) == 0 )
				{
					$error[] = "failed to add a category: @@gameshop_items_add@@";
				}
			}

			$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'homepage' LIMIT 1");
			if( $result_category )
			{
				if( ($parentid = category_add(0, 0, "p_usershop", "@@usershop@@", "", "userBank")) == 0 )
				{
					$error[] = "failed to add a category: @@usershop@@";
				}
				else
				{
					if( ($parentid2 = category_add($parentid, 0, "p_usershop_buywindow", "@@usershop_buywindow@@", "", "userBank")) == 0 )
					{
						$error[] = "failed to add a category: @@usershop_buywindow@@";
					}
				}
			}
		}

		if( empty($error) )
		{
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_payment` (
				`pid` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`uid` int(11) unsigned NOT NULL DEFAULT '0',
				`amount` decimal(12,6) NOT NULL DEFAULT '0.000000',
				`created` int(11) unsigned NOT NULL DEFAULT '0',
				`memo` varchar(255) NOT NULL DEFAULT '',
				`enrolled` int(11) unsigned NOT NULL DEFAULT '0',
				`error` varchar(255) NOT NULL DEFAULT '',
				`params` text NOT NULL,
				`currency` enum('mm','points') NOT NULL DEFAULT 'mm',
				`pattern` int(11) NOT NULL DEFAULT '-1',
				PRIMARY KEY (`pid`)
			) ENGINE=InnoDB");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_payment_groups` (
				`gid` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(250) NOT NULL DEFAULT '',
				`description` text NOT NULL,
				PRIMARY KEY (`gid`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_payment_groups_patterns` (
				`gid` int(11) NOT NULL,
				`pattern_id` int(11) NOT NULL,
				PRIMARY KEY (`gid`,`pattern_id`),
				KEY `gid` (`gid`) USING BTREE
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_payment_patterns` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(250) NOT NULL DEFAULT '',
				`description` text NOT NULL,
				`image` int(11) NOT NULL DEFAULT '0',
				`price_mm` int(11) NOT NULL DEFAULT '0',
				`price_points` int(11) NOT NULL DEFAULT '0',
				`duration_type` enum('date','year','month','day') NOT NULL DEFAULT 'day',
				`item_duration` int(11) NOT NULL DEFAULT '0',
				`item_duration_select` tinyint(1) NOT NULL DEFAULT '1',
				`max_sale_items` int(11) NOT NULL DEFAULT '0',
				`max_sale_items_duration` enum('month','week','total','day') NOT NULL DEFAULT 'total',
				`max_sale_for_user` int(11) NOT NULL DEFAULT '0',
				`max_sale_for_user_duration` enum('total','month','week','day') NOT NULL DEFAULT 'total',
				`new_usergroup_id` int(11) NOT NULL DEFAULT '0',
				`enable_server_select` tinyint(1) NOT NULL DEFAULT '1',
				`add_flags` varchar(128) NOT NULL DEFAULT '',
				`add_points` int(11) NOT NULL DEFAULT '0',
				`do_php_exec` text NOT NULL,
				`purchased` int(11) NOT NULL DEFAULT '0',
				`active` tinyint(1) NOT NULL DEFAULT '1',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_payment_patterns_server` (
				`pattern_id` int(11) NOT NULL,
				`server_id` int(11) NOT NULL,
				UNIQUE KEY `pattern` (`pattern_id`,`server_id`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_payment_patterns_usergroups` (
				`pattern_id` int(11) NOT NULL,
				`usergroup_id` int(11) NOT NULL,
				UNIQUE KEY `pattern` (`pattern_id`,`usergroup_id`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_payment_user` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`uid` int(11) NOT NULL,
				`pattern_id` int(11) NOT NULL,
				`date_start` int(11) NOT NULL DEFAULT '0',
				`date_end` int(11) NOT NULL DEFAULT '0',
				`add_mask_id` int(11) NOT NULL DEFAULT '0',
				`new_group` int(11) NOT NULL DEFAULT '0',
				`params` text NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_gameshop` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`game_descr` varchar(32) NOT NULL DEFAULT '',
				`web_descr` varchar(40) NOT NULL DEFAULT '',
				`cost` int(11) NOT NULL DEFAULT '0',
				`duration` int(11) NOT NULL DEFAULT '1',
				`cmd` int(11) NOT NULL DEFAULT '0',
				`active` tinyint(4) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_gameshop_servers` (
				`item_id` int(11) NOT NULL,
				`server_id` int(11) NOT NULL,
				UNIQUE KEY `item` (`item_id`,`server_id`)
			) ENGINE=MyISAM");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', null, '', '@@user_bank@@', 'text', '100', null, null, 'userBank')");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
				VALUES ('user_bank', 'ub_methods', 'robokassa', '@@ub_methods@@', 'checkbox', 'robokassa|@@method_robokassa@@\r\na1pay|@@method_apay@@', '', '@@help_ub_methods@@', 'userBank') 
				ON DUPLICATE KEY UPDATE options = 'robokassa|@@method_robokassa@@\r\na1pay|@@method_apay@@'
			");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_min_payment', '100', '@@ub_min_payment@@', 'text', null, 'numeric', '@@help_ub_min_payment@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_currency_suffix', 'MM', '@@ub_currency_suffix@@', 'text', null, null, '@@help_ub_currency_suffix@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_pagesize', '5', '@@ub_pagesize@@', 'text', 'size=5', 'numeric', '@@help_ub_pagesize@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_rate_points', '0.5', '@@ub_rate_points@@', 'text', null, 'numeric', '@@help_ub_rate_points@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_commission_exchanger', '5', '@@ub_commission_exchanger@@', 'text', null, null, '@@help_ub_commission_exchanger@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_robo_login', '', '@@ub_robo_login@@', 'text', null, null, '@@help_ub_robo_login@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_robo_password_one', '', '@@ub_robo_password_one@@', 'text', null, null, '@@help_ub_robo_password_one@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_robo_password_two', '', '@@ub_robo_password_two@@', 'text', null, null, '@@help_ub_robo_password_two@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_robo_merchant_url', 'http://test.robokassa.ru/Index.aspx', '@@ub_robo_merchant_url@@', 'text', null, null, '@@help_ub_robo_merchant_url@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_robo_default_currency', 'BANKOCEAN2R', '@@ub_robo_default_currency@@', 'text', null, null, '@@help_ub_robo_default_currency@@', 'userBank')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('user_bank', 'ub_robo_memo', '', '@@ub_robo_memo@@', 'text', null, null, '@@help_ub_robo_memo@@', 'userBank')");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
				VALUES ('user_bank', 'ub_apay_memo', '', '@@ub_apay_memo@@', 'text', null, null, '@@help_ub_apay_memo@@', 'userBank')
				ON DUPLICATE KEY UPDATE productid = 'userBank'
			");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
				VALUES ('user_bank', 'ub_apay_key', '', '@@ub_apay_key@@', 'text', null, null, '@@help_ub_apay_key@@', 'userBank')
				ON DUPLICATE KEY UPDATE productid = 'userBank'
			");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
				VALUES ('user_bank', 'ub_apay_secretkey', '', '@@ub_apay_secretkey@@', 'text', null, null, '@@help_ub_apay_secretkey@@', 'userBank')
				ON DUPLICATE KEY UPDATE productid = 'userBank'
			");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) 
				VALUES ('user_bank', 'ub_apay_merchant_url', 'https://partner.a1pay.ru/a1lite/input/', '@@ub_apay_merchant_url@@', 'text', null, null, '@@help_ub_apay_merchant_url@@', 'userBank')
				ON DUPLICATE KEY UPDATE productid = 'userBank'
			");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('user_bank', null, '@@help_user_bank@@', 'text', 'userBank', '0')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('user_bank', 'ub_perm_payment', '@@help_ub_perm_payment@@', 'bitmask', 'userBank', '10')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('main', 'weight', '@@help_weight@@', 'text', 'ACPanel', '5')");

			$result_alter = $db->Query("ALTER TABLE `acp_users` ADD `money` DECIMAL(18,2) NOT NULL default '0.00', ADD `real_groupid` INT(11) NOT NULL default '0'");
			$result_alter = $db->Query("ALTER TABLE `acp_usergroups` ADD `weight` INT(10) NOT NULL default '0'");
		}
	}
}
else
{
	if( $result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'userBank' ORDER BY catleft LIMIT 1") )
	{
		if( !category_delete($result_category) )
		{
			$error = "Error deleting product categories.";
		}
		else
		{
			if( $result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'userBank' ORDER BY catleft LIMIT 1") )
			{
				if( !category_delete($result_category) )
				{
					$error = "Error deleting product categories.";
				}
				else
				{
					$result = $db->Query("ALTER TABLE `acp_users` DROP `money`, DROP `real_groupid`");
		
					$result = $db->Query("DELETE FROM `acp_config` WHERE productid = 'userBank'");
					$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'userBank'");
					$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'userBank'");
					$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
						LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'userBank'");
		
					$result = $db->Query("DROP TABLE IF EXISTS `acp_gameshop`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_gameshop_servers`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_payment`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_payment_groups`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_payment_groups_patterns`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_payment_patterns`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_payment_patterns_server`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_payment_patterns_usergroups`");
					$result = $db->Query("DROP TABLE IF EXISTS `acp_payment_user`");
				}
			}
			else
			{
				$error = "Not found category.";
			}
		}
	}
	else
	{
		$error = "Not found category.";
	}
}

?>
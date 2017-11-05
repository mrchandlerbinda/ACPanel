<?php

// ###############################################################################
// Rating Servers version 1.0
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
		if( ($parentid = category_add(1, 0, "p_server_card", "@@server_card@@", "", "ratingServers")) == 0 )
		{
			$error[] = "failed to add a category: @@server_card@@";
		}
	
		if( empty($error) )
		{
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_servers_rating_temp` (
				`server_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`updated` tinyint(1) NOT NULL DEFAULT '0',
				`server_position` int(11) NOT NULL DEFAULT '0',
				`server_rating` int(11) NOT NULL DEFAULT '0',
				`server_rating_vars` text NOT NULL,
				`server_votes_up` int(11) NOT NULL DEFAULT '0',
				`server_votes_down` int(11) NOT NULL DEFAULT '0',
				`server_descr` tinyint(1) NOT NULL DEFAULT '0',
				`server_site_pr` tinyint(2) DEFAULT NULL,
				`server_site_cy` smallint(4) DEFAULT NULL,
				`check_time_prcy` int(11) NOT NULL DEFAULT '0',
				`server_banner` tinyint(1) NOT NULL DEFAULT '0',
				`check_time_banner` int(11) NOT NULL DEFAULT '0',
				`vk_likes` int(11) NOT NULL DEFAULT '0',
				`check_time_vklike` int(11) NOT NULL DEFAULT '0',
				UNIQUE KEY `id` (`server_id`)
			) ENGINE=InnoDB");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_servers_statistics` (
				`statsid` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`serverid` int(11) NOT NULL,
				`active` tinyint(4) NOT NULL DEFAULT '0',
				`players` int(11) NOT NULL,
				`map` varchar(250) NOT NULL,
				`viewed` int(11) NOT NULL DEFAULT '0',
				`votes` int(11) DEFAULT '0',
				`dateline` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`statsid`),
				KEY `serverid` (`serverid`)
			) ENGINE=InnoDB");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_moderated', '1', '@@mon_moderated@@', 'boolean', '', null, '@@help_mon_moderated@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_name_maxlen', '30', '@@mon_name_maxlen@@', 'text', 'size=5', 'numeric', '@@help_mon_name_maxlen@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_name_minlen', '5', '@@mon_name_minlen@@', 'text', 'size=5', 'numeric', '@@help_mon_name_minlen@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_vote_multiple', '0', '@@mon_vote_multiple@@', 'boolean', '', null, '@@help_mon_vote_multiple@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_vote_guests', '1', '@@mon_vote_guests@@', 'boolean', '', null, '@@help_mon_vote_guests@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_vote_user_weight', '2', '@@mon_vote_user_weight@@', 'text', 'size=5', 'numeric', '@@help_mon_vote_user_weight@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_vote_lifetime', '1440', '@@mon_vote_lifetime@@', 'text', 'size=5', 'numeric', '@@help_mon_vote_lifetime@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_vote_cookie', 'thumbsup', '@@mon_vote_cookie@@', 'text', '', null, '@@help_mon_vote_cookie@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_vote_format', '{+BALANCE}', '@@mon_vote_format@@', 'text', '', null, '@@help_mon_vote_format@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'rating_formula', '({description}*20)+{viewed}+{votes}+{online}+{uptime}+({pr}*30/10)+({cy}*30/100)+({banner}*30)+{vklikes}', '@@rating_formula@@', 'text', '', null, '@@help_rating_formula@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_time_prcy', '30', '@@mon_time_prcy@@', 'text', 'size=5', 'numeric', '@@help_mon_time_prcy@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_time_site', '24', '@@mon_time_site@@', 'text', 'size=5', 'numeric', '@@help_mon_time_site@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_descr_length', '100', '@@mon_descr_length@@', 'text', 'size=5', 'numeric', '@@help_mon_descr_length@@', 'ratingServers')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('monitoring', 'mon_time_vklike', '60', '@@mon_time_vklike@@', 'text', 'size=5', 'numeric', '@@help_mon_time_vklike@@', 'ratingServers')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'ratingServers' ORDER BY catleft LIMIT 1");
	
	if( !category_delete($result_category) )
	{
		$error = "Error deleting product categories.";
	}
	else
	{	
		$result = $db->Query("DELETE FROM `acp_config` WHERE productid = 'ratingServers'");
		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'ratingServers'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'ratingServers'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_servers_rating_temp`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_servers_statistics`");
	}
}

?>
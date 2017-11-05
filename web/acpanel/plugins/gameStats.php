<?php

// ###############################################################################
// Game Stats version 1.0
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
		if( empty($error) )
		{
			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_stats_maps` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`date` varchar(32) NOT NULL DEFAULT '0',
				`map` varchar(32) NOT NULL,
				`serverip` varchar(32) NOT NULL DEFAULT '',
				`t_win` int(11) NOT NULL DEFAULT '0',
				`ct_win` int(11) NOT NULL DEFAULT '0',
				`connections` int(11) NOT NULL DEFAULT '0',
				`games` int(11) NOT NULL DEFAULT '1',
				`online` int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `date` (`date`,`map`,`serverip`)
			) ENGINE=InnoDB");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_stats_players` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`date` varchar(32) NOT NULL DEFAULT '0',
				`serverip` varchar(32) NOT NULL DEFAULT '',
				`map` varchar(32) NOT NULL DEFAULT '',
				`dbid` int(11) NOT NULL,
				`kills` int(11) NOT NULL DEFAULT '0',
				`headshotkills` int(11) NOT NULL DEFAULT '0',
				`deaths` int(11) NOT NULL DEFAULT '0',
				`suicides` int(11) NOT NULL DEFAULT '0',
				`ffkills` int(11) NOT NULL DEFAULT '0',
				`ffdeaths` int(11) NOT NULL DEFAULT '0',
				`streak_kills` int(11) NOT NULL DEFAULT '0',
				`streak_deaths` int(11) NOT NULL DEFAULT '0',
				`ct_team` int(10) unsigned NOT NULL DEFAULT '0',
				`t_team` int(10) unsigned NOT NULL DEFAULT '0',
				`wins` int(11) NOT NULL DEFAULT '0',
				`last_time` int(1) NOT NULL DEFAULT '0',
				`last_name` varchar(32) NOT NULL DEFAULT '',
				`last_ip` varchar(32) NOT NULL,
				`last_steamid` varchar(32) NOT NULL,
				`connections` int(11) NOT NULL DEFAULT '0',
				`online` int(11) NOT NULL DEFAULT '0',
				`updated` tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `date` (`date`,`serverip`,`map`,`dbid`),
				KEY `updated` (`updated`)
			) ENGINE=InnoDB");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_stats_weapons` (
				`weaponid` int(10) unsigned NOT NULL,
				`name` varchar(32) NOT NULL,
				`code` varchar(32) NOT NULL,
				`modifier` float(10,2) NOT NULL DEFAULT '1.00',
				PRIMARY KEY (`weaponid`)
			) ENGINE=MyISAM");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_stats_weapons_data` (
				`date` varchar(32) NOT NULL DEFAULT '0',
				`weaponid` int(10) unsigned NOT NULL,
				`serverip` varchar(32) NOT NULL DEFAULT '',
				`dbid` int(11) NOT NULL,
				`shots` int(10) NOT NULL DEFAULT '0',
				`kills` int(11) NOT NULL DEFAULT '0',
				`headshotkills` int(11) NOT NULL DEFAULT '0',
				`shot_head` int(11) NOT NULL DEFAULT '0',
				`shot_chest` int(11) NOT NULL DEFAULT '0',
				`shot_stomach` int(11) NOT NULL DEFAULT '0',
				`shot_leftarm` int(11) NOT NULL DEFAULT '0',
				`shot_rightarm` int(11) NOT NULL DEFAULT '0',
				`shot_leftleg` int(11) NOT NULL DEFAULT '0',
				`shot_rightleg` int(11) NOT NULL DEFAULT '0',
				`damage` int(11) NOT NULL DEFAULT '0',
				UNIQUE KEY `date` (`date`,`weaponid`,`serverip`,`dbid`)
			) ENGINE=InnoDB");

			$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_stats_players_rank` (
				`userid` int(11) NOT NULL,
				`server_id` int(11) NOT NULL DEFAULT '0',
				`kills` int(11) NOT NULL DEFAULT '0',
				`kills_hs` int(11) NOT NULL DEFAULT '0',
				`kills_ff` int(11) NOT NULL DEFAULT '0',
				`deaths` int(11) NOT NULL DEFAULT '0',
				`deaths_suicides` int(11) NOT NULL DEFAULT '0',
				`deaths_ff` int(11) NOT NULL DEFAULT '0',
				`streak_kills` int(11) NOT NULL DEFAULT '0',
				`streak_deaths` int(11) NOT NULL DEFAULT '0',
				`team_ct` int(10) unsigned NOT NULL DEFAULT '0',
				`team_t` int(10) unsigned NOT NULL DEFAULT '0',
				`wins` int(11) NOT NULL DEFAULT '0',
				`last_visit` int(1) NOT NULL DEFAULT '0',
				`connections` int(11) NOT NULL DEFAULT '0',
				`online` int(11) NOT NULL DEFAULT '0',
				`skill` int(11) NOT NULL DEFAULT '0',
				`position` int(11) NOT NULL DEFAULT '0',
				`history` text NOT NULL,
				`updated` tinyint(1) NOT NULL DEFAULT '0',
				UNIQUE KEY `userserver` (`userid`,`server_id`),
				KEY `updated` (`updated`)
			) ENGINE=InnoDB");

			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', null, '', '@@gamestats@@', 'text', '90', null, null, 'gameStats')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', 'stats_skill_formula', '((2*{wins}/({team_t}+{team_ct}))+(5*{hs}/{kills})+({streak_kills}/{streak_deaths})+(2*{kills}/{deaths})+(60*{kills}/{online}))*{activity}', '@@stats_skill_formula@@', 'text', '', '', '@@help_stats_skill_formula@@', 'gameStats')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', 'stats_activity_time', '744', '@@stats_activity_time@@', 'text', 'size=5', 'numeric', '@@help_stats_activity_time@@', 'gameStats')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', 'stats_skill_min_kills', '1', '@@stats_activity_time@@', 'text', 'size=5', 'numeric', '@@help_stats_skill_min_kills@@', 'gameStats')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', 'stats_players_per_page', '20', '@@stats_players_per_page@@', 'text', 'size=5', 'numeric', '@@help_stats_players_per_page@@', 'gameStats')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', 'stats_cache_blocks', '10', '@@stats_cache_blocks@@', 'text', 'size=5', 'numeric', '@@help_stats_cache_blocks@@', 'gameStats')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', 'stats_cache_time', '180', '@@stats_cache_time@@', 'text', 'size=5', 'numeric', '@@help_stats_cache_time@@', 'gameStats')");
			$result = $db->Query("INSERT INTO `acp_config` (section, varname, value, label, type, options, verifycodes, help, productid) VALUES ('game_stats', 'stats_max_top_block', '10', '@@stats_max_top_block@@', 'text', 'size=5', 'numeric', '@@help_stats_max_top_block@@', 'gameStats')");

			$result = $db->Query("INSERT INTO `acp_blocks` (`productid`, `title`, `description`, `link`, `display_order`) VALUES ('gameStats', '@@block_stats_player_skill@@', 'Top players by type skill', 'player_skill', '40')");

			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('1', 'Sig Sauer P-228', 'p228', '1.50')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('3', 'Steyr Scout', 'scout', '1.60')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('4', 'High Explosive Grenade', 'hegrenade', '1.80')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('5', 'Benelli/H&K M4 Super 90 XM1014', 'xm1014', '1.40')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('6', 'C4', 'c4', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('7', 'Ingram MAC-10', 'mac10', '1.25')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('8', 'Steyr Aug', 'aug', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('9', 'SMOKEGRENADE', 'smokegrenade', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('10', 'Dual Beretta 96G Elite', 'elite', '1.50')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('11', 'FN Five-Seven', 'fiveseven', '1.50')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('12', 'H&K UMP45', 'ump45', '1.25')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('13', 'SG550', 'sg550', '1.70')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('14', 'Galil', 'galil', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('15', 'Fusil Automatique', 'famas', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('16', 'H&K USP .45 Tactical', 'usp', '1.50')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('17', 'Glock 18 Select Fire', 'glock18', '1.50')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('18', 'Arctic Warfare Magnum (Police)', 'awp', '1.40')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('19', 'H&K MP5-Navy', 'mp5navy', '1.25')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('20', 'M249 PARA Light Machine Gun', 'm249', '0.80')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('21', 'Benelli M3 Super 90 Combat', 'm3', '1.40')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('22', 'Colt M4A1 Carbine', 'm4a1', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('23', 'Steyr Tactical Machine Pistol', 'tmp', '1.25')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('24', 'H&K G3/SG1 Sniper Rifle', 'g3sg1', '1.40')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('25', 'FLASHBANG', 'flashbang', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('26', 'Desert Eagle .50AE', 'deagle', '1.50')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('27', 'Sig Sauer SG-552 Commando', 'sg552', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('28', 'Kalashnikov AK-47', 'ak47', '1.00')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('29', 'Knife', 'knife', '1.80')");
			$result = $db->Query("INSERT INTO `acp_stats_weapons` VALUES ('30', 'FN P90', 'p90', '1.25')");
		}
	}
}
else
{
		$result = $db->Query("DELETE FROM `acp_config` WHERE productid = 'gameStats'");
		$result = $db->Query("DELETE FROM `acp_blocks` WHERE productid = 'gameStats'");
		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'gameStats'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'gameStats'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_stats_maps`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_stats_players`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_stats_players_rank`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_bans_reasons`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_stats_weapons`");
		$result = $db->Query("DROP TABLE IF EXISTS `acp_stats_weapons_data`");
}

?>
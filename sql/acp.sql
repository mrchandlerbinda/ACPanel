/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50635
 Source Host           : localhost:3306
 Source Schema         : lime

 Target Server Type    : MySQL
 Target Server Version : 50635
 File Encoding         : 65001

 Date: 05/11/2017 15:03:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for acp_access_mask
-- ----------------------------
DROP TABLE IF EXISTS `acp_access_mask`;
CREATE TABLE `acp_access_mask` (
  `mask_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_flags` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`mask_id`),
  FULLTEXT KEY `access` (`access_flags`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_access_mask
-- ----------------------------
BEGIN;
INSERT INTO `acp_access_mask` VALUES (1, 'a');
INSERT INTO `acp_access_mask` VALUES (2, 'b');
INSERT INTO `acp_access_mask` VALUES (3, 'c');
INSERT INTO `acp_access_mask` VALUES (4, 'd');
INSERT INTO `acp_access_mask` VALUES (5, 'e');
INSERT INTO `acp_access_mask` VALUES (6, 'f');
INSERT INTO `acp_access_mask` VALUES (7, 'g');
INSERT INTO `acp_access_mask` VALUES (8, 'h');
INSERT INTO `acp_access_mask` VALUES (9, 'i');
INSERT INTO `acp_access_mask` VALUES (10, 'j');
INSERT INTO `acp_access_mask` VALUES (11, 'k');
INSERT INTO `acp_access_mask` VALUES (12, 'l');
INSERT INTO `acp_access_mask` VALUES (13, 'm');
INSERT INTO `acp_access_mask` VALUES (14, 'n');
INSERT INTO `acp_access_mask` VALUES (15, 'o');
INSERT INTO `acp_access_mask` VALUES (16, 'p');
INSERT INTO `acp_access_mask` VALUES (17, 'q');
INSERT INTO `acp_access_mask` VALUES (18, 'r');
INSERT INTO `acp_access_mask` VALUES (19, 's');
INSERT INTO `acp_access_mask` VALUES (20, 't');
INSERT INTO `acp_access_mask` VALUES (21, 'u');
INSERT INTO `acp_access_mask` VALUES (22, 'v');
COMMIT;

-- ----------------------------
-- Table structure for acp_access_mask_players
-- ----------------------------
DROP TABLE IF EXISTS `acp_access_mask_players`;
CREATE TABLE `acp_access_mask_players` (
  `userid` int(11) NOT NULL,
  `mask_id` int(11) NOT NULL,
  `access_expired` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `userid` (`userid`,`mask_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_access_mask_players
-- ----------------------------
BEGIN;
INSERT INTO `acp_access_mask_players` VALUES (1, 20, 0);
COMMIT;

-- ----------------------------
-- Table structure for acp_access_mask_servers
-- ----------------------------
DROP TABLE IF EXISTS `acp_access_mask_servers`;
CREATE TABLE `acp_access_mask_servers` (
  `mask_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  UNIQUE KEY `mask` (`mask_id`,`server_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_access_mask_servers
-- ----------------------------
BEGIN;
INSERT INTO `acp_access_mask_servers` VALUES (1, 0);
INSERT INTO `acp_access_mask_servers` VALUES (2, 0);
INSERT INTO `acp_access_mask_servers` VALUES (3, 0);
INSERT INTO `acp_access_mask_servers` VALUES (4, 0);
INSERT INTO `acp_access_mask_servers` VALUES (5, 0);
INSERT INTO `acp_access_mask_servers` VALUES (6, 0);
INSERT INTO `acp_access_mask_servers` VALUES (7, 0);
INSERT INTO `acp_access_mask_servers` VALUES (8, 0);
INSERT INTO `acp_access_mask_servers` VALUES (9, 0);
INSERT INTO `acp_access_mask_servers` VALUES (10, 0);
INSERT INTO `acp_access_mask_servers` VALUES (11, 0);
INSERT INTO `acp_access_mask_servers` VALUES (12, 0);
INSERT INTO `acp_access_mask_servers` VALUES (13, 0);
INSERT INTO `acp_access_mask_servers` VALUES (14, 0);
INSERT INTO `acp_access_mask_servers` VALUES (15, 0);
INSERT INTO `acp_access_mask_servers` VALUES (16, 0);
INSERT INTO `acp_access_mask_servers` VALUES (17, 0);
INSERT INTO `acp_access_mask_servers` VALUES (18, 0);
INSERT INTO `acp_access_mask_servers` VALUES (19, 0);
INSERT INTO `acp_access_mask_servers` VALUES (20, 0);
INSERT INTO `acp_access_mask_servers` VALUES (21, 0);
INSERT INTO `acp_access_mask_servers` VALUES (22, 0);
COMMIT;

-- ----------------------------
-- Table structure for acp_bans
-- ----------------------------
DROP TABLE IF EXISTS `acp_bans`;
CREATE TABLE `acp_bans` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_bans_history
-- ----------------------------
DROP TABLE IF EXISTS `acp_bans_history`;
CREATE TABLE `acp_bans_history` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_bans_reasons
-- ----------------------------
DROP TABLE IF EXISTS `acp_bans_reasons`;
CREATE TABLE `acp_bans_reasons` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `address` varchar(32) NOT NULL,
  `reason` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_bans_subnets
-- ----------------------------
DROP TABLE IF EXISTS `acp_bans_subnets`;
CREATE TABLE `acp_bans_subnets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subipaddr` varchar(32) NOT NULL,
  `bitmask` varchar(2) NOT NULL,
  `comment` text NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_blocks
-- ----------------------------
DROP TABLE IF EXISTS `acp_blocks`;
CREATE TABLE `acp_blocks` (
  `blockid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `productid` varchar(25) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext,
  `link` varchar(40) DEFAULT NULL,
  `execute_code` text NOT NULL,
  `view_in_block` enum('no','yes') NOT NULL DEFAULT 'yes',
  `display_order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`blockid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_blocks
-- ----------------------------
BEGIN;
INSERT INTO `acp_blocks` VALUES (1, 'ACPanel', '@@cloud_head@@', 'A cloud of servers that are added to the panel', 'cloud_servers', '', 'yes', 5);
INSERT INTO `acp_blocks` VALUES (2, 'ACPanel', '@@steam_head@@', 'Steam conversion to comunity and vice versa', 'steam_tool', '', 'yes', 45);
INSERT INTO `acp_blocks` VALUES (3, 'gameAccounts', '@@block_accounts_stats@@', 'Summary statistics for game accounts', 'accounts_stats', '', 'yes', 10);
INSERT INTO `acp_blocks` VALUES (4, 'gameBans', '@@block_bans_stats@@', 'Summary statistics for bans', 'bans_stats', '', 'yes', 20);
INSERT INTO `acp_blocks` VALUES (5, 'gameBans', '@@block_bans_best_admin@@', 'List of the best admins today', 'bans_best_admin', '', 'yes', 25);
INSERT INTO `acp_blocks` VALUES (6, 'nickControl', '@@checknick_head@@', 'Verification tool nickname based on regular expressions that are defined in the admin', 'check_nick', '', 'yes', 40);
INSERT INTO `acp_blocks` VALUES (7, 'gameAccounts', '@@my_game_acc@@', '', 'user_info', '', 'yes', 5);
INSERT INTO `acp_blocks` VALUES (8, 'gameStats', '@@block_stats_player_skill@@', 'Top players by type skill', 'player_skill', '', 'yes', 35);
COMMIT;

-- ----------------------------
-- Table structure for acp_category
-- ----------------------------
DROP TABLE IF EXISTS `acp_category`;
CREATE TABLE `acp_category` (
  `categoryid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sectionid` int(10) DEFAULT NULL,
  `parentid` int(10) DEFAULT NULL,
  `catleft` int(10) unsigned NOT NULL DEFAULT '0',
  `catright` int(10) unsigned NOT NULL DEFAULT '0',
  `catlevel` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(40) DEFAULT NULL,
  `description` varchar(250) NOT NULL DEFAULT '',
  `link` varchar(40) DEFAULT NULL,
  `url` varchar(250) NOT NULL DEFAULT '',
  `show_blocks` text NOT NULL,
  `productid` varchar(25) DEFAULT NULL,
  `display_order` int(10) NOT NULL DEFAULT '10',
  PRIMARY KEY (`categoryid`),
  KEY `parent` (`sectionid`)
) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_category
-- ----------------------------
BEGIN;
INSERT INTO `acp_category` VALUES (1, NULL, NULL, 1, 22, 0, '@@home@@', 'Главная страница игровых серверов', 'homepage', '', '1,2,3,6', 'ACPanel', 10);
INSERT INTO `acp_category` VALUES (2, NULL, NULL, 1, 78, 0, '@@panel_settings@@', '', 'p_general', '', '', 'ACPanel', 20);
INSERT INTO `acp_category` VALUES (3, NULL, NULL, 1, 32, 0, '@@control_servers@@', '', 'p_servers', '', '', 'ACPanel', 30);
INSERT INTO `acp_category` VALUES (4, 2, 7, 3, 4, 2, '@@general_options@@', '', 'p_general_options', '', '', 'ACPanel', 10);
INSERT INTO `acp_category` VALUES (5, 3, 38, 3, 4, 2, '@@control_servers_add@@', '', 'p_servers_control_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (6, 2, 2, 34, 47, 1, '@@users_and_groups@@', '', '', '', '', 'ACPanel', 20);
INSERT INTO `acp_category` VALUES (7, 2, 2, 2, 33, 1, '@@services@@', '', '', '', '', 'ACPanel', 10);
INSERT INTO `acp_category` VALUES (8, 2, 7, 5, 10, 2, '@@categories@@', '', 'p_general_categories', '', '', 'ACPanel', 20);
INSERT INTO `acp_category` VALUES (9, 2, 7, 31, 32, 2, '@@panel_logs@@', '', 'p_general_logs', '', '', 'ACPanel', 70);
INSERT INTO `acp_category` VALUES (10, 2, 7, 21, 30, 2, '@@optimizing@@', '', 'p_optimization', '', '', 'ACPanel', 50);
INSERT INTO `acp_category` VALUES (11, 2, 26, 49, 54, 2, '@@languages@@', '', 'p_general_lang', '', '', 'ACPanel', 10);
INSERT INTO `acp_category` VALUES (12, 2, 7, 17, 20, 2, '@@products@@', '', 'p_products', '', '', 'ACPanel', 40);
INSERT INTO `acp_category` VALUES (13, 2, 6, 35, 40, 2, '@@users_list@@', '', 'p_users', '', '', 'ACPanel', 10);
INSERT INTO `acp_category` VALUES (14, 2, 6, 41, 42, 2, '@@users_search@@', '', 'p_users_search', '', '', 'ACPanel', 30);
INSERT INTO `acp_category` VALUES (15, 2, 6, 43, 46, 2, '@@groups_setting@@', '', 'p_usergroups', '', '', 'ACPanel', 30);
INSERT INTO `acp_category` VALUES (16, 1, 1, 6, 7, 1, '@@admins@@', '', 'p_admins', '', '2,3,6,7', 'ACPanel', 20);
INSERT INTO `acp_category` VALUES (17, 1, 1, 2, 3, 1, '@@rules@@', '', 'custom_page', '', '1,2', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (18, 2, 26, 61, 66, 2, '@@phrases@@', '', 'p_general_phrases', '', '', 'ACPanel', 30);
INSERT INTO `acp_category` VALUES (19, 2, 11, 50, 51, 3, '@@general_add_lang@@', '', 'p_general_lang_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (20, 2, 18, 64, 65, 3, '@@general_add_phrase@@', '', 'p_general_phrase_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (21, 2, 11, 52, 53, 3, '@@general_edit_lang@@', '', 'p_general_lang_edit', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (22, 2, 18, 62, 63, 3, '@@general_edit_phrase@@', '', 'p_general_phrase_edit', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (23, 2, 8, 6, 7, 3, '@@category_add@@', '', 'p_general_categories_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (24, 2, 8, 8, 9, 3, '@@category_edit@@', '', 'p_general_categories_edit', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (25, 2, 12, 18, 19, 3, '@@add_product@@', '', 'p_products_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (26, 2, 2, 48, 69, 1, '@@langs_and_phrases@@', '', '', '', '', 'ACPanel', 30);
INSERT INTO `acp_category` VALUES (27, 2, 2, 70, 77, 1, '@@tools@@', '', '', '', '', 'ACPanel', 40);
INSERT INTO `acp_category` VALUES (28, 2, 10, 28, 29, 3, '@@check_category_tree@@', '', 'optimization/check_category_tree', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (29, 2, 10, 26, 27, 3, '@@php_settings@@', '', 'optimization/php_settings', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (30, 2, 15, 44, 45, 3, '@@usergroup_add@@', '', 'p_usergroups_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (31, 2, 13, 38, 39, 3, '@@users_add@@', '', 'p_users_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (32, 2, 13, 36, 37, 3, '@@users_edit@@', '', 'p_users_edit', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (33, 2, 26, 67, 68, 2, '@@search_phrases@@', '', 'p_general_phrase_search', '', '', 'ACPanel', 40);
INSERT INTO `acp_category` VALUES (34, 2, 7, 11, 16, 2, '@@blocks@@', '', 'p_general_blocks', '', '', 'ACPanel', 30);
INSERT INTO `acp_category` VALUES (35, 2, 34, 14, 15, 3, '@@block_add@@', '', 'p_general_blocks_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (36, 2, 34, 12, 13, 3, '@@block_edit@@', '', 'p_general_blocks_edit', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (37, 2, 10, 24, 25, 3, '@@clear_cache@@', '', 'optimization/clear_cache', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (38, 3, 3, 2, 5, 1, '@@servers_list@@', '', 'p_servers_control', '', '', 'ACPanel', 5);
INSERT INTO `acp_category` VALUES (39, 2, 26, 55, 60, 2, '@@general_phrases_template@@', '', 'p_general_phrases_template', '', '', 'ACPanel', 20);
INSERT INTO `acp_category` VALUES (40, 2, 39, 58, 59, 3, '@@general_phrases_template_add@@', '', 'p_general_phrases_template_add', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (41, 2, 39, 56, 57, 3, '@@general_phrases_template_edit@@', '', 'p_general_phrases_template_edit', '', '', 'ACPanel', 0);
INSERT INTO `acp_category` VALUES (42, 1, 1, 4, 5, 1, '@@server_card@@', '', 'p_server_card', '', '2,6,7,8', 'ratingServers', 0);
INSERT INTO `acp_category` VALUES (43, 2, 27, 71, 76, 2, '@@task_sheduler@@', '', 'task_sheduler', '', '', 'taskSheduler', 10);
INSERT INTO `acp_category` VALUES (44, 2, 43, 74, 75, 3, '@@add_new_task@@', '', 'task_sheduler_add', '', '', 'taskSheduler', 0);
INSERT INTO `acp_category` VALUES (45, 2, 43, 72, 73, 3, '@@edit_task@@', '', 'task_sheduler_edit', '', '', 'taskSheduler', 0);
INSERT INTO `acp_category` VALUES (46, NULL, NULL, 1, 20, 0, '@@game_accounts@@', '', 'p_gamecp', '', '', 'gameAccounts', 40);
INSERT INTO `acp_category` VALUES (47, 46, 46, 2, 7, 1, '@@user_accounts@@', '', 'p_gamecp_accounts', '', '', 'gameAccounts', 10);
INSERT INTO `acp_category` VALUES (48, 46, 47, 5, 6, 2, '@@add_account@@', '', 'p_gamecp_accounts_add', '', '', 'gameAccounts', 0);
INSERT INTO `acp_category` VALUES (49, 46, 47, 3, 4, 2, '@@edit_account@@', '', 'p_gamecp_accounts_edit', '', '', 'gameAccounts', 0);
INSERT INTO `acp_category` VALUES (50, 46, 46, 8, 11, 1, '@@user_requests@@', '', 'p_gamecp_requests', '', '', 'gameAccounts', 20);
INSERT INTO `acp_category` VALUES (51, 46, 50, 9, 10, 2, '@@user_requests_edit@@', '', 'p_gamecp_requests_edit', '', '', 'gameAccounts', 0);
INSERT INTO `acp_category` VALUES (52, 46, 46, 12, 17, 1, '@@access_mask@@', '', 'p_gamecp_mask', '', '', 'gameAccounts', 30);
INSERT INTO `acp_category` VALUES (53, 46, 52, 15, 16, 2, '@@access_mask_add@@', '', 'p_gamecp_mask_add', '', '', 'gameAccounts', 0);
INSERT INTO `acp_category` VALUES (54, 46, 52, 13, 14, 2, '@@access_mask_edit@@', '', 'p_gamecp_mask_edit', '', '', 'gameAccounts', 0);
INSERT INTO `acp_category` VALUES (55, 46, 46, 18, 19, 1, '@@ga_search@@', '', 'p_gamecp_search', '', '', 'gameAccounts', 40);
INSERT INTO `acp_category` VALUES (56, NULL, NULL, 1, 20, 0, '@@gamebans@@', '', 'p_gamebans', '', '', 'gameBans', 50);
INSERT INTO `acp_category` VALUES (57, 56, 56, 2, 7, 1, '@@gamebans_players@@', '', 'p_gamebans_players', '', '', 'gameBans', 10);
INSERT INTO `acp_category` VALUES (58, 56, 57, 5, 6, 2, '@@ban_edit@@', '', 'p_gamebans_players_add', '', '', 'gameBans', 0);
INSERT INTO `acp_category` VALUES (59, 56, 57, 3, 4, 2, '@@add_ban@@', '', 'p_gamebans_players_edit', '', '', 'gameBans', 0);
INSERT INTO `acp_category` VALUES (60, 56, 56, 8, 11, 1, '@@ban_reasons@@', '', 'p_gamebans_reasons', '', '', 'gameBans', 20);
INSERT INTO `acp_category` VALUES (61, 56, 60, 9, 10, 2, '@@add_ban_reason@@', '', 'p_gamebans_reasons_add', '', '', 'gameBans', 0);
INSERT INTO `acp_category` VALUES (62, 56, 56, 12, 17, 1, '@@gamebans_subnets@@', '', 'p_gamebans_subnets', '', '', 'gameBans', 30);
INSERT INTO `acp_category` VALUES (63, 56, 62, 15, 16, 2, '@@add_subnet@@', '', 'p_gamebans_subnets_add', '', '', 'gameBans', 0);
INSERT INTO `acp_category` VALUES (64, 56, 62, 13, 14, 2, '@@edit_subnet@@', '', 'p_gamebans_subnets_edit', '', '', 'gameBans', 0);
INSERT INTO `acp_category` VALUES (65, 56, 56, 18, 19, 1, '@@bans_search@@', '', 'p_gamebans_search', '', '', 'gameBans', 40);
INSERT INTO `acp_category` VALUES (102, 1, 1, 8, 15, 1, 'Игровые баны', '', '', '', '', 'gameBans', 30);
INSERT INTO `acp_category` VALUES (67, 1, 102, 9, 10, 2, '@@gamebans_players@@', '', 'p_gamebans_public_players', '', '2,4,5,6,7', 'gameBans', 20);
INSERT INTO `acp_category` VALUES (68, 1, 102, 11, 12, 2, '@@gamebans_subnets@@', '', 'p_gamebans_public_subnets', '', '2,4,5,6,7', 'gameBans', 30);
INSERT INTO `acp_category` VALUES (69, 1, 102, 13, 14, 2, '@@gb_banlist_stats@@', '', 'p_gamebans_public_stats', '', '2,4,5,6,7', 'gameBans', 40);
INSERT INTO `acp_category` VALUES (70, 2, 10, 22, 23, 3, '@@bans_prune@@', '', 'optimization/bans_prune', '', '', 'gameBans', 0);
INSERT INTO `acp_category` VALUES (71, 3, 3, 14, 25, 1, '@@chat_control@@', '', '', '', '', 'chatControl', 10);
INSERT INTO `acp_category` VALUES (72, 3, 71, 15, 18, 2, '@@chat_patterns@@', '', 'p_cc_patterns', '', '', 'chatControl', 10);
INSERT INTO `acp_category` VALUES (73, 3, 72, 16, 17, 3, '@@chat_add_pattern@@', '', 'p_cc_patterns_add', '', '', 'chatControl', 0);
INSERT INTO `acp_category` VALUES (74, 3, 71, 19, 22, 2, '@@chat_commands@@', '', 'p_cc_commands', '', '', 'chatControl', 20);
INSERT INTO `acp_category` VALUES (75, 3, 74, 20, 21, 3, '@@chat_add_command@@', '', 'p_cc_commands_add', '', '', 'chatControl', 0);
INSERT INTO `acp_category` VALUES (76, 3, 71, 23, 24, 2, '@@chat_logs@@', '', 'p_cc_logs', '', '', 'chatControl', 30);
INSERT INTO `acp_category` VALUES (77, 1, 1, 16, 17, 1, '@@gamechat@@', '', 'p_gamechat', '', '2,3,6,7', 'chatControl', 40);
INSERT INTO `acp_category` VALUES (78, 3, 3, 28, 31, 1, '@@hud_manager@@', '', 'p_hm_patterns', '', '', 'hudManager', 40);
INSERT INTO `acp_category` VALUES (79, 3, 78, 29, 30, 2, '@@hud_add_pattern@@', '', 'p_hm_patterns_add', '', '', 'hudManager', 0);
INSERT INTO `acp_category` VALUES (80, 3, 3, 6, 13, 1, '@@nick_control@@', '', '', '', '', 'nickControl', 10);
INSERT INTO `acp_category` VALUES (81, 3, 80, 7, 10, 2, '@@nick_patterns@@', '', 'p_nc_patterns', '', '', 'nickControl', 10);
INSERT INTO `acp_category` VALUES (82, 3, 81, 8, 9, 3, '@@nick_add_pattern@@', '', 'p_nc_patterns_add', '', '', 'nickControl', 0);
INSERT INTO `acp_category` VALUES (83, 3, 80, 11, 12, 2, '@@nick_logs@@', '', 'p_nc_logs', '', '', 'nickControl', 30);
INSERT INTO `acp_category` VALUES (84, NULL, NULL, 1, 32, 0, '@@usershop_manage@@', '', 'p_usershop_admin', '', '', 'userBank', 60);
INSERT INTO `acp_category` VALUES (85, 84, 84, 2, 21, 1, '@@payment_privileges@@', '', 'p_usershop_admin_payments', '', '', 'userBank', 10);
INSERT INTO `acp_category` VALUES (86, 84, 85, 9, 14, 2, '@@usershop_admin_patterns@@', '', 'p_usershop_admin_patterns', '', '', 'userBank', 20);
INSERT INTO `acp_category` VALUES (87, 84, 86, 12, 13, 3, '@@usershop_admin_patterns_add@@', '', 'p_usershop_admin_patterns_add', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (88, 84, 86, 10, 11, 3, '@@usershop_admin_patterns_edit@@', '', 'p_usershop_admin_patterns_edit', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (89, 84, 85, 3, 8, 2, '@@usershop_admin_groups@@', '', 'p_usershop_admin_groups', '', '', 'userBank', 10);
INSERT INTO `acp_category` VALUES (90, 84, 89, 6, 7, 3, '@@usershop_admin_groups_add@@', '', 'p_usershop_admin_groups_add', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (91, 84, 89, 4, 5, 3, '@@usershop_admin_groups_edit@@', '', 'p_usershop_admin_groups_edit', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (92, 84, 85, 15, 20, 2, '@@payment_user_privileges@@', '', 'p_usershop_admin_patterns_user', '', '', 'userBank', 30);
INSERT INTO `acp_category` VALUES (93, 84, 92, 16, 17, 3, '@@usershop_admin_patterns_user_detail@@', '', 'p_usershop_admin_patterns_user_detail', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (94, 84, 92, 18, 19, 3, '@@usershop_profile_privilege_detail@@', '', 'p_usershop_profile_privilege_detail', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (95, 84, 84, 22, 23, 1, '@@usershop_admin_payments@@', '', 'p_usershop_admin_payments', '', '', 'userBank', 20);
INSERT INTO `acp_category` VALUES (96, 1, 1, 18, 21, 1, '@@usershop@@', '', 'p_usershop', '', '2,3,6,7', 'userBank', 50);
INSERT INTO `acp_category` VALUES (97, 1, 96, 19, 20, 2, '@@usershop_buywindow@@', '', 'p_usershop_buywindow', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (98, 84, 84, 24, 31, 1, '@@game_shop@@', '', 'p_gameshop_items', '', '', 'userBank', 30);
INSERT INTO `acp_category` VALUES (99, 84, 98, 29, 30, 2, '@@game_shop_servers@@', '', 'p_gameshop_items_servers', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (100, 84, 98, 27, 28, 2, '@@gameshop_items_edit@@', '', 'p_gameshop_items_edit', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (101, 84, 98, 25, 26, 2, '@@gameshop_items_add@@', '', 'p_gameshop_items_add', '', '', 'userBank', 0);
INSERT INTO `acp_category` VALUES (105, 3, 3, 26, 27, 1, '@@vbk_logs@@', '', 'p_vbk_logs', '', '', 'voteBanKick', 30);
COMMIT;

-- ----------------------------
-- Table structure for acp_chat_logs
-- ----------------------------
DROP TABLE IF EXISTS `acp_chat_logs`;
CREATE TABLE `acp_chat_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serverip` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  `authid` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(100) NOT NULL DEFAULT '',
  `alive` int(11) NOT NULL DEFAULT '0',
  `team` varchar(100) NOT NULL DEFAULT '',
  `timestamp` int(1) NOT NULL DEFAULT '0',
  `cmd` varchar(100) NOT NULL DEFAULT '',
  `foradmins` int(11) NOT NULL DEFAULT '0',
  `pattern` int(11) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_chat_nswords
-- ----------------------------
DROP TABLE IF EXISTS `acp_chat_nswords`;
CREATE TABLE `acp_chat_nswords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `value` (`value`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_chat_nswords
-- ----------------------------
BEGIN;
INSERT INTO `acp_chat_nswords` VALUES (1, '/boost');
INSERT INTO `acp_chat_nswords` VALUES (2, '/bf2menu');
INSERT INTO `acp_chat_nswords` VALUES (3, '/admin');
INSERT INTO `acp_chat_nswords` VALUES (4, '!weapons');
INSERT INTO `acp_chat_nswords` VALUES (5, '!top10');
INSERT INTO `acp_chat_nswords` VALUES (6, '!score');
INSERT INTO `acp_chat_nswords` VALUES (7, '!rules');
INSERT INTO `acp_chat_nswords` VALUES (8, '!level');
INSERT INTO `acp_chat_nswords` VALUES (9, '/changerace');
INSERT INTO `acp_chat_nswords` VALUES (10, '/clearpowers');
INSERT INTO `acp_chat_nswords` VALUES (11, '/colorchat');
INSERT INTO `acp_chat_nswords` VALUES (12, '/cp');
INSERT INTO `acp_chat_nswords` VALUES (13, '/(gc|gocheck|tp)');
INSERT INTO `acp_chat_nswords` VALUES (14, '/guns');
INSERT INTO `acp_chat_nswords` VALUES (15, '/help');
INSERT INTO `acp_chat_nswords` VALUES (16, '/herolist');
INSERT INTO `acp_chat_nswords` VALUES (17, '/(hp|me)');
INSERT INTO `acp_chat_nswords` VALUES (18, '/invis');
INSERT INTO `acp_chat_nswords` VALUES (19, '/keys');
INSERT INTO `acp_chat_nswords` VALUES (20, '/ljtop');
INSERT INTO `acp_chat_nswords` VALUES (21, '/lm');
INSERT INTO `acp_chat_nswords` VALUES (22, '/mute');
INSERT INTO `acp_chat_nswords` VALUES (23, '/playerlevels');
INSERT INTO `acp_chat_nswords` VALUES (24, '/rank');
INSERT INTO `acp_chat_nswords` VALUES (25, '/rankstats');
INSERT INTO `acp_chat_nswords` VALUES (26, '/reset');
INSERT INTO `acp_chat_nswords` VALUES (27, '/resetscore');
INSERT INTO `acp_chat_nswords` VALUES (28, '/rr');
INSERT INTO `acp_chat_nswords` VALUES (29, '/save');
INSERT INTO `acp_chat_nswords` VALUES (30, '/scout');
INSERT INTO `acp_chat_nswords` VALUES (31, '/showmenu');
INSERT INTO `acp_chat_nswords` VALUES (32, '/spec');
INSERT INTO `acp_chat_nswords` VALUES (33, '/speckeys');
INSERT INTO `acp_chat_nswords` VALUES (34, '/start');
INSERT INTO `acp_chat_nswords` VALUES (35, '/stuck');
INSERT INTO `acp_chat_nswords` VALUES (36, '/top15');
INSERT INTO `acp_chat_nswords` VALUES (37, '/?votemap');
INSERT INTO `acp_chat_nswords` VALUES (38, 'amulet');
INSERT INTO `acp_chat_nswords` VALUES (39, 'ankh');
INSERT INTO `acp_chat_nswords` VALUES (40, 'boots');
INSERT INTO `acp_chat_nswords` VALUES (41, 'changerace');
INSERT INTO `acp_chat_nswords` VALUES (42, 'cloak');
INSERT INTO `acp_chat_nswords` VALUES (43, 'gloves');
INSERT INTO `acp_chat_nswords` VALUES (44, 'guns');
INSERT INTO `acp_chat_nswords` VALUES (45, 'mask');
INSERT INTO `acp_chat_nswords` VALUES (46, 'mole');
INSERT INTO `acp_chat_nswords` VALUES (47, 'necklace');
INSERT INTO `acp_chat_nswords` VALUES (48, 'player(skills|levels)');
INSERT INTO `acp_chat_nswords` VALUES (49, 'rings');
INSERT INTO `acp_chat_nswords` VALUES (50, 'scroll');
INSERT INTO `acp_chat_nswords` VALUES (51, 'shopmenu2?');
INSERT INTO `acp_chat_nswords` VALUES (52, 'thetime');
INSERT INTO `acp_chat_nswords` VALUES (53, 'timeleft');
INSERT INTO `acp_chat_nswords` VALUES (54, 'tome');
INSERT INTO `acp_chat_nswords` VALUES (55, 'top15');
COMMIT;

-- ----------------------------
-- Table structure for acp_chat_patterns
-- ----------------------------
DROP TABLE IF EXISTS `acp_chat_patterns`;
CREATE TABLE `acp_chat_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern` text NOT NULL,
  `action` tinyint(1) NOT NULL DEFAULT '0',
  `reason` varchar(255) NOT NULL,
  `length` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_chat_patterns
-- ----------------------------
BEGIN;
INSERT INTO `acp_chat_patterns` VALUES (1, '193\\.106\\.92\\.153', 0, '', '');
INSERT INTO `acp_chat_patterns` VALUES (2, '(g|d)o(l|ji|\\/\\\\)(b|6)(o|a)e(b|6)', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (3, 'add to favorites', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (4, 'a114(games)?\\.(ru|com)', 0, '', '');
INSERT INTO `acp_chat_patterns` VALUES (5, '(s|c)(o{1,5}|u|y)(k|q)(a|i|u|y)', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (6, '(n|p|II)(i|1)?(3|z)(d|g)e?c?', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (7, 'pid(o|a)?r', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (8, 'Bloody\\sVectors', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (9, '(r|g)(a|o)(n|h)(g|d)o(n|h)', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (10, 'm(u|y)(d|g)(a?(k|c)|(i|u)l)', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (11, 'buy server', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (12, 'voteban', 0, '', '');
INSERT INTO `acp_chat_patterns` VALUES (13, '^(/|@|!|%)', 1, '', '');
INSERT INTO `acp_chat_patterns` VALUES (14, '(!g|!w|!t)', 1, '', '');
INSERT INTO `acp_chat_patterns` VALUES (15, 'senses fail', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (16, 'e(6|b)((lo(?!ck)|la|lu)|(a|i|y|u|@)(t|h|l))', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (17, 'badboy', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (18, 'f(a|u)ck', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (19, 'jedai hack', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (20, 'nigger nogger', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (21, 'm@f1a team', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (22, 'fighter fx', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (23, '\\-\\s(kills|frags)\\:\\[\\d+\\]', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (24, 'alien h4x', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (25, 'codename exclusive', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (26, 'w4r hook', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (27, 'ai\\-house', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (28, '\\[emo tear', 2, 'Cheating/ 4uTbI (Chat Message)', '0');
INSERT INTO `acp_chat_patterns` VALUES (29, '[0-9a-z]\\.(ru|com|su|net|info|org|pl|in)', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (30, '[0-9a-z]\\.(tk|ro|ua|ws|nu|lt|co|il)', 3, 'Advertising Forbidden!', '0');
INSERT INTO `acp_chat_patterns` VALUES (31, 'mvpro_net', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (32, 'chekovskii', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (33, 'icq\\s526994', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (34, 'gamesdom\\.do\\.am', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (35, 'titanic\\sserver', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (36, '\\d{1,3}.{1,5}\\d{1,3}.{1,5}\\d{1,3}.{1,5}\\:', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (37, '\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (38, '(444\\-444\\-987)|(444444987)', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (39, 'skype\\:davk93', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (40, 'canek12rus', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (41, 'ismagilov\\-server', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (42, 'a\\.icewater', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (43, '396226945', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (44, 'fast\\.ice\\.fire', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (45, 'bcem_3hakom', 3, 'Advertising Forbidden!', '');
INSERT INTO `acp_chat_patterns` VALUES (46, '(a|o)?(x|h)((yu|ui|yi)|((y|u)e(t|ji|l|n|h)))', 4, '', '');
INSERT INTO `acp_chat_patterns` VALUES (47, '(p|n)(i|u)(z|s|c|3)(g|d|t)(a|y|u)', 4, '', '');
COMMIT;

-- ----------------------------
-- Table structure for acp_config
-- ----------------------------
DROP TABLE IF EXISTS `acp_config`;
CREATE TABLE `acp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(128) DEFAULT NULL,
  `varname` varchar(128) DEFAULT NULL,
  `value` text NOT NULL,
  `label` varchar(128) DEFAULT NULL,
  `type` enum('text','textarea','checkbox','select','boolean') NOT NULL DEFAULT 'text',
  `options` text,
  `verifycodes` varchar(64) DEFAULT NULL,
  `help` text,
  `productid` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `varname` (`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=120 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_config
-- ----------------------------
BEGIN;
INSERT INTO `acp_config` VALUES (1, 'main', 'language', '2', '@@language@@', 'select', '1|@@english@@\r\n2|@@russian@@', NULL, '@@help_lang@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (2, 'main', 'pagesize', '15', '@@pagesize@@', 'text', 'size=5', 'numeric', '@@help_pagesize@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (3, 'main', 'charset', 'utf-8', '@@charset_content@@', 'text', 'size=5', '', '@@help_charset@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (4, 'main', 'site_offline_text', 'Игровая панель временно не доступна.', '@@site_offline_text@@', 'text', NULL, NULL, '@@help_site_off_txt@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (5, 'main', 'site_offline', '1', '@@site_offline@@', 'boolean', NULL, NULL, '@@help_site_off@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (6, 'main', 'template', 'default', '@@template@@', 'text', NULL, NULL, '@@help_tpl@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (7, 'main', 'site_name', 'Игровая панель', '@@site_name@@', 'text', NULL, NULL, '@@help_site_name@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (8, 'main', 'site_description', 'Уникальная в своем роде панель управления игровыми серверами.', '@@site_description@@', 'text', NULL, NULL, '@@help_site_description@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (9, 'main', 'admin_groups', '1', '@@admin_groups@@', 'select', 'acp_usergroups|usergroupid|usergroupname', 'multiple', '@@help_admin_groups@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (10, 'main', NULL, '', '@@basic_settings@@', 'text', '10', NULL, NULL, 'ACPanel');
INSERT INTO `acp_config` VALUES (11, 'main', 'sql_debug', '1', '@@sql_debug@@', 'boolean', NULL, NULL, '@@help_sql_debug@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (12, 'main', 'user_action_log', 'log_login,log_login_error,log_change_lang,log_steam_convert,log_edititing', '@@user_action_log@@', 'checkbox', 'log_login|@@log_login_check@@\r\nlog_login_error|@@log_login_error@@\r\nlog_change_lang|@@log_change_lang@@\r\nlog_steam_convert|@@log_steam_convert@@\r\nlog_edititing|@@log_action_edit@@', NULL, '@@help_user_action_log@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (13, 'main', 'timezone', '4', '@@timezone@@', 'select', '-12|@@timezone_gmt_minus_1200@@\r\n-11|@@timezone_gmt_minus_1100@@\r\n-10|@@timezone_gmt_minus_1000@@\r\n-9|@@timezone_gmt_minus_0900@@\r\n-8|@@timezone_gmt_minus_0800@@\r\n-7|@@timezone_gmt_minus_0700@@\r\n-6|@@timezone_gmt_minus_0600@@\r\n-5|@@timezone_gmt_minus_0500@@\r\n-4.5|@@timezone_gmt_minus_0430@@\r\n-4|@@timezone_gmt_minus_0400@@\r\n-3.5|@@timezone_gmt_minus_0330@@\r\n-3|@@timezone_gmt_minus_0300@@\r\n-2|@@timezone_gmt_minus_0200@@\r\n-1|@@timezone_gmt_minus_0100@@\r\n0|@@timezone_gmt_plus_0000@@\r\n1|@@timezone_gmt_plus_0100@@\r\n2|@@timezone_gmt_plus_0200@@\r\n3|@@timezone_gmt_plus_0300@@\r\n3.5|@@timezone_gmt_plus_0330@@\r\n4|@@timezone_gmt_plus_0400@@\r\n4.5|@@timezone_gmt_plus_0430@@\r\n5|@@timezone_gmt_plus_0500@@\r\n5.5|@@timezone_gmt_plus_0530@@\r\n5.75|@@timezone_gmt_plus_0545@@\r\n6|@@timezone_gmt_plus_0600@@\r\n6.5|@@timezone_gmt_plus_0630@@\r\n7|@@timezone_gmt_plus_0700@@\r\n8|@@timezone_gmt_plus_0800@@\r\n9|@@timezone_gmt_plus_0900@@\r\n9.5|@@timezone_gmt_plus_0930@@\r\n10|@@timezone_gmt_plus_1000@@\r\n11|@@timezone_gmt_plus_1100@@\r\n12|@@timezone_gmt_plus_1200@@', NULL, '@@help_timezone@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (14, 'main', 'date_format', 'd-m-Y, H:i', '@@date_format@@', 'text', NULL, NULL, '@@help_date_format@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (15, 'main', 'default_email', 'admin@localhost.ua', '@@default_email@@', 'text', NULL, NULL, '@@help_default_email@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (16, 'image_options', 'file_types', 'jpg,png,gif,php', '@@file_types@@', 'text', '', NULL, '@@help_file_types@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (17, 'image_options', 'file_height', '120', '@@file_height@@', 'text', NULL, 'numeric', '@@help_file_height@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (18, 'image_options', 'file_size', '256000', '@@file_size@@', 'text', NULL, NULL, '@@help_file_size@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (19, 'image_options', 'file_width', '160', '@@file_width@@', 'text', NULL, 'numeric', '@@help_file_width@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (20, 'image_options', 'avatar_width', '120', '@@avatar_width@@', 'text', NULL, 'numeric', '@@help_avatar_width@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (21, 'image_options', 'avatar_height', '120', '@@avatar_height@@', 'text', NULL, 'numeric', '@@help_avatar_height@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (22, 'image_options', NULL, '', '@@image_options@@', 'text', '40', NULL, NULL, 'ACPanel');
INSERT INTO `acp_config` VALUES (23, 'image_options', 'avatar_thumb_width', '48', '@@avatar_thumb_width@@', 'text', NULL, 'numeric', '@@help_avatar_thumb_width@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (24, 'image_options', 'avatar_thumb_height', '48', '@@avatar_thumb_height@@', 'text', NULL, 'numeric', '@@help_avatar_thumb_height@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (25, 'cloud_servers', 'cloud_cache', '1', '@@cloud_cache@@', 'boolean', NULL, NULL, '@@help_cloud_cache@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (26, 'cloud_servers', 'cloud_limit', '10', '@@cloud_limit@@', 'text', NULL, NULL, '@@help_cloud_limit@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (27, 'cloud_servers', 'cloud_erase', '', '@@cloud_erase@@', 'text', NULL, NULL, '@@help_cloud_erase@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (28, 'cloud_servers', 'cloud_speed', '150', '@@cloud_speed@@', 'text', NULL, NULL, '@@help_cloud_speed@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (29, 'cloud_servers', 'cloud_width', '100%', '@@cloud_width@@', 'text', NULL, NULL, '@@help_cloud_width@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (30, 'cloud_servers', 'cloud_height', '150', '@@cloud_height@@', 'text', NULL, NULL, '@@help_cloud_height@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (31, 'cloud_servers', NULL, '', '@@cloud_servers@@', 'text', '20', NULL, NULL, 'ACPanel');
INSERT INTO `acp_config` VALUES (32, 'cloud_servers', 'cloud_cache_time', '15', '@@cloud_cache_time@@', 'text', 'size=5', 'numeric', '@@help_cloud_cache_time@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (33, 'users_reg', 'reg_type', '1', '@@reg_type@@', 'select', '1|@@reg_closed@@\r\n2|@@reg_site_email_activated_no@@\r\n3|@@reg_site_email_activated_yes@@\r\n4|@@reg_soft@@', NULL, '@@help_reg_type@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (34, 'users_reg', 'username_minlen', '3', '@@username_minlen@@', 'text', 'size=5', 'numeric', '@@help_username_minlen@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (35, 'users_reg', 'username_maxlen', '25', '@@username_maxlen@@', 'text', 'size=5', 'numeric', '@@help_username_maxlen@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (36, 'users_reg', 'passwd_minlen', '6', '@@passwd_minlen@@', 'text', 'size=5', 'numeric', '@@help_passwd_minlen@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (37, 'users_reg', 'group_for_new_user', '2', '@@group_for_new_user@@', 'select', 'acp_usergroups|usergroupid|usergroupname', 'select', '@@help_group_for_new_user@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (38, 'users_reg', NULL, '', '@@users_reg@@', 'text', '50', NULL, NULL, 'ACPanel');
INSERT INTO `acp_config` VALUES (39, 'monitoring', NULL, '', '@@monitoring@@', 'text', '70', NULL, NULL, 'ACPanel');
INSERT INTO `acp_config` VALUES (40, 'monitoring', 'home_refresh_time', '25', '@@home_refresh_time@@', 'text', 'size=5', 'numeric', '@@help_home_refresh_time@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (41, 'monitoring', 'mon_hide_offline', '0', '@@mon_hide_offline@@', 'boolean', NULL, NULL, '@@help_mon_hide_offline@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (42, 'monitoring', 'mon_view_per_page', '15', '@@mon_view_per_page@@', 'text', 'size=5', 'numeric', '@@help_mon_view_per_page@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (43, 'monitoring', 'mon_cache', '1', '@@mon_cache@@', 'boolean', NULL, NULL, '@@help_mon_cache@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (44, 'monitoring', 'mon_cache_time', '5', '@@mon_cache_time@@', 'text', 'size=5', 'numeric', '@@help_mon_cache_time@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (45, 'monitoring', 'mon_servers_favorites', '', '@@mon_servers_favorites@@', 'text', NULL, NULL, '@@help_mon_servers_favorites@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (46, 'main', 'vkid', '', '@@vkid@@', 'text', 'size=5', 'numeric', '@@help_vkid@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (47, 'main', 'home_title', 'Мониторинг игровых серверов', '@@home_title@@', 'text', NULL, NULL, '@@help_home_title@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (48, 'monitoring', 'mon_view_lifetime', '1', '@@mon_view_lifetime@@', 'text', 'size=5', 'numeric', '@@help_mon_view_lifetime@@', 'ACPanel');
INSERT INTO `acp_config` VALUES (49, 'monitoring', 'mon_moderated', '1', '@@mon_moderated@@', 'boolean', '', NULL, '@@help_mon_moderated@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (50, 'monitoring', 'mon_name_maxlen', '30', '@@mon_name_maxlen@@', 'text', 'size=5', 'numeric', '@@help_mon_name_maxlen@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (51, 'monitoring', 'mon_name_minlen', '5', '@@mon_name_minlen@@', 'text', 'size=5', 'numeric', '@@help_mon_name_minlen@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (52, 'monitoring', 'mon_vote_multiple', '0', '@@mon_vote_multiple@@', 'boolean', '', NULL, '@@help_mon_vote_multiple@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (53, 'monitoring', 'mon_vote_guests', '0', '@@mon_vote_guests@@', 'boolean', '', NULL, '@@help_mon_vote_guests@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (54, 'monitoring', 'mon_vote_user_weight', '2', '@@mon_vote_user_weight@@', 'text', 'size=5', 'numeric', '@@help_mon_vote_user_weight@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (55, 'monitoring', 'mon_vote_lifetime', '1440', '@@mon_vote_lifetime@@', 'text', 'size=5', 'numeric', '@@help_mon_vote_lifetime@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (56, 'monitoring', 'mon_vote_cookie', '', '@@mon_vote_cookie@@', 'text', '', NULL, '@@help_mon_vote_cookie@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (57, 'monitoring', 'mon_vote_format', '{+BALANCE}', '@@mon_vote_format@@', 'text', '', NULL, '@@help_mon_vote_format@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (58, 'monitoring', 'rating_formula', '({description}*20)+{viewed}+{votes}+{online}+{uptime}+({pr}*30/10)+({cy}*30/100)+({banner}*30)+{vklikes}', '@@rating_formula@@', 'text', '', NULL, '@@help_rating_formula@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (59, 'monitoring', 'mon_time_prcy', '30', '@@mon_time_prcy@@', 'text', 'size=5', 'numeric', '@@help_mon_time_prcy@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (60, 'monitoring', 'mon_time_site', '1', '@@mon_time_site@@', 'text', 'size=5', 'numeric', '@@help_mon_time_site@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (61, 'monitoring', 'mon_descr_length', '100', '@@mon_descr_length@@', 'text', 'size=5', 'numeric', '@@help_mon_descr_length@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (62, 'monitoring', 'mon_time_vklike', '1440', '@@mon_time_vklike@@', 'text', 'size=5', 'numeric', '@@help_mon_time_vklike@@', 'ratingServers');
INSERT INTO `acp_config` VALUES (63, 'game_accounts', NULL, '', '@@game_accounts@@', 'text', '60', NULL, NULL, 'gameAccounts');
INSERT INTO `acp_config` VALUES (64, 'game_accounts', 'default_access', '20', '@@default_access@@', 'select', 'acp_access_mask|mask_id|access_flags', 'select', '@@help_default_access@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (65, 'game_accounts', 'ga_nicklen_max', '30', '@@ga_nicklen_max@@', 'text', 'size=5', 'numeric', '@@help_ga_nicklen_max@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (66, 'game_accounts', 'ga_nicklen_min', '3', '@@ga_nicklen_min@@', '', '', 'numeric', '@@help_ga_nicklen_min@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (67, 'game_accounts', 'ticket_moderate', '1', '@@ticket_moderate@@', 'boolean', NULL, NULL, '@@help_ticket_moderate@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (68, 'game_accounts', 'ga_time_format', 'dddd hhhh mmmm ssss', '@@ga_time_format@@', 'text', NULL, 'numeric', '@@help_ga_time_format@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (69, 'game_accounts', 'ga_access_type', 'by_nick,by_steam', '@@ga_access_type@@', 'checkbox', 'by_nick|@@type_by_nick@@\r\nby_ip|@@type_by_ip@@\r\nby_steam|@@type_by_steam@@', NULL, '@@help_ga_access_type@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (70, 'game_accounts', 'ga_registration', '2', '@@ga_registration@@', 'select', '1|@@ga_reg_closed@@\r\n2|@@ga_reg_site@@\r\n3|@@ga_reg_soft@@', NULL, '@@help_ga_registration@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (71, 'game_accounts', 'ga_cache_block_accounts', '10', '@@ga_cache_block_accounts@@', 'text', 'size=5', 'numeric', '@@help_ga_cache_block_accounts@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (72, 'game_accounts', 'ga_active_time', '7', '@@ga_active_time@@', 'text', 'size=5', 'numeric', '@@help_ga_active_time@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (73, 'game_accounts', 'ga_admin_flag', 'd', '@@ga_admin_flag@@', 'select', '|@@ga_flag_ignore@@\r\na|Flag \"a\", immunity (can\'t be kicked/baned/slayed/slaped and affected by other commmands)\r\nb|Flag \"b\", reservation (can join on reserved slots)\nc|Flag \"c\", amx_kick command\nd|Flag \"d\", amx_ban and amx_unban commands (permanent and temporary bans)\r\ne|Flag \"e\", amx_slay and amx_slap commands\r\nf|Flag \"f\", amx_map command\r\ng|Flag \"g\", amx_cvar command (not all cvars will be available)\r\nh|Flag \"h\", amx_cfg command\r\ni|Flag \"i\", amx_chat and other chat commands\r\nj|Flag \"j\", amx_vote and other vote commands\r\nk|Flag \"k\", access to sv_password cvar (by amx_cvar command)\r\nl|Flag \"l\", access to amx_rcon command and rcon_password cvar (by amx_cvar command)\r\nm|Flag \"m\", custom level A (for additional plugins)\r\nn|Flag \"n\", custom level B\r\no|Flag \"o\", custom level C\r\np|Flag \"p\", custom level D\r\nq|Flag \"q\", custom level E\r\nr|Flag \"r\", custom level F\r\ns|Flag \"s\", custom level G\r\nt|Flag \"t\", user (no admin)\r\nu|Flag \"u\", menu access\nv|Flag \"v\", amx_ban and amx_unban commands (temporary bans only, about amx_unban, only self performed ban during map gonna be allowed)', NULL, '@@help_ga_admin_flag@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (74, 'game_accounts', 'ga_steam_validate', '^STEAM_0:(0|1):\\d{4,9}$', '@@ga_steam_validate@@', 'text', NULL, NULL, '@@help_ga_steam_validate@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (75, 'game_accounts', 'default_access_time', '', '@@default_access_time@@', 'text', 'size=5', 'numeric', '@@help_default_access_time@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (76, 'game_accounts', 'ga_password_validate', '', '@@ga_password_validate@@', 'text', NULL, NULL, '@@help_ga_password_validate@@', 'gameAccounts');
INSERT INTO `acp_config` VALUES (77, 'game_bans', NULL, '', '@@gamebans@@', 'text', '80', NULL, NULL, 'gameBans');
INSERT INTO `acp_config` VALUES (78, 'game_bans', 'gb_length_format', 'dddd hhhh mmmm ssss', '@@gb_length_format@@', 'text', '', '', '@@help_gb_length_format@@', 'gameBans');
INSERT INTO `acp_config` VALUES (79, 'game_bans', 'gb_view_per_page', '30', '@@gb_view_per_page@@', 'text', 'size=5', 'numeric', '@@help_gb_view_per_page@@', 'gameBans');
INSERT INTO `acp_config` VALUES (80, 'game_bans', 'gb_display_admin', '1', '@@gb_display_admin@@', 'boolean', NULL, NULL, '@@help_gb_display_admin@@', 'gameBans');
INSERT INTO `acp_config` VALUES (81, 'game_bans', 'gb_bans_select', '1', '@@gb_bans_select@@', 'select', '0|@@gb_bans_all@@\r\n1|@@gb_bans_active@@\r\n2|@@gb_bans_passed@@', '', '@@help_gb_bans_select@@', 'gameBans');
INSERT INTO `acp_config` VALUES (82, 'game_bans', 'gb_cache_block_stats', '600', '@@gb_cache_block_stats@@', 'text', 'size=5', 'numeric', '@@help_gb_cache_block_stats@@', 'gameBans');
INSERT INTO `acp_config` VALUES (83, 'game_bans', 'gb_topstats_max', '10', '@@gb_topstats_max@@', 'text', 'size=5', 'numeric', '@@help_gb_topstats_max@@', 'gameBans');
INSERT INTO `acp_config` VALUES (84, 'game_bans', 'gb_topstats_cache', '60', '@@gb_topstats_cache@@', 'text', 'size=5', 'numeric', '@@help_gb_topstats_cache@@', 'gameBans');
INSERT INTO `acp_config` VALUES (85, 'game_bans', 'gb_block_admins_max', '9', '@@gb_block_admins_max@@', 'text', 'size=5', 'numeric', '@@help_gb_block_admins_max@@', 'gameBans');
INSERT INTO `acp_config` VALUES (86, 'chat_control', 'cc_cmd', 'say,say_team', '@@cc_cmd@@', 'checkbox', 'say|@@say@@\r\nsay_team|@@say_team@@\r\namx_chat|@@amx_chat@@', NULL, '@@help_cc_cmd@@', 'chatControl');
INSERT INTO `acp_config` VALUES (87, 'chat_control', NULL, '', '@@chat_control@@', 'text', '30', NULL, NULL, 'chatControl');
INSERT INTO `acp_config` VALUES (88, 'chat_control', 'cc_foradmins', '1', '@@cc_foradmins@@', 'boolean', NULL, NULL, '@@help_cc_foradmins@@', 'chatControl');
INSERT INTO `acp_config` VALUES (89, 'chat_control', 'cc_alive', '1', '@@cc_alive@@', 'boolean', NULL, NULL, '@@help_cc_alive@@', 'chatControl');
INSERT INTO `acp_config` VALUES (90, 'chat_control', 'cc_servers', '127.0.0.1:27015', '@@cc_servers@@', 'select', 'acp_servers|address|hostname', 'multiple', '@@help_cc_servers@@', 'chatControl');
INSERT INTO `acp_config` VALUES (91, 'chat_control', 'cc_limit', '0', '@@cc_limit@@', 'text', 'size=5', 'numeric', '@@help_cc_limit@@', 'chatControl');
INSERT INTO `acp_config` VALUES (92, 'chat_control', 'cc_delay', '0', '@@cc_delay@@', 'text', 'size=5', 'numeric', '@@help_cc_delay@@', 'chatControl');
INSERT INTO `acp_config` VALUES (93, 'chat_control', 'cc_block_msg', '0', '@@cc_block_msg@@', 'boolean', NULL, NULL, '@@help_cc_block_msg@@', 'chatControl');
INSERT INTO `acp_config` VALUES (94, 'chat_control', 'cc_refresh', '25', '@@cc_refresh@@', 'text', NULL, NULL, '@@help_cc_refresh@@', 'chatControl');
INSERT INTO `acp_config` VALUES (95, 'game_stats', NULL, '', '@@gamestats@@', 'text', '90', NULL, NULL, 'gameStats');
INSERT INTO `acp_config` VALUES (96, 'game_stats', 'stats_skill_formula', '((2*{wins}/({team_t}+{team_ct}))+(5*{hs}/{kills})+({streak_kills}/{streak_deaths})+(2*{kills}/{deaths})+(60*{kills}/{online}))*{activity}', '@@stats_skill_formula@@', 'text', '', '', '@@help_stats_skill_formula@@', 'gameStats');
INSERT INTO `acp_config` VALUES (97, 'game_stats', 'stats_activity_time', '744', '@@stats_activity_time@@', 'text', 'size=5', 'numeric', '@@help_stats_activity_time@@', 'gameStats');
INSERT INTO `acp_config` VALUES (98, 'game_stats', 'stats_skill_min_kills', '1', '@@stats_activity_time@@', 'text', 'size=5', 'numeric', '@@help_stats_skill_min_kills@@', 'gameStats');
INSERT INTO `acp_config` VALUES (99, 'game_stats', 'stats_players_per_page', '20', '@@stats_players_per_page@@', 'text', 'size=5', 'numeric', '@@help_stats_players_per_page@@', 'gameStats');
INSERT INTO `acp_config` VALUES (100, 'game_stats', 'stats_cache_blocks', '5', '@@stats_cache_blocks@@', 'text', 'size=5', 'numeric', '@@help_stats_cache_blocks@@', 'gameStats');
INSERT INTO `acp_config` VALUES (101, 'game_stats', 'stats_cache_time', '5', '@@stats_cache_time@@', 'text', 'size=5', 'numeric', '@@help_stats_cache_time@@', 'gameStats');
INSERT INTO `acp_config` VALUES (102, 'game_stats', 'stats_max_top_block', '10', '@@stats_max_top_block@@', 'text', 'size=5', 'numeric', '@@help_stats_max_top_block@@', 'gameStats');
INSERT INTO `acp_config` VALUES (103, 'user_bank', NULL, '', '@@user_bank@@', 'text', '100', NULL, NULL, 'userBank');
INSERT INTO `acp_config` VALUES (104, 'user_bank', 'ub_methods', 'robokassa,a1pay', '@@ub_methods@@', 'checkbox', 'robokassa|@@method_robokassa@@\r\na1pay|@@method_apay@@', '', '@@help_ub_methods@@', 'userBank');
INSERT INTO `acp_config` VALUES (105, 'user_bank', 'ub_min_payment', '50', '@@ub_min_payment@@', 'text', NULL, 'numeric', '@@help_ub_min_payment@@', 'userBank');
INSERT INTO `acp_config` VALUES (106, 'user_bank', 'ub_currency_suffix', ' руб.', '@@ub_currency_suffix@@', 'text', NULL, NULL, '@@help_ub_currency_suffix@@', 'userBank');
INSERT INTO `acp_config` VALUES (107, 'user_bank', 'ub_pagesize', '15', '@@ub_pagesize@@', 'text', 'size=5', 'numeric', '@@help_ub_pagesize@@', 'userBank');
INSERT INTO `acp_config` VALUES (108, 'user_bank', 'ub_rate_points', '0.1', '@@ub_rate_points@@', 'text', NULL, 'numeric', '@@help_ub_rate_points@@', 'userBank');
INSERT INTO `acp_config` VALUES (109, 'user_bank', 'ub_commission_exchanger', '95', '@@ub_commission_exchanger@@', 'text', NULL, NULL, '@@help_ub_commission_exchanger@@', 'userBank');
INSERT INTO `acp_config` VALUES (110, 'user_bank', 'ub_robo_login', '', '@@ub_robo_login@@', 'text', NULL, NULL, '@@help_ub_robo_login@@', 'userBank');
INSERT INTO `acp_config` VALUES (111, 'user_bank', 'ub_robo_password_one', '', '@@ub_robo_password_one@@', 'text', NULL, NULL, '@@help_ub_robo_password_one@@', 'userBank');
INSERT INTO `acp_config` VALUES (112, 'user_bank', 'ub_robo_password_two', '', '@@ub_robo_password_two@@', 'text', NULL, NULL, '@@help_ub_robo_password_two@@', 'userBank');
INSERT INTO `acp_config` VALUES (113, 'user_bank', 'ub_robo_merchant_url', 'https://merchant.roboxchange.com/Index.aspx', '@@ub_robo_merchant_url@@', 'text', NULL, NULL, '@@help_ub_robo_merchant_url@@', 'userBank');
INSERT INTO `acp_config` VALUES (114, 'user_bank', 'ub_robo_default_currency', '', '@@ub_robo_default_currency@@', 'text', NULL, NULL, '@@help_ub_robo_default_currency@@', 'userBank');
INSERT INTO `acp_config` VALUES (115, 'user_bank', 'ub_robo_memo', '', '@@ub_robo_memo@@', 'text', NULL, NULL, '@@help_ub_robo_memo@@', 'userBank');
INSERT INTO `acp_config` VALUES (116, 'user_bank', 'ub_apay_memo', '', '@@ub_apay_memo@@', 'text', NULL, NULL, '@@help_ub_apay_memo@@', 'userBank');
INSERT INTO `acp_config` VALUES (117, 'user_bank', 'ub_apay_key', '', '@@ub_apay_key@@', 'text', NULL, NULL, '@@help_ub_apay_key@@', 'userBank');
INSERT INTO `acp_config` VALUES (118, 'user_bank', 'ub_apay_secretkey', '', '@@ub_apay_secretkey@@', 'text', NULL, NULL, '@@help_ub_apay_secretkey@@', 'userBank');
INSERT INTO `acp_config` VALUES (119, 'user_bank', 'ub_apay_merchant_url', 'https://partner.a1pay.ru/a1lite/input/', '@@ub_apay_merchant_url@@', 'text', NULL, NULL, '@@help_ub_apay_merchant_url@@', 'userBank');
COMMIT;

-- ----------------------------
-- Table structure for acp_cron_entry
-- ----------------------------
DROP TABLE IF EXISTS `acp_cron_entry`;
CREATE TABLE `acp_cron_entry` (
  `entry_id` int(10) NOT NULL AUTO_INCREMENT,
  `cron_file` varchar(75) NOT NULL,
  `run_rules` varchar(255) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL,
  `product_id` varchar(25) NOT NULL,
  `task_update` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`entry_id`),
  KEY `active_next_run` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_cron_entry
-- ----------------------------
BEGIN;
INSERT INTO `acp_cron_entry` VALUES (1, 'monitoring_update_cache.php', 'a:6:{i:0;s:2:\"00\";i:1;s:1:\"*\";i:2;s:1:\"*\";i:3;s:1:\"*\";i:4;s:1:\"*\";i:5;s:1:\"*\";}', 1, 'ratingServers', 1451915848);
INSERT INTO `acp_cron_entry` VALUES (2, 'monitoring_update_rating.php', 'a:6:{i:0;s:2:\"00\";i:1;s:1:\"*\";i:2;s:1:\"*\";i:3;s:1:\"*\";i:4;s:1:\"*\";i:5;s:1:\"*\";}', 1, 'ratingServers', 1351242835);
INSERT INTO `acp_cron_entry` VALUES (3, 'prune_bans.php', 'a:6:{i:0;s:2:\"00\";i:1;s:1:\"*\";i:2;s:1:\"*\";i:3;s:1:\"*\";i:4;s:1:\"*\";i:5;s:1:\"*\";}', 1, 'gameBans', 1358958876);
INSERT INTO `acp_cron_entry` VALUES (4, 'stats_update_list.php', 'a:6:{i:0;s:2:\"00\";i:1;s:1:\"*\";i:2;s:1:\"*\";i:3;s:1:\"*\";i:4;s:1:\"*\";i:5;s:1:\"*\";}', 1, 'gameStats', 1358958870);
INSERT INTO `acp_cron_entry` VALUES (5, 'clearing_debris.php', 'a:6:{i:0;s:2:\"00\";i:1;s:1:\"*\";i:2;s:1:\"*\";i:3;s:1:\"*\";i:4;s:2:\"06\";i:5;s:1:\"*\";}', 1, 'ACPanel', 1368898925);
COMMIT;

-- ----------------------------
-- Table structure for acp_cron_log
-- ----------------------------
DROP TABLE IF EXISTS `acp_cron_log`;
CREATE TABLE `acp_cron_log` (
  `logid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(10) NOT NULL DEFAULT '0',
  `dateline` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`logid`),
  KEY `entry_id` (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_gameshop
-- ----------------------------
DROP TABLE IF EXISTS `acp_gameshop`;
CREATE TABLE `acp_gameshop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_descr` varchar(32) NOT NULL DEFAULT '',
  `web_descr` varchar(40) NOT NULL DEFAULT '',
  `cost` int(11) NOT NULL DEFAULT '0',
  `duration` int(11) NOT NULL DEFAULT '1',
  `cmd` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_gameshop_servers
-- ----------------------------
DROP TABLE IF EXISTS `acp_gameshop_servers`;
CREATE TABLE `acp_gameshop_servers` (
  `item_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  UNIQUE KEY `item` (`item_id`,`server_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_hud_manager
-- ----------------------------
DROP TABLE IF EXISTS `acp_hud_manager`;
CREATE TABLE `acp_hud_manager` (
  `hud_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `flags` smallint(4) NOT NULL,
  `priority` int(11) NOT NULL,
  PRIMARY KEY (`hud_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_hud_manager
-- ----------------------------
BEGIN;
INSERT INTO `acp_hud_manager` VALUES (1, 'Hide all', 4, 3);
INSERT INTO `acp_hud_manager` VALUES (2, 'Hide flashlight', 2, 2);
INSERT INTO `acp_hud_manager` VALUES (3, 'Draw Additional Crosshair', 128, 8);
INSERT INTO `acp_hud_manager` VALUES (4, 'Hide crosshair, ammo, weapon list', 1, 1);
INSERT INTO `acp_hud_manager` VALUES (5, 'Hide Radar, Health, Armor', 8, 4);
INSERT INTO `acp_hud_manager` VALUES (6, 'Hide Timer', 16, 5);
INSERT INTO `acp_hud_manager` VALUES (7, 'Hide Money', 32, 6);
INSERT INTO `acp_hud_manager` VALUES (8, 'Hide all crosshairs', 64, 7);
COMMIT;

-- ----------------------------
-- Table structure for acp_lang
-- ----------------------------
DROP TABLE IF EXISTS `acp_lang`;
CREATE TABLE `acp_lang` (
  `lang_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lang_title` varchar(255) NOT NULL DEFAULT '',
  `lang_code` varchar(12) NOT NULL DEFAULT '',
  `lang_active` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`lang_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_lang
-- ----------------------------
BEGIN;
INSERT INTO `acp_lang` VALUES (1, 'English (US)', 'lw_en', 'yes');
INSERT INTO `acp_lang` VALUES (2, 'Русский (RU)', 'lw_ru', 'yes');
COMMIT;

-- ----------------------------
-- Table structure for acp_lang_pages
-- ----------------------------
DROP TABLE IF EXISTS `acp_lang_pages`;
CREATE TABLE `acp_lang_pages` (
  `lp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lp_name` varchar(255) NOT NULL DEFAULT '',
  `productid` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`lp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=172 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_lang_pages
-- ----------------------------
BEGIN;
INSERT INTO `acp_lang_pages` VALUES (1, 'login.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (2, 'footer.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (3, 'header.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (4, 'p_general.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (5, 'homepage.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (6, '404.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (7, 'under_constructions.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (8, 'p_general_phrase_search.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (9, 'p_general_phrase_search_result.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (10, 'p_general_phrase_search_load.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (11, 'p_general_blocks.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (12, 'p_general_blocks_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (13, 'p_general_blocks_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (14, 'p_general_blocks_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (15, 'p_users.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (16, 'p_users_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (17, 'p_users_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (18, 'p_users_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (19, 'p_users_search.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (20, 'p_users_search_result.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (21, 'p_users_search_load.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (22, 'page_small.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (23, 'p_servers_control.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (24, 'p_servers_control_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (25, 'p_servers_control_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (26, 'p_servers_control_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (27, 'p_servers_control_refresh.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (28, 'homepage_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (29, 'blocks/block_steam_tool.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (30, 'blocks/block_cloud_servers.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (31, 'register.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (32, 'homepage_info.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (33, 'page.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (34, 'p_admins.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (35, 'p_admins_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (36, 'p_general_options.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (37, 'p_general_lang.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (38, 'p_general_lang_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (39, 'p_general_phrases_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (40, 'p_general_phrases.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (41, 'p_general_lang_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (42, 'p_general_lang_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (43, 'p_general_phrase_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (44, 'p_general_phrase_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (45, 'p_general_categories_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (46, 'p_general_categories_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (48, 'p_general_categories.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (49, 'p_general_categories_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (50, 'p_products.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (51, 'p_products_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (52, 'p_products_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (53, 'p_general_logs.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (54, 'p_general_logs_result.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (55, 'p_general_logs_load.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (56, 'p_optimization.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (57, 'optimization/check_category_tree.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (58, 'optimization/php_settings.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (59, 'p_usergroups.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (60, 'p_usergroups_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (61, 'p_usergroups_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (62, 'p_usergroups_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (63, 'profile.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (64, 'profile_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (65, 'optimization/clear_cache.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (66, 'p_general_phrases_template.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (67, 'p_general_phrases_template_add.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (68, 'p_general_phrases_template_edit.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (69, 'p_general_phrases_template_list.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (70, 'p_server_card.tpl', 'ratingServers');
INSERT INTO `acp_lang_pages` VALUES (71, 'task_sheduler.tpl', 'taskSheduler');
INSERT INTO `acp_lang_pages` VALUES (72, 'task_sheduler_list.tpl', 'taskSheduler');
INSERT INTO `acp_lang_pages` VALUES (73, 'task_sheduler_add.tpl', 'taskSheduler');
INSERT INTO `acp_lang_pages` VALUES (74, 'task_sheduler_edit.tpl', 'taskSheduler');
INSERT INTO `acp_lang_pages` VALUES (75, 'p_gamecp.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (76, 'p_gamecp_accounts.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (77, 'p_gamecp_accounts_list.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (78, 'p_gamecp_accounts_add.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (79, 'p_gamecp_accounts_edit.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (80, 'profile_tickets.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (81, 'p_gamecp_mask.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (82, 'p_gamecp_mask_list.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (83, 'p_gamecp_mask_add.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (84, 'p_gamecp_mask_edit.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (85, 'profile_account.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (86, 'profile_account_load.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (87, 'p_gamecp_requests.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (88, 'p_gamecp_requests_list.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (89, 'p_gamecp_requests_edit.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (90, 'blocks/block_accounts_stats.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (91, 'p_gamecp_mask_serverlist.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (92, 'p_gamecp_search.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (93, 'p_gamecp_search_result.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (94, 'p_gamecp_search_load.tpl', 'gameAccounts');
INSERT INTO `acp_lang_pages` VALUES (95, 'p_gamebans.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (96, 'p_gamebans_players.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (97, 'p_gamebans_players_list.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (98, 'p_gamebans_players_edit.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (99, 'p_gamebans_players_add.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (100, 'p_gamebans_reasons.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (101, 'p_gamebans_reasons_list.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (102, 'p_gamebans_reasons_add.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (103, 'p_gamebans_subnets.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (104, 'p_gamebans_subnets_list.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (105, 'p_gamebans_subnets_add.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (106, 'p_gamebans_subnets_edit.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (107, 'p_gamebans_search.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (108, 'p_gamebans_search_result.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (109, 'p_gamebans_search_load.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (110, 'p_gamebans_public_players.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (111, 'p_gamebans_public_players_list.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (112, 'p_gamebans_public_players_view.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (113, 'blocks/block_bans_stats.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (114, 'p_gamebans_public_subnets_list.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (115, 'p_gamebans_public_subnets.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (116, 'p_gamebans_public_stats.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (117, 'p_gamebans_public_stats_list.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (118, 'p_gamebans_public_stats_top.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (119, 'blocks/block_bans_best_admin.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (120, 'optimization/bans_prune.tpl', 'gameBans');
INSERT INTO `acp_lang_pages` VALUES (121, 'p_cc_commands.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (122, 'p_cc_commands_list.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (123, 'p_cc_commands_add.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (124, 'p_cc_patterns.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (125, 'p_cc_patterns_list.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (126, 'p_cc_patterns_add.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (127, 'p_cc_logs.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (128, 'p_cc_logs_result.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (129, 'p_cc_logs_load.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (130, 'p_gamechat.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (131, 'p_gamechat_list.tpl', 'chatControl');
INSERT INTO `acp_lang_pages` VALUES (132, 'p_hm_patterns.tpl', 'hudManager');
INSERT INTO `acp_lang_pages` VALUES (133, 'p_hm_patterns_list.tpl', 'hudManager');
INSERT INTO `acp_lang_pages` VALUES (134, 'p_hm_patterns_add.tpl', 'hudManager');
INSERT INTO `acp_lang_pages` VALUES (135, 'p_hm_patterns_edit.tpl', 'hudManager');
INSERT INTO `acp_lang_pages` VALUES (136, 'p_nc_patterns.tpl', 'nickControl');
INSERT INTO `acp_lang_pages` VALUES (137, 'p_nc_patterns_list.tpl', 'nickControl');
INSERT INTO `acp_lang_pages` VALUES (138, 'p_nc_patterns_add.tpl', 'nickControl');
INSERT INTO `acp_lang_pages` VALUES (139, 'p_nc_logs.tpl', 'nickControl');
INSERT INTO `acp_lang_pages` VALUES (140, 'p_nc_logs_result.tpl', 'nickControl');
INSERT INTO `acp_lang_pages` VALUES (141, 'p_nc_logs_load.tpl', 'nickControl');
INSERT INTO `acp_lang_pages` VALUES (142, 'blocks/block_check_nick.tpl', 'nickControl');
INSERT INTO `acp_lang_pages` VALUES (143, 'profile_balance.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (144, 'profile_transactions.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (145, 'p_usershop_admin.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (146, 'p_usershop_admin_groups.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (147, 'p_usershop_admin_groups_list.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (148, 'p_usershop_admin_groups_add.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (149, 'p_usershop_admin_groups_edit.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (150, 'p_usershop_admin_payments_list.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (151, 'p_usershop_admin_payments.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (152, 'p_usershop_admin_patterns.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (153, 'p_usershop_admin_patterns_list.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (154, 'p_usershop_admin_patterns_add.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (155, 'p_usershop_admin_patterns_edit.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (156, 'p_usershop_admin_patterns_user.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (157, 'p_usershop_admin_patterns_user_list.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (158, 'p_usershop_admin_patterns_user_detail.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (159, 'p_usershop.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (160, 'p_usershop_buywindow.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (161, 'profile_shop_privileges.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (162, 'profile_shop_privileges_detail.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (163, 'p_gameshop_items.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (164, 'p_gameshop_items_list.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (165, 'p_gameshop_items_servers.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (166, 'p_gameshop_items_add.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (167, 'p_gameshop_items_edit.tpl', 'userBank');
INSERT INTO `acp_lang_pages` VALUES (168, 'blocks/block_user_info.tpl', 'ACPanel');
INSERT INTO `acp_lang_pages` VALUES (169, 'p_vbk_logs_result.tpl', 'voteBanKick');
INSERT INTO `acp_lang_pages` VALUES (170, 'p_vbk_logs_load.tpl', 'voteBanKick');
INSERT INTO `acp_lang_pages` VALUES (171, 'p_vbk_logs.tpl', 'voteBanKick');
COMMIT;

-- ----------------------------
-- Table structure for acp_lang_words
-- ----------------------------
DROP TABLE IF EXISTS `acp_lang_words`;
CREATE TABLE `acp_lang_words` (
  `lw_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lw_word` varchar(255) NOT NULL,
  `lw_page` int(10) NOT NULL,
  `lw_en` text NOT NULL,
  `lw_ru` text NOT NULL,
  `productid` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`lw_id`),
  UNIQUE KEY `lw_word` (`lw_word`,`lw_page`)
) ENGINE=MyISAM AUTO_INCREMENT=2494 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_lang_words
-- ----------------------------
BEGIN;
INSERT INTO `acp_lang_words` VALUES (1, 'login_info', 1, 'Enter your username and password.', 'Введите имя пользователя и пароль.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2, 'login_user', 1, 'Username:', 'Имя пользователя:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (3, 'login_pass', 1, 'Password:', 'Пароль:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (4, 'login_submit', 1, 'Join', 'Войти', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (5, 'login_head', 1, 'Login', 'Вход', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (6, 'footer_page_gen', 2, 'Page generated time:', 'Генерация страницы:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (7, 'footer_gen_sec', 2, 'sec.', 'сек.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (8, 'login_err_empty', 1, 'All fields must not be empty!', 'Все поля должны быть заполнены!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (9, 'login_err_valid', 1, 'Entered an incorrect username or password!', 'Неверно введен логин или пароль!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (10, 'login_title', 3, 'Authorization', 'Авторизация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (11, 'home', 0, 'Game servers', 'Игровые сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (12, 'language', 4, 'Default language', 'Язык по-умолчанию', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (13, 'pagesize', 4, 'Number of results per page', 'Количество строк на страницу', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (14, 'panel_settings', 0, 'Panel Settings', 'Настройки Панели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (15, 'control_servers', 0, 'Servers Managment', 'Управление Серверами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (16, 'general_options', 0, 'General Options', 'Основные Настройки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (17, 'title', 6, '404 error', '404 ошибка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (18, 'head', 6, '404 error', '404 ошибка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (19, 'home_link', 6, 'go to home', 'перейти на главную', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (20, 'error_text', 6, 'The requested page does not exist or you do not have sufficient permissions to view it.', 'Запрашиваемая страница не существует или у Вас не достаточно прав для её просмотра.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (21, 'title', 7, 'Under constructions', 'Сайт закрыт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (22, 'head', 7, 'Under constructions', 'Сайт закрыт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (23, 'info_text', 7, 'At the moment access to the site is closed. Technical work is underway. We apologize for any inconvenience.', 'В данный момент доступ на сайт закрыт. Ведутся технические работы. Приносим извинения за возможные неудобства.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (24, 'sign_in', 3, 'Sign In', 'Вход', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (25, 'go_home', 3, 'Go home', 'На главную', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (26, 'empty_data', 0, '- no data available -', '- данные отсутствуют -', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (27, 'showing', 0, 'Showing', 'Показано с', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (28, 'to', 0, 'to', 'по', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (29, 'of', 0, 'of', 'из', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (30, 'tbl_srv_error', 0, 'Table servers missing!', 'Таблица с серверами отсутствует!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (31, 'tbl_srv_empty', 0, 'Table servers is empty!', 'Таблица с серверами пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (32, 'search_empty', 0, 'Sorry, nothing found. Try to specify other search options.', 'Извините, ничего не найдено. Попробуйте указать другие настройки поиска.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (33, 'from', 0, 'From:', 'От:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (34, 'totime', 0, 'To:', 'До:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (35, 'head', 23, 'Servers managment', 'Управление серверами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (36, 'game_panel', 0, 'Game Panel', 'Игровая панель', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (37, 'added', 24, 'Added', 'Добавил', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (38, 'address', 24, 'Address', 'Адрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (39, 'hostname', 24, 'Hostname', 'Название сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (40, 'del_selected', 24, 'Remove selected', 'Удалить записи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (41, 'not_selected', 24, 'Entries not selected!', 'Записи не выбраны!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (42, 'confirm_del', 24, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (43, 'add_server', 23, 'Add server', 'Добавить сервер', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (44, 'add_server', 25, 'Add server', 'Добавление сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (45, 'address', 25, 'Address', 'Адрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (46, 'hostname', 25, 'Hostname', 'Название сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (47, 'add', 25, 'Add', 'Добавить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (48, 'check_hostname', 25, 'Get hostname', 'Загрузить hostname', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (49, 'error_filesize', 23, 'Error adding map: file size greater than', 'Ошибка при добавлении карты: размер файла больше', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (50, 'users_and_groups', 0, 'Users and Groups', 'Пользователи и Группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (51, 'services', 0, 'Services', 'Обслуживание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (52, 'categories', 0, 'Section Management', 'Управление Разделами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (53, 'panel_logs', 0, 'Logs Panel', 'Логи Панели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (54, 'optimizing', 0, 'Testing / Optimization', 'Тест / Оптимизация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (55, 'languages', 0, 'Control Language', 'Управление Языками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (56, 'products', 0, 'Products', 'Продукты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (57, 'users_list', 0, 'List of Users', 'Список Пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (58, 'users_search', 0, 'Search Users', 'Поиск Пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (59, 'groups_setting', 0, 'Group Setting', 'Настройка Групп', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (60, 'rules', 0, 'Rules', 'Правила поведения', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (61, 'admins', 0, 'Administrations', 'Администрация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (62, 'gametype_not_valid', 23, 'Do not select the type of game!', 'Не выбран тип игры!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (63, 'dont_empty', 23, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (64, 'add_try', 23, 'This server already exists in the database!', 'Данный сервер уже существует в базе!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (65, 'add_success', 23, 'The server successfully added!', 'Сервер успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (66, 'add_failed', 23, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (67, 'del_success', 23, 'The server successfully removed!', 'Сервер успешно удален!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (68, 'del_failed', 23, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (69, 'del_multiply_success', 23, 'Successfully removed the servers:', 'Успешно удалены сервера:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (70, 'not_valid_server', 26, 'This server does not exist!', 'Этот сервер не существует!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (71, 'server', 26, 'Server', 'Сервер', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (72, 'address', 26, 'Address', 'Адрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (73, 'hostname', 26, 'Hostname', 'Название сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (74, 'save', 26, 'Save', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (75, 'edit', 26, 'Edit', 'Правка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (76, 'status', 27, 'Status:', 'Статус:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (77, 'map', 27, 'Current map:', 'Текущая карта:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (78, 'players', 27, 'Players:', 'Игроки:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (79, 'no_image', 23, 'Map image is missing', 'Изображение карты отсутствует', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (80, 'refresh', 27, 'Refresh', 'Обновить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (81, 'ping', 27, 'Ping:', 'Пинг:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (82, 'ms', 23, 'ms.', 'мс.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (83, 'upload_map', 27, 'Upload map', 'Загрузить карту', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (84, 'server', 3, 'Server', 'Сервер', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (85, 'loading', 0, 'Loading...', 'Загрузка...', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (86, 'edit_empty_field', 23, 'do not put a address!', 'не введен адрес сервера!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (87, 'hostname_empty_field', 23, 'do not put a hostname!', 'не введено название сервера!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (88, 'edit_try', 23, 'address already exists!', 'такой сервер уже существует!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (89, 'edit_error', 23, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (90, 'edit_success', 23, 'The server successfully edited!', 'Сервер успешно отредактирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (91, 'error_filetype', 23, 'Error adding map: invalid file type!', 'Ошибка при добавлении карты: неверный тип файла!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (92, 'already_map_pre', 27, 'Image for the map', 'Изображение для карты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (93, 'already_map_post', 27, 'already exists. Are you sure you want to replace it?', 'уже существует. Вы уверены, что хотите заменить его?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (94, 'error_fileincorrect', 23, 'Error adding map: invalid image properties!', 'Ошибка при добавлении карты: неверные свойства изображения!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (95, 'error_fileresize', 23, 'Error adding map: function for image processing does not exist!', 'Ошибка при добавлении карты: функция для обработки изображений не существует!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (96, 'nick', 27, 'Nick', 'Ник', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (97, 'frags', 27, 'Points', 'Очки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (98, 'time', 27, 'Duration', 'Время', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (99, 'total_players', 27, 'total players', 'всего игроков', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (100, 'total_frags', 27, 'total points', 'всего очков', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (101, 'head', 5, 'Game servers monitoring', 'Мониторинг игровых серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (102, 'hostname', 28, 'Hostname', 'Название сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (103, 'address', 28, 'Address', 'Адрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (104, 'map', 28, 'on map', 'на карте', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (105, 'online', 28, 'Now players', 'Сейчас игроков', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (106, 'connect', 28, 'Launch', 'Подключиться', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (107, 'players_info', 28, 'Players in the game', 'Информация по игрокам', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (108, 'country', 27, 'Country:', 'Страна:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (109, 'connect', 24, 'Launch', 'Подключиться', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (110, 'players_info', 23, 'Players in the game', 'Информация по игрокам', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (111, 'confirm_del', 12, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (112, 'convert', 29, 'Convert', 'Конвертировать', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (113, 'steam_label', 29, 'steamID/steamcommunityID', 'steamID/steamcommunityID', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (114, 'steam_wrong', 29, 'Invalid input!', 'Некорректный ввод!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (115, 'steam_empty', 29, 'The string is empty!', 'Данные не введены!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (116, 'cloud_flash_msg', 30, 'Block uses <strong>Flash</strong>. For full viewing requires a newer version<br /><strong>Adobe Flash Player</strong>', 'Блок использует технологию <strong>Flash</strong>. Для полноценного просмотра требуется более новая версия<br /><strong>Adobe Flash Player</strong>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (117, 'cloud_get_flash', 30, 'Get the Player Adobe Flash Player from the official Adobe website', 'Получить проигрыватель Adobe Flash Player с официального сайта Adobe', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (118, 'srv_not_resp', 28, '<em>- server not responding -</em>', '<em>- сервер не отвечает -</em>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (119, 'nick', 32, 'Nick', 'Ник', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (120, 'frags', 32, 'Points', 'Очки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (121, 'time', 32, 'Duration', 'Время', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (122, 'total_players', 32, 'total players', 'всего игроков', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (123, 'total_frags', 32, 'total points', 'всего очков', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (124, 'head', 34, 'Administrators', 'Администраторы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (125, 'monitoring', 36, 'Monitoring', 'Мониторинг', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (126, 'save', 3, 'Save', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (127, 'cancel', 3, 'Cancel', 'Отменить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (128, 'edit', 3, 'Edit', 'Редактировать', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (129, 'edit', 33, 'Edit', 'Редактировать', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (130, 'edit_error', 5, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (131, 'edit_success', 5, 'The content successfully edited!', 'Контент успешно отредактирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (132, 'nick', 35, 'Nickname', 'Никнейм', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (133, 'icq', 35, 'ICQ', 'ICQ', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (134, 'group', 35, 'Group', 'Группа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (135, 'not_groups_in_cfg', 34, 'Administrators group is not listed!', 'Группы администраторов не указаны!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (136, 'empty_table', 34, 'Data table is empty!', 'Таблица с данными пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (137, 'head_general_options', 36, 'General Options', 'Основные настройки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (138, 'basic_settings', 36, 'Basic', 'Базовые', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (139, 'cloud_servers', 36, 'Cloud servers', 'Облако серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (140, 'empty_table', 36, 'Data table is empty!', 'Таблица с данными пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (141, 'access_denied', 0, 'Access denied!', 'Доступ запрещен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (142, 'apply', 36, 'Apply', 'Применить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (143, 'language', 36, 'Default language', 'Язык по-умолчанию', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (144, 'pagesize', 36, 'Page size', 'Результатов на страницу', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (145, 'charset_content', 36, 'Content charset', 'Кодировка контента', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (146, 'template', 36, 'Template', 'Шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (147, 'site_offline', 36, 'Site offline?', 'Сайт закрыт?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (148, 'site_offline_text', 36, 'Message at the closed site', 'Сообщение при закрытом сайте', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (149, 'site_name', 36, 'Site name', 'Название сайта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (150, 'site_description', 36, 'Short description the site', 'Краткое описание сайта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (151, 'file_types', 36, 'Types of image file', 'Типы файла картинки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (152, 'file_width', 36, 'Image width for maps', 'Ширина картинки для карт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (153, 'file_height', 36, 'Image height for maps', 'Высота картинки для карт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (154, 'file_size', 36, 'File size image', 'Размер файла картинки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (155, 'yes', 36, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (156, 'no', 36, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (157, 'cloud_cache', 36, 'Cache?', 'Кешировать?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (158, 'cloud_limit', 36, 'Limit', 'Лимит', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (159, 'cloud_erase', 36, 'Phrase for removal', 'Фраза для удаления', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (160, 'cloud_speed', 36, 'Speed', 'Скорость', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (161, 'cloud_width', 36, 'Width of the block', 'Ширина блока', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (162, 'cloud_height', 36, 'Height of the block', 'Высота блока', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (163, 'admin_groups', 36, 'Administrators groups', 'Группы администраторов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (164, 'help', 36, 'Help', 'Информация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (165, 'help_lang', 36, 'Select Interface Language by default.', 'Выбор языка интерфейса по-умолчанию.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (166, 'help_pagesize', 36, 'What is the maximum number of entries will be displayed on each page of results.', 'Какое максимальное количество элементов будет выводиться на каждой странице результатов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (167, 'help_charset', 36, 'Choosing a charset site content.', 'Выбор кодировки контента сайта.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (168, 'help_tpl', 36, 'The choice of site template by default.', 'Выбор шаблона сайта по-умолчанию.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (169, 'help_site_off', 36, 'Enabling / Disabling the site. When off site, only users with administrator privileges can view it.', 'Включение/Отключение сайта. При отключенном сайте только пользователи с правами администратора смогут его просматривать.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (170, 'help_site_off_txt', 36, 'Text messages that users will see when the closed site.', 'Текст сообщения, которые будут видеть пользователи при закрытом сайте.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (171, 'help_site_name', 36, 'Short name of the site, which will be displayed in the header.', 'Краткое название сайта, которое будет отображаться в заголовке страниц.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (172, 'help_site_description', 36, 'A brief description of your site.', 'Краткое описание Вашего сайта.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (173, 'help_file_types', 36, 'Image file types are allowed to upload.', 'Типы файлов изображений, которые допустимы к загрузке на сайт.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (174, 'help_file_width', 36, 'Prior to this width will be corrected image for maps.', 'До этой ширины будет откорректировано изображение для карт.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (175, 'help_file_height', 36, 'Up to this height will correct the image for maps.', 'До этой высоты будет откорректировано изображение для карт.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (176, 'help_file_size', 36, 'Maximum allowable size of the images.', 'Максимально допустимый размер изображений.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (177, 'help_admin_groups', 36, 'Groups of users that will be displayed on the page with the list admins.', 'Группы пользователей, которые будут отображаться на странице со списком админов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (178, 'help_cloud_cache', 36, 'Enable Caching cloud servers will optimize the page and expedite the work site.', 'Включение кеширования облака серверов позволит оптимизировать страницу и ускорить работу сайта.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (179, 'help_cloud_limit', 36, 'Maximum number of servers that will appear in the cloud.', 'Максимальное количество серверов, которое будет отображаться в облаке.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (180, 'help_cloud_erase', 36, 'This phrase is deleted from the name servers in the cloud. Leave this field blank if you do not want to cut the name servers.', 'Эта фраза будет удалена из названия серверов в облаке. Оставьте поле пустым, если не хотите сокращать название серверов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (181, 'help_cloud_speed', 36, 'The speed of rotation of the cloud.', 'Скорость вращения облака.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (182, 'help_cloud_width', 36, 'Width of the block, which will display the cloud.', 'Ширина блока, в котором будет отображаться облако.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (183, 'help_cloud_height', 36, 'The height of the block in which to display the cloud.', 'Высота блока, в котором будет отображаться облако.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (184, 'empty_array', 36, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (185, 'edit_success', 36, 'Config successfully updated.', 'Конфиг успешно обновлен.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (186, 'edit_error', 36, 'An error occurred while upgrading the database:', 'Произошли ошибки при обновлении базы данных:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (187, 'phrases', 0, 'Control Phrases', 'Управление Фразами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (188, 'dont_empty', 37, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (189, 'not_selected', 38, 'Entries not selected!', 'Записи не выбраны!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (190, 'head', 37, 'Control Language', 'Управление Языками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (191, 'del_selected', 38, 'Remove selected', 'Удалить записи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (192, 'add_lang', 37, 'Add language', 'Добавить язык', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (193, 'lang_title', 38, 'Language', 'Язык', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (194, 'lang_code', 38, 'Code', 'Символьный код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (195, 'phrase', 39, 'Phrase', 'Фраза', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (196, 'phrase_word', 39, 'Code', 'Код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (197, 'phrase_tpl', 39, 'Template', 'Шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (198, 'del_selected', 39, 'Remove selected', 'Удалить записи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (199, 'not_selected', 39, 'Entries not selected!', 'Записи не выбраны!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (200, 'add_phrase', 40, '<font size=\"5\">+</font>', '<font size=\"5\">+</font>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (201, 'head_general_phrases', 40, 'Control Phrases', 'Управление Фразами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (202, 'add_lang', 41, 'Add language', 'Добавление языка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (203, 'lang_title', 41, 'Language name', 'Название языка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (204, 'lang_code', 41, 'Code', 'Символьный код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (205, 'add', 41, 'Add', 'Добавить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (206, 'edit_lang', 42, 'Editing language', 'Редактирование языка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (207, 'lang_title', 42, 'Language name', 'Название языка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (208, 'lang_code', 42, 'Code', 'Символьный код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (209, 'apply', 42, 'Apply', 'Применить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (210, 'confirm_del', 38, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (211, 'confirm_del', 39, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (212, 'global_phrases', 40, 'Global phrases', 'Глобальные фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (213, 'head_general_phrase_edit', 43, 'Editing phrase', 'Редактирование фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (214, 'code', 43, 'Code', 'Символьный код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (215, 'template', 43, 'Template', 'Шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (216, 'translate', 43, 'Translate', 'Перевод', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (217, 'save', 43, 'Save', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (218, 'id_incorrect', 43, 'The phrase is not found', 'Указанная фраза не найдена', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (219, 'general_edit_phrase', 0, 'Editing Phrase', 'Редактирование Фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (220, 'dont_empty', 43, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (221, 'edit_success', 40, 'The phrase successfully edited!', 'Фраза успешно отредактирована!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (222, 'edit_failed', 40, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (223, 'del_failed', 40, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (224, 'del_multiply_success', 40, 'Successfully removed the phrases:', 'Успешно удалено фраз:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (225, 'del_success', 40, 'Successfully removed the phrase:', 'Успешно удалена фраза:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (226, 'add_success', 40, 'Successfully added!', 'Фраза добавлена!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (227, 'add_failed', 40, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (228, 'lang_active', 38, 'Available for selection', 'Доступен для выбора', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (229, 'yes', 38, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (230, 'no', 38, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (231, 'back_url', 43, 'Back to the list of phrases', 'Вернуться к списку фраз', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (232, 'add_phrase', 44, 'Add phrase', 'Добавить фразу', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (233, 'code', 44, 'Code', 'Символьный код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (234, 'template', 44, 'Template', 'Шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (235, 'translate', 44, 'Translate', 'Перевод', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (236, 'add', 44, 'Add', 'Добавить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (237, 'add_success', 44, 'The phrase successfully added!', 'Фраза успешно добавлена!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (238, 'dont_empty', 40, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (239, 'add_failed', 44, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (240, 'lang_active', 42, 'Users can choose the language?', 'Пользователи могут выбирать язык?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (241, 'yes', 42, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (242, 'no', 42, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (243, 'edit_success', 37, 'Language successfully updated.', 'Язык успешно обновлен.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (244, 'edit_failed', 37, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (245, 'edit_success', 42, 'Language successfully updated.', 'Язык успешно обновлен.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (246, 'lang_active', 41, 'Users can choose the language?', 'Пользователи могут выбирать язык?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (247, 'yes', 41, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (248, 'no', 41, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (249, 'add_success', 41, 'The language successfully added!', 'Язык успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (250, 'del_multiply_success', 37, 'Successfully removed the languages:', 'Успешно удалено языков:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (251, 'del_failed', 37, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (252, 'add_success', 37, 'The language successfully added!', 'Язык успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (253, 'add_failed', 37, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (254, 'del_success', 37, 'The language successfully removed!', 'Язык успешно удален!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (255, 'add_try', 40, 'This phrase already exists in this template!', 'Данная фраза уже существует в указанном шаблоне!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (256, 'add_try', 37, 'This code already exists in the database!', 'Данный символьный код уже существует в базе!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (257, 'head_general_category', 48, 'Section management', 'Управление разделами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (258, 'add_cat', 48, 'Add section', 'Добавить раздел', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (259, 'name', 45, 'Title', 'Заголовок', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (260, 'order', 45, 'Sort', 'Сортировка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (261, 'save_order', 45, 'Save sort', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (262, 'confirm_del', 16, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (263, 'empty_table', 15, 'Data table is empty!', 'Таблица с данными пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (264, 'del_selected', 16, 'Remove selected', 'Удалить записи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (265, 'user_activity', 16, 'Activity', 'Активность', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (266, 'user_reg', 16, 'Registered', 'Зарегистрирован', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (267, 'head', 15, 'List of users', 'Список пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (268, 'all_groups', 15, 'All groups', 'Все группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (269, 'control_servers_add', 0, 'Add Server', 'Добавить Сервер', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (270, 'user_email', 16, 'E-mail', 'E-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (271, 'add_user', 15, 'Add user', 'Добавить пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (272, 'help_game_accounts', 61, 'Game accounts', 'Игровые аккаунты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (273, 'group_name', 62, 'Usergroup name', 'Название группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (274, 'help_main', 62, 'General permissions', 'Основные разрешения', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (275, 'help_read_category', 62, 'Access to view the categories', 'Доступ для просмотра категорий', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (276, 'help_admin_access', 62, 'Is the site administrator', 'Является администратором сайта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (277, 'help_edit_pages', 62, 'Access for editing static pages', 'Доступ для редактирования статичных страниц', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (278, 'general_add_lang', 0, 'Add Language', 'Добавить Язык', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (279, 'general_edit_lang', 0, 'Edit Language', 'Редактировать Язык', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (280, 'yes', 62, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (281, 'no', 62, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (282, 'general_add_phrase', 0, 'Add Phrase', 'Добавить фразу', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (283, 'add_group', 62, 'Add group', 'Добавление группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (284, 'back_url', 62, 'Back to the list of groups', 'Вернуться к списку групп', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (285, 'category_add', 0, 'Add Section', 'Добавить Раздел', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (286, 'user_name', 16, 'Username', 'Имя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (287, 'add_category', 46, 'Add section', 'Добавление раздела', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (288, 'title', 46, 'Title', 'Заголовок', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (289, 'parent', 46, 'Parent section', 'Родительский раздел', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (290, 'link', 46, 'The name of the script file', 'Имя файла скрипта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (291, 'order', 46, 'Sort', 'Сортировка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (292, 'show_blocks', 46, 'Display blocks', 'Отображать блоки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (293, 'product', 46, 'Product ID', 'ID продукта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (294, 'add', 46, 'Add', 'Добавить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (295, 'not_parent', 46, '- no -', '- нет -', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (296, 'cloud_head', 0, 'Cloud servers', 'Облако серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (297, 'steam_head', 0, 'Converter steam', 'Конвертер steam', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (298, 'category_edit', 0, 'Edit Section', 'Редактировать Раздел', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (299, 'confirm_del', 45, 'Attention! This will delete all subsection. Are you sure you want to delete?', 'Внимание! Будут удалены все вложенные разделы. Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (300, 'edit_category', 49, 'Edit section', 'Редактирование раздела', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (301, 'title', 49, 'Title', 'Заголовок', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (302, 'parent', 49, 'Parent section', 'Родительский раздел', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (303, 'not_parent', 49, '- no -', '- нет -', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (304, 'link', 49, 'The name of the script file', 'Имя файла скрипта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (305, 'order', 49, 'Sort', 'Сортировка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (306, 'show_blocks', 49, 'Display blocks', 'Отображать блоки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (307, 'product', 49, 'Product ID', 'ID продукта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (308, 'apply', 49, 'Apply', 'Применить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (309, 'error_select_list', 48, 'Sections missing!', 'Разделы отсутствуют!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (310, 'error_select_section', 48, 'Categories missing!', 'Категории отсутствуют!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (311, 'dont_empty', 48, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (312, 'add_success', 48, 'Category added!', 'Категория добавлена!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (313, 'add_failed', 48, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (314, 'del_success', 48, 'The category successfully removed!', 'Категория успешно удалена!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (315, 'install_file_not_found', 50, 'The installer of the product is missing.', 'Установщик продукта отсутствует.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (316, 'del_failed', 48, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (317, 'resort_error', 48, 'Error while resorting section!', 'Ошибка при пересортировки секции!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (318, 'parent_error', 48, 'The selected section will not allow!', 'Выбранный раздел не допустим!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (319, 'edit_success', 48, 'Section edited successfully!', 'Раздел успешно отредактирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (320, 'resort_success', 48, 'Re-ordering was a success!', 'Пересортировка произведена удачно!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (321, 'head_products', 50, 'Products', 'Продукты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (322, 'name', 51, 'Name', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (323, 'version', 51, 'Version', 'Версия', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (324, 'description', 51, 'Description', 'Описание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (325, 'langs_and_phrases', 0, 'Languages and Phrases', 'Языки и Фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (326, 'add_product', 0, 'Add Product', 'Добавить Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (327, 'users_edit', 0, 'Edit User', 'Редактирование пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (328, 'add_cat', 50, 'Add product', 'Добавить продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (329, 'upload_xml', 52, 'Upload XML', 'Загрузить XML', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (330, 'users_add', 0, 'Add User', 'Добавление Пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (331, 'add_success', 46, 'Category added successfully!', 'Категория успешно добавлена!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (332, 'offline_warning', 3, '<b>Warning:</b> the site is currently closed to the users!', '<b>Предупреждение:</b> сайт в данный момент закрыт для пользователей!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (333, 'sql_debug', 36, 'Enable debug database queries?', 'Включить дебаг запросов к базе данных?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (334, 'help_sql_debug', 36, 'Enable / Disable debug database queries. The text of each query will be displayed in the debugger for usergroups with \"admin_access\".', 'Включение/Отключение дебага запросов к базе данных. Текст каждого запроса будет отображаться в отладчике для групп пользователей с правами \"admin_access\".', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (335, 'refresh', 3, 'Refresh', 'Обновить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (336, 'to_refresh', 3, 'seconds to refresh', 'секунд до обновления', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (337, 'refreshing', 3, 'Refreshing', 'Обновление', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (338, 'help_home_refresh_time', 36, 'After this many seconds will be automatically updated the server list on the main page. Set to \"0\" to disable automatic updates.', 'Через это количество секунд будет производиться автоматическое обновление списка серверов на главной странице. Установите \"0\", чтобы отключить автоматическое обновление.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (339, 'home_refresh_time', 36, 'Time auto-update server list on the main page', 'Время автоматического обновления списка серверов на главной странице', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (340, 'refreshing', 5, 'Refreshing', 'Обновление', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (341, 'add_result', 52, 'The result of adding', 'Результат добавления', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (342, 'tools', 0, 'Tools', 'Инструменты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (343, 'help_ga_perm_players', 62, 'Manage game account', 'Управление игровыми аккаунтами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (344, 'help_user_action_log', 36, 'Enable / Disable logging of user actions.', 'Включение/Отключение логирования действий пользователей.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (345, 'user_action_log', 36, 'Logging of user actions', 'Логирование действий пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (346, 'log_login_check', 36, 'Login and logout users', 'Вход и выход пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (347, 'log_login_error', 36, 'Login failed', 'Ошибки входа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (348, 'log_change_lang', 36, 'Change language', 'Смена языка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (349, 'log_action_edit', 36, 'Editing data', 'Редактирование данных', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (350, 'log_steam_convert', 36, 'Use steam-converter', 'Использование steam-конвертера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (351, 'head', 53, 'Logs panel', 'Логи панели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (352, 'action', 53, 'Action', 'Действие', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (353, 'user_login', 53, 'User name', 'Имя пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (354, 'user_ip', 53, 'User IP', 'IP пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (355, 'all_actions', 53, 'All actions', 'Все действия', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (356, 'search', 53, 'Search', 'Найти', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (357, 'delete', 53, 'Delete', 'Удалить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (358, 'tbl_empty', 53, 'Table logs is empty!', 'Таблица с логами пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (359, 'head', 54, 'Logs panel', 'Логи панели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (360, 'new_search', 54, 'New search', 'Новый поиск', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (361, 'time', 55, 'Time', 'Время', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (362, 'user_login', 55, 'User name', 'Имя пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (363, 'user_ip', 55, 'User IP', 'IP пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (364, 'action', 55, 'Action', 'Действие', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (365, 'info', 55, 'Info', 'Инфо', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (366, 'del_success', 53, 'Successfully deleted logs:', 'Успешно удалено логов:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (367, 'del_null', 53, 'At your request, the logs are not found. Try to specify other search options.', 'По вашему запросу логи не найдены. Попробуйте указать другие настройки поиска.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (368, 'del_error', 53, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (369, 'head_optimization', 56, 'Testing / Optimization', 'Тестирование / Оптимизация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (370, 'name', 56, 'Title', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (371, 'check_category_tree', 0, 'Check the category tree', 'Проверка дерева категорий', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (372, 'apply', 62, 'Apply', 'Применить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (373, 'run_script', 56, 'Run script', 'Запустить скрипт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (374, 'check_tree_result', 57, 'The result of checking the category tree', 'Результат проверки дерева категорий', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (375, 'catright_more_catleft', 57, 'left key is ALWAYS smaller than the right', 'левый ключ ВСЕГДА меньше правого', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (376, 'not_found', 57, 'No errors', 'Ошибки отсутствуют', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (377, 'rule', 57, 'RULE', 'ПРАВИЛО', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (378, 'min_catleft', 57, 'the lowest left key is ALWAYS 1', 'наименьший левый ключ ВСЕГДА равен 1', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (379, 'max_catright', 57, 'maximal right key is ALWAYS equal to twice the number of nodes', 'наибольший правый ключ ВСЕГДА равен двойному числу узлов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (380, 'between_key', 57, 'the difference between right and left key ALWAYS an odd number', 'разница между правым и левым ключом ВСЕГДА нечетное число', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (381, 'even_odd', 57, 'if the level of the section odd number then the left key is ALWAYS an even number, and vice versa', 'если уровень узла нечетное число то тогда левый ключ ВСЕГДА четное число и наоборот', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (382, 'uniq_keys', 57, 'the key is always unique, regardless of whether it is right or left', 'ключи ВСЕГДА уникальны, вне зависимости от того правый он или левый', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (383, 'change_active_success', 50, 'Status is changed successfully', 'Статус активности успешно изменен', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (384, 'php_settings', 0, 'PHP Settings', 'Настройки PHP', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (385, 'users', 60, 'Users', 'Пользователи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (386, 'confirm_del', 51, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (387, 'head', 59, 'Group setting', 'Настройка групп', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (388, 'group', 60, 'Group', 'Группа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (389, 'del_selected', 60, 'Remove selected', 'Удалить записи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (390, 'not_selected', 60, 'Entries not selected!', 'Записи не выбраны!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (391, 'confirm_del', 60, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (392, 'usergroup_add', 0, 'Add Group', 'Добавление Группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (393, 'add_group', 59, 'Add group', 'Добавить группу', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (394, 'edit_group', 61, 'Edit group', 'Редактирование группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (395, 'back_url', 61, 'Back to the list of groups', 'Вернуться к списку групп', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (396, 'group_name', 61, 'Usergroup name', 'Название группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (397, 'help_main', 61, 'General permissions', 'Основные разрешения', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (398, 'help_read_category', 61, 'Access to view the categories', 'Доступ для просмотра категорий', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (399, 'help_admin_access', 61, 'Is the site administrator', 'Является администратором сайта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (400, 'yes', 61, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (401, 'no', 61, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (402, 'help_edit_pages', 61, 'Access for editing static pages', 'Доступ для редактирования статичных страниц', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (403, 'apply', 61, 'Apply', 'Применить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (404, 'help_game_accounts', 62, 'Game accounts', 'Игровые аккаунты', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (405, 'help_ga_perm_players', 61, 'Manage game account', 'Управление игровыми аккаунтами', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (406, 'not_group', 61, 'Group not found', 'Группа не найдена', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (407, 'edit_group', 3, 'Edit Group', 'Редактирование Группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (408, 'edit_error', 59, 'Error when editing the user group:', 'Ошибки при редактировании группы:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (409, 'edit_success', 59, 'The usergroup successfully edited!', 'Группа успешно отредактирована!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (410, 'empty_array', 59, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (411, 'dont_empty', 61, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (412, 'global_phrases', 43, 'Global phrases', 'Глобальные фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (413, 'global_phrases', 44, 'Global phrases', 'Глобальные фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (414, 'english', 36, 'English', 'English', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (415, 'russian', 36, 'Russian', 'Русский', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (416, 'timezone', 36, 'Default time zone offset', 'Часовой пояс по умолчанию', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (417, 'help_timezone', 36, 'Default time zone offset for guests. Time zone offset are for the users configured in their profile.', 'Часовой пояс по умолчанию для гостей. Часовой пояс для пользователей настраивается в профиле.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (418, 'timezone_gmt_minus_1200', 0, '(GMT -12:00) Eniwetok, Kwajalein', '(GMT -12:00) Эневеток, Кваджалейн', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (419, 'timezone_gmt_minus_1100', 0, '(GMT -11:00) Midway Island, Samoa', '(UTC-11:00) Американское Самоа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (420, 'timezone_gmt_minus_1000', 0, '(GMT -10:00) Hawaii', '(UTC-10:00) Гавайи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (421, 'timezone_gmt_minus_0900', 0, '(GMT -9:00) Alaska', '(UTC-09:00) Аляска', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (422, 'timezone_gmt_minus_0800', 0, '(GMT -8:00) Pacific Time (US &amp; Canada)', '(UTC-08:00) Тихоокеанское время (США и Канада)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (423, 'timezone_gmt_minus_0700', 0, '(GMT -7:00) Mountain Time (US &amp; Canada)', '(UTC-07:00) Горное время (США и Канада)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (424, 'timezone_gmt_minus_0600', 0, '(GMT -6:00) Central Time (US &amp; Canada), Mexico City', '(UTC-06:00) Центральное время (США и Канада)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (425, 'timezone_gmt_minus_0500', 0, '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima', '(UTC-05:00) Восточное время (США и Канада)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (426, 'timezone_gmt_minus_0430', 0, '(GMT -4:30) Caracas', '(UTC-04:30) Каракас', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (427, 'timezone_gmt_minus_0400', 0, '(GMT -4:00) Atlantic Time (Canada), La Paz, Santiago', '(UTC-04:00) Атлантическое время (Канада)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (428, 'timezone_gmt_minus_0330', 0, '(GMT -3:30) Newfoundland', '(UTC-03:30) Ньюфаундленд', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (429, 'timezone_gmt_minus_0300', 0, '(GMT -3:00) Brazil, Buenos Aires, Georgetown', '(UTC-03:00) Буэнос-Айрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (430, 'timezone_gmt_minus_0200', 0, '(GMT -2:00) Mid-Atlantic', '(UTC-02:00) Среднеатлантическое время', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (431, 'timezone_gmt_minus_0100', 0, '(GMT -1:00) Azores, Cape Verde Islands', '(UTC-01:00) Кабо-Верде', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (432, 'timezone_gmt_plus_0000', 0, '(GMT) Western Europe Time, London, Lisbon, Casablanca', '(UTC) Дублин, Эдинбург, Лиссабон, Лондон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (433, 'timezone_gmt_plus_0100', 0, '(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris', '(UTC+01:00) Центральноевропейское время', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (434, 'timezone_gmt_plus_0200', 0, '(GMT +2:00) Kiev, South Africa, Cairo', '(UTC+02:00) Восточноевропейское время', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (435, 'timezone_gmt_plus_0300', 0, '(GMT +3:00) Kaliningrad, Minsk, Baghdad', '(UTC+03:00) Найроби, Багдад, Кувейт, Катар, Эр-Рияд', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (436, 'timezone_gmt_plus_0330', 0, '(GMT +3:30) Tehran', '(UTC+03:30) Тегеран', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (437, 'timezone_gmt_plus_0400', 0, '(GMT +4:00) Moscow, St. Petersburg, Abu Dhabi, Baku, Tbilisi', '(UTC+04:00) Москва, Санкт-Петербург, Волгоград', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (438, 'timezone_gmt_plus_0430', 0, '(GMT +4:30) Kabul', '(UTC+04:30) Кабул', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (439, 'timezone_gmt_plus_0500', 0, '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent', '(UTC+05:00) Ташкент, Карачи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (440, 'timezone_gmt_plus_0530', 0, '(GMT +5:30) Mumbai, Kolkata, Chennai, New Delhi', '(UTC+05:30) Ченнай, Калькутта, Мумбаи, Нью-Дели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (441, 'timezone_gmt_plus_0545', 0, '(GMT +5:45) Kathmandu', '(UTC+05:45) Катманду', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (442, 'timezone_gmt_plus_0600', 0, '(GMT +6:00) Almaty, Dhaka, Colombo', '(UTC+06:00) Екатеринбург, Челябинск', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (443, 'timezone_gmt_plus_0630', 0, '(GMT +6:30) Yangon, Cocos Islands', '(UTC+06:30) Янгон (Рангун)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (444, 'timezone_gmt_plus_0700', 0, '(GMT +7:00) Bangkok, Hanoi, Jakarta', '(UTC+07:00) Бангкок, Ханой, Джакарта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (445, 'timezone_gmt_plus_0800', 0, '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong', '(UTC+08:00) Пекин, Чунцин, Гонконг, Урумчи', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (446, 'timezone_gmt_plus_0900', 0, '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk', '(UTC+09:00) Иркутск', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (447, 'timezone_gmt_plus_0930', 0, '(GMT +9:30) Adelaide, Darwin', '(UTC+09:30) Аделаида', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (448, 'timezone_gmt_plus_1000', 0, '(GMT +10:00) Eastern Australia, Guam, Vladivostok', '(UTC+10:00) Брисбен, Гуам', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (449, 'timezone_gmt_plus_1100', 0, '(GMT +11:00) Magadan, Solomon Islands, New Caledonia', '(UTC+11:00) Соломоновы острова, Новая Каледония', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (450, 'timezone_gmt_plus_1200', 0, '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka', '(UTC+12:00) Анадырь, Камчатка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (451, 'date_format', 36, 'Date format', 'Формат даты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (452, 'help_date_format', 36, 'The date format on the site.', 'Формат даты на страницах сайта.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (453, 'edit_user', 17, 'Edit user', 'Редактирование пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (454, 'edit_user', 3, 'Edit User', 'Редактирование Пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (455, 'not_user', 17, 'User not found', 'Пользователь не найден', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (456, 'back_url', 17, 'Back to the list of users', 'Вернуться к списку пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (457, 'username', 17, 'Username', 'Имя пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (458, 'password', 17, 'Password', 'Пароль', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (459, 'usergroup', 17, 'Group', 'Группа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (460, 'email', 17, 'Email', 'Email', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (461, 'icq', 17, 'ICQ', 'ICQ', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (462, 'save', 17, 'Save', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (463, 'timezone', 17, 'Timezone', 'Часовой пояс', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (464, 'dont_empty', 17, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (465, 'reg_date', 17, 'Registration date', 'Дата регистрации', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (466, 'last_visit', 17, 'Date of last visit', 'Дата последнего визита', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (467, 'add_failed', 15, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (468, 'add_success', 15, 'The user successfully added!', 'Пользователь успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (469, 'empty_array', 15, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (470, 'del_success', 15, 'The user successfully removed!', 'Пользователь успешно удален!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (471, 'del_failed', 15, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (472, 'del_multiply_success', 15, 'Successfully removed the users:', 'Успешно удалены пользователи:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (473, 'edit_error', 15, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (474, 'edit_success', 15, 'The user successfully edited!', 'Пользователь успешно отредактирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (475, 'email_not_valid', 15, 'Email is not valid', 'Email указан неверно', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (476, 'ip_not_valid', 15, 'IP address is not valid', 'IP адрес указан неверно', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (477, 'reg_ip', 17, 'IP address', 'IP адрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (478, 'values_error', 15, 'Errors in editing', 'Ошибки при редактировании', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (479, 'add_user', 18, 'Add user', 'Добавление пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (480, 'back_url', 18, 'Back to the list of users', 'Вернуться к списку пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (481, 'username', 18, 'Username', 'Имя пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (482, 'password', 18, 'Password', 'Пароль', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (483, 'usergroup', 18, 'Group', 'Группа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (484, 'timezone', 18, 'Timezone', 'Часовой пояс', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (485, 'reg_date', 18, 'Registration date', 'Дата регистрации', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (486, 'last_visit', 18, 'Date of last visit', 'Дата последнего визита', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (487, 'reg_ip', 18, 'IP address', 'IP адрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (488, 'email', 18, 'Email', 'Email', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (489, 'icq', 18, 'ICQ', 'ICQ', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (490, 'save', 18, 'Save', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (491, 'pass_not_empty', 15, 'Password must not be empty', 'Пароль не должен быть пустым', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (492, 'user_already_exist', 15, 'This user name already exists', 'Пользователь с таким именем уже существует', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (493, 'edit_success', 49, 'Section edited successfully!', 'Раздел успешно отредактирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (494, 'error_filetype', 50, 'Error adding product: invalid file type!', 'Ошибка при добавлении продукта: неверный тип файла!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (495, 'add_failed', 52, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (496, 'add_success', 52, 'The product successfully added!', 'Продукт успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (497, 'error_filesize', 50, 'Error adding product: file size greater than', 'Ошибка при добавлении продукта: размер файла больше', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (498, 'del_multiply_success', 59, 'Successfully removed the usergroups:', 'Успешно удалено групп:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (499, 'head', 19, 'Search users', 'Поиск пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (500, 'user_login', 19, 'Login', 'Логин', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (501, 'user_mail', 19, 'E-mail', 'E-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (502, 'user_icq', 19, 'ICQ', 'ICQ', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (503, 'user_group', 19, 'Group', 'Группа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (504, 'all_groups', 19, 'All groups', 'Все группы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (505, 'reg_date', 19, 'Registration Date', 'Дата регистрации', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (506, 'reg_ip', 19, 'Registration IP', 'IP при регистрации', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (507, 'last_visit', 19, 'Last visit', 'Последний визит', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (508, 'search', 19, 'Search', 'Найти', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (509, 'delete', 19, 'Delete', 'Удалить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (510, 'head', 20, 'Search result', 'Результаты поиска', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (511, 'user_name', 21, 'Username', 'Имя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (512, 'user_email', 21, 'E-mail', 'E-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (513, 'user_reg', 21, 'Registered', 'Зарегистрирован', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (514, 'user_activity', 21, 'Activity', 'Активность', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (515, 'del_error', 15, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (516, 'del_null', 15, 'At your request, the users are not found. Try to specify other search options.', 'По вашему запросу пользователи не найдены. Попробуйте указать другие настройки поиска.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (517, 'register_head', 31, 'Registration', 'Регистрация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (518, 'reg_only_soft', 31, 'Registration is available only through a special program! <a href=\"acpanel/files/rega.exe\">Download program now...</a>', 'Регистрация возможна только через специальную программу! <a href=\"acpanel/files/rega.exe\">Скачать программу...</a>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (519, 'register_title', 3, 'Registration', 'Регистрация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (520, 'register_info', 31, 'All fields are required.', 'Все поля обязательны для заполнения.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (521, 'register', 1, 'Registration', 'Регистрация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (522, 'login', 31, 'Authorization', 'Авторизация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (523, 'reg_type', 36, 'Registration type', 'Тип регистрации', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (524, 'help_reg_type', 36, '<ul>\r\n<li>1 - Registration is closed.</li>\r\n<li>2 - Register without the confirmation by e-mail.</li>\r\n<li>3 - Register with the mandatory confirmation by e-mail.</li>\r\n<li>4 - Register through a special program.</li>\r\n</ul>', '<ul>\r\n<li>1 - Регистрация закрыта.</li>\r\n<li>2 - Регистрация без подтверждения по e-mail.</li>\r\n<li>3 - Регистрация с обязательным подтверждением по e-mail.</li>\r\n<li>4 - Регистрация через специальную программу.</li>\r\n</ul>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (525, 'reg_closed', 36, '1 - closed', '1 - закрыта', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (526, 'reg_soft', 36, '4 - program', '4 - программа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (527, 'reg_site_email_activated_no', 36, '2 - without confirmation by e-mail', '2 - без подтверждения по e-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (528, 'reg_site_email_activated_yes', 36, '3 - confirmation by e-mail', '3 - с подтверждением по e-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (529, 'username_minlen', 36, 'The minimum length of username', 'Минимальная длина имени пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (530, 'help_username_minlen', 36, 'Specify the minimum length of user name, which will be checked at registration.', 'Укажите минимальную длину имени пользователя, которая будет проверяться при регистрации.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (531, 'username_maxlen', 36, 'The maximum length of username', 'Максимальная длина имени пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (532, 'help_username_maxlen', 36, 'Specify the maximum length of user name, which will be checked at registration.', 'Укажите максимальную длину имени пользователя, которая будет проверяться при регистрации.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (533, 'passwd_minlen', 36, 'The minimum length of password', 'Минимальная длина пароля пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (534, 'help_passwd_minlen', 36, 'The longer the password - the higher the security of your account user. For security purposes, it is recommended to set the minimum password length of at least \"6\".', 'Чем длиннее пароль, тем выше безопасность аккаунта пользователя. В целях безопасности рекомендуется установить минимальную длину пароля не менее \"6\".', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (535, 'captcha_lock', 3, 'Locked: the form can not be sent', 'Форма не может быть отправлена', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (536, 'captcha_unlock', 3, 'Unlocked: the form can be sent', 'Форма может быть отправлена', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (537, 'login_user', 31, 'Username', 'Имя пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (538, 'reg_submit', 31, 'Send', 'Отправить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (539, 'email_user', 31, 'E-mail', 'E-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (540, 'login_pass', 31, 'Password', 'Пароль', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (541, 'login_pass_check', 31, 'Password again', 'Пароль ещё раз', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (542, 'register_closed', 31, 'Registration is closed!', 'Регистрация закрыта!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (543, 'reg_type_not_set', 31, 'Registration type is not specified by the administrator!', 'Тип регистрации не указан администратором!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (544, 'uname_not_empty', 5, 'username unspecified', 'имя не указано', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (545, 'uname_must_by', 5, 'username length:', 'длина имени:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (546, 'uname_already_exists', 5, 'username busy', 'имя занято', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (547, 'email_incorrect', 5, 'e-mail not valid', 'e-mail не корректен', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (548, 'email_already_exists', 0, 'e-mail busy', 'e-mail занят', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (549, 'pass_not_check', 5, 'passwords do not match', 'пароли не совпадают', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (550, 'pass_must_by', 5, 'password less than', 'пароль меньше', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (551, 'reg_error_log', 5, 'Error log:', 'Ошибка регистрации:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (552, 'group_for_new_user', 36, 'A group for new users', 'Группа для новых пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (553, 'help_group_for_new_user', 36, 'Which group will get users after registration.', 'В какую группу будут попадать пользователи после регистрации.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (554, 'captcha_not_defined', 5, 'captcha is not selected', 'captcha не выбрана', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (555, 'hid', 17, 'HID', 'HID', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (556, 'add_success', 59, 'The group successfully added!', 'Группа успешно добавлена!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (557, 'login_link', 0, 'sign in', 'войти на сайт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (558, 'you_can', 6, 'You can:', 'Вы можете:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (559, 'profile_title', 3, 'My profile', 'Мой профиль', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (560, 'usergroup', 63, 'User group:', 'Группа:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (561, 'profile_head', 63, 'My profile', 'Мой профиль', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (562, 'reg_date', 63, 'Registration date:', 'Дата регистрации:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (563, 'edit_pass_email', 64, 'Edit password and e-mail', 'Редактировать пароль и e-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (564, 'icq', 63, 'ICQ:', 'ICQ:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (565, 'timezone', 63, 'Timezone:', 'Часовой пояс:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (566, 'upload_avatar', 63, 'Upload avatar', 'Загрузить аватар', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (567, 'current_pass', 64, 'Current password', 'Текущий пароль', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (568, 'new_pass', 64, 'New password (optional)', 'Новый пароль (не обязательно)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (569, 'new_pass_check', 64, 'New password again (optional)', 'Новый пароль ещё раз (не обязательно)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (570, 'email_user', 64, 'E-mail (optional)', 'E-mail (не обязательно)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (571, 'edit', 63, 'Settings', 'Настройки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (572, 'save', 63, 'Save', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (573, 'username', 63, 'Name:', 'Имя:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (574, 'pass_not_empty', 5, 'Current password must not be empty', 'Текущий пароль не должен быть пустым', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (575, 'image_options', 36, 'For images', 'Для изображений', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (576, 'avatar_width', 36, 'Image width for avatars', 'Ширина картинки для аватар', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (577, 'avatar_height', 36, 'Image height for avatars', 'Высота картинки для аватар', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (578, 'help_avatar_width', 36, 'Prior to this width will be corrected image for avatars.', 'До этой ширины будет откорректировано изображение для аватар.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (579, 'help_avatar_height', 36, 'Up to this height will correct the image for avatars.', 'До этой высоты будет откорректировано изображение для аватар.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (580, 'edit_pass_email', 63, 'Edit password and e-mail', 'Редактировать пароль и e-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (581, 'save', 64, 'Save', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (582, 'current_pass_not_check', 5, 'The password does not match the current', 'Введенный пароль не совпадает с текущим', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (583, 'profile_edit_success', 5, 'Settings updated successfully!', 'Настройки успешно сохранены!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (584, 'need_activated_registr', 63, 'To complete the registration click on the link sent to your e-mail.', 'Для завершения регистрации перейдите по ссылке высланной на Ваш e-mail.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (585, 'need_activated_email', 63, 'Require activation e-mail', 'Требуется активация e-mail', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (586, 'users_reg', 36, 'Authentication', 'Аутентификация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (587, 'default_email', 36, 'Default e-mail address', 'E-mail адрес по-умолчанию', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (588, 'help_default_email', 36, 'This e-mail address will be used in the \"Sender\" when sending messages to users, etc.', 'Этот адрес электронной почты будет использоваться в поле \"Отправитель\" при рассылке писем пользователям и т.п.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (589, 'sending_success', 5, 'Confirmation letter has been sent to you email.', 'Письмо с подтверждением успешно отправлено на указанный email.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (590, 'sending_fail', 5, 'Confirmation letter could be sent to your email.', 'Письмо с подтверждением на удалось отправить на Ваш email.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (591, 'edit_empty', 5, 'There is no information to change!', 'Нет информации для изменения!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (592, 'template_email_change_subject', 0, '{sitename} # E-mail confirmation', '{sitename} # E-mail подтверждение', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (593, 'template_email_change_body', 0, '<html>\r\n<body>\r\n<b>a114_mysql</b>,<br /><br />\r\nTo confirm the e-mail you want to follow the link below.<br /><br />\r\n{link}<br /><br />\r\nSincerely,<br />\r\n{sitename}\r\n</body>\r\n</html>', '<html>\r\n<body>\r\n<b>a114_mysql</b>,<br /><br />\r\nДля подтверждения изменения e-mail Вам требуется пройти по ссылке ниже.<br /><br />\r\n{link}<br /><br />\r\nС уважением,<br />\r\n{sitename}\r\n</body>\r\n</html>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (594, 'activation_success', 22, 'Activation was successful!', 'Активация произведена успешно!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (595, 'activation_error', 22, 'Error when activating! The code used is incorrect or the specified user does not exist, or the activation has already been made.', 'Ошибка при активации! Используемый код неверен, либо указанного пользователя не существует, либо активация уже произведена.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (596, 'email_activated_head', 22, 'E-mail activation', 'E-mail активация', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (597, 'template_registr_confirm_subject', 0, '{sitename} # Registration confirmation', '{sitename} # Подтверждение регистрации', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (598, 'template_registr_confirm_body', 0, '<html>\r\n<body>\r\n<b>{username}</b>,<br /><br />\r\nTo complete your account registration is required to pass on the link below.<br /><br />\r\n{link}<br /><br />\r\nSincerely,<br />\r\n{sitename}\r\n</body>\r\n</html>', '<html>\r\n<body>\r\n<b>{username}</b>,<br /><br />\r\nДля завершения регистрации Вашего аккаунта требуется пройти по ссылке ниже.<br /><br />\r\n{link}<br /><br />\r\nС уважением,<br />\r\n{sitename}\r\n</body>\r\n</html>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (599, 'values_error', 5, 'Errors in editing', 'Ошибки при редактировании', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (600, 'user_edit_error', 5, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (601, 'user_edit_success', 5, 'Your preferences have been successfully edited!', 'Ваши настройки успешно отредактированы!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (602, 'empty_array', 5, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (603, 'isq_not_valid', 0, 'ICQ number must contain only numbers.', 'номер ICQ должен содержать только цифры.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (604, 'avatar_thumb_width', 36, 'Thumbnail width for avatars', 'Ширина миниатюры для аватар', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (605, 'avatar_thumb_height', 36, 'Thumbnail height for avatars', 'Высота миниатюры для аватар', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (606, 'help_avatar_thumb_width', 36, 'Prior to this width will be adjusted thumbnail avatars.', 'До этой ширины будут откорректированы миниатюры аватар.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (607, 'help_avatar_thumb_height', 36, 'Up to this height will be adjusted thumbnail avatars.', 'До этой высоты будут откорректированы миниатюры аватар.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (608, 'general_setting', 63, 'Basic settings', 'Основные настройки', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (609, 'empty_table', 5, 'Data table is empty!', 'Таблица с данными пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (610, 'empty_table', 23, 'Data table is empty!', 'Таблица с данными пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (611, 'not_products', 50, 'Products are not installed!', 'Продукты не установлены!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (612, 'install_success', 50, 'Product succesfully added!', 'Продукт успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (613, 'del_success', 50, 'Product successfully removed!', 'Продукт успешно удален!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (614, 'yes', 26, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (615, 'no', 26, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (616, 'server_active', 26, 'Server active?', 'Сервер активен?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (617, 'rating', 26, 'Rating', 'Рейтинг', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (618, 'url', 46, 'External URL', 'Внешний URL', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (619, 'url', 49, 'External URL', 'Внешний URL', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (620, 'search_phrases', 0, 'Search Phrases', 'Поиск Фраз', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (621, 'head', 8, 'Search phrases', 'Поиск фраз', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (622, 'head', 9, 'Search results for phrases', 'Результаты поиска фраз', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (623, 'all_phrases', 8, 'All phrases', 'Все фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (624, 'general_phrases', 8, 'Global phrases', 'Глобальные фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (625, 'pattern', 8, 'Template', 'Шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (626, 'search_word', 8, 'Phrase', 'Фраза', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (627, 'search', 8, 'Search', 'Поиск', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (628, 'new_search', 9, 'New search', 'Новый поиск', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (629, 'phrase', 10, 'Phrase', 'Фраза', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (630, 'phrase_word', 10, 'Code', 'Код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (631, 'phrase_tpl', 10, 'Template', 'Шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (632, 'blocks', 0, 'Block Managment', 'Управление Блоками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (633, 'block_add', 0, 'Add Block', 'Добавить Блок', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (634, 'block_edit', 0, 'Edit Block', 'Редактировать Блок', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (635, 'head_general_blocks', 11, 'Blocks management', 'Управление блоками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (636, 'add_block', 11, 'Add block', 'Добавить блок', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (637, 'order', 12, 'Sort', 'Сортировка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (638, 'name', 12, 'Name', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (639, 'description', 12, 'Description', 'Описание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (640, 'save_order', 12, 'Save sort', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (641, 'add_success', 13, 'The block successfully added!', 'Блок успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (642, 'title', 13, 'Name', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (643, 'description', 13, 'Description', 'Описание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (644, 'link', 13, 'Link to script', 'Ссылка на обработчик', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (645, 'product', 13, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (646, 'order', 13, 'Sort', 'Сортировка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (647, 'add_block', 13, 'Add block', 'Добавление блока', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (648, 'add', 13, 'Add', 'Добавить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (649, 'apply', 14, 'Apply', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (650, 'edit_block', 14, 'Edit block', 'Редактирование блока', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (651, 'order', 14, 'Sort', 'Сортировка', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (652, 'product', 14, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (653, 'link', 14, 'Link to script', 'Ссылка на обработчик', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (654, 'description', 14, 'Description', 'Описание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (655, 'title', 14, 'Name', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (656, 'edit_success', 14, 'The block successfully edited!', 'Блок успешно отредактирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (657, 'dont_empty', 11, 'Field must not be empty!', 'Поле не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (658, 'add_try', 11, 'This block already exists in the database!', 'Этот блок уже существует в базе данных!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (659, 'add_failed', 11, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (660, 'add_success', 11, 'The block successfully added!', 'Блок успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (661, 'del_success', 11, 'The block successfully removed!', 'Блок успешно удален!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (662, 'del_failed', 11, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (663, 'edit_success', 11, 'The block successfully edited!', 'Блок успешно отредактирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (664, 'edit_error', 11, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (665, 'resort_success', 11, 'Re-ordering was a success!', 'Пересортировка произведена удачно!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (666, 'display_order_info', 13, '(set to \"0\" to hide blocks everywhere)', '(установите \"0\", чтобы скрыть блоки везде)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (667, 'display_order_info', 14, '(set to \"0\" to hide blocks everywhere)', '(установите \"0\", чтобы скрыть блоки везде)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (668, 'execute_code', 13, 'Execute code', 'Код выполнения', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (669, 'execute_code', 14, 'Execute code', 'Код выполнения', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (670, 'delete_avatar_error', 5, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (671, 'delete_avatar_success', 5, 'Avatar was successfully removed!', 'Аватар успешно удален!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (672, 'error_filesize', 63, 'Error adding avatar: file size greater than', 'Ошибка при добавлении аватара: размер файла больше', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (673, 'error_filetype', 63, 'Error adding avatar: invalid file type!', 'Ошибка при добавлении аватара: неверный тип файла!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (674, 'error_fileincorrect', 63, 'Error adding avatar: invalid image properties!', 'Ошибка при добавлении аватара: неверные свойства изображения!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (675, 'error_fileresize', 63, 'Error adding avatar: function for image processing does not exist!', 'Ошибка при добавлении аватара: функция для обработки изображений не существует!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (676, 'confirm_del_avatar', 63, 'You want to delete avatar?', 'Вы желаете удалить аватар?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (677, 'avatar_size_info', 63, 'Size of avatar:', 'Размер аватара:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (678, 'time_dd_one', 0, 'day', 'день', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (679, 'time_dd_several', 0, 'days', 'дня', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (680, 'time_dd_many', 0, 'days', 'дней', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (681, 'time_hh_one', 0, 'hour', 'час', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (682, 'time_hh_several', 0, 'hours', 'часа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (683, 'time_hh_many', 0, 'hours', 'часов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (684, 'time_mm_one', 0, 'minute', 'минута', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (685, 'time_mm_several', 0, 'minutes', 'минуты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (686, 'time_mm_many', 0, 'minutes', 'минут', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (687, 'time_ss_one', 0, 'second', 'секунда', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (688, 'time_ss_several', 0, 'seconds', 'секунды', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (689, 'time_ss_many', 0, 'seconds', 'секунд', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (690, 'time_dd_compact', 0, 'd', 'д', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (691, 'time_hh_compact', 0, 'h', 'ч', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (692, 'time_mm_compact', 0, 'm', 'м', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (693, 'time_ss_compact', 0, 's', 'с', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (694, 'acc_online_all', 63, 'Total online:', 'Всего онлайн:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (695, 'mon_hide_offline', 36, 'Hide offline servers in the list?', 'Скрывать оффлайн сервера в списке?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (696, 'help_mon_hide_offline', 36, 'If set \"Yes\", then the server that have not responded to the inquiry: whether the change map, or other reasons - will be hidden in the server list.', 'Если установлено \"Да\", то сервера, которые не ответили на запрос: будь то смена карты или другие причины - будут скрыты в списке серверов.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (697, 'mon_view_per_page', 36, 'The number of servers per page', 'Количество серверов на страницу', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (698, 'help_mon_view_per_page', 36, 'How many servers to display pagination.', 'Какое количество серверов отображать для постраничной навигации.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (699, 'cloud_cache_time', 36, 'The storage time of the cache', 'Время хранения кэша', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (700, 'help_cloud_cache_time', 36, 'After a specified number of seconds the cache will be reloaded.', 'После указанного количества секунд кэш будет перезагружен.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (701, 'mon_cache', 36, 'List of servers is cached?', 'Cписок серверов кешируется?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (702, 'help_mon_cache', 36, 'When the speed caching to accelerate, however, the accuracy of the displayed data on servers in the monitoring would be an infelicity.', 'При включенном кешировании скорость работы ускорится, однако, точность отображаемых данных по серверам в мониторинге будет иметь погрешность. ', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (703, 'mon_cache_time', 36, 'The storage time of the cache', 'Время хранения кэша', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (704, 'help_mon_cache_time', 36, 'The time in seconds that will be stored in the cache to update.', 'Время в секундах, которое будет хранится кэш до обновления.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (705, 'hostname', 5, 'Hostname', 'Название сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (706, 'address', 5, 'Address', 'Адрес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (707, 'map', 5, 'on map', 'на карте', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (708, 'online', 5, 'Now players', 'Сейчас игроков', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (709, 'connect', 5, 'Launch', 'Подключиться', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (710, 'players_info', 5, 'Players in the game', 'Информация по игрокам', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (711, 'srv_not_resp', 5, '<em>- server not responding -</em>', '<em>- сервер не отвечает -</em>', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (712, 'select_option', 36, 'Select an option', 'Выберите вариант', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (713, 'rating', 24, 'Rating', 'Рейтинг', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (714, 'change_status_success', 23, 'Server status successfully changed!', 'Статус сервера успешно изменен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (715, 'change_status_error', 23, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (716, 'click_change_status', 24, 'Click to change the status of server monitoring', 'Нажмите, чтобы изменить статус сервера в мониторинге', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (717, 'go_to_profile', 24, 'Go to profile', 'Перейти в профиль пользователя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (718, 'added_date', 26, 'Date added', 'Дата добавления', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (719, 'added_user', 26, 'Added by', 'Добавил пользователь', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (720, 'opt_rcon', 26, 'RCON', 'RCON', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (721, 'opt_motd', 26, 'MOTD', 'MOTD', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (722, 'meta_description', 46, 'Meta description of the page', 'Мета описание страницы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (723, 'meta_description', 49, 'Meta description of the page', 'Мета описание страницы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (724, 'you_can', 7, 'You can:', 'Вы можете:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (725, 'create_account', 17, 'Create an account', 'Создать аккаунт', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (726, 'user_account', 17, 'Go to user game account', 'Перейти в игровой аккаунт', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (727, 'acc_reg_date', 17, 'Date of creation:', 'Дата создания:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (728, 'acc_last_online', 17, 'Last visit:', 'Последний заход:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (729, 'donat', 0, 'Donat', 'Донат', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (730, 'server_description', 26, 'Short description', 'Краткое описание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (731, 'position_in_rating', 5, 'position in the rating', 'позиция в рейтинге', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (732, 'position_in_rating', 28, 'position in the rating', 'позиция в рейтинге', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (733, 'add_favorite', 5, 'Add to favorites', 'Добавить в избранное', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (734, 'add_favorite', 28, 'Add to favorites', 'Добавить в избранное', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (735, 'rating', 5, 'Rating:', 'Рейтинг:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (736, 'rating', 28, 'Rating:', 'Рейтинг:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (737, 'help_mon_favorites_limit', 62, 'The number of servers that can be added to favorites', 'Количество серверов, которое можно добавить в избранное', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (738, 'help_mon_favorites_limit', 61, 'The number of servers that can be added to favorites', 'Количество серверов, которое можно добавить в избранное', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (739, 'mon_servers_favorites', 36, 'Name of cookie to servers in favorites', 'Название cookie для избранных серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (740, 'help_monitoring', 61, 'Servers monitoring', 'Мониторинг серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (741, 'help_monitoring', 62, 'Servers monitoring', 'Мониторинг серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (742, 'add_favorite_error', 5, 'You can not add another server, because the maximum number of favorite: %d', 'Вы не можете добавить ещё один сервер, так как максимальное количество избранных: %d', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (743, 'add_favorite_success', 5, 'The server was successfully added to favorites!', 'Сервер успешно добавлен в избранные!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (744, 'del_favorite_success', 5, 'The server successfully removed from your favorites!', 'Сервер успешно удален из избранных!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (745, 'del_favorite_error', 5, 'Unknown error when deleting from the favorites. Notify the administrator about the problem!', 'Неизвестная ошибка при удалении из избранных. Сообщите администратору!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (746, 'del_success', 59, 'Usergroup has been successfully deleted!', 'Группа пользователей успешно удалена!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (747, 'add_failed', 59, 'An error occurred while adding the group:', 'Произошли ошибки при добавлении группы:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (748, 'del_failed', 59, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (749, 'clear_cache', 0, 'Clear File Cache', 'Очистка Файлового Кеша', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (750, 'clear_cache_success', 65, 'Cache files deleted successfully!', 'Файлы кеша успешно удалены!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (751, 'clear_cache_edit', 65, 'There are no files to delete. Cache folder is empty!', 'Нет файлов для удаления. Папка кеша пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (752, 'all_admins', 34, 'All admins', 'Все админы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (753, 'server_card', 0, 'Card Server', 'Карточка Сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (754, 'undefined_server', 0, 'Unknown server', 'Неизвестный сервер', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (755, 'server_address_not_empty', 23, 'The field with the address must not be empty!', 'Поле с адресом не должно быть пустым!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (756, 'select_gametype', 25, '- Select the type of game -', '- Выберите тип игры -', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (757, 'server_vip_time', 26, 'VIP-status expired', 'Время окончания VIP-статуса', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (758, 'vkid', 36, 'vK API ID', 'вКонтакте API ID', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (759, 'help_vkid', 36, 'API ID of your application in a social network vKontakte. This identifier is needed to accommodate the social networking buttons on the pages panel.', 'API ID вашего приложения в социальной сети вКонтакте. Этот идентификатор необходим для размещения кнопок социальной сети на страницах панели.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (760, 'home_title', 36, 'Title for the main panel page', 'Title для главной страницы панели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (761, 'help_home_title', 36, 'Title main page ACPanel.', 'Title главной страницы ACPanel.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (762, 'ga_steam_validate', 36, 'Template for checking SteamID', 'Шаблон для проверки SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (763, 'help_ga_steam_validate', 36, 'Specify a regular expression match which will be verified SteamID for creating and editing user game accounts.', 'Укажите регулярное выражение, соответствие которому будет проверяться SteamID при создании и редактировании игровых аккаунтов пользователями.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (764, 'user_hid', 19, 'HID', 'HID', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (765, 'search_servers', 5, 'Search servers', 'Поиск серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (766, 'server_rating', 5, 'Rating game servers', 'Рейтинг игровых серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (767, 'my_favorites', 5, 'My favorite server', 'Мои избранные сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (768, 'search', 5, 'Search', 'Найти', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (769, 'opt_mode', 26, 'Game mode', 'Режим игры', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (770, 'opt_city', 26, 'City', 'Город', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (771, 'all_servers', 5, 'All types of servers', 'Все типы серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (772, 'all_mods', 5, 'All modes', 'Все моды', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (773, 'all_city', 5, 'All cities', 'Все города', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (774, 'no_favorites', 0, 'Your favorites list is empty servers. To add a server to favorites, click the star next to its name in the list.', 'Ваш список избранных серверов пуст. Чтобы добавить сервер в избранное, нажмите на звездочку рядом с его названием в списке.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (775, 'empty_data_servers', 3, 'For a given filter settings, active server not found!', 'По заданным настройкам фильтра активных серверов не найдено!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (776, 'empty_data_servers', 5, 'For a given filter settings, active server not found!', 'По заданным настройкам фильтра активных серверов не найдено!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (777, 'empty_data_servers', 28, 'For a given filter settings, active server not found!', 'По заданным настройкам фильтра активных серверов не найдено!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (778, 'help_mon_view_lifetime', 36, 'Specify the time in hours, which will store information about a user visiting a page server. After a visit by the specified time will be counted again.', 'Укажите время в часах, которое будет хранить информацию о посещении страницы сервера пользователем. После указанного времени визит пользователя будет снова подсчитан.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (779, 'mon_view_lifetime', 36, 'Storage time information about visits to the page server', 'Время хранения информации о визитах на страницу сервера', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (780, '24_hours', 3, '24 hours', '24 часа', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (781, 'one_week', 3, '1 week', '1 неделя', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (782, 'one_year', 3, '1 year', '1 год', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (783, 'opt_site', 26, 'Web-site', 'Web-сайт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (784, 'mon_descr_length', 36, 'Length interesting description of the server', 'Длина интересного описания сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (785, 'help_mon_descr_length', 36, 'Specify the number of characters for the description field servers. Above this amount will be considered as an interesting description.', 'Укажите количество символов для поля описания серверов. При превышении этого количества описание будет считаться интересным.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (786, 'values_error', 23, 'Errors in editing', 'Ошибки при редактировании', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (787, 'user_not_found', 23, 'user not found', 'пользователь не найден', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (788, 'username_empty_field', 23, 'user name can not be empty', 'имя пользователя не должно быть пустым', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (789, 'mon_gametype', 0, 'Types of game servers', 'Типы игровых серверов', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (790, 'perm_read', 61, 'Read', 'Чтение', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (791, 'perm_write', 61, 'Write', 'Изменение', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (792, 'perm_add', 61, 'Create', 'Создание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (793, 'perm_delete', 61, 'Delete', 'Удаление', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (794, 'help_general_perm_options', 61, 'Manage general options', 'Управление основными настройками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (795, 'help_general_perm_options', 62, 'Manage general options', 'Управление основными настройками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (796, 'help_general_perm_categories', 61, 'Manage categories', 'Управление разделами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (797, 'help_general_perm_categories', 62, 'Manage categories', 'Управление разделами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (798, 'help_general_perm_blocks', 61, 'Manage blocks', 'Управление боковыми блоками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (799, 'help_general_perm_blocks', 62, 'Manage blocks', 'Управление боковыми блоками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (800, 'help_general_perm_products', 61, 'Manage products', 'Управление продуктами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (801, 'help_general_perm_products', 62, 'Manage products', 'Управление продуктами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (802, 'help_general_perm_testing', 61, 'Manage optimization', 'Управление оптимизацией', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (803, 'help_general_perm_testing', 62, 'Manage optimization', 'Управление оптимизацией', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (804, 'help_general_perm_users', 61, 'Manage users', 'Управление пользователями', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (805, 'help_general_perm_users', 62, 'Manage users', 'Управление пользователями', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (806, 'help_general_perm_usergroups', 61, 'Manage user groups', 'Управление группами пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (807, 'help_general_perm_langs', 61, 'Manage languages', 'Управление языками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (808, 'help_general_perm_langs', 62, 'Manage languages', 'Управление языками', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (809, 'help_general_perm_usergroups', 62, 'Manage user groups', 'Управление группами пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (810, 'help_general_perm_phrases', 61, 'Manage phrases', 'Управление фразами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (811, 'help_general_perm_phrases', 62, 'Manage phrases', 'Управление фразами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (812, 'help_perm_tools', 61, 'Tools', 'Инструменты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (813, 'help_perm_tools', 62, 'Tools', 'Инструменты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (814, 'help_servers_perm_control', 61, 'Manage servers', 'Управление серверами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (815, 'help_servers_perm_control', 62, 'Manage servers', 'Управление серверами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (816, 'error_perm_update', 59, 'could not update permissions %s', 'не удалось обновить права доступа %s', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (817, 'error_group_update', 59, 'could not update the table of user groups', 'не удалось обновить таблицу пользователей', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (818, 'error_perm_table_empty', 59, 'permissions table is empty', 'таблица прав пуста', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (819, 'perm_read', 62, 'Read', 'Чтение', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (820, 'perm_write', 62, 'Write', 'Изменение', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (821, 'perm_add', 62, 'Create', 'Создание', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (822, 'perm_delete', 62, 'Delete', 'Удаление', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (823, 'error_group_insert', 59, 'failed to add entry in table groups', 'не удалось добавить запись в таблицу групп', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (824, 'help_general_perm_logs', 61, 'Manage panel logs', 'Управление логами панели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (825, 'help_general_perm_logs', 62, 'Manage panel logs', 'Управление логами панели', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (826, 'servers_list', 0, 'List of Servers', 'Список Серверов', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (827, 'all_templates', 66, 'All products', 'Все продукты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (828, 'add_template', 66, 'Add template', 'Добавить шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (829, 'head', 66, 'Manage templates phrases', 'Управление шаблонами фраз', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (830, 'productid', 67, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (831, 'template_title', 67, 'Name', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (832, 'add', 67, 'Add', 'Добавить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (833, 'productid', 68, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (834, 'template_title', 68, 'Name', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (835, 'apply', 68, 'Apply', 'Сохранить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (836, 'productid', 69, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (837, 'template_title', 69, 'Name', 'Название', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (838, 'count_phrases', 69, 'Phrases', 'Фразы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (839, 'not_selected', 69, 'Entries not selected!', 'Записи не выбраны!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (840, 'del_selected', 69, 'Delete', 'Удалить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (841, 'confirm_del', 69, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (842, 'search_word_code', 8, 'Code', 'Код', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (843, 'all_products', 8, 'All products', 'Все продукты', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (844, 'productid', 8, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (845, 'productid', 43, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (846, 'productid', 44, 'Product', 'Продукт', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (847, 'edit_success_tpl', 37, 'Template successfully updated.', 'Шаблон успешно обновлен.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (848, 'del_multiply_success_tpl', 37, 'Successfully removed the templates:', 'Успешно удалено шаблонов:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (849, 'add_success_tpl', 37, 'The template successfully added!', 'Шаблон успешно добавлен!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (850, 'del_success_tpl', 37, 'The template successfully removed!', 'Шаблон успешно удален!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (851, 'add_try_tpl', 37, 'The specified name already exists in the database!', 'Указанное имя уже существует в базе!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (852, 'general_phrases_template_edit', 0, 'Edit template', 'Редактирование шаблона', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (853, 'general_phrases_template_add', 0, 'Add template', 'Добавить шаблон', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (854, 'general_phrases_template', 0, 'Manage Templates', 'Управление Шаблонами', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (855, 'empty_table', 40, 'Data table is empty!', 'Таблица с данными пуста!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (856, 'upload_xml', 41, 'Upload XML', 'Загрузить XML', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (857, 'error_filesize', 37, 'Error loading XML: file size is greater than', 'Ошибка при загрузке XML: размер файла больше', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (858, 'error_filetype', 37, 'Error adding language: invalid file type!', 'Ошибка при добавлении языка: неверный тип файла!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (859, 'upload_success', 37, 'Language has been successfully imported!', 'Язык успешно импортирован!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (860, 'template_password_recovery_subject', 0, '{sitename} # Request the password recovery', '{sitename} # Запрос восстановления пароля', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (861, 'template_password_recovery_body', 0, '<html>\r\n<body>\r\n<b>{username}</b>,<br /><br />\r\nIn order to reset your password, you need to click the link below. This will generate a new password that will be emailed to you.<br /><br />\r\n{link}<br /><br />\r\nSincerely,<br />\r\n{sitename}\r\n</body>\r\n</html>', '<html>\r\n<body>\r\n<b>{username}</b>,<br /><br />\r\nДля обновления Вашего пароля пройдите по нижеследующей ссылке. Новый, автоматически сгенерированный пароль, будет отправлен на Вашу электронную почту.<br /><br />\r\n{link}<br /><br />\r\nС уважением,<br />\r\n{sitename}', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (862, 'template_new_password_subject', 0, '{sitename} # Password recovery', '{sitename} # Восстановление пароля', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (863, 'template_new_password_body', 0, '<html>\r\n<body>\r\n<b>{username}</b>,<br /><br />\r\nYour password has been reset. You may log in using the password listed below.<br /><br />\r\nUser Name: {username}<br />New Password: {password}<br /><br />\r\nSincerely,<br />\r\n{sitename}', '<html>\r\n<body>\r\n<b>{username}</b>,<br /><br />\r\nВаш пароль был успешно обновлен. Вы можете войти, используя пароль, указанный ниже.<br /><br />\r\nИмя пользователя: {username}<br />Новый пароль: {password}<br /><br />\r\nС уважением,<br />\r\n{sitename}', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (864, 'confirmation_error', 0, 'An error occurred while confirmation!', 'Произошла ошибка при потверждении!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (865, 'lost_password', 1, 'Forgot your password?', 'Забыли пароль?', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (866, 'send', 1, 'Send', 'Отправить', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (867, 'recovery_password_info', 1, 'Enter e-mail to receive an email with instructions to reset your password.', 'Введите e-mail для получения письма с инструкцией по восстановлению пароля.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (868, 'email', 1, 'E-mail:', 'E-mail:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (869, 'request_password_recovery_head', 22, 'Request the password recovery', 'Запрос восстановления пароля', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (870, 'password_recovery_head', 22, 'Password recovery', 'Восстановление пароля', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (871, 'email_error', 22, 'E-mail address is not valid or the user with that address does not exist.', 'Адрес электронной почты указан неверно или пользователя с таким адресом не существует.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (872, 'success_send_password', 22, 'A new password generated and sent to your email.', 'Новый пароль сгенерирован и отправлен на Ваш почтовый ящик.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (873, 'send_success', 22, 'On your email message containing instructions to reset your password.', 'На вашу почту отправлено письмо с инструкцией по восстановлению пароля.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (874, 'send_error', 22, 'Error sending email!', 'Ошибка при отправке почты!', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (875, 'view_in_block', 13, 'Show within a block with the name', 'Показывать внутри блока с названием', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (876, 'view_in_block', 14, 'Show within a block with the name', 'Показывать внутри блока с названием', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (877, 'yes', 13, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (878, 'yes', 14, 'Yes', 'Да', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (879, 'no', 13, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (880, 'no', 14, 'No', 'Нет', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (881, 'vote_already', 3, 'You have already voted. Please try again later!', 'Вы уже голосовали. Попробуйте позже!', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (882, 'vote_need_login', 3, 'Voting is only available to registered users. Log in!', 'Голосование доступно только для зарегистрированных пользователей. Авторизуйтесь!', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (883, 'vote_error', 3, 'The server you voted on no longer exists!', 'Сервер для голосования не существует!', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (884, 'vote_closed', 3, 'Voting has been closed for this server.', 'Голосование для этого сервера было закрыто.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (885, 'monitoring_stats_by_day', 3, 'in the last 24 hours', 'за последние 24 часа', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (886, 'monitoring_stats_by_week', 3, 'in the last 7 days', 'за последние 7 дней', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (887, 'monitoring_stats_by_year', 3, 'in the last 12 months', 'за последние 12 месяцев', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (888, 'server_statistics', 3, 'Server statistics', 'Статистика сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (889, 'percent_uptime', 3, 'Server uptime', 'Аптайм сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (890, 'percent_players', 3, '% of players', '% игроков', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (891, 'vote_up', 5, 'Vote UP', 'Голосовать ЗА', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (892, 'vote_down', 5, 'Vote DOWN', 'Голосовать ПРОТИВ', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (893, 'vote_up', 28, 'Vote UP', 'Голосовать ЗА', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (894, 'vote_down', 28, 'Vote DOWN', 'Голосовать ПРОТИВ', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (895, 'mon_moderated', 36, 'Moderate the server?', 'Модерировать сервера?', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (896, 'help_mon_moderated', 36, 'If set \"Yes\", then when the user adds a server to monitoring or when the user changes the current server settings require moderation.', 'Если установлено \"Да\", то при добавлении пользователем сервера в мониторинг или при изменении пользователем текущих настроек сервера потребуется модерация.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (897, 'mon_name_maxlen', 36, 'Maximum number of characters in the name of the server', 'Максимальное количество символов в названии сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (898, 'help_mon_name_maxlen', 36, 'Users can not add a server to monitoring, if the length of the name server will be greater than the specified value.', 'Пользователи не смогут добавить сервера в мониторинг, если длина названия сервера будет больше, чем указанной значение.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (899, 'mon_name_minlen', 36, 'Minimum number of characters in the name of the server', 'Минимальное количество символов в названии сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (900, 'help_mon_name_minlen', 36, 'Users can not add a server to monitoring, if the length of the name server will be less than the specified value.', 'Пользователи не смогут добавить сервера в мониторинг, если длина названия сервера будет меньше, чем указанной значение.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (901, 'mon_vote_multiple', 36, 'Allow multi-voting?', 'Разрешить мульти-голосование?', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (902, 'help_mon_vote_multiple', 36, 'If you allow a multi-vote, users can vote for each server, and not only one of the entire list.', 'Если разрешить мульти-голосование, то пользователи смогут голосовать за каждый сервер, а не только за один из всего списка.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (903, 'mon_vote_guests', 36, 'Allow guests to vote?', 'Разрешить голосование для гостей?', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (904, 'mon_vote_format', 36, 'The format of voting results', 'Формат результатов голосования', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (905, 'help_mon_vote_guests', 36, 'Enables or disables the guests to take part in voting for the server.', 'Разрешает или запрещает гостям принимать участие в голосованиях за сервер.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (906, 'mon_vote_user_weight', 36, 'Weight of votes a registered user', 'Вес голоса зарегистрированного пользователя', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (907, 'help_mon_vote_user_weight', 36, 'For guests weight of votes equal to 1. Weight voting for registered users, you can set higher to encourage users to register.', 'Для гостей вес голоса равен 1. Вес голоса для зарегистрированных пользователей можно установить выше, чтобы стимулировать пользователей к регистрации.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (908, 'mon_vote_lifetime', 36, 'Time to re-vote', 'Время до повторного голосования', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (909, 'help_mon_vote_lifetime', 36, 'Number of minutes to re-vote. If set to \"0\", then the value will be retained until the end of the current user session.', 'Количество минут до повторного голосования. Если установлено \"0\", то значение будет сохранено до конца текущей сессии пользователя.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (910, 'mon_vote_cookie', 36, 'Name of cookie when voting', 'Название cookie при голосовании', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (911, 'help_mon_vote_cookie', 36, 'This variable cookie will store information about the voting person.', 'В этой переменной cookie будет хранится информация о голосовании пользователя.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (912, 'rating_formula', 36, 'The formula for calculating the rating', 'Формула расчета рейтинга', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (913, 'help_rating_formula', 36, 'The variables are allowed the following templates:<ul><li><b>{viewed}</b> - the number of page views a server.</li><li><b>{votes}</b> - the balance of voting for the server.</li><li><b>{uptime}</b> - the percentage of server uptime.</li><li><b> {online}</b> - the percentage of occupancy of the server players.</li><li><b>{description}</b> - the presence of interesting description.</li><li><b>{banner}</b> - presence of a banner on the site server.</li><li><b>{pr}</b> - Google PageRank site server.</li><li><b>{cy}</b> - Yandex CY site server.</li></ul>', 'В качестве переменных допустимы следующие шаблоны:<ul><li><b>{viewed}</b> - количество просмотров страницы сервера.</li><li><b>{votes}</b> - баланс голосования за сервер.</li><li><b>{uptime}</b> - процент непрерывной работы сервера.</li><li><b>{online}</b> - процент заполненности сервера игроками.</li><li><b>{description}</b> - наличие интересного описания.</li><li><b>{banner}</b> - наличие баннера на сайте сервера.</li><li><b>{pr}</b> - Google PageRank сайта сервера.</li><li><b>{cy}</b> - Yandex CY сайта сервера.</li></ul>', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (914, 'mon_time_prcy', 36, 'The frequency of check CY and PR indicators for sites in days', 'Частота проверки PR и CY показателей для сайтов в днях', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (915, 'help_mon_time_prcy', 36, 'Set to 0 or leave it blank to check Google PageRank and Yandex CY servers for sites mentioned in the monitoring was not performed.', 'Установите 0 или оставьте поле пустым, чтобы проверка Google PageRank и Яндекс ТИЦ для сайтов серверов указанных в мониторинге не проводилась.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (916, 'mon_time_site', 36, 'Frequency of checking the banner sites in hours', 'Частота проверки размещения баннера на сайтах в часах', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (917, 'help_mon_time_site', 36, 'Set to 0 or leave it blank to check the installation site banner for the servers in the monitoring was not performed.', 'Установите 0 или оставьте поле пустым, чтобы проверка установки баннера для сайтов серверов указанных в мониторинге не проводилась.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (918, 'mon_time_vklike', 36, 'Check frequency of VK-Likes in minutes', 'Частота проверки количества VK-лайков в минутах', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (919, 'help_mon_time_vklike', 36, 'If you use the formula ranking figure VK-Likes page server and configured a Vkontakte API ID, please indicate here the number of minutes between checks of VK-Likes page server.', 'Если вы используете в формуле рейтинга показатель VK-лайков страницы сервера и в настройках указан Vkontakte API ID, то укажите тут количество минут между проверками количества VK-лайков страницы сервера.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (920, 'affected_rating', 70, 'Participate in improving server:', 'Участвуй в развитии сервера:', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (921, 'position_rating', 70, 'position in the overall ranking', 'позиция в общем рейтинге', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (922, 'players', 70, 'Players:', 'Игроки:', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (923, 'map', 70, 'Map:', 'Карта:', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (924, 'ms', 70, 'ms.', 'мс.', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (925, 'rating', 70, 'Rating:', 'Рейтинг:', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (926, 'no_image', 70, 'Map image is missing', 'Изображение карты отсутствует', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (927, 'players_info', 70, 'Players in the game', 'Информация по игрокам', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (928, 'not_valid_server', 70, 'This server does not exist!', 'Этот сервер не существует!', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (929, 'steam_connect', 70, 'Connect through Steam', 'Подключиться через Steam', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (930, 'srv_info', 70, 'Server information', 'Информация о сервере', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (931, 'srv_userbar', 70, 'Userbars', 'Юзербары', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (932, 'srv_rating', 70, 'Rating', 'Рейтинг', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (933, 'srv_stats', 70, 'Statistics', 'Статистика', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (934, 'vote_up', 70, 'Vote UP', 'Голосовать ЗА', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (935, 'vote_down', 70, 'Vote DOWN', 'Голосовать ПРОТИВ', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (936, 'start_voting', 70, 'Vote:', 'Голосовать:', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (937, 'add_favorites', 70, 'Add to favorites', 'Добавить в избранные', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (938, 'remove_favorites', 70, 'Remove from favorites', 'Удалить из избранных', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (939, 'website', 70, 'Site:', 'Сайт:', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (940, 'ping', 70, 'Ping:', 'Пинг:', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (941, 'rating_calc_option', 70, 'Parameter', 'Параметр', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (942, 'rating_calc_value', 70, 'Value', 'Значение', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (943, 'rating_calc_result', 70, 'Number of points', 'Количество баллов', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (944, 'rating_var_description', 70, 'Interesting description', 'Интересное описание', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (945, 'rating_var_viewed', 70, 'Page views server', 'Количество просмотров страницы сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (946, 'rating_var_votes', 70, 'Number of votes', 'Количество голосов', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (947, 'rating_var_online', 70, 'Fullness server players', 'Заполненность сервера игроками', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (948, 'rating_var_uptime', 70, 'Server uptime', 'Аптайм сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (949, 'rating_var_pr', 70, 'Google PageRank server site', 'Google PageRank сайта сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (950, 'rating_var_cy', 70, 'Yandex CY server site', 'Яндекс ТИЦ сайта сервера', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (951, 'rating_var_banner', 70, 'The server site has banner', 'Баннер на сайте размещен', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (952, 'yes', 70, 'yes', 'да', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (953, 'no', 70, 'no', 'нет', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (954, 'rating_var_vklikes', 70, 'Number VK-Likes', 'Количество VK-лайков', 'ratingServers');
INSERT INTO `acp_lang_words` VALUES (955, 'add_new_task', 0, 'Add Task', 'Добавление Задания', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (956, 'task_sheduler', 0, 'Task Sheduler', 'Планировщик Задач', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (957, 'edit_task', 0, 'Edit Task', 'Редактирование Задания', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (958, 'help_tools_perm_cron', 61, 'Manage crontab', 'Управление диспетчером задач', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (959, 'help_tools_perm_cron', 62, 'Manage crontab', 'Управление диспетчером задач', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (960, 'add_task', 71, 'Add task', 'Добавить задачу', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (961, 'empty_table', 71, 'Data table is empty!', 'Таблица с данными пуста!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (962, 'file_not_exists', 71, 'file \"/acpanel/includes/cron/%s\" does not exist', 'файл \"/acpanel/includes/cron/%s\" не существует', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (963, 'file_not_empty', 71, 'executable is not specified', 'не указан выполняемый файл', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (964, 'day_not_month', 71, 'In the %s months of %s days is not', 'В %s месяце нет %s числа', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (965, 'values_error', 71, 'Errors in adding', 'Ошибки при добавлении', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (966, 'add_failed', 71, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (967, 'add_success', 71, 'Task has been successfully created!', 'Задача успешно добавлена!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (968, 'empty_array', 71, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (969, 'del_success', 71, 'The value successfully removed!', 'Значение успешно удалено!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (970, 'del_failed', 71, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (971, 'del_multiply_success', 71, 'Successfully removed the values:', 'Успешно удалено значений:', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (972, 'change_status_success', 71, 'Task status successfully changed!', 'Статус задачи успешно изменен!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (973, 'change_status_error', 71, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (974, 'edit_success', 71, 'Edited successfully!', 'Отредактировано успешно!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (975, 'edit_failed', 71, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (976, 'edit_error', 71, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (977, 'cache_timeleft', 71, 'Before update the cache tasks remain:', 'До обновления кэша задач осталось:', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (978, 'confirm_del', 72, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (979, 'cron_expression', 72, 'Cron expression', 'Правила', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (980, 'last_run', 72, 'Last run', 'Последний', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (981, 'next_run', 72, 'Next run', 'Следующий', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (982, 'run_file', 72, 'File', 'Файл', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (983, 'click_change_status', 72, 'Click to change the status of task', 'Нажмите, чтобы изменить статус задачи', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (984, 'del_selected', 72, 'Remove selected', 'Удалить записи', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (985, 'not_selected', 72, 'Entries not selected!', 'Записи не выбраны!', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (986, 'run_file', 73, 'File to execute', 'Файл для выполнения', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (987, 'minutes', 73, 'minutes', 'минуты', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (988, 'hours', 73, 'hours', 'часы', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (989, 'days', 73, 'days', 'дни', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (990, 'months', 73, 'months', 'месяцы', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (991, 'product_id', 73, 'Product ID', 'ID продукта', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (992, 'task_active', 73, 'Task active?', 'Задание активно?', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (993, 'yes', 73, 'Yes', 'Да', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (994, 'no', 73, 'No', 'Нет', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (995, 'add', 73, 'Add', 'Добавить', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (996, 'file_need_upload', 73, 'The executable file must be uploaded to the server.', 'Выполняемый файл должен быть загружен на сервер.', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (997, 'run_file', 74, 'File to execute', 'Файл для выполнения', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (998, 'minutes', 74, 'minutes', 'минуты', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (999, 'hours', 74, 'hours', 'часы', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1000, 'days', 74, 'days', 'дни', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1001, 'months', 74, 'months', 'месяцы', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1002, 'product_id', 74, 'Product ID', 'ID продукта', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1003, 'task_active', 74, 'Task active?', 'Задание активно?', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1004, 'yes', 74, 'Yes', 'Да', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1005, 'no', 74, 'No', 'Нет', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1006, 'apply', 74, 'Apply', 'Применить', 'taskSheduler');
INSERT INTO `acp_lang_words` VALUES (1007, 'game_accounts', 0, 'Game Accounts', 'Игровые Аккаунты', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1008, 'user_accounts', 0, 'User Accounts', 'Аккаунты пользователей', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1009, 'add_account', 0, 'Add Account', 'Добавление Аккаунта', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1010, 'user_requests', 0, 'User Requests', 'Заявки пользователей', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1011, 'edit_account', 0, 'Edit Account', 'Редактирование Аккаунта', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1012, 'access_mask', 0, 'Access Mask', 'Маски Доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1013, 'access_mask_add', 0, 'Add Mask', 'Добавить Маску', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1014, 'access_mask_edit', 0, 'Edit Mask', 'Редактирование Маски', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1015, 'user_requests_edit', 0, 'Edit Request', 'Редактирование Заявки', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1016, 'user_notvalid_for_regaccount', 0, 'Your user account not activated to control the gaming account. On our project to activate the account using a special program. <a href=\"acpanel/acp.exe\">Download the program...</a>', 'Ваша учетная запись не активирована для управления игровым аккаунтом. На нашем проекте для активации учетной записи используется специальная программа. <a href=\"acpanel/files/rega.exe\">Скачать программу...</a>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1017, 'ga_registration_closed', 0, 'Register new account is currently disabled by the administrator.', 'Регистрация новых аккаунтов в данный момент отключена администратором.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1018, 'block_accounts_stats', 0, 'Game accounts', 'Игровые аккаунты', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1019, 'ga_search', 0, 'Search accounts', 'Поиск аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1020, 'total_reg_accounts', 3, 'Registered accounts', 'Зарегистрировано аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1021, 'accounts_reg_stats', 3, 'Statistics registered accounts', 'Статистика регистрации аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1022, 'accounts_reg_stats_by_week', 3, 'in the last 7 days', 'за последние 7 дней', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1023, 'accounts_reg_stats_by_year', 3, 'in the last 12 months', 'за последние 12 месяцев', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1024, 'acc_auth', 17, 'Authorization:', 'Авторизация:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1025, 'auth_by_nick', 17, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1026, 'auth_by_ip', 17, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1027, 'auth_by_steam', 17, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1028, 'player_nick', 17, 'Nick:', 'Ник:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1029, 'player_ip', 17, 'IP address:', 'IP адрес:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1030, 'player_steam', 17, 'SteamID:', 'SteamID:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1031, 'acc_status', 17, 'Status:', 'Статус:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1032, 'active', 17, '<b><font color=\"green\">active</font></b>', '<b><font color=\"green\">активен</font></b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1033, 'inactive', 17, '<b><font color=\"red\">inactive</font></b>', '<b><font color=\"red\">неактивен</font></b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1034, 'acc_online', 17, 'Online (seconds):', 'Онлайн (секунды):', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1035, 'acc_points', 17, 'Points:', 'Поинты:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1036, 'opt_accounts', 26, 'Activate accounts work?', 'Активировать работу аккаунтов?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1037, 'default_access', 36, 'The access mask default', 'Маска доступа по-умолчанию', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1038, 'help_default_access', 36, 'This mask will be placed access to all new accounts during the registration.', 'Эта маска доступа будет проставляться всем новым аккаунтам при регистрации.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1039, 'ticket_moderate', 36, 'Moderate tickets of players?', 'Модерировать заявки игроков?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1040, 'help_ticket_moderate', 36, 'If you put \"no\", then create/edit users game account will not require confirmation by the administrator.', 'Если поставить \"нет\", то создание/изменение игрового аккаунта пользователем не будет требовать подтверждения администратором.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1041, 'ga_time_format', 36, 'The display format of the time in seconds', 'Формат отображения времени в секундах', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1042, 'help_ga_time_format', 36, 'If you leave the field blank, you will see a second without formatting. For all other cases:<ul><li>dddd, hhhh, mmmm, ssss - full text (eg. dddd hhhh mmmm ssss - 1 day 2 hours 3 minutes 10 seconds)</li><li>ddd, hhh, mmm, sss - with a short text (eg. ddd hhh mmm sss - 1d 2h 3m 10s)</li><li>dd, hh, mm, ss - without text support (eg. hh:mm:ss - 02:03:10)</li></ul>', 'Если оставить поле пустым, то будут отображены секунды без форматирования. Для всех других случаев:<ul><li>dddd, hhhh, mmmm, ssss - с полным текстом (например dddd hhhh mmmm ssss - 1 день 2 часа 3 минуты 10 секунд)</li><li>ddd, hhh, mmm, sss - с кратким текстом (например ddd hhh mmm sss - 1д 2ч 3м 10с)</li><li>dd, hh, mm, ss - без текстового сопровождения (например hh:mm:ss -  02:03:10)</li></ul>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1043, 'ga_nicklen_min', 36, 'The minimum length of nick', 'Минимальная длина ника', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1044, 'ga_nicklen_max', 36, 'The maximum length of nick', 'Максимальная длина ника', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1045, 'help_ga_nicklen_min', 36, 'The minimum number of characters in the nickname for the account registration by nick.', 'Минимальное количество символов в нике для регистрации аккаунтов по нику.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1046, 'help_ga_nicklen_max', 36, 'Maximum number of characters in the nickname for the account registration by nick.', 'Максимальное количество символов в нике для регистрации аккаунтов по нику.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1047, 'help_ga_access_type', 36, 'Specify the allowed types of registration for the new game accounts.', 'Укажите разрешенные типы регистрации для новых игровых аккаунтов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1048, 'ga_access_type', 36, 'The permitted types of registration', 'Разрешенные типы регистрации', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1049, 'type_by_nick', 36, 'by Nickname and Password', 'по Нику и Паролю', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1050, 'type_by_ip', 36, 'by IP', 'по IP-адресу', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1051, 'type_by_steam', 36, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1052, 'ga_reg_closed', 36, 'Registration is closed', 'Регистрация закрыта', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1053, 'ga_reg_site', 36, 'Register via the website', 'Регистрация через сайт', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1054, 'ga_reg_soft', 36, 'Register through the program', 'Регистрация через программу', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1055, 'ga_registration', 36, 'Registration type game accounts', 'Тип регистрации игровых аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1056, 'help_ga_registration', 36, 'When emissions such as \"Register through the program\" - the user will need to download and run a program for transferring HID hard drive, and only then he can create an account. If you select \"Register via the website\" - Users will register accounts in standard mode.', 'При выбре типа \"Регистрация через программу\" - пользователю будет необходимо скачать программу и запустить её для передачи HID жесткого диска и только после этого он сможет создать аккаунт. При выборе \"Регистрация через сайт\" - пользователи будут регистрировать аккаунты в стандартном режиме.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1057, 'ga_admin_flag', 36, 'Admin flag', 'Флаг админа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1058, 'help_ga_admin_flag', 36, 'Setting up is needed to display the player on the page server administrators. If the flag is not specified, the list of admins will be formed only on the basis set of groups of administrators.', 'Настройка необходима для вывода игроков на странице администраторов серверов. Если флаг не указан, то список админов будет формироваться только с учетом настройки групп администраторов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1059, 'ga_flag_ignore', 36, 'Ignore the flag', 'Не учитывать флаг', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1060, 'help_ga_cache_block_accounts', 36, 'Time in minutes to update the cache for game accounts stats. Enter \"0\" if you want to disable caching.', 'Время в минутах до обновления кеша для статистики игровых аккаунтов в боковой панели. Укажите \"0\", если желаете отключить кеширование.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1061, 'help_ga_active_time', 36, 'Time in hours, beyond which the user will be inactive.', 'Время в часах, при превышении которого пользователь будет считаться неактивным.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1062, 'ga_cache_block_accounts', 36, 'Caching time block of game accounts', 'Время кеширования блока игровых аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1063, 'ga_active_time', 36, 'The period of active accounts', 'Период активности аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1064, 'help_default_access_time', 36, 'Specify how many hours will apply an access mask by default. Leave blank for infinite action.', 'Укажите сколько часов будет действовать маска доступа по-умолчанию. Оставьте поле пустым для постоянного действия.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1065, 'default_access_time', 36, 'Duration of the access mask default, in hours', 'Срок действия маски доступа по-умолчанию, в часах', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1066, 'ga_password_validate', 36, 'Pattern to check the password', 'Шаблон для проверки пароля игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1067, 'help_ga_password_validate', 36, 'Specify a regular expression that matches the password will be checked by the player in the creation and editing of game accounts users.', 'Укажите регулярное выражение, соответствие которому будет проверяться пароль игрока при создании и редактировании игровых аккаунтов пользователями.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1068, 'help_ga_perm_masks', 61, 'Manage access masks', 'Управление масками доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1069, 'help_tickets', 61, 'Users requests', 'Заявки пользователей', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1070, 'help_perm_tickets', 61, 'Manage requests', 'Управление заявками', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1071, 'help_ga_perm_masks', 62, 'Manage access masks', 'Управление масками доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1072, 'help_tickets', 62, 'Users requests', 'Заявки пользователей', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1073, 'help_perm_tickets', 62, 'Manage requests', 'Управление заявками', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1074, 'game_account', 63, 'Game account', 'Игровой аккаунт', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1075, 'ticket_on_moderate', 63, 'You have sent a request to change the account. While the ticket is in moderation you will be able to continue to play under the old data. If you make a mistake when you make a ticket or have decided not to change an account, you have the opportunity to withdraw the ticket by clicking on the link in the list of tickets.', 'Вы отправляли запрос на изменение аккаунта. Пока заявка находится на модерации Вы сможете продолжать играть под старыми данными. Если Вы допустили ошибку при оформлении заявки или передумали изменять аккаунт, то есть возможность отозвать заявку, нажав на соответствующую ссылку в списке заявок.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1076, 'ticket_reg_on_moderate', 63, 'You have sent a request to create a game account. The ticket is in moderation. Terms of moderation is not rationing, but usually is not more than 24 hours.', 'Вы отправляли запрос на создание игрового аккаунта. Заявка находится на модерации. Сроки модерирования не нормированны, но, как правило, составляют не более 24 часов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1077, 'not_account', 63, 'Account is not created. To create it using the form.', 'Аккаунт не создан. Для его создания заполните форму.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1078, 'player_ip', 63, 'Your ip-address', 'Ваш ip-адрес', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1079, 'edit_account_info', 63, 'Edit account', 'Редактировать', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1080, 'create_account', 63, 'Create account', 'Создать аккаунт', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1081, 'auth_type', 63, '<b>Authorization type:</b>', '<b>Тип авторизации:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1082, 'player_steam', 63, '<b>Your SteamID:</b>', '<b>Ваш SteamID:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1083, 'player_nick', 63, '<b>Your nick:</b>', '<b>Ваш ник:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1084, 'block_account', 63, 'Account has been locked by the administrator. The reason for the block you can find on the forum.', 'Аккаунт заблокирован администратором. О причине блокировки Вы можете узнать на форуме.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1085, 'auth_type_info', 63, '<ul><li><b>Authorization by Nick</b> - linking your account to your gaming nick. Log on to the server under his nickname will be possible only when you specify the correct password.</li><li><b>Authorization by IP</b> - linking your account to an IP-address. Ideal for players with a static IP.</li><li><b>Authorization by SteamID</b> - linking your account number to Steam. You can change nicknames, ip-address, but you will always be given to you, if SteamID coincides with the one you have registered.</li></ul>', '<ul><li><b>Авторизация по Нику</b> - привязка аккаунта к Вашему игровому нику. Вход на сервер под своим ником будет возможен только при указании верного пароля.</li><li><b>Авторизация по IP</b> - привязка аккаунта к IP-адресу. Идеально подходит для игроков имеющих статический IP.</li><li><b>Авторизация по SteamID</b> - привязка аккаунта к номеру Steam. Можете менять ники, ip-адреса, но доступ всегда будет Вам предоставлен, если SteamID совпадает с тем, который вы зарегистрируете.</li></ul>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1086, 'acc_online', 63, 'Last online:', 'Последний онлайн:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1087, 'acc_created', 63, 'Created:', 'Создан:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1088, 'auth_nick', 63, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1089, 'auth_ip', 63, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1090, 'auth_steam', 63, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1091, 'user_email_confirm', 63, 'Your email address is not confirmed. Click on the link sent to you by e-mail to manage your game account.', 'Ваш адрес электронной почты ещё не подтвержден. Перейдите по ссылке, отправленной Вам на почту, чтобы управлять своим игровым аккаунтом.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1092, 'user_moderated', 63, 'Your account is in moderation. In this status you can not control their gaming account.', 'Ваша учетная запись находится на модерации. В данном статусе Вы не сможете управлять своим игровым аккаунтом.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1093, 'user_not_found', 75, 'user not found', 'пользователь не найден', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1094, 'pass_not_empty', 75, 'Password must not be empty', 'Пароль не должен быть пустым', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1095, 'ip_not_valid', 75, 'IP address is not valid', 'IP адрес указан неверно', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1096, 'error_list', 75, 'Errors when execution:', 'Ошибки при выполнении:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1097, 'add_failed', 75, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1098, 'add_success', 75, 'Account has been successfully created!', 'Аккаунт успешно создан!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1099, 'empty_array', 75, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1100, 'del_success', 75, 'Account has been successfully deleted!', 'Аккаунт успешно удален!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1101, 'del_failed', 75, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1102, 'del_multiply_success', 75, 'Successfully removed user accounts:', 'Успешно удалены аккаунты пользователей:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1103, 'active_success', 75, 'Accounts are activated successfully:', 'Аккаунты успешно активированы:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1104, 'active_error', 75, 'An error occurred when changing the status of your account. Try again later!', 'Произошла ошибка при изменении статуса аккаунта. Повторите позже!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1105, 'inactive_success', 75, 'Accounts are deactivated successfully:', 'Аккаунты успешно деактивированы:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1106, 'user_reg_date', 75, 'Registration date:', 'Дата регистрации:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1107, 'user_last_visit', 75, 'Last visit:', 'Последний визит:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1108, 'user_mail', 75, 'E-mail:', 'E-mail:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1109, 'user_icq', 75, 'ICQ:', 'ICQ:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1110, 'user_hid', 75, 'HID:', 'HID:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1111, 'user_group', 75, 'Group:', 'Группа:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1112, 'user_reg_ip', 75, 'IP at registration:', 'IP при регистрации:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1113, 'change_status_success', 75, 'Account status successfully changed!', 'Статус аккаунта успешно изменен!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1114, 'change_status_error', 75, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1115, 'edit_error', 75, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1116, 'edit_success', 75, 'Account has been successfully edited!', 'Аккаунт успешно отредактирован!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1117, 'add_sync_failed', 75, 'An error occurred during the binding account to access the masks!', 'Ошибка во время привязки аккаунта к маскам доступа!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1118, 'add_mask_success', 75, 'The access mask was successfully added!', 'Маска доступа успешно добавлена!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1119, 'add_lastid_failed', 75, 'Error in getting ID added mask!', 'Ошибка в получении ID добавленной маски!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1120, 'add_srvsync_failed', 75, 'An error occurred while synchronizing with the server!', 'Ошибка при синхронизации с серверами!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1121, 'del_mask_success', 75, 'The access mask is removed successfully!', 'Маска доступа успешно удалена!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1122, 'del_synctbl_failed', 75, 'Error removing the mask from the server and players!', 'Ошибка при удалении соответствия маски с серверами и игроками!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1123, 'dont_empty', 75, 'Field must not be empty!', 'Поле не должно быть пустым!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1124, 'edit_srvsync_failed', 75, 'An error occurred while synchronizing with the server!', 'Ошибка при синхронизации с серверами!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1125, 'edit_mask_success', 75, 'The access mask is successfully edited!', 'Маска доступа успешно отредактирована!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1126, 't_create_acc_by_nick', 75, 'Registration by Nick', 'Регистрация по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1127, 't_create_acc_by_ip', 75, 'Registration by IP', 'Регистрация по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1128, 't_create_acc_by_steam', 75, 'Registration by SteamID', 'Регистрация по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1129, 't_change_nick', 75, 'Change nick', 'Смена ника', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1130, 't_change_ip', 75, 'Change IP', 'Смена IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1131, 't_change_steam', 75, 'Change SteamID', 'Смена SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1132, 't_change_auth_nick', 75, 'Change authorization by nick', 'Смена авторизации по нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1133, 't_change_auth_ip', 75, 'Change authorization by IP', 'Смена авторизации по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1134, 't_change_auth_steam', 75, 'Change authorization by SteamID', 'Смена авторизации по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1135, 'error_ticket_closed', 75, 'Error: the ticket is now closed!', 'Ошибка: заявка уже закрыта!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1136, 'error_ticket_not_found', 75, 'Error: ticket not found!', 'Ошибка: заявка не найдена!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1137, 'ticket_withdraw_success', 75, 'Ticket withdrawn successfully!', 'Заявка успешно отозвана!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1138, 'data_identity', 75, 'These are identical!', 'Данные идентичны!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1139, 'create_ticket_denied', 75, 'Change your account denied!', 'Изменение аккаунта запрещено!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1140, 'steam_not_empty', 75, 'SteamID is not valid!', 'SteamID указан неверно!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1141, 'update_pass_error', 75, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1142, 'ticket_error', 75, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1143, 'ticket_success', 75, 'Ticket for change of account has been successfully created!', 'Заявка на изменение аккаунта успешно создана!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1144, 'nick_not_empty', 75, 'Nickname is empty!', 'Nick не заполнен!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1145, 'ip_not_empty', 75, 'IP-address is empty!', 'IP-адрес не заполнен!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1146, 'nick_already_used', 75, 'This nickname is already in use!', 'Указанный ник уже используется!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1147, 'ticket_success_edit', 75, 'Account has been successfully changed!', 'Аккаунт успешно изменен!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1148, 'nick_is_short', 75, 'Short nickname!', 'Ник короткий!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1149, 'nick_is_long', 75, 'Nick is too long!', 'Ник слишком длинный!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1150, 'del_ticket_success', 75, 'The ticket was successfully deleted!', 'Заявка успешно удалена!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1151, 'del_multiply_tickets_success', 75, 'Successfully removed user tickets:', 'Успешно удалены заявки пользователей:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1152, 'tickets_not_found', 75, 'tickets not found!', 'заявки не найдены!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1153, 'error_account_update', 75, 'has not been updated for a user account: #', 'не обновлен аккаунт для пользователя: #', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1154, 'error_ticket_update', 75, 'ticket is not updated: #', 'не обновлена заявка: #', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1155, 'error_account_created', 75, 'not created an account for user: #', 'не создан аккаунт для пользователя: #', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1156, 'approve_multiply_tickets_success', 75, 'Successfully approved tickets:', 'Успешно одобрено заявок:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1157, 'disapprove_multiply_tickets_success', 75, 'Successfully disapproved tickets:', 'Успешно отклонено заявок:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1158, 'update_pass_success', 75, 'Password was successfully changed!', 'Пароль успешно изменен!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1159, 'del_default_mask', 75, 'You can not delete the access mask specified by default!', 'Невозможно удалить маску доступа заданную по-умолчанию!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1160, 'server_not_select', 75, 'Server not selected!', 'Сервера не выбраны!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1161, 'accounts_all', 75, 'Total accounts', 'Всего аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1162, 'accounts_by_ip', 75, '- by ip', '- по ip', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1163, 'accounts_by_nick', 75, '- by nick', '- по нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1164, 'accounts_by_steam', 75, '- by steamid', '- по steamid', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1165, 'accounts_blocked', 75, 'Blocked accounts', 'Заблокировано аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1166, 'tickets_total', 75, 'Total tickets players', 'Всего заявок игроков', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1167, 'tickets_approved', 75, '- approved', '- одобрено', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1168, 'tickets_rejected', 75, '- rejected', '- отклонено', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1169, 'tickets_open', 75, '- opened', '- открыто', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1170, 'active_access_masks', 75, 'Active access masks', 'Активных масок доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1171, 'not_accounts_and_tickets', 75, 'Game accounts and request of players missing from the database!', 'Игровые аккаунты и заявки игроков в базе данных отсутствуют!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1172, 'error_unserialize_ticket_type', 75, 'error when serializing the array or the wrong type of ticket: #', 'ошибка при сериализации массива или некорректный тип заявки: #', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1173, 'error_not_product', 75, 'handler of the product was not found for ticket: #', 'обработчик продукта не найден для заявки: #', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1174, 'possible_bans_no', 75, 'Possible bans not found', 'Возможные баны не найдены', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1175, 'possible_bans_yes', 75, 'Possible bans: %s (<span title=\"Active bans\">%s</span>)', 'Возможные баны: %s (<span title=\"Активные баны\">%s</span>)', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1176, 'password_not_valid', 75, 'The password contains illegal characters', 'Пароль содержит запрещенные символы', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1177, 'empty_table', 76, 'Data table is empty!', 'Таблица с данными пуста!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1178, 'head', 76, 'User accounts', 'Аккаунты пользователей', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1179, 'add_account_link', 76, '<font size=\"5\">+</font>', '<font size=\"5\">+</font>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1180, 'all_types', 76, 'All types', 'Все типы', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1181, 'all_status', 76, 'All status', 'Все статусы', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1182, 'active', 76, 'Active', 'Активные', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1183, 'inactive', 76, 'Inactive', 'Неактивные', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1184, 'auth_nick', 76, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1185, 'auth_ip', 76, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1186, 'auth_steam', 76, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1187, 'player_nick', 77, 'Player nick', 'Ник игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1188, 'auth_type', 77, 'Authorization', 'Авторизация', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1189, 'user_name', 77, 'User', 'Пользователь', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1190, 'last_game', 77, 'Last game', 'Последняя игра', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1191, 'auth_steam', 77, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1192, 'auth_ip', 77, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1193, 'auth_nick', 77, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1194, 'not_auth_type', 77, '-', '-', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1195, 'confirm_del', 77, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1196, 'apply', 77, 'Apply', 'Применить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1197, 'delete', 77, 'Delete', 'Удалить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1198, 'active', 77, 'Activate', 'Активировать', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1199, 'inactive', 77, 'Deactivate', 'Деактивировать', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1200, 'click_change_status', 77, 'Click to change the account status', 'Нажмите, чтобы изменить статус аккаунта', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1201, 'not_selected', 77, 'Entries not selected!', 'Записи не выбраны!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1202, 'data_created', 77, 'Created', 'Создан', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1203, 'back_url', 78, 'Back to the list of accounts', 'Вернуться к списку аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1204, 'username', 78, 'User', 'Пользователь', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1205, 'auth_type', 78, 'Authorization', 'Авторизация', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1206, 'auth_nick', 78, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1207, 'auth_ip', 78, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1208, 'auth_steam', 78, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1209, 'player_nick_password', 78, 'Player nick and password', 'Ник и пароль игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1210, 'player_ip', 78, 'Player IP', 'IP игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1211, 'player_steam', 78, 'Player SteamID', 'SteamID игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1212, 'add_access', 78, 'Access flags and expiration date', 'Флаги доступа и дата окончания их действия', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1213, 'approved', 78, 'Account active?', 'Аккаунт активен?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1214, 'yes', 78, 'Yes', 'Да', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1215, 'no', 78, 'No', 'Нет', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1216, 'player_online', 78, 'Player online (seconds)', 'Онлайн игрока (секунды)', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1217, 'player_points', 78, 'Points', 'Поинты', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1218, 'save', 78, 'Save', 'Сохранить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1219, 'user_profile', 78, 'Open a user profile in a new window', 'Открыть профиль пользователя в новом окне', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1220, 'user_profile_link', 78, 'Go to profile', 'Перейти в профиль пользователя', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1221, 'insert_nick', 78, 'enter nickname', 'введите ник', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1222, 'insert_pass', 78, 'enter password', 'введите пароль', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1223, 'field_not_valid', 78, 'field not valid -', 'поле некорректно заполнено -', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1224, 'error_list', 78, 'Errors when adding:', 'Ошибки при добавлении:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1225, 'permanent', 78, 'never', 'никогда', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1226, 'default_mask_not_set', 78, 'Do not set access mask default!', 'Не установлена маска доступа по-умолчанию!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1227, 'mask_not_found', 78, 'A table with access masks is empty!', 'Таблица с масками доступа пуста!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1228, 'mask_access_limit', 78, 'Reached the limit of the existing access masks!', 'Достигнут лимит существующих масок доступа!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1229, 'access_mask', 78, 'Access mask:', 'Маска доступа:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1230, 'add_one_mask', 78, 'ADD АNОТHЕR ACCESS MASK', 'ДОБАВИТЬ ЕЩЕ ОДНУ МАСКУ ДОСТУПА', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1231, 'ga_access_servers', 78, 'Available on the servers:', 'Доступ на серверах:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1232, 'ga_all_servers', 78, 'all', 'все', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1233, 'back_url', 79, 'Back to the list of accounts', 'Вернуться к списку аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1234, 'username', 79, 'User', 'Пользователь', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1235, 'auth_type', 79, 'Authorization', 'Авторизация', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1236, 'auth_nick', 79, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1237, 'auth_ip', 79, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1238, 'auth_steam', 79, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1239, 'player_nick_password', 79, 'Player nick and password', 'Ник и пароль игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1240, 'player_ip', 79, 'Player IP', 'IP игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1241, 'player_steam', 79, 'Player SteamID', 'SteamID игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1242, 'add_access', 79, 'Access flags and expiration date', 'Флаги доступа и дата окончания их действия', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1243, 'approved', 79, 'Account active?', 'Аккаунт активен?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1244, 'yes', 79, 'Yes', 'Да', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1245, 'no', 79, 'No', 'Нет', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1246, 'player_online', 79, 'Player online (seconds)', 'Онлайн игрока (секунды)', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1247, 'player_points', 79, 'Points', 'Поинты', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1248, 'save', 79, 'Save', 'Сохранить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1249, 'user_profile', 79, 'Go to user profile', 'Перейти в профиль', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1250, 'permanent', 79, 'never', 'никогда', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1251, 'mask_access_limit', 79, 'Reached the limit of the existing access masks!', 'Достигнут лимит существующих масок доступа!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1252, 'add_one_mask', 79, 'ADD АNОТHЕR ACCESS MASK', 'ДОБАВИТЬ ЕЩЕ ОДНУ МАСКУ ДОСТУПА', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1253, 'not_account', 79, 'Account does not exist!', 'Аккаунт не существует!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1254, 'user_reg_date', 79, 'Registration date:', 'Дата регистрации:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1255, 'user_last_visit', 79, 'Last visit:', 'Последний визит:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1256, 'user_mail', 79, 'E-mail:', 'E-mail:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1257, 'user_icq', 79, 'ICQ:', 'ICQ:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1258, 'user_hid', 79, 'HID:', 'HID:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1259, 'user_group', 79, 'Group:', 'Группа:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1260, 'user_reg_ip', 79, 'IP at registration:', 'IP при регистрации:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1261, 'account_reg_date', 79, 'Registered:', 'Зарегистрирован:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1262, 'account_last_time', 79, 'Last online:', 'Последний онлайн:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1263, 'error_list', 79, 'Errors when editing:', 'Ошибки при редактировании:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1264, 'field_not_valid', 79, 'incorrect field', 'некорректное поле', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1265, 'account_not_mask', 79, 'By the account is not tied access mask!', 'К акаунту не привязаны маски доступа!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1266, 'add_first_mask', 79, 'ADD ACCESS MASK', 'ДОБАВИТЬ МАСКУ ДОСТУПА', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1267, 'delete', 79, 'Delete', 'Удалить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1268, 'question_delete_account', 79, 'Are you sure you want to delete the account?', 'Вы уверены, что хотите удалить аккаунт?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1269, 'ga_access_servers', 79, 'Available on the servers:', 'Доступ на серверах:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1270, 'ga_all_servers', 79, 'all', 'все', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1271, 'find_possible_bans', 79, 'Find possible bans...', 'Поиск возможных банов...', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1272, 'coincidence_of_nick', 79, 'matches by Nck:', 'совпадения по Нику:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1273, 'coincidence_of_ip', 79, 'matches by IP:', 'совпадения по IP:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1274, 'coincidence_of_cookie', 79, 'matches by Cookie:', 'совпадения по Cookie:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1275, 'coincidence_of_steam', 79, 'matches by SteamID:', 'совпадения по SteamID:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1276, 'ticket_date', 80, 'Date', 'Дата', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1277, 'ticket_action', 80, 'Action', 'Действие', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1278, 'ticket_approved', 80, 'Approved', 'Одобрено', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1279, 'ticket_status', 80, 'Status', 'Статус', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1280, 'ticket_denied', 80, 'Denied', 'Отказано', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1281, 'ticket_elapsed', 80, 'Processing time', 'Время рассмотрения', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1282, 'ticket_moderation', 80, 'Moderation', 'На модерации', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1283, 'ticket_withdraw', 80, 'withdraw', 'отозвать', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1284, 'ticket_details', 80, 'details', 'детали', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1285, 'ticket_comment', 80, 'Comment moderator', 'Комментарий модератора', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1286, 'my_tickets', 80, 'My tickets', 'Мои заявки', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1287, 'confirm_del', 80, 'Are you sure you want to withdraw the ticket?', 'Вы уверены, что желаете отозвать заявку?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1288, 'add_mask', 81, 'Add mask', 'Добавить маску', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1289, 'head', 81, 'Access mask', 'Маски доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1290, 'mask_servers', 82, 'Servers', 'Сервера', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1291, 'mask_flags', 82, 'Flags', 'Флаги', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1292, 'permanent', 82, 'never', 'никогда', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1293, 'del_selected', 82, 'Remove selected', 'Удалить записи', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1294, 'not_selected', 82, 'Entries not selected!', 'Записи не выбраны!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1295, 'confirm_del', 82, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1296, 'all', 82, 'ALL', 'ВСЕ', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1297, 'mask_players', 82, 'Accounts', 'Аккаунты', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1298, 'add_mask', 83, 'Add mask', 'Добавить маску', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1299, 'mask_servers', 83, 'Access to all servers?', 'Доступ на всех серверах?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1300, 'mask_flags', 83, 'Access flags', 'Флаги доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1301, 'yes', 83, 'Yes', 'Да', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1302, 'no', 83, 'No', 'Нет', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1303, 'add', 83, 'Add', 'Добавить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1304, 'add_success', 83, 'The mask is added!', 'Маска добавлена!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1305, 'edit_mask', 84, 'Edit mask', 'Редактирование маски', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1306, 'mask_servers', 84, 'Access to all servers?', 'Доступ на всех серверах?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1307, 'mask_flags', 84, 'Access flags', 'Флаги доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1308, 'yes', 84, 'Yes', 'Да', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1309, 'no', 84, 'No', 'Нет', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1310, 'save', 84, 'Save', 'Сохранить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1311, 'edit_success', 84, 'Edited successfully!', 'Отредактировано успешно!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1312, 'auth_type', 85, 'Authorization', 'Авторизация', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1313, 'player_steam', 85, 'Your SteamID', 'Ваш SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1314, 'moderate_change_steam', 85, 'If you change the type of authorization or SteamID, your account will go to moderation, you can continue to play under the old data until the new settings will be handled. Timing of review of your application irregular, but usually range from a few minutes to 24 hours.', 'При смене типа авторизации или номера Steam, аккаунт отправится на модерацию, Вы сможете продолжать играть под старыми данными пока новые настройки будут обрабатываться. Сроки рассмотрения Вашей заявки ненормированы, но, как правило, составляют от нескольких минут до 24 часов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1315, 'moderate_change_nick', 85, 'If you change the type of authorization or nickname, your account will go to moderation, you can continue to play under the old data until the new settings will be handled. Timing of review of your application irregular, but usually range from a few minutes to 24 hours.', 'При смене типа авторизации или ника, аккаунт отправится на модерацию, Вы сможете продолжать играть под старыми данными пока новые настройки будут обрабатываться. Сроки рассмотрения Вашей заявки ненормированы, но, как правило, составляют от нескольких минут до 24 часов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1316, 'moderate_change_ip', 85, 'If you change the type of authorization or IP-address, your account will go to moderation, you can continue to play under the old data until the new settings will be handled. Timing of review of your application irregular, but usually range from a few minutes to 24 hours.', 'При смене типа авторизации или IP-адреса, аккаунт отправится на модерацию, Вы сможете продолжать играть под старыми данными пока новые настройки будут обрабатываться. Сроки рассмотрения Вашей заявки ненормированы, но, как правило, составляют от нескольких минут до 24 часов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1317, 'player_nick_password', 85, 'Your nick and password', 'Ваш ник и пароль', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1318, 'password_change_success', 85, 'Password was successfully changed!', 'Пароль успешно изменен!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1319, 'auth_steam', 85, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1320, 'auth_ip', 85, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1321, 'auth_nick', 85, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1322, 'save', 85, 'Save', 'Сохранить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1323, 'player_ip', 85, '<b>Your ip-address:</b>', 'Ваш ip-адрес', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1324, 'block_account', 85, 'Account has been locked by the administrator. The reason for the block you can find on the forum.', 'Аккаунт заблокирован администратором. О причине блокировки Вы можете узнать на форуме.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1325, 'ticket_on_moderate', 85, 'You have sent a request to change the account. While the ticket is in moderation you will be able to continue to play under the old data. If you make a mistake when you make a ticket or have decided not to change an account, you have the opportunity to withdraw the ticket by clicking on the link in the list of tickets.', 'Вы отправляли запрос на изменение аккаунта. Пока заявка находится на модерации Вы сможете продолжать играть под старыми данными. Если Вы допустили ошибку при оформлении заявки или передумали изменять аккаунт, то есть возможность отозвать заявку, нажав на соответствующую ссылку в списке заявок.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1326, 'ticket_reg_on_moderate', 85, 'You have sent a request to create a game account. The ticket is in moderation. Terms of moderation is not rationing, but usually is not more than 24 hours.', 'Вы отправляли запрос на создание игрового аккаунта. Заявка находится на модерации. Сроки модерирования не нормированны, но, как правило, составляют не более 24 часов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1327, 'user_email_confirm', 85, 'Your email address is not confirmed. Click on the link sent to you by e-mail to manage your game account.', 'Ваш адрес электронной почты ещё не подтвержден. Перейдите по ссылке, отправленной Вам на почту, чтобы управлять своим игровым аккаунтом.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1328, 'user_moderated', 85, 'Your account is in moderation. In this status you can not control their gaming account.', 'Ваша учетная запись находится на модерации. В данном статусе Вы не сможете управлять своим игровым аккаунтом.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1329, 'create_account', 86, 'Create account', 'Создать аккаунт', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1330, 'edit_account_info', 86, 'Edit account', 'Редактировать', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1331, 'not_account', 86, 'Account is not created. To create it using the form.', 'Аккаунт не создан. Для его создания заполните форму.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1332, 'auth_type_info', 86, '<ul><li><b>Authorization by Nick</b> - linking your account to your gaming nick. Log on to the server under his nickname will be possible only when you specify the correct password.</li><li><b>Authorization by IP</b> - linking your account to an IP-address. Ideal for players with a static IP.</li><li><b>Authorization by SteamID</b> - linking your account number to Steam. You can change nicknames, ip-address, but you will always be given to you, if SteamID coincides with the one you have registered.</li></ul>', '<ul><li><b>Авторизация по Нику</b> - привязка аккаунта к Вашему игровому нику. Вход на сервер под своим ником будет возможен только при указании верного пароля.</li><li><b>Авторизация по IP</b> - привязка аккаунта к IP-адресу. Идеально подходит для игроков имеющих статический IP.</li><li><b>Авторизация по SteamID</b> - привязка аккаунта к номеру Steam. Можете менять ники, ip-адреса, но доступ всегда будет Вам предоставлен, если SteamID совпадает с тем, который вы зарегистрируете.</li></ul>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1333, 'acc_online_all', 86, 'Total online:', 'Всего онлайн:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1334, 'acc_online', 86, 'Last online:', 'Последний онлайн:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1335, 'acc_created', 86, 'Created:', 'Создан:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1336, 'auth_type', 86, '<b>Authorization type:</b>', '<b>Тип авторизации:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1337, 'player_steam', 86, '<b>Your SteamID:</b>', '<b>Ваш SteamID:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1338, 'player_nick', 86, '<b>Your nick:</b>', '<b>Ваш ник:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1339, 'player_ip', 86, 'Your ip-address', '<b>Ваш ip-адрес:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1340, 'auth_steam', 86, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1341, 'auth_nick', 86, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1342, 'auth_ip', 86, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1343, 'block_account', 86, 'Account has been locked by the administrator. The reason for the block you can find on the forum.', 'Аккаунт заблокирован администратором. О причине блокировки Вы можете узнать на форуме.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1344, 'ticket_on_moderate', 86, 'You have sent a request to change the account. While the ticket is in moderation you will be able to continue to play under the old data. If you make a mistake when you make a ticket or have decided not to change an account, you have the opportunity to withdraw the ticket by clicking on the link in the list of tickets.', 'Вы отправляли запрос на изменение аккаунта. Пока заявка находится на модерации Вы сможете продолжать играть под старыми данными. Если Вы допустили ошибку при оформлении заявки или передумали изменять аккаунт, то есть возможность отозвать заявку, нажав на соответствующую ссылку в списке заявок.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1345, 'ticket_reg_on_moderate', 86, 'You have sent a request to create a game account. The ticket is in moderation. Terms of moderation is not rationing, but usually is not more than 24 hours.', 'Вы отправляли запрос на создание игрового аккаунта. Заявка находится на модерации. Сроки модерирования не нормированны, но, как правило, составляют не более 24 часов.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1346, 'user_email_confirm', 86, 'Your email address is not confirmed. Click on the link sent to you by e-mail to manage your game account.', 'Ваш адрес электронной почты ещё не подтвержден. Перейдите по ссылке, отправленной Вам на почту, чтобы управлять своим игровым аккаунтом.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1347, 'user_moderated', 86, 'Your account is in moderation. In this status you can not control their gaming account.', 'Ваша учетная запись находится на модерации. В данном статусе Вы не сможете управлять своим игровым аккаунтом.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1348, 'all_status', 87, 'All status', 'Все статусы', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1349, 'opened', 87, 'Opened', 'Открытые', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1350, 'approved', 87, 'Approved', 'Одобренные', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1351, 'head', 87, 'User Requests', 'Заявки Пользователей', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1352, 'empty_table', 87, 'Data table is empty!', 'Таблица с данными пуста!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1353, 'rejected', 87, 'Rejected', 'Отклоненные', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1354, 'apply', 88, 'Apply', 'Применить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1355, 'ticket_date', 88, 'Date', 'Дата', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1356, 'ticket_user', 88, 'User', 'Пользователь', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1357, 'ticket_action', 88, 'Action', 'Действие', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1358, 'delete', 88, 'Delete', 'Удалить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1359, 'moderated', 88, 'Ticket for moderation', 'Заявка требует модерации', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1360, 'approved', 88, 'Ticket approved', 'Заявка одобрена', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1361, 'disapproved', 88, 'Ticket disapproved', 'Заявка отклонена', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1362, 'confirm_del', 88, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1363, 'approve', 88, 'Approve', 'Одобрить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1364, 'disapprove', 88, 'Disapprove', 'Отклонить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1365, 'not_selected', 88, 'Entries not selected!', 'Записи не выбраны!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1366, 'ticket_reason', 88, 'tickets with a cause:', 'заявок с причиной:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1367, 'selected_incorrect', 88, 'Selected tickets are closed, and moderation is not possible!', 'Выбранные заявки уже закрыты и модерация не возможна!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1368, 'ticket_date', 89, '<b>Ticket date:</b>', '<b>Дата заявки:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1369, 'ticket_elapsed', 89, '<b>Time elapsed:</b>', '<b>Время рассмотрения:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1370, 'ticket_moderator', 89, '<b>Moderated by:</b>', '<b>Модерировал:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1371, 'ticket_comment', 89, '<b>Comment moderator:</b>', '<b>Комментарий модератора:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1372, 'ticket_user', 89, '<b>Created:</b>', '<b>Создал:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1373, 'ticket_closed', 89, 'The ticket is closed. Change of status can not be!', 'Заявка закрыта. Изменение статуса невозможно!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1374, 'not_ticket', 89, 'Ticket not found!', 'Заявка не найдена!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1375, 'ticket_flag', 89, '<b>Authorization type:</b>', '<b>Тип авторизации:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1376, 'by_steam', 89, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1377, 'by_ip', 89, 'by IP-address', 'по IP-адресу', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1378, 'by_nick', 89, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1379, 'flag_curent', 89, 'current type -', 'текущий тип -', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1380, 'ticket_flag_steam', 89, '<b>SteamID:</b>', '<b>SteamID:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1381, 'ticket_flag_ip', 89, '<b>IP-address:</b>', '<b>IP-адрес:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1382, 'ticket_flag_nick', 89, '<b>Nick:</b>', '<b>Ник:</b>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1383, 'curent_value', 89, 'current value -', 'текущее значение -', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1384, 'ticket_approved', 89, 'Approve', 'Одобрить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1385, 'ticket_disapproved', 89, 'Reject', 'Отклонить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1386, 'ticket_deleted', 89, 'Delete', 'Удалить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1387, 'question_delete_ticket', 89, 'Delete ticket?', 'Удалить заявку?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1388, 'ticket_moderator_comment', 89, 'Enter a comment:', 'Введите комментарий:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1389, 'user_hid_duplicate', 89, 'HID users is the same:', 'HID пользователей совпадает:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1390, 'find_possible_bans', 89, 'Find possible bans...', 'Поиск возможных банов...', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1391, 'coincidence_of_nick', 89, 'matches by Nck:', 'совпадения по Нику:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1392, 'coincidence_of_ip', 89, 'matches by IP:', 'совпадения по IP:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1393, 'coincidence_of_cookie', 89, 'matches by Cookie:', 'совпадения по Cookie:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1394, 'coincidence_of_steam', 89, 'matches by SteamID:', 'совпадения по SteamID:', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1395, 'accounts_all', 90, 'Registered', 'Зарегистрировано', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1396, 'accounts_by_ip', 90, '- by ip', '- по ip', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1397, 'accounts_by_nick', 90, '- by nick', '- по нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1398, 'accounts_by_steam', 90, '- by steamid', '- по steamid', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1399, 'accounts_active', 90, 'Active', 'Активных', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1400, 'all_servers', 91, 'All servers', 'Все сервера', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1401, 'not_mask', 91, 'No servers found for this access mask.', 'Серверы для указанной маски доступа не найдены.', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1402, 'auth_type_all', 92, 'All types of authorization', 'Все типы авторизации', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1403, 'auth_type_select', 92, 'Select types', 'Выбрать типы', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1404, 'auth_nick', 92, '>>> by Nick', '>>> по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1405, 'auth_ip', 92, '>>> by IP', '>>> по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1406, 'auth_steam', 92, '>>> by SteamID', '>>> по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1407, 'player_nick', 92, 'Player nick', 'Ник игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1408, 'player_ip', 92, 'Player IP', 'IP игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1409, 'player_steam', 92, 'Player SteamID', 'SteamID игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1410, 'access_server_all', 92, 'Access to any server', 'Доступ на любом сервере', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1411, 'access_server_select', 92, 'Select servers', 'Выбрать серверы', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1412, 'access_flags', 92, 'Access flags', 'Флаги доступа', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1413, 'all_masks', 92, 'All masks', 'Все маски', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1414, 'access_totime', 92, 'Access to', 'Доступ до', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1415, 'show_advanced_search_options', 92, 'SHOW ADVANCED OPTIONS', 'ПОКАЗАТЬ ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1416, 'hide_advanced_search_options', 92, 'HIDE ADVANCED OPTIONS', 'СКРЫТЬ ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1417, 'search', 92, 'Search', 'Поиск', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1418, 'delete', 92, 'Delete', 'Удалить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1419, 'last_visit_from', 92, 'Last visit from', 'Последний визит с', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1420, 'last_visit_totime', 92, 'Last visit to', 'Последний визит до', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1421, 'username', 92, 'User name', 'Имя пользователя', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1422, 'ga_search_result', 93, 'Search results account', 'Результаты поиска аккаунтов', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1423, 'new_search', 93, 'New search', 'Новый поиск', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1424, 'data_created', 94, 'Created', 'Создан', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1425, 'player_nick', 94, 'Player nick', 'Ник игрока', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1426, 'auth_type', 94, 'Authorization', 'Авторизация', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1427, 'last_game', 94, 'Last game', 'Последняя игра', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1428, 'auth_steam', 94, 'by SteamID', 'по SteamID', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1429, 'auth_ip', 94, 'by IP', 'по IP', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1430, 'auth_nick', 94, 'by Nick', 'по Нику', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1431, 'not_auth_type', 94, '-', '-', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1432, 'confirm_del', 94, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1433, 'apply', 94, 'Apply', 'Применить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1434, 'delete', 94, 'Delete', 'Удалить', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1435, 'active', 94, 'Activate', 'Активировать', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1436, 'inactive', 94, 'Deactivate', 'Деактивировать', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1437, 'click_change_status', 94, 'Click to change the account status', 'Нажмите, чтобы изменить статус аккаунта', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1438, 'not_selected', 94, 'Entries not selected!', 'Записи не выбраны!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1439, 'user_name', 94, 'User', 'Пользователь', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (1440, 'gb_banlist_stats', 0, 'Bans Statistics', 'Статистика Банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1441, 'gamebans', 0, 'Bans Gaming System', 'Игровая Система Банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1442, 'gamebans_players', 0, 'Bans Players', 'Баны Игроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1443, 'add_ban', 0, 'Add Ban', 'Добавить бан', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1444, 'gamebans_subnets', 0, 'Bans Subnets', 'Баны Подсетей', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1445, 'ban_edit', 0, 'Edit Ban', 'Редактирование Бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1446, 'ban_reasons', 0, 'Reasons For Bans', 'Причины Банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1447, 'bans_search', 0, 'Search Bans', 'Поиск Банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1448, 'add_ban_reason', 0, 'Add Reason', 'Добавление Причины', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1449, 'add_subnet', 0, 'Add Subnet', 'Добавление Подсети', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1450, 'edit_subnet', 0, 'Edit Subnet', 'Редактирование Подсети', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1451, 'gb_banlist', 0, 'Ban List', 'Список Банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1452, 'block_bans_stats', 0, 'Overall statistics', 'Общая статистика', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1453, 'block_bans_best_admin', 0, 'Best administrators today', 'Лучшие админы сегодня', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1454, 'bans_prune', 0, 'Moving game bans', 'Перемещение игровых банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1455, 'add_bans_stats_by_week', 3, 'in the last 7 days', 'за последние 7 дней', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1456, 'add_bans_stats_by_year', 3, 'in the last 12 months', 'за последние 12 месяцев', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1457, 'add_bans_stats', 3, 'Statistics added bans', 'Статистика добавленных банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1458, 'total_add_bans', 3, 'Number of bans', 'Количество банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1459, 'opt_bansubnets', 26, 'Activate ban subnets work?', 'Активировать работу банов подсети?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1460, 'total_bans', 35, 'Bans', 'Баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1461, 'help_gb_length_format', 36, 'If you leave the field blank, you will see a second without formatting. For all other cases:<ul><li>dddd, hhhh, mmmm, ssss - full text (eg. dddd hhhh mmmm ssss - 1 day 2 hours 3 minutes 10 seconds)</li><li>ddd, hhh, mmm, sss - with a short text (eg. ddd hhh mmm sss - 1d 2h 3m 10s)</li><li>dd, hh, mm, ss - without text support (eg. hh:mm:ss - 02:03:10)</li></ul>', 'Если оставить поле пустым, то будут отображены секунды без форматирования. Для всех других случаев:<ul><li>dddd, hhhh, mmmm, ssss - с полным текстом (например dddd hhhh mmmm ssss - 1 день 2 часа 3 минуты 10 секунд)</li><li>ddd, hhh, mmm, sss - с кратким текстом (например ddd hhh mmm sss - 1д 2ч 3м 10с)</li><li>dd, hh, mm, ss - без текстового сопровождения (например hh:mm:ss -  02:03:10)</li></ul>', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1462, 'gb_length_format', 36, 'Time format', 'Формат времени', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1463, 'gb_view_per_page', 36, 'The number of bans per page', 'Количество банов на страницу', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1464, 'help_gb_view_per_page', 36, 'How many bans to display pagination.', 'Какое количество банов отображать для постраничной навигации.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1465, 'gb_bans_all', 36, 'All bans', 'Все баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1466, 'gb_bans_active', 36, 'Active only', 'Только активные', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1467, 'gb_bans_passed', 36, 'Passed only', 'Только прошедшие', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1468, 'gb_bans_select', 36, 'What bans are displayed in the public list?', 'Какие баны отображать в общем доступе?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1469, 'help_gb_bans_select', 36, 'Select whether you ban players for the users.', 'Выбор режима отображения банов для пользователей.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1470, 'gb_display_admin', 36, 'Hide admin for users?', 'Скрыть админа для пользователей?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1471, 'help_gb_display_admin', 36, 'If you select \"Yes\", then in the general ban list admin nick will not be displayed.', 'Если выбрано \"Да\", то в общем списке банов ник админа не будет отображаться.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1472, 'gb_cache_block_stats', 36, 'Time block caching statistics', 'Время кеширования блока статистики', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1473, 'help_gb_cache_block_stats', 36, 'The value in seconds. Set to \"0\" if you do not want to cache the short side of the block statistics bans.', 'Значение в секундах. Установите \"0\", если не хотите кешировать боковой блок краткой статистики по банам.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1474, 'gb_topstats_max', 36, 'Number of results in the top statistics', 'Количество результатов в ТОП статистике', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1475, 'help_gb_topstats_max', 36, 'Specify how many positions need to display the TOP in general.', 'Укажите какое количество позиций необходимо выводить в общем ТОПе.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1476, 'gb_topstats_cache', 36, 'Caching time of general TOP', 'Время кеширования общего ТОПа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1477, 'help_gb_topstats_cache', 36, 'Time in minutes to update the cache for a general TOP. Enter \"0\" if you want to disable caching.', 'Время в минутах до обновления кеша для общего ТОПа. Укажите \"0\", если желаете отключить кеширование.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1478, 'gb_block_admins_max', 36, 'Number of the best admins', 'Количество лучших админов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1479, 'help_gb_block_admins_max', 36, 'Specify the maximum number of the best admins today that will be displayed in a side block.', 'Укажите максимальное количество лучших админов за сегодня, которое будет отображаться в боковом блоке.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1480, 'help_game_bans', 61, 'Game bans', 'Игровые баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1481, 'help_gb_perm_players', 61, 'Manage bans players', 'Управление банами игроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1482, 'help_gb_perm_players_my', 61, 'Manage only their bans', 'Управление только своими банами', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1483, 'help_gb_perm_reasons', 61, 'Manage reasons bans', 'Управление причинами банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1484, 'help_gb_perm_subnets', 61, 'Manage subnet bans', 'Управление банами подсетей', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1485, 'help_gb_perm_players', 62, 'Manage bans players', 'Управление банами игроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1486, 'help_gb_perm_players_my', 62, 'Manage only their bans', 'Управление только своими банами', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1487, 'help_game_bans', 62, 'Game bans', 'Игровые баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1488, 'help_gb_perm_reasons', 62, 'Manage reasons bans', 'Управление причинами банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1489, 'help_gb_perm_subnets', 62, 'Manage subnet bans', 'Управление банами подсетей', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1490, 'ban_removed', 95, ' (removed)', ' (снят)', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1491, 'ban_expired', 95, ' (expired)', ' (истек)', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1492, 'permanent', 95, '- permanent -', '- перманентный -', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1493, 'del_success', 95, 'Ban has been successfully deleted!', 'Бан успешно удален!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1494, 'del_failed', 95, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1495, 'del_multiply_success', 95, 'Successfully removed bans:', 'Успешно удалены баны:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1496, 'empty_field', 95, 'Field \'%s\' must not be empty', 'Поле \'%s\' не должно быть пустым', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1497, 'ip_not_valid', 95, 'IP address is not valid', 'IP адрес указан неверно', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1498, 'serverip_not_valid', 95, 'Server IP address is not valid', 'IP адрес сервера указан неверно', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1499, 'values_error', 95, 'Errors in editing', 'Ошибки при редактировании', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1500, 'edit_error', 95, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1501, 'edit_success', 95, 'The ban successfully edited!', 'Бан успешно отредактирован!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1502, 'empty_array', 95, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1503, 'add_double_error', 95, 'This ban already exists!', 'Подобный бан уже существует!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1504, 'add_failed', 95, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1505, 'add_success', 95, 'Ban has been successfully created!', 'Бан успешно добавлен!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1506, 'del_reason_success', 95, 'Reason has been successfully deleted!', 'Причина успешно удалена!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1507, 'del_reason_multiply_success', 95, 'Successfully removed reasons:', 'Успешно удалены причины:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1508, 'edit_reason_success', 95, 'The reason successfully edited!', 'Причина успешно отредактирована!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1509, 'edit_empty_field', 95, 'All fields must not be empty!', 'Все поля должны быть заполнены!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1510, 'add_reason_success', 95, 'The reason for the ban was successfully added!', 'Причина бана успешно добавлена!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1511, 'del_subnet_success', 95, 'Subnet has been successfully deleted!', 'Подсеть успешно удалена!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1512, 'del_subnet_multiply_success', 95, 'Successfully removed subnets:', 'Успешно удалены подсети:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1513, 'add_subnet_success', 95, 'Subnet has been successfully added!', 'Подсеть успешно добавлена!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1514, 'edit_subnet_success', 95, 'The subnet successfully edited!', 'Подсеть успешно отредактирована!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1515, 'change_status_success', 95, 'Subnet status successfully changed!', 'Статус подсети успешно изменен!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1516, 'change_status_error', 95, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1517, 'ban_website', 95, 'BANNED FROM SITE', 'БАН С САЙТА', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1518, 'bans_server_name', 95, 'Server', 'Сервер', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1519, 'bans_reason', 95, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1520, 'bans_length', 95, 'Length', 'Длительность', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1521, 'bans_subnet', 95, 'Subnet', 'Подсеть', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1522, 'bans_admin', 95, 'Admin', 'Админ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1523, 'ban_permanently', 95, 'Permanent', 'Перманентный', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1524, 'bans_country', 95, 'Country', 'Страна', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1525, 'bans_subnets_all', 95, 'Bans subnets', 'Банов подсетей', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1526, 'bans_players_all', 95, 'Bans players', 'Банов игроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1527, 'bans_by_nick', 95, '- by Nick', '- по Нику', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1528, 'bans_by_ip', 95, '- by ip', '- по IP', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1529, 'bans_by_steam', 95, '- by SteamID', '- по SteamID', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1530, 'not_bans', 95, 'Bans not found!', 'Баны в базе данных отсутствуют!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1531, 'head', 96, 'Bans players', 'Баны игроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1532, 'all_bans', 96, 'All bans', 'Все баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1533, 'bans_active', 96, 'Active bans', 'Активные баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1534, 'bans_passed', 96, 'Passed bans', 'Прошедшие баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1535, 'empty_table', 96, 'Data table is empty!', 'Таблица с данными пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1536, 'ban_created', 97, 'Date', 'Дата', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1537, 'ban_player_name', 97, 'Player nick', 'Ник игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1538, 'ban_player_reason', 97, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1539, 'ban_player_time', 97, 'Length', 'Длительность', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1540, 'del_selected', 97, 'Remove selected', 'Удалить записи', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1541, 'not_selected', 97, 'Entries not selected!', 'Записи не выбраны!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1542, 'ban_player_admin', 97, 'Admin', 'Админ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1543, 'ban_active', 97, 'Ban active', 'Бан активен', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1544, 'ban_passed', 97, 'Ban passed', 'Бан истек или снят', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1545, 'confirm_del', 97, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1546, 'ban_history', 98, 'Ban in history', 'Бан в истории', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1547, 'ban_player_nick', 98, 'Player nick', 'Ник игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1548, 'ban_type', 98, 'Ban type', 'Тип бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1549, 'ban_by_nick', 98, 'ban by NICK', 'бан по НИКУ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1550, 'ban_by_ip', 98, 'ban by IP', 'бан по IP', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1551, 'ban_by_steam', 98, 'ban by STEAM', 'бан по STEAM', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1552, 'ban_player_ip', 98, 'Player IP', 'IP игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1553, 'ban_cookie_ip', 98, 'Player cookie IP', 'Cookie IP игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1554, 'ban_player_steam', 98, 'Player SteamID', 'SteamID игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1555, 'ban_created', 98, 'Ban created', 'Дата добавления бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1556, 'ban_length', 98, 'Length minutes', 'Продолжительность минут', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1557, 'ban_reason', 98, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1558, 'save', 98, 'Save', 'Сохранить', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1559, 'ban_admin_nick', 98, 'Admin nick', 'Ник админа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1560, 'ban_admin_ip', 98, 'Admin IP', 'IP админа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1561, 'ban_admin_id', 98, 'Admin SteamID', 'SteamID админа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1562, 'ban_server', 98, 'Server IP', 'IP сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1563, 'ban_server_name', 98, 'Server name', 'Название сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1564, 'go_to_profile', 98, 'Go to profile', 'Перейти в профиль', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1565, 'ban_active', 98, 'Ban on active', 'Бан в активных', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1566, 'ban_passed', 98, 'Ban in the history', 'Бан в истории', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1567, 'ban_expired', 98, 'Ban expired', 'Бан истек', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1568, 'ban_remain', 98, 'Remaining: ', 'Осталось: ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1569, 'ban_permanently', 98, 'Permanent', 'Перманентный', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1570, 'unban_reason', 98, 'Unban reason', 'Причина разбана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1571, 'ban_player_nick', 99, 'Player nick', 'Ник игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1572, 'ban_type', 99, 'Ban type', 'Тип бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1573, 'ban_by_nick', 99, 'ban by NICK', 'бан по НИКУ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1574, 'ban_by_ip', 99, 'ban by IP', 'бан по IP', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1575, 'ban_by_steam', 99, 'ban by STEAM', 'бан по STEAM', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1576, 'ban_player_ip', 99, 'Player IP', 'IP игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1577, 'ban_cookie_ip', 99, 'Player cookie IP', 'Cookie IP игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1578, 'ban_player_steam', 99, 'Player SteamID', 'SteamID игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1579, 'ban_length', 99, 'Length minutes', 'Продолжительность минут', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1580, 'ban_reason', 99, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1581, 'save', 99, 'Save', 'Сохранить', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1582, 'add_reason', 100, 'Add reason', 'Добавить причину', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1583, 'all_reasons', 100, 'All servers', 'Все сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1584, 'empty_table', 100, 'Data table is empty!', 'Таблица с данными пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1585, 'reason_server', 101, 'Server', 'Сервер', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1586, 'reason_text', 101, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1587, 'del_selected', 101, 'Remove selected', 'Удалить записи', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1588, 'not_selected', 101, 'Entries not selected!', 'Записи не выбраны!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1589, 'confirm_del', 101, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1590, 'ban_server', 102, 'Server IP address', 'IP адрес сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1591, 'ban_reason', 102, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1592, 'save', 102, 'Save', 'Сохранить', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1593, 'add_ban_subnet', 103, 'Add subnet', 'Добавить подсеть', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1594, 'all_status', 103, 'All status', 'Все статусы', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1595, 'status_active', 103, 'Active bans', 'Активные баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1596, 'status_inactive', 103, 'Inactive bans', 'Неактивные баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1597, 'empty_table', 103, 'Data table is empty!', 'Таблица с данными пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1598, 'subnet_mask', 104, 'Mask', 'Маска', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1599, 'subnet_comment', 104, 'Comment', 'Комментарий', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1600, 'del_selected', 104, 'Remove selected', 'Удалить записи', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1601, 'not_selected', 104, 'Entries not selected!', 'Записи не выбраны!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1602, 'confirm_del', 104, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1603, 'click_change_status', 104, 'Click to change the subnet status', 'Нажмите, чтобы изменить статус подсети', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1604, 'add_ban_subnet', 105, 'Add ban subnet', 'Добавить бан подсети', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1605, 'subnet_mask', 105, 'Mask', 'Маска', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1606, 'subnet_comment', 105, 'Comment', 'Комментарий', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1607, 'subnet_active', 105, 'Subnet ban active?', 'Бан подсети активен?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1608, 'yes', 105, 'Yes', 'Да', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1609, 'no', 105, 'No', 'Нет', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1610, 'save', 105, 'Save', 'Сохранить', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1611, 'ip_range', 105, 'IP range:', 'IP диапазон:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1612, 'ip_count', 105, 'Number of IP:', 'Количество IP:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1613, 'subnet_edit', 106, 'Edit subnet', 'Редактирование подсети', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1614, 'subnet_mask', 106, 'Mask', 'Маска', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1615, 'subnet_comment', 106, 'Comment', 'Комментарий', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1616, 'subnet_active', 106, 'Subnet ban active?', 'Бан подсети активен?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1617, 'yes', 106, 'Yes', 'Да', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1618, 'no', 106, 'No', 'Нет', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1619, 'save', 106, 'Save', 'Сохранить', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1620, 'ip_range', 106, 'IP range:', 'IP диапазон:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1621, 'ip_count', 106, 'Number of IP:', 'Количество IP:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1622, 'search_everywhere', 107, 'Search all', 'Искать везде', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1623, 'search_in_active', 107, 'Search in active', 'Искать в активных', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1624, 'search_in_history', 107, 'Search in history', 'Искать в истории', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1625, 'ban_type_all', 107, 'All types of ban', 'Все типы бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1626, 'ban_type_select', 107, 'Select types', 'Выбрать типы', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1627, 'ban_by_nick', 107, '>>> bans by NICK', '>>> баны по НИКУ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1628, 'ban_by_ip', 107, '>>> bans by IP', '>>> баны по IP', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1629, 'ban_by_steam', 107, '>>> bans by STEAM', '>>> баны по STEAM', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1630, 'ban_player_nick', 107, 'Player nick', 'Ник игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1631, 'ban_player_ip', 107, 'Player IP', 'IP игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1632, 'ban_cookie_ip', 107, 'Player cookie IP', 'Cookie IP игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1633, 'ban_player_steam', 107, 'Player SteamID', 'SteamID игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1634, 'search', 107, 'Search', 'Поиск', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1635, 'delete', 107, 'Delete', 'Удалить', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1636, 'show_advanced_search_options', 107, 'SHOW ADVANCED OPTIONS', 'ПОКАЗАТЬ ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1637, 'hide_advanced_search_options', 107, 'HIDE ADVANCED OPTIONS', 'СКРЫТЬ ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1638, 'ban_server_all', 107, 'All servers', 'Все сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1639, 'ban_server_select', 107, 'Servers select', 'Выбрать сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1640, 'ban_website', 107, 'WEBSITE', 'БАН С САЙТА', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1641, 'ban_reason', 107, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1642, 'ban_admin_nick', 107, 'Admin nick', 'Ник админа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1643, 'ban_admin_ip', 107, 'Admin IP', 'IP админа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1644, 'ban_admin_id', 107, 'Admin SteamID', 'SteamID админа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1645, 'tbl_srv_error', 107, 'Table servers missing!', 'Таблица с серверами отсутствует!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1646, 'tbl_srv_empty', 107, 'Table servers is empty!', 'Таблица с серверами пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1647, 'ban_length_interval', 107, 'The interval length in minutes', 'Интервал срока в минутах', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1648, 'bans_search_result', 108, 'Search results bans', 'Результаты поиска банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1649, 'new_search', 108, 'New search', 'Новый поиск', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1650, 'ban_created', 109, 'Date', 'Дата', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1651, 'ban_player_name', 109, 'Player nick', 'Ник игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1652, 'ban_player_reason', 109, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1653, 'ban_player_time', 109, 'Length', 'Длительность', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1654, 'del_selected', 109, 'Remove selected', 'Удалить записи', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1655, 'not_selected', 109, 'Entries not selected!', 'Записи не выбраны!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1656, 'confirm_del', 109, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1657, 'ban_player_admin', 109, 'Admin', 'Админ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1658, 'ban_passed', 109, 'Ban active', 'Бан активен', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1659, 'ban_active', 109, 'Ban active', 'Бан активен', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1660, 'head', 110, 'Bans players', 'Баны игроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1661, 'all_bans', 110, 'All bans', 'Все баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1662, 'bans_active', 110, 'Bans active', 'Активные баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1663, 'bans_passed', 110, 'Bans passed', 'Прошедшие баны', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1664, 'empty_table', 110, 'Data table is empty!', 'Таблица с данными пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1665, 'your_ip', 110, 'Your IP:', 'Ваш IP:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1666, 'ip_banned', 110, 'banned!', 'забанен!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1667, 'ip_not_banned', 110, 'not in the list of current bans!', 'отсутствует в списке текущих банов!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1668, 'your_subnet', 110, 'Your subnet', 'Ваша подсеть', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1669, 'subnet_banned_post', 110, 'banned! To play on our servers to <a href=\"?do=profile&s=2\" rel=\"nofollow\">create a game account</a>.', 'забанена! Для игры на наших серверах необходимо <a href=\"?do=profile&s=2\" rel=\"nofollow\">создать игровой аккаунт</a>.', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1670, 'your_subnet_not_banned', 110, 'Your subnet NOT banned!', 'Ваша подсеть НЕ забанена!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1671, 'search', 110, 'Search', 'Поиск', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1672, 'search_nick', 110, 'Nick', 'Ник', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1673, 'search_ip', 110, 'IP address', 'IP адрес', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1674, 'search_steam', 110, 'SteamID', 'SteamID', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1675, 'bans_search_nick', 110, 'by nick', 'по нику', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1676, 'bans_search_ip', 110, 'by IP', 'по IP', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1677, 'bans_search_id', 110, 'by SteamID', 'по SteamID', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1678, 'bans_not_found', 110, 'As a result of the search bans not found!', 'В результате поиска баны не найдены!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1679, 'bans_found', 110, 'A search found bans:', 'В результате поиска найдено банов:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1680, 'bans_search_admin', 110, 'by admin', 'по админу', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1681, 'bans_search_server', 110, 'by server', 'по серверу', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1682, 'search_bans_options_switch_off', 110, 'Show advanced options', 'Показать дополнительные настройки', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1683, 'search_bans_options_switch_on', 110, 'Hide advanced options', 'Скрыть дополнительные настройки', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1684, 'all_servers', 110, 'All servers', 'Все сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1685, 'all_admins', 110, 'All admins', 'Все админы', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1686, 'ban_created', 111, 'Date', 'Дата', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1687, 'ban_player_name', 111, 'Player nick', 'Ник игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1688, 'ban_player_reason', 111, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1689, 'ban_player_time', 111, 'Length', 'Длительность', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1690, 'ban_player_admin', 111, 'Admin', 'Админ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1691, 'ban_detail', 111, 'Ban details', 'Подробности бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1692, 'ban_detail', 112, 'Ban details', 'Подробности бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1693, 'ban_player_nick', 112, 'Player nick', 'Ник игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1694, 'ban_type', 112, 'Ban type', 'Тип бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1695, 'ban_by_nick', 112, 'ban by NICK', 'бан по НИКУ', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1696, 'ban_by_ip', 112, 'ban by IP', 'бан по IP', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1697, 'ban_by_steam', 112, 'ban by STEAM', 'бан по STEAM', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1698, 'ban_player_steam', 112, 'Player SteamID', 'SteamID игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1699, 'ban_created', 112, 'Ban created', 'Дата добавления бана', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1700, 'ban_length', 112, 'Length', 'Продолжительность', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1701, 'ban_reason', 112, 'Reason', 'Причина', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1702, 'ban_admin_nick', 112, 'Admin nick', 'Ник админа', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1703, 'ban_server', 112, 'Server name', 'Название сервера', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1704, 'ban_player_ip', 112, 'Player IP', 'IP игрока', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1705, 'permanent', 112, 'permanent', 'перманентный', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1706, 'ban_removed', 112, ' (removed)', ' (снят)', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1707, 'ban_expired', 112, ' (expired)', ' (истек)', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1708, 'bans_subnets', 113, 'Subnets banned', 'Забанено подсетей', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1709, 'bans_players', 113, 'Players banned', 'Забанено игроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1710, 'bans_by_ip', 113, '- by ip', '- по ip', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1711, 'bans_by_steam', 113, '- by steamid', '- по steamid', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1712, 'bans_by_nick', 113, '- by nickname', '- по нику', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1713, 'bans_permanent', 113, 'Permanent bans', 'Постоянных банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1714, 'bans_timed', 113, 'Temporary bans', 'Временных банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1715, 'subnet_mask', 114, 'Mask', 'Маска', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1716, 'subnet_comment', 114, 'Comment', 'Комментарий', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1717, 'empty_table', 115, 'Data table is empty!', 'Таблица с данными пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1718, 'bans_stats_top', 116, 'Overall TOP', 'Общий ТОП', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1719, 'bans_stats_server', 116, 'Statistics servers', 'Статистика серверов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1720, 'bans_stats_reason', 116, 'Statistics reasons', 'Статистика причин', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1721, 'bans_stats_length', 116, 'Statistics lengths', 'Статистика сроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1722, 'bans_stats_subnet', 116, 'Statistics subnets', 'Статистика подсетей', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1723, 'bans_stats_admin', 116, 'Statistics admins', 'Статистика админов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1724, 'bans_stats_country', 116, 'Statistics countries', 'Статистика стран', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1725, 'empty_table', 116, 'Data table is empty!', 'Таблица с данными пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1726, 'count_bans', 117, 'Bans quantity', 'Количество банов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1727, 'bans_topstats_server', 118, 'TOP servers', 'ТОП серверов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1728, 'bans_topstats_reason', 118, 'TOP reasons', 'ТОП причин', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1729, 'bans_topstats_length', 118, 'TOP lengths', 'ТОП сроков', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1730, 'bans_topstats_subnet', 118, 'TOP subnets', 'ТОП подсетей', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1731, 'bans_topstats_country', 118, 'TOP countries', 'ТОП стран', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1732, 'bans_topstats_admin', 118, 'TOP admins', 'ТОП админов', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1733, 'view_all', 118, 'view all', 'посмотреть все', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1734, 'empty_table', 118, 'Data table is empty!', 'Таблица с данными пуста!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1735, 'bans_today', 119, 'Bans today', 'Банов за сегодня', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1736, 'not_best_admins_today', 119, 'Bans today was not yet!', 'Банов сегодня ещё не было!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1737, 'bans_prune_error', 120, 'An unexpected error occurred during the move. Repeat later!', 'Возникла непредвиденная ошибка при перемещении. Повторите позже!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1738, 'bans_prune_not_find', 120, 'No bans found to move!', 'Не найдены баны для перемещения!', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1739, 'bans_prune_success', 120, 'Successfully moved bans:', 'Удачно перемещено банов:', 'gameBans');
INSERT INTO `acp_lang_words` VALUES (1740, 'gamechat', 0, 'Game Chat', 'Игровой Чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1741, 'chat_control', 0, 'Chat Control', 'Контроль Чата', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1742, 'chat_patterns', 0, 'Patterns Dictionaries', 'Словари Проверки', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1743, 'chat_commands', 0, 'Client Commands', 'Клиентские Команды', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1744, 'chat_logs', 0, 'Chat Logs', 'Логи Чата', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1745, 'chat_add_command', 0, 'Add Command', 'Добавить Команду', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1746, 'chat_add_pattern', 0, 'Add Pattern', 'Добавить Шаблон', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1747, 'cc_refresh', 36, 'Automatic refresh chat', 'Автоматическое обновление страницы чата', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1748, 'help_cc_refresh', 36, 'After this many seconds will be automatically updated with the chat. Set to \"0\" to disable automatic updates.', 'Через это количество секунд будет производиться автоматическое обновление страницы с чатом. Установите \"0\", чтобы отключить автоматическое обновление.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1749, 'say', 36, 'General chat', 'Общий чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1750, 'say_team', 36, 'Team chat', 'Командный чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1751, 'amx_chat', 36, 'Admins chat', 'Админский чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1752, 'cc_block_msg', 36, 'Show blocked messages?', 'Показывать заблокированные сообщения?', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1753, 'help_cc_block_msg', 36, 'Enable/disable the display system of blocked messages.', 'Включение/отключение отображения заблокированных системой сообщений.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1754, 'help_cc_limit', 36, 'If you want to display only a certain number of messages, then set the threshold here. Or, enter \"0\" if you want to display all.', 'Если Вам нужно отображать только определенное количество сообщений, то задайте здесь этот порог. Или укажите \"0\", если нужно отображать все.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1755, 'help_cc_delay', 36, 'Specifying a time delay avoids monitoring chat players. Specify the number of minutes in which to display.', 'Указание времени задержки позволяет исключить мониторинг чата игроков. Укажите количество минут, через которое будут отображаться сообщения.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1756, 'help_cc_servers', 36, 'Choose from any server will run an instant message.', 'Выберите с каких серверов будут показываться сообщения чата.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1757, 'help_cc_alive', 36, 'Enable/disable display messages with real live players.', 'Включение/отключение показа сообщений живых игроков.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1758, 'help_cc_foradmins', 36, 'Enable/disable display of messages intended for administrators. This applies to messages sent through the \"@\".', 'Включение/отключение отображения сообщений предназначенных для админов. Это относится к сообщениям отправленным через \"@\".', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1759, 'help_cc_cmd', 36, 'Enable/disable display of messages from the team chat.', 'Включение/отключение показа сообщений из командного чата.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1760, 'cc_limit', 36, 'The number of messages for viewing', 'Количество сообщений для просмотра', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1761, 'cc_delay', 36, 'Delay instant messages (minutes)', 'Задержка сообщений чата (минуты)', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1762, 'cc_servers', 36, 'Chat servers which display?', 'Чат каких серверов отображать?', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1763, 'cc_alive', 36, 'Show live players chat?', 'Показывать чат живых игроков?', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1764, 'cc_foradmins', 36, 'Show messages for admins?', 'Показывать сообщения для админов?', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1765, 'cc_cmd', 36, 'Show chat', 'Показывать чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1766, 'help_chat_control', 61, 'Chat control', 'Контроль чата', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1767, 'help_cc_perm_patterns', 61, 'Manage patterns', 'Управление шаблонами проверки', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1768, 'help_cc_perm_commands', 61, 'Manage commands', 'Управление клиентскими командами', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1769, 'help_cc_perm_patterns', 62, 'Manage patterns', 'Управление шаблонами проверки', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1770, 'help_cc_perm_commands', 62, 'Manage commands', 'Управление клиентскими командами', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1771, 'help_chat_control', 62, 'Chat control', 'Контроль чата', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1772, 'head', 121, 'Client commands', 'Клиентские команды', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1773, 'empty_table', 121, 'Data table is empty!', 'Таблица с данными пуста!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1774, 'add_command', 121, 'Add command', 'Добавить команду', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1775, 'dont_empty', 121, 'Field must not be empty!', 'Поле не должно быть пустым!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1776, 'del_success', 121, 'The command successfully removed!', 'Команда успешно удалена!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1777, 'del_failed', 121, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1778, 'add_try', 121, 'This command already exists in the database!', 'Данная команда уже существует в базе!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1779, 'add_success', 121, 'The command successfully added!', 'Команда успешно добавлена!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1780, 'add_failed', 121, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1781, 'del_multiply_success', 121, 'Successfully removed the commands:', 'Успешно удалено команд:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1782, 'edit_empty_field', 121, 'Error when editing: do not put a command!', 'Ошибка при редактировании: не введена команда!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1783, 'edit_try', 121, 'Error when editing: Command already exists!', 'Ошибка при редактировании: команда уже существует!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1784, 'edit_error', 121, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1785, 'edit_success', 121, 'The command successfully edited!', 'Команда успешно отредактирована!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1786, 'command', 122, 'Command', 'Команда', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1787, 'confirm_del', 122, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1788, 'del_selected', 122, 'Remove selected', 'Удалить записи', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1789, 'not_selected', 122, 'Entries not selected!', 'Записи не выбраны!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1790, 'add_command', 123, 'Add command', 'Добавление команды', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1791, 'add_success', 123, 'The command successfully added!', 'Команда успешно добавлена!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1792, 'command', 123, 'Command:', 'Команда:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1793, 'add', 123, 'Add', 'Добавить', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1794, 'empty_table', 124, 'Table with templates dictionary is empty!', 'Таблица с шаблонами словаря пуста!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1795, 'add_pattern', 124, 'Add pattern', 'Добавить шаблон', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1796, 'head', 124, 'Patterns dictionaries', 'Словари проверки', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1797, 'dont_empty', 124, 'Field must not be empty!', 'Поле не должно быть пустым!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1798, 'add_try', 124, 'This pattern already exists in the database!', 'Данный шаблон уже существует в базе!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1799, 'add_success', 124, 'The pattern successfully added!', 'Шаблон успешно добавлен!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1800, 'add_failed', 124, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1801, 'del_success', 124, 'The pattern successfully removed!', 'Шаблон успешно удален!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1802, 'del_failed', 124, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1803, 'del_multiply_success', 124, 'Successfully removed the patterns:', 'Успешно удалено шаблонов:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1804, 'edit_empty_field', 124, 'Error when editing: do not put a pattern!', 'Ошибка при редактировании: не задан шаблон!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1805, 'edit_error', 124, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1806, 'edit_success', 124, 'The pattern successfully edited!', 'Шаблон успешно отредактирован!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1807, 'move_success', 124, 'Successfully moved the patterns:', 'Успешно перемещено шаблонов:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1808, 'move_error', 124, 'Error when moving. Try again later!', 'Произошла ошибка при перемещении. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1809, 'confirm_del', 125, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1810, 'pattern', 125, 'Pattern', 'Шаблон', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1811, 'reason', 125, 'Reason', 'Причина', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1812, 'time', 125, 'Duration', 'Время', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1813, 'delete', 125, 'Delete', 'Удалить', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1814, 'move', 125, 'Move', 'Переместить', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1815, 'apply', 125, 'Apply', 'Применить', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1816, 'not_selected', 125, 'Entries not selected!', 'Записи не выбраны!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1817, 'add_success', 126, 'The pattern successfully added!', 'Шаблон успешно добавлен!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1818, 'add_pattern', 126, 'Add pattern', 'Добавление шаблона', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1819, 'pattern', 126, 'Pattern:', 'Шаблон:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1820, 'reason', 126, 'Reason:', 'Причина:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1821, 'time', 126, 'Duration:', 'Время:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1822, 'add', 126, 'Add', 'Добавить', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1823, 'dict', 126, 'Dictionary:', 'Словарь:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1824, 'tbl_srv_error', 127, 'Table servers missing!', 'Таблица с серверами отсутствует!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1825, 'tbl_srv_empty', 127, 'Table servers is empty!', 'Таблица с серверами пуста!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1826, 'head', 127, 'Chat logs', 'Логи чата', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1827, 'server', 127, 'Server:', 'Сервер:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1828, 'all_servers', 127, 'All servers', 'Все сервера', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1829, 'msg_type', 127, 'Message type:', 'Тип сообщения:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1830, 'all_messages', 127, 'All messages', 'Все сообщения', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1831, 'chat_public', 127, 'General chat', 'Общий чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1832, 'chat_team', 127, 'Team chat', 'Командный чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1833, 'chat_admin', 127, 'Admin chat', 'Админский чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1834, 'player_nick', 127, 'Player Nick:', 'Ник игрока:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1835, 'player_steam', 127, 'Player SteamID:', 'SteamID игрока:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1836, 'player_ip', 127, 'Player IP:', 'IP игрока:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1837, 'keyword', 127, 'Keyword or phrase:', 'Ключевое слово или фраза:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1838, 'search', 127, 'Search', 'Найти', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1839, 'delete', 127, 'Delete', 'Удалить', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1840, 'del_success', 127, 'Successfully deleted messages in the logs:', 'Успешно удалено сообщений в логах:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1841, 'del_null', 127, 'At your request, the logs are not found. Try to specify other search options.', 'По вашему запросу логи не найдены. Попробуйте указать другие настройки поиска.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1842, 'del_error', 127, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1843, 'msg_status', 127, 'Message status:', 'Статус сообщения:', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1844, 'msg_free', 127, 'Allowed messages', 'Разрешенные сообщения', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1845, 'msg_white', 127, 'Pattern: White-List', 'Шаблон: White-List', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1846, 'msg_hide', 127, 'Pattern: Hide-List', 'Шаблон: Hide-List', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1847, 'msg_ban', 127, 'Pattern: Ban-List', 'Шаблон: Ban-List', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1848, 'msg_kick', 127, 'Pattern: Kick-List', 'Шаблон: Kick-List', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1849, 'msg_notice', 127, 'Pattern: Notice-List', 'Шаблон: Notice-List', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1850, 'search_empty', 128, 'Sorry, nothing found. Try to specify other search options.', 'Извините, ничего не найдено. Попробуйте указать другие настройки поиска.', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1851, 'head', 128, 'Chat logs', 'Логи чата', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1852, 'new_search', 128, 'New search', 'Новый поиск', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1853, 'time', 129, 'Time', 'Время', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1854, 'message', 129, 'Message', 'Сообщение', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1855, 'info', 129, 'Info', 'Инфо', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1856, 'info_more', 129, 'More info', 'Дополнительная информация', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1857, 'server_ip', 129, 'Server IP', 'IP сервера', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1858, 'player_ip', 129, 'Player IP', 'IP игрока', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1859, 'player_steam', 129, 'Player SteamID', 'SteamID игрока', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1860, 'pattern', 129, 'Pattern', 'Шаблон', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1861, 'head', 130, 'Game chat', 'Игровой чат', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1862, 'refreshing', 130, 'Refreshing', 'Обновление', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1863, 'chat_disabled', 130, 'Displaying instant messages disabled!', 'Отображение сообщений чата отключено!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1864, 'to_refresh', 130, 'seconds to refresh', 'секунд до обновления', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1865, 'refresh', 130, 'Refresh', 'Обновить', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1866, 'empty_table', 130, 'Data table is empty!', 'Таблица с данными пуста!', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1867, 'all_servers', 130, 'All servers', 'Все сервера', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1868, 'time', 131, 'Time', 'Время', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1869, 'message', 131, 'Message', 'Сообщение', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1870, 'serverip', 131, 'Server', 'Сервер', 'chatControl');
INSERT INTO `acp_lang_words` VALUES (1871, 'hud_manager', 0, 'HUD Manager', 'HUD Менеджер', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1872, 'hud_add_pattern', 0, 'Adding menu item', 'Добавление пункта меню', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1873, 'opt_hudmanager', 26, 'Enable HUD-manager?', 'Включить HUD-менеджер?', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1874, 'help_perm_hudm', 61, 'Manage settings', 'Управление настройками', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1875, 'help_hudm', 61, 'HUD control', 'Котроль HUD-а игрока', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1876, 'help_perm_hudm', 62, 'Manage settings', 'Управление настройками', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1877, 'help_hudm', 62, 'HUD control', 'Котроль HUD-а игрока', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1878, 'add_pattern', 132, 'Add value', 'Добавить значение', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1879, 'dont_empty', 132, 'The field with the name must not be empty!', 'Поле с названием не должно быть пустым!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1880, 'add_failed', 132, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1881, 'add_success', 132, 'The value successfully added!', 'Значение успешно добавлено!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1882, 'hm_flags_empty', 132, 'Must be selected at least one flag!', 'Должен быть выбран как минимум 1 флаг!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1883, 'del_success', 132, 'The value successfully removed!', 'Значение успешно удалено!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1884, 'del_failed', 132, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1885, 'del_multiply_success', 132, 'Successfully removed the values:', 'Успешно удалено значений:', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1886, 'edit_empty_field', 132, 'Error when editing: not specified a name!', 'Ошибка при редактировании: не указано название!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1887, 'edit_error', 132, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1888, 'edit_success', 132, 'The value of well edited!', 'Значение успешно отредактировано!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1889, 'empty_table', 132, 'Data table is empty!', 'Таблица с данными пуста!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1890, 'pattern', 133, 'Name', 'Название', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1891, 'priority', 133, 'Priority', 'Приоритет', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1892, 'confirm_del', 133, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1893, 'delete', 133, 'Delete', 'Удалить', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1894, 'empty_data', 133, '- no data available -', '- данные отсутствуют -', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1895, 'not_selected', 133, 'Entries not selected!', 'Записи не выбраны!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1896, 'del_selected', 133, 'Remove selected', 'Удалить записи', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1897, 'pattern', 134, 'Name', 'Название', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1898, 'add_success', 134, 'Successfully added!', 'Значение добавлено!', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1899, 'add', 134, 'Add', 'Добавить', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1900, 'flags', 134, 'Flags', 'Флаги', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1901, 'priority', 134, 'Priority', 'Приоритет', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1902, 'opt_1', 134, 'Hide crosshair, ammo, weapon list', 'Скрыть прицел, патроны, список оружия', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1903, 'opt_2', 134, 'Hide flashlight', 'Скрыть фонарик', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1904, 'opt_4', 134, 'Hide all', 'Скрыть всё', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1905, 'opt_8', 134, 'Hide Radar, Health, Armor', 'Скрыть радар, запас жизни, броню', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1906, 'opt_16', 134, 'Hide Timer', 'Скрыть таймер', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1907, 'opt_32', 134, 'Hide Money', 'Скрыть деньги', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1908, 'opt_64', 134, 'Hide all crosshairs', 'Скрыть все прицелы', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1909, 'opt_128', 134, 'Draw Additional Crosshair', 'Показывать дополнительный прицел', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1910, 'pattern', 135, 'Name', 'Название', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1911, 'apply', 135, 'Apply', 'Применить', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1912, 'flags', 135, 'Flags', 'Флаги', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1913, 'priority', 135, 'Priority', 'Приоритет', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1914, 'opt_1', 135, 'Hide crosshair, ammo, weapon list', 'Скрыть прицел, патроны, список оружия', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1915, 'opt_2', 135, 'Hide flashlight', 'Скрыть фонарик', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1916, 'opt_4', 135, 'Hide all', 'Скрыть всё', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1917, 'opt_8', 135, 'Hide Radar, Health, Armor', 'Скрыть радар, запас жизни, броню', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1918, 'opt_16', 135, 'Hide Timer', 'Скрыть таймер', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1919, 'opt_32', 135, 'Hide Money', 'Скрыть деньги', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1920, 'opt_64', 135, 'Hide all crosshairs', 'Скрыть все прицелы', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1921, 'opt_128', 135, 'Draw Additional Crosshair', 'Показывать дополнительный прицел', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1922, 'hud_edit_pattern', 135, 'Editing the value menu', 'Редактирование пункта меню', 'hudManager');
INSERT INTO `acp_lang_words` VALUES (1923, 'opt_redirect', 26, 'Redirection enabled?', 'Редирект включен?', 'multiserverRedirect');
INSERT INTO `acp_lang_words` VALUES (1924, 'nick_add_pattern', 0, 'Add Pattern', 'Добавить Шаблон', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1925, 'checknick_head', 0, 'Check nick', 'Проверка ника', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1926, 'nick_control', 0, 'Nick Control', 'Контроль Ника', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1927, 'nick_patterns', 0, 'Patterns Dictionaries', 'Словари Проверки', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1928, 'nick_logs', 0, 'Work Logs', 'Логи Работы', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1929, 'help_nick_control', 61, 'Nick control', 'Контроль ника', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1930, 'help_nc_perm_patterns', 61, 'Manage patterns', 'Управление шаблонами проверки', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1931, 'help_nick_control', 62, 'Nick control', 'Контроль ника', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1932, 'help_nc_perm_patterns', 62, 'Manage patterns', 'Управление шаблонами проверки', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1933, 'empty_table', 136, 'Table with templates dictionary is empty!', 'Таблица с шаблонами словаря пуста!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1934, 'add_pattern', 136, 'Add pattern', 'Добавить шаблон', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1935, 'head', 136, 'Patterns dictionaries', 'Словари проверки', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1936, 'dont_empty', 136, 'Field must not be empty!', 'Поле не должно быть пустым!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1937, 'add_try', 136, 'This pattern already exists in the database!', 'Данный шаблон уже существует в базе!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1938, 'add_success', 136, 'The pattern successfully added!', 'Шаблон успешно добавлен!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1939, 'add_failed', 136, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1940, 'del_success', 136, 'The pattern successfully removed!', 'Шаблон успешно удален!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1941, 'del_failed', 136, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1942, 'del_multiply_success', 136, 'Successfully removed the patterns:', 'Успешно удалено шаблонов:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1943, 'edit_empty_field', 136, 'Error when editing: do not put a pattern!', 'Ошибка при редактировании: не задан шаблон!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1944, 'edit_error', 136, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1945, 'edit_success', 136, 'The pattern successfully edited!', 'Шаблон успешно отредактирован!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1946, 'move_success', 136, 'Successfully moved the patterns:', 'Успешно перемещено шаблонов:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1947, 'move_error', 136, 'Error when moving. Try again later!', 'Произошла ошибка при перемещении. Повторите позже!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1948, 'checknick_allow', 136, 'Nick is allowed!', 'Ник разрешен!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1949, 'checknick_disallow', 136, 'Nick is disabled!', 'Ник запрещен!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1950, 'checknick_empty', 136, 'Nick is not entered!', 'Ник не введен!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1951, 'confirm_del', 137, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1952, 'pattern', 137, 'Pattern', 'Шаблон', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1953, 'delete', 137, 'Delete', 'Удалить', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1954, 'move', 137, 'Move', 'Переместить', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1955, 'apply', 137, 'Apply', 'Применить', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1956, 'empty_data', 137, '- no data available -', '- данные отсутствуют -', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1957, 'not_selected', 137, 'Entries not selected!', 'Записи не выбраны!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1958, 'add_success', 138, 'The pattern successfully added!', 'Шаблон успешно добавлен!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1959, 'add_pattern', 138, 'Add pattern', 'Добавление шаблона', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1960, 'pattern', 138, 'Pattern:', 'Шаблон:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1961, 'add', 138, 'Add', 'Добавить', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1962, 'dict', 138, 'Dictionary:', 'Словарь:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1963, 'tbl_srv_error', 139, 'Table servers missing!', 'Таблица с серверами отсутствует!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1964, 'tbl_srv_empty', 139, 'Table servers is empty!', 'Таблица с серверами пуста!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1965, 'head', 139, 'Work logs', 'Логи работы', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1966, 'server', 139, 'Server:', 'Сервер:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1967, 'all_servers', 139, 'All servers', 'Все сервера', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1968, 'from', 139, 'From:', 'От:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1969, 'to', 139, 'To:', 'До:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1970, 'reason', 139, 'The reason for trip:', 'Причина срабатывания:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1971, 'all_reasons', 139, 'All reasons', 'Все причины', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1972, 'lock_length', 139, 'Lock length nick', 'Блокировка длины ника', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1973, 'time', 139, 'Time of inspection:', 'Время проверки:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1974, 'all_time', 139, 'All time', 'Всё время', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1975, 'join_game', 139, 'When joining a game', 'При заходе в игру', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1976, 'rename', 139, 'When rename in the game', 'При ренейме в игре', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1977, 'player_nick', 139, 'Player Nick:', 'Ник:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1978, 'player_steam', 139, 'Player SteamID:', 'SteamID игрока:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1979, 'player_ip', 139, 'Player IP:', 'IP игрока:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1980, 'search', 139, 'Search', 'Найти', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1981, 'delete', 139, 'Delete', 'Удалить', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1982, 'del_success', 139, 'Successfully deleted messages in the logs:', 'Успешно удалено сообщений в логах:', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1983, 'del_null', 139, 'At your request, the logs are not found. Try to specify other search options.', 'По вашему запросу логи не найдены. Попробуйте указать другие настройки поиска.', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1984, 'del_error', 139, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1985, 'lock_repeat', 139, 'Blocking the character repeat', 'Блокировка повтора символов', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1986, 'search_empty', 140, 'Sorry, nothing found. Try to specify other search options.', 'Извините, ничего не найдено. Попробуйте указать другие настройки поиска.', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1987, 'head', 140, 'Work logs', 'Логи работы', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1988, 'new_search', 140, 'New search', 'Новый поиск', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1989, 'time', 141, 'Time', 'Время', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1990, 'checked_nick', 141, 'РЎhecked nickname', 'Проверяемый ник', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1991, 'player_action', 141, 'Player action', 'Действие игрока', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1992, 'join_game', 141, 'Join the game', 'Вход в игру', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1993, 'rename', 141, 'Rename nick', 'Смена ника', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1994, 'lock_length', 141, 'Lock length nick', 'Блокировка длины ника', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1995, 'lock_repeat', 141, 'Blocking the character repeat', 'Блокировка повтора символов', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1996, 'info', 141, 'Info', 'Инфо', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1997, 'more_info', 141, 'More info', 'Дополнительная информация', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1998, 'server_ip', 141, 'Server IP', 'IP сервера', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (1999, 'player_ip', 141, 'Player IP', 'IP игрока', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (2000, 'player_steam', 141, 'Player SteamID', 'SteamID игрока', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (2001, 'empty_data', 141, '- no data available -', '- данные отсутствуют -', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (2002, 'reason', 141, 'Reason', 'Причина', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (2003, 'checknick_label', 142, 'Enter a nickname', 'Введите никнейм', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (2004, 'check', 142, 'Check', 'Проверить', 'nickControl');
INSERT INTO `acp_lang_words` VALUES (2005, 'description_game_panel', 0, 'Unique in its control panel game servers.', 'Уникальная в своем роде панель управления игровыми серверами.', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2006, 'my_game_acc', 0, 'My game account', 'Мой игровой аккаунт', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (2007, 'block_stats_player_skill', 0, 'Top players by type skill', 'Лучшие по скиллу', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2008, 'gamestats', 0, 'Game Statistics', 'Игровая Статистика', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2009, 'stats_skill_formula', 36, 'The formula for calculating the skill', 'Формула расчета скилла', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2010, 'help_stats_skill_formula', 36, 'The variables are allowed the following templates: <ul><li><b>{kills}</b> - the number of murders.</li><li><b>{deaths}</b> - the number of deaths.</li><li><b>{wins}</b> - the number of wins the team.</li><li><b>{online}</b> - the number of seconds spent in the game.</li><li><b>{team_t}</b> - the number of games per team of terrorists.</li><li><b>{team_ct}</b> - the number of games for the team of counter-terrorists.</li><li><b>{hs}</b> - the number of headshots.</li><li><b>{streak_kills}</b> - A series of murders.</li><li><b>{streak_deaths}</b> - A series of deaths.</li><li><b>{activity}</b> - % activity player.</li></ul>', 'В качестве переменных допустимы следующие шаблоны:<ul><li><b>{kills}</b> - количество убийств.</li><li><b>{deaths}</b> - количество смертей.</li><li><b>{wins}</b> - количество побед команды.</li><li><b>{online}</b> - количество секунд, проведенных в игре.</li><li><b>{team_t}</b> - количество игр за команду террористов.</li><li><b>{team_ct}</b> - количество игр за команду контр-террористов.</li><li><b>{hs}</b> - количество хэдшотов.</li><li><b>{streak_kills}</b> - Серия убийств.</li><li><b>{streak_deaths}</b> - Серия смертей.</li><li><b>{activity}</b> - % активности игрока.</li></ul>', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2011, 'stats_activity_time', 36, 'Limit player activity in hours', 'Лимит активности игрока в часах', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2012, 'help_stats_activity_time', 36, 'Specify the number of hours after which the activity of the players not to visit the server is \"0\".', 'Укажите количество часов, по истечению которых активность игроков не посетивших сервер будет равна \"0\".', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2013, 'stats_skill_min_kills', 36, 'The minimum number of kills for the calculation of the skill', 'Минимально количество убийств для расчета скилла', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2014, 'help_stats_skill_min_kills', 36, 'Specify the minimum number of kills a player, after which it will be calculated skill.', 'Укажите минимальное количество убийств игроком, после которого будет рассчитываться его скилл.', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2015, 'stats_players_per_page', 36, 'The number of entries per page', 'Количество записей на страницу', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2016, 'help_stats_players_per_page', 36, 'What is the number of records displayed for pagination.', 'Какое количество записей отображать для постраничной навигации.', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2017, 'stats_cache_blocks', 36, 'Caching time for the side blocks', 'Время кеширования боковых блоков', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2018, 'help_stats_cache_blocks', 36, 'Time in minutes to update the cache side blocks. Enter \"0\" if you want to disable caching.', 'Время в минутах до обновления кеша боковых блоков. Укажите \"0\", если желаете отключить кеширование.', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2019, 'stats_cache_time', 36, 'Time statistics were updated in minutes', 'Время обновления статистики в минутах', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2020, 'help_stats_cache_time', 36, 'Specify after how many minutes will be updated statistics of players.', 'Укажите через какое количество минут будет обновляться статистика игроков.', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2021, 'stats_max_top_block', 36, 'Maximum number of entries in the TOP side block', 'Максимальное число записей в ТОП бокового блока', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2022, 'help_stats_max_top_block', 36, 'Specify how many items to display in the side TOP block. If set to \"0\", then displays all positions.', 'Укажите сколько позиций ТОП\'а выводить в боковом блоке. Если установить \"0\", то будут выведены все позиции.', 'gameStats');
INSERT INTO `acp_lang_words` VALUES (2023, 'user', 0, 'User', 'Пользователь', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2024, 'servers', 0, 'Servers', 'Серверы', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2025, 'my_shop', 0, 'Shop', 'Игровой магазин', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2026, 'usershop', 0, 'Shop', 'Игровой магазин', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2027, 'balance_purshase', 0, 'Refill balance', 'Пополнить баланс', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2028, 'exchange_commission', 0, 'commission', 'комиссия', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2029, 'points_suffix', 0, 'PT', 'ПТ', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2030, 'exchanger', 0, 'Exchanger', 'Обменник', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2031, 'usershop_manage', 0, 'Shop management', 'Управление магазином', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2032, 'usershop_admin_patterns', 0, 'Patterns privileges', 'Шаблоны привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2033, 'usershop_admin_payments', 0, 'Payment history', 'История платежей', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2034, 'usershop_admin_patterns_add', 0, 'Adding privileges', 'Добавление привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2035, 'usershop_admin_patterns_edit', 0, 'Editing privileges', 'Редактирование привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2036, 'usershop_admin_groups', 0, 'Privilege groups', 'Группы привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2037, 'usershop_admin_groups_add', 0, 'Add a group of privileges', 'Добавление группы привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2038, 'usershop_admin_groups_edit', 0, 'Edit a group of privileges', 'Редактирование группы привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2039, 'time_day_one', 0, 'day', 'день', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2040, 'time_day_several', 0, 'days', 'дня', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2041, 'time_day_many', 0, 'days', 'дней', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2042, 'time_month_one', 0, 'month', 'месяц', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2043, 'time_month_several', 0, 'months', 'месяца', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2044, 'time_month_many', 0, 'months', 'месяцев', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2045, 'time_year_one', 0, 'year', 'год', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2046, 'time_year_several', 0, 'years', 'года', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2047, 'time_year_many', 0, 'years', 'лет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2048, 'time_day_short', 0, 'd.', 'дн.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2049, 'time_month_short', 0, 'm.', 'мес.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2050, 'time_year_short', 0, 'y.', 'г.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2051, 'day', 0, 'day', 'день', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2052, 'week', 0, 'week', 'неделя', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2053, 'month', 0, 'month', 'месяц', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2054, 'payment_user_privileges', 0, 'User privileges', 'Привилегии играков', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2055, 'payment_privileges', 0, 'Privileges', 'Привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2056, 'usershop_admin_patterns_user_detail', 0, 'Details of the service', 'Подробности об услуге', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2057, 'usershop_buywindow', 0, 'Window sales', 'Окно продажи', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2058, 'usershop_profile_privilege_detail', 0, 'Details of the service', 'Подробности об услуге', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2059, 'weight', 60, 'Weight', 'Вес', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2060, 'help_weight', 61, 'Weight group (higher weight overrides low)', 'Вес группы (высокий вес перекрывает низкий)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2061, 'help_weight', 62, 'Weight group (higher weight overrides low)', 'Вес группы (высокий вес перекрывает низкий)', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2062, 'payment_stats', 3, 'Statistics buy local currency', 'Статистика покупки внутренней валюты', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2063, 'payment_stats_by_week', 3, 'in the last 7 days', 'за последние 7 дней', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2064, 'total_payment', 3, 'Amount of payments', 'Сумма платежей', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2065, 'payment_stats_by_year', 3, 'in the last 12 months', 'за последние 12 месяцев', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2066, 'my_money', 17, 'Local money', 'Внутренняя валюта', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2067, 'my_money', 18, 'Local money', 'Внутренняя валюта', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2068, 'user_bank', 36, 'Bank, payment', 'Банк, платежи', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2069, 'ub_methods', 36, 'Payment methods', 'Методы оплаты', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2070, 'help_ub_methods', 36, 'Specify the active methods of payment, which will be available in your store.', 'Укажите активные методы оплаты, которые будут доступны в Вашем магазине.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2071, 'method_robokassa', 36, 'ROBOKASSA', 'ROBOKASSA', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2072, 'ub_rate_points', 36, 'Exchange rate points (cost of 1 point)', 'Курс обмена поинтов (стоимость 1 поинта)', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2073, 'help_ub_rate_points', 36, 'Enter the value of 1 point. This course will take into account the exchange of domestic currency on game points.', 'Укажите стоимость 1 поинта. Этот курс будет учитываться при обмене внутренней валюты на игровые поинты.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2074, 'ub_robo_login', 36, '[ROBOKASSA] Merchant login', '[ROBOKASSA] Логин мерчанта', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2075, 'help_ub_robo_login', 36, 'Enter your username to log in to your merchant account on the site robokassa.', 'Укажите логин для входа в Ваш личный кабинет на сайте робокассы.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2076, 'ub_commission_exchanger', 36, 'The commission points to exchange local currency', 'Комиссия обмена поинтов на внутреннюю валюту', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2077, 'ub_robo_password_one', 36, '[ROBOKASSA] Payment password #1', '[ROBOKASSA] Пароль #1', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2078, 'help_ub_robo_password_one', 36, 'Password #1 uses the interface initialization payment. The same password must be specified in the <a href=\"https://www.roboxchange.com/Environment/Partners/Login/Merchant/Administration.aspx\">merchant setup page</a>.', 'Пароль #1 используется интерфейсом инициализации оплаты. Этот же пароль следует указать на <a href=\"https://www.roboxchange.com/Environment/Partners/Login/Merchant/Administration.aspx\">странице настройки мерчанта</a>.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2079, 'ub_robo_password_two', 36, '[ROBOKASSA] Payment password #2', '[ROBOKASSA] Пароль #2', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2080, 'help_ub_robo_password_two', 36, 'Password #2 uses a payment notification interface, XML-interfaces. The same password must be specified in the <a href=\"https://www.roboxchange.com/Environment/Partners/Login/Merchant/Administration.aspx\">merchant setup page</a>.', 'Пароль #2 используется интерфейсом оповещения о платеже, XML-интерфейсах. Этот же пароль следует указать на <a href=\"https://www.roboxchange.com/Environment/Partners/Login/Merchant/Administration.aspx\">странице настройки мерчанта</a>.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2081, 'ub_robo_merchant_url', 36, '[ROBOKASSA] URL Merchant', '[ROBOKASSA] URL мерчанта', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2082, 'help_ub_robo_merchant_url', 36, 'Test URL for the initial setup and testing:<br /><em>http://test.robokassa.ru/Index.aspx</em><br />Working URL for the operation of online payments:<br /><em>https://merchant.roboxchange.com/Index.aspx</em>', 'Тестовый URL для первоначальной настройки и проверки:<br /><em>http://test.robokassa.ru/Index.aspx</em><br />Рабочий URL для функционирования онлайн платежей:<br /><em>https://merchant.roboxchange.com/Index.aspx</em>', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2083, 'ub_robo_default_currency', 36, '[ROBOKASSA] Default currency', '[ROBOKASSA] Валюта по-умолчанию', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2084, 'help_ub_robo_default_currency', 36, 'See <a href=\"https://merchant.roboxchange.com/WebService/Service.asmx/GetCurrencies?MerchantLogin=demo&Language=en\">the list of currencies</a> in the site Robokassa.', 'Смотрите <a href=\"https://merchant.roboxchange.com/WebService/Service.asmx/GetCurrencies?MerchantLogin=demo&Language=ru\">список валют</a> на сайте Robokassa.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2085, 'ub_currency_suffix', 36, 'Suffix currency', 'Суффикс валюты', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2086, 'help_ub_currency_suffix', 36, 'This abbreviation is used in the text on the site at the mention of domestic currency.', 'Эта аббревиатура будет использоваться в текстах на сайте при упоминании внутренней валюты сайта.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2087, 'ub_robo_memo', 36, '[ROBOKASSA] Payment description', '[ROBOKASSA] Описание платежа', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2088, 'help_ub_robo_memo', 36, 'Enter a description of the payment, such as: \"Payment for the site www.site.ru\".', 'Введите описание платежа, например: \"Платеж для сайта www.site.ru\".', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2089, 'ub_min_payment', 36, 'Minimum possible payment', 'Минимально возможный платеж', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2090, 'help_ub_min_payment', 36, 'Specify the minimum amount of payment. Make a deposit in the amount of less than this would be impossible.', 'Укажите минимальную сумму платежа. Производить пополнение счета на сумму меньше указанной будет невозможно.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2091, 'help_ub_commission_exchanger', 36, 'Specify % commission for the transfer of the store points in local currency - that is where the % will reduce the amount of currency in exchange. % character set is not necessary, only the numbers. If the commission is not required, set to \"0\". If the function of the exchange points on the local currency should be disabled, set to \"-1\".', 'Укажите % комиссии магазина за перевод из поинтов во внутреннюю валюту - именно на этот % будет уменьшена сумма валюты при обмене. Знак % ставить не нужно, только цифры. Если комиссия не требуется, то установите \"0\". Если функцию обмена поинтов на внутреннюю валюту нужно отключить, то установите \"-1\".', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2092, 'help_ub_pagesize', 36, 'What a number of services to display pagination.', 'Какое количество услуг отображать для постраничной навигации.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2093, 'ub_pagesize', 36, 'Amount of services/privileges your store page', 'Количество услуг/привилегий на страницу магазина', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2094, 'help_user_bank', 61, 'Shop', 'Магазин', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2095, 'help_ub_perm_payment', 61, 'Access control', 'Управление доступом', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2096, 'help_user_bank', 62, 'Shop', 'Магазин', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2097, 'help_ub_perm_payment', 62, 'Access control', 'Управление доступом', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2098, 'error_not_game_account', 63, 'Error: to purchase this service requires a game account that you do not have. Create a game account and try again!', 'Ошибка: для покупки этой услуги требуется игровой аккаунт, который у Вас отсутствует. Создайте игровой аккаунт и повторите попытку!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2099, 'user_services', 63, 'Purchased services', 'Купленные услуги', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2100, 'transaction_history', 63, 'History of transactions', 'История операций', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2101, 'user_balance', 63, 'Balance', 'Баланс', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2102, 'purshase', 63, 'Purshase', 'Пополнить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2103, 'payment_method_not_find', 63, 'Payment methods not found!', 'Методы оплаты не найдены!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2104, 'payment_failed_pre', 63, 'Your operation to replenish the internal balance canceled and bill в„–', 'Ваша операция по пополнению внутреннего баланса отменена и счет №', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2105, 'payment_failed_post', 63, ' revoked!', ' аннулирован!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2106, 'payment_success_pre', 63, 'Replenishment the internal balance of the bill в„–', 'Пополнение внутреннего баланса по счету №', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2107, 'payment_success_post', 63, ' was successful in the amount of: ', ' выполнено успешно на сумму: ', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2108, 'exchange_rate_note', 63, '*Rate: 1 PT =', '*Курс: 1 ПТ =', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2109, 'buy_points', 63, 'Buy points', 'Купить поинты', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2110, 'sell_points', 63, 'Sell points', 'Продать поинты', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2111, 'count_points', 63, 'Number', 'Количество', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2112, 'to_exchange', 63, 'Exchange', 'Обменять', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2113, 'count_points_max', 63, 'All', 'Все', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2114, 'memo_buy_points', 63, 'Buy game points', 'Покупка игровых поинтов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2115, 'exchange_query_error', 63, 'Error updating database tables!', 'Ошибка при обновлении таблиц базы данных!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2116, 'exchange_not_enough_money_error', 63, 'Error - not enough money for the operation!', 'Ошибка - недостаточно средств для проведения операции!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2117, 'exchange_not_user_error', 63, 'Error - user not found!', 'Ошибка - пользователь не найден!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2118, 'exchange_incorrect_type_error', 63, 'Error - unknown type of exchange!', 'Ошибка - неизвестный тип обмена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2119, 'exchange_incorrect_cnt_error', 63, 'Error - incorrectly entered number of points to exchange!', 'Ошибка - некорректно введено количество поинтов для обмена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2120, 'exchange_success', 63, 'Exchange operation completed successfully!', 'Обменная операция успешно выполнена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2121, 'memo_sell_points', 63, 'Sale of game points', 'Продажа игровых поинтов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2122, 'group_name_is_empty', 63, 'Error! Do not fill in the name of the group.', 'Ошибка! Не заполнено название группы.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2123, 'add_group_failed', 63, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2124, 'add_group_success', 63, 'Group has been successfully created!', 'Группа привилегий успешно добавлена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2125, 'edit_group_failed', 63, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2126, 'edit_group_success', 63, 'Edited successfully!', 'Отредактировано успешно!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2127, 'del_group_success', 63, 'Group together with associated privileges deleted successfully!', 'Группа вместе с привязанными к ней привилегиями успешно удалена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2128, 'del_group_failed', 63, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2129, 'del_group_privileges_failed', 63, 'Error deleting privileges of the group!', 'Ошибка при удалении привилегий группы!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2130, 'del_multiply_success', 63, 'Successfully removed the values:', 'Успешно удалено значений:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2131, 'del_bill_failed', 63, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2132, 'change_status_success', 63, 'Activity status was successfully changed!', 'Статус активности успешно изменен!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2133, 'change_status_error', 63, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2134, 'del_pattern_success', 63, 'Removal operation is completed successfully!', 'Операция удаления успешно выполнена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2135, 'del_pattern_user_failed', 63, 'Error while deleting a user privileges!', 'Ошибка при удалении привилегий у пользователя!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2136, 'empty_array', 63, 'There are no indices of the elements.', 'Отсутствуют индексы элементов.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2137, 'priv_not_name', 63, 'Do not contains the name the privilege', 'Не указано название привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2138, 'priv_field_incorrect', 63, 'Field is filled incorrectly', 'Некорректно заполнено поле', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2139, 'priv_add_flags_incorrect', 63, 'Flags player is incorrect', 'Флаги игрока введены неправильно', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2140, 'priv_no_price', 63, 'No price listed privileges', 'Не указана цена привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2141, 'error_list', 63, 'Errors', 'Допущены ошибки', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2142, 'add_failed', 63, 'An error occurred while adding. Try again later!', 'Произошла ошибка при добавлении. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2143, 'priv_usergroups_add_error', 63, 'Unable to add groups', 'Не удалось добавить группы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2144, 'priv_servers_add_error', 63, 'Unable to add servers', 'Не удалось добавить серверы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2145, 'add_payment_pattern_success', 63, 'Pattern privileges added successfully!', 'Шаблон привилегии успешно добавлен!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2146, 'edit_failed', 63, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2147, 'payment_patternid_incorrect', 63, 'Pattern privilege does not exist!', 'Шаблон привилегии не существует!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2148, 'nothing_edit', 63, 'There is nothing to update!', 'Нечего обновлять!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2149, 'edit_payment_pattern_success', 63, 'Pattern privileges edited successfully!', 'Шаблон привилегии отредактирован успешно!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2150, 'not_payment_pattern', 63, 'Error: pattern privilege does not exist!', 'Ошибка: шаблон привилегии не существует!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2151, 'payment_only_registered', 63, 'Error: purchase of services is available to registered users. Log on to the site under your login or register. The registration process will not take you much time, but you can use all the advantages of our party game project!', 'Ошибка: покупка услуг доступна только для зарегистрированных пользователей. Войдите на сайт под своим логином или зарегистрируйтесь. Процесс регистрации не займет у Вас много времени, но позволит использовать все преимущества участника нашего игрового проекта!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2152, 'user_login', 63, 'Login', 'Вход', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2153, 'user_register', 63, 'Registration', 'Регистрация', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2154, 'payment_usergroup_not_access', 63, 'Error: unfortunately, the purchase of this service is disabled by the administrator for your user group.', 'Ошибка: к сожалению, покупка этой услуги отключена администратором для Вашей группы пользователей.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2155, 'payment_servers_list_empty', 63, 'Error: the list of servers to select empty! Contact the administrator.', 'Ошибка: список серверов для выбора пуст! Свяжитесь с администратором.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2156, 'payment_price_not', 63, 'Error: to purchase this service, you have enough funds in your account! Recharge balance and try again.', 'Ошибка: для покупки этой услуги у Вас недостаточно средств на счету! Пополните баланс и повторите попытку.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2157, 'plugin_ga_not_active', 63, 'Error: product \"Game accounts\" is not set. Report the problem to an administrator!', 'Ошибка: плагин \"Игровые аккаунты\" не установлен. Сообщите о проблеме администратору!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2158, 'not_game_account', 63, 'Error: to purchase this service, you want a game account that you do not have. Create a game account and try again!', 'Ошибка: для покупки данной услуги требуется игровой аккаунт, который у Вас отсутствует. Создайте игровой аккаунт и повторите попытку!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2159, 'store_item_limit_time_purchased', 63, 'Error: limit reached sales of this service for a specified period of time. Buy service will be possible through:', 'Ошибка: достигнут лимит продаж этой услуги за установленный период времени. Купить услугу будет возможно через:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2160, 'store_item_for_user_limit_time_purchased', 63, 'Error: for you, this limit has been reached sales of services for a set period of time. Buy service will be possible through:', 'Ошибка: для Вас достигнут лимит продаж этой услуги за установленный период времени. Купить услугу будет возможно через:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2161, 'store_item_for_user_limit_purchased', 63, 'Error: for you, this limit has been reached sales service!', 'Ошибка: для Вас достигнут лимит продаж этой услуги!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2162, 'admin_phone', 63, '* Tel.:', '* Тел.:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2163, 'admin_email', 63, '* Email:', '* Email:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2164, 'admin_icq', 63, '* ICQ:', '* ICQ:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2165, 'payment_servers_select_empty', 63, 'Error: the server is not selected for the service!', 'Ошибка: не выбран сервер для услуги!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2166, 'error_add_flags', 63, 'Error: failed to add the access mask. Report the problem to an administrator!', 'Ошибка: не удалось добавить маску доступа. Сообщите о проблеме администратору!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2167, 'buy_privilege_payment', 63, 'Buying service \"{privilege_name}\"', 'Покупка услуги \"{privilege_name}\"', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2168, 'privilege_buy_success', 63, 'You have successfully purchased service', 'Вами успешно приобретена услуга', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2169, 'purshase_balance', 143, 'Replenishment internal balance', 'Пополнение внутреннего баланса', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2170, 'payment_summ', 143, 'Enter the payment amount', 'Введите сумму платежа', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2171, 'payment_not_summ', 143, 'Replenishment amount is not specified!', 'Сумма пополнения не указана!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2172, 'purshase', 143, 'Purshase', 'Пополнить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2173, 'payment_not_active', 143, 'Currently deposits disabled by your administrator!', 'В данный момент пополнение счета отключено администратором!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2174, 'minimum_payment', 143, 'Minimum payment:', 'Минимальная сумма платежа:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2175, 'rub_suffix', 143, 'rub.', 'руб.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2176, 'payment_not_minimum', 143, 'Introduced the payment amount is less than the minimum allowed!', 'Введенная сумма платежа меньше допустимого минимума!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2177, 'transaction_id', 144, 'в„– bill', '№ счета', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2178, 'transaction_amount', 144, 'Amount', 'Сумма', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2179, 'transaction_memo', 144, 'Description', 'Описание', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2180, 'transaction_enrolled', 144, 'Transaction date', 'Дата операции', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2181, 'buy_money_all', 145, 'Purchased MM', 'Куплено MM', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2182, 'buy_points_all', 145, 'Purchased PT', 'Куплено ПТ', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2183, 'not_payments', 145, 'No payments in the store!', 'Платежи отсутствуют в магазине!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2184, 'add_group', 146, 'Add group', 'Добавить группу', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2185, 'empty_table', 146, 'Data table is empty, the groups do not exist.', 'Таблица с данными пуста, группы ещё не созданы.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2186, 'group_name', 147, 'Name', 'Название', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2187, 'group_count_privileges', 147, 'Patterns', 'Шаблоны', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2188, 'del_selected', 147, 'Remove entries', 'Удалить записи', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2189, 'not_selected', 147, 'Entries not selected!', 'Записи не выбраны!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2190, 'confirm_del', 147, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2191, 'group_name', 148, 'Group name', 'Название группы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2192, 'group_description', 148, 'Description', 'Описание', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2193, 'add', 148, 'Add', 'Добавить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2194, 'group_description', 149, 'Description', 'Описание', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2195, 'group_name', 149, 'Group name', 'Название группы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2196, 'save', 149, 'Save', 'Сохранить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2197, 'transaction_id', 150, 'в„– bill', '№ счета', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2198, 'transaction_amount', 150, 'Amount', 'Сумма', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2199, 'transaction_memo', 150, 'Description', 'Описание', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2200, 'transaction_enrolled', 150, 'Transaction date', 'Дата операции', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2201, 'transaction_user', 150, 'User', 'Пользователь', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2202, 'not_pay', 150, 'not paid', 'не оплачено', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2203, 'del_selected', 150, 'Remove entries', 'Удалить записи', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2204, 'not_selected', 150, 'Entries not selected!', 'Записи не выбраны!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2205, 'all_transactions', 151, 'All trasnsactions', 'Все операции', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2206, 'buy_mymoney', 151, 'Balance recharge', 'Пополнение счета', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2207, 'exchange_transactions', 151, 'Exchange transactions', 'Операции обмена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2208, 'empty_table', 151, 'Data table is empty, there are no bills.', 'Таблица с данными пуста, счета отсутствуют.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2209, 'empty_table', 152, 'Data table is empty, the privilege is not found.', 'Таблица с данными пуста, привилегии не найдены.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2210, 'add_payment_pattern', 152, 'Add pattern', 'Добавить шаблон', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2211, 'all_groups', 152, 'All groups', 'Все группы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2212, 'choose_period_info', 153, 'You can choose the period when buying', 'Возможен выбор срока при покупке', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2213, 'pattern_name', 153, 'Name', 'Название', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2214, 'pattern_duration', 153, 'Duration', 'Срок', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2215, 'pattern_price', 153, 'Price', 'Цена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2216, 'pattern_purchased', 153, 'Purchased', 'Куплено', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2217, 'del_selected', 153, 'Remove entries', 'Удалить записи', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2218, 'not_selected', 153, 'Entries not selected!', 'Записи не выбраны!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2219, 'confirm_del', 153, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2220, 'all_duration', 153, 'permanent', 'навсегда', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2221, 'click_change_status', 153, 'Click to change the pattern status', 'Нажмите, чтобы изменить статус шаблона', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2222, 'pattern_limit', 153, 'Limit', 'Лимит', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2223, 'no_limit', 153, 'not', 'нет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2224, 'back_url', 154, 'Back to the list of privileges', 'Вернуться к списку привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2225, 'pattern_group', 154, 'Privilege group', 'Группа привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2226, 'pattern_group_not', 154, 'Not group', 'Нет группы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2227, 'pattern_name', 154, 'Name', 'Название', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2228, 'pattern_description', 154, 'Description', 'Описание', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2229, 'pattern_price', 154, 'Price', 'Цена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2230, 'pattern_price_mm', 154, 'Local currency', 'Внутренняя валюта', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2231, 'pattern_price_points', 154, 'Number of points', 'Количество поинтов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2232, 'pattern_duration', 154, 'Duration', 'Срок действия', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2233, 'duration_type_date', 154, 'Date', 'Дата', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2234, 'duration_type_day', 154, 'Number of days', 'Количество дней', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2235, 'duration_type_month', 154, 'Number of months', 'Количество месяцев', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2236, 'duration_type_year', 154, 'Number of years', 'Количество лет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2237, 'pattern_sale_items', 154, 'The number of sales privileges', 'Количество продаж привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2238, 'per_week', 154, 'Per week', 'В неделю', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2239, 'per_month', 154, 'Per month', 'В месяц', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2240, 'per_day', 154, 'Per day', 'В день', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2241, 'per_all', 154, 'Total', 'Всего', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2242, 'pattern_duration_select', 154, 'Allow selection of the term of the user?', 'Разрешить выбор срока действия пользователем?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2243, 'yes', 154, 'Yes', 'Да', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2244, 'no', 154, 'No', 'Нет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2245, 'pattern_sale_items_for_user', 154, 'The number of sales user', 'Количество продаж пользователю', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2246, 'pattern_new_usergroup', 154, 'Transfer to group', 'Перевести в группу', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2247, 'pattern_new_usergroup_not', 154, 'Not to transfer', 'Не переводить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2248, 'pattern_enable_server_select', 154, 'Allow server selection by the user?', 'Разрешить выбор сервера пользователем?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2249, 'pattern_add_flags', 154, 'Add flags player', 'Добавить флаги игроку', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2250, 'pattern_add_points', 154, 'Add points player', 'Добавить поинты игроку', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2251, 'pattern_do_php_exec', 154, 'PHP-handler', 'PHP-обработчик', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2252, 'pattern_active', 154, 'The privilege is active?', 'Привилегия активна?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2253, 'save', 154, 'Save', 'Сохранить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2254, 'pattern_usergroups_access', 154, 'The privilege is enabled for user groups', 'Привилегия разрешена для групп пользователей', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2255, 'pattern_servers_access', 154, 'Activate for servers', 'Активировать для серверов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2256, 'not_payment_pattern', 155, 'Pattern privilege does not exist!', 'Шаблон привилегии не существует!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2257, 'back_url', 155, 'Back to the list of privileges', 'Вернуться к списку привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2258, 'pattern_group', 155, 'Privilege group', 'Группа привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2259, 'pattern_group_not', 155, 'Not group', 'Нет группы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2260, 'pattern_name', 155, 'Name', 'Название', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2261, 'pattern_description', 155, 'Description', 'Описание', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2262, 'pattern_price', 155, 'Price', 'Цена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2263, 'pattern_price_mm', 155, 'Local currency', 'Внутренняя валюта', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2264, 'pattern_price_points', 155, 'Number of points', 'Количество поинтов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2265, 'pattern_duration', 155, 'Duration', 'Срок действия', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2266, 'duration_type_date', 155, 'Date', 'Дата', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2267, 'duration_type_day', 155, 'Number of days', 'Количество дней', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2268, 'duration_type_month', 155, 'Number of months', 'Количество месяцев', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2269, 'duration_type_year', 155, 'Number of years', 'Количество лет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2270, 'pattern_sale_items', 155, 'The number of sales privileges', 'Количество продаж привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2271, 'per_week', 155, 'Per week', 'В неделю', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2272, 'per_month', 155, 'Per month', 'В месяц', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2273, 'per_day', 155, 'Per day', 'В день', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2274, 'per_all', 155, 'Total', 'Всего', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2275, 'pattern_duration_select', 155, 'Allow selection of the term of the user?', 'Разрешить выбор срока действия пользователем?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2276, 'yes', 155, 'Yes', 'Да', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2277, 'no', 155, 'No', 'Нет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2278, 'pattern_sale_items_for_user', 155, 'The number of sales user', 'Количество продаж пользователю', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2279, 'pattern_new_usergroup', 155, 'Transfer to group', 'Перевести в группу', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2280, 'pattern_new_usergroup_not', 155, 'Not to transfer', 'Не переводить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2281, 'pattern_enable_server_select', 155, 'Allow server selection by the user?', 'Разрешить выбор сервера пользователем?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2282, 'pattern_add_flags', 155, 'Add flags player', 'Добавить флаги игроку', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2283, 'pattern_add_points', 155, 'Add points player', 'Добавить поинты игроку', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2284, 'pattern_do_php_exec', 155, 'PHP-handler', 'PHP-обработчик', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2285, 'pattern_active', 155, 'The privilege is active?', 'Привилегия активна?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2286, 'save', 155, 'Save', 'Сохранить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2287, 'pattern_usergroups_access', 155, 'The privilege is enabled for user groups', 'Привилегия разрешена для групп пользователей', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2288, 'pattern_servers_access', 155, 'Activate for servers', 'Активировать для серверов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2289, 'empty_table', 156, 'Data table is empty, the privileges of user do not exist.', 'Таблица с данными пуста, привилегии у пользователей не найдены.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2290, 'all_privileges', 156, 'All privileges', 'Все привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2291, 'active_privileges', 156, 'Active privileges', 'Активные привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2292, 'inactive_privileges', 156, 'Finished privileges', 'Завершенные привилегии', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2293, 'user_name', 157, 'User', 'Пользователь', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2294, 'pattern_name', 157, 'Privilege', 'Привилегия', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2295, 'date_privileges_start', 157, 'Activated date', 'Дата активации', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2296, 'time_expired', 157, 'Time expired', 'Время истекло', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2297, 'payment_pattern_deleted', 157, 'Privilege removed', 'Привилегия удалена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2298, 'info_details', 157, 'More details', 'Подробнее', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2299, 'time_expired_pre', 157, 'Remaining', 'Осталось', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2300, 'time_expired_permanent', 157, 'Unlimited period', 'Неограниченный срок', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2301, 'user_privileges_details_not_found', 158, 'Service with the given identity was not found!', 'Услуга с указанным идентификатором не найдена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2302, 'payment_pattern_deleted', 158, 'Privilege removed', 'Привилегия удалена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2303, 'usergroup_removed', 158, 'Group removed', 'Группа удалена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2304, 'time_expired_permanent', 158, 'Unlimited period', 'Неограниченный срок', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2305, 'privilege_name', 158, 'Privilege', 'Привилегия', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2306, 'effective_period', 158, 'Effective period', 'Период активности', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2307, 'override_group', 158, 'New usergroup', 'Новая группа', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2308, 'account_access_mask', 158, 'Access mask', 'Маска доступа', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2309, 'all_servers', 158, 'all', 'все', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2310, 'time_expired', 158, 'Time expired', 'Время истекло', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2311, 'time_expired_pre', 158, 'Remaining', 'Осталось', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2312, 'active_privileges_not_found', 159, 'There are no available services and privileges for purchase.', 'Нет доступных услуг и привилегий для покупки.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2313, 'usershop_privileges_services', 159, 'Shop services and privileges', 'Магазин услуг и привилегий', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2314, 'buy', 159, 'Buy', 'Купить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2315, 'store_item_price', 159, 'Price', 'Цена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2316, 'choose_period_info', 159, 'You can choose the period when buying', 'Возможен выбор срока при покупке', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2317, 'store_item_name', 159, 'Privilege name', 'Название услуги', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2318, 'store_item_duration', 159, 'Time', 'Срок', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2319, 'all_duration', 159, 'Forever', 'Навсегда', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2320, 'item_sale_free', 159, 'Free', 'Бесплатно', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2321, 'not_payment_pattern', 160, 'Error: the selected service does not exist or is disabled by the administrator!', 'Ошибка: выбранная услуга не существует или отключена администратором!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2322, 'usershop_buywindow_title', 160, 'Buying services', 'Покупка услуги', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2323, 'buy_item', 160, 'Buy', 'Купить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2324, 'store_select_server', 160, 'Select a server to service', 'Выберите серверы для услуги', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2325, 'sale_item_forever', 160, 'forever', 'навсегда', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2326, 'store_servers', 160, 'Service will be activated for the servers', 'Услуга будет активирована для серверов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2327, 'show_more', 160, 'show more', 'показать все', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2328, 'hide_more', 160, 'hide', 'скрыть', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2329, 'store_item_duration', 160, 'Time', 'Срок', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2330, 'payment_only_registered', 160, 'Error: purchase of services is available to registered users. Log on to the site under your login or register. The registration process will not take you much time, but you can use all the advantages of our party game project!', 'Ошибка: покупка услуг доступна только для зарегистрированных пользователей. Войдите на сайт под своим логином или зарегистрируйтесь. Процесс регистрации не займет у Вас много времени, но позволит использовать все преимущества участника нашего игрового проекта!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2331, 'user_login', 160, 'Login', 'Вход', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2332, 'user_register', 160, 'Registration', 'Регистрация', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2333, 'payment_usergroup_not_access', 160, 'Error: unfortunately, the purchase of this service is disabled by the administrator for your user group.', 'Ошибка: к сожалению, покупка этой услуги отключена администратором для Вашей группы пользователей.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2334, 'payment_servers_list_empty', 160, 'Error: the list of servers to select empty! Contact the administrator.', 'Ошибка: список серверов для выбора пуст! Свяжитесь с администратором.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2335, 'payment_price_not', 160, 'Error: to purchase this service, you have enough funds in your account! Recharge balance and try again.', 'Ошибка: для покупки этой услуги у Вас недостаточно средств на счету! Пополните баланс и повторите попытку.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2336, 'plugin_ga_not_active', 160, 'Error: product \"Game accounts\" is not set. Report the problem to an administrator!', 'Ошибка: плагин \"Игровые аккаунты\" не установлен. Сообщите о проблеме администратору!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2337, 'not_game_account', 160, 'Error: to purchase this service, you want a game account that you do not have. Create a game account and try again!', 'Ошибка: для покупки данной услуги требуется игровой аккаунт, который у Вас отсутствует. Создайте игровой аккаунт и повторите попытку!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2338, 'store_item_limit_time_purchased', 160, 'Error: limit reached sales of this service for a specified period of time. Buy service will be possible through:', 'Ошибка: достигнут лимит продаж этой услуги за установленный период времени. Купить услугу будет возможно через:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2339, 'store_item_for_user_limit_time_purchased', 160, 'Error: for you, this limit has been reached sales of services for a set period of time. Buy service will be possible through:', 'Ошибка: для Вас достигнут лимит продаж этой услуги за установленный период времени. Купить услугу будет возможно через:', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2340, 'store_item_for_user_limit_purchased', 160, 'Error: for you, this limit has been reached sales service!', 'Ошибка: для Вас достигнут лимит продаж этой услуги!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2341, 'you_enough_money', 160, '* enough money your balance', '* недостаточно средств на Вашем счету', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2342, 'pattern_name', 161, 'Service name', 'Название услуги', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2343, 'date_privileges_start', 161, 'Activated date', 'Дата активации', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2344, 'time_expired_pre', 161, 'Remaining', 'Осталось', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2345, 'info_details', 161, 'More details', 'Подробнее', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2346, 'payment_pattern_deleted', 161, 'Service removed', 'Услуга удалена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2347, 'time_expired_permanent', 161, 'Unlimited period', 'Неограниченный срок', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2348, 'user_privileges_details_not_found', 162, 'Service with the given identity was not found!', 'Услуга с указанным идентификатором не найдена!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2349, 'privilege_name', 162, 'Service', 'Услуга', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2350, 'effective_period', 162, 'Effective period', 'Период активности', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2351, 'override_group', 162, 'New usergroup', 'Новая группа', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2352, 'account_access_mask', 162, 'Access flags', 'Флаги доступа', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2353, 'all_servers', 162, 'all', 'все', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2354, 'time_expired', 162, 'Time expired', 'Время истекло', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2355, 'time_expired_pre', 162, 'Remaining', 'Осталось', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2356, 'privilege_description', 162, 'Description of services', 'Описание услуги', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2357, 'method_apay', 36, 'a1pay', 'a1pay', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2358, 'ub_apay_merchant_url', 36, '[A1PAY] URL Merchant', '[A1PAY] URL мерчанта', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2359, 'help_ub_apay_merchant_url', 36, 'Working URL for the operation of online payments:<br /><em>https://partner.a1pay.ru/a1lite/input/</em>', 'Рабочий URL для функционирования онлайн платежей:<br /><em>https://partner.a1pay.ru/a1lite/input/</em>', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2360, 'ub_apay_secretkey', 36, '[A1PAY] Secret key', '[A1PAY] Ключ безопасности', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2361, 'help_ub_apay_secretkey', 36, 'Build a security key \"key\" in your personal account online payment system a1pay and enter it here.', 'Сформируйте ключ безопасности \"key\" в своем личном кабинете на сайте платежной системы a1pay и укажите его здесь.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2362, 'ub_apay_key', 36, '[A1PAY] Key payment buttons', '[A1PAY] Ключ кнопки оплаты', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2363, 'help_ub_apay_key', 36, 'This key will be available when you create payment buttons in your account payment system. Do not confuse this option with the secret key.', 'Этот ключ будет доступен при создании кнопки оплаты в личном кабинете платежной системы. Не путайте этот ключ с ключом безопасности.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2364, 'ub_apay_memo', 36, '[A1PAY] Payment description', '[A1PAY] Описание платежа', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2365, 'help_ub_apay_memo', 36, 'Enter a description of the payment, such as: \"Payment for the site www.site.ru\".', 'Введите описание платежа, например: \"Платеж для сайта www.site.ru\".', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2366, 'payment_method_select', 143, 'Select a payment system', 'Выберите систему оплаты', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2367, 'points_suffix_game', 0, 'Points', 'Очков', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2368, 'game_shop', 0, 'Game shop', 'Игровой магазин', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2369, 'game_shop_items', 0, 'List of items', 'Список итемов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2370, 'game_shop_servers', 0, 'List of servers', 'Список серверов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2371, 'gameshop_items_edit', 0, 'Edit item', 'Редактирование итема', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2372, 'gameshop_items_add', 0, 'Add item', 'Добавление итема', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2373, 'item', 0, 'Item', 'Итем', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2374, 'del_item_success', 63, 'Item deleted successfully!', 'Итем успешно удален!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2375, 'del_item_failed', 63, 'An error occurred while deleting. Try again later!', 'Произошла ошибка при удалении. Повторите позже!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2376, 'del_item_servers_failed', 63, 'Error deleting servers linked to an item!', 'Ошибка при удалении привязанных к итему серверов!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2377, 'game_descr_empty', 63, 'Error: the name of the game item is not filled!', 'Ошибка: игровое название итема не заполнено!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2378, 'add_item_success', 63, 'An item was successfully added!', 'Итем успешно добавлен!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2379, 'add_srvsync_failed', 63, 'An error occurred while synchronizing with the server!', 'Ошибка при синхронизации с серверами!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2380, 'add_lastid_failed', 63, 'Error in getting ID added item!', 'Ошибка в получении ID добавленного итема!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2381, 'edit_item_success', 63, 'Item is successfully edited!', 'Итем успешно отредактирован!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2382, 'empty_table', 163, 'Data table is empty, the items do not exist.', 'Таблица с данными пуста, итемы ещё не созданы.', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2383, 'all_servers', 163, 'All servers', 'Все серверы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2384, 'all_status', 163, 'All status', 'Все статусы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2385, 'active', 163, 'Active', 'Активные', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2386, 'inactive', 163, 'Inactive', 'Неактивные', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2387, 'add_gameshop_item', 163, 'Add an item', 'Добавить итем', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2388, 'del_selected', 164, 'Remove entries', 'Удалить записи', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2389, 'not_selected', 164, 'Entries not selected!', 'Записи не выбраны!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2390, 'confirm_del', 164, 'Do you really want to delete this?', 'Вы действительно хотите удалить?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2391, 'item_name', 164, 'Item name', 'Название итема', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2392, 'item_cost', 164, 'Cost', 'Цена', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2393, 'item_duration', 164, 'Value', 'Значение', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2394, 'item_servers', 164, 'Servers', 'Серверы', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2395, 'click_change_status', 164, 'Click to change the item status', 'Нажмите, чтобы изменить статус итема', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2396, 'all_servers_active', 164, 'ALL', 'ВСЕ', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2397, 'servers_not_found', 165, 'Servers not found!', 'Серверы не найдены!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2398, 'server_not_found', 165, 'Server not found!', 'Сервер на найден!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2399, 'item_not_found', 165, 'The specified item is not there!', 'Указанный итем не существует!', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2400, 'item_all_servers', 165, 'Active for all servers', 'Активно для всех серверов', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2401, 'item_const_hp', 166, 'Adding health', 'Добавление здоровья', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2402, 'item_const_ap', 166, 'Adding armor', 'Добавление брони', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2403, 'item_const_money', 166, 'Adding money', 'Добавление денег', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2404, 'item_const_glow', 166, 'Adding glow', 'Добавление свечения', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2405, 'item_const_resp', 166, 'Adding respawn', 'Добавление воскрешения', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2406, 'item_const_god', 166, 'Adding invulnerability', 'Добавление неуязвимости', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2407, 'item_web_descr', 166, 'Item web-name', 'WEB-название итема', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2408, 'item_game_descr', 166, 'Item game-name', 'Название итема в игре', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2409, 'item_cost', 166, 'Price in points', 'Цена в поинтах', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2410, 'item_duration', 166, 'Amount/Duration', 'Срок/Количество/Продолжительность', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2411, 'item_servers', 166, 'Available on all servers?', 'Доступно на всех серверах?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2412, 'item_active', 166, 'Item active?', 'Итем активен?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2413, 'add', 166, 'Add', 'Добавить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2414, 'yes', 166, 'Yes', 'Да', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2415, 'no', 166, 'No', 'Нет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2416, 'item_const_hp', 167, 'Adding health', 'Добавление здоровья', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2417, 'item_const_ap', 167, 'Adding armor', 'Добавление брони', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2418, 'item_const_money', 167, 'Adding money', 'Добавление денег', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2419, 'item_const_glow', 167, 'Adding glow', 'Добавление свечения', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2420, 'item_const_resp', 167, 'Adding respawn', 'Добавление воскрешения', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2421, 'item_const_god', 167, 'Adding invulnerability', 'Добавление неуязвимости', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2422, 'item_web_descr', 167, 'Item web-name', 'WEB-название итема', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2423, 'item_game_descr', 167, 'Item game-name', 'Название итема в игре', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2424, 'item_cost', 167, 'Price in points', 'Цена в поинтах', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2425, 'item_duration', 167, 'Amount/Duration', 'Срок/Количество/Продолжительность', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2426, 'item_servers', 167, 'Available on all servers?', 'Доступно на всех серверах?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2427, 'item_active', 167, 'Item active?', 'Итем активен?', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2428, 'edit', 167, 'Save', 'Сохранить', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2429, 'yes', 167, 'Yes', 'Да', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2430, 'no', 167, 'No', 'Нет', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2431, 'time_expired', 0, 'Time expired', 'Время истекло', 'userBank');
INSERT INTO `acp_lang_words` VALUES (2432, 'account_help_info', 63, '<div class=\"message info\"><p>У вас стоит тип авторизации по нику. Вам необходимо ввести в консоль или config.cfg игры свой пароль.<br>\r\nВ таком формате: \r\n<b>setinfo \"_pw\" \"пароль\"</b><br>\r\nКавычки желательны.</p></div>\r\n<div class=\"message warning\"><p>Внимание. Иногда в вашей кс может быть переполнение базы доп. данных. Это определяется по выводу Ошибки <b>Info string length exceeded</b> после ввода пароля.<br><br>\r\n               \r\nОшибка Info string length exceeded возникает когда заполняется память команды setinfo. В результате вводимая команда setinfo \"_pw\" \"пароль\" не сохраняется.<br><br>\r\n \r\nЧтобы это исправить нужно очистить память setinfo. Для этого вводим в консоль setinfo и нажимаем энтер. Результат - вывод всех значений записанных в setinfo. Теперь нужно очистить несколько штук, для этого каждому задаем пустое значение.<br><br>\r\n \r\nПример:<br>\r\nsetinfo model \"\"<br>\r\nsetinfo bottomcolor \"\"<br>\r\nsetinfo lang \"\"<br>\r\nsetinfo _gm \"\"<br>\r\n<br>\r\nПосле этого вводим заного setinfo \"_pw\" \"пароль\" в консоль. Если в ответ ничего не написало, значит все ок подключаемся к серверу.\r\n</p></div>', '<div class=\"message info\"><p>У вас стоит тип авторизации по нику. Вам необходимо ввести в консоль или config.cfg игры свой пароль.<br>\r\nВ таком формате: \r\n<b>setinfo \"_pw\" \"пароль\"</b><br>\r\nКавычки желательны.</p></div>\r\n<div class=\"message warning\"><p>Внимание. Иногда в вашей кс может быть переполнение базы доп. данных. Это определяется по выводу Ошибки <b>Info string length exceeded</b> после ввода пароля.<br><br>\r\n               \r\nОшибка Info string length exceeded возникает когда заполняется память команды setinfo. В результате вводимая команда setinfo \"_pw\" \"пароль\" не сохраняется.<br><br>\r\n \r\nЧтобы это исправить нужно очистить память setinfo. Для этого вводим в консоль setinfo и нажимаем энтер. Результат - вывод всех значений записанных в setinfo. Теперь нужно очистить несколько штук, для этого каждому задаем пустое значение.<br><br>\r\n \r\nПример:<br>\r\nsetinfo model \"\"<br>\r\nsetinfo bottomcolor \"\"<br>\r\nsetinfo lang \"\"<br>\r\nsetinfo _gm \"\"<br>\r\n<br>\r\nПосле этого вводим заного setinfo \"_pw\" \"пароль\" в консоль. Если в ответ ничего не написало, значит все ок подключаемся к серверу.\r\n</p></div>', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (2433, 'game_account', 168, 'Nickname:', 'Игровой ник:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2434, 'acc_last_online', 168, 'Last Online:', 'Последний онлайн:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2435, 'acc_online_all', 168, 'Total Online:', 'Всего онлайн:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2436, 'acc_points', 168, 'Points:', 'Игровые очки:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2437, 'acc_money', 168, 'Game Money:', 'Игровые деньги:', 'ACPanel');
INSERT INTO `acp_lang_words` VALUES (2438, 'empty_table', 81, 'Data table is empty!', 'Таблица с данными пуста!', 'gameAccounts');
INSERT INTO `acp_lang_words` VALUES (2439, 'vbk_logs', 0, 'Logs voting', 'Логи голосований', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2440, 'help_vbk', 61, 'Logs vote players', 'Логи голосований игроков', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2441, 'help_vbk_perm', 61, 'Manage logs', 'Управление логами', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2442, 'help_vbk', 62, 'Logs vote players', 'Логи голосований игроков', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2443, 'help_vbk_perm', 62, 'Manage logs', 'Управление логами', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2444, 'new_search', 169, 'New search', 'Новый поиск', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2445, 'head', 169, 'Voting search results', 'Результаты поиска голосований', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2446, 'time', 170, 'Time', 'Время', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2447, 'server', 170, 'Server', 'Сервер', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2448, 'info_text', 170, 'Action', 'Действие', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2449, 'vote_info_ban', 170, 'started voting to ban a player', 'запустил голосование на бан игрока', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2450, 'vote_info_kick', 170, 'started voting to kick a player', 'запустил голосование на кик игрока', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2451, 'player_info', 170, 'Player info', 'Информация об игроке', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2452, 'player_nick', 170, 'Player nick', 'Ник игрока', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2453, 'player_ip', 170, 'Player IP', 'IP игрока', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2454, 'player_steam', 170, 'Player SteamID', 'SteamID игрока', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2455, 'success', 170, 'Success', 'Успешно', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2456, 'failed', 170, 'Failed', 'Провалено', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2457, 'vote_result_info', 170, 'Results', 'Результаты', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2458, 'vote_type', 170, 'Vote type', 'Тип голосования', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2459, 'vote_who', 170, 'Who started', 'Кто запустил', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2460, 'vote_info_type_ban', 170, 'BAN for player', 'за БАН игрока', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2461, 'vote_info_type_kick', 170, 'KICK for player', 'за КИК игрока', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2462, 'vote_against', 170, 'Against whom', 'Против кого', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2463, 'vote_reason', 170, 'Reason', 'Причина', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2464, 'vote_ban_length', 170, 'Expires (minutes)', 'Время бана (минут)', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2465, 'vote_ban_permanent', 170, 'permanently', 'навсегда', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2466, 'voted', 170, 'Voted', 'Проголосовало', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2467, 'vote_result', 170, 'Result', 'Результат', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2468, 'vote_success_ban', 170, 'player successfully banned', 'игрок успешно забанен', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2469, 'vote_success_kick', 170, 'player successfully kicked', 'игрок успешно кикнут', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2470, 'vote_failed', 170, 'vote failed', 'голосование провалено', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2471, 'voted_string', 171, '%d players, are FOR - %d, need to - %d', '%d игроков, из них ЗА - %d, нужно - %d', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2472, 'del_success', 171, 'Successfully deleted logs:', 'Удачно удалено логов:', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2473, 'del_null', 171, 'At your request, the logs are not found. Try to specify other search options.', 'По вашему запросу логи не найдены. Попробуйте указать другие настройки поиска.', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2474, 'del_error', 171, 'Unexpected error. Try again later!', 'Непредвиденная ошибка. Повторите позже!', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2475, 'head', 171, 'Logs voting for violations', 'Логи голосований за нарушения', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2476, 'server', 171, 'Server', 'Сервер', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2477, 'all_servers', 171, 'All servers', 'Все сервера', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2478, 'search_vote_type', 171, 'Vote type', 'Тип голосования', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2479, 'all_types', 171, 'All types', 'Все типы', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2480, 'vote_for_ban', 171, 'Vote for ban', 'Голосование за бан', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2481, 'vote_for_kick', 171, 'Vote for kick', 'Голосование за кик', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2482, 'search_vote_result', 171, 'Vote result', 'Результат голосования', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2483, 'all_results', 171, 'All results', 'Все результаты', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2484, 'vote_only_success', 171, 'Only successful', 'Только успешные', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2485, 'vote_only_failed', 171, 'Only failed', 'Только проваленные', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2486, 'vote_player_nick', 171, 'Nick player who started the vote', 'Ник начавшего голосование', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2487, 'vote_player_steam', 171, 'SteamID player who started the vote', 'SteamID начавшего голосование', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2488, 'vote_player_ip', 171, 'IP-address of the player who started the vote', 'IP-адрес начавшего голосование', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2489, 'nom_player_nick', 171, 'Nick player who is nominated', 'Ник номинанта', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2490, 'nom_player_steam', 171, 'SteamID player who is nominated', 'SteamID номинанта', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2491, 'nom_player_ip', 171, 'IP-address player who is nominated', 'IP-адрес номинанта', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2492, 'search', 171, 'Search', 'Найти', 'voteBanKick');
INSERT INTO `acp_lang_words` VALUES (2493, 'delete', 171, 'Delete', 'Удалить', 'voteBanKick');
COMMIT;

-- ----------------------------
-- Table structure for acp_logs
-- ----------------------------
DROP TABLE IF EXISTS `acp_logs`;
CREATE TABLE `acp_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(1) NOT NULL,
  `ip` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `remarks` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_nick_logs
-- ----------------------------
DROP TABLE IF EXISTS `acp_nick_logs`;
CREATE TABLE `acp_nick_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serverip` varchar(32) NOT NULL DEFAULT '',
  `timestamp` int(1) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `authid` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(100) NOT NULL DEFAULT '',
  `pattern` varchar(4) NOT NULL DEFAULT '',
  `action` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_nick_patterns
-- ----------------------------
DROP TABLE IF EXISTS `acp_nick_patterns`;
CREATE TABLE `acp_nick_patterns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern` varchar(100) NOT NULL,
  `action` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `pattern` (`pattern`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_nick_patterns
-- ----------------------------
BEGIN;
INSERT INTO `acp_nick_patterns` VALUES (1, '^%', 1);
INSERT INTO `acp_nick_patterns` VALUES (2, '!g', 1);
INSERT INTO `acp_nick_patterns` VALUES (3, '\\.(net|ru|su|nu|co|il|com|info|org|eu|nu|ro)', 1);
INSERT INTO `acp_nick_patterns` VALUES (4, 'f(a|u)ck', 1);
INSERT INTO `acp_nick_patterns` VALUES (5, 'e(6|b)((lo(?!ck)|la|lu)|(a|i|y|u|@)(t|h|l))', 1);
INSERT INTO `acp_nick_patterns` VALUES (6, '(r|g)(a|o)(n|h)(g|d)o(n|h)', 1);
INSERT INTO `acp_nick_patterns` VALUES (7, 'm(u|y)(d|g)(a?(k|c)|(i|u)l)', 1);
INSERT INTO `acp_nick_patterns` VALUES (8, 'pid(o|a)?r', 1);
INSERT INTO `acp_nick_patterns` VALUES (9, '(n|p|II)(i|1)?(3|z)(d|g)e?c?', 1);
INSERT INTO `acp_nick_patterns` VALUES (10, '(s|c)(o{1,5}|u|y)(k|q)(a|i|u|y)', 1);
INSERT INTO `acp_nick_patterns` VALUES (11, '(g|d)o(l|ji|\\/\\\\)(b|6)(o|a)e(b|6)', 1);
INSERT INTO `acp_nick_patterns` VALUES (12, '(a|o)?(x|h)((yu|ui|yi)|((y|u)e(t|ji|l|n|h)))', 1);
INSERT INTO `acp_nick_patterns` VALUES (13, '(p|n)(i|u)(z|s|c|3)(g|d|t)(a|y|u)', 1);
COMMIT;

-- ----------------------------
-- Table structure for acp_pages
-- ----------------------------
DROP TABLE IF EXISTS `acp_pages`;
CREATE TABLE `acp_pages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `catid` int(10) DEFAULT NULL,
  `pagetext` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_pages
-- ----------------------------
BEGIN;
INSERT INTO `acp_pages` VALUES (1, 17, '<ul><li>Правила поведения!<br></li></ul>');
COMMIT;

-- ----------------------------
-- Table structure for acp_payment
-- ----------------------------
DROP TABLE IF EXISTS `acp_payment`;
CREATE TABLE `acp_payment` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_payment_groups
-- ----------------------------
DROP TABLE IF EXISTS `acp_payment_groups`;
CREATE TABLE `acp_payment_groups` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_payment_groups_patterns
-- ----------------------------
DROP TABLE IF EXISTS `acp_payment_groups_patterns`;
CREATE TABLE `acp_payment_groups_patterns` (
  `gid` int(11) NOT NULL,
  `pattern_id` int(11) NOT NULL,
  PRIMARY KEY (`gid`,`pattern_id`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_payment_patterns
-- ----------------------------
DROP TABLE IF EXISTS `acp_payment_patterns`;
CREATE TABLE `acp_payment_patterns` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_payment_patterns_server
-- ----------------------------
DROP TABLE IF EXISTS `acp_payment_patterns_server`;
CREATE TABLE `acp_payment_patterns_server` (
  `pattern_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  UNIQUE KEY `pattern` (`pattern_id`,`server_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_payment_patterns_usergroups
-- ----------------------------
DROP TABLE IF EXISTS `acp_payment_patterns_usergroups`;
CREATE TABLE `acp_payment_patterns_usergroups` (
  `pattern_id` int(11) NOT NULL,
  `usergroup_id` int(11) NOT NULL,
  UNIQUE KEY `pattern` (`pattern_id`,`usergroup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_payment_user
-- ----------------------------
DROP TABLE IF EXISTS `acp_payment_user`;
CREATE TABLE `acp_payment_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `pattern_id` int(11) NOT NULL,
  `date_start` int(11) NOT NULL DEFAULT '0',
  `date_end` int(11) NOT NULL DEFAULT '0',
  `add_mask_id` int(11) NOT NULL DEFAULT '0',
  `new_group` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_permissons_action
-- ----------------------------
DROP TABLE IF EXISTS `acp_permissons_action`;
CREATE TABLE `acp_permissons_action` (
  `usergroupid` int(11) NOT NULL,
  `action` int(11) NOT NULL,
  `bitmask` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `groupaction` (`usergroupid`,`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_permissons_action
-- ----------------------------
BEGIN;
INSERT INTO `acp_permissons_action` VALUES (1, 41, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 21, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 36, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 26, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 19, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 31, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 34, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 24, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 38, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 32, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 22, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 27, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 28, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 29, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 7, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 8, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 9, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 10, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 11, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 12, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 13, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 14, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 15, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 17, 0);
INSERT INTO `acp_permissons_action` VALUES (1, 18, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 41, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 21, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 36, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 26, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 19, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 31, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 34, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 24, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 38, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 32, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 22, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 27, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 28, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 29, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 7, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 8, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 9, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 10, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 11, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 12, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 13, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 14, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 15, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 17, 0);
INSERT INTO `acp_permissons_action` VALUES (2, 18, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 41, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 21, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 36, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 26, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 19, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 31, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 34, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 24, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 38, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 32, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 22, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 27, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 28, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 29, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 7, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 8, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 9, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 10, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 11, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 12, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 13, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 14, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 15, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 17, 0);
INSERT INTO `acp_permissons_action` VALUES (3, 18, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 41, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 21, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 36, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 26, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 19, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 31, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 34, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 24, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 38, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 32, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 22, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 27, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 28, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 29, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 7, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 8, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 9, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 10, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 11, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 12, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 13, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 14, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 15, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 17, 0);
INSERT INTO `acp_permissons_action` VALUES (4, 18, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 41, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 21, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 36, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 26, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 19, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 31, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 34, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 24, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 38, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 32, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 22, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 27, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 28, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 29, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 7, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 8, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 9, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 10, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 11, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 12, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 13, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 14, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 15, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 17, 0);
INSERT INTO `acp_permissons_action` VALUES (5, 18, 0);
COMMIT;

-- ----------------------------
-- Table structure for acp_players
-- ----------------------------
DROP TABLE IF EXISTS `acp_players`;
CREATE TABLE `acp_players` (
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
  `points` int(11) NOT NULL DEFAULT '100',
  UNIQUE KEY `userid` (`userid`),
  KEY `flag` (`flag`),
  FULLTEXT KEY `player_nick` (`player_nick`),
  FULLTEXT KEY `password` (`password`),
  FULLTEXT KEY `player_ip` (`player_ip`),
  FULLTEXT KEY `steamid` (`steamid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_players
-- ----------------------------
BEGIN;
INSERT INTO `acp_players` VALUES (1, 3, '', '', '', 'STEAM_0:0:12345678', 1451829626, 0, 'yes', 0, 100);
COMMIT;

-- ----------------------------
-- Table structure for acp_players_requests
-- ----------------------------
DROP TABLE IF EXISTS `acp_players_requests`;
CREATE TABLE `acp_players_requests` (
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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_players_requests
-- ----------------------------
BEGIN;
INSERT INTO `acp_players_requests` VALUES (1, 1, 1451829609, 3, 'gameAccounts', 'a:3:{s:4:\"flag\";s:1:\"3\";s:7:\"steamid\";s:18:\"STEAM_0:0:12345678\";s:6:\"userid\";s:1:\"1\";}', 1, 1451829626, 'root', 'Ваша заявка одобрена!');
COMMIT;

-- ----------------------------
-- Table structure for acp_products
-- ----------------------------
DROP TABLE IF EXISTS `acp_products`;
CREATE TABLE `acp_products` (
  `productid` varchar(25) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `version` varchar(25) NOT NULL DEFAULT '',
  `active` smallint(5) unsigned NOT NULL DEFAULT '1',
  `url` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`productid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_products
-- ----------------------------
BEGIN;
INSERT INTO `acp_products` VALUES ('ratingServers', 'Rating Servers', 'Monitoring and the rating game servers', '1.0', 1, 'http://www.a114games.com/community/threads/1939/');
INSERT INTO `acp_products` VALUES ('taskSheduler', 'Task Sheduler', 'Planning and automatically run your tasks', '1.3', 1, 'http://www.a114games.com/community/threads/1775/');
INSERT INTO `acp_products` VALUES ('gameAccounts', 'Game Accounts', 'The registration system game accounts', '1.3', 1, 'http://www.a114games.com/community/threads/1658/');
INSERT INTO `acp_products` VALUES ('gameBans', 'Game Bans', 'The ban system for game servers', '1.2', 1, 'http://www.a114games.com/community/threads/1659/');
INSERT INTO `acp_products` VALUES ('chatControl', 'Chat Control', 'The system control game chat', '3.6', 1, 'http://www.a114games.com/community/threads/1634/');
INSERT INTO `acp_products` VALUES ('hudManager', 'Hud Manager', 'Allows you to configure the player HUD', '1.2', 1, 'http://www.a114games.com/community/threads/1705/');
INSERT INTO `acp_products` VALUES ('multiserverRedirect', 'Multiserver Redirect', 'The system redirects to servers', '1.5', 1, 'http://www.a114games.com/content.php/99-Multiserver-Redirect');
INSERT INTO `acp_products` VALUES ('nickControl', 'Nick Control', 'The system control players nick', '2.0', 1, 'http://www.a114games.com/community/threads/1637/');
INSERT INTO `acp_products` VALUES ('gameStats', 'Game Stats', 'Server statistics, maps, players', '1.0', 1, 'http://www.a114games.com');
INSERT INTO `acp_products` VALUES ('userBank', 'User Bank', 'Bank and shop for users and players', '1.2', 1, 'http://www.a114games.com/community/threads/2031/');
INSERT INTO `acp_products` VALUES ('voteBanKick', 'Vote Ban and Kick', 'Voting for the ban and kick players', '1.5', 1, 'http://www.a114games.com/community/threads/1660/');
COMMIT;

-- ----------------------------
-- Table structure for acp_servers
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers`;
CREATE TABLE `acp_servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT '0',
  `gametype` varchar(32) NOT NULL DEFAULT '',
  `address` varchar(32) NOT NULL DEFAULT '',
  `hostname` varchar(100) NOT NULL DEFAULT 'Unknown',
  `description` text NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `rating` int(11) NOT NULL DEFAULT '0',
  `rating_vars` text NOT NULL,
  `vip` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cache` text NOT NULL,
  `cache_country` varchar(32) NOT NULL DEFAULT '',
  `cache_map` varchar(250) NOT NULL,
  `cache_map_path` tinyint(1) NOT NULL DEFAULT '0',
  `cache_players` int(11) NOT NULL,
  `cache_playersmax` int(11) NOT NULL,
  `cache_time` int(11) NOT NULL DEFAULT '0',
  `statistics` text NOT NULL,
  `votes_up` int(11) NOT NULL,
  `votes_down` int(11) NOT NULL,
  `opt_rcon` varchar(32) NOT NULL DEFAULT '',
  `opt_motd` varchar(250) NOT NULL DEFAULT '',
  `opt_city` int(11) NOT NULL DEFAULT '1',
  `opt_mode` int(11) NOT NULL DEFAULT '1',
  `opt_site` varchar(250) NOT NULL DEFAULT '',
  `opt_accounts` tinyint(1) NOT NULL DEFAULT '0',
  `opt_bansubnets` tinyint(1) NOT NULL DEFAULT '0',
  `opt_hudmanager` tinyint(1) NOT NULL DEFAULT '0',
  `opt_redirect` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `address` (`address`),
  KEY `opt_mode` (`opt_mode`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_servers
-- ----------------------------
BEGIN;
INSERT INTO `acp_servers` VALUES (1, 1, 'cstrike', '127.0.0.1:27015', 'Игровой сервер!', 'Описание сервера!', 1, 1, 'a:9:{s:11:\"description\";s:1:\"0\";s:6:\"viewed\";i:1;s:5:\"votes\";i:-2;s:6:\"online\";d:0;s:6:\"uptime\";d:0;s:2:\"pr\";i:0;s:2:\"cy\";i:0;s:6:\"banner\";s:1:\"0\";s:7:\"vklikes\";s:1:\"0\";}', 0, 1, 1451829660, 0, 'a:11:{s:8:\"favorite\";b:0;s:4:\"ping\";s:0:\"\";s:2:\"os\";s:0:\"\";s:4:\"pass\";s:0:\"\";s:3:\"vac\";s:0:\"\";s:3:\"map\";s:0:\"\";s:8:\"hostname\";s:0:\"\";s:7:\"players\";i:0;s:10:\"playersmax\";i:0;s:8:\"map_path\";i:0;s:7:\"country\";s:7:\"err.gif\";}', 'err.gif', '', 0, 0, 0, 1509876060, 'a:3:{s:1:\"d\";a:4:{s:5:\"votes\";a:24:{i:1455379200000;i:0;i:1455382800000;i:0;i:1455386400000;i:0;i:1455390000000;i:0;i:1455393600000;s:1:\"0\";i:1455397200000;i:0;i:1455400800000;i:0;i:1455404400000;i:0;i:1455408000000;i:0;i:1455411600000;i:0;i:1455415200000;i:0;i:1455418800000;i:0;i:1455422400000;i:0;i:1455426000000;i:0;i:1455429600000;s:1:\"2\";i:1455433200000;s:1:\"0\";i:1509390000000;s:1:\"0\";i:1509393600000;s:1:\"0\";i:1509429600000;s:1:\"0\";i:1509433200000;s:1:\"0\";i:1509858000000;s:1:\"0\";i:1509865200000;s:2:\"-2\";i:1509868800000;s:1:\"0\";i:1509872400000;s:1:\"0\";}s:6:\"viewed\";a:24:{i:1455379200000;i:0;i:1455382800000;i:0;i:1455386400000;i:0;i:1455390000000;i:0;i:1455393600000;s:1:\"0\";i:1455397200000;i:0;i:1455400800000;i:0;i:1455404400000;i:0;i:1455408000000;i:0;i:1455411600000;i:0;i:1455415200000;i:0;i:1455418800000;i:0;i:1455422400000;i:0;i:1455426000000;i:0;i:1455429600000;s:1:\"1\";i:1455433200000;s:1:\"0\";i:1509390000000;s:1:\"1\";i:1509393600000;s:1:\"0\";i:1509429600000;s:1:\"0\";i:1509433200000;s:1:\"0\";i:1509858000000;s:1:\"1\";i:1509865200000;s:1:\"0\";i:1509868800000;s:1:\"0\";i:1509872400000;s:1:\"0\";}s:6:\"uptime\";a:24:{i:1455379200000;i:0;i:1455382800000;i:0;i:1455386400000;i:0;i:1455390000000;i:0;i:1455393600000;d:100;i:1455397200000;i:0;i:1455400800000;i:0;i:1455404400000;i:0;i:1455408000000;i:0;i:1455411600000;i:0;i:1455415200000;i:0;i:1455418800000;i:0;i:1455422400000;i:0;i:1455426000000;i:0;i:1455429600000;d:100;i:1455433200000;d:100;i:1509390000000;d:0;i:1509393600000;d:0;i:1509429600000;d:0;i:1509433200000;d:0;i:1509858000000;d:0;i:1509865200000;d:0;i:1509868800000;d:0;i:1509872400000;d:0;}s:7:\"players\";a:24:{i:1455379200000;i:0;i:1455382800000;i:0;i:1455386400000;i:0;i:1455390000000;i:0;i:1455393600000;d:61.46000000000000085265128291212022304534912109375;i:1455397200000;i:0;i:1455400800000;i:0;i:1455404400000;i:0;i:1455408000000;i:0;i:1455411600000;i:0;i:1455415200000;i:0;i:1455418800000;i:0;i:1455422400000;i:0;i:1455426000000;i:0;i:1455429600000;d:0.65000000000000002220446049250313080847263336181640625;i:1455433200000;d:0;i:1509390000000;d:0;i:1509393600000;d:0;i:1509429600000;d:0;i:1509433200000;d:0;i:1509858000000;d:0;i:1509865200000;d:0;i:1509868800000;d:0;i:1509872400000;d:0;}}s:1:\"w\";a:4:{s:5:\"votes\";a:7:{i:1455148800000;i:0;i:1455235200000;i:0;i:1455321600000;i:0;i:1455408000000;i:2;i:1509321600000;i:0;i:1509408000000;i:0;i:1509840000000;i:-2;}s:6:\"viewed\";a:7:{i:1455148800000;i:0;i:1455235200000;i:0;i:1455321600000;i:0;i:1455408000000;i:1;i:1509321600000;i:1;i:1509408000000;i:0;i:1509840000000;i:1;}s:6:\"uptime\";a:7:{i:1455148800000;i:0;i:1455235200000;i:0;i:1455321600000;d:4.1699999999999999289457264239899814128875732421875;i:1455408000000;d:8.339999999999999857891452847979962825775146484375;i:1509321600000;d:0;i:1509408000000;d:0;i:1509840000000;d:0;}s:7:\"players\";a:7:{i:1455148800000;i:0;i:1455235200000;i:0;i:1455321600000;d:2.560000000000000053290705182007513940334320068359375;i:1455408000000;d:0.0299999999999999988897769753748434595763683319091796875;i:1509321600000;d:0;i:1509408000000;d:0;i:1509840000000;d:0;}}s:1:\"y\";a:4:{s:5:\"votes\";a:12:{i:1430438400000;i:0;i:1433116800000;i:0;i:1435708800000;i:0;i:1438387200000;i:0;i:1441065600000;i:0;i:1443657600000;i:0;i:1446336000000;i:0;i:1448928000000;i:0;i:1451606400000;i:0;i:1454284800000;i:2;i:1506816000000;i:0;i:1509494400000;i:-2;}s:6:\"viewed\";a:12:{i:1430438400000;i:0;i:1433116800000;i:0;i:1435708800000;i:0;i:1438387200000;i:0;i:1441065600000;i:0;i:1443657600000;i:0;i:1446336000000;i:0;i:1448928000000;i:0;i:1451606400000;i:0;i:1454284800000;i:1;i:1506816000000;i:1;i:1509494400000;i:1;}s:6:\"uptime\";a:12:{i:1430438400000;i:0;i:1433116800000;i:0;i:1435708800000;i:0;i:1438387200000;i:0;i:1441065600000;i:0;i:1443657600000;i:0;i:1446336000000;i:0;i:1448928000000;i:0;i:1451606400000;i:0;i:1454284800000;d:0.419999999999999984456877655247808434069156646728515625;i:1506816000000;d:0;i:1509494400000;d:0;}s:7:\"players\";a:12:{i:1430438400000;i:0;i:1433116800000;i:0;i:1435708800000;i:0;i:1438387200000;i:0;i:1441065600000;i:0;i:1443657600000;i:0;i:1446336000000;i:0;i:1448928000000;i:0;i:1451606400000;i:0;i:1454284800000;d:0.0899999999999999966693309261245303787291049957275390625;i:1506816000000;d:0;i:1509494400000;d:0;}}}', 10, 2, '', '', 1, 1, 'localhost', 1, 1, 1, 1);
COMMIT;

-- ----------------------------
-- Table structure for acp_servers_cities
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_cities`;
CREATE TABLE `acp_servers_cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_servers_cities
-- ----------------------------
BEGIN;
INSERT INTO `acp_servers_cities` VALUES (1, 'Москва');
INSERT INTO `acp_servers_cities` VALUES (2, 'Санкт-Петербург');
INSERT INTO `acp_servers_cities` VALUES (3, 'Екатеринбург');
INSERT INTO `acp_servers_cities` VALUES (4, 'Новосибирск');
INSERT INTO `acp_servers_cities` VALUES (5, 'Пермь');
INSERT INTO `acp_servers_cities` VALUES (6, 'Киев');
INSERT INTO `acp_servers_cities` VALUES (7, 'Казань');
INSERT INTO `acp_servers_cities` VALUES (8, 'Минск');
INSERT INTO `acp_servers_cities` VALUES (9, 'Нижний Новгород');
INSERT INTO `acp_servers_cities` VALUES (10, 'Братск');
INSERT INTO `acp_servers_cities` VALUES (11, 'Челябинск');
COMMIT;

-- ----------------------------
-- Table structure for acp_servers_modes
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_modes`;
CREATE TABLE `acp_servers_modes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_servers_modes
-- ----------------------------
BEGIN;
INSERT INTO `acp_servers_modes` VALUES (1, 'Public', '');
INSERT INTO `acp_servers_modes` VALUES (2, 'Classic', '');
INSERT INTO `acp_servers_modes` VALUES (3, 'GunGame', '');
INSERT INTO `acp_servers_modes` VALUES (4, 'War3FT', '');
INSERT INTO `acp_servers_modes` VALUES (5, 'Zombie', '');
INSERT INTO `acp_servers_modes` VALUES (6, 'DeathMatch', '');
INSERT INTO `acp_servers_modes` VALUES (7, 'Deathrun', '');
INSERT INTO `acp_servers_modes` VALUES (8, 'Kreedz', '');
INSERT INTO `acp_servers_modes` VALUES (9, 'HNS', '');
INSERT INTO `acp_servers_modes` VALUES (10, 'SuperHero', '');
INSERT INTO `acp_servers_modes` VALUES (11, 'Jail', '');
INSERT INTO `acp_servers_modes` VALUES (12, 'GodFather', '');
INSERT INTO `acp_servers_modes` VALUES (13, 'RolePlay', '');
INSERT INTO `acp_servers_modes` VALUES (14, 'Meat', '');
INSERT INTO `acp_servers_modes` VALUES (15, 'SoccerJam', '');
INSERT INTO `acp_servers_modes` VALUES (16, 'GTO', '');
INSERT INTO `acp_servers_modes` VALUES (17, 'MixPlay', '');
COMMIT;

-- ----------------------------
-- Table structure for acp_servers_options
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_options`;
CREATE TABLE `acp_servers_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `varname` varchar(64) NOT NULL DEFAULT '',
  `label` varchar(128) NOT NULL DEFAULT '',
  `type` enum('text','textarea','checkbox','select','boolean') NOT NULL DEFAULT 'text',
  `options` text NOT NULL,
  `verifycodes` varchar(64) NOT NULL DEFAULT '',
  `sortnum` smallint(6) NOT NULL DEFAULT '10',
  `productid` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `varname` (`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_servers_options
-- ----------------------------
BEGIN;
INSERT INTO `acp_servers_options` VALUES (1, 'opt_rcon', '@@opt_rcon@@', 'text', '', '', 10, 'ACPanel');
INSERT INTO `acp_servers_options` VALUES (2, 'opt_motd', '@@opt_motd@@', 'text', '', '', 20, 'ACPanel');
INSERT INTO `acp_servers_options` VALUES (3, 'opt_mode', '@@opt_mode@@', 'select', 'acp_servers_modes|id|name', 'select', 30, 'ACPanel');
INSERT INTO `acp_servers_options` VALUES (4, 'opt_city', '@@opt_city@@', 'select', 'acp_servers_cities|id|name', 'select', 30, 'ACPanel');
INSERT INTO `acp_servers_options` VALUES (5, 'opt_site', '@@opt_site@@', 'text', '', '', 10, 'ACPanel');
INSERT INTO `acp_servers_options` VALUES (6, 'opt_accounts', '@@opt_accounts@@', 'boolean', '', '', 10, 'gameAccounts');
INSERT INTO `acp_servers_options` VALUES (7, 'opt_bansubnets', '@@opt_bansubnets@@', 'boolean', '', '', 10, 'gameBans');
INSERT INTO `acp_servers_options` VALUES (8, 'opt_hudmanager', '@@opt_hudmanager@@', 'boolean', '', '', 10, 'hudManager');
INSERT INTO `acp_servers_options` VALUES (9, 'opt_redirect', '@@opt_redirect@@', 'boolean', '', '', 10, 'multiserverRedirect');
COMMIT;

-- ----------------------------
-- Table structure for acp_servers_rating_temp
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_rating_temp`;
CREATE TABLE `acp_servers_rating_temp` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_servers_rating_temp
-- ----------------------------
BEGIN;
INSERT INTO `acp_servers_rating_temp` VALUES (1, 1, 1, 1, 'a:9:{s:11:\"description\";s:1:\"0\";s:6:\"viewed\";i:1;s:5:\"votes\";i:-2;s:6:\"online\";d:0;s:6:\"uptime\";d:0;s:2:\"pr\";i:0;s:2:\"cy\";i:0;s:6:\"banner\";s:1:\"0\";s:7:\"vklikes\";s:1:\"0\";}', 10, 2, 0, 0, 0, 1509391612, 0, 1509875340, 0, 0);
COMMIT;

-- ----------------------------
-- Table structure for acp_servers_redirect
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_redirect`;
CREATE TABLE `acp_servers_redirect` (
  `server_id` int(11) NOT NULL,
  `current_map` varchar(32) NOT NULL,
  `current_pwd` varchar(32) NOT NULL,
  `current_players` int(8) NOT NULL DEFAULT '0',
  `current_maxplayers` int(8) NOT NULL DEFAULT '0',
  `current_viewplayers` int(8) NOT NULL DEFAULT '0',
  `current_admins` int(8) NOT NULL DEFAULT '0',
  `current_reserved_slots` tinyint(1) NOT NULL DEFAULT '0',
  `current_timestamp` int(1) NOT NULL DEFAULT '0',
  `current_online` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `server_id` (`server_id`),
  KEY `timestamp` (`current_timestamp`),
  KEY `online` (`current_online`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_servers_statistics
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_statistics`;
CREATE TABLE `acp_servers_statistics` (
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
) ENGINE=InnoDB AUTO_INCREMENT=1145 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_servers_statistics
-- ----------------------------
BEGIN;
INSERT INTO `acp_servers_statistics` VALUES (1143, 1, 0, 0, '', 0, 0, 1509876000);
INSERT INTO `acp_servers_statistics` VALUES (1144, 1, 0, 0, '', 0, 0, 1509876060);
COMMIT;

-- ----------------------------
-- Table structure for acp_servers_viewed
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_viewed`;
CREATE TABLE `acp_servers_viewed` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `server_id` int(11) unsigned NOT NULL,
  `visitor_ip` varchar(255) DEFAULT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `server_id` (`server_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_servers_votes
-- ----------------------------
DROP TABLE IF EXISTS `acp_servers_votes`;
CREATE TABLE `acp_servers_votes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(11) unsigned NOT NULL,
  `value` tinyint(1) unsigned NOT NULL,
  `vote_value` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(255) DEFAULT NULL,
  `date` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_servers_votes
-- ----------------------------
BEGIN;
INSERT INTO `acp_servers_votes` VALUES (6, 1, 0, -2, '', 1509861497, 1);
COMMIT;

-- ----------------------------
-- Table structure for acp_stats_maps
-- ----------------------------
DROP TABLE IF EXISTS `acp_stats_maps`;
CREATE TABLE `acp_stats_maps` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_stats_players
-- ----------------------------
DROP TABLE IF EXISTS `acp_stats_players`;
CREATE TABLE `acp_stats_players` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_stats_players_rank
-- ----------------------------
DROP TABLE IF EXISTS `acp_stats_players_rank`;
CREATE TABLE `acp_stats_players_rank` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_stats_weapons
-- ----------------------------
DROP TABLE IF EXISTS `acp_stats_weapons`;
CREATE TABLE `acp_stats_weapons` (
  `weaponid` int(10) unsigned NOT NULL,
  `name` varchar(32) NOT NULL,
  `code` varchar(32) NOT NULL,
  `modifier` float(10,2) NOT NULL DEFAULT '1.00',
  PRIMARY KEY (`weaponid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_stats_weapons
-- ----------------------------
BEGIN;
INSERT INTO `acp_stats_weapons` VALUES (1, 'Sig Sauer P-228', 'p228', 1.50);
INSERT INTO `acp_stats_weapons` VALUES (3, 'Steyr Scout', 'scout', 1.60);
INSERT INTO `acp_stats_weapons` VALUES (4, 'High Explosive Grenade', 'hegrenade', 1.80);
INSERT INTO `acp_stats_weapons` VALUES (5, 'Benelli/H&K M4 Super 90 XM1014', 'xm1014', 1.40);
INSERT INTO `acp_stats_weapons` VALUES (6, 'C4', 'c4', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (7, 'Ingram MAC-10', 'mac10', 1.25);
INSERT INTO `acp_stats_weapons` VALUES (8, 'Steyr Aug', 'aug', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (9, 'SMOKEGRENADE', 'smokegrenade', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (10, 'Dual Beretta 96G Elite', 'elite', 1.50);
INSERT INTO `acp_stats_weapons` VALUES (11, 'FN Five-Seven', 'fiveseven', 1.50);
INSERT INTO `acp_stats_weapons` VALUES (12, 'H&K UMP45', 'ump45', 1.25);
INSERT INTO `acp_stats_weapons` VALUES (13, 'SG550', 'sg550', 1.70);
INSERT INTO `acp_stats_weapons` VALUES (14, 'Galil', 'galil', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (15, 'Fusil Automatique', 'famas', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (16, 'H&K USP .45 Tactical', 'usp', 1.50);
INSERT INTO `acp_stats_weapons` VALUES (17, 'Glock 18 Select Fire', 'glock18', 1.50);
INSERT INTO `acp_stats_weapons` VALUES (18, 'Arctic Warfare Magnum (Police)', 'awp', 1.40);
INSERT INTO `acp_stats_weapons` VALUES (19, 'H&K MP5-Navy', 'mp5navy', 1.25);
INSERT INTO `acp_stats_weapons` VALUES (20, 'M249 PARA Light Machine Gun', 'm249', 0.80);
INSERT INTO `acp_stats_weapons` VALUES (21, 'Benelli M3 Super 90 Combat', 'm3', 1.40);
INSERT INTO `acp_stats_weapons` VALUES (22, 'Colt M4A1 Carbine', 'm4a1', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (23, 'Steyr Tactical Machine Pistol', 'tmp', 1.25);
INSERT INTO `acp_stats_weapons` VALUES (24, 'H&K G3/SG1 Sniper Rifle', 'g3sg1', 1.40);
INSERT INTO `acp_stats_weapons` VALUES (25, 'FLASHBANG', 'flashbang', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (26, 'Desert Eagle .50AE', 'deagle', 1.50);
INSERT INTO `acp_stats_weapons` VALUES (27, 'Sig Sauer SG-552 Commando', 'sg552', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (28, 'Kalashnikov AK-47', 'ak47', 1.00);
INSERT INTO `acp_stats_weapons` VALUES (29, 'Knife', 'knife', 1.80);
INSERT INTO `acp_stats_weapons` VALUES (30, 'FN P90', 'p90', 1.25);
COMMIT;

-- ----------------------------
-- Table structure for acp_stats_weapons_data
-- ----------------------------
DROP TABLE IF EXISTS `acp_stats_weapons_data`;
CREATE TABLE `acp_stats_weapons_data` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for acp_ticket_type
-- ----------------------------
DROP TABLE IF EXISTS `acp_ticket_type`;
CREATE TABLE `acp_ticket_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  `varname` varchar(64) NOT NULL DEFAULT '',
  `productid` varchar(25) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_ticket_type
-- ----------------------------
BEGIN;
INSERT INTO `acp_ticket_type` VALUES (1, '@@t_create_acc_by_nick@@', 'player_nick', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (2, '@@t_create_acc_by_ip@@', 'player_ip', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (3, '@@t_create_acc_by_steam@@', 'steamid', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (4, '@@t_change_nick@@', 'player_nick', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (5, '@@t_change_ip@@', 'player_ip', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (6, '@@t_change_steam@@', 'steamid', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (7, '@@t_change_auth_nick@@', 'player_nick', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (8, '@@t_change_auth_ip@@', 'player_ip', 'gameAccounts');
INSERT INTO `acp_ticket_type` VALUES (9, '@@t_change_auth_steam@@', 'steamid', 'gameAccounts');
COMMIT;

-- ----------------------------
-- Table structure for acp_usergroups
-- ----------------------------
DROP TABLE IF EXISTS `acp_usergroups`;
CREATE TABLE `acp_usergroups` (
  `usergroupid` int(10) NOT NULL AUTO_INCREMENT,
  `usergroupname` varchar(64) NOT NULL,
  `admin_access` enum('yes','no') NOT NULL DEFAULT 'no',
  `read_category` text NOT NULL,
  `edit_pages` enum('yes','no') NOT NULL DEFAULT 'no',
  `mon_favorites_limit` int(11) NOT NULL DEFAULT '3',
  `weight` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`usergroupid`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_usergroups
-- ----------------------------
BEGIN;
INSERT INTO `acp_usergroups` VALUES (1, 'Administrative', 'yes', '1,17,42,16,102,67,68,69,77,96,97,2,7,4,8,23,24,34,36,35,12,25,10,70,37,29,28,9,6,13,32,31,14,15,30,26,11,19,21,39,41,40,18,22,20,33,27,43,45,44,3,38,5,80,81,82,83,71,72,73,74,75,76,105,78,79,46,47,49,48,50,51,52,54,53,55,56,57,59,58,60,61,62,64,63,65,84,85,89,91,90,86,88,87,92,93,94,95,98,101,100,99', 'yes', 21, 1000);
INSERT INTO `acp_usergroups` VALUES (2, 'Registered', 'no', '1,17,42,16,102,67,68,69,77,96,97', 'no', 4, 0);
INSERT INTO `acp_usergroups` VALUES (3, 'Unregistered / Unconfirmed', 'no', '1,17,42,16,102,67,68,69,77,96,97', 'no', 2, 0);
INSERT INTO `acp_usergroups` VALUES (4, 'Пользователи, ожидающие подтверждения', 'no', '1,17,16', 'no', 3, 0);
INSERT INTO `acp_usergroups` VALUES (5, 'Moderating', 'no', '1,17,42,16,102,67,68,69,77,96,97,2,7,10,70,37,6,13,14,3,38,80,81,82,83,71,72,73,74,75,76,105,46,47,49,50,51,52,54,55,56,57,59,58,60,61,62,64,63,65', 'no', 5, 900);
COMMIT;

-- ----------------------------
-- Table structure for acp_usergroups_permissions
-- ----------------------------
DROP TABLE IF EXISTS `acp_usergroups_permissions`;
CREATE TABLE `acp_usergroups_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(128) DEFAULT NULL,
  `varname` varchar(128) DEFAULT NULL,
  `description` varchar(128) DEFAULT NULL,
  `type` enum('text','textarea','checkbox','select','bitmask','boolean') NOT NULL DEFAULT 'text',
  `options` text,
  `verifycodes` varchar(64) DEFAULT NULL,
  `productid` varchar(25) NOT NULL DEFAULT 'ACPanel',
  `perm_sort` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `varname` (`section`,`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_usergroups_permissions
-- ----------------------------
BEGIN;
INSERT INTO `acp_usergroups_permissions` VALUES (1, 'main', 'read_category', '@@help_read_category@@', 'select', 'acp_category|categoryid|title', 'multiple', 'ACPanel', 20);
INSERT INTO `acp_usergroups_permissions` VALUES (2, 'main', NULL, '@@help_main@@', 'text', NULL, NULL, 'ACPanel', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (3, 'main', 'admin_access', '@@help_admin_access@@', 'boolean', NULL, NULL, 'ACPanel', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (4, 'main', 'edit_pages', '@@help_edit_pages@@', 'boolean', NULL, '', 'ACPanel', 30);
INSERT INTO `acp_usergroups_permissions` VALUES (5, 'monitoring', NULL, '@@help_monitoring@@', 'text', NULL, NULL, 'ACPanel', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (6, 'monitoring', 'mon_favorites_limit', '@@help_mon_favorites_limit@@', 'text', 'size=5', 'numeric', 'ACPanel', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (7, 'main', 'general_perm_options', '@@help_general_perm_options@@', 'bitmask', NULL, NULL, 'ACPanel', 40);
INSERT INTO `acp_usergroups_permissions` VALUES (8, 'main', 'general_perm_categories', '@@help_general_perm_categories@@', 'bitmask', NULL, NULL, 'ACPanel', 50);
INSERT INTO `acp_usergroups_permissions` VALUES (9, 'main', 'general_perm_blocks', '@@help_general_perm_blocks@@', 'bitmask', NULL, NULL, 'ACPanel', 60);
INSERT INTO `acp_usergroups_permissions` VALUES (10, 'main', 'general_perm_products', '@@help_general_perm_products@@', 'bitmask', NULL, NULL, 'ACPanel', 70);
INSERT INTO `acp_usergroups_permissions` VALUES (11, 'main', 'general_perm_testing', '@@help_general_perm_testing@@', 'bitmask', NULL, NULL, 'ACPanel', 80);
INSERT INTO `acp_usergroups_permissions` VALUES (12, 'main', 'general_perm_users', '@@help_general_perm_users@@', 'bitmask', NULL, NULL, 'ACPanel', 90);
INSERT INTO `acp_usergroups_permissions` VALUES (13, 'main', 'general_perm_usergroups', '@@help_general_perm_usergroups@@', 'bitmask', NULL, NULL, 'ACPanel', 100);
INSERT INTO `acp_usergroups_permissions` VALUES (14, 'main', 'general_perm_langs', '@@help_general_perm_langs@@', 'bitmask', NULL, NULL, 'ACPanel', 110);
INSERT INTO `acp_usergroups_permissions` VALUES (15, 'main', 'general_perm_phrases', '@@help_general_perm_phrases@@', 'bitmask', NULL, NULL, 'ACPanel', 120);
INSERT INTO `acp_usergroups_permissions` VALUES (16, 'tools', NULL, '@@help_perm_tools@@', 'text', NULL, NULL, 'ACPanel', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (17, 'main', 'servers_perm_control', '@@help_servers_perm_control@@', 'bitmask', NULL, NULL, 'ACPanel', 130);
INSERT INTO `acp_usergroups_permissions` VALUES (18, 'main', 'general_perm_logs', '@@help_general_perm_logs@@', 'bitmask', NULL, NULL, 'ACPanel', 140);
INSERT INTO `acp_usergroups_permissions` VALUES (19, 'tools', 'tools_perm_cron', '@@help_tools_perm_cron@@', 'bitmask', NULL, NULL, 'taskSheduler', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (20, 'game_accounts', NULL, '@@help_game_accounts@@', 'text', NULL, NULL, 'gameAccounts', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (21, 'game_accounts', 'ga_perm_players', '@@help_ga_perm_players@@', 'bitmask', NULL, NULL, 'gameAccounts', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (22, 'game_accounts', 'ga_perm_masks', '@@help_ga_perm_masks@@', 'bitmask', NULL, NULL, 'gameAccounts', 20);
INSERT INTO `acp_usergroups_permissions` VALUES (23, 'tickets', '', '@@help_tickets@@', 'text', NULL, NULL, 'ACPanel', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (24, 'tickets', 'perm_tickets', '@@help_perm_tickets@@', 'bitmask', NULL, NULL, 'ACPanel', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (25, 'game_bans', NULL, '@@help_game_bans@@', 'text', NULL, NULL, 'gameBans', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (26, 'game_bans', 'gb_perm_players', '@@help_gb_perm_players@@', 'bitmask', NULL, NULL, 'gameBans', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (27, 'game_bans', 'gb_perm_players_my', '@@help_gb_perm_players_my@@', 'bitmask', NULL, NULL, 'gameBans', 20);
INSERT INTO `acp_usergroups_permissions` VALUES (28, 'game_bans', 'gb_perm_reasons', '@@help_gb_perm_reasons@@', 'bitmask', NULL, NULL, 'gameBans', 30);
INSERT INTO `acp_usergroups_permissions` VALUES (29, 'game_bans', 'gb_perm_subnets', '@@help_gb_perm_subnets@@', 'bitmask', NULL, NULL, 'gameBans', 40);
INSERT INTO `acp_usergroups_permissions` VALUES (30, 'chat_control', NULL, '@@help_chat_control@@', 'text', NULL, NULL, 'chatControl', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (31, 'chat_control', 'cc_perm_patterns', '@@help_cc_perm_patterns@@', 'bitmask', NULL, NULL, 'chatControl', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (32, 'chat_control', 'cc_perm_commands', '@@help_cc_perm_commands@@', 'bitmask', NULL, NULL, 'chatControl', 20);
INSERT INTO `acp_usergroups_permissions` VALUES (33, 'hudm', NULL, '@@help_hudm@@', 'text', NULL, NULL, 'hudManager', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (34, 'hudm', 'perm_hudm', '@@help_perm_hudm@@', 'bitmask', NULL, NULL, 'hudManager', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (35, 'nick_control', NULL, '@@help_chat_control@@', 'text', NULL, NULL, 'nickControl', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (36, 'nick_control', 'nc_perm_patterns', '@@help_nc_perm_patterns@@', 'bitmask', NULL, NULL, 'nickControl', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (37, 'user_bank', NULL, '@@help_user_bank@@', 'text', NULL, NULL, 'userBank', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (38, 'user_bank', 'ub_perm_payment', '@@help_ub_perm_payment@@', 'bitmask', NULL, NULL, 'userBank', 10);
INSERT INTO `acp_usergroups_permissions` VALUES (39, 'main', 'weight', '@@help_weight@@', 'text', NULL, NULL, 'ACPanel', 5);
INSERT INTO `acp_usergroups_permissions` VALUES (40, 'vbk', NULL, '@@help_vbk@@', 'text', NULL, NULL, 'voteBanKick', 0);
INSERT INTO `acp_usergroups_permissions` VALUES (41, 'vbk', 'vbk_perm', '@@help_vbk_perm@@', 'bitmask', NULL, NULL, 'voteBanKick', 10);
COMMIT;

-- ----------------------------
-- Table structure for acp_users
-- ----------------------------
DROP TABLE IF EXISTS `acp_users`;
CREATE TABLE `acp_users` (
  `uid` int(12) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `mail` varchar(60) NOT NULL DEFAULT '',
  `icq` varchar(32) NOT NULL DEFAULT '',
  `usergroupid` int(11) NOT NULL,
  `ipaddress` varchar(60) NOT NULL DEFAULT '',
  `secretkey` varchar(32) NOT NULL DEFAULT '',
  `code_activated` varchar(32) NOT NULL DEFAULT '',
  `reg_date` int(1) NOT NULL DEFAULT '0',
  `last_visit` int(1) NOT NULL DEFAULT '0',
  `timezone` varchar(4) NOT NULL DEFAULT '0',
  `hid` varchar(128) DEFAULT NULL,
  `avatar` varchar(60) NOT NULL DEFAULT '',
  `user_state` enum('moderated','email_confirm','valid') NOT NULL DEFAULT 'valid',
  `money` decimal(18,2) NOT NULL DEFAULT '0.00',
  `real_groupid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  KEY `usergroupid` (`usergroupid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of acp_users
-- ----------------------------
BEGIN;
INSERT INTO `acp_users` VALUES (1, 'root', 'e10adc3949ba59abbe56e057f20f883e', 'admin@localhost.ua', '', 1, '127.0.0.1', 'bae4e7c5b764a63ffa31550c302c0bee', '', 1399011660, 1509873131, '6', '', '', 'valid', 0.00, 0);
COMMIT;

-- ----------------------------
-- Table structure for acp_vbk_logs
-- ----------------------------
DROP TABLE IF EXISTS `acp_vbk_logs`;
CREATE TABLE `acp_vbk_logs` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(1) NOT NULL DEFAULT '0',
  `vote_type` enum('ban','kick') NOT NULL DEFAULT 'ban',
  `vote_result` smallint(4) NOT NULL DEFAULT '0',
  `vote_all` smallint(4) NOT NULL DEFAULT '0',
  `vote_yes` smallint(4) NOT NULL DEFAULT '0',
  `vote_need` smallint(4) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL,
  `vote_player_ip` varchar(100) DEFAULT NULL,
  `vote_player_id` varchar(50) NOT NULL DEFAULT '0',
  `vote_player_nick` varchar(100) NOT NULL DEFAULT 'Unknown',
  `nom_player_ip` varchar(100) DEFAULT NULL,
  `nom_player_id` varchar(50) NOT NULL DEFAULT '0',
  `nom_player_nick` varchar(100) NOT NULL DEFAULT 'Unknown',
  `vote_reason` varchar(255) NOT NULL DEFAULT '',
  `ban_length` varchar(20) NOT NULL DEFAULT '',
  `server_ip` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`vote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;

<?php

// ###############################################################################
// Vote Ban & Kick version 1.5
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
	
			if($old_version < 12)
			{
				$error[] = "please update acpanel and try again";
			}
			elseif($old_version > $new_version)
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
		$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE link = 'p_servers' LIMIT 1");
		
		if($result_category)
		{
			if( ($parentid = category_add($result_category, 30, "p_vbk_logs", "@@vbk_logs@@", "", "voteBanKick")) == 0 )
			{
				$error[] = "failed to add a category: @@vbk_logs@@";
			}
		}
		else
		{
			$error[] = "the product of 'srvControl' is out";
		}

		if(empty($error))
		{
			$result = $db->Query("
				CREATE TABLE IF NOT EXISTS `acp_vbk_logs` (
					`vote_id` int(11) NOT NULL auto_increment,
					`timestamp` int(1) NOT NULL default '0',
					`vote_type` enum('ban','kick') NOT NULL default 'ban',
					`vote_result` smallint(4) NOT NULL default '0',
					`vote_all` smallint(4) NOT NULL default '0',
					`vote_yes` smallint(4) NOT NULL default '0',
					`vote_need` smallint(4) NOT NULL default '0',
					`uid` int(11) NOT NULL,
					`vote_player_ip` varchar(100) default NULL,
					`vote_player_id` varchar(50) NOT NULL default '0',
					`vote_player_nick` varchar(100) NOT NULL default 'Unknown',
					`nom_player_ip` varchar(100) default NULL,
					`nom_player_id` varchar(50) NOT NULL default '0',
					`nom_player_nick` varchar(100) NOT NULL default 'Unknown',
					`vote_reason` varchar(255) NOT NULL default '',
					`ban_length` varchar(20) NOT NULL default '',
					`server_ip` varchar(100) NOT NULL default '',
					PRIMARY KEY  (`vote_id`)
				) ENGINE=MyISAM
			");

			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('vbk', null, '@@help_vbk@@', 'text', 'voteBanKick', '0')");
			$result = $db->Query("INSERT INTO `acp_usergroups_permissions` (`section`, `varname`, `description`, `type`, `productid`, `perm_sort`) VALUES ('vbk', 'vbk_perm', '@@help_vbk_perm@@', 'bitmask', 'voteBanKick', '10')");
		}
	}
}
else
{
	$result_category = $db->Query("SELECT categoryid FROM `acp_category` WHERE productid = 'voteBanKick' ORDER BY catleft LIMIT 1");
	
	if(!category_delete($result_category))
	{
		$error = "Error deleting product categories.";
	}
	else
	{
		$result = $db->Query("DELETE FROM `acp_lang_words` WHERE productid = 'voteBanKick'");
		$result = $db->Query("DELETE FROM `acp_lang_pages` WHERE productid = 'voteBanKick'");
		$result = $db->Query("DELETE a, b FROM `acp_usergroups_permissions` AS a 
			LEFT JOIN `acp_permissons_action` AS b ON a.id = b.action WHERE a.productid = 'voteBanKick'");

		$result = $db->Query("DROP TABLE IF EXISTS `acp_vbk_logs`"); 
	}
}

?>
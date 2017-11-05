<?php

// ###############################################################################
// Multiserver Redirect version 1.5
// ###############################################################################

if($product_install)
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
		if( $result_alter = $db->Query("ALTER TABLE `acp_servers` ADD `opt_redirect` TINYINT(1) NOT NULL default '0'") )
			$result_insert = $db->Query("INSERT INTO `acp_servers_options` (varname, label, type, productid) VALUES ('opt_redirect', '@@opt_redirect@@', 'boolean', 'multiserverRedirect')");

		$result = $db->Query("CREATE TABLE IF NOT EXISTS `acp_servers_redirect` (
			`server_id` int(11) NOT NULL,
			`current_map` varchar(32) NOT NULL,
			`current_pwd` varchar(32) NOT NULL,
			`current_players` int(8) NOT NULL default '0',
			`current_maxplayers` int(8) NOT NULL default '0',
			`current_viewplayers` int(8) NOT NULL default '0',
			`current_admins` int(8) NOT NULL default '0',
			`current_reserved_slots` tinyint(1) NOT NULL default '0',
			`current_timestamp` int(1) NOT NULL default '0',
			`current_online` tinyint(1) NOT NULL default '0',
			UNIQUE KEY `server_id` (`server_id`),
			KEY `timestamp` (`current_timestamp`),
			KEY `online` (`current_online`)
		) ENGINE=InnoDB");
	}
}
else
{
	if( $result = $db->Query("ALTER TABLE `acp_servers` DROP `opt_redirect`") )
		$result_delete = $db->Query("DELETE FROM `acp_servers_options` WHERE varname = 'opt_redirect'");

	$result = $db->Query("DROP TABLE IF EXISTS `acp_servers_redirect`"); 
}

?>
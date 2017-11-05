<?php
 
define("IN_ACP", true);

if( !is_null($this->db()) )
{
	$query = $this->db()->Query("SELECT bid FROM `acp_bans` WHERE ".time()." > (ban_created+(ban_length*60)) AND ban_length > 0", array());
	
	if( is_array($query) )
	{
		foreach( $query as $obj )
		{
			$ids[] = $obj->bid;
		}
	}
	else
	{
	    $ids = ($query) ? array($query) : array();
	}
	 
	if( !empty($ids) )
	{
		$arguments = array('ids' => $ids);
		$query = $this->db()->Query("INSERT INTO `acp_bans_history` (bid, player_ip, player_id, player_nick, cookie_ip, admin_ip, admin_id, admin_nick, admin_uid, ban_type, ban_reason, ban_created, ban_length, server_ip, server_name, unban_created)
			SELECT bid, player_ip, player_id, player_nick, cookie_ip, admin_ip, admin_id, admin_nick, admin_uid, ban_type, ban_reason, ban_created, ban_length, server_ip, server_name, UNIX_TIMESTAMP()
			FROM `acp_bans` WHERE bid IN ('{ids}')
			ON DUPLICATE KEY UPDATE player_ip = VALUES(player_ip), player_id = VALUES(player_id), player_nick = VALUES(player_nick), cookie_ip = VALUES(cookie_ip), admin_ip = VALUES(admin_ip), admin_id = VALUES(admin_ip),
			admin_nick = VALUES(admin_nick), admin_uid = VALUES(admin_uid), ban_type = VALUES(ban_type), ban_reason = VALUES(ban_reason), ban_created = VALUES(ban_created), ban_length = VALUES(ban_length),
			server_ip = VALUES(server_ip), server_name = VALUES(server_name)", $arguments);
		
		if( $query )
		{
			$query = $this->db()->Query("DELETE FROM `acp_bans` WHERE bid IN ('{ids}')", $arguments);
		}
	}
}
 
?>
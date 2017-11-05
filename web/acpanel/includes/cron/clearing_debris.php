<?php
 
define("IN_ACP", true);
 
if( !is_null($this->db()) )
{
	$query = $this->db()->Query("DELETE FROM `acp_logs` WHERE TIMESTAMPDIFF(DAY, FROM_UNIXTIME(timestamp), NOW()) > 14");
	$query = $this->db()->Query("DELETE FROM `acp_nick_logs` WHERE TIMESTAMPDIFF(DAY, FROM_UNIXTIME(timestamp), NOW()) > 7");
    $query = $this->db()->Query("DELETE FROM `acp_chat_logs` WHERE TIMESTAMPDIFF(DAY, FROM_UNIXTIME(timestamp), NOW()) > 7");
    $query = $this->db()->Query("DELETE FROM `acp_cron_log` WHERE TIMESTAMPDIFF(DAY, FROM_UNIXTIME(dateline), NOW()) > 1");
    $query = $this->db()->Query("DELETE FROM `acp_bans_history` WHERE TIMESTAMPDIFF(DAY, FROM_UNIXTIME(unban_created), NOW()) > 90");
}
 
?>
<?php

$arguments = array('limit' => $config['gb_block_admins_max'], 'time' => get_datetime(time(), "Y-m-d"));
$result = $db->Query("SELECT  count(*) AS count, temptable.admin_nick, temptable.admin_uid AS uid, u.username, u.avatar FROM (
	(SELECT admin_nick, admin_uid FROM `acp_bans` WHERE admin_uid != 0 AND FROM_UNIXTIME(ban_created,'%Y-%m-%d') = '{time}') UNION ALL
	(SELECT admin_nick, admin_uid FROM `acp_bans_history` WHERE admin_uid != 0 AND FROM_UNIXTIME(ban_created,'%Y-%m-%d') = '{time}')) temptable 
	LEFT JOIN `acp_users` u ON u.uid = temptable.admin_uid 
	WHERE u.username IS NOT NULL 
	GROUP BY uid ORDER BY count DESC LIMIT {limit}", $arguments, $config['sql_debug']);

if( is_array($result) )
{
	$first = true;

	foreach( $result as $obj )
	{
		$avatar_size = ($first) ? "m" : "s";
		switch($ext_auth_type)
		{
			case "xf":

				$xfUser = $xf->getUserInfo($obj->uid);
				$obj->avatar = ($obj->avatar) ? $xf->getAvatarFilePath($avatar_size, $obj->uid).'?'.$obj->avatar : $config['xfAuth']['forumUrl'].'styles/'.$config["template"].'/xenforo/avatars/avatar_'.( ($xfUser["gender"]) ? $xfUser["gender"]."_" : "" ).$avatar_size.'.png';
				$obj->avatar_date = $xfUser['avatar_date'];
				break;

			default:

				$obj->avatar_date = (strlen($obj->avatar)) ? 1 : 0;	
				$obj->avatar = ($obj->avatar) ? 'acpanel/images/avatars/'.$avatar_size.'/'.$obj->avatar : 'acpanel/images/noavatar_'.$avatar_size.'.gif';
				break;
		}

		if( $first )
		{
			$best_admin = (array)$obj;
		}
		else
		{
			$best_admins[] = (array)$obj;
		}

		$first = false;
	}
}
else
{
	$error_msg = "@@not_best_admins_today@@";
}

if( isset($best_admins) ) $smarty->assign("bas", $best_admins);
if( isset($best_admin) ) $smarty->assign("ba", $best_admin);
if( isset($error_msg) ) $smarty->assign("error_msg", $error_msg);

?>
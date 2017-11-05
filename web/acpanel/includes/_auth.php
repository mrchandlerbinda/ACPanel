<?php

if( !defined('IN_ACP') ) die("Hacking attempt!");

// ###############################################################################
// CHECK USER
// ###############################################################################

$userID = 0;
$userinfo = array();
switch($ext_auth_type)
{
	case "xf":

		if( $xf->isLoggedIn() )
		{
			$userID = $xf->getUserId();
		}
		break;
}

$product_UB = getProduct("userBank");
$userinfo = get_user_info($userID, $product_UB);
if( $userID )
{
	if( $ext_auth_type == "xf" )
	{
		$xfUser = $xf->getCurrentUser();

		if( $xfUser['avatar_date'] )
		{
			if( $xfUser['user_id'] < 1000 )
			{
				$xfUser['avatar_url'] = "0/".$xfUser['user_id'].".jpg";
			}
			else
			{
				$xfUser['avatar_url'] = substr((string)$xfUser['user_id'], 0, 1)."/".$xfUser['user_id'].".jpg";
			}
		}
		$xfUser['custom_fields'] = unserialize($xfUser['custom_fields']);
		$userinfo = array_merge($userinfo, array('xf' => $xfUser));
	}
}

if( $userinfo['uid'] )
{
if( !empty($product_UB) )
        {
                if( $userinfo['real_groupid'] > 0 && $userinfo['new_groupid'] > 0 )
                {
            if( $userinfo['new_groupid'] != $userinfo['current_groupid'] )
                $set_group = $userinfo['new_groupid'];
 
                        if( $userinfo['new_groupid'] == $userinfo['real_groupid'] )
                                $set_real = 0;
 
                        if( isset($set_group) || isset($set_real) )
                        {
                                $separator = "";
                                if( isset($set_group) && isset($set_real) )
                                        $separator = ",";
   
                                $sqlconds = ( (!isset($set_group)) ? "" : " usergroupid = ".$set_group ).$separator.( (!isset($set_real)) ? "" : " real_groupid = ".$set_real );
   
                                if( isset($userinfo['xf']) )
                                {
                                        if( isset($set_group) ) $xf->setUserData($userinfo['uid'], array("user_group_id" => $set_group));
                                   
                                        if( isset($set_real) ) $db->Query("UPDATE `acp_users` SET real_groupid = 0 WHERE uid = ".$userinfo['uid'], array(), $config['sql_debug']);
                                }
                                else
                                {
                                        $db->Query("UPDATE `acp_users` SET".$sqlconds." WHERE uid = ".$userinfo['uid'], array(), $config['sql_debug']);
                                }
                        }
                }
        }

	if( isset($_GET['logout']) )
	{
		if( $userID )
		{
			if( $ext_auth_type == "xf" )
			{
				$xf->logout();
			}
		}

		acp_logout();
	}
}

?>
<?php

if( !$userinfo['uid'] )
{
    $err = true;
}
else
{
    if( !is_numeric($userinfo['uid']) )
    {
        $err = true;
    }
    else
    {
        $result = $db->Query("SELECT u.uid, u.avatar, p.player_nick, p.last_time, p.online, p.points, u.money FROM `acp_users` u
            LEFT JOIN `acp_players` p ON p.userid = u.uid
            WHERE u.uid = ".$userinfo['uid']." LIMIT 1", array(), $config['sql_debug']);

        if( is_array($result) )
        {
            $err = false;

            foreach( $result as $obj )
            {
                $avatar_size = "s";
                switch($ext_auth_type)
                {
                    case "xf":
              
                        $xfUser = $xf->getUserInfo($obj->uid);
                        $obj->avatar = ($obj->avatar) ? $xf->getAvatarFilePath($avatar_size, $obj->uid).'?'.$obj->avatar : $config['xfAuth']['forumUrl'].'styles/'.$config["template"].'/xenforo/avatars/avatar_'.( ($xfUser["gender"]) ? $xfUser["gender"]."_" : "" ).$avatar_size.'.png';
                        break;
              
                    default:
              
                        $obj->avatar = ($obj->avatar) ? 'acpanel/images/avatars/'.$avatar_size.'/'.$obj->avatar : 'acpanel/images/noavatar_'.$avatar_size.'.gif';
                        break;
                }

                $obj->last_time = get_datetime($obj->last_time, "d.m.Y, H:i");
                $obj->online = compacttime($obj->online, $format="hhh:mmm:sss");
                $arrUser = (array)$obj;
            }
        }
        else
        {
            $err = true;
        }
    }
}

if( isset($arrUser) ) $smarty->assign("arrUser", $arrUser);
$smarty->assign("err", $err);

?>
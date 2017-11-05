<?php

if (!defined("IN_ACP")) die ("Hacking attempt!");

$post_info = array();

if( isset($_POST['hid']) )
{
	$productCHECK = getProduct("gameAccounts");
	if( empty($productCHECK) )
	{
		die('2');
	}

	if( isset($config['ga_registration']) &&  $config['ga_registration'] != 3 )
	{
		die('2');
	}

	$user_need_confirm = true;
	$post_info['newhid'] = $userinfo['newhid'] = $_POST['hid'];
}
else
{
	$user_need_confirm = false;
}

function hidAccountConfirm($need, $uinfo, $login = false)
{
	global $config, $db;

	if( !$need )
	{
		return false;
	}

	if( isset($uinfo['hid']) )
	{
		if( strlen($uinfo['hid']) > 4 )
		{
			die('3');
		}	
	}

	if( !$login )
	{
		die('0');
	}

	if( strlen($uinfo['newhid']) < 5 )
	{
		die('2');
	}

	if( !isset($uinfo['uid']) )
		$sqlconds = "`username` = '{username}'";
	else
		$sqlconds = "`uid` = ".$uinfo['uid'];

	$arguments = array('username'=>$uinfo['username'], 'hid'=>$uinfo['newhid']);
	$result = $db->Query("SELECT hid FROM `acp_users` WHERE ".$sqlconds." LIMIT 1", $arguments, $config['sql_debug']);

	if( $result )
	{
		if( strlen($result) > 4 )
		{
			die("3");
		}
	}

	if( !$config['ticket_moderate'] )
	{
		$result = $db->Query("SELECT hid FROM `acp_users` WHERE `hid` = '{hid}' LIMIT 1", $arguments, $config['sql_debug']);
	
		if( $result )
		{
			die("4");
		}
	}

	$result = $db->Query("UPDATE `acp_users` SET `hid` = '{hid}' WHERE ".$sqlconds, $arguments, $config['sql_debug']);

	if( $result )
	{
		die('3');
	}
	else
	{
		die('2');
	}
}

if( $userinfo['uid'] && !isset($_POST['hid']) )
{
	header('Location: '.$config['acpanel'].'.php');
	exit;
}

if( isset($_POST['login']) )
{
	$user = trim($_POST['user']);
	$pass = trim($_POST['password']);
	$post_info['username'] = $user;

	if( !$user || !$pass )
	{
		hidAccountConfirm($user_need_confirm, $post_info, false);
		$error = "@@login_err_empty@@";
	}
	else
	{
		switch($ext_auth_type)
		{
			case "xf":
				$user_login = $xf->userLogin($user, $pass);
				if( is_numeric($user_login) )
				{
					$post_info['uid'] = $user_login;
					hidAccountConfirm($user_need_confirm, $post_info, true);
					header('Location: '.$config['acpanel'].'.php');
					exit;
				}
				else
				{
					hidAccountConfirm($user_need_confirm, $post_info, false);
					$error = $user_login;
				}
				break;

			default:
				if( acp_login($user,$pass) )
				{
					hidAccountConfirm($user_need_confirm, $post_info, true);
					header('Location: '.$config['acpanel'].'.php');
					exit;
				}
				else
				{
					hidAccountConfirm($user_need_confirm, $post_info, false);
					$error = "@@login_err_valid@@";
				}
				break;
		}
	}
}

$smarty->assign("action",$_SERVER['PHP_SELF']."?do=login");
$smarty->assign("action_recovery",$_SERVER['PHP_SELF']."?do=profile");
$smarty->assign("reg_type",$config['reg_type']);
$smarty->assign("site_offline",$config['site_offline']);
if(isset($error)) $smarty->assign("iserror",$error);

?>
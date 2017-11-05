<?php

if (!defined("IN_ACP")) die ("Hacking attempt!");

if ($userinfo['uid']) {
	header('Location: '.$config['acpanel'].'.php');
	exit;
}

// Request type for $config['reg_type']=4:
// 0 - wrong hid
// 1 - wrong step
// 2 - hid already exists
// 3 - step[1] success
// xxx - fields errors (uname|email|passwd): "0" => error, "1" => success (example1: 010 - uname false, email true, passwd false)
// 4 - step[2] success
// 5 - registration closed
// 6 - error when a user is added to the database
// 7 - register through the website only

switch($config['reg_type'])
{
	case "1":

		$error = "@@register_closed@@";

		if (isset($_POST['hid']))
		{
			die("5");
		}

		break;

	case "2":
	case "3":

		if (isset($_POST['hid']))
		{
			die("7");
		}
		break;

	case "4":

		if (!isset($_POST['hid']) || !isset($_POST['step']))
		{
			$error = "@@reg_only_soft@@";
		}
		else
		{
			$hid = trim($_POST['hid']);
			if (strlen($hid) < 5)
			{
				die("0");
			}

			if ($_POST['step'] == '1')
			{
				$arguments = array('hid'=>$hid);
				$result = $db->Query("SELECT uid FROM `acp_users` WHERE hid = '{hid}' LIMIT 1", $arguments, $config['sql_debug']);

				if ($result)
				{					die("2");
				}

				die("3");
			}
			elseif ($_POST['step'] == '2')
			{				$arguments = array('hid'=>$hid);
				$result = $db->Query("SELECT uid FROM `acp_users` WHERE hid = '{hid}' LIMIT 1", $arguments, $config['sql_debug']);

				if ($result)
				{
					die("2");
				}
				$err = array(1,1,1);
				$uname = (isset($_POST['uname'])) ? trim($_POST['uname']) : '';
				if (!$uname)
				{					$err[0] = 0;
				}
				else
				{					if (strlen($uname) < $config['username_minlen'] || strlen($uname) > $config['username_maxlen'])
					{						$err[0] = 0;
					}
					else
					{						$arguments = array('username'=>$uname);
						$result = $db->Query("SELECT uid FROM `acp_users` WHERE username = '{username}'", $arguments, $config['sql_debug']);

						if ($result)
						{
							$err[0] = 0;
						}
					}
				}

				$email = (isset($_POST['umail'])) ? trim($_POST['umail']) : '';
				if (!preg_match("/^[0-9a-z_\.\-]+@[0-9a-z_^\.\-]+\.[a-z]{2,6}$/i", $email))
				{
					$err[1] = 0;
				}
				else
				{
					$arguments = array('mail'=>$email);
					$result = $db->Query("SELECT uid FROM `acp_users` WHERE mail = '{mail}'", $arguments, $config['sql_debug']);

					if ($result)
					{
						$err[1] = 0;
					}
				}

				$passwd = (isset($_POST['passwd'])) ? trim($_POST['passwd']) : '';
				$passwd_check = (isset($_POST['passwd_check'])) ? trim($_POST['passwd_check']) : '';
				if ($passwd != $passwd_check || strlen($passwd) < $config['passwd_minlen'])
				{					$err[2]	= 0;
				}

				if (in_array(0, $err, true))
				{
					die(implode('',$err));
				}
				else
				{
					switch($ext_auth_type)
					{
						case "xf":
				
							$xf->createUser($uname, $email, $passwd, "valid");
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("user_register", "new user: ".$uname);	
							die("4");
							break;
				
						default:
				
							$arguments = array('hid'=>$hid,'username'=>$uname,'password'=>md5($passwd),'mail'=>$email,'group'=>$config['group_for_new_user'],'ip'=>getRealIpAddr(),'reg_date'=>time(),'timezone'=>$config['timezone']);
							$result = $db->Query("INSERT INTO `acp_users` (username,password,mail,usergroupid,ipaddress,reg_date,timezone,hid) VALUES ('{username}','{password}','{mail}','{group}','{ip}','{reg_date}','{timezone}','{hid}')", $arguments, $config['sql_debug']);
		
							if (!$result)
							{
								die("6");
							}
							else
							{
								if (in_array("log_edititing", $config['user_action_log'])) saveLogs("user_register", "new user: ".$uname);
								die("4");
							}
							break;
					}
				}
			}
			else
			{
				die("1");
			}
		}

		break;

	default:

		$error = "@@reg_type_not_set@@";
}

if(isset($error))
{	$smarty->assign("iserror",$error);
}
else
{
	$ext_auth = false;

	switch($ext_auth_type)
	{
		case "xf":

			$ext_auth = true;
			break;

		default:

			$ext_auth = false;
			break;
	}
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
	$smarty->assign("action",$_SERVER['PHP_SELF']);	$headinclude = "
		<link rel='stylesheet' href='".TEMPLATE_PATH.$config['template']."/css/qaptcha.css' type='text/css' />
		<script type='text/javascript' src='acpanel/scripts/js/jquery-ui.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.ui.touch.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.qaptcha.js'></script>
		<script type='text/javascript'>
			function createCookie(name,value,days) {
			    if (days) {
			        var date = new Date();
			        date.setTime(date.getTime()+(days*24*60*60*1000));
			        var expires = '; expires='+date.toGMTString();
			    }
			    else var expires = '';
			    document.cookie = name+'='+value+expires+'; path=/';
			}

			jQuery(document).ready(function($){
				$('.QapTcha').QapTcha({
					txtLock : '@@captcha_lock@@',
					txtUnlock : '@@captcha_unlock@@',
					disabledSubmit : true,
					autoRevert : true
				});

				$('#regform').submit(function() {
					var data = $(this).serialize();

					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_homepage',
						data:data + '&url=".$url."&go=5',
						success:function(result) {
							if(result.indexOf('class=\"message errormsg\"') + 1)
							{
								$('.accessMessage').html(result);
							}
							else
							{								".( (!$ext_auth) ? 'createCookie(\'acp_user\',result,'.$config["cookie_time"].');' : '' )."
								window.location.href = '".$config['acpanel'].".php?do=profile';
							}
						}
					});

					return false;
				});
			});
		</script>
	";
}

?>
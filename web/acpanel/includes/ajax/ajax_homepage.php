<?php

if(!isset($_POST['go']))
{
	die("Hacking Attempt");
}
else
{
	require_once(INCLUDE_PATH . 'class.mysql.php');

	try {
		$db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
	} catch (Exception $e) {
		die($e->getMessage());
	}

	$array_cfg = $db->Query("SELECT varname, value FROM `acp_config` WHERE varname IS NOT NULL", array(), true);

	if(is_array($array_cfg))
	{
		foreach ($array_cfg as $obj)
		{
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();
	}

	include(INCLUDE_PATH . 'functions.main.php');
	$langs = create_lang_list();

	unset($translate);
	$arguments = array('lp_name'=>'homepage.tpl','lang'=>get_language(1));
	$tr_result = $db->Query("
		SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`
		LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_name='{lp_name}'
		WHERE acp_lang_pages.lp_id = acp_lang_words.lw_page OR acp_lang_words.lw_page = '0'
	", $arguments, $config['sql_debug']);
	if(is_array($tr_result)) {
		foreach ($tr_result as $obj){
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}

	require_once(INCLUDE_PATH . '_auth.php');

	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create monitoring list
	// 2 - save custom page
	// 3 - create admins list
	// 4 - check captcha
	// 5 - user registration
	// 6 - save security options for user
	// 7 - save general options for user
	// 8 - delete avatar
	// 9 - ThumbsUp cast the vote
	// 10 - Update data from XF user
	// 11 - Add/Delete favorites
	// 12 - total items for filter
	// 13 - generate filters
	// 14 - server page counter
	// 15 - get server online statistics

	switch($_POST['go']) {

		case "1":

			$productID = getProduct("ratingServers");
			$ratingServers = (!empty($productID)) ? TRUE : FALSE;

			if( $ratingServers )
			{
				// ###############################################################################
				// Start load the required ThumbsUp classes
				// ###############################################################################
			
				define('THUMBSUP_DOCROOT', SCRIPT_PATH . 'thumbsup/');
				require THUMBSUP_DOCROOT.'classes/thumbsup.php';
				$tUP = new ThumbsUp($config, $db, $userinfo['uid']);
				require THUMBSUP_DOCROOT.'classes/thumbsup_cookie.php';
				require THUMBSUP_DOCROOT.'classes/thumbsup_item.php';
				require THUMBSUP_DOCROOT.'classes/thumbsup_template.php';
			
				// Debug mode is enabled
				if( ThumbsUp::config('sql_debug') )
				{
					// Enable all error reporting
					ThumbsUp::debug_mode();
				
					// Show an error if the headers are already sent
					if( headers_sent() )
					{
						trigger_error('thumbsup/init.php must be included before any output has been sent. Include it at the very top of your page.');
					}
				}
				
				// Enable support for json functions
				ThumbsUp::json_support();
				
				// Register new votes if any
				ThumbsUp::catch_vote();
			
				// ###############################################################################
				// End load ThumbsUp classes
				// ###############################################################################
			}

			include_once(INCLUDE_PATH . 'functions.servers.php');

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$sorting = ($ratingServers) ? "position ASC" : "rating DESC";

			$where = " WHERE active = 1";
			$arguments = $filter = array();
			if( isset($_POST['srv']) && $_POST['srv'] ) $filter['gametype'] = $_POST['srv'];
			if( isset($_POST['mod']) && $_POST['mod'] ) $filter['opt_mode'] = $_POST['mod'];
			if( isset($_POST['city']) && $_POST['city'] ) $filter['opt_city'] = $_POST['city'];
			if( !empty($filter) )
			{
				$arguments = array();
				foreach($filter as $k => $v)
				{
					$arguments[$k] = $v;
					$where .= " AND ".$k." = '{".$k."}'";
				}
			}

			if( $filter['status'] = $config['mon_hide_offline'] )
			{
				$where .= " AND status = 1";
			}
			else
				unset($filter['status']);

			if( $config['mon_cache'] && $ratingServers )
			{
				if( $total_items = $db->Query("SELECT count(*) FROM `acp_servers`".$where, $arguments, $config['sql_debug']) )
					$servers = getMonitoringCache($offset, $limit, $sorting, $filter);
				else
					$servers = array();
			}
			else
			{
				if( $total_items = $db->Query("SELECT count(*) FROM `acp_servers`".$where, $arguments, $config['sql_debug']) )
				{
					include(INCLUDE_PATH . 'class.SypexGeo.php');
					$SxGeo = new SypexGeo(SCRIPT_PATH . 'geoip/SypexGeo.dat');
		
					$servers = create_monitoring_list(0, $total_items, $sorting, $total_items, true, "", $filter);
		
					if( count($servers) > 1 )
					{
						$total_items = $servers['total'];
					}
					else
						$total_items = 0;
		
					unset($servers['total']);
				}
				else
				{
					$servers = array();
				}

				$servers = array_slice($servers, $offset, $limit);
			}

			// GET FAVORITES START
			$favorites = favoritesGet();
	
			if( !empty($favorites) )
			{
				foreach($favorites as $k => $v)
				{
					if( isset($servers[$v]) )
					{
						$servers[$v]['favorite'] = true;
					}
				}
			}
			// GET FAVORITES END

			require_once("scripts/smarty/SmartyBC.class.php");

			$smarty = new SmartyBC();

			function getVote($params, $smarty) 
			{
				global $config, $tUP;

			        $srv = $params['serverid'];
				return ThumbsUp::item($srv)->template('mini_thumbs')->format($config['mon_vote_format'])->options('align=right');
			} 

			$smarty->registerPlugin("function", "getVote", "getVote"); 

			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($servers)) $smarty->assign("servers", $servers);
			if(isset($error)) $smarty->assign("iserror", $error);
			$smarty->assign("rating", $ratingServers);
			$smarty->assign("current_cat", $_POST['cat']);
			$smarty->assign("server_cat", $_POST['srv_cat']);

			$smarty->registerFilter("output", "translate_template");
			$smarty->display('homepage_list.tpl');

			break;

		case "2":

			if( $userinfo['edit_pages'] == 'yes' )
			{
				$data = trim($_POST['content']);
				$id = trim($_POST['id']);
				if ( !is_numeric($id) ) die("Hacking Attempt");
	
				if ($config['charset'] != 'utf-8')
				{
					$data = iconv('utf-8', $config['charset'], $data);
				}
	
				$arguments = array('data'=>$data,'id'=>$id);
				$result = $db->Query("UPDATE `acp_pages` SET pagetext = '{data}' WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if (!$result)
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_error'].'</span>';
				}
				else
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("save_curent_page", "save curent page");
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "3":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$t = $_POST['t'];
			$colums = 3;
			$admin_groups = strlen($config['admin_groups']) ? explode(',', $config['admin_groups']) : array();
			$sqlcond_select = "";
			$sqlcond_join = "";
			$sqlcond_where = "";
			$sqlcond_where_bans = "";
			$sqlcond_order = "";
			$sqlcond_order = " ORDER BY g.usergroupid";
			$arguments = array('offset'=>$offset,'limit'=>$limit,'usergroups'=>$admin_groups);
		
			$productID = getProduct("gameAccounts");
			if( !empty($productID) )
			{
				$sqlcond_select = ", p.player_nick, GROUP_CONCAT(DISTINCT m_s.server_id) AS servers";
				$sqlcond_join = " LEFT JOIN `acp_players` p ON p.userid = u.uid 
					LEFT JOIN `acp_access_mask_players` m_p ON m_p.userid = u.uid 
					LEFT JOIN `acp_access_mask` m ON m.mask_id = m_p.mask_id 
					LEFT JOIN `acp_access_mask_servers` m_s ON m_s.mask_id = m_p.mask_id
				";

				$sqlcond_where = " AND (m_p.access_expired > {time} OR m_p.access_expired = 0)";
				$arguments['time'] = time();
				if( $t > 0 )
				{
					$sqlcond_where .= " AND (m_s.server_id = {srv} OR m_s.server_id = 0)";
					$arguments['srv'] = $t;
				}
		
				if( $config['ga_admin_flag'] )
				{
					$sqlcond_where .= " AND INSTR(m.access_flags,'{admin_flag}') > 0";
					$arguments['admin_flag'] = $config['ga_admin_flag'];
				}

				$productID = getProduct("gameBans");
				if( !empty($productID) )
				{
					$result_servers = $db->Query("SELECT id, address FROM `acp_servers` WHERE active = 1", array(), $config['sql_debug']);
					if( is_array($result_servers) )
					{
						foreach( $result_servers as $obj )
						{
							$array_servers[$obj->id] = $obj->address;
						}

						if( array_key_exists($t, $array_servers) )
							$sqlcond_where_bans = " WHERE server_ip = '".$array_servers[$t]."'";
					}

					$colums = 4;
					$sqlcond_select .= ", (IFNULL(t1.bans_h, 0) + IFNULL(t2.bans_a, 0)) AS total_bans";

					$sqlcond_join .= " LEFT JOIN (SELECT admin_uid, count(*) AS bans_h FROM `acp_bans_history`".$sqlcond_where_bans." GROUP BY admin_uid) t1 ON t1.admin_uid = u.uid 
						LEFT JOIN (SELECT admin_uid, count(*) AS bans_a FROM `acp_bans`".$sqlcond_where_bans." GROUP BY admin_uid) t2 ON t2.admin_uid = u.uid";

					$sqlcond_order = " ORDER BY total_bans DESC";

					$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_gamebans_public_players'", array(), $config['sql_debug']);
					
					if( is_array($result_cats) )
					{
						foreach( $result_cats as $obj )
						{
							$cat_bans = array('cat' => $obj->sectionid, 'do' => $obj->categoryid);
						}
					}
				}
			}

			$result = $db->Query("SELECT u.uid, u.username, g.usergroupname, u.icq".$sqlcond_select." 
				FROM `acp_users` u LEFT JOIN `acp_usergroups` g ON g.usergroupid = u.usergroupid".$sqlcond_join." 
				WHERE u.usergroupid IN ('{usergroups}')".$sqlcond_where." GROUP BY u.uid".$sqlcond_order." LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					if( !$obj->icq )
						$obj->icq = "-";
					if( !isset($obj->total_bans) )
						$obj->total_bans = 0;
					else
						$obj->total_bans = intval($obj->total_bans);

					$admins[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("colums",$colums);
			if(isset($admins)) $smarty->assign("admins",$admins);
			if(isset($error)) $smarty->assign("iserror",$error);
			if(isset($cat_bans)) $smarty->assign("cat_bans", $cat_bans);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_admins_list.tpl');

			break;

		case "4":

			if(!isset($_SESSION)) session_start();

			$aResponse['error'] = false;
			$_SESSION['iQaptcha'] = false;

			if(isset($_POST['action']))
			{
				if(htmlentities($_POST['action'], ENT_QUOTES, 'UTF-8') == 'qaptcha')
				{
					$_SESSION['iQaptcha'] = true;
					if($_SESSION['iQaptcha'])
						echo json_encode($aResponse);
					else
					{
						$aResponse['error'] = true;
						echo json_encode($aResponse);
					}
				}
				else
				{
					$aResponse['error'] = true;
					echo json_encode($aResponse);
				}
			}
			else
			{
				$aResponse['error'] = true;
				echo json_encode($aResponse);
			}

			break;

		case "5":

			$err = array();

			if (isset($_POST['iQapTcha']) && empty($_POST['iQapTcha']) && isset($_SESSION['iQaptcha']) && $_SESSION['iQaptcha'])
			{
				$uname = (isset($_POST['uname'])) ? trim($_POST['uname']) : '';
				if (!$uname)
				{
					$err[] = $translate['uname_not_empty'];
				}
				else
				{
					if (strlen($uname) < $config['username_minlen'] || strlen($uname) > $config['username_maxlen'])
					{
						$err[] = $translate['uname_must_by']."&nbsp;".$config['username_minlen']."-".$config['username_maxlen'];
					}
					else
					{
						$arguments = array('username'=>$uname);
						$result = $db->Query("SELECT uid FROM `acp_users` WHERE username = '{username}'", $arguments, $config['sql_debug']);

						if ($result)
						{
							$err[] = $translate['uname_already_exists'];
						}
					}
				}

				$email = (isset($_POST['umail'])) ? trim($_POST['umail']) : '';
				if (!preg_match("/^[0-9a-z_\.\-]+@[0-9a-z_^\.\-]+\.[a-z]{2,6}$/i", $email))
				{
					$err[] = $translate['email_incorrect'];
				}
				else
				{
					$arguments = array('mail'=>$email);
					$result = $db->Query("SELECT uid FROM `acp_users` WHERE mail = '{mail}'", $arguments, $config['sql_debug']);

					if ($result)
					{
						$err[] = $translate['email_already_exists'];
					}
				}

				$passwd = (isset($_POST['passwd'])) ? trim($_POST['passwd']) : '';
				$passwd_check = (isset($_POST['passwd_check'])) ? trim($_POST['passwd_check']) : '';
				if ($passwd != $passwd_check)
				{
					$err[] = $translate['pass_not_check'];
				}
				if (strlen($passwd) < $config['passwd_minlen'])
				{
					$err[] = $translate['pass_must_by']."&nbsp;".$config['passwd_minlen'];
				}
			}
			else
			{
				$err[] = $translate['captcha_not_defined'];
			}

			if (!empty($err))
			{
				if(count($err) > 1)
				{
					$err = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $err);
				}
				else
				{
					$err = $err[0];
				}
				print '<div class="message errormsg"><p>'.$translate['reg_error_log'].'&nbsp;'.$err.'</p></div>';
			}
			else
			{
				$code_activated = ($config['reg_type'] == 3) ? md5(RandomCode(10)) : '';
				$user_state = ($config['reg_type'] == 3) ? 'email_confirm' : 'valid';
				$group = ($config['reg_type'] == 3) ? "4" : $config['group_for_new_user'];
				$skey = md5($uname.$config['secretkey']);

				if( $ext_auth_type == "xf" )
				{
					$reg_info = $xf->createUser($uname, $email, $passwd, $user_state);
					if( isset($reg_info['user_id']) )
					{
						$xf->setLogin($reg_info['user_id']);
					}
					else
					{
						print '<div class="message errormsg"><p>'.$translate['reg_error'].'</p></div>';
					}

					unset($_SESSION['iQaptcha']);
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("user_register", "new user: ".$uname);	
				}
				else
				{
					$arguments = array('user_state'=>$user_state,'secretkey'=>$skey,'username'=>$uname,'password'=>md5($passwd),'mail'=>$email,'group'=>$group,'ip'=>getRealIpAddr(),'code'=>$code_activated,'reg_date'=>time(),'timezone'=>$config['timezone']);
					$result = $db->Query("INSERT INTO `acp_users` (user_state,username,password,mail,usergroupid,ipaddress,code_activated,reg_date,timezone,secretkey) VALUES ('{user_state}','{username}','{password}','{mail}','{group}','{ip}','{code}','{reg_date}','{timezone}','{secretkey}')", $arguments, $config['sql_debug']);
	
					if (!$result)
					{
						print '<div class="message errormsg"><p>'.$translate['reg_error'].'</p></div>';
					}
					else
					{
						$user_insert_id = $db->LastInsertID();
	
						if ($config['reg_type'] == 3)
						{
							include_once(INCLUDE_PATH . 'class.EasyEmail.php');
							$array_holders = array('username'=>$uname,'sitename'=>$config['site_name'],'link'=>$_POST['url'].'?do=profile&u='.$user_insert_id.'&c='.$code_activated);
							function holders_replace($matches)
							{
								global $array_holders;
	
								return $array_holders[$matches[1]];
							}
	
							$subject = preg_replace_callback('#{([^}]+)}#sUi', 'holders_replace', $translate['template_registr_confirm_subject']);
							$body = preg_replace_callback('#{([^}]+)}#sUi',  'holders_replace', $translate['template_registr_confirm_body']);
							$hdrs = "Content-type: text/html; charset=".$config['charset']."\r\n";
							$easy_email = new Easy_Email($email, $config['default_email']);
							$easy_email->sendEmail($subject, $body, $hdrs);
						}
	
						unset($_SESSION['iQaptcha']);
	
						print $uname.":".$skey.":".$user_insert_id;
	
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("user_register", "new user: ".$uname);
					}
				}
			}

			break;

		case "6":

			$err = $updateData = array();
			$sqlcond = "";

			$passwd_current = (isset($_POST['passwd_current'])) ? trim($_POST['passwd_current']) : '';
			if( $passwd_current )
			{
				$arguments = array('uid'=>$_POST['uid']);
				$result = $db->Query("SELECT password, mail FROM `acp_users` WHERE uid = '{uid}'", $arguments, $config['sql_debug']);
				if( is_array($result) )
				{
					foreach ($result as $obj)
					{
						$passwd_db = $obj->password;
						$email_db = $obj->mail;
					}
				}

				if (md5($passwd_current) != $passwd_db)
				{
					$err[] = $translate['current_pass_not_check'];
				}
				else
				{
					$passwd_new = (isset($_POST['passwd_new'])) ? trim($_POST['passwd_new']) : '';
					$passwd_new_check = (isset($_POST['passwd_new_check'])) ? trim($_POST['passwd_new_check']) : '';
					if ($passwd_new != $passwd_new_check)
					{
						$err[] = $translate['pass_not_check'];
					}
					else
					{
						if( $passwd_new )
						{
							if (strlen($passwd_new) < $config['passwd_minlen'])
							{
								$err[] = $translate['pass_must_by']."&nbsp;".$config['passwd_minlen'];
							}
							else
							{
								$updateData['password'] = $passwd_new;
								$sqlcond .= "password = '{password}',";
							}
						}
					}

					$email = (isset($_POST['umail'])) ? trim($_POST['umail']) : '';
					if (!preg_match("/^[0-9a-z_\.\-]+@[0-9a-z_^\.\-]+\.[a-z]{2,6}$/i", $email))
					{
						$err[] = $translate['email_incorrect'];
					}
					else
					{
						$arguments = array('mail'=>$email,'uid'=>$_POST['uid']);
						$result = $db->Query("SELECT uid FROM `acp_users` WHERE mail = '{mail}' AND uid <> '{uid}'", $arguments, $config['sql_debug']);

						if ($result)
						{
							$err[] = $translate['email_already_exists'];
						}

						if ($email_db != $email)
						{
							$updateData['email'] = $email;
							$sqlcond .= "mail = '{mail}',";
							if ($config['reg_type'] == 3) $sqlcond .= "code_activated = '{code}', user_state = 'email_confirm_edit',";
						}
					}
				}
			}
			else
			{
				$err[] = $translate['pass_not_empty'];
			}

			if (!empty($err))
			{
				if(count($err) > 1)
				{
					$err = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $err);
				}
				else
				{
					$err = $err[0];
				}
				print '<div class="message errormsg"><p>'.$translate['reg_error_log'].'&nbsp;'.$err.'</p></div>';
			}
			else
			{
				if( $sqlcond )
				{
					if( $ext_auth_type == "xf" )
					{
						$xf->setUserData($xf->getUserId(), $updateData);
	
						print '<div class="message success"><p>'.$translate['profile_edit_success'].'</p></div>';
						break;
					}

					$sqlcond = substr($sqlcond, 0, strlen($sqlcond)-1);
					$code_activated = md5(RandomCode(10));
					$arguments = array('password'=>md5($passwd_new),'mail'=>$email,'code'=>$code_activated,'uid'=>$_POST['uid']);
					$result = $db->Query("UPDATE `acp_users` SET ".$sqlcond." WHERE uid = '{uid}'", $arguments, $config['sql_debug']);

					if (!$result)
					{
						print '<div class="message errormsg"><p>'.$translate['edit_error'].'</p></div>';
					}
					else
					{
						$email_sending = "";

						if ($config['reg_type'] == 3)
						{
							include_once(INCLUDE_PATH . 'class.EasyEmail.php');
							$array_holders = array('username'=>$_POST['uname'],'sitename'=>$config['site_name'],'link'=>$_POST['url'].'?do=profile&u='.$_POST['uid'].'&c='.$code_activated);
							function holders_replace($matches)
							{
								global $array_holders;

								return $array_holders[$matches[1]];
							}

							$subject = preg_replace_callback('#{([^}]+)}#sUi', 'holders_replace', $translate['template_email_change_subject']);
							$body = preg_replace_callback('#{([^}]+)}#sUi',  'holders_replace', $translate['template_email_change_body']);
							$easy_email = new Easy_Email($email, $config['default_email']);
							$easy_email->sendEmail($subject, $body);
							if ($easy_email->getIsSent())
								$email_sending .= " ".$translate['sending_success'];
							else
								$email_sending .= " ".$translate['sending_fail'];
						}

						print '<div class="message success"><p>'.$translate['profile_edit_success'].$email_sending.'</p></div>';
					}
				}
				else
				{
					print '<div class="message warning"><p>'.$translate['edit_empty'].'</p></div>';
				}
			}

			break;

		case "7":

			$id = $_POST['uid'];
			unset($_POST['go'],$_POST['uid']);
			$query_string = "";
			$updateData = array();

			if( is_array($_POST) )
			{
				foreach ($_POST as $var => $value)
				{
					$value = trim($value);

					switch($var)
					{
						case "icq":

							if( !is_numeric($value) && strlen($value) )
							{
								$error[] = $translate['isq_not_valid'];
							}
							else
							{
								$updateData['icq'] = $value;
							}

							break;

						default:

							$updateData[$var] = $value;
							break;
					}

					$query_string .= $var." = '".mysql_real_escape_string($value)."',";
				}

				if( !empty($error) )
				{
					if(count($error) > 1)
					{
						$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
					}
					else
					{
						$error = $error[0];
					}
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['values_error'].':&nbsp;'.$error.'</span>';
				}
				else
				{
					if( $ext_auth_type == "xf" )
					{
						$xf->setUserData($xf->getUserId(), $updateData);
	
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['user_edit_success'].'</span>';
						break;
					}

					$query_string = substr($query_string, 0, strlen($query_string)-1);
					$arguments = array('id'=>$id);
					$result = $db->Query("UPDATE `acp_users` SET ".$query_string." WHERE uid = '{id}'", $arguments, $config['sql_debug']);

					if (!$result)
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['user_edit_error'].'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['user_edit_success'].'</span>';
					}
				}
			}
			else
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['empty_array'].'</span>';
			}

			break;

		case "8":

			$id = $_POST['uid'];
			if ( !is_numeric($id) ) die("Hacking Attempt");

			if( $ext_auth_type == "xf" )
			{
				$xf->deleteAvatar();

				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['delete_avatar_success'].'</span>';
				break;
			}

			$arguments = array('id'=>$id);
			$result = $db->Query("UPDATE `acp_users` SET avatar = '' WHERE uid = '{id}'", $arguments, $config['sql_debug']);

			if (!$result)
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['delete_avatar_error'].'</span>';
			}
			else
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['delete_avatar_success'].'</span>';
			}

			break;

		case "9":

			// ###############################################################################
			// Start load the required ThumbsUp classes
			// ###############################################################################
		
			define('THUMBSUP_DOCROOT', SCRIPT_PATH . 'thumbsup/');
			require THUMBSUP_DOCROOT.'classes/thumbsup.php';
			$tUP = new ThumbsUp($config, $db, $userinfo['uid']);
			require THUMBSUP_DOCROOT.'classes/thumbsup_cookie.php';
			require THUMBSUP_DOCROOT.'classes/thumbsup_item.php';
			require THUMBSUP_DOCROOT.'classes/thumbsup_template.php';
		
			// Debug mode is enabled
			if( ThumbsUp::config('sql_debug') )
			{
				// Enable all error reporting
				ThumbsUp::debug_mode();
			
				// Show an error if the headers are already sent
				if( headers_sent() )
				{
					trigger_error('thumbsup/init.php must be included before any output has been sent. Include it at the very top of your page.');
				}
			}
			
			// Enable support for json functions
			ThumbsUp::json_support();
			
			// Register new votes if any
			ThumbsUp::catch_vote();
		
			// ###############################################################################
			// End load ThumbsUp classes
			// ###############################################################################

			break;

		case "10":

			unset($_POST['go']);
			$fields = $_POST;

			if( !empty($fields) && is_array($fields) )
			{
				if( $ext_auth_type == "xf" )
				{
					if( $userID = $xf->getUserId() )
					{
						$xf->setUserData($userID, $fields);
					}
				}
			}

			break;

		case "11":

			include_once(INCLUDE_PATH . 'functions.servers.php');

			$action = $_POST['action'];
			$id = $_POST['server'];

			if( $action )
			{
				if( favoritesAdd($id) )
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_favorite_success'].'</span>';
				else
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.sprintf($translate['add_favorite_error'], $userinfo['mon_favorites_limit']).'</span>';
			}
			else
			{
				if( favoritesDeleteID($id) )
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_favorite_success'].'</span>';
				else
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_favorite_error'].'</span>';
			}

			break;

		case "12":

			$productID = getProduct("ratingServers");
			$ratingServers = (!empty($productID)) ? TRUE : FALSE;

			include_once(INCLUDE_PATH . 'functions.servers.php');

			$sorting = ($ratingServers) ? "position ASC" : "rating DESC";

			$where = " WHERE active = 1";
			$arguments = $filter = array();
			if( isset($_POST['srv']) && $_POST['srv'] ) $filter['gametype'] = $_POST['srv'];
			if( isset($_POST['mod']) && $_POST['mod'] ) $filter['opt_mode'] = $_POST['mod'];
			if( isset($_POST['city']) && $_POST['city'] ) $filter['opt_city'] = $_POST['city'];
			if( !empty($filter) )
			{
				foreach($filter as $k => $v)
				{
					$arguments[$k] = $v;
					$where .= " AND ".$k." = '{".$k."}'";
				}
			}

			$total_items = $db->Query("SELECT count(*) FROM `acp_servers`".$where, $arguments, $config['sql_debug']);
		
			echo $total_items;

			break;

		case "13":

			include_once(INCLUDE_PATH . 'functions.servers.php');

			$output = "";
			$filters = $_POST['filters'];
			$where = " WHERE s.active = 1";
			$arguments = $filter = array();
			if( $filters[0] && $_POST['current_name'] != "srv" ) $filter['gametype'] = $filters[0];
			if( $filters[1] && $_POST['current_name'] != "mod" ) $filter['opt_mode'] = $filters[1];
			if( $filters[2] && $_POST['current_name'] != "city" ) $filter['opt_city'] = $filters[2];
			if( !empty($filter) )
			{
				foreach($filter as $k => $v)
				{
					$arguments[$k] = $v;
					$where .= " AND s.".$k." = '{".$k."}'";
				}
			}

			switch($_POST['current_name'])
			{
				case "srv":

					$types_result = $db->Query("SELECT s.gametype, count(s.gametype) AS cnt FROM `acp_servers` s".$where." GROUP BY gametype ORDER BY cnt DESC", $arguments, $config['sql_debug']);
					if( is_array($types_result) )
					{
						$protocolList = server_protocol_list();
						foreach( $types_result as $obj )
						{
							$filterTypes[$obj->gametype] = $obj->cnt;
							$protocolList[$obj->gametype]['cnt'] = $obj->cnt;
						}
			
						$filterTypes = array_intersect_key($protocolList, $filterTypes);

						$output = '
							<select class="chosen" name="srv" data-placeholder="'.$translate['all_servers'].'...">
								<option value="0"></option>
						';

						foreach( $filterTypes as $k => $v )
						{
							$output .= '<option value="'.$k.'"'.(($_POST['current_val'] == $k) ? ' selected' : '').'>'.$v['name'].' ('.$v['cnt'].')</option>';
						}

						$output .= '</select>';
					}
					break;

				case "mod":

					$modes_result = $db->Query("SELECT m.id, m.name, count(m.name) AS cnt FROM `acp_servers_modes` m LEFT JOIN `acp_servers` s ON s.opt_mode = m.id".$where." AND s.id IS NOT NULL GROUP BY m.name ORDER BY cnt DESC", $arguments, $config['sql_debug']);
					if( is_array($modes_result) )
					{
						$output = '
							<select class="chosen" name="mod" data-placeholder="'.$translate['all_mods'].'...">
								<option value="0"></option>
						';

						foreach( $modes_result as $obj )
						{
							$output .= '<option value="'.$obj->id.'"'.(($_POST['current_val'] == $obj->id) ? ' selected' : '').'>'.$obj->name.' ('.$obj->cnt.')</option>';
						}

						$output .= '</select>';
					}
					break;

				case "city":

					$cities_result = $db->Query("SELECT c.id, c.name, count(c.name) AS cnt FROM `acp_servers_cities` c LEFT JOIN `acp_servers` s ON s.opt_city = c.id".$where." AND s.id IS NOT NULL GROUP BY c.name ORDER BY cnt DESC", $arguments, $config['sql_debug']);
					if( is_array($cities_result) )
					{
						$output = '
							<select class="chosen" name="city" data-placeholder="'.$translate['all_city'].'...">
								<option value="0"></option>
						';

						foreach( $cities_result as $obj )
						{
							$output .= '<option value="'.$obj->id.'"'.(($_POST['current_val'] == $obj->id) ? ' selected' : '').'>'.$obj->name.' ('.$obj->cnt.')</option>';
						}

						$output .= '</select>';
					}
					break;
			}

			echo $output;

			break;

		case "14":

			$id = $_POST['id'];

			if( is_numeric($id) )
			{
				$visitor_ip = getRealIpAddr();
				$current_time = time() - ( 60 * 60 * $config['mon_view_lifetime'] );
	
				if( $date = $db->Query("SELECT timestamp FROM `acp_servers_viewed` WHERE timestamp > ".$current_time." AND server_id = ".$id." AND visitor_ip = '{visitor_ip}' ORDER BY timestamp DESC LIMIT 1", array('visitor_ip' => $visitor_ip), $config['sql_debug']) )
				{
					break;
				}

				$result_insert = $db->Query("INSERT INTO `acp_servers_viewed` (server_id, visitor_ip, timestamp) VALUES ('{server_id}', '{visitor_ip}', '{timestamp}')", array('server_id' => $id, 'visitor_ip' => $visitor_ip, 'timestamp' => time()), $config['sql_debug']);
			}

			break;

		case "15":

			date_default_timezone_set('UTC');

			function getDateArray($id, $type, $currTime)
			{
				global $db;

				$arrOut = array();

				$query = $db->Query("SELECT statistics FROM `acp_servers` WHERE id = '{id}'", array('id' => $id));
				$stats = unserialize($query);

				switch($type)
				{
					case "d":

						$i = 0;
						$currDateString = date('Y-m-d H', $currTime).":00:00";
						$startTime = strtotime($currDateString) - (3600*24);

						while( $i < 24 )
						{
							$index = (string)($startTime*1000);
							$arrOut["players"][$index] = (isset($stats[$type]["players"][$index])) ? $stats[$type]["players"][$index] : 0;
							$arrOut["uptime"][$index] = (isset($stats[$type]["uptime"][$index])) ? $stats[$type]["uptime"][$index] : 0;
							$startTime = $startTime + 3600;
							$i++;
						}
						break;

					case "w":

						$i = 0;
						$currDateString = date('Y-m-d', $currTime)." 00:00:00";
						$startTime = strtotime($currDateString) - (3600*24*6);

						while( $i < 7 )
						{
							$index = (string)($startTime*1000);
							$arrOut["players"][$index] = (isset($stats[$type]["players"][$index])) ? $stats[$type]["players"][$index] : 0;
							$arrOut["uptime"][$index] = (isset($stats[$type]["uptime"][$index])) ? $stats[$type]["uptime"][$index] : 0;
							$startTime = $startTime + 86400;
							$i++;
						}
						break;

					case "y":

						$i = 0;
						$currDateString = date('Y-m', $currTime)."-01 00:00:00";
						$startTime = strtotime("1 year ago", strtotime($currDateString));
						$startTime = strtotime("next month", $startTime);

						while( $i < 12 )
						{
							$index = (string)($startTime*1000);
							$arrOut["players"][$index] = (isset($stats[$type]["players"][$index])) ? $stats[$type]["players"][$index] : 0;
							$arrOut["uptime"][$index] = (isset($stats[$type]["uptime"][$index])) ? $stats[$type]["uptime"][$index] : 0;
							$startTime = strtotime("next month", $startTime);
							$i++;
						}
						break;
				}

				return $arrOut;
			}

			$id = $_POST['server'];
			$action = $_POST['action'];
			$dateArray = array();

			if( is_numeric($id) && in_array($action, array('d', 'w', 'y')) )
			{
				$dateArray = getDateArray($id, $action, time());
			}

			echo json_encode($dateArray);

			break;

		default:

			die("Hacking Attempt");
	}
}

?>
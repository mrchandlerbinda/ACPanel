<?php

if( !defined("IN_ACP") ) die ("Hacking attempt!");

if( isset($_POST["recovery"]) && isset($_POST["email"]) )
{
	$email = trim($_POST["email"]);
	if( !preg_match("/^[0-9a-z_\.\-]+@[0-9a-z_^\.\-]+\.[a-z]{2,6}$/i", $email) )
		$content_info = '<div class="message errormsg"><p>@@email_error@@</p></div>';
	else
	{
		$arguments = array('mail'=>$email);
		$uname = $db->Query("SELECT username, uid FROM `acp_users` WHERE mail = '{mail}' LIMIT 1", $arguments, $config['sql_debug']);

		if( !is_array($uname) )
			$content_info = '<div class="message errormsg"><p>@@email_error@@</p></div>';
		else
		{
			foreach( $uname as $obj )
			{
				$code_activated = generate_code(16);
				$arguments = array('uid'=>$obj->uid, 'type'=>'password', 'key'=>$code_activated, 'date'=>time());
				$set_code = $db->Query("INSERT INTO `acp_users_confirmation` SET uid = '{uid}', confirmation_type = '{type}', confirmation_key = '{key}', confirmation_date = '{date}' 
					ON DUPLICATE KEY UPDATE confirmation_key = '{key}', confirmation_date = '{date}'", $arguments, $config['sql_debug']);

				if( $set_code )
				{
					$langs = create_lang_list();
				
					unset($translate);
					$arguments['lang'] = get_language(1);
					$tr_result = $db->Query("
						SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`
						WHERE lw_word = 'template_password_recovery_subject' OR lw_word = 'template_password_recovery_body' 
					", $arguments, $config['sql_debug']);
					if( is_array($tr_result) )
						foreach( $tr_result as $ob )
							$translate[$ob->lw_word] = $ob->lw_translate;

					$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
					include_once(INCLUDE_PATH . 'class.EasyEmail.php');
					$array_holders = array('username'=>$obj->username,'sitename'=>$config['site_name'],'link'=>$url.'?do=profile&u='.$obj->uid.'&p='.$code_activated);
					function holders_replace($matches)
					{
						global $array_holders;

						return $array_holders[$matches[1]];
					}

					$subject = preg_replace_callback('#{([^}]+)}#sUi', 'holders_replace', $translate['template_password_recovery_subject']);
					$body = preg_replace_callback('#{([^}]+)}#sUi',  'holders_replace', $translate['template_password_recovery_body']);

					$hdrs = "Content-type: text/html; charset=".$config['charset']."\r\n";
					$easy_email = new Easy_Email($email, $config['default_email']);
					$easy_email->sendEmail($subject, $body, $hdrs);

					$content_info = '<div class="message success"><p>@@send_success@@</p></div>';
				}
				else
					$content_info = '<div class="message errormsg"><p>@@send_error@@</p></div>';
			}
		}
	}

	$head_info = '@@request_password_recovery_head@@';
	$smarty->assign("head_info", $head_info);
	$smarty->assign("content_info", $content_info);
	$go_page = 'page_small';
}
elseif( isset($_GET["u"]) && isset($_GET["c"]) )
{	unset($content_error, $content_info);
	$activated_uid = trim($_GET["u"]);
	$activated_code = trim($_GET["c"]);

	switch($ext_auth_type)
	{
		case "xf":

			$content_error = "@@activation_error@@";
			break;

		default:

			if (is_numeric($activated_uid))
			{
				$arguments = array('id'=>$activated_uid);
				$select_code = $db->Query("SELECT code_activated FROM `acp_users` WHERE uid = '{id}'", $arguments, $config['sql_debug']);
		
				if (!$select_code)
				{
					$content_error = "@@activation_error@@";
				}
				else
				{
					if ($select_code != $activated_code)
					{
						$content_error = "@@activation_error@@";
					}
					else
					{
						$arguments['group'] = $config['group_for_new_user'];
						$result = $db->Query("UPDATE `acp_users` SET code_activated = '', user_state = 'valid', usergroupid = IF(usergroupid <> 4, usergroupid, {group})  WHERE uid = '{id}'", $arguments, $config['sql_debug']);
						if ($result)
						{
							$content_info = "@@activation_success@@";
						}
						else
						{
							$content_error = "@@activation_error@@";
						}
					}
				}
			}
			else
			{
				$content_error = "@@activation_error@@";
			}
			break;
	}

	if (isset($content_error))
	{		$content_info = '<div class="message errormsg"><p>'.$content_error.'</p></div>';
	}
	else
	{		$content_info = '<div class="message success"><p>'.$content_info.'</p></div>';
	}

	$head_info = '@@email_activated_head@@';
	$smarty->assign("head_info", $head_info);
	$smarty->assign("content_info", $content_info);
	$go_page = 'page_small';
}
elseif( isset($_GET['pay']) && isset($_GET['status']) && $_GET['status'] == 0 )
{
	$method = strtoupper($_GET['pay']);

	switch($method)
	{
		case "ROBOKASSA":

			if( isset($_REQUEST['InvId']) )
			{
				$out_summ = $_REQUEST["OutSum"];
				$inv_id = $_REQUEST["InvId"];
				$crc = strtoupper($_REQUEST["SignatureValue"]);
				$my_crc = strtoupper(md5($out_summ.":".$inv_id.":".$config['ub_robo_password_two']));

				if( $my_crc != $crc )
				{
					die('Verifying the signature information about the payment failed!');
				}

				require_once(INCLUDE_PATH . "class.Payment.php");
			
				$cl = new PAYMENTS($db);
				$resultPID = $cl->pidLoad($inv_id);
				if( $resultPID !== FALSE )
				{
					if( $resultPID['enrolled'] == 0 )
					{
						if( $cl->enrollPayment($resultPID['pid']) !== FALSE )
						{
							$query = $db->Query("UPDATE `acp_users` SET money = money+{summ} WHERE uid = '{uid}'", array('uid' => $resultPID['uid'], 'summ' => $out_summ));
						}
					}
				}
			}
			else
				die('Bad request!');

			break;

		case "A1PAY":

			function A1Lite_processor($t, $secret)
			{
				$params = array(
					'tid' => $t['tid'],
					'name' => $t['name'], 
					'comment' => $t['comment'],
					'partner_id' => $t['partner_id'],
					'service_id' => $t['service_id'],
					'order_id' => $t['order_id'],
					'type' => $t['type'],
					'partner_income' => $t['partner_income'],
					'system_income' => $t['system_income']
				);
				 
				$params['check'] = md5(join('', array_values($params)) . $secret);
				 
				if( $params['check'] === $t['check'] )
				{
					$ok = TRUE;
				}
				else
				{
					$ok = FALSE;
				}
				 
				return $ok;
			}

			if( isset($_REQUEST['order_id']) )
			{
				$out_summ = $_REQUEST["system_income"];
				$inv_id = $_REQUEST["order_id"];

				if( A1Lite_processor($_POST, $config['ub_apay_secretkey']) !== TRUE )
				{
					die('Verifying the signature information about the payment failed!');
				}

				require_once(INCLUDE_PATH . "class.Payment.php");
			
				$cl = new PAYMENTS($db);
				$resultPID = $cl->pidLoad($inv_id);
				if( $resultPID !== FALSE )
				{
					if( $resultPID['enrolled'] == 0 )
					{
						if( $cl->enrollPayment($resultPID['pid']) !== FALSE )
						{
							$query = $db->Query("UPDATE `acp_users` SET money = money+{summ} WHERE uid = '{uid}'", array('uid' => $resultPID['uid'], 'summ' => $out_summ));
						}
					}
				}
			}
			else
				die('Bad request!');

			break;

		default:

			die('Bad request!');

		echo "OK";
	}
}
else
{	$arguments = array('id'=>$userinfo['uid']);
	$result_user = $db->Query("
		SELECT uid, username, password, mail, icq, reg_date, last_visit, timezone, usergroupname, code_activated, user_state, avatar
		FROM `acp_users`
		LEFT JOIN `acp_usergroups` ON acp_users.usergroupid = acp_usergroups.usergroupid
		WHERE acp_users.uid = '{id}'
	", $arguments, $config['sql_debug']);

	if (is_array($result_user))
	{
		foreach ($result_user as $obj)
		{
			$obj->reg_date = ($obj->reg_date > 0) ? get_datetime($obj->reg_date, 'd-m-Y, H:i') : '';
			$obj->last_visit = ($obj->last_visit > 0) ? get_datetime($obj->last_visit, 'd-m-Y, H:i') : '';

			switch($ext_auth_type)
			{
				case "xf":
	
					$obj->avatar = ($obj->avatar) ? $xf->getAvatarFilePath("m").'?'.$obj->avatar : $config['xfAuth']['forumUrl'].'styles/'.$config["template"].'/xenforo/avatars/avatar_'.( ($xf->get("gender")) ? $xf->get("gender")."_" : "" ).'m.png';
					$obj->avatar_date = $userinfo['xf']['avatar_date'];
					break;
	
				default:

					$obj->avatar_date = (strlen($obj->avatar)) ? 1 : 0;	
					$obj->avatar = ($obj->avatar) ? 'acpanel/images/avatars/m/'.$obj->avatar : 'acpanel/images/noavatar_m.gif';
					break;
			}

			$array_user = (array)$obj;
		}

		$smarty->assign("array_user", $array_user);
	}

	if( isset($_GET['paygo']) )
	{
		if( $_GET['paygo'] == 'yes' )
		{
			header('Content-type: text/html; charset='.$config['charset']);

			if( $config['ub_methods'] )
			{
				$arrPayMethods = explode(",", $config['ub_methods']);
				if( count($arrPayMethods) > 1 )
				{
					foreach($arrPayMethods as $method)
					{	
						switch($method)
						{
							case "robokassa":

								$payment[$method] = array(
									'method' => $method,
									'action' => $config['ub_robo_merchant_url'],
									'login' => $config['ub_robo_login'],
									'memo' => $config['ub_robo_memo'],
									'currency' => $config['ub_robo_default_currency'],
									'email' => $userinfo['mail']
								);

								break;

							case "a1pay":

								$payment[$method] = array(
									'method' => $method,
									'action' => $config['ub_apay_merchant_url'],
									'key' => $config['ub_apay_key'],
									'name' => $config['ub_apay_memo'],
									'default_email' => $userinfo['mail']
								);

								break;
						}
					}
				}
				else
				{
					switch($arrPayMethods[0])
					{
						case "robokassa":

							$payment[$arrPayMethods[0]] = array(
								'method' => $arrPayMethods[0],
								'action' => $config['ub_robo_merchant_url'],
								'login' => $config['ub_robo_login'],
								'memo' => $config['ub_robo_memo'],
								'currency' => $config['ub_robo_default_currency'],
								'email' => $userinfo['mail']
							);

							break;

						case "a1pay":

							$payment[$arrPayMethods[0]] = array(
								'method' => $arrPayMethods[0],
								'action' => $config['ub_apay_merchant_url'],
								'key' => $config['ub_apay_key'],
								'name' => $config['ub_apay_memo'],
								'default_email' => $userinfo['mail']
							);

							break;
					}
				}
			}

			if( !$config['ub_methods'] || !isset($payment) )
			{
				$error = "@@payment_not_active@@";
			}

			if( isset($payment) ) $smarty->assign("payment", $payment);
			if( isset($error) ) $smarty->assign("iserror", $error);
			$smarty->assign("min_payment", $config['ub_min_payment']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('profile_balance.tpl');

			exit;
		}
	}

	if( isset($_GET['edit']) )
	{
		if( $_GET['edit'] == 'yes' )
		{			header('Content-type: text/html; charset='.$config['charset']);

			$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
			$smarty->assign("url", $url);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('profile_edit.tpl');

			exit;
		}
	}

	$profilePageArray = array();
	$profilePageArray[1] = '@@general_setting@@';
	$productID = getProduct("gameAccounts");
	if( !empty($productID) ) $profilePageArray[2] = '@@game_account@@';
	$productID = getProduct("userBank");
	if( !empty($productID) ) $profilePageArray[3] = '@@my_shop@@';
	$smarty->assign("profilePageArray", $profilePageArray);

	if( !isset($_GET['s']) || !is_numeric($_GET['s']) || !array_key_exists($_GET['s'], $profilePageArray) )
	{
		header('Location: '.$config['acpanel'].'.php?do=profile&s=1');
		exit;
	}

	$headinclude = "
		<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
		<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
		<script type='text/javascript'>
			(function ($) {
				$(function () {
					$('a[rel*=facebox], input[rel*=facebox]').facebox();
				});
			})(jQuery);
		</script>
	";

	$smarty->assign("get_in", $_GET['s']);

	switch($_GET['s'])
	{		case "1":

			$result_tz = $db->Query("SELECT type, options FROM `acp_config` WHERE varname = 'timezone' LIMIT 1", array(), $config['sql_debug']);
			if (is_array($result_tz))
			{
				foreach ($result_tz as $obj)
				{
					$box = explode("\n", $obj->options);
					foreach($box as $b) {
						$box_value = explode("|", $b);
						$array_tz[$box_value[0]] = $box_value[1];
					}
				}

				$smarty->assign("array_tz", $array_tz);
			}

			$headinclude .= "
				<script type='text/javascript' src='acpanel/scripts/js/jquery.ajaxupload.js'></script>
				<script type='text/javascript'>
					function delete_avatar(subjm,txt)
					{
						if (confirm(txt))
						{
							jQuery.ajax({
								type:'POST',
								url:'acpanel/ajax.php?do=ajax_homepage',
								data:({uid : subjm,'go' : 8}),
								success:function(result) {
									if(result.indexOf('id=\"success\"') + 1)
									{
										humanMsg.displayMsg(result,'success');
										jQuery('img.map-block').attr('src','".( ($ext_auth_type == 'xf') ? $config['xfAuth']['forumUrl'].'styles/'.$config["template"].'/xenforo/avatars/avatar_'.( ($gender = $xf->get("gender")) ? $gender."_" : "").'m.png' : 'acpanel/images/noavatar_m.gif' )."').removeClass('custom-ava');
									}
									else
									{
										humanMsg.displayMsg(result,'error');
									}
								}
							});
						}
						return false;
					}

					jQuery(document).ready(function($) {

						var button = $('#uploadButton');
						var maplink;
						var uid = ".$array_user['uid'].";

						$.ajax_upload(button, {
							action: 'acpanel/upload.php',
							name: 'userfile',
							data: {uid: uid,type: 'avatar'},
							onSubmit: function(file, ext) {
								this.disable();
								maplink = $('img.map-block').attr('src');
								$('img.map-block').addClass('load-load');
								$('img.map-block').attr('src', 'acpanel/images/ajax-160-120-upload.gif');
							},
							onComplete: function(file, response) {
								this.enable();
								setTimeout(function() {
									if(response.indexOf('id=\"error\"') + 1)
									{
										humanMsg.displayMsg(response,'error');
										$('img.map-block').attr('src', maplink);
									}
									else
									{
										$('img.map-block').attr({'src': response + '?' + new Date().getTime()}).addClass('custom-ava');
									}
									$('img.map-block').removeClass('load-load');
								}, 2000);
							}
						});

						$('.avatar-img').mouseenter(function() {
							if( $('img.map-block').hasClass('custom-ava') && !$('img.map-block').hasClass('load-load') )
							{
								$('.avatar-block ul').css({'display':'block','left':this.offsetLeft+55,'top':this.offsetTop+55});
							}
						}).mouseleave(function() {
							$('.avatar-block ul').fadeOut(400);
						});

						$('#forma-edit').submit(function() {
							var data = $(this).serialize();

							$.ajax({
								type:'POST',
								url:'acpanel/ajax.php?do=ajax_homepage',
								data:data + '&go=7',
								success:function(result) {
									if(result.indexOf('id=\"success\"') + 1)
									{
										humanMsg.displayMsg(result,'success');
									}
									else
									{
										humanMsg.displayMsg(result,'error');
									}
								}
							});

							return false;
						});
					});
				</script>
			";

			if ($array_user['user_state'] != 'valid')
			{
				switch($array_user['user_state'])
				{
					case "email_confirm":

						$warnings[] = "@@need_activated_registr@@";
						break;

					case "moderated":

						$warnings[] = "@@need_activated_email@@";
						break;
				}
			}

			$smarty->assign("avatar_width", $config['avatar_width']);
			$smarty->assign("avatar_height", $config['avatar_height']);

			break;

		case "2":

			// ACCOUNTS STATUS INFO
			// -1 - account blocked
			// 0 - not account
			// 1 - first reg account
			// 2 - account moderate
			// 3 - account active

			if( $userinfo['user_state'] != 'valid' )
			{
				switch( $userinfo['user_state'] )
				{
					case "email_confirm":

						$error = "@@user_email_confirm@@";
						break;

					default:

						$error = "@@user_moderated@@";
						break;
				}
			}
			elseif( $config['ga_registration'] == 3 && strlen($userinfo['hid']) < 5 )
			{
				$error = "@@user_notvalid_for_regaccount@@";
			}
			else
			{
				$arguments = array('id'=>$userinfo['uid']);
				$result_user = $db->Query("SELECT userid, timestamp, flag, player_nick, password, player_ip, steamid, last_time, approved, online, 
					(SELECT ticket_status FROM `acp_players_requests` WHERE userid = {id} AND ticket_status = 0 LIMIT 1) AS ticket 
					FROM `acp_players` WHERE userid = {id}", $arguments, $config['sql_debug']);
		
				if( is_array($result_user) )
				{
					foreach( $result_user as $obj )
					{
						$obj->timestamp = ($obj->timestamp > 0) ? get_datetime($obj->timestamp, 'd-m-Y, H:i') : '-';
						$obj->last_time = ($obj->last_time > 0) ? get_datetime($obj->last_time, 'd-m-Y, H:i') : '-';
						$obj->online = compacttime($obj->online, $config['ga_time_format']);
		
						$array_user = (array)$obj;
					}
		
					if( $array_user['approved'] == 'no' )
					{
						$account_status = -1;
						$warnings[] = "@@block_account@@";
					}
					elseif( !is_null($array_user['ticket']) )
					{
						$account_status = 2;
						$warnings[] = "@@ticket_on_moderate@@";
					}
					else
					{
						$account_status = 3;
					}
				}
				else
				{
					$array_user['userid'] = $userinfo['uid'];
					$result_reg = $db->Query("SELECT fields_update FROM `acp_players_requests` WHERE userid = {id} AND ticket_status = 0 AND productid = 'gameAccounts' LIMIT 1", $arguments, $config['sql_debug']);
	
					if (is_array($result_reg))
					{
						foreach ($result_reg as $obj)
						{
							$fields_update = unserialize($obj->fields_update);
							$array_user = array('flag'=>$fields_update['flag'],'player_nick'=>$fields_update['player_nick'],'player_ip'=>$fields_update['player_ip'],'steamid'=>$fields_update['steamid']);
						}
						$account_status = 1;
						$warnings[] = "@@ticket_reg_on_moderate@@";
					}
					else
					{
						if( $config['ga_registration'] == 1 )
						{
							$error = "@@ga_registration_closed@@";
						}
						else
						{
							$warnings[] = "@@not_account@@";
						}

						$account_status = 0;
					}
				}
	
				$smarty->assign("account", $array_user);
				$ga_access_type = ( $config['ga_access_type'] ) ? explode(',', $config['ga_access_type']) : array();
				$smarty->assign("ga_access_type",$ga_access_type);
				$smarty->assign("account_status",$account_status);
	
				if( isset($_GET['account']) )
				{
					if( $_GET['account'] == 'edit' )
					{
						header('Content-type: text/html; charset='.$config['charset']);
	
						unset($error, $warnings);
			
						switch($account_status)
						{
							case "-1":
	
								$error = "@@block_account@@";
								break;
	
							case "1":
	
								$error = "@@ticket_reg_on_moderate@@";
								break;
	
							case "2":
	
								$error = "@@ticket_on_moderate@@";
								break;
	
							case "3":
	
								if( $config['ticket_moderate'] )
								{
									switch( $array_user['flag'] )
									{
										case "1":
					
											$warnings[] = "@@moderate_change_nick@@";
											break;
					
										case "2":
					
											$warnings[] = "@@moderate_change_ip@@";
											break;
				
										case "3":
					
											$warnings[] = "@@moderate_change_steam@@";
											break;
									}
								}
								break;
						}
	
						if (isset($warnings)) $smarty->assign("iswarn",$warnings);
						if (isset($error)) $smarty->assign("iserror",$error);
			
						$smarty->registerFilter("output","translate_template");
						$smarty->display('profile_account.tpl');
			
						exit;
					}
				}
	
				$total_items = $db->Query("SELECT count(*) FROM `acp_players_requests` WHERE userid = '{id}'", $arguments, $config['sql_debug']);
	
				$headinclude .= "
					<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
					<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
					<script type='text/javascript'>
	
						function refreshAccount() {
							var uid = ".$userinfo['uid'].";
	
							jQuery.ajax({
								type:'POST',
								url:'acpanel/ajax.php?do=ajax_gamecp',
								data:'go=17&uid=' + uid,
								success:function(result) {
									jQuery('#accountInfoBox').html(result);
								}
							});
				
							return false;
						}
				
						function pageselectCallback(page_id, total, jq) {
							var uid = ".$userinfo['uid'].";
							var pg_size = ".$config['pagesize'].";
							var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
				
							if(total < second)
							{
								second = total;
							}
				
							if(!total)
							{
								jQuery('#Searchresult').html('@@showing@@ 0 @@to@@ 0 @@of@@ <span>0</span>');
							}
							else
							{
								jQuery('#Searchresult').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
							}
				
							jQuery.ajax({
								type:'POST',
								url:'acpanel/ajax.php?do=ajax_gamecp',
								data:'go=11&uid=' + uid + '&offset=' + first + '&limit=' + pg_size,
								success:function(result) {
									jQuery('#ajaxContent').html(result);
								}
							});
				
							return false;
						}
				
						function rePagination(diff) {
							var total = parseInt(jQuery('#Searchresult span').text()) + diff;
				
							if(total == 0)
							{
								jQuery('.tablesorter').append(jQuery('<tfoot>')
									.append(jQuery('<tr>').addClass('emptydata')
										.append(jQuery('<td>').attr('colspan', '3').html('@@empty_data@@'))
									)
								);
							}
				
							var pg_size = ".$config['pagesize'].";
							var set_page = parseInt(jQuery('.pagination span.active').not('.prev, .next').text()) - 1;
							var count_row = jQuery('.tablesorter tbody tr').length + diff;
				
							if(count_row <= 0 && diff < 0 && total && set_page)
							{
								set_page = set_page - 1;
							}
				
							jQuery('#Pagination').pagination( total, {
								num_edge_entries: 2,
								num_display_entries: 8,
								callback: pageselectCallback,
								items_per_page: pg_size,
								current_page: set_page
							});
						}
				
						jQuery(document).ready(function($) {
							$('#Pagination').pagination( ".$total_items.", {
								num_edge_entries: 2,
								num_display_entries: 8,
								callback: pageselectCallback,
								items_per_page: ".$config['pagesize']."
							});
						});
					</script>
				";
			}

			break;

		case "3":

			if( isset($_GET['pay']) && isset($_GET['status']) && $_GET['status'] > 0 )
			{
				$payMethod = strtoupper($_GET['pay']);
			
				switch($payMethod)
				{
					case "ROBOKASSA":
			
						if( isset($_REQUEST['InvId']) )
						{
							$out_summ = $_REQUEST["OutSum"];
							$inv_id = $_REQUEST["InvId"];

							if( $_GET['status'] == 2 )
							{
								$crc = strtoupper($_REQUEST["SignatureValue"]);
								$my_crc = strtoupper(md5($out_summ.":".$inv_id.":".$config['ub_robo_password_one']));

								if( $my_crc != $crc )
								{
									die('Verifying the signature information about the payment failed!');
								}
							}

							require_once(INCLUDE_PATH . "class.Payment.php");

							$cl = new PAYMENTS($db);
							$resultPID = $cl->pidLoad($inv_id);
							if( $resultPID !== FALSE )
							{
								if( $_GET['status'] == 1 )
								{
									if( $resultPID['enrolled'] == 0 )
									{
										if( $cl->deletePayment($resultPID['pid']) !== FALSE )
											$info_message = "<div style='margin-top:0;' class='message errormsg'><p>@@payment_failed_pre@@".$resultPID['pid']."@@payment_failed_post@@</p></div>";
									}
								}
								elseif( $_GET['status'] == 2 )
								{
									$info_message = "<div style='margin-top:0;' class='message success'><p>@@payment_success_pre@@".$resultPID['pid']."@@payment_success_post@@".round($out_summ,2)." ".$config['ub_currency_suffix']."</p></div>";
									if( $resultPID['enrolled'] == 0 )
									{
										if( $cl->enrollPayment($resultPID['pid']) !== FALSE )
										{
											if( $query = $db->Query("UPDATE `acp_users` SET money = money+{summ} WHERE uid = '{uid}'", array('uid' => $resultPID['uid'], 'summ' => round($out_summ,2))) )
												$userinfo['money'] = $db->Query("SELECT money FROM `acp_users` WHERE uid = '{uid}'", array('uid' => $resultPID['uid']));
										}
									}
								}
							}
						}
						else
							die('Bad request!');
			
						break;

					case "A1PAY":

						function A1Lite_processor($t, $secret)
						{
							$params = array(
								'tid' => $t['tid'],
								'name' => $t['name'], 
								'comment' => $t['comment'],
								'partner_id' => $t['partner_id'],
								'service_id' => $t['service_id'],
								'order_id' => $t['order_id'],
								'type' => $t['type'],
								'partner_income' => $t['partner_income'],
								'system_income' => $t['system_income']
							);
							 
							$params['check'] = md5(join('', array_values($params)) . $secret);
							 
							if( $params['check'] === $t['check'] )
							{
								$ok = TRUE;
							}
							else
							{
								$ok = FALSE;
							}
							 
							return $ok;
						}
			
						if( isset($_REQUEST['order_id']) )
						{
							$out_summ = $_REQUEST["system_income"];
							$inv_id = $_REQUEST["order_id"];

							if( $_GET['status'] == 2 )
							{
								if( A1Lite_processor($_REQUEST, $config['ub_apay_secretkey']) !== TRUE )
								{
									die('Verifying the signature information about the payment failed!');
								}
							}

							require_once(INCLUDE_PATH . "class.Payment.php");

							$cl = new PAYMENTS($db);
							$resultPID = $cl->pidLoad($inv_id);
							if( $resultPID !== FALSE )
							{
								if( $_GET['status'] == 1 )
								{
									if( $resultPID['enrolled'] == 0 )
									{
										if( $cl->deletePayment($resultPID['pid']) !== FALSE )
											$info_message = "<div style='margin-top:0;' class='message errormsg'><p>@@payment_failed_pre@@".$resultPID['pid']."@@payment_failed_post@@</p></div>";
									}
								}
								elseif( $_GET['status'] == 2 )
								{
									$info_message = "<div style='margin-top:0;' class='message success'><p>@@payment_success_pre@@".$resultPID['pid']."@@payment_success_post@@".round($out_summ,2)." ".$config['ub_currency_suffix']."</p></div>";
									if( $resultPID['enrolled'] == 0 )
									{
										if( $cl->enrollPayment($resultPID['pid']) !== FALSE )
										{
											if( $query = $db->Query("UPDATE `acp_users` SET money = money+{summ} WHERE uid = '{uid}'", array('uid' => $resultPID['uid'], 'summ' => round($out_summ,2))) )
												$userinfo['money'] = $db->Query("SELECT money FROM `acp_users` WHERE uid = '{uid}'", array('uid' => $resultPID['uid']));
										}
									}
								}
							}
						}
						else
							die('Bad request!');
			
						break;

					default:
			
						die('Bad request!');
				}
			}

			if( $config['ub_methods'] )
			{
				$arrPayMethods = explode(",", $config['ub_methods']);
				if( count($arrPayMethods) > 1 )
				{
					foreach($arrPayMethods as $method)
					{	
						switch($method)
						{
							case "robokassa":

								$payment = true;

								break;

							case "a1pay":

								$payment = true;

								break;
						}
					}
				}
				else
				{
					switch($arrPayMethods[0])
					{
						case "robokassa":

 							$payment = true;

							break;

						case "a1pay":

 							$payment = true;

							break;
					}
				}
			}

			if( !$config['ub_methods'] || !isset($payment) )
			{
				$error = true;
			}

			$productID = getProduct("gameAccounts");
			if( empty($productID) ) $userinfo['points'] = false;
			else
			{
				$query = $db->Query("SELECT points FROM `acp_players` WHERE userid = '{uid}'", array('uid' => $userinfo['uid']), $config['sql_debug']);
				$userinfo['points'] = ( is_null($query) ) ? false : $query;
			}

			$total_items = $db->Query("SELECT count(*) FROM `acp_payment` WHERE uid = '{uid}' AND enrolled > 0", array('uid' => $userinfo['uid']), $config['sql_debug']);
			$total_items_privs = $db->Query("SELECT count(*) FROM `acp_payment_user` WHERE uid = '{uid}'", array('uid' => $userinfo['uid']), $config['sql_debug']);

			$headinclude .= "
				<link href='acpanel/templates/".$config['template']."/css/usershop.css' rel='stylesheet' type='text/css' />
				<script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
				<script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
				<script type='text/javascript'>
			
					function pageselectCallback(page_id, total, jq) {
						var pg_size = ".$config['pagesize'].";
						var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
			
						if(total < second)
						{
							second = total;
						}
			
						if(!total)
						{
							jQuery('#Searchresult').html('@@showing@@ 0 @@to@@ 0 @@of@@ <span>0</span>');
						}
						else
						{
							jQuery('#Searchresult').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
						}
			
						jQuery.ajax({
							type:'POST',
							url:'acpanel/ajax.php?do=ajax_payment',
							data:'go=2&offset=' + first + '&limit=' + pg_size,
							success:function(result) {
								jQuery('#ajaxContent').html(result);
							}
						});
			
						return false;
					}

					function pageselectCallbackS(page_id, total, jq) {
						var pg_size = ".$config['pagesize'].";
						var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
			
						if(total < second)
						{
							second = total;
						}
			
						if(!total)
						{
							jQuery('#SearchresultS').html('@@showing@@ 0 @@to@@ 0 @@of@@ <span>0</span>');
						}
						else
						{
							jQuery('#SearchresultS').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
						}
			
						jQuery.ajax({
							type:'POST',
							url:'acpanel/ajax.php?do=ajax_payment',
							data:'go=23&offset=' + first + '&limit=' + pg_size,
							success:function(result) {
								jQuery('#ajaxContentS').html(result);
							}
						});
			
						return false;
					}
			
					function rePagination(diff) {
						var total = parseInt(jQuery('#Searchresult span').text()) + diff;
			
						if(total == 0)
						{
							jQuery('.tablesorter').append(jQuery('<tfoot>')
								.append(jQuery('<tr>').addClass('emptydata')
									.append(jQuery('<td>').attr('colspan', '4').html('@@empty_data@@'))
								)
							);
						}
			
						var pg_size = ".$config['pagesize'].";
						var set_page = parseInt(jQuery('.pagination span.active').not('.prev, .next').text()) - 1;
						var count_row = jQuery('.tablesorter tbody tr').length + diff;
			
						if(count_row <= 0 && diff < 0 && total && set_page)
						{
							set_page = set_page - 1;
						}
			
						jQuery('#Pagination').pagination( total, {
							num_edge_entries: 2,
							num_display_entries: 8,
							callback: pageselectCallback,
							items_per_page: pg_size,
							current_page: set_page
						});
					}
			
					jQuery(document).ready(function($) {
						$('#Pagination').pagination( ".$total_items.", {
							num_edge_entries: 2,
							num_display_entries: 8,
							callback: pageselectCallback,
							items_per_page: ".$config['pagesize']."
						});

						$('#PaginationS').pagination( ".$total_items_privs.", {
							num_edge_entries: 2,
							num_display_entries: 8,
							callback: pageselectCallbackS,
							items_per_page: ".$config['pagesize']."
						});
					});
				</script>
			";

			if( isset($info_message) ) $smarty->assign("info_message", $info_message);
			if( isset($payment) ) $smarty->assign("payment", $payment);
			$smarty->assign("userinfo", $userinfo);
			$smarty->assign("userbank", array('money_suffix' => $config['ub_currency_suffix'], 'money_rate' => $config['ub_rate_points'], 'commission' => $config['ub_commission_exchanger']));

			break;
	}

	if( isset($warnings) ) $smarty->assign("iswarn",$warnings);
	if( isset($error) ) $smarty->assign("iserror",$error);
	$smarty->assign("home",$config['acpanel'].'.php');
}

?>
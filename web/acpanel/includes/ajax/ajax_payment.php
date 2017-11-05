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

	if(is_array($array_cfg)) {
		foreach ($array_cfg as $obj){
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();
	}

	include(INCLUDE_PATH . 'functions.main.php');
	$langs = create_lang_list();

	unset($translate);
	$filter = "lp_name='profile.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
	$arguments = array('lang'=>get_language(1));
	$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
	if(is_array($tr_result)) {
		foreach ($tr_result as $obj){
			$translate[$obj->lw_word] = $obj->lw_translate;
		}
	}

	include(INCLUDE_PATH . '_auth.php');

	header('Content-type: text/html; charset='.$config['charset']);

	// 1 - create payment
	// 2 - list transactions
	// 3 - exchanger
	// 4 - load stats payment for admin
	// 5 - creat groups list
	// 6 - delete group
	// 7 - multiple delete groups
	// 8 - add group
	// 9 - edit group
	// 10 - create payment history list for admin
	// 11 - multiple delete bill
	// 12 - create patterns list
	// 13 - parent change active
	// 14 - delete pattern
	// 15 - multiple delete patterns
	// 16 - creat privileges list for admin
	// 17 - add privilege pattern
	// 18 - edit privilege pattern

	// 22 - buy privilege
	// 23 - privileges list for user in profile
	// 24 - create gameshop items list
	// 25 - delete item
	// 26 - multiple delete items
	// 27 - change status for item
	// 28 - add new item
	// 29 - edit item

	switch($_POST['go'])
	{
		case "1":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['read'] || $userinfo['admin_access'] == 'yes' ) 
			{
				switch($_POST['method'])
				{
					case "robokassa":

						$params = array(
							'uid' => $userinfo['uid'],
							'created' => time(),
							'amount' => $_POST['OutSum'],
							'memo' => $_POST['Desc']
						);

						$method = strtoupper($_POST['method']);
						require_once(INCLUDE_PATH . "class.Payment.php");

						$cl = new PAYMENTS($db);
						$resultCreate = $cl->createPayment($params);
						if( $resultCreate === FALSE )
							$dateArray = array('error' => $cl->GetErrorInfo());
						else
							$dateArray = array('error' => 0, 'sig' => md5($config['ub_robo_login'].":".$_POST['OutSum'].":".$resultCreate['pid'].":".$config['ub_robo_password_one']), 'pid' => $resultCreate['pid']);

						break;

					case "a1pay":

						$params = array(
							'uid' => $userinfo['uid'],
							'created' => time(),
							'amount' => $_POST['cost'],
							'memo' => $_POST['name']
						);

						$method = strtoupper($_POST['method']);
						require_once(INCLUDE_PATH . "class.Payment.php");

						$cl = new PAYMENTS($db);
						$resultCreate = $cl->createPayment($params);
						if( $resultCreate === FALSE )
							$dateArray = array('error' => $cl->GetErrorInfo());
						else
							$dateArray = array('error' => 0, 'secretkey' => $config['ub_apay_key'], 'pid' => $resultCreate['pid']);

						break;
				}

				if( !isset($dateArray) ) $dateArray = array('error' => $translate['payment_method_not_find']);
			}
			else $dateArray = array('error' => $translate['access_denied']);

			echo json_encode($dateArray);

			break;

		case "2":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset' => $offset, 'limit' => $limit, 'uid' => $userinfo['uid']);
			$result = $db->Query("SELECT pid, created, amount, enrolled, memo, currency FROM `acp_payment` WHERE uid = '{uid}' AND enrolled > 0 ORDER BY pid DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach($result as $obj)
				{
					$obj->created = ($obj->created > 0) ? get_datetime($obj->created, $config['date_format']) : "-";
					$obj->enrolled = ($obj->enrolled > 0) ? get_datetime($obj->enrolled, $config['date_format']) : "-";
					$obj->amount = round($obj->amount, 2)." ".(($obj->currency == "mm") ? $config['ub_currency_suffix'] : "@@points_suffix@@");

					$transactions[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			if(isset($transactions)) $smarty->assign("transactions",$transactions);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('profile_transactions.tpl');

			break;

		case "3":

			if( isset($_POST['cnt']) && is_numeric($_POST['cnt']) && $_POST['cnt'] > 0 )
			{
				if( isset($_POST['type']) && in_array($_POST['type'], array(1,2)) )
				{
					$arguments = array('uid' => $userinfo['uid'], 'points' => $_POST['cnt'], 'time' => time(), 'memo' => ($_POST['type'] == 1) ? $translate['memo_buy_points'] : $translate['memo_sell_points']);
					$result = $db->Query("SELECT a.uid, a.money, b.points FROM `acp_users` a LEFT JOIN `acp_players` b ON a.uid = b.userid WHERE a.uid = '{uid}' LIMIT 1", $arguments, $config['sql_debug']);
		
					if( is_array($result) )
					{
						foreach($result as $obj)
						{
							if( $_POST['type'] == 1 )
							{
								$arguments['money'] = round($arguments['points']*$config['ub_rate_points'], 2);
								if( $obj->money >= $arguments['money'] )
								{
									if( $query = $db->Query("UPDATE `acp_players` SET points = points + {points} WHERE userid = '{uid}'", $arguments, $config['sql_debug']) )
									{
										$query = $db->Query("INSERT INTO `acp_payment` (uid, amount, created, enrolled, memo, currency, pattern) VALUES ('{uid}', '{points}', '{time}', '{time}', '{memo}', 'points', '0')", $arguments, $config['sql_debug']);

										if( $query = $db->Query("UPDATE `acp_users` SET money = money - {money} WHERE uid = '{uid}'", $arguments, $config['sql_debug']) )
											$query = $db->Query("INSERT INTO `acp_payment` (uid, amount, created, enrolled, memo, currency, pattern) VALUES ('{uid}', '-{money}', '{time}', '{time}', '{memo}', 'mm', '0')", $arguments, $config['sql_debug']);
						
										if( !$query )
										{
											print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_query_error'].'</span>';
										}
										else
										{
											print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['exchange_success'].'</span>';
										}
									}
									else
									{
										print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_query_error'].'</span>';
									}
								}
								else
								{
									print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_not_enough_money_error'].'</span>';
								}
							}
							elseif( $_POST['type'] == 2 )
							{
								$arguments['money'] = $arguments['points']*$config['ub_rate_points'];
								if( $config['ub_commission_exchanger'] > 0 )
									$arguments['money'] = $arguments['money'] - ($arguments['money']/100*$config['ub_commission_exchanger']);

								$arguments['money'] = round($arguments['money'], 2);

								if( $obj->points >= $arguments['points'] )
								{
									if( $query = $db->Query("UPDATE `acp_players` SET points = points - {points} WHERE userid = '{uid}'", $arguments, $config['sql_debug']) )
									{
										$query = $db->Query("INSERT INTO `acp_payment` (uid, amount, created, enrolled, memo, currency, pattern) VALUES ('{uid}', '-{points}', '{time}', '{time}', '{memo}', 'points', '0')", $arguments, $config['sql_debug']);

										if( $query = $db->Query("UPDATE `acp_users` SET money = money + {money} WHERE uid = '{uid}'", $arguments, $config['sql_debug']) )
											$query = $db->Query("INSERT INTO `acp_payment` (uid, amount, created, enrolled, memo, currency, pattern) VALUES ('{uid}', '{money}', '{time}', '{time}', '{memo}', 'mm', '0')", $arguments, $config['sql_debug']);
						
										if( !$query )
										{
											print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_query_error'].'</span>';
										}
										else
										{
											print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['exchange_success'].'</span>';
										}
									}
									else
									{
										print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_query_error'].'</span>';
									}
								}
								else
								{
									print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_not_enough_money_error'].'</span>';
								}
							}
						}
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_not_user_error'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_incorrect_type_error'].'</span>';
				}	
			}
			else
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['exchange_incorrect_cnt_error'].'</span>';
			}

			break;

		case "4":

			date_default_timezone_set('UTC');

			function getDateArray($type, $currTime)
			{
				global $db;

				$arrOut = array();
				list($currYear, $currMonth, $currDay, $currHour) = explode("-", date('Y-m-d-H', $currTime));

				switch($type)
				{
					case "w":

						$currDateString = $currYear."-".$currMonth."-".$currDay." 00:00:00";
						$startTime = strtotime($currDateString) - (3600*24*6);

						$query = $db->Query("SELECT enrolled, FROM_UNIXTIME(enrolled, '%Y-%m-%d') AS time, SUM(amount) AS cnt FROM `acp_payment` WHERE pattern = '-1' AND enrolled > ".$startTime." GROUP BY time LIMIT 7", array());

						$i = 0;
						while( $i < 7 )
						{
							$index = $startTime."000";
							$arrOut["pay"][$index] = 0;
							$startTime = $startTime + 86400;
							$i++;
						}
						break;

					case "y":

						$currDateString = $currYear."-".$currMonth."-01 00:00:00";
						$startTime = strtotime("1 year ago", strtotime($currDateString));
						$startTime = strtotime("next month", $startTime);

						$query = $db->Query("SELECT enrolled, FROM_UNIXTIME(enrolled, '%Y-%m') AS time, SUM(amount) AS cnt FROM `acp_payment` WHERE pattern = '-1' AND enrolled > ".$startTime." GROUP BY time LIMIT 12", array());

						$i = 0;
						while( $i < 12 )
						{
							$index = $startTime."000";
							$arrOut["pay"][$index] = 0;
							$startTime = strtotime("next month", $startTime);
							$i++;
						}
						break;
				}

				if( is_array($query) )
				{
					foreach( $query as $obj )
					{
						$bd_time = explode("-", $obj->time);
						$time = strtotime($bd_time[0]."-".$bd_time[1]."-".((isset($bd_time[2])) ? $bd_time[2] : '01')." 00:00:00");
						$index = $time."000";

						if( isset($arrOut["pay"][$index]) )
							$arrOut["pay"][$index] += $obj->cnt;
					}
				}

				return $arrOut;
			}

			$action = $_POST['action'];
			$dateArray = array();

			if( in_array($action, array('w', 'y')) )
			{
				$dateArray = getDateArray($action, time());
			}

			echo json_encode($dateArray);
			break;

		case "5":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT * FROM `acp_payment_groups` LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				$privileges_count = array();
				$result_priv = $db->Query("SELECT a.gid, count(b.pattern_id) AS cnt FROM `acp_payment_groups` a 
					LEFT JOIN `acp_payment_groups_patterns` b ON a.gid = b.gid 
					GROUP BY a.gid", array(), $config['sql_debug']);

				if( is_array($result_priv) )
				{
					foreach( $result_priv as $obj_p )
					{
						$privileges_count[$obj_p->gid] = $obj_p->cnt;
					}
				}

				foreach( $result as $obj )
				{
					$obj->privileges = ( isset($privileges_count[$obj->gid]) ) ? $privileges_count[$obj->gid] : 0;
					$groups[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($groups)) $smarty->assign("groups",$groups);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("tpl", $config['template']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_usershop_admin_groups_list.tpl');

			break;

		case "6":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_payment_groups` WHERE gid = '{id}'", $arguments, $config['sql_debug']);
		
				if( $result )
				{	
					$result = $db->Query("DELETE FROM `acp_payment_groups_patterns` WHERE gid = '{id}'", $arguments, $config['sql_debug']);
	
					if( $result )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "delete group id: ".$id);
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_group_success'].'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_group_privileges_failed'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_group_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "7":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_payment_groups` WHERE gid IN ('{ids}')", $arguments, $config['sql_debug']);				
	
				if( $result )
				{
					$result = $db->Query("DELETE FROM `acp_payment_groups_patterns` WHERE gid IN ('{ids}')", $arguments, $config['sql_debug']);

					if( $result )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "myltiple delete groups: ".count($ids));
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.count($ids).'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_group_privileges_failed'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_group_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "8":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$name = trim($_POST['name']);
	
				if( $name == "" )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['group_name_is_empty'].'</span>';
				}
				else
				{
					$arguments = array('name'=>$name, 'description'=>trim($_POST['description']));
	
					$result = $db->Query("INSERT INTO `acp_payment_groups` (name, description) VALUES ('{name}', '{description}')", $arguments, $config['sql_debug']);
	
					if( !$result )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_group_failed'].'</span>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "add payment group");
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_group_success'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "9":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['gid'];
				$name = trim($_POST['name']);
	
				if( $name == "" )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['group_name_is_empty'].'</span>';
				}
				else
				{
					$arguments = array('id' => $id, 'name'=>$name, 'description'=>trim($_POST['description']));
	
					$result = $db->Query("UPDATE `acp_payment_groups` SET name = '{name}', description = '{description}' WHERE gid = {id}", $arguments, $config['sql_debug']);
	
					if( !$result )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_group_failed'].'</span>';
					}
					else
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "edit payment group: ".$id);
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_group_success'].'</span>';
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "10":

			$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_users'", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach( $result_cats as $obj )
				{
					$cat_users = $obj->sectionid;
					$cat_user_edit = $obj->categoryid;
				}
			}

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$s = $_POST['s'];

			$sqlconds = "WHERE 1 = 1";
			$arguments = array('offset'=>$offset,'limit'=>$limit);

			if( $s != -2 && is_numeric($s) )
			{
				$sqlconds .= " AND pattern = ".$s;
			}

			$result = $db->Query("SELECT a.pid, a.uid, a.amount, a.created, a.memo, a.enrolled, a.currency, a.pattern, b.username FROM `acp_payment` a 
				LEFT JOIN `acp_users` b ON b.uid = a.uid ".$sqlconds." ORDER BY a.pid DESC LIMIT {offset},{limit}
			", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$obj->created = ($obj->created > 0) ? get_datetime($obj->created, $config['date_format']) : "-";
					$obj->enrolled = ($obj->enrolled > 0) ? get_datetime($obj->enrolled, $config['date_format']) : "";
					$obj->amount = round($obj->amount, 2)." ".(($obj->currency == "mm") ? $config['ub_currency_suffix'] : "@@points_suffix@@");
					$obj->username = ( !$obj->username ) ? '<span style="text-decoration:line-through;">@@user@@</span>' : '<a href="'.$config['acpanel'].'.php?cat='.$cat_users.'&do='.$cat_user_edit.'&t=0&id='.$obj->uid.'">'.htmlspecialchars($obj->username).'</a>';

					$transactions[] = (array)$obj;
				}
			}

			require_once("scripts/smarty/Smarty.class.php");

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($cat_users)) $smarty->assign("cat_users", $cat_users);
			if(isset($cat_user_edit)) $smarty->assign("cat_user_edit", $cat_user_edit);
			$smarty->assign("get_status",$s);
			if(isset($transactions)) $smarty->assign("transactions",$transactions);
			if(isset($error)) $smarty->assign("iserror",$error);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_usershop_admin_payments_list.tpl');

			break;

		case "11":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_payment` WHERE pid IN ('{ids}')", $arguments, $config['sql_debug']);				
	
				if( $result )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "myltiple delete bills: ".count($ids));
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.count($ids).'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_bill_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "12":

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];

			$t = $_POST['grupa'];

			$sqlconds = "WHERE 1 = 1";
			$arguments = array('offset'=>$offset,'limit'=>$limit);

			if( $t != 0 )
			{
				$sqlconds .= " AND b.gid = ".$t;
			}

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT a.id, a.name, a.price_mm, a.price_points, a.duration_type, a.active, a.item_duration, a.item_duration_select, 
				a.purchased, a.max_sale_items, a.max_sale_items_duration, a.max_sale_for_user, a.max_sale_for_user_duration 
				FROM `acp_payment_patterns` a
				LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
				".$sqlconds." GROUP BY a.id ORDER BY a.id DESC LIMIT {offset},{limit}
			", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					switch($obj->duration_type)
					{
						case "date":

							$obj->item_duration = ($obj->item_duration > 0) ? get_datetime($obj->item_duration, $config['date_format']) : "@@all_duration@@";
							break;

						case "year":
						case "month":
						case "day":

							$obj->item_duration = get_correct_str($obj->item_duration, "@@time_".$obj->duration_type."_one@@", "@@time_".$obj->duration_type."_several@@", "@@time_".$obj->duration_type."_many@@");
							break;
					}
					if( $obj->price_mm > 0 ) $obj->price_mm_info = $obj->price_mm.$config['ub_currency_suffix'];
					if( $obj->price_points > 0 ) $obj->price_points_info = $obj->price_points."@@points_suffix@@";
					if( $obj->max_sale_items > 0 )
					{
						if( $obj->max_sale_items_duration )
						{
							$obj->max_sale_items_info = $obj->max_sale_items.(($obj->max_sale_items_duration == 'total') ? "" : "/@@".$obj->max_sale_items_duration."@@");
						}
					}
					if( $obj->max_sale_for_user > 0 )
					{
						if( $obj->max_sale_for_user_duration )
						{
							$obj->max_sale_for_user = $obj->max_sale_for_user.(($obj->max_sale_for_user_duration == 'total') ? "" : "/@@".$obj->max_sale_for_user_duration."@@");
						}
					}

					$patterns[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($patterns)) $smarty->assign("patterns",$patterns);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("tpl", $config['template']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_usershop_admin_patterns_list.tpl');

			break;

		case "13":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST["id"];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("UPDATE `acp_payment_patterns` SET active = IF(active = '0', '1', '0') WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "change status for pattern: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['change_status_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['change_status_error'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "14":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_payment_patterns` WHERE id = '{id}'", $arguments, $config['sql_debug']);
		
				if( $result )
				{
					$result = $db->Query("DELETE FROM `acp_payment_patterns_server` WHERE pattern_id = '{id}'", $arguments, $config['sql_debug']);
					$result = $db->Query("DELETE FROM `acp_payment_patterns_usergroups` WHERE pattern_id = '{id}'", $arguments, $config['sql_debug']);
					$result = $db->Query("DELETE FROM `acp_payment_groups_patterns` WHERE pattern_id = '{id}'", $arguments, $config['sql_debug']);

					$result = $db->Query("DELETE FROM `acp_payment_user` WHERE pattern_id = '{id}'", $arguments, $config['sql_debug']);
	
					if( $result )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "delete pattern id: ".$id);
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_pattern_success'].'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_pattern_user_failed'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_group_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "15":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_payment_patterns` WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);				
	
				if( $result )
				{
					$result = $db->Query("DELETE FROM `acp_payment_patterns_server` WHERE pattern_id IN ('{ids}')", $arguments, $config['sql_debug']);
					$result = $db->Query("DELETE FROM `acp_payment_patterns_usergroups` WHERE pattern_id IN ('{ids}')", $arguments, $config['sql_debug']);
					$result = $db->Query("DELETE FROM `acp_payment_groups_patterns` WHERE pattern_id IN ('{ids}')", $arguments, $config['sql_debug']);

					$result = $db->Query("DELETE FROM `acp_payment_user` WHERE pattern_id IN ('{ids}')", $arguments, $config['sql_debug']);

					if( $result )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "myltiple delete patterns: ".count($ids));
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.count($ids).'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_pattern_user_failed'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_group_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "16":

			$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_users' OR link = 'p_usershop_admin_patterns'", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach( $result_cats as $obj )
				{
					if( $obj->link == 'p_users' )
					{
						$cats['users'] = $obj->sectionid;
						$cats['user_edit'] = $obj->categoryid;
					}
					else
					{
						$cats['patterns'] = $obj->sectionid;
						$cats['pattern_edit'] = $obj->categoryid;
					}
				}
			}

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$status = $_POST['status'];
			$time = time();

			$sqlconds = " WHERE 1 = 1";
			
			if( $status != 0 )
			{
				$sqlconds .= ($status == 2) ? " AND a.date_end <= ".$time : " AND a.date_end > ".$time;
			}

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT a.id, a.uid, b.name AS pattern_name, a.pattern_id, a.date_start, a.date_end, c.username FROM `acp_payment_user` a 
				LEFT JOIN `acp_payment_patterns` b ON b.id = a.pattern_id 
				LEFT JOIN `acp_users` c ON c.uid = a.uid
				".$sqlconds." LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$obj->time_expired = ($obj->date_end > 0 && $time > $obj->date_end) ? "@@time_expired@@" : ((!$obj->date_end) ? "<span class='infinity'></span>" : compacttime(($obj->date_end - $time), "dddd hhhh"));
					$obj->date_start = ($obj->date_start > 0) ? get_datetime($obj->date_start, $config['date_format']) : "-";
					$obj->username = ( !$obj->username ) ? '<span style="text-decoration:line-through;">@@user@@</span>' : '<a href="'.$config['acpanel'].'.php?cat='.$cats['users'].'&do='.$cats['user_edit'].'&t=0&id='.$obj->uid.'">'.htmlspecialchars($obj->username).'</a>';
					$obj->pattern_name = ( !$obj->pattern_name ) ? '<span style="text-decoration:line-through;">@@payment_pattern_deleted@@</span>' : '<a href="'.$config['acpanel'].'.php?cat='.$cats['patterns'].'&do='.$cats['pattern_edit'].'&t=0&id='.$obj->pattern_id.'">'.htmlspecialchars($obj->pattern_name).'</a>';

					$privs[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');
			if(isset($cats)) $smarty->assign("cats", $cats);

			$smarty->assign("current_time",$time);
			if(isset($privs)) $smarty->assign("privs",$privs);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("tpl", $config['template']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_usershop_admin_patterns_user_list.tpl');

			break;

		case "17":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$error = $array_keys = $array_usergroups = $array_servers = array();
				unset($_POST['go']);
	
				if( is_array($_POST) )
				{
					date_default_timezone_set('UTC');

					foreach( $_POST as $var => $value )
					{
						switch($var) 
						{
							case "name":
							case "description":
	
								$value = trim($value);
								if( $config['charset'] != 'utf-8' )
								{
									$value = iconv('utf-8', $config['charset'], $value);
								}

								if( $var == "name" && strlen($value) == 0 )
									$error[] = $translate['priv_not_name'];
								else
									$array_keys[$var] = $value;

								break;

							case "group":

								if( $value ) $priv_group = $value;
								break;

							case "price_mm":
							case "price_points":
							case "item_duration":
							case "max_sale_items":
							case "max_sale_for_user":
							case "add_points":

								$value = trim($value);
								if( is_numeric($value) ) $array_keys[$var] = $value;
								else
								{
									if( $value ) $error[] = $translate['priv_field_incorrect'].": ".$var;
								}
								break;

							case "item_duration_date":

								$value = trim($value);
								if( strlen($value) > 0 )
								{
									$value = strtotime($value);
									$array_keys['item_duration'] = get_datetime($value, false, true);
								}

								break;

							case "duration_type":
							case "max_sale_items_duration":
							case "max_sale_for_user_duration":
							case "enable_server_select":
							case "new_usergroup_id":
							case "item_duration_select":
							case "active":

								$array_keys[$var] = $value;
								break;

							case "add_flags":

								$value = trim($value);
								if( $value = strtolower($value) )
								{
									if( preg_match("/^[abcdefghijklmnoprqstuyz]+$/i", $value) ) $array_keys[$var] = $value;
									else $error[] = $translate['priv_add_flags_incorrect'];
								}
								break;

							case "usergroups_access":

								if( !empty($value) ) $priv_usergroups = $value;
								break;

							case "servers_access":

								if( !empty($value) ) $priv_servers = $value;
								break;								
						}
					}

					if( (!isset($array_keys['price_mm']) && !isset($array_keys['price_points'])) || ($array_keys['price_mm'] == 0 && $array_keys['price_points'] == 0) )
						$error[] = $translate['priv_no_price'];

					if( !empty($error) )
					{
						if( count($error) > 1 )
						{
							$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
						}
						else
						{
							$error = $error[0];
						}
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].':&nbsp;'.$error.'</span>';
					}
					else
					{
						$result = $db->Query("INSERT INTO `acp_payment_patterns` (".implode(',',array_keys($array_keys)).") VALUES ('".implode('\',\'',array_map('mysql_real_escape_string', $array_keys))."')", array(), $config['sql_debug']);
	
						if( !$result )
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
						}
						else
						{
							$err = array();
							$priv_id = $db->LastInsertID();

							if( isset($priv_group) && $priv_group > 0 )
							{
								$result_group = $db->Query("INSERT INTO `acp_payment_groups_patterns` (gid, pattern_id) VALUES ('".$priv_group."', '".$priv_id."')", array(), $config['sql_debug']);
							}

							if( isset($priv_usergroups) && !empty($priv_usergroups) )
							{
								$newerr = 0;
								foreach( $priv_usergroups AS $k => $v )
								{		
									$result_usergroups = $db->Query("INSERT INTO `acp_payment_patterns_usergroups` (pattern_id, usergroup_id) VALUES ('".$priv_id."', '".$v."')", array(), $config['sql_debug']);
		
									if( !$result_usergroups )
									{
										$newerr++;
									}
								}

								if( $newerr ) $err[] = $translate['priv_usergroups_add_error'];
							}

							if( isset($priv_servers) && !empty($priv_servers) )
							{
								$newerr = 0;
								foreach( $priv_servers AS $k => $v )
								{		
									$result_srv = $db->Query("INSERT INTO `acp_payment_patterns_server` (pattern_id, server_id) VALUES ('".$priv_id."', '".$v."')", array(), $config['sql_debug']);
		
									if( !$result_srv )
									{
										$newerr++;
									}
								}

								if( $newerr ) $err[] = $translate['priv_servers_add_error'];
							}

							if( empty($err) )
							{
								if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "add pattern privilege: ".$priv_id);
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_payment_pattern_success'].'</span>';
							}
							else
							{
								if( count($err) > 1 )
								{
									$err = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $err);
								}
								else
								{
									$err = $err[0];
								}
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].':&nbsp;'.$err.'</span>';
							}
						}
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['empty_array'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "18":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
				$error = $array_usergroups = $array_servers = array();
				unset($_POST['go'], $_POST['id']);
				$query_string = "";
	
				if( is_array($_POST) )
				{
					date_default_timezone_set('UTC');

					$result_pattern = $db->Query("SELECT a.id, a.name, a.description, a.price_mm, a.price_points, a.duration_type, a.active, a.item_duration, a.item_duration_select, 
						a.max_sale_items, a.max_sale_items_duration, a.max_sale_for_user, a.max_sale_for_user_duration, a.new_usergroup_id, a.enable_server_select, 
						a.add_flags, a.add_points, a.do_php_exec, b.gid AS mygroup, GROUP_CONCAT(c.server_id SEPARATOR ',') AS servers_access, GROUP_CONCAT(d.usergroup_id SEPARATOR ',') AS usergroups_access 
						FROM `acp_payment_patterns` a
						LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
						LEFT JOIN `acp_payment_patterns_server` c ON c.pattern_id = a.id
						LEFT JOIN `acp_payment_patterns_usergroups` d ON d.pattern_id = a.id
						WHERE a.id = ".$id." GROUP BY a.id", array(), $config['sql_debug']);

					if( is_array($result_pattern) )
					{
						foreach( $result_pattern as $pat )
						{
							if( is_null($pat->mygroup) ) $pat->mygroup = 0;
							if( is_null($pat->servers_access) ) $pat->servers_access = array();
							else $pat->servers_access = explode(",", $pat->servers_access);
							if( is_null($pat->usergroups_access) ) $pat->usergroups_access = array();
							else $pat->usergroups_access = explode(",", $pat->usergroups_access);
				
							$pattern = (array)$pat;
						}

						foreach( $_POST as $var => $value )
						{
							switch($var) 
							{
								case "name":
								case "description":
		
									$value = trim($value);
									if( $config['charset'] != 'utf-8' )
									{
										$value = iconv('utf-8', $config['charset'], $value);
									}
	
									if( $var == "name" && strlen($value) == 0 )
										$error[] = $translate['priv_not_name'];
									elseif( $pattern[$var] != $value )
										$query_string .= $var." = '".mysql_real_escape_string($value)."',";
	
									break;
	
								case "group":
	
									if( $pattern['mygroup'] != $value ) $priv_group = $value;
									break;
	
								case "price_mm":
								case "price_points":
								case "item_duration":
								case "max_sale_items":
								case "max_sale_for_user":
								case "add_points":
	
									$value = trim($value);
									if( is_numeric($value) )
									{
										if( $pattern[$var] != $value ) $query_string .= $var." = '".$value."',";
									}
									else
									{
										if( $value ) $error[] = $translate['priv_field_incorrect'].": ".$var;
										else $query_string .= $var." = 0,";
									}
									break;
	
								case "item_duration_date":
	
									$value = trim($value);
									if( strlen($value) > 0 )
									{
										$value = strtotime($value);
										if( $pattern['item_duration'] != get_datetime($value, false, true) ) $query_string .= "item_duration = '".get_datetime($value, false, true)."',";
									}
	
									break;
	
								case "duration_type":
								case "max_sale_items_duration":
								case "max_sale_for_user_duration":
								case "enable_server_select":
								case "new_usergroup_id":
								case "item_duration_select":
								case "active":
	
									if( $pattern[$var] != $value ) $query_string .= $var." = '".$value."',";
									break;
	
								case "add_flags":
	
									$value = trim($value);
									if( $value = strtolower($value) )
									{
										if( preg_match("/^[abcdefghijklmnoprqstuyz]+$/i", $value) )
										{
											if( $pattern[$var] != $value ) $query_string .= $var." = '".$value."',";
										}
										else $error[] = $translate['priv_add_flags_incorrect'];
									}
									break;
	
								case "usergroups_access":
	
									if( $pattern['usergroups_access'] != $value ) $priv_usergroups = $value;
									break;
	
								case "servers_access":
	
									if( $pattern['servers_access'] != $value ) $priv_servers = $value;
									break;								
							}
						}
	
						if( (!isset($_POST['price_mm']) && !isset($_POST['price_points'])) || ($_POST['price_mm'] == 0 && $_POST['price_points'] == 0) )
							$error[] = $translate['priv_no_price'];
	
						if( !empty($error) )
						{
							if( count($error) > 1 )
							{
								$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
							}
							else
							{
								$error = $error[0];
							}
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].':&nbsp;'.$error.'</span>';
						}
						else
						{
							if( !$query_string && !isset($priv_group) && !isset($priv_usergroups) && !isset($priv_servers) )
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['nothing_edit'].'</span>';
							}
							else
							{
								if( !$query_string )
									$result = true;
								else
								{
									$query_string = substr($query_string, 0, strlen($query_string)-1);
									$arguments = array('id'=>$id);
									$result = $db->Query("UPDATE `acp_payment_patterns` SET ".$query_string." WHERE id = '{id}'", $arguments, $config['sql_debug']);
								}
			
								if( !$result )
								{
									print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_failed'].'</span>';
								}
								else
								{
									$err = array();
		
									if( isset($priv_group) )
									{
										if( $pattern['mygroup'] == 0 )
											$result_group = $db->Query("INSERT INTO `acp_payment_groups_patterns` (gid, pattern_id) VALUES ('".$priv_group."', '".$id."')", array(), $config['sql_debug']);
										else
											$result_group = $db->Query("UPDATE `acp_payment_groups_patterns` SET gid = ".$priv_group." WHERE pattern_id = ".$id, array(), $config['sql_debug']);
									}
		
									if( isset($priv_usergroups) )
									{
										$newerr = 0;

										$arr_add = array_diff($priv_usergroups, $pattern['usergroups_access']);
										$arr_del = array_diff($pattern['usergroups_access'], $priv_usergroups);
			
										if( !empty($arr_add) )
										{
											foreach( $arr_add as $v )
											{
												$result = $db->Query("INSERT INTO `acp_payment_patterns_usergroups` (pattern_id, usergroup_id) VALUES ('".$id."', '".$v."')", array(), $config['sql_debug']);
				
												if( !$result )
												{
													$newerr++; 
												}
											}
										}
				
										if( !empty($arr_del) )
										{
											foreach( $arr_del as $v )
											{
												$result = $db->Query("DELETE FROM `acp_payment_patterns_usergroups` WHERE usergroup_id = ".$v." AND pattern_id = ".$id, array(), $config['sql_debug']);
				
												if( !$result )
												{
													$newerr++; 
												}
											}
										}
		
										if( $newerr ) $err[] = $translate['priv_usergroups_add_error'];
									}

									if( isset($priv_servers) )
									{
										$newerr = 0;

										$arr_add = array_diff($priv_servers, $pattern['servers_access']);
										$arr_del = array_diff($pattern['servers_access'], $priv_servers);
			
										if( !empty($arr_add) )
										{
											foreach( $arr_add as $v )
											{
												$result = $db->Query("INSERT INTO `acp_payment_patterns_server` (pattern_id, server_id) VALUES ('".$id."', '".$v."')", array(), $config['sql_debug']);
				
												if( !$result )
												{
													$newerr++; 
												}
											}
										}
				
										if( !empty($arr_del) )
										{
											foreach( $arr_del as $v )
											{
												$result = $db->Query("DELETE FROM `acp_payment_patterns_server` WHERE server_id = ".$v." AND pattern_id = ".$id, array(), $config['sql_debug']);
				
												if( !$result )
												{
													$newerr++; 
												}
											}
										}
		
										if( $newerr ) $err[] = $translate['priv_servers_add_error'];
									}
		
									if( empty($err) )
									{
										if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "update pattern privilege: ".$id);
										print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_payment_pattern_success'].'</span>';
									}
									else
									{
										if( count($err) > 1 )
										{
											$err = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $err);
										}
										else
										{
											$err = $err[0];
										}
										print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_list'].':&nbsp;'.$err.'</span>';
									}
								}
							}
						}
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['payment_patternid_incorrect'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['empty_array'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "22":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['read'] || $userinfo['admin_access'] == 'yes' ) 
			{
				if( isset($_POST['id']) && is_numeric($_POST['id']) )
				{
					if( !$userinfo['uid'] )
					{
						$error = $translate['payment_only_registered']." <a href='".$config['acpanel'].".php?do=login' rel='nofollow'>".$translate['user_login']."</a> | <a href='".$config['acpanel'].".php?do=register' rel='nofollow'>".$translate['user_register']."</a>";
					}
					else
					{
						$id = $_POST['id'];
						$product_GA = getProduct("gameAccounts");
				
						$result_pattern = $db->Query("SELECT a.id, a.name, a.description, a.price_mm, a.price_points, a.duration_type, a.active, a.item_duration, a.item_duration_select, 
							a.max_sale_items, a.max_sale_items_duration, a.max_sale_for_user, a.max_sale_for_user_duration, a.new_usergroup_id, a.enable_server_select, a.purchased, 
							a.add_flags, a.add_points, a.do_php_exec, b.gid AS mygroup, GROUP_CONCAT(c.server_id SEPARATOR ',') AS servers_access, GROUP_CONCAT(d.usergroup_id SEPARATOR ',') AS usergroups_access 
							FROM `acp_payment_patterns` a
							LEFT JOIN `acp_payment_groups_patterns` b ON b.pattern_id = a.id
							LEFT JOIN `acp_payment_patterns_server` c ON c.pattern_id = a.id
							LEFT JOIN `acp_payment_patterns_usergroups` d ON d.pattern_id = a.id
							WHERE a.id = ".$id." AND a.active = 1 AND (a.duration_type != 'date' OR a.item_duration > UNIX_TIMESTAMP()) AND (a.max_sale_items_duration != 'total' OR a.max_sale_items = 0 OR (a.max_sale_items_duration = 'total' AND a.max_sale_items > a.purchased)) GROUP BY a.id", array(), $config['sql_debug']);
					
						if( is_array($result_pattern) )
						{
							$user = array('uid' => $userinfo['uid'], 'money' => $userinfo['money'], 'points' => 0);
							date_default_timezone_set('UTC');
							include_once(INCLUDE_PATH . 'functions.payment.php');
					
							foreach( $result_pattern as $pat )
							{
								// check usergroup access
								if( is_null($pat->usergroups_access) ) $pat->usergroups_access = array();
								else $pat->usergroups_access = explode(",", $pat->usergroups_access);
								if( !in_array($userinfo['usergroupid'], $pat->usergroups_access) )
								{
									$error = $translate['payment_usergroup_not_access'];
									break;
								}
								
								// check server select list
								if( is_null($pat->servers_access) ) $pat->servers_access = array();
								else
								{
									$result_servers = $db->Query("SELECT id, hostname, address FROM `acp_servers` WHERE id IN(".$pat->servers_access.")", array(), $config['sql_debug']);
									$pat->servers_access = array();
									
									if( is_array($result_servers) )
									{
										foreach( $result_servers as $obj )
										{
											$pat->servers_access[$obj->id] = array('hostname' => $obj->hostname, 'address' => $obj->address);
										}
									}
								}
								if( $pat->enable_server_select )
								{
									if( empty($pat->servers_access) )
									{
										$error = $translate['payment_servers_list_empty'];
										break;
									}

									if( !isset($_POST['servers_select']) )
									{
										$error = $translate['payment_servers_select_empty'];
										break;
									}
								}
				
								// check price
								$srv = $srok = 1;
								if( isset($_POST['duration_select']) )
								{
									$srok = $_POST['duration_select']/$pat->item_duration;
									$srok = intval($srok);
								}
								if( isset($_POST['servers_select']) )
								{
									$srv = count($_POST['servers_select']);
								}
								$total_price_money = $srok * $srv * $pat->price_mm;
								$total_price_points = $srok * $srv * $pat->price_points;
								if( $total_price_money > $userinfo['money'] )
								{
									$error = $translate['payment_price_not'];
									break;
								}
								if( $total_price_points > 0 )
								{
									if( !empty($product_GA) )
									{
										$result_player = $db->Query("SELECT userid, points FROM `acp_players` WHERE userid = ".$userinfo['uid'], array(), $config['sql_debug']);
					
										if( is_array($result_player) )
										{
											foreach( $result_player as $obj )
											{
												$user['points'] = $obj->points;
											}
				
											if( $total_price_points > $user['points'] )
											{
												$error = $translate['payment_price_not'];
												break;
											}
										}
										else
										{
											$error = $translate['not_game_account'];
											break;
										}
									}
									else
									{
										$error = $translate['plugin_ga_not_active'];
										break;
									}
								}
				
								$time = time();
				
								// check limit time max items
								if( $pat->max_sale_items > 0 && $pat->max_sale_items_duration != "total" && $pat->purchased > 0 )
								{
									switch($pat->max_sale_items_duration)
									{
										case "month":
											$purchased_time = $pat->max_sale_items*30*24*3600;
											break;
				
										case "week":
											$purchased_time = $pat->max_sale_items*7*24*3600;
											break;
				
										case "day":
											$purchased_time = $pat->max_sale_items*24*3600;
											break;
									}
									$purchased_time = $time - $purchased_time - 1;
									$result_purchased = $db->Query("SELECT MIN(date_start) AS mintime, count(*) AS cnt FROM `acp_payment_user` WHERE date_start > ".$purchased_time." AND pattern_id = ".$id, array(), $config['sql_debug']);
				
									if( is_array($result_purchased) )
									{
										foreach( $result_purchased as $obj )
										{
											$count_purchased = $obj->cnt;
											$mintime = (is_null($obj->mintime)) ? 0 : ($purchased_time - $obj->mintime);
										}
				
										if( $count_purchased >= $pat->max_sale_items )
										{
											$error = $translate['store_item_limit_time_purchased']." ".compacttime($mintime, 'dddd hhhh mmmm ssss');
											break;
										}
									}
								}
				
								// check limit time max items for user
								if( $pat->max_sale_for_user > 0 && $pat->purchased > 0 )
								{
									$sqlconds = "";
									if( $pat->max_sale_for_user_duration != "total" )
									{
										switch($pat->max_sale_for_user_duration)
										{
											case "month":
												$purchased_time = $pat->max_sale_items*30*24*3600;
												break;
					
											case "week":
												$purchased_time = $pat->max_sale_items*7*24*3600;
												break;
					
											case "day":
												$purchased_time = $pat->max_sale_items*24*3600;
												break;
										}
										$purchased_time_user = $time - $purchased_time_user - 1;
										$sqlconds .= " AND date_start > ".$purchased_time_user;
									}
				
									$result_purchased = $db->Query("SELECT MIN(date_start) AS mintime, count(*) AS cnt FROM `acp_payment_user` WHERE uid = ".$userinfo['uid']." AND pattern_id = ".$id.$sqlconds, array(), $config['sql_debug']);
				
									if( is_array($result_purchased) )
									{
										foreach( $result_purchased as $obj )
										{
											$count_purchased = $obj->cnt;
											$mintime_user = (is_null($obj->mintime) || !isset($purchased_time_user)) ? 0 : ($purchased_time_user - $obj->mintime);
										}
				
										if( $count_purchased >= $pat->max_sale_for_user )
										{
											$error = ($mintime_user > 0) ? $translate['store_item_for_user_limit_time_purchased']." ".compacttime($mintime_user, 'dddd hhhh mmmm ssss') : $translate['store_item_for_user_limit_purchased'];
											break;
										}
									}
								}
				
								if( !is_numeric($pat->item_duration) ) $pat->item_duration = 0;
								if( !is_numeric($pat->new_usergroup_id) ) $pat->new_usergroup_id = 0;
								if( !is_numeric($pat->add_points) ) $pat->add_points = 0;
								if( $pat->duration_type == "date" )
								{
									$pat->item_duration_select = 0;
								}
								else
								{
									$array_durations = getArrayDurations($pat->item_duration, $pat->duration_type);
								}
					
								$pattern = (array)$pat;
							}
						}
						else $error = $translate['not_payment_pattern'];
					}
				}
				else $error = $translate['not_payment_pattern'];

				if( isset($error) )
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$error.'</span>';
				else
				{
					// var servers
					if( $pattern['enable_server_select'] )
					{
						$update_info['servers'] = $_POST['servers_select'];
					}
					elseif( !empty($pattern['servers_access']) )
					{
						$update_info['servers'] = array_keys($pattern['servers_access']);
					}
					else $update_info['servers'] = 0;

					// var time_end
					if( $pattern['item_duration_select'] )
					{
						$temp_string = "+".$_POST['duration_select']." ".$pattern['duration_type'];
						$update_info['time_end'] = strtotime($temp_string, $time);
					}
					else
					{
						if( $pattern['item_duration'] )
						{
							if( $pattern['duration_type'] != "date" )
							{
								$temp_string = "+".$pattern['item_duration']." ".$pattern['duration_type'];
								$update_info['time_end'] = strtotime($temp_string, $time);
							}
							else $update_info['time_end'] = $pattern['item_duration'];
						}
						else $update_info['time_end'] = 0;
					}

					// var add_flags and add_points
					$mask_id = 0;
					if( strlen($pattern['add_flags']) > 0 || $pattern['add_points'] )
					{
						if( !empty($product_GA) )
						{
							$gameAccID = $db->Query("SELECT userid FROM `acp_players` WHERE userid = ".$userinfo['uid'], array(), $config['sql_debug']);
		
							if( $gameAccID )
							{
								if( strlen($pattern['add_flags']) > 0 )
								{
									if( $result_create_mask = $db->Query("INSERT INTO `acp_access_mask` SET access_flags = '".$pattern['add_flags']."'", array(), $config['sql_debug']) )
									{
										$mask_id = $db->LastInsertID();
										if( !is_array($update_info['servers']) )
										{
											$result_add_servers = $db->Query("INSERT INTO `acp_access_mask_servers` SET server_id = '".$update_info['servers']."', mask_id = '".$mask_id."'", array(), $config['sql_debug']);
										}
										else
										{
											foreach($update_info['servers'] as $v)
											{
												$result_add_servers = $db->Query("INSERT INTO `acp_access_mask_servers` SET server_id = '".$v."', mask_id = '".$mask_id."'", array(), $config['sql_debug']);
											}
										}
		
										if( $result_add_servers )
										{
											$result_add_mask = $db->Query("INSERT INTO `acp_access_mask_players` SET userid = '".$userinfo['uid']."', mask_id = '".$mask_id."', access_expired = '".$update_info['time_end']."'", array(), $config['sql_debug']);
											if( !$result_add_mask )
											{
												$error = $translate['error_add_flags'];
												$db->Query("DELETE FROM `acp_access_mask_servers` WHERE mask_id = ".$mask_id, array(), $config['sql_debug']);
												$db->Query("DELETE FROM `acp_access_mask` WHERE mask_id = ".$mask_id, array(), $config['sql_debug']);
											}
										}
										else
										{
											$error = $translate['error_add_flags'];
											$db->Query("DELETE FROM `acp_access_mask` WHERE mask_id = ".$mask_id, array(), $config['sql_debug']);
										}
									}
									else $error = $translate['error_add_flags'];
								}
	
								if( !isset($error) && $pattern['add_points'] )
								{
									$db->Query("UPDATE `acp_players` SET points = points + ".$pattern['add_points']." WHERE userid = ".$userinfo['uid'], array(), $config['sql_debug']);
								}
							}
							else $error = $translate['error_not_game_account'];
						}
						else $error = $translate['plugin_ga_not_active'];
					}

					if( isset($error) )
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$error.'</span>';
					else
					{
						$db->Query("UPDATE `acp_payment_patterns` SET purchased = purchased + 1 WHERE id = ".$id, array(), $config['sql_debug']);
						$db->Query("INSERT INTO `acp_payment_user` SET uid = '".$userinfo['uid']."', pattern_id = '".$id."', date_start = '".$time."', date_end = '".$update_info['time_end']."', add_mask_id = '".$mask_id."', new_group = '".$pattern['new_usergroup_id']."'", array(), $config['sql_debug']);

						if( $pattern['new_usergroup_id'] )
						{
							if( $check_usergroup = $db->Query("SELECT usergroupid FROM `acp_usergroups` WHERE usergroupid = ".$pattern['new_usergroup_id'], array(), $config['sql_debug']) )
							{
								if( $check_usergroup != $userinfo['usergroupid'] )
								{
									switch($ext_auth_type)
									{
										case "xf":
											$db->Query("UPDATE `acp_users` SET real_groupid = IF(real_groupid > 0, real_groupid, usergroupid) WHERE uid = ".$userinfo['uid'], array(), $config['sql_debug']);
											$userArray = $xf->setUserData($userinfo['uid'], array('user_group_id' => $pattern['new_usergroup_id']));
											break;
	
										default:
	
											$db->Query("UPDATE `acp_users` SET real_groupid = IF(real_groupid > 0, real_groupid, usergroupid), usergroupid = '".$pattern['new_usergroup_id']."' WHERE uid = ".$userinfo['uid'], array(), $config['sql_debug']);
											break;
									}
								}
							}
						}

						$array_holders = array('privilege_name'=>$pattern['name']);
						function holders_replace($matches)
						{
							global $array_holders;
	
							return $array_holders[$matches[1]];
						}
						$memo = preg_replace_callback('#{([^}]+)}#sUi', 'holders_replace', $translate['buy_privilege_payment']);
						$arguments = array('uid' => $userinfo['uid'], 'points' => $total_price_points, 'time' => $time, 'money' => $total_price_money, 'pattern' => $id, 'memo' => $memo);
	
						if( $total_price_points > 0 )
						{
							if( $query_pay = $db->Query("UPDATE `acp_players` SET points = points - {points} WHERE userid = '{uid}'", $arguments, $config['sql_debug']) )
							{
								$query_pay = $db->Query("INSERT INTO `acp_payment` (uid, amount, created, enrolled, memo, currency, pattern) VALUES ('{uid}', '-{points}', '{time}', '{time}', '{memo}', 'points', '{pattern}')", $arguments, $config['sql_debug']);
							}
						}
	
						if( $total_price_money > 0 )
						{
							if( $query_pay = $db->Query("UPDATE `acp_users` SET money = money - {money} WHERE uid = '{uid}'", $arguments, $config['sql_debug']) )
							{
								$query_pay = $db->Query("INSERT INTO `acp_payment` (uid, amount, created, enrolled, memo, currency, pattern) VALUES ('{uid}', '-{money}', '{time}', '{time}', '{memo}', 'mm', '{pattern}')", $arguments, $config['sql_debug']);
							}
						}

						if( strlen($pattern['do_php_exec']) > 0 ) eval($pattern['do_php_exec']);

						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['privilege_buy_success'].': '.$pattern['name'].'</span>';
						unset($pattern);
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "23":

			$result_cats = $db->Query("SELECT categoryid, sectionid, link FROM `acp_category` WHERE link = 'p_usershop_profile_privilege_detail'", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach( $result_cats as $obj )
				{
					$cats['section_detail'] = $obj->sectionid;
					$cats['category_detail'] = $obj->categoryid;
				}
			}

			$offset = $_POST['offset'] - 1;
			$limit = $_POST['limit'];
			$time = time();

			$sqlconds = " WHERE uid = ".$userinfo['uid'];

			$arguments = array('offset'=>$offset,'limit'=>$limit);
			$result = $db->Query("SELECT a.id, a.uid, b.name AS pattern_name, a.pattern_id, a.date_start, a.date_end FROM `acp_payment_user` a 
				LEFT JOIN `acp_payment_patterns` b ON b.id = a.pattern_id 
				".$sqlconds." LIMIT {offset},{limit}", $arguments, $config['sql_debug']);

			if( is_array($result) )
			{
				foreach( $result as $obj )
				{
					$obj->time_expired = ($obj->date_end > 0 && $time > $obj->date_end) ? "@@time_expired@@" : ((!$obj->date_end) ? "<span class='infinity'></span>" : compacttime(($obj->date_end - $time), "dddd hhhh"));
					$obj->date_start = ($obj->date_start > 0) ? get_datetime($obj->date_start, $config['date_format']) : "-";
					$obj->pattern_name = ( !$obj->pattern_name ) ? '<span style="text-decoration:line-through;">@@payment_pattern_deleted@@</span>' : htmlspecialchars($obj->pattern_name);

					$privs[] = (array)$obj;
				}
			}

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			$smarty->assign("current_time",$time);
			if(isset($cats)) $smarty->assign("cats",$cats);
			if(isset($privs)) $smarty->assign("privs",$privs);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("tpl", $config['template']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('profile_shop_privileges.tpl');

			break;

		case "24":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['read'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$offset = $_POST['offset'] - 1;
				$limit = $_POST['limit'];
				$s = $_POST['status'];
				$t = $_POST['srv'];
	
				$sqlconds = " WHERE 1 = 1";
				$arguments = array('offset'=>$offset,'limit'=>$limit);
	
				if( $s )
				{
					$active = ($s == 1) ? 1 : 0;
					$sqlconds .= " AND a.active = ".$active;
				}
				
				if( $t )
				{
					$sqlconds .= " AND (b.server_id = ".$t." OR b.server_id = 0)";
				}
	
				$arguments = array('offset'=>$offset,'limit'=>$limit);
				$result = $db->Query("SELECT a.id, a.game_descr, a.web_descr, a.cost, a.duration, a.cmd, a.active, a.server_id, count(a.id) AS servers
					FROM 
						(SELECT c.id, c.game_descr, c.web_descr, c.cost, c.duration, c.cmd, c.active, MIN(b.server_id) AS server_id
						FROM `acp_gameshop` c
						LEFT JOIN `acp_gameshop_servers` b ON b.item_id = c.id 
						".$sqlconds." GROUP BY c.id) AS a 
					LEFT JOIN `acp_gameshop_servers` d ON d.item_id = a.id
					GROUP BY a.id ORDER BY a.id DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
	
				if( is_array($result) )
				{
					foreach( $result as $obj )
					{
						$obj->cost_info = $obj->cost."@@points_suffix@@";
						$items[] = (array)$obj;
					}
				}
			}
			else $error = $translate['access_denied'];

			require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

			$smarty = new Smarty();
			$smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
			$smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
			$smarty->config_dir = TEMPLATE_PATH . '_configs/';
			$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

			$smarty->assign("home",$config['acpanel'].'.php');

			if(isset($items)) $smarty->assign("items",$items);
			if(isset($error)) $smarty->assign("iserror",$error);
			$smarty->assign("tpl", $config['template']);

			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_gameshop_items_list.tpl');

			break;

		case "25":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("DELETE FROM `acp_gameshop` WHERE id = '{id}'", $arguments, $config['sql_debug']);
		
				if( $result )
				{	
					$result = $db->Query("DELETE FROM `acp_gameshop_servers` WHERE item_id = '{id}'", $arguments, $config['sql_debug']);
	
					if( $result )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "delete gameshop item id: ".$id);
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_item_success'].'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_item_servers_failed'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_item_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "26":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$ids = $_POST['marked_word'];
	
				$arguments = array('ids'=>$ids);
				$result = $db->Query("DELETE FROM `acp_gameshop` WHERE id IN ('{ids}')", $arguments, $config['sql_debug']);				
	
				if( $result )
				{
					$result = $db->Query("DELETE FROM `acp_gameshop_servers` WHERE item_id IN ('{ids}')", $arguments, $config['sql_debug']);

					if( $result )
					{
						if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "myltiple delete items: ".count($ids));
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.count($ids).'</span>';
					}
					else
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_item_servers_failed'].'</span>';
					}
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_item_failed'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "27":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST["id"];
	
				$arguments = array('id'=>$id);
				$result = $db->Query("UPDATE `acp_gameshop` SET active = IF(active = 0, 1, 0) WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
				if( $result )
				{
					if( in_array("log_edititing", $config['user_action_log']) ) saveLogs("payments", "change status for game item: ".$id);
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['change_status_success'].'</span>';
				}
				else
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['change_status_error'].'</span>';
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "28":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$game_descr = trim($_POST['game_descr']);
				$web_descr = trim($_POST['web_descr']);
	
				if( $_POST['servers_all'] == "yes" )
				{
					$access_servers = 0;
				}
				else
				{
					if( isset($_POST['access_servers']) )
					{
						$access_servers = $_POST['access_servers'];
					}
					else
					{
						$access_servers = "-1";
					}
				}
	
				if( $game_descr == "" )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['game_descr_empty'].'</span>';
				}
				else
				{
					$cost = (is_numeric($_POST['cost'])) ? $_POST['cost'] : 0;
					$duration = (is_numeric($_POST['duration'])) ? $_POST['duration'] : 0;
					$arguments = array('game_descr'=>$game_descr, 'web_descr'=>$web_descr, 'cost'=>$cost, 'duration'=>$duration, 'active'=>$_POST['active'], 'cmd'=>$_POST['cmd']);
	
					$result = $db->Query("INSERT INTO `acp_gameshop` (game_descr, web_descr, cost, duration, cmd, active) VALUES ('{game_descr}', '{web_descr}', '{cost}', '{duration}', '{cmd}', '{active}')", $arguments, $config['sql_debug']);
	
					if( !$result )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
					}
					else
					{
						$item_insert_id = $db->LastInsertID();
						$err = 0;
	
						if( $item_insert_id )
						{
							if( $access_servers === 0 )
							{
								$result = $db->Query("INSERT INTO `acp_gameshop_servers` (item_id, server_id) VALUES ('".$item_insert_id."', '0')", array(), $config['sql_debug']);
	
								if( !$result )
								{
									$err++; 
								}
							}
							else
							{
								if( $access_servers !== "-1" )
								{
									foreach($access_servers as $val)
									{
										$result = $db->Query("INSERT INTO `acp_gameshop_servers` (item_id, server_id) VALUES ('".$item_insert_id."', '".$val."')", array(), $config['sql_debug']);
	
										if( !$result )
										{
											$err++; 
										}
									}
								}
							}
	
							if( !$err )
							{
								if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "add item #".$item_insert_id);
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_item_success'].'</span>';
							}
							else
							{
								print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_srvsync_failed'].'</span>';
							}
						}
						else
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_lastid_failed'].'</span>';
						}
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		case "29":

			require_once(INCLUDE_PATH . 'class.Permissions.php');
			$permClass = new Permissions($db);
			$userPerm = $permClass->getPermissions('ub_perm_payment', $userinfo['usergroupid']);

			if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' ) 
			{
				$id = $_POST['id'];
				if( !is_numeric($id) ) die("Hacking Attempt");

				$game_descr = trim($_POST['game_descr']);
				$web_descr = trim($_POST['web_descr']);
	
				if( $_POST['servers_all'] == "yes" )
				{
					$access_servers[] = 0;
				}
				else
				{
					if( isset($_POST['access_servers']) )
					{
						$access_servers = $_POST['access_servers'];
					}
					else
					{
						$access_servers = array();
					}
				}
	
				if( $game_descr == "" )
				{
					print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['game_descr_empty'].'</span>';
				}
				else
				{
					$cost = (is_numeric($_POST['cost'])) ? $_POST['cost'] : 0;
					$duration = (is_numeric($_POST['duration'])) ? $_POST['duration'] : 0;
					$arguments = array('id'=>$id, 'game_descr'=>$game_descr, 'web_descr'=>$web_descr, 'cost'=>$cost, 'duration'=>$duration, 'active'=>$_POST['active'], 'cmd'=>$_POST['cmd']);
	
					$result = $db->Query("UPDATE `acp_gameshop` SET game_descr = '{game_descr}', web_descr = '{web_descr}', cost = '{cost}', duration = '{duration}', cmd = '{cmd}', active = '{active}' WHERE id = '{id}'", $arguments, $config['sql_debug']);
	
					if( !$result )
					{
						print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_failed'].'</span>';
					}
					else
					{
						$curent_servers = array();
						$err = 0;
						$result_sync = $db->Query("SELECT item_id, server_id FROM `acp_gameshop_servers` WHERE item_id = '{id}'", $arguments, $config['sql_debug']);
	
						if( is_array($result_sync) )
						{
							foreach ($result_sync as $obj_sync)
							{
								$curent_servers[] = $obj_sync->server_id;
							}
						}
	
						if( $access_servers != $curent_servers )
						{
							$arr_add = array_diff($access_servers, $curent_servers);
							$arr_del = array_diff($curent_servers, $access_servers);
	
							if( !empty($arr_add) )
							{
								foreach($arr_add as $val)
								{
									$result = $db->Query("INSERT INTO `acp_gameshop_servers` (item_id, server_id) VALUES ('".$id."', '".$val."')", array(), $config['sql_debug']);
	
									if( !$result )
									{
										$err++; 
									}
								}
							}
	
							if( !empty($arr_del) )
							{
								foreach($arr_del as $val)
								{
									$result = $db->Query("DELETE FROM `acp_gameshop_servers` WHERE item_id = ".$id." AND server_id = ".$val."", array(), $config['sql_debug']);
	
									if( !$result )
									{
										$err++; 
									}
								}
							}
						}

						if( !$err )
						{
							if (in_array("log_edititing", $config['user_action_log'])) saveLogs("payments", "edit item #".$id);
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_item_success'].'</span>';
						}
						else
						{
							print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_srvsync_failed'].'</span>';
						}
					}
				}
			}
			else
				print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';

			break;

		default:

			die("Hacking Attempt");
	}
}

?>
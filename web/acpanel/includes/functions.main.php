<?php

if (!defined('IN_ACP')) die("Hacking attempt!");
define("TIMENOW", time());

function clearCacheFolder($folder)
{
	if( is_dir($folder) )
	{
		$handle = opendir($folder);
		while( $subfile = readdir($handle) )
		{
			if( $subfile == '.' OR $subfile == '..' ) continue;
			if( is_file($subfile) )
			{
				if( basename($subfile) != '.htaccess' )
					unlink("{$folder}/{$subfile}");
			}
			else
				clearCacheFolder("{$folder}/{$subfile}");
		}
		closedir($handle);
	}
	else
	{
		if( basename($folder) != '.htaccess' )
			unlink($folder);
	}
}

function get_correct_str($num, $str1, $str2, $str3)
{
	$val = $num % 100;
	
	if( $val > 10 && $val < 20 )
		return $num .' '. $str3;
	else
	{
		$val = $num % 10;
		if( $val == 1 )
			return $num .' '. $str1;
		elseif( $val > 1 && $val < 5 )
			return $num .' '. $str2;
		else
			return $num .' '. $str3;
	}
}

function compacttime($seconds, $format="hh:mm:ss")
{
	$d = $h = $m = $s = "00";
	if( !preg_match_all('#[^dhms]#', $format, $seps) )
		$seps = array();

	if(!$format) return get_correct_str($seconds, '@@time_ss_one@@', '@@time_ss_several@@', '@@time_ss_many@@');
	if(!isset($seconds)) $seconds = 0;
	if( (strpos($format, 'dd') !== FALSE) && ($seconds / (60*60*24)) >= 1) { $d = sprintf("%d", $seconds / (60*60*24)); $seconds -= $d * (60*60*24); }
	if( (strpos($format, 'hh') !== FALSE) && ($seconds / (60*60)) >= 1) { $h = sprintf("%d", $seconds / (60*60)); $seconds -= $h * (60*60); }
	if( (strpos($format, 'mm') !== FALSE) && ($seconds / 60) >= 1) { $m = sprintf("%d", $seconds / 60); $seconds -= $m * (60); }
	if( (strpos($format, 'ss') !== FALSE) && ($seconds % 60) >= 1) { $s = sprintf("%d", $seconds % 60); }
	if( preg_match('#(d{2,4})#', $format, $format_mask) ) { $format_mask = $format_mask[1]; } else { $format_mask = ''; }
	switch($format_mask)
	{
		case "dddd":
		case "ddd":

			if( $d === '00' )
			{
				$format = trim(str_replace($format_mask, '', $format));
			}
			else
			{
				$repl = get_correct_str($d, ($format_mask == 'dddd') ? '@@time_dd_one@@' : '@@time_dd_compact@@', ($format_mask == 'dddd') ? '@@time_dd_several@@' : '@@time_dd_compact@@', ($format_mask == 'dddd') ? '@@time_dd_many@@' : '@@time_dd_compact@@');
				if( $format_mask == 'ddd' ) while( strpos($repl,' ') !== false ) $repl = str_replace(' ', '', $repl);
				$format = str_replace($format_mask, $repl, $format);
			}
			break;

		case "dd":

			$format = str_replace($format_mask, sprintf('%02d',$d), $format);
			break;
	}
	if( preg_match('#(h{2,4})#', $format, $format_mask) ) { $format_mask = $format_mask[1]; } else { $format_mask = ''; }
	switch($format_mask)
	{
		case "hhhh":
		case "hhh":

			if( $h === '00' )
			{
				$format = trim(str_replace($format_mask, '', $format));
			}
			else
			{
				$repl = get_correct_str($h, ($format_mask == 'hhhh') ? '@@time_hh_one@@' : '@@time_hh_compact@@', ($format_mask == 'hhhh') ? '@@time_hh_several@@' : '@@time_hh_compact@@', ($format_mask == 'hhhh') ? '@@time_hh_many@@' : '@@time_hh_compact@@');
				if( $format_mask == 'hhh' ) while( strpos($repl,' ') !== false ) $repl = str_replace(' ', '', $repl);
				$format = str_replace($format_mask, $repl, $format);
			}
			break;

		case "hh":

			$format = str_replace($format_mask, sprintf('%02d',$h), $format);
			break;
	}
	if( preg_match('#(m{2,4})#', $format, $format_mask) ) { $format_mask = $format_mask[1]; } else { $format_mask = ''; }
	switch($format_mask)
	{
		case "mmmm":
		case "mmm":

			if( $m === '00' )
			{
				$format = trim(str_replace($format_mask, '', $format));
			}
			else
			{
				$repl = get_correct_str($m, ($format_mask == 'mmmm') ? '@@time_mm_one@@' : '@@time_mm_compact@@', ($format_mask == 'mmmm') ? '@@time_mm_several@@' : '@@time_mm_compact@@', ($format_mask == 'mmmm') ? '@@time_mm_many@@' : '@@time_mm_compact@@');
				if( $format_mask == 'mmm' ) while( strpos($repl,' ') !== false ) $repl = str_replace(' ', '', $repl);
				$format = str_replace($format_mask, $repl, $format);
			}
			break;

		case "mm":

			$format = str_replace($format_mask, sprintf('%02d',$m), $format);
			break;
	}
	if( preg_match('#(s{2,4})#', $format, $format_mask) ) { $format_mask = $format_mask[1]; } else { $format_mask = ''; }
	switch($format_mask)
	{
		case "ssss":
		case "sss":

			if( $s === '00' && ($m !== '00' || $h !== '00' || $d !== '00') )
			{
				$format = trim(str_replace($format_mask, '', $format));
			}
			else
			{
				if( $s === '00' ) { $s = 0; }
				$repl = get_correct_str($s, ($format_mask == 'ssss') ? '@@time_ss_one@@' : '@@time_ss_compact@@', ($format_mask == 'ssss') ? '@@time_ss_several@@' : '@@time_ss_compact@@', ($format_mask == 'ssss') ? '@@time_ss_many@@' : '@@time_ss_compact@@');
				if( $format_mask == 'sss' ) while( strpos($repl,' ') !== false ) $repl = str_replace(' ', '', $repl);
				$format = str_replace($format_mask, $repl, $format);
			}
			break;

		case "ss":

			$format = str_replace($format_mask, sprintf('%02d',$s), $format);
			break;
	}

	if( !empty($seps) )
	{
		foreach($seps[0] as $k => $v)
		{
			$format = ltrim($format, $v);
			$format = rtrim($format, $v);
		}
	}
	return $format;
}

function RandomCode($length)
{
	$genstring="abcdefghijkmnopqrstuvwxyz123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
  	srand((double) microtime()*1000000);
  	$r="";
  	$mx=strlen($genstring)-1;

  	for($i=0; $i<$length; $i++) {
    		$rpos = rand(0,$mx);
    		$c = substr($genstring,$rpos,1);
    		$r .= $c;
  	}
  	return $r;
}

function getRealIpAddr()
{
	if (!empty($_SERVER['HTTP_CLIENT_IP']))
	{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	{
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	return $ip;
}

function get_datetime($timestamp = TIMENOW, $format = false, $reverse = false)
{
	global $config, $userinfo;

	if( isset($userinfo['timezone']) )
	{
		$hourdiff = $userinfo['timezone'] * 3600;
	}
	else
	{
		$hourdiff = $config['timezone'] * 3600;
	}

	$timestamp_adjusted = (!$reverse) ? ($timestamp + $hourdiff) : ($timestamp - $hourdiff);

	return (!$format) ? $timestamp_adjusted : gmdate($format, $timestamp_adjusted);
}

function saveLogs($action, $remarks, $username = false)
{
	global $db, $config, $userinfo, $_COOKIE;

	$timestamp = time();
	if( !$username )
	{
		if( !$userinfo['username'] )
		{
			if(isset($_COOKIE['acp_user'])) $cookie_vars = explode(":", $_COOKIE['acp_user']);
			$find_username = (isset($cookie_vars)) ? $cookie_vars[0] : '';
		}
		else
		{
			$find_username = $userinfo['username'];
		}

		$username = (!$find_username) ? "unknown" : $find_username;
	}
	$arguments = array('timestamp'=>$timestamp,'uip'=>getRealIpAddr(),'username'=>$username,'action'=>$action,'remarks'=>$remarks);
	$db->Query("INSERT INTO `acp_logs` (timestamp, ip, username, action, remarks) VALUES ('{timestamp}', '{uip}', '{username}', '{action}', '{remarks}')", $arguments, $config['sql_debug']);
}

function acp_logout()
{
	global $config;

	if (in_array("log_login", $config['user_action_log'])) saveLogs("user_auth", "user logout");
	setcookie("acp_user", "", time()-60*60*24*$config['cookie_time'], "/");
	die(header('Location: '.$config['acpanel'].'.php'));
}

function acp_login($username, $password)
{
	global $db, $config;

	$hash = md5($password);

	$arguments = array('username'=>$username,'password'=>$hash);
	$query = $db->Query("SELECT uid, username FROM `acp_users` WHERE username = '{username}' AND password = '{password}' LIMIT 1", $arguments, $config['sql_debug']);
	if(is_array($query))
	{
		$result = (array)$query[0];

		$skey = md5($result['username'].$config['secretkey']);
		$cookiestring = $result['username'].":".$skey.":".$result['uid'];
		$expires = (86400 * $config['cookie_time']) + time();
		$savecookie = setcookie("acp_user", $cookiestring, $expires, "/");

		$arguments = array('uid'=>$result['uid'],'key'=>$skey);
		$db->Query("UPDATE `acp_users` SET `secretkey` = '{key}' WHERE `uid` = '{uid}'", $arguments, $config['sql_debug']);
		if (in_array("log_login", $config['user_action_log'])) saveLogs("user_auth", $username." login", $username);

		return true;
	}
	else
	{
		if (in_array("log_login_error", $config['user_action_log'])) saveLogs("user_auth_error", $username." failed to login");

		return false;
	}
}

function get_user_info($userID = 0)
{
	global $db, $config, $_COOKIE;

	if( $userID > 0 )
	{
		if( $config['ext_auth_type'] == "xf" )
		{
			$arguments = array('id'=>$userID);
			$query = $db->Query("SELECT * FROM `acp_users` AS usr LEFT JOIN `acp_usergroups` AS grp ON usr.usergroupid = grp.usergroupid WHERE usr.uid = '{id}' LIMIT 1", $arguments, $config['sql_debug']);
			if( is_array($query) )
			{
				$userinfo = (array)$query[0];

				$userinfo['read_category'] = strlen($userinfo['read_category']) ? explode(',', $userinfo['read_category']) : array();
				unset($userinfo['password']);

				return $userinfo;
			}
		}
	}

	if( isset($_COOKIE['acp_user']) )
	{
		$cookie_vars = explode(":", $_COOKIE['acp_user']);
		$key = md5($cookie_vars[0].$config['secretkey']);

		if( $key == $cookie_vars[1] )
		{
			$arguments = array('secret'=>$key);
			$query = $db->Query("SELECT * FROM `acp_users` AS usr LEFT JOIN `acp_usergroups` AS grp ON usr.usergroupid = grp.usergroupid WHERE secretkey = '{secret}' LIMIT 1", $arguments, $config['sql_debug']);
			if( is_array($query) )
			{
				$userinfo = (array)$query[0];

				$userinfo['read_category'] = strlen($userinfo['read_category']) ? explode(',', $userinfo['read_category']) : array();
				unset($userinfo['password']);

				$time = time();

				$arguments = array('time'=>$time,'uid'=>$userinfo['uid']);
				$db->Query("UPDATE `acp_users` SET last_visit = '{time}' WHERE uid = '{uid}'", $arguments, $config['sql_debug']);

				return $userinfo;
			}
		}
	}

	$query = $db->Query("SELECT * FROM `acp_usergroups` WHERE usergroupid = '3' LIMIT 1", array(), $config['sql_debug']);

	if( is_array($query) )
	{
		$userinfo = (array)$query[0];
		$userinfo['read_category'] = strlen($userinfo['read_category']) ? explode(',', $userinfo['read_category']) : array();
	}
	else
	{
		$userinfo['read_category'] = array();
		$userinfo['edit_pages'] = 'no';
		$userinfo['admin_access'] = 'no';
		$userinfo['usergroupid'] = 3;
	}

	$userinfo['uid'] = 0;
	$userinfo['username'] = NULL;
	$userinfo['timezone'] = $config['timezone'];

	return $userinfo;
}

function listing($url, $mode)
{
	if (is_dir($url))
	{
		if ($dir = opendir($url))
		{
			while (false !== ($file = readdir($dir)))
			{
				if ($file != "." && $file != "..")
				{
					if(is_dir($url."/".$file))
					{
						$folders[] = $file;
					}
					else
					{
						$files[] = $file;
					}
				}
			}
		}

		closedir($dir);
	}

	if($mode == 1) { return $folders; }
	if($mode == 0) { return $files; }
}

function translate_template($tpl_output, &$smarty)
{
	global $config, $db;

	preg_match_all('/@@(.+?)@@/', $tpl_output, $lng_word);
	while( list($key, $value) = each($lng_word[1]) )
	{
		$lng_words[] = $value;
	}

	if( empty($lng_words) ) { return $tpl_output; }

	$current_tpl = $smarty->template_resource;

	$arguments = array('lp_name'=>$current_tpl,'lw_words'=>$lng_words,'lang'=>get_language(1));
	$result = $db->Query("
		SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`
		LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_name='{lp_name}'
		WHERE ((acp_lang_pages.lp_id = acp_lang_words.lw_page) OR (acp_lang_words.lw_page = '0')) AND acp_lang_words.lw_word IN('{lw_words}')
	", $arguments, $config['sql_debug']);
	if(is_array($result))
	{
		foreach ($result as $obj)
		{
			$tpl_output = preg_replace("/@@".$obj->lw_word."@@/", $obj->lw_translate, $tpl_output);
		}
	}

	return $tpl_output;
}

function get_language($action)
{
	global $config, $langs, $_COOKIE, $xf;

	if( is_object($xf) )
	{
		$lang_variable = ($lang_temp = $xf->get('language_id')) ? $lang_temp : XenForo_Application::get('options')->defaultLanguageId;
	}
	else
	{
		$lang_variable = ( isset($_COOKIE['acp_language']) ) ? $_COOKIE['acp_language'] : $config['language'];
	}

	if($action)
	{
		return (array_key_exists($lang_variable, $langs)) ? $langs[$lang_variable][1] : "lw_en";
	}
	else
	{
		return (array_key_exists($lang_variable, $langs)) ? $lang_variable : 1;
	}
}

function create_lang_list()
{
	global $db, $config;

	$result = $db->Query("SELECT * FROM `acp_lang` WHERE lang_active != 'no'", array(), $config['sql_debug']);
	if(is_array($result))
	{
		foreach ($result as $obj)
		{
			$output[$obj->lang_id] = array($obj->lang_title, $obj->lang_code);
		}
	}

	return (isset($output)) ? $output : array();
}

function cmp($a, $b)
{
	if ($a["options"] == $b["options"]) return 0;
	return ($a["options"] < $b["options"]) ? -1 : 1;
}

function in_multi_array($needle, $haystack)
{
	$in_multi_array = false;
	if(in_array($needle, $haystack))
	{
		$in_multi_array = true;
	}
	else
	{
		for($i = 0; $i < sizeof($haystack); $i++)
		{
			if(isset($haystack[$i]))
			{
				if(is_array($haystack[$i]))
				{
					if(in_multi_array($needle, $haystack[$i]))
					{
						$in_multi_array = true;
						break;
					}
				}
			}
		}
	}
	return $in_multi_array;
}

function phrase_add($lang_code, $lw_page, $lw_word, $phrase_value, $phrase_product = 'ACPanel')
{
    global $config, $db;
 
    $arguments = array('lang' => $lang_code, 'tpl' => $lw_page, 'phrase_name' => $lw_word, 'phrase_value' => $phrase_value, 'productid' => $phrase_product);
    $result_update = $db->Query("INSERT INTO `acp_lang_words` (lw_word, lw_page, {lang}, productid)
        VALUES ('{phrase_name}', '{tpl}', '{phrase_value}', '{productid}')
        ON DUPLICATE KEY UPDATE {lang} = '{phrase_value}', productid = '{productid}'", $arguments, $config['sql_debug']);
 
    if($result_update)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function category_add($parent, $display_order, $link, $title, $show_blocks, $productid, $admin_access = true, $url = "", $cat_desc = "")
{
	global $config, $db;

	if( !$parent )
	{
		$arguments = array('display_order'=>$display_order,'title'=>$title,'link'=>$link,'url'=>$url,'show_blocks'=>$show_blocks,'productid'=>$productid,'description'=>$cat_desc);
		$result_insert = $db->Query("INSERT INTO `acp_category` (catleft, catright, catlevel, display_order, title, link, show_blocks, productid, url, description) VALUES ('1', '2', '0', '{display_order}', '{title}', '{link}', '{show_blocks}', '{productid}', '{url}', '{description}')", $arguments, $config['sql_debug']);
		$last_unsert_id = $db->LastInsertID();

		if( $result_insert && $last_unsert_id )
		{
			if( $link == "custom_page" )
				$result_insert = $db->Query("INSERT INTO `acp_pages` SET catid = ".$last_unsert_id, array(), $config['sql_debug']);

			if( $admin_access )
			{
				$arguments = array('last_id'=>$last_unsert_id);
				$result_update = $db->Query("UPDATE `acp_usergroups` SET read_category = IF(LENGTH(read_category) > 0, CONCAT(read_category, ',', {last_id}), {last_id}) WHERE admin_access = 'yes'", $arguments, $config['sql_debug']);
			}

			return $last_unsert_id;
		}
	}
	else
	{
		$arguments = array('parent'=>$parent);
		$result = $db->Query("SELECT * FROM `acp_category` WHERE categoryid = '{parent}' LIMIT 1", $arguments, $config['sql_debug']);
		if(is_array($result))
		{
			foreach ($result as $obj)
			{
				$level = $obj->catlevel + 1;
				$section = (!$obj->sectionid) ? $obj->categoryid : $obj->sectionid;

				if(($obj->catright - $obj->catleft) == 1)
				{
					$catleft = $obj->catright;
				}
				else
				{
					$arguments = array('section'=>$section,'catright'=>$obj->catright,'display_order'=>$display_order,'parent'=>$parent);
					$catleft = $db->Query("SELECT MIN(catleft) FROM `acp_category` WHERE sectionid = '{section}' AND catright < {catright} AND display_order >= {display_order} AND parentid = '{parent}'", $arguments, $config['sql_debug']);
					if(!$catleft)
					{
						$catleft = $obj->catright;
					}
				}

				$arguments = array('section'=>$section,'catright'=>$obj->catright,'catleft'=>$catleft);
				$result_update = $db->Query("UPDATE `acp_category` SET catleft = IF(catleft < $catleft, catleft, catleft + 2), catright = IF(catright < $catleft, catright, catright + 2) WHERE categoryid = $section OR sectionid = '$section'", $arguments, $config['sql_debug']);
				$catright = $catleft + 1;

				$arguments = array('section'=>$section,'display_order'=>$display_order,'title'=>$title,'link'=>$link,'url'=>$url,'show_blocks'=>$show_blocks,'productid'=>$productid,'parent'=>$parent,'catleft'=>$catleft,'catright'=>$catright,'level'=>$level,'description'=>$cat_desc);
				$result_insert = $db->Query("INSERT INTO `acp_category` (sectionid, parentid, catleft, catright, catlevel, display_order, title, link, show_blocks, productid, url, description) VALUES ('{section}', '{parent}', '{catleft}', '{catright}', '{level}', '{display_order}', '{title}', '{link}', '{show_blocks}', '{productid}', '{url}', '{description}')", $arguments, $config['sql_debug']);
				$last_unsert_id = $db->LastInsertID();
				if( $result_insert && $last_unsert_id )
				{
					if( $link == "custom_page" )
						$result_insert = $db->Query("INSERT INTO `acp_pages` SET catid = ".$last_unsert_id, array(), $config['sql_debug']);

					if( $admin_access )
					{
						$arguments = array('last_id'=>$last_unsert_id);
						$result_update = $db->Query("UPDATE `acp_usergroups` SET read_category = IF(LENGTH(read_category) > 0, CONCAT(read_category, ',', {last_id}), {last_id}) WHERE admin_access = 'yes'", $arguments, $config['sql_debug']);
					}

					return $last_unsert_id;
				}
			}
		}
	}

	return 0;
}

function category_delete($category)
{
	global $config, $db;

	$arguments = array('category'=>$category);
	$result = $db->Query("SELECT * FROM `acp_category` WHERE categoryid = '{category}'", $arguments, $config['sql_debug']);
	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			if( $obj->sectionid )
			{
				$id = $obj->sectionid;
				$where = " AND sectionid = {id}";
			}
			else
			{
				$id = $obj->categoryid;
				$where = " AND (sectionid = {id} OR categoryid = {id})";
			}

			$arguments = array('catleft'=>$obj->catleft,'catright'=>$obj->catright,'id'=>$id);
			$result_select = $db->Query("SELECT categoryid, title FROM `acp_category` WHERE catleft >= {catleft} AND catright <= {catright}".$where, $arguments, $config['sql_debug']);
			if( is_array($result_select) )
			{
				foreach( $result_select as $objid )
				{
					$array_delete_id[] = $objid->categoryid;
				}
			}

			$result_del = $db->Query("DELETE FROM `acp_category` WHERE catleft >= $obj->catleft AND catright <= $obj->catright".$where, $arguments, $config['sql_debug']);
			if( $result_del && $obj->link == "custom_page" )
				$result_del_page = $db->Query("DELETE FROM `acp_pages` WHERE catid = ".$obj->categoryid, array(), $config['sql_debug']);

			if( $obj->catlevel )
			{
				if( $result_del )
				{
					$arguments = array('catleft'=>$obj->catleft,'catright'=>$obj->catright,'section'=>$obj->sectionid);
					$result_update = $db->Query("UPDATE `acp_category` SET catleft = IF(catleft < {catleft}, catleft, catleft - ({catright} - {catleft} + 1)), catright = IF(catright < {catleft}, catright, catright - ({catright} - {catleft} + 1)) WHERE categoryid = {section} OR sectionid = {section}", $arguments, $config['sql_debug']);

					if( !$result_update )
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}

			if( !empty($array_delete_id) )
			{
				$result_select_access = $db->Query("SELECT read_category, usergroupid FROM `acp_usergroups` WHERE LENGTH(read_category) > 0", array(), $config['sql_debug']);
				if( is_array($result_select_access) )
				{
					foreach( $result_select_access as $objrc )
					{
						$search_cat = is_numeric($objrc->read_category) ? array($objrc->read_category) : explode(',', $objrc->read_category);
						$cat_string_read = implode(',', array_diff($search_cat, $array_delete_id));
						$arguments = array('usergroupid'=>$objrc->usergroupid,'read_category'=>$cat_string_read);
						$result_update_access = $db->Query("UPDATE `acp_usergroups` SET read_category = '{read_category}' WHERE usergroupid = {usergroupid}", $arguments, $config['sql_debug']);
					}
				}
			}

			return true;
		}
	}
}

function category_resort($parent)
{
	global $config, $db;

	if(!$parent)
	{
		return false;
	}
	else
	{
		$arguments = array('parent'=>$parent);
		$result = $db->Query("SELECT * FROM `acp_category` WHERE categoryid = {parent} OR parentid = {parent} ORDER BY catlevel, display_order", $arguments, $config['sql_debug']);

		if (is_array($result))
		{
			foreach ($result as $obj)
			{
				if ($obj->categoryid == $parent)
				{
					$left = $obj->catleft;
					$right = $obj->catright;
					$first = true;
				}
				else
				{
					if (!$left OR !$right)
					{
						return false;
					}
					else
					{
						if ($first)
						{
							$first = false;

							$diff = $obj->catleft - $left - 1;
							$left = $obj->catleft - $diff;
							$right = $obj->catright - $diff;
						}
						else
						{
							$diff = $obj->catleft - $right - 1;
							$left = $obj->catleft - $diff;
							$right = $obj->catright - $diff;
						}

						if ($obj->catleft != $left)
						{
							unset($child_id);

							$arguments = array('catleft'=>$obj->catleft,'catright'=>$obj->catright,'section'=>$obj->sectionid);
							$result_select = $db->Query("SELECT categoryid FROM `acp_category` WHERE catleft BETWEEN {catleft} AND {catright} AND sectionid = {section}", $arguments, $config['sql_debug']);
							if(is_array($result_select))
							{
								foreach ($result_select as $childobj)
								{
									$child_id[] = $childobj->categoryid;
								}
							}
							else
							{
								$child_id[0] = $result_select;
							}

							if(!empty($child_id))
							{
								$update_string[] = "UPDATE `acp_category` SET catright = catright - ".$diff.", catleft = catleft - ".$diff." WHERE categoryid IN (".implode(',',$child_id).")";
							}
						}
					}
				}
			}

			if (!empty($update_string))
			{
				foreach ($update_string as $query)
				{
					$result_update = $db->Query($query, array(), $config['sql_debug']);
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}
}

function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
	$size = getimagesize($src);

	if ($size === false) return -1;

	$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
	$icfunc = "imagecreatefrom" . $format;
	if (!function_exists($icfunc)) return -2;

	$x_ratio = $width / $size[0];
	$y_ratio = $height / $size[1];

	$ratio = min($x_ratio, $y_ratio);
	$use_x_ratio = ($x_ratio == $ratio);

	$new_width = $use_x_ratio ? $width  : floor($size[0] * $ratio);
	$new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
	$new_left = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
	$new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

	$isrc = $icfunc($src);
	$idest = imagecreatetruecolor($width, $height);

	imagefill($idest, 0, 0, $rgb);
	imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0,
	$new_width, $new_height, $size[0], $size[1]);

	imagejpeg($idest, $dest, $quality);

	imagedestroy($isrc);
	imagedestroy($idest);

	return 1;
}

function block_output_content($code)
{
	eval($code);
    return (isset($return)) ? $return : "";
}

function getProduct($productID)
{
	global $config, $db;

	$output = array();
	$arguments = array('productID'=>$productID);
	$result = $db->Query("SELECT version, active FROM `acp_products` WHERE productid = '{productID}'", $arguments, $config['sql_debug']);

	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$output['version'] = $obj->version;
			$output['active'] = $obj->active;
		}
	}

	return $output;
}

?>
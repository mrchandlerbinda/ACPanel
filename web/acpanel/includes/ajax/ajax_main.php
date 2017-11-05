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

	// 1 - set cookie lang
	// 2 - convert steam

	switch($_POST['go'])
	{
		case "1":

			$lang = $_POST['lang'];

			switch($ext_auth_type)
			{
				case "xf":

					$xf->setVisitorLanguage($lang);
					break;

				default:

					$expires = (60 * 60 * 24 * $config['cookie_time']) + time();
					$savecookie = setcookie("acp_language", $lang, $expires, "/");
					break;
			}

			if (in_array("log_change_lang", $config['user_action_log'])) saveLogs("user_change_lang", "user changed the language to ".get_language(1));

			break;

		case "2":

			$langs = create_lang_list();

			unset($translate);
			$filter = "lp_name='blocks/block_steam_tool.tpl' AND lp_id = lw_page";
			$arguments = array('lang'=>get_language(1));
			$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
			if(is_array($tr_result)) {
				foreach ($tr_result as $obj){
					$translate[$obj->lw_word] = $obj->lw_translate;
				}
			}

			header('Content-type: text/html; charset='.$config['charset']);

			$steam = strtoupper($_POST['steam']);

			$RightSteam = "/^(STEAM_0)\:([0-1])\:([0-9]{4,8})$/";
			$RightNumber = "/^(7656119)([0-9]{10})$/";

			if( $steam == "" )
			{				print '<div class="message errormsg"><p>'.$translate['steam_empty'].'</p></div>';
			}
			elseif( preg_match($RightSteam, $steam, $match) )
			{
				if (in_array("log_steam_convert", $config['user_action_log'])) saveLogs("convert_steam", "user checks through the steam converter: ".$steam);

				$newst1 = "$match[2]";
				$newst2 = "$match[3]";
				$const1 = 7656119;
				$const2 = 7960265728;
				$answer = $newst1 + $newst2 * 2 + $const2;

				print '<div class="message success"><p><a target="_blank" href="http://steamcommunity.com/profiles/'.$const1.$answer.'" alt="">'.$const1.$answer.'</a></p></div>';
			}
			elseif (preg_match($RightNumber, $steam, $match))
			{
				if (in_array("log_steam_convert", $config['user_action_log'])) saveLogs("convert_steam", "user checks through the steam converter: ".$steam);

				$const1 = 7960265728;
				$const2 = "STEAM_0:";

				if ($const1 <= $match[2])
				{
					$a = ($match[2] - $const1)%2;
					$b = ($match[2] - $const1 - $a)/2;

					print '<div class="message success"><p>'.$const2.$a.':'.$b.'</p></div>';
				}
				else
				{					print '<div class="message errormsg"><p>'.$translate['steam_wrong'].'</p></div>';
				}
			}
			else
			{
				print '<div class="message errormsg"><p>'.$translate['steam_wrong'].'</p></div>';
			}

			break;

		default:

			die("Hacking Attempt");
	}
}

?>
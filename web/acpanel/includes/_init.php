<?php

if(!defined('IN_ACP')) die("Hacking attempt!");

// ###############################################################################
// INIT SCRIPT START TIME
// ###############################################################################

$start_time = microtime();
$start_array = explode(" ",$start_time);
$start_time = $start_array[1] + $start_array[0];

// ###############################################################################
// LOAD GENERAL OPTIONS
// ###############################################################################

unset($config); // for security
require(INCLUDE_PATH . '_cfg.php');

if( !file_exists(ROOT_PATH . $config['acpanel'] . ".php") ) die("The main script is incorrect!");

if( ($config['acpanel'] != 'index' && $config['acpanel'] != 'lime') ) die('Main script does not correctly defined!');

if( ($config['acpanel'] == 'index' && !defined('MAIN_INDEX')) || ($config['acpanel'] == 'lime' && defined('MAIN_INDEX')) )
{
	header('Location: '.$config['acpanel'].'.php');
}

// ###############################################################################
// ESTABLISH DATABASE CONNECTION
// ###############################################################################

require_once(INCLUDE_PATH . 'class.mysql.php');

try {
	$db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
} catch (Exception $e) {
	die($e->getMessage());
}

// ###############################################################################
// LOAD CONFIG
// ###############################################################################

$array_cfg = $db->Query("SELECT varname, value FROM `acp_config` WHERE varname IS NOT NULL", array(), true);

if( is_array($array_cfg) )
{
	foreach( $array_cfg as $obj )
	{
		$config[$obj->varname] = $obj->value;
	}
	$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();			
}

// ###############################################################################
// INIT SMARTY
// ###############################################################################

require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');

$smarty = new Smarty();
$smarty->template_dir = TEMPLATE_PATH . $config['template'].'/';
$smarty->compile_dir = TEMPLATE_PATH . $config['template'].'/templates_c/';
$smarty->config_dir = TEMPLATE_PATH . '_configs/';
$smarty->cache_dir = TEMPLATE_PATH . '_cache/';

$smarty->assign("tpl", $config['template']);
$smarty->assign("charset", $config['charset']);

// ###############################################################################
// LOAD FUNCTIONS
// ###############################################################################

include(INCLUDE_PATH . 'functions.main.php');

// ###############################################################################
// INTEGRATION SETUP
// ###############################################################################

$ext_auth_type = '';
if( isset($config['ext_auth_type']) )
{
	if( $config['ext_auth_type'] == "xf" && isset($config['xfAuth']) )
	{
		$ext_auth_type = $config['ext_auth_type'];

		require_once(INCLUDE_PATH . 'class.xfAuth.php');
		$xf = new XF_auth($config['xfAuth']);
	}
}

// ###############################################################################
// LOAD LANGUAGES
// ###############################################################################

$langs = create_lang_list();
$userLangEXT = get_language(0);

$smarty->assign("arr_lang", $langs);
$smarty->assign("get_lang", $userLangEXT);
$smarty->assign("home",$_SERVER['PHP_SELF']);

// ###############################################################################
// USER AUTHORIZED
// ###############################################################################

include(INCLUDE_PATH . '_auth.php');
$smarty->assign("isuser", $userinfo);

?>

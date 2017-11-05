<?php

if(isset($_GET["do"]) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
{
	// ###############################################################################
	// DEFINE CONSTANT
	// ###############################################################################

	define("IN_ACP", true);
	define('ROOT_PATH', './');
	define('SCRIPT_PATH', ROOT_PATH . 'scripts/');
	define('INCLUDE_PATH', ROOT_PATH . 'includes/');
	define('TEMPLATE_PATH', ROOT_PATH . 'templates/');

	// ###############################################################################
	// LOAD GENERAL OPTIONS
	// ###############################################################################

	unset($config); // for security
	require(INCLUDE_PATH . '_cfg.php');
	$ext_auth_type = '';
	if( isset($config['ext_auth_type']) )
	{
		if( $config['ext_auth_type'] == "xf" && isset($config['xfAuth']) )
		{
			$ext_auth_type = $config['ext_auth_type'];
			require_once(INCLUDE_PATH . 'class.xfAuth.php');

			$config['xfAuth']['AJAX'] = true;
			$xf = new XF_auth($config['xfAuth']);
		}
	}

	// ###############################################################################
	// GENERATE CONTENT
	// ###############################################################################

	$filepath = INCLUDE_PATH . 'ajax/' . $_GET["do"] . '.php';
	if(file_exists($filepath))
	{
		include($filepath);
	}
	else
	{
		die('File handler could not be found!');
	}
}
else
{
	die('Allowed to use only the ajax requests!');
}

?>
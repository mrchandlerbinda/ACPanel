<?php

$config = array();

// Main script (index/acpanel)
$config['acpanel'] = "index";

// Database Charset
$config['charset_db'] = "utf8";

// Storage Time Cookie (days)
$config['cookie_time'] = "365";

// Cookie Salt
$config['secretkey'] = "iAd7as32Dt2123";

// Database Name
$config['dbname'] = "lime";

// Database Host
$config['hostname'] = "localhost";

// Database Login
$config['username'] = "lime";

// Database Password
$config['password'] = "lime";

// External authentication
$config['ext_auth_type'] = ""; // xf

// Config options for xfAuth
$config['xfAuth'] = array(

	/**
	 * Use for link construction.
	 */
	'forumUrl' => 'http://localohst/xenforo/',

	/**
	 * Root location for xenForo install, to locate Autoloader.
	*/
	'fileDir' => '/htdocs/xenforo'

);

$config['cron'] = array(
	"curl" => true, // enable cURL for the scheduler?
	"time" => 600, // repeat delay for cURL = false
	"cache" => 720, // cron jobs cache time in minutes
	"log" => true // logging cron entry?
);

?>
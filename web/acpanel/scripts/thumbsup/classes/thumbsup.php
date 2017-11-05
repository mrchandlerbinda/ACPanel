<?php

if(!defined('IN_ACP')) die("Hacking attempt!");

class ThumbsUp {

	static public $config = NULL;
	static public $mysql;
	static public $user;

	function __construct($cfg, $mysql, $user)  
	{
		self::$config = $cfg;
		self::$mysql = $mysql;
		self::$user = $user;
	}

	public static function db()
	{
		return self::$mysql;
	}

	/**
	 * Returns configuration settings from _cfg.php.
	 *
	 * @param   string  config key
	 * @param   mixed   default config value
	 * @return  mixed   config value
	 */
	public static function config($key, $default = NULL)
	{
		// Cache
		static $config;

		// Load the config.php file only once
		if( $config === NULL )
		{
			$config = self::$config;
		}

		// If the key is not found, return the default value
		return (isset($config[$key])) ? $config[$key] : $default;
	}

	/**
	 * Enables all error reporting, used in debug mode.
	 *
	 * @return  void
	 */
	public static function debug_mode()
	{
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', TRUE);
	}

	/**
	 * Enables support for the json_encode and json_decode functions.
	 * These functions should be available natively from PHP 5.2.
	 *
	 * @return  void
	 */
	public static function json_support()
	{
		// If the json functions are already available, we're done here
		if (function_exists('json_encode') AND function_exists('json_decode'))
			return;

		if ( ! class_exists('Services_JSON', FALSE))
		{
			require THUMBSUP_DOCROOT.'classes/services_json.php';
		}

		if ( ! function_exists('json_encode'))
		{
			function json_encode($data)
			{
				$json = new Services_JSON;
				return $json->encode($data);
			}
		}

		if ( ! function_exists('json_decode'))
		{
			function json_decode($data)
			{
				$json = new Services_JSON;
				return $json->decode($data);
			}
		}
	}

	/**
	 * Returns TRUE if the current request in an ajax request, FALSE otherwise.
	 *
	 * @return  boolean
	 */
	public static function is_ajax()
	{
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
	}

	/**
	 * Retrieves the IP address for the current user.
	 *
	 * @return  mixed  valid IP string, or NULL if not found
	 */
	public static function get_ip()
	{
		// Cache
		static $ip = FALSE;

		// This code only needs to be executed once per request
		if ($ip !== FALSE)
			return $ip;

		// Loop over $_SERVER keys that can contain the IP address
		foreach (array('REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR') as $key)
		{
			// Return the first valid IP we find
			if (isset($_SERVER[$key]) AND preg_match('~^(?:\d{1,3}+\.){3}\d{1,3}+$~D', $_SERVER[$key]))
				return $ip = $_SERVER[$key];
		}

		// No valid IP found
		return $ip = NULL;
	}

	/**
	 * Retrieves the user ID for the current user.
	 *
	 * @return  mixed  user id integer, or NULL if not found
	 */
	public static function get_user_id()
	{
		// Cache
		static $user_id = FALSE;

		// This code only needs to be executed once per request
		if ($user_id !== FALSE)
			return $user_id;

		// Load the user id
		$user_id = self::$user;

		// Set user id to NULL if not found
		return ($user_id) ? $user_id : $user_id = NULL;
	}

	/**
	 * Returns a ThumbsUp item. If no existing item could be found,
	 * a new item with the given name will be created automatically.
	 *
	 * @param   string  item name
	 * @return  object  ThumbsUp_Item
	 */
	public static function item($name)
	{
		// Load the ThumbsUp item
		return $item = ThumbsUp_Item::load((string) $name);
	}

	/**
	 * Looks at the POST data to catch a possible new vote. If one, the vote is
	 * completely validated first before being registered.
	 *
	 * @return  boolean  TRUE if a new vote was cast; FALSE otherwise
	 */
	public static function catch_vote()
	{
		// Immediately get out of here if no valid vote was cast.
		// All required POST keys must be present.
		if ( ! isset($_POST['thumbsup_id']) OR ! isset($_POST['thumbsup_vote']) OR ! isset($_POST['thumbsup_format']))
			return FALSE;

		// Has somebody been messing with the form?
		// Well, we won't let them mess with us!
		if ( ! preg_match('/^[0-9]++$/D', (string) $_POST['thumbsup_id']) OR ! is_string($format = $_POST['thumbsup_format']))
			return FALSE;

		// Clean form input
		$id   = (int) $_POST['thumbsup_id'];
		$vote = (int) $_POST['thumbsup_vote'];

		// Attempt to load the relevant ThumbsUp item.
		// If the item doesn't exist, the id is invalid.
		if ( ! $item = ThumbsUp_Item::load($id))
		{
			$error = 'invalid_id';
		}
		// Voting on the item has been closed
		elseif ($item->closed)
		{
			$error = 'closed';
		}
		// The user has already voted on this item
		elseif ($item->user_voted)
		{
			$error = 'already_voted';
		}
		// You have to be logged in to vote
		elseif (!ThumbsUp::config('mon_vote_guests') AND ! self::get_user_id())
		{
			$error = 'login_required';
		}

		// All checks passed, yay!
		if (empty($error))
		{
			if( ! $user_id = ThumbsUp::get_user_id() OR ! $num = ThumbsUp::config('mon_vote_user_weight')  )
			{
				$num = 1;
			}
			// Update the vote count in the items table, and recalculate the vote results
			$item->cast_vote($vote, $num);
		}

		// Send an ajax response
		if (self::is_ajax())
		{
			// Send the item back in JSON format
			header('Content-Type: application/json; charset=utf-8');

			if ( ! empty($error))
			{
				// Send back the error
				echo json_encode(array('error' => $error));
			}
			else
			{
				// Format the result using the same format the item was created with
				$item->format($format);

				// Send back the updated item.
				// Note: all the public properties of $item will be included.
				echo json_encode(array('item' => $item));
			}
		}

		// A new vote has been cast successfully
		return empty($error);
	}

	/**
	 * Generates the CSS stylesheet links that should go into the HTML page.
	 *
	 * @return  string
	 */
	public static function css()
	{
		return '<link rel="stylesheet" href="acpanel/scripts/thumbsup/styles.css" />'."\n";
	}
}
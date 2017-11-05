<?php

define("IN_ACP", true);
define('ROOT_PATH', './');
define('SCRIPT_PATH', ROOT_PATH . 'scripts/');
define('INCLUDE_PATH', ROOT_PATH . 'includes/');

// mb - mini bar 350*20
// cb - counter banner 88*31

if( isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['type']) && isset($_GET['theme']) )
{
	if( in_array($_GET['type'], array('mb', 'cb')) )
	{
		unset($config); // for security
		require(INCLUDE_PATH . '_cfg.php');
		include(INCLUDE_PATH . 'functions.main.php');
		require_once(INCLUDE_PATH . 'class.mysql.php');
		try {
			$db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
		} catch (Exception $e) {
			die($e->getMessage());
		}

		switch($_GET['type'])
		{
			case "mb":

				$arrOptions = array(
					'left' => '#BA0014',
					'right' => '#480014',
					'textcolor' => '#FFFFFF',
					'bordercolor' => ''
				);

				$options_string = $_GET['theme'];
				if( strlen($options_string) > 6 && $options_string{0} == "_" )
				{
					$options_string = substr($options_string, 1);
					$options_array = explode("_", $options_string);

					$arrKeys = array_keys($arrOptions);
					$count = count($arrKeys);

					foreach($options_array as $k => $v)
					{
						$count--;

						if( preg_match("/^[0-9a-z]{6}$/i", $v) )
						{
							$arrOptions[$arrKeys[$k]] = $v;
						}

						if( $count == 0 )
							break;
					}
				}

				$result = $db->Query("SELECT id, gametype, address, hostname, rating, position, vip, status, cache_map, cache_players, cache_playersmax FROM `acp_servers` WHERE id = '{id}' LIMIT 1", array('id' => $_GET['id']));
				if( is_array($result) )
				{
					require(INCLUDE_PATH . 'class.UserBars.php');
					$generator = new UserBars();
			
					foreach( $result as $obj )
					{
						$image = $generator->create($obj->hostname, $obj->address."  ".$obj->cache_players."/".$obj->cache_playersmax."  ".$obj->cache_map, $obj->status, $obj->gametype.".png", $obj->position, $arrOptions);
					}
			
					header('Content-type: image/png'); 
					imagepng($image);
					imagedestroy($image);
				}

				break;

			case "cb":

				$arrOptions = array(
					'left' => '#BA0014',
					'right' => '#480014',
					'textcolor' => '#FFFFFF',
					'bordercolor' => ''
				);

				$options_string = $_GET['theme'];
				if( strlen($options_string) > 6 && $options_string{0} == "_" )
				{
					$options_string = substr($options_string, 1);
					$options_array = explode("_", $options_string);

					$arrKeys = array_keys($arrOptions);
					$count = count($arrKeys);

					foreach($options_array as $k => $v)
					{
						$count--;

						if( preg_match("/^[0-9a-z]{6}$/i", $v) )
						{
							$arrOptions[$arrKeys[$k]] = $v;
						}

						if( $count == 0 )
							break;
					}
				}

				$result = $db->Query("SELECT id, gametype, address, hostname, rating, position, vip, status, cache_map, cache_players, cache_playersmax FROM `acp_servers` WHERE id = '{id}' LIMIT 1", array('id' => $_GET['id']));
				if( is_array($result) )
				{
					require(INCLUDE_PATH . 'class.UserBars.php');
					$generator = new UserBars();
			
					foreach( $result as $obj )
					{
						$image = $generator->create("в рейтинге", false, false, false, $obj->position." место", $arrOptions, "cb");
					}
			
					header('Content-type: image/png'); 
					imagepng($image);
					imagedestroy($image);
				}

				break;
		}
	}
}

?>
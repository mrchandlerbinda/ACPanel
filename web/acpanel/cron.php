<?php
 
// ###############################################################################
// DEFINE CONSTANT
// ###############################################################################
 
ini_set('ignore_user_abort','On');
ignore_user_abort(true);
define('RELOAD_SCRIPT_FREQUENCY',20);
set_time_limit(RELOAD_SCRIPT_FREQUENCY+10);
session_write_close();
 
define("IN_ACP", true);
define('ROOT_PATH', './');
define('SCRIPT_PATH', ROOT_PATH . 'scripts/');
define('INCLUDE_PATH', ROOT_PATH . 'includes/');
define('TEMPLATE_PATH', ROOT_PATH . 'templates/');
define('CACHE_PATH', TEMPLATE_PATH . '_cache/');
 
// ###############################################################################
// LOAD GENERAL OPTIONS
// ###############################################################################
 
unset($config);
require(INCLUDE_PATH . '_cfg.php');
 
$db = NULL;
$nowTime = time();
$config['cron'] = array(
    "curl" => (isset($config['cron']['curl'])) ? $config['cron']['curl'] : false,
    "time" => (isset($config['cron']['time']) && $config['cron']['time'] > 0) ? $config['cron']['time'] : 600, // in seconds (10 minutes)
    "cache" => (isset($config['cron']['cache']) && $config['cron']['cache'] > 0) ? $config['cron']['cache']*60 : 43200, // in minutes (12 hours)
    "log" => (isset($config['cron']['log'])) ? $config['cron']['log'] : false
);
 
function getFileCache($file, $cache_time)
{
    clearstatcache();
    if( is_file($file) )
    {
        if( @filemtime($file) + $cache_time > time() )
        {
            $fp = fopen($file, 'r');
            flock($fp, LOCK_SH);
            $data = stream_get_contents($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
   
            return $data;
        }
    }
 
    return false;
}
 
require_once(INCLUDE_PATH . 'class.mysql.php');
 
// ###############################################################################
// CHECK CRON JOBS
// ###############################################################################
 
if( file_exists(CACHE_PATH . 'cron.pid') && !$config['cron']['curl'] )
{
    unlink(CACHE_PATH . 'cron.pid');
}
 
clearstatcache();
if( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )
{
    if( !file_exists(CACHE_PATH . 'cron.pid') && $config['cron']['curl'] )
    {
        $pid = time();
        file_put_contents(CACHE_PATH . 'cron.pid', $pid);
 
        if( extension_loaded('curl') )
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?pid='.$pid);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_exec($ch);
            curl_close($ch);
        }
        elseif ( ini_get('allow_url_fopen') )
        {
            $ctx = stream_context_create(array(
                'http' => array(
                    'method' => 'HEAD',
                    'header' => '',
                    'timeout' => 10
                )
            ));
            file_get_contents('http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?pid='.$pid, 0, $ctx);
        }
 
        die();
    }
 
    clearstatcache();
    if( file_exists(CACHE_PATH . 'cron.last') )
    {
        if( file_get_contents(CACHE_PATH . 'cron.last') + $config['cron']['time'] > $nowTime )
            die();
    }
 
    file_put_contents(CACHE_PATH . 'cron.last', $nowTime);
 
    if( ($cronJobs = getFileCache(CACHE_PATH . 'cron.jobs', $config['cron']['cache'])) === false )
    {
        try {
            $db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
        } catch (Exception $e) {
            die($e->getMessage());
        }
 
        $cronJobs = $db->Query("SELECT c.entry_id, c.cron_file, c.run_rules FROM `acp_cron_entry` AS c
            LEFT JOIN `acp_products` AS a ON (a.productid = c.product_id)
            WHERE c.active = 1 AND (a.productid IS NULL OR a.active = 1)", array());
 
        if( !is_array($cronJobs) )
            $cronJobs = array();
 
        @file_put_contents(CACHE_PATH . 'cron.jobs', serialize($cronJobs), LOCK_EX);
        @chmod(CACHE_PATH . 'cron.jobs', 0666);
    }
    else
    {
        $cronJobs = unserialize($cronJobs);
    }
 
    $cronJobs = ( !is_array($cronJobs) ) ? array() : $cronJobs;
 
    require_once(INCLUDE_PATH . 'class.CronParser.php');
    $cron = new CronParser($cronJobs, $config, ROOT_PATH, $db, RELOAD_SCRIPT_FREQUENCY+10);
 
    if( $cron->getFoundTask() && $config['cron']['curl'] )
    {
        $pid = time();
        file_put_contents(CACHE_PATH . 'cron.pid', $pid);
 
        if( extension_loaded('curl') )
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?pid='.$pid);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_exec($ch);
            curl_close($ch);
        }
        elseif ( ini_get('allow_url_fopen') )
        {
            $ctx = stream_context_create(array(
                'http' => array(
                    'method' => 'HEAD',
                    'header' => '',
                    'timeout' => 10
                )
            ));
            file_get_contents('http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?pid='.$pid, 0, $ctx);
        }
    }
}
elseif( isset($_GET["pid"]) && file_exists(CACHE_PATH . 'cron.pid') )
{
    // prevent double cron
    $pid = file_get_contents(CACHE_PATH . 'cron.pid');
    if( $pid != $_GET['pid'] )
        die();
 
    file_put_contents(CACHE_PATH . 'cron.last', $nowTime);
 
    require_once(INCLUDE_PATH . 'class.CronParser.php');
 
    if( ($cronJobs = getFileCache(CACHE_PATH . 'cron.jobs', $config['cron']['cache'])) === false )
    {
        try {
            $db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
        } catch (Exception $e) {
            die($e->getMessage());
        }
 
        $cronJobs = $db->Query("SELECT c.entry_id, c.cron_file, c.run_rules FROM `acp_cron_entry` AS c
            LEFT JOIN `acp_products` AS a ON (a.productid = c.product_id)
            WHERE c.active = 1 AND (a.productid IS NULL OR a.active = 1)", array());
 
        if( !is_array($cronJobs) )
            $cronJobs = array();
 
        @file_put_contents(CACHE_PATH . 'cron.jobs', serialize($cronJobs), LOCK_EX);
        @chmod(CACHE_PATH . 'cron.jobs', 0666);
    }
    else
    {
        $cronJobs = unserialize($cronJobs);
    }
 
    $cronJobs = ( !is_array($cronJobs) ) ? array() : $cronJobs;
 
    $c = new CronParser($cronJobs, $config, ROOT_PATH, $db);
    sleep(1);
 
    if( extension_loaded('curl') )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?pid='.$pid);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);
    }
    elseif ( ini_get('allow_url_fopen') )
    {
        $ctx = stream_context_create(array(
            'http' => array(
                'method' => 'HEAD',
                'header' => '',
                'timeout' => 10
            )
        ));
        file_get_contents('http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?pid='.$pid, 0, $ctx);
    }
}
 
die();
 
?>
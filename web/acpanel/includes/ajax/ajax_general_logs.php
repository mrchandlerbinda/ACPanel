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
    $filter = "lp_name='p_general_logs.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
    $arguments = array('lang'=>get_language(1));
    $tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
    if(is_array($tr_result))
    {
        foreach ($tr_result as $obj)
        {
            $translate[$obj->lw_word] = $obj->lw_translate;
        }
    }
 
    require_once(INCLUDE_PATH . '_auth.php');
 
    header('Content-type: text/html; charset='.$config['charset']);
 
    // 1 - create list
    // 2 - delete logs
 
    switch($_POST['go'])
    {
        case "1":
 
            $offset = $_POST['offset'] - 1;
            $limit = $_POST['limit'];
 
            $action = (isset($_POST['action'])) ? $_POST['action'] : '';
            $startdate = (isset($_POST['startdate'])) ? $_POST['startdate'] : '';
            $enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : '';
            $user_login = (isset($_POST['user_login'])) ? $_POST['user_login'] : '';
            $user_ip = (isset($_POST['user_ip'])) ? $_POST['user_ip'] : '';
 
            $sqlconds = 'WHERE 1=1 ';
 
            if ($action) { $sqlconds .= " AND action = '$action' "; }
            if ($user_login) { $sqlconds .= " AND username LIKE '%{user_login}%' "; }
            if ($user_ip) { $sqlconds .= " AND ip LIKE '%{user_ip}%' "; }
            if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
            if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }
 
            date_default_timezone_set('UTC');
            $arguments = array('offset'=>$offset,'limit'=>$limit,'action'=>$action,'user_login'=>$user_login,'user_ip'=>$user_ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true));
            $result = $db->Query("SELECT * FROM `acp_logs` $sqlconds ORDER BY `id` DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
 
            if( is_array($result) )
            {
                foreach( $result as $obj )
                {
                    $obj->timestamp = get_datetime($obj->timestamp, $config['date_format']);
                    $array_logs[] = (array)$obj;
                }
            }
 
            require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');
 
            $smarty = new Smarty();
            $smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
            $smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
            $smarty->config_dir = TEMPLATE_PATH . '_configs/';
            $smarty->cache_dir = TEMPLATE_PATH . '_cache/';
 
            $smarty->assign("home",$config['acpanel'].'.php');
 
            $smarty->assign("array_logs",$array_logs);
            if(isset($error)) $smarty->assign("iserror",$error);
 
            $smarty->registerFilter("output","translate_template");
            $smarty->display('p_general_logs_load.tpl');
 
            break;
 
        case "2":
 
            require_once(INCLUDE_PATH . 'class.Permissions.php');
            $permClass = new Permissions($db);
            $userPerm = $permClass->getPermissions('general_perm_logs', $userinfo['usergroupid']);
 
            if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' )
            {
                $action = (isset($_POST['action'])) ? $_POST['action'] : '';
                $startdate = (isset($_POST['startdate'])) ? $_POST['startdate'] : '';
                $enddate = (isset($_POST['enddate'])) ? $_POST['enddate'] : '';
                $user_login = (isset($_POST['user_login'])) ? $_POST['user_login'] : '';
                $user_ip = (isset($_POST['user_ip'])) ? $_POST['user_ip'] : '';
   
                $sqlconds = 'WHERE 1=1 ';
   
                if ($action != 'all') { $sqlconds .= " AND action = '{action}' "; }
                if ($user_login) { $sqlconds .= " AND username LIKE '%{user_login}%' "; }
                if ($user_ip) { $sqlconds .= " AND ip LIKE '%{user_ip}%' "; }
                if ($startdate) { $sqlconds .= " AND timestamp >= '{startdate}' "; }
                if ($enddate) { $sqlconds .= " AND timestamp <= '{enddate}' "; }
 
                date_default_timezone_set('UTC');
                $arguments = array('action'=>$action,'user_login'=>$user_login,'user_ip'=>$user_ip,'startdate'=>get_datetime(strtotime($startdate), false, true),'enddate'=>get_datetime(strtotime($enddate), false, true));
                $result = $db->Query("DELETE FROM `acp_logs` $sqlconds", $arguments, $config['sql_debug']);
   
                if ($result)
                {
                    $delnum = $db->Affected();
                    if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_general_logs", "delete logs: ".$delnum);
   
                    if( $delnum > 0 )
                    {
                        print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_success'].'&nbsp;'.$delnum.'</span>';
                    }
                    else
                    {
                        print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_null'].'</span>';
                    }
                }
                else
                {
                    print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_error'].'</span>';
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
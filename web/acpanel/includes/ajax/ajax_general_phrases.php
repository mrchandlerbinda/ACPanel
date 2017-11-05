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
    $filter = "lp_name='p_general_phrases.tpl' AND lp_id = lw_page OR lw_word = 'access_denied'";
    $arguments = array('lang'=>get_language(1));
    $tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
    if(is_array($tr_result)) {
        foreach ($tr_result as $obj){
            $translate[$obj->lw_word] = $obj->lw_translate;
        }
    }
 
    require_once(INCLUDE_PATH . '_auth.php');
 
    header('Content-type: text/html; charset='.$config['charset']);
 
    // 1 - create list
    // 2 - add item
    // 3 - del item
    // 4 - multiply del items
    // 5 - edit item
    // 6 - search result
 
    switch($_POST['go'])
    {
        case "1":
 
            $offset = $_POST['offset'] - 1;
            $limit = $_POST['limit'];
            $code = $_POST['code'];
            $lp_id = $_POST['lp_id'];
 
            $arguments = array('offset'=>$offset,'limit'=>$limit,'code'=>$code,'id'=>$lp_id);
            $where = (!$lp_id) ? " OR acp_lang_words.lw_page = '0'" : "";
            $result = $db->Query("SELECT lw_id, lw_word, {code} AS lw_lang, lp_name FROM `acp_lang_words`
                LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0','{id}')
                WHERE acp_lang_pages.lp_id = acp_lang_words.lw_page".$where." ORDER BY lp_name DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
 
            if( is_array($result) )
            {
                foreach ($result as $obj)
                {
                    $array_phrases[] = (array)$obj;
                }
            }
 
            require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');
 
            $smarty = new Smarty();
            $smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
            $smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
            $smarty->config_dir = TEMPLATE_PATH . '_configs/';
            $smarty->cache_dir = TEMPLATE_PATH . '_cache/';
 
            $smarty->assign("home",$config['acpanel'].'.php');
 
            $smarty->assign("colums","5");
            $smarty->assign("phrases",$array_phrases);
            if(isset($error)) $smarty->assign("iserror",$error);
 
            $smarty->registerFilter("output","translate_template");
            $smarty->display('p_general_phrases_list.tpl');
 
            break;
 
        case "2":
 
            require_once(INCLUDE_PATH . 'class.Permissions.php');
            $permClass = new Permissions($db);
            $userPerm = $permClass->getPermissions('general_perm_phrases', $userinfo['usergroupid']);
 
            if( $userPerm['add'] || $userinfo['admin_access'] == 'yes' )
            {
                $phrase_code = trim($_POST['code']);
                $phrase_tpl = trim($_POST['tpl']);
                $phrase_text = $_POST['phrase_text'];
                $productid = $_POST['productid'];
   
                if ($phrase_code == '')
                {
                    print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['dont_empty'].'</span>';
                }
                else
                {
                    unset($array_keys, $array_values);
                    if (is_array($phrase_text))
                    {
                        foreach ($phrase_text as $key => $text)
                        {
                            if ($config['charset'] != 'utf-8')
                            {
                                $f = iconv('utf-8', $config['charset'], $text);
                            }
                            else
                            {
                                $f = $text;
                            }
   
                            if (!empty($f))
                            {
                                $array_keys[] = $key;
                                $array_values[] = mysql_real_escape_string($f);
                            }
                        }
                    }
   
                    $arguments = array('phrase_code'=>$phrase_code,'phrase_tpl'=>$phrase_tpl,'phrase_text'=>$phrase_text,'productid'=>$productid);
                    $check = $db->Query("SELECT * FROM `acp_lang_words` WHERE lw_word = '{phrase_code}' AND lw_page = '{phrase_tpl}'", $arguments, $config['sql_debug']);
   
                    if ($check)
                    {
                        print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_try'].'</span>';
                    }
                    else
                    {
                        $result = $db->Query("INSERT INTO `acp_lang_words` (lw_word, lw_page, productid".((!empty($array_keys)) ? ', '.implode(',',$array_keys).'' : '').") VALUES ('{phrase_code}', '{phrase_tpl}', '{productid}'".((!empty($array_values)) ? ', \''.implode('\',\'',$array_values).'\'' : '').")", $arguments, $config['sql_debug']);
   
                        if ($result)
                        {
                            if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_phrases", "add phrase: ".$phrase_code);
                            print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['add_success'].'</span>';
                        }
                        else
                        {
                            print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_failed'].'</span>';
                        }
                    }
                }
            }
            else
                print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';
 
            break;
 
        case "3":
 
            require_once(INCLUDE_PATH . 'class.Permissions.php');
            $permClass = new Permissions($db);
            $userPerm = $permClass->getPermissions('general_perm_phrases', $userinfo['usergroupid']);
 
            if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' )
            {
                $id = $_POST['id'];
   
                $arguments = array('id'=>$id);
                $result = $db->Query("DELETE FROM `acp_lang_words` WHERE lw_id = '{id}'", $arguments, $config['sql_debug']);
   
                if ($result)
                {
                    if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_phrases", "delete phrase id: ".$id);
                    print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_success'].'</span>';
                }
                else
                {
                    print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
                }
            }
            else
                print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';
 
            break;
 
        case "4":
 
            require_once(INCLUDE_PATH . 'class.Permissions.php');
            $permClass = new Permissions($db);
            $userPerm = $permClass->getPermissions('general_perm_phrases', $userinfo['usergroupid']);
 
            if( $userPerm['delete'] || $userinfo['admin_access'] == 'yes' )
            {
                $ids = $_POST['marked_word'];
   
                $arguments = array('ids'=>$ids);
                $result = $db->Query("DELETE FROM `acp_lang_words` WHERE lw_id IN ('{ids}')", $arguments, $config['sql_debug']);
   
                if ($result)
                {
                    if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_phrases", "multiple delete phrases: ".count($ids));
                    print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['del_multiply_success'].'&nbsp;'.count($ids).'</span>';
                }
                else
                {
                    print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['del_failed'].'</span>';
                }
            }
            else
                print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';
 
            break;
 
        case "5":
 
            require_once(INCLUDE_PATH . 'class.Permissions.php');
            $permClass = new Permissions($db);
            $userPerm = $permClass->getPermissions('general_perm_phrases', $userinfo['usergroupid']);
 
            if( $userPerm['write'] || $userinfo['admin_access'] == 'yes' )
            {
                $phrase_code = trim($_POST['code']);
                $phrase_id = trim($_POST['lw_id']);
                $phrase_tpl = trim($_POST['tpl']);
                $phrase_text = $_POST['phrase_text'];
                $productid = $_POST['productid'];
   
                $update_string = "lw_word = '".mysql_real_escape_string($phrase_code)."', lw_page = '".mysql_real_escape_string($phrase_tpl)."', productid = '".mysql_real_escape_string($productid)."'";
                if( is_array($phrase_text) )
                {
                    foreach( $phrase_text as $key => $text )
                    {
                        if ($config['charset'] != 'utf-8') $text = iconv('utf-8', $config['charset'], $text);
                        $update_string .= ", ".$key." = '".mysql_real_escape_string($text)."'";
                    }
                }
   
                $arguments = array('phrase_code'=>$phrase_code,'phrase_tpl'=>$phrase_tpl,'id'=>$phrase_id,'productid'=>$productid);
                $check = $db->Query("SELECT * FROM `acp_lang_words` WHERE lw_word = '".mysql_real_escape_string($phrase_code)."' AND lw_page = '".mysql_real_escape_string($phrase_tpl)."' AND lw_id != ".$phrase_id, array(), $config['sql_debug']);
   
                if( $check )
                {
                    print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['add_try'].'</span>';
                }
                else
                {
                    $result = $db->Query("UPDATE `acp_lang_words` SET ".$update_string." WHERE lw_id = ".$phrase_id, array(), $config['sql_debug']);
   
                    if( $result )
                    {
                        if (in_array("log_edititing", $config['user_action_log'])) saveLogs("edit_phrases", "edit phrase: ".$phrase_code);
                        print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/success.gif" alt=""><span id="success" class="indent">'.$translate['edit_success'].'</span>';
                    }
                    else
                    {
                        print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['edit_failed'].'</span>';
                    }
                }
            }
            else
                print '<img style="vertical-align:middle;" src="acpanel/templates/' . $config['template'] . '/images/error.gif" alt=""><span id="error" class="indent">'.$translate['access_denied'].'</span>';
 
            break;
 
        case "6":
 
            $offset = $_POST['offset'] - 1;
            $limit = $_POST['limit'];
 
            $tpl = (isset($_POST['tpl'])) ? $_POST['tpl'] : '';
            $word = (isset($_POST['word'])) ? $_POST['word'] : '';
            $product = (isset($_POST['product'])) ? $_POST['product'] : '';
            $code = ( isset($_POST['code']) ) ? $_POST['code'] : "";
            if( $word )
            {
                if( $config['charset'] != 'utf-8' ) $word = iconv('utf-8', $config['charset'], $word);
            }
 
            $sqlconds = '';
            $array_phrases = array();
 
            if( $word )
            {
                $result = $db->Query("SELECT lang_code FROM `acp_lang`", array(), $config['sql_debug']);
                if( is_array($result) )
                {
                    foreach( $result as $obj )
                    {
                        $arrLang[] = $obj->lang_code;
                    }
                }
                else
                {
                    $arrLang[] = $result;
                }
       
                if( isset($arrLang) )
                {       
                    foreach( $arrLang as $k => $v )
                    {
                        if( count($array_phrases) < $limit )
                        {
                            if( $product ) $sqlconds = " AND acp_lang_words.productid = '{product}'";
                            if( $code ) $sqlconds .= " AND acp_lang_words.lw_word LIKE '%{code}%'";
                            $arguments = array('offset'=>$offset,'limit'=>$limit,'codelang'=>$v,'id'=>$tpl,'word'=>$word, 'product'=>$product, 'code'=>$code);
                            if( $tpl == "-1" )
                                $result = $db->Query("SELECT lw_id, lw_word, {codelang} AS lw_lang, lp_name FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0',acp_lang_words.lw_page) WHERE acp_lang_words.{codelang} LIKE '%{word}%'".$sqlconds." ORDER BY lp_name DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
                            else
                                $result = $db->Query("SELECT lw_id, lw_word, {codelang} AS lw_lang, lp_name FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0','{id}') WHERE acp_lang_words.{codelang} LIKE '%{word}%' AND (acp_lang_pages.lp_id = acp_lang_words.lw_page OR acp_lang_words.lw_page = '{id}')".$sqlconds." ORDER BY lp_name DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
   
                            if( is_array($result) )
                            {
                                foreach( $result as $obj )
                                {
                                    $array_phrases[] = (array)$obj;
                                    $limit -= count($array_phrases);
                                }
                            }
 
                            $offset = 0;
                        }
                        else
                        {
                            break;
                        }
                    }
                }
            }
            else
            {
                if( $product ) $sqlconds = " AND acp_lang_words.productid = '{product}'";
                if( $code ) $sqlconds .= " AND acp_lang_words.lw_word LIKE '%{code}%'";
                $arguments = array('offset'=>$offset,'limit'=>$limit,'id'=>$tpl, 'product'=>$product, 'code'=>$code);
                if( $tpl == "-1" )
                    $result = $db->Query("SELECT lw_id, lw_word, lw_en AS lw_lang, lp_name FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0',acp_lang_words.lw_page) WHERE 1 = 1".$sqlconds." ORDER BY lp_name DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
                else
                    $result = $db->Query("SELECT lw_id, lw_word, lw_en AS lw_lang, lp_name FROM `acp_lang_words` LEFT JOIN `acp_lang_pages` ON acp_lang_pages.lp_id=IF(acp_lang_words.lw_page = '0','0','{id}') WHERE (acp_lang_pages.lp_id = acp_lang_words.lw_page OR acp_lang_words.lw_page = '{id}')".$sqlconds." ORDER BY lp_name DESC LIMIT {offset},{limit}", $arguments, $config['sql_debug']);
   
                if( is_array($result) )
                {
                    foreach( $result as $obj )
                    {
                        $array_phrases[] = (array)$obj;
                    }
                }
            }
 
            require_once(SCRIPT_PATH . 'smarty/Smarty.class.php');
 
            $smarty = new Smarty();
            $smarty->template_dir = TEMPLATE_PATH . $config['template'] . '/';
            $smarty->compile_dir = TEMPLATE_PATH . $config['template'] . '/templates_c/';
            $smarty->config_dir = TEMPLATE_PATH . '_configs/';
            $smarty->cache_dir = TEMPLATE_PATH . '_cache/';
 
            $smarty->assign("home",$config['acpanel'].'.php');
 
            $smarty->assign("array_phrases",$array_phrases);
            if(isset($error)) $smarty->assign("iserror",$error);
 
            $smarty->registerFilter("output","translate_template");
            $smarty->display('p_general_phrase_search_load.tpl');
 
            break;
 
        default:
 
            die("Hacking Attempt");
    }
}
 
?>
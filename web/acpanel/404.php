<?php

if (!defined("IN_ACP")) die ("Hacking attempt!");

header("HTTP/1.0 404 Not Found");

$smarty->assign("home",$config['acpanel'].'.php');
$smarty->assign("site_name",$config['site_name']);
$smarty->registerFilter("output","translate_template");
$smarty->display('404.tpl');

exit;

?>
<?php

header('Content-type: text/html; charset='.$config['charset']);

include_once(INCLUDE_PATH . 'functions.servers.php');

$smarty->assign("gtypes",server_protocol_list());
$smarty->assign("uid",$userinfo['uid']);
$smarty->registerFilter("output","translate_template");
$smarty->display('p_servers_control_add.tpl');

exit;

?>
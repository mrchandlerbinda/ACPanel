<?php

header('Content-type: text/html; charset='.$config['charset']);

ob_start();

echo phpinfo();

$buffer = ob_get_contents();
preg_match('#<div class="center">(.+)</div>#sU', $buffer, $bMatches);
$result = $bMatches[1];

ob_end_clean();

$smarty->assign("result", $result);
$smarty->registerFilter("output","translate_template");
$smarty->display('optimization/php_settings.tpl');

exit;

?>
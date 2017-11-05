<?php

	header('Content-type: text/html; charset='.$config['charset']);

	clearCacheFolder(TEMPLATE_PATH . "_cache");
	$result = true;

	$smarty->assign("result", $result);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('optimization/clear_cache.tpl');

	exit;

?>
<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_hm_patterns_add.tpl');

	exit;

?>
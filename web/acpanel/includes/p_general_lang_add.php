<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_general_lang_add.tpl');

	exit;

?>
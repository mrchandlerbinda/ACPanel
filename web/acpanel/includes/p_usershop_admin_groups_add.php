<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$smarty->registerFilter("output","translate_template");
	$smarty->display('p_usershop_admin_groups_add.tpl');

	exit;

?>
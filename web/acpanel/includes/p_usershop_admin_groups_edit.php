<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$gid = trim($_GET['s']);
	if( !is_numeric($gid) ) die("Hacking Attempt");

	$arguments = array('gid'=>$gid);
	$result = $db->Query("SELECT * FROM `acp_payment_groups` WHERE gid = '{gid}'", $arguments, $config['sql_debug']);
	if( is_array($result) )
	{
		foreach( $result as $obj )
		{
			$array_group = (array)$obj;
		}
	}

	$smarty->assign("group_edit", $array_group);

	$smarty->registerFilter("output", "translate_template");
	$smarty->display('p_usershop_admin_groups_edit.tpl');

	exit;

?>
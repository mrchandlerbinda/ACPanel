<?php

	function getMinutes()
	{
		$out = array();

		for( $i=0; $i<60; $i++ )
		{
			$out[] = (strlen($i) > 1) ? $i : "0".$i;
		}

		return $out;
	}

	function getHours()
	{
		$out = array();

		for( $i=0; $i<24; $i++ )
		{
			$out[] = (strlen($i) > 1) ? $i : "0".$i;
		}

		return $out;
	}

	function getDays()
	{
		$out = array();

		for( $i=1; $i<32; $i++ )
		{
			$out[] = (strlen($i) > 1) ? $i : "0".$i;
		}

		return $out;
	}

	function getMonths()
	{
		$out = array();

		for( $i=1; $i<13; $i++ )
		{
			$out[] = (strlen($i) > 1) ? $i : "0".$i;
		}

		return $out;
	}

	header('Content-type: text/html; charset='.$config['charset']);

	$task_id = trim($_GET['id']);
	if( !is_numeric($task_id) ) die("Hacking Attempt");

	$arguments = array('task_id'=>$task_id);
	$result = $db->Query("SELECT * FROM `acp_cron_entry` WHERE entry_id = '{task_id}'", $arguments, $config['sql_debug']);
	if( is_array($result) )
	{
		foreach ($result as $obj)
		{
			$run_rules = unserialize($obj->run_rules);
			$obj->minutes = $run_rules[1];
			$obj->hours = $run_rules[2];
			$obj->days = $run_rules[3];
			$obj->months = $run_rules[4];

			$array_task = (array)$obj;
		}
	}

	$smarty->assign("task_edit",$array_task);

	$smarty->assign("minutes", getMinutes());
	$smarty->assign("hours", getHours());
	$smarty->assign("days", getDays());
	$smarty->assign("months", getMonths());

	$smarty->registerFilter("output","translate_template");
	$smarty->display('task_sheduler_edit.tpl');

	exit;
?>
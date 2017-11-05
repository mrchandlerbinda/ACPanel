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

	$smarty->assign("minutes", getMinutes());
	$smarty->assign("hours", getHours());
	$smarty->assign("days", getDays());
	$smarty->assign("months", getMonths());

	$smarty->registerFilter("output","translate_template");
	$smarty->display('task_sheduler_add.tpl');

	exit;

?>
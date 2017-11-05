<?php

	header('Content-type: text/html; charset='.$config['charset']);

	$result_node = $db->Query("SELECT * FROM `acp_category` WHERE catlevel = 0 ORDER BY display_order", array(), $config['sql_debug']);

	unset($result);
	$result['1']['title'] = '@@catright_more_catleft@@';
	$result['2']['title'] = '@@min_catleft@@';
	$result['3']['title'] = '@@max_catright@@';
	$result['4']['title'] = '@@between_key@@';
	$result['5']['title'] = '@@even_odd@@';
	$result['6']['title'] = '@@uniq_keys@@';

	if (is_array($result_node))
	{
		foreach ($result_node as $obj)
		{			// rule 1			$arguments = array('category'=>$obj->categoryid);
			$test = $db->Query("SELECT * FROM `acp_category` WHERE catleft >= catright AND (sectionid = '{category}' OR categoryid = '{category}')", $arguments, $config['sql_debug']);
			if (is_array($test))
			{
				foreach ($test as $obj_test)
				{					$result['1']['result'][] = "section ID: ".$obj->categoryid.", incorrect category ID: ".$obj_test->categoryid;
				}
			}

			// rule 2, 3
			$test = $db->Query("SELECT COUNT(categoryid) AS cnt, MIN(catleft) AS min, MAX(catright) AS max FROM `acp_category` WHERE sectionid = '{category}' OR categoryid = '{category}'", $arguments, $config['sql_debug']);
			if (is_array($test))
			{
				foreach ($test as $obj_test)
				{
					if( $obj_test->min != 1 )
					{						$result['2']['result'][] = "section ID: ".$obj->categoryid.", minimum left key: ".$obj_test->min;					}
					if( $obj_test->max != 2*$obj_test->cnt )
					{						$result['3']['result'][] = "section ID: ".$obj->categoryid.", maximum right key (".$obj_test->max.") <> 2 * count categories (".$obj_test->cnt.")";
					}
				}
			}

			// rule 4
			$test = $db->Query("SELECT categoryid, MOD((catright - catleft),2) FROM `acp_category` WHERE MOD((catright - catleft),2) = 0 AND (sectionid = '{category}' OR categoryid = '{category}')", $arguments, $config['sql_debug']);
			if (is_array($test))
			{
				foreach ($test as $obj_test)
				{
  					$result['4']['result'][] = "section ID: ".$obj->categoryid.", incorrect category ID: ".$obj_test->categoryid;
				}
			}

			// rule 5
			$test = $db->Query("SELECT categoryid, MOD((catleft - catlevel + 1),2) FROM `acp_category` WHERE MOD((catleft - catlevel + 1),2) = 1 AND (sectionid = '{category}' OR categoryid = '{category}')", $arguments, $config['sql_debug']);
			if (is_array($test))
			{
				foreach ($test as $obj_test)
				{
  					$result['5']['result'][] = "section ID: ".$obj->categoryid.", incorrect category ID: ".$obj_test->categoryid;
				}
			}

			// rule 6
			$test = $db->Query("SELECT t1.categoryid, COUNT(t1.categoryid) AS rep, MAX(t3.catright) AS max_right
				FROM `acp_category` AS t1, `acp_category` AS t2, `acp_category` AS t3
				WHERE t1.catleft <> t2.catleft AND t1.catleft <> t2.catright AND t1.catright <> t2.catleft AND t1.catright <> t2.catright
				AND (t1.categoryid = {category} OR t1.sectionid = {category}) AND (t2.categoryid = {category} OR t2.sectionid = {category}) AND (t3.categoryid = {category} OR t3.sectionid = {category})
				GROUP BY t1.categoryid HAVING max_right <> SQRT(4 * rep + 1) + 1", $arguments, $config['sql_debug']);

			if (is_array($test))
			{
				foreach ($test as $obj_test)
				{
  					$result['6']['result'][] = "section ID: ".$obj->categoryid.", incorrect category ID: ".$obj_test->categoryid;
				}
			}
		}
	}
	else
	{
		$error = '@@error_category_node@@';
	}

	$smarty->assign("result", $result);
	$smarty->registerFilter("output","translate_template");
	$smarty->display('optimization/check_category_tree.tpl');

	exit;

?>
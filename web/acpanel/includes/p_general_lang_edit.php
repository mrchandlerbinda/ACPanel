<?php

	if( isset($_GET['xml']) || isset($_GET['s']) )
	{
		if( isset($_GET['xml']) && is_numeric($_GET['xml']) )
		{
			$id = $_GET['xml'];
			$select = $db->Query("SELECT * FROM `acp_lang` WHERE lang_id = ".$id." LIMIT 1", array(), $config['sql_debug']);
	
			if( is_array($select) )
			{
				foreach( $select as $obj )
				{
					$lang_code = $obj->lang_code;
					$lang_name = $obj->lang_title;
				}

				$sqlcond = "";
				if( isset($_GET['pr']) && $_GET['pr'] )
					$sqlcond .= " AND a.productid = '".mysql_real_escape_string($_GET['pr'])."'";

				$select = $db->Query("SELECT a.lw_word, a.".$lang_code.", a.lw_page, a.productid AS word_product, 
					IF(a.lw_page = 0, NULL, b.lp_name) AS tpl_name, IF(a.lw_page = 0, NULL, b.productid) AS page_product 
					FROM `acp_lang_words` a, `acp_lang_pages` b 
					WHERE (a.lw_page = b.lp_id OR a.lw_page = 0)".$sqlcond." 
					GROUP BY lw_word, lw_page ORDER BY lw_page
				", array(), $config['sql_debug']);
	
				if( is_array($select) )
				{						
					foreach( $select as $obj )
					{
						if( !isset($lang[$obj->lw_page]['template']) ) $lang[$obj->lw_page]['template'] = $obj->tpl_name;
						if( !isset($lang[$obj->lw_page]['product']) ) $lang[$obj->lw_page]['product'] = $obj->page_product;
						$lang[$obj->lw_page]['phrases'][] = array('product' => $obj->word_product, 'name' => $obj->lw_word, 'data' => $obj->$lang_code);
					}
	
					$rootXML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><languages></languages>');
					$parentXML = $rootXML->addChild('phrases');	
					$parentXML->addAttribute('lang', $lang_code);
					$parentXML->addAttribute('name', $lang_name);
					
					foreach($lang as $v)
					{
						$childXML = $parentXML->addChild('phrasetype');	
						if( is_null($v['template']) ) $v['template'] = "GLOBAL";
						else $childXML->addAttribute('product', $v['product']);
						$childXML->addAttribute('template', $v['template']);
					
						foreach($v['phrases'] as $phrase)
						{
							$phraseXML = $childXML->addChild('phrase', $phrase['data']);
							$phraseXML->addAttribute('name', $phrase['name']);
							$phraseXML->addAttribute('product', $phrase['product']);
						}
					}					
					
					header('Content-type: application/xml; charset=UTF-8');
					header('Content-Disposition: attachment; filename="'.$lang_code.'Phrases.xml"');

					$dom = new DOMDocument('1.0', 'UTF-8');
					$dom->preserveWhiteSpace = false;
					$dom->formatOutput = true;
					$dom->loadXML($rootXML->asXML());
					echo $dom->saveXML();

					exit;
				}
			}
		}
		elseif( isset($_GET['s']) && is_numeric($_GET['s']) )
		{
			header('Content-type: text/html; charset='.$config['charset']);
		
			$lang_id = trim($_GET['s']);
			if( !is_numeric($lang_id) ) die("Hacking Attempt");
		
			$arguments = array('lang_id'=>$lang_id);
			$result = $db->Query("SELECT * FROM `acp_lang` WHERE lang_id = '{lang_id}'", $arguments, $config['sql_debug']);
			if( is_array($result) )
			{
				foreach ($result as $obj)
				{
					$array_lang = (array)$obj;
				}
			}
		
			$smarty->assign("lang_edit",$array_lang);
		
			$smarty->registerFilter("output","translate_template");
			$smarty->display('p_general_lang_edit.tpl');
		
			exit;
		}
	}

	die("Hacking Attempt");

?>
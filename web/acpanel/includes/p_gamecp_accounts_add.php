<?php

	if( $config['default_access'] == "" )
	{
		$error = "@@default_mask_not_set@@";
	}
	else
	{
		$result_mask = $db->Query("SELECT a.mask_id, a.access_flags, IF(MIN(b.server_id) > 0, COUNT(b.server_id), 0) AS servers FROM `acp_access_mask` a 
			LEFT JOIN `acp_access_mask_servers` b ON a.mask_id = b.mask_id GROUP BY a.mask_id", array(), $config['sql_debug']);
		
		if( is_array($result_mask) )
		{
			foreach( $result_mask as $obj )
			{
				if( $obj->servers == 0 ) $obj->servers = '@@ga_all_servers@@';
				$array_mask[$obj->mask_id] = array('flags'=>$obj->access_flags, 'servers'=>$obj->servers);
			}
	
			$result_cats = $db->Query("SELECT categoryid, sectionid FROM `acp_category` WHERE link = 'p_users'", array(), $config['sql_debug']);
			
			if( is_array($result_cats) )
			{
				foreach ($result_cats as $obj)
				{
					$smarty->assign("cat_users", $obj->sectionid);
					$smarty->assign("cat_user_edit", $obj->categoryid);
				}
			}
		
			unset($cat_accounts_list);
			foreach( $all_categories as $key => $value )
			{
				$search_acc_list = array_search("p_gamecp_accounts", $value);
				if( $search_acc_list )
				{
					$cat_accounts_list = $key;
					break;
				}
			}
	
			$uname = ( isset($_GET['u']) ) ? $_GET['u'] : "";
			$smarty->assign("username",$uname);
			$def_time = (!$config['default_access_time']) ? "" :  get_datetime((time() + ($config['default_access_time']*3600)), 'd-m-Y, H:i');
			$smarty->assign("default_mask",array('mask' => $config['default_access'], 'expired' => $def_time));
		}
		else
		{
			$error = "@@mask_not_found@@";
		}
	}

	$action_uri = $config['acpanel'].".php?cat=".$_GET['cat']."&do=".$cat_accounts_list;

	$smarty->assign("action_uri", $action_uri);
	$smarty->assign("head_title","@@add_account@@");

	$headinclude = "
		<link href='acpanel/templates/".$config['template']."/css/date_input.css' rel='stylesheet' type='text/css' />
	";

	if(isset($error)) $smarty->assign("iserror",$error);
	if(isset($array_mask)) $smarty->assign("array_masks",$array_mask);

?>
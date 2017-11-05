<?php

if (isset($_FILES['userfile']) && is_uploaded_file($_FILES['userfile']['tmp_name']))
{
	// ###############################################################################
	// DEFINE CONSTANT
	// ###############################################################################

	define("IN_ACP", true);
	define('ROOT_PATH', './');
	define('SCRIPT_PATH', ROOT_PATH . 'scripts/');
	define('INCLUDE_PATH', ROOT_PATH . 'includes/');
	define('TEMPLATE_PATH', ROOT_PATH . 'templates/');

	// ###############################################################################
	// LOAD GENERAL OPTIONS
	// ###############################################################################

	unset($config); // for security
	require(INCLUDE_PATH . '_cfg.php');

	require_once(INCLUDE_PATH . 'class.mysql.php');

	try {
		$db  = new MySQL($config['hostname'],$config['username'],$config['password'],$config['dbname'],$config['charset_db']);
	} catch (Exception $e) {
		die($e->getMessage());
	}

	$array_cfg = $db->Query("SELECT varname, value FROM `acp_config` WHERE varname IS NOT NULL", array(), true);

	if(is_array($array_cfg))
	{
		foreach ($array_cfg as $obj)
		{
			$config[$obj->varname] = $obj->value;
		}
		$config['user_action_log'] = strlen($config['user_action_log']) ? explode(',', $config['user_action_log']) : array();
	}

	$config['file_types'] = strlen($config['file_types']) ? explode(',', $config['file_types']) : array();

	include(INCLUDE_PATH . 'functions.main.php');
	$langs = create_lang_list();
	unset($translate);

	switch($_POST['type'])
	{
		case "avatar":

			$filter = "lp_name='profile.tpl' AND lp_id = lw_page";
			$arguments = array('lang'=>get_language(1));
			$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
			if(is_array($tr_result)) {
				foreach ($tr_result as $obj){
					$translate[$obj->lw_word] = $obj->lw_translate;
				}
			}

			header('Content-type: text/html; charset='.$config['charset']);

			$filename = $_FILES['userfile']['tmp_name'];
			$ext = substr($_FILES['userfile']['name'], 1 + strrpos($_FILES['userfile']['name'], "."));

			if (filesize($filename) > $config['file_size'])
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_filesize'].'&nbsp;'.$config['file_size'].'</span>';
			}
			elseif (!in_array($ext, $config['file_types']))
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_filetype'].'</span>';
			}
			else
			{
				if( isset($config['ext_auth_type']) )
				{
					if( $config['ext_auth_type'] == "xf" && isset($config['xfAuth']) )
					{
						require_once(INCLUDE_PATH . 'class.xfAuth.php');
						$xf = new XF_auth($config['xfAuth']);
						$xf->uploadAvatar();
						$avatar_url = $xf->getAvatarFilePath("m");
						print $avatar_url;
						break;
					}
				}

				$error = false;
				$uid = (string)$_POST['uid'];
				$arrImageTypes = array('m','s');
				$load_url = ROOT_PATH . 'images/avatars/';

				for($i=0; $i<strlen($uid); $i++)
				{
					$arrNum[] = $uid[$i];
				}

				if(count($arrNum) < 2)
				{
					array_unshift($arrNum, 0);
				}

				$avatar_name = $uid.'.jpg';
				$avatar_path = implode('/',$arrNum).'/'.$avatar_name;

				foreach($arrImageTypes as $t)
				{
					$new_url = $load_url.$t;

					foreach($arrNum as $v)
					{
						$new_url = $new_url.'/'.$v;

						if (!is_dir($new_url))
						{
							mkdir($new_url);
						}
					}

					$new_url = $new_url.'/';
					$uploadfile = $new_url.$avatar_name;
					if (file_exists($uploadfile)) unlink($uploadfile);

					if ($t == 'm')
					{
						$resize = img_resize($filename, $uploadfile, $config['avatar_width'], $config['avatar_height']);

						switch($resize) {
							case "-1":
								$error = true;
								print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_fileincorrect'].'</span>';
								break;

							case "-2":
								$error = true;
								print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_fileresize'].'</span>';
								break;

							default:
								print 'acpanel/images/avatars/'.$t.'/'.$avatar_path;
						}
					}
					else
					{
						$resize = img_resize($filename, $uploadfile, $config['avatar_thumb_width'], $config['avatar_thumb_height']);
					}

					if ($error) break;
				}

				if (!$error)
				{
					$arguments = array('id'=>$_POST['uid'],'url'=>$avatar_path.'?'.time());
					$result = $db->Query("UPDATE `acp_users` SET avatar = '{url}' WHERE uid = '{id}'", $arguments, $config['sql_debug']);
				}
			}

			break;

		case "img":

			$filter = "lp_name='p_servers_control.tpl' AND lp_id = lw_page";
			$arguments = array('lang'=>get_language(1));
			$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
			if(is_array($tr_result)) {
				foreach ($tr_result as $obj){
					$translate[$obj->lw_word] = $obj->lw_translate;
				}
			}

			include(INCLUDE_PATH . 'functions.servers.php');

			header('Content-type: text/html; charset='.$config['charset']);

			$filename = $_FILES['userfile']['tmp_name'];
			$uploaddir = ROOT_PATH . 'images/maps/' . $_POST['gtype'] . '/';
			$map = ($_POST['map'] == "-") ? basename($_FILES['userfile']['name']) : $_POST['map'].".jpg";
			$uploadfile = $uploaddir.$map;
			$ext = substr($_FILES['userfile']['name'], 1 + strrpos($_FILES['userfile']['name'], "."));

			if (filesize($filename) > $config['file_size'])
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_filesize'].'&nbsp;'.$config['file_size'].'</span>';
			}
			elseif (!in_array($ext, $config['file_types']))
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_filetype'].'</span>';
			}
			else
			{
				$resize = img_resize($filename, $uploadfile, $config['file_width'], $config['file_height']);

				switch($resize) {
					case "-1":
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_fileincorrect'].'</span>';
						break;

					case "-2":
						print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$translate['error_fileresize'].'</span>';
						break;

					default:
						print 'acpanel/images/maps/'.$_POST['gtype'].'/'.$map;
				}
			}

			break;

		case "xml":

			$filter = "lp_name='p_products.tpl' AND lp_id = lw_page";
			$arguments = array('lang'=>get_language(1));
			$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
			if(is_array($tr_result)) {
				foreach ($tr_result as $obj){
					$translate[$obj->lw_word] = $obj->lw_translate;
				}
			}

			header('Content-type: text/html; charset='.$config['charset']);

			$filename = $_FILES['userfile']['tmp_name'];
			$ext = substr($_FILES['userfile']['name'], 1 + strrpos($_FILES['userfile']['name'], "."));

			if (filesize($filename) > $config['file_size'])
			{
				$error = $translate['error_filesize'].'&nbsp;'.$config['file_size'];
			}
			elseif ($ext != "xml")
			{
				$error = $translate['error_filetype'];
			}
			else
			{
				libxml_use_internal_errors(true);
				$xml = simplexml_load_file($_FILES['userfile']['tmp_name']);
				if(!$xml)
				{
					$error = "Failed loading XML:";
					foreach(libxml_get_errors() as $err)
					{
						$error .= "<br />&raquo;&raquo;&raquo;&nbsp;".$err->message;
					}
				}
				else
				{
					if($prd_id = (string)$xml['productid'])
					{
						$prd_active = ((string)$xml['active']) ? 1 : 0;

						foreach($xml as $key => $value)
						{
							switch((string)$key)
							{
								case "title":

									$prd_title = (string)$value;
									break;

								case "description":

									$prd_desc = (string)$value;
									break;

								case "version":

									$prd_version = (string)$value;
									break;

								case "url":

									$prd_url = (string)$value;
									break;

								case "languages":

									$langsObj = $value;
									break;
							}
						}

						if( !$prd_title )
						{
							$error = 'product name missing';
						}
						elseif( !$prd_version )
						{
							$error = 'product version missing';
						}
						else
						{
							$filepath = ROOT_PATH . "plugins/". $prd_id . ".php";
							if( file_exists($filepath) )
							{
								$product_install = true;
								include($filepath);

								if( empty($error) )
								{
									$arguments = array('productid'=>$prd_id,'active'=>$prd_active,'title'=>$prd_title,'description'=>$prd_desc,'version'=>$prd_version,'url'=>$prd_url);
									$result_update = $db->Query("INSERT INTO `acp_products` (productid, active, title, description, version, url)
										VALUES ('{productid}', '{active}', '{title}', '{description}', '{version}', '{url}')
										ON DUPLICATE KEY UPDATE active = '{active}', title = '{title}', description = '{description}', version = '{version}', url = '{url}'", $arguments, $config['sql_debug']);

									if( isset($langsObj) )
									{
										$i = 0;
										$i_error = 0;
				
										foreach( $langsObj as $phrases )
										{
											if( !isset($phrases['lang']) || !isset($phrases['name']) )
											{
												$error = 'language code or language name not found';
												break;
											}
				
											$lang_code = (string)$phrases['lang'];
											if( !preg_match("/[a-z\_]{2,}/", $lang_code) )
											{
												$error = 'language code missing';
												break;
											}
				
											$lang_name = ($config['charset'] != 'utf-8') ? iconv('utf-8', $config['charset'], (string)$phrases['name']) : (string)$phrases['name'];
											if( !$lang_name )
											{
												$error = 'language name missing';
												break;
											}
				
											$args = array('code' => $lang_code, 'name' => $lang_name);
											$check_lang = $db->Query("SELECT lang_id FROM `acp_lang` WHERE lang_code = '{code}'", $args, $config['sql_debug']);
											if( $check_lang )
											{
												$update_lang = $db->Query("UPDATE `acp_lang` SET lang_code = '{code}', lang_title = '{name}' WHERE lang_id = ".$check_lang, $args, $config['sql_debug']);
											}
											else
											{
												if( $update_lang = $db->Query("INSERT INTO `acp_lang` (lang_title, lang_code, lang_active) VALUES ('{name}', '{code}', '1')", $args, $config['sql_debug']) )
													$update_lang = $db->Query("ALTER TABLE `acp_lang_words` ADD {code} TEXT", $args, $config['sql_debug']);
											}
				
											if( !$update_lang )
											{
												$error = 'language update failed';
												break;
											}
											else
											{
												foreach( $phrases as $phrasetype )
												{
													$page_name = strtolower($phrasetype['template']);
													if( !preg_match("/[a-z0-9\_\.\/]{6,}/", $page_name) )
													{
														$error = 'template code missing '.$page_name;
														break;
													}
													$page_product = (isset($phrasetype['product'])) ? $phrasetype['product'] : NULL;
				
													if( $page_name == "global" )
														$page_id = 0;
													else
													{
														$args = array('lp_name' => $page_name, 'productid' => $page_product);
														$page_id = $db->Query("SELECT lp_id FROM `acp_lang_pages` WHERE lp_name = '{lp_name}' LIMIT 1", $args, $config['sql_debug']);
														if( !$page_id )
														{
															if( !($page_create = $db->Query("INSERT INTO `acp_lang_pages` (lp_name, productid) VALUES ('{lp_name}','{productid}')", $args, $config['sql_debug'])) )
															{
																$error = 'template create failed';
																break;
															}
															$page_id = $db->LastInsertID();
														}
													}
				
													foreach( $phrasetype as $phrase_value )
													{
														$ph = ($config['charset'] != 'utf-8') ? iconv('utf-8', $config['charset'], (string)$phrase_value) : (string)$phrase_value;
														$ph = html_entity_decode($ph);
														
														if( !phrase_add($lang_code, $page_id, (string)$phrase_value['name'], $ph, (string)$phrase_value['product']) )
														{
															$i_error++;
														}
					
														$i++;
													}
												}
											}
										}
				
										if( $i_error )
										{
											$error = 'failed to add the phrase: '.$i_error.' out of '.$i;
										}
									}
								}
								else
								{
									if(count($error) > 1)
									{
										$error = '<br />&raquo;&raquo;&raquo;&nbsp;'.implode("<br />&raquo;&raquo;&raquo;&nbsp;", $error);
									}
									else
									{
										$error = $error[0];
									}
								}
							}
							else
							{
								$error = 'an installer can not be found';
							}
						}
					}
					else
					{
						$error = 'productid is not specified in xml file';
					}
				}

				if (isset($error)) $error = 'Install error:&nbsp;'.$error;
			}

			if (!isset($error))
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['install_success'].'</span>';
			}
			else
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$error.'</span>';
			}

			break;

		case "lang":

			$filter = "lp_name='p_general_lang.tpl' AND lp_id = lw_page";
			$arguments = array('lang'=>get_language(1));
			$tr_result = $db->Query("SELECT lw_word, {lang} AS lw_translate FROM `acp_lang_words`, `acp_lang_pages` WHERE ".$filter, $arguments, $config['sql_debug']);
			if(is_array($tr_result)) {
				foreach ($tr_result as $obj){
					$translate[$obj->lw_word] = $obj->lw_translate;
				}
			}

			header('Content-type: text/html; charset='.$config['charset']);

			$filename = $_FILES['userfile']['tmp_name'];
			$ext = substr($_FILES['userfile']['name'], 1 + strrpos($_FILES['userfile']['name'], "."));

			if( filesize($filename) > $config['file_size'] )
			{
				$error = $translate['error_filesize'].'&nbsp;'.$config['file_size'];
			}
			elseif( $ext != "xml" )
			{
				$error = $translate['error_filetype'];
			}
			else
			{
				libxml_use_internal_errors(true);
				$xml = simplexml_load_file($_FILES['userfile']['tmp_name']);
				if( !$xml )
				{
					$error = "Failed loading XML:";
					foreach(libxml_get_errors() as $err)
					{
						$error .= "<br />&raquo;&raquo;&raquo;&nbsp;".$err->message;
					}
				}
				else
				{
					if( $xml->getName() == 'languages' )
					{
						$i = 0;
						$i_error = 0;

						foreach( $xml as $phrases )
						{
							if( !isset($phrases['lang']) || !isset($phrases['name']) )
							{
								$error = 'language code or language name not found';
								break;
							}

							$lang_code = (string)$phrases['lang'];
							if( !preg_match("/[a-z\_]{2,}/", $lang_code) )
							{
								$error = 'language code missing';
								break;
							}

							$lang_name = ($config['charset'] != 'utf-8') ? iconv('utf-8', $config['charset'], (string)$phrases['name']) : (string)$phrases['name'];
							if( !$lang_name )
							{
								$error = 'language name missing';
								break;
							}

							$args = array('code' => $lang_code, 'name' => $lang_name);
							$check_lang = $db->Query("SELECT lang_id FROM `acp_lang` WHERE lang_code = '{code}'", $args, $config['sql_debug']);
							if( $check_lang )
							{
								$update_lang = $db->Query("UPDATE `acp_lang` SET lang_code = '{code}', lang_title = '{name}' WHERE lang_id = ".$check_lang, $args, $config['sql_debug']);
							}
							else
							{
								if( $update_lang = $db->Query("INSERT INTO `acp_lang` (lang_title, lang_code, lang_active) VALUES ('{name}', '{code}', '1')", $args, $config['sql_debug']) )
									$update_lang = $db->Query("ALTER TABLE `acp_lang_words` ADD {code} TEXT", $args, $config['sql_debug']);
							}

							if( !$update_lang )
							{
								$error = 'language update failed';
								break;
							}
							else
							{
								foreach( $phrases as $phrasetype )
								{
									$page_name = strtolower($phrasetype['template']);
									if( !preg_match("/[a-z0-9\_\.\/]{6,}/", $page_name) )
									{
										$error = 'template code missing '.$page_name;
										break;
									}
									$page_product = (isset($phrasetype['product'])) ? $phrasetype['product'] : NULL;

									if( $page_name == "global" )
										$page_id = 0;
									else
									{
										$args = array('lp_name' => $page_name, 'productid' => $page_product);
										$page_id = $db->Query("SELECT lp_id FROM `acp_lang_pages` WHERE lp_name = '{lp_name}' LIMIT 1", $args, $config['sql_debug']);
										if( !$page_id )
										{
											if( !($page_create = $db->Query("INSERT INTO `acp_lang_pages` (lp_name, productid) VALUES ('{lp_name}','{productid}')", $args, $config['sql_debug'])) )
											{
												$error = 'template create failed';
												break;
											}
											$page_id = $db->LastInsertID();
										}
									}

									foreach( $phrasetype as $phrase_value )
									{
										$ph = ($config['charset'] != 'utf-8') ? iconv('utf-8', $config['charset'], (string)$phrase_value) : (string)$phrase_value;
										$ph = html_entity_decode($ph);
	
										if( !phrase_add($lang_code, $page_id, (string)$phrase_value['name'], $ph, (string)$phrase_value['product']) )
										{
											$i_error++;
										}
	
										$i++;
									}
								}
							}
						}

						if( $i_error )
						{
							$error = 'failed to add the phrase: '.$i_error.' out of '.$i;
						}
					}
					else
					{
						$error = 'incorrect file structure XML';
					}

					if( isset($error) ) $error = 'Upload error:&nbsp;'.$error;
				}
			}

			if (!isset($error))
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/success.gif" alt=""><span id="success" class="indent">'.$translate['upload_success'].'</span>';
			}
			else
			{
				print '<img style="vertical-align:middle;" src="acpanel/templates/'.$config['template'].'/images/error.gif" alt=""><span id="error" class="indent">'.$error.'</span>';
			}

			break;

		default:

			die("Hacking Attempt");
	}
}
else
{
	die("Hacking Attempt");
}

?>
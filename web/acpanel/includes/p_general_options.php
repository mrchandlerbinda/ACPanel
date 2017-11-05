<?php

$result = $db->Query("SELECT section, label, options FROM `acp_config` WHERE varname IS NULL", array(), $config['sql_debug']);

if( is_array($result) )
{
	foreach ($result as $obj)
	{		$general_sections[] = (array)$obj;
	}
}

if( !isset($_GET['s']) || !in_multi_array($_GET['s'], $general_sections) )
{
	header('Location: '.$config['acpanel'].'.php?cat='.$current_section_id.'&do='.$_GET["do"].'&s=main');
	exit;
}

$s = $_GET['s'];

usort($general_sections, "cmp");

$arguments = array('section'=>$s);
$result = $db->Query("SELECT * FROM `acp_config` WHERE section = '{section}' AND varname IS NOT NULL", $arguments, $config['sql_debug']);

if( is_array($result) )
{
	foreach ($result as $obj)
	{
		switch($obj->type)
		{
			case "textarea":
				$general_editable = "
					<textarea name='{$obj->varname}' rows='5' cols='30' />{$obj->value}</textarea>
				";
				break;
			case "checkbox":
				$box = explode("\n", $obj->options);
				$general_editable = "";
				foreach($box as $b) {
					$box_value = explode("|", $b);
					$var_array = (is_array($config[$obj->varname])) ? $config[$obj->varname] : explode(",", $config[$obj->varname]);
					$general_editable .= "
						<input type='hidden' name='{$obj->varname}[]' value='' />
						<input class='checkbox' type='checkbox' name='{$obj->varname}[]' value='{$box_value[0]}' ".((in_array($box_value[0], $var_array)) ? "checked=\"checked\"" : "" )." /> ".$box_value[1]."<br />
					";
				}
				break;
			case "select":
				$general_editable = "
					<select name='{$obj->varname}".( ($obj->verifycodes == 'multiple') ? "[]" : "" )."'".( ($obj->verifycodes == 'multiple') ? " multiple='multiple'" : " class='styled'" ).">
				";
				if( $obj->verifycodes == 'multiple' ||  $obj->verifycodes == 'select' )
				{
					$box_value = explode("|", $obj->options);
					$arguments = array('table'=>$box_value[0],'field1'=>$box_value[1],'field2'=>$box_value[2]);
					$res = $db->Query("SELECT {field1} AS one_field, {field2} AS two_field FROM `{table}`", $arguments, $config['sql_debug']);
					if( is_array($res) )
					{
						$var_array = ( $config[$obj->varname] ) ? explode(",", $config[$obj->varname]) : array();
						if( empty($var_array) && $obj->verifycodes == 'select' )
						{
							$general_editable .= "
									<option value='' selected='selected'>@@select_option@@</option>
							";								
						}

						foreach ($res as $obj_new)
						{
							$general_editable .= "
									<option value='{$obj_new->one_field}'".((in_array($obj_new->one_field, $var_array)) ? " selected='selected'" : "" ).">".((count($box_value) > 3) ? '#'.$obj_new->one_field.': '.$obj_new->two_field : $obj_new->two_field)."</option>
							";
						}
					}
				}
				else
				{
					$box = explode("\n", $obj->options);
					foreach($box as $b) {
						$box_value = explode("|", $b);
						$general_editable .= "
								<option value='{$box_value[0]}' ".(($config[$obj->varname] == $box_value[0]) ? "selected" : "" ).">".$box_value[1]."</option>
						";
					}
				}

				$general_editable .= "
					</select>
				";
				break;
			case "boolean":
				$general_editable = "
					<input class='radio' type='radio' name='{$obj->varname}' value='1' ".(($config[$obj->varname] == 1) ? "checked=\"checked\"" : "" )." /> @@yes@@&nbsp;
					<input class='radio' type='radio' name='{$obj->varname}' value='0' ".(($config[$obj->varname] == 0) ? "checked=\"checked\"" : "" )." /> @@no@@
				";
				break;
			default:
				$general_editable = "
					<input class='text small' type='text' name='{$obj->varname}' value='{$obj->value}' size='30' />
				";
		}

		$general_options[] = array('id' => $obj->id, 'description' => $obj->label, 'content' => $general_editable, 'help' => $obj->help);
	}
}
else
{
	$error = "@@empty_table@@";
}

$smarty->assign("general_sections",$general_sections);
if(isset($general_options)) $smarty->assign("general_options",$general_options);

$headinclude = "
	<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$('a[rel*=facebox]').facebox()
			});
		})(jQuery);

		jQuery(document).ready(function($) {
			$('#forma-select select').change(function () {
				window.location.href = '".$config['acpanel'].".php?cat=".$current_section_id."&do=".$_GET['do']."&s=' + $('option:selected', this).val();
			});

			$('#forma-options').submit(function() {
				var str = $('#forma-options').serialize();
				$('select', this).each(function() {
					if($(this).val() == null) str += '&' + $(this).attr('name') + '=';
				});
				
				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_general_options',
					data:str,
					success:function(result) {
						if( result.indexOf('id=\"success\"') + 1)
						{
							$('.accessMessage').html('');
							humanMsg.displayMsg(result,'success');
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}
					}
				});

				return false;
			});
		});
	</script>
";

$smarty->assign("get_in",$s);
if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("head_title","@@head_general_options@@");

?>
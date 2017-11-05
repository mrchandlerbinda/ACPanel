<?php

$search_addcat = false;
$cat_edit_id = false;
foreach ($all_categories as $key => $value)
{
	$search_addcat_id = array_search("p_general_blocks_add", $value);
	$search_edit_id = array_search("p_general_blocks_edit", $value);
	if ($search_addcat_id)
	{
		$smarty->assign("cat_addcat_id", $key);
		$search_addcat = true;
	}
	elseif ($search_edit_id)
	{
		$cat_edit_id = $key;
	}

	if ($search_addcat && $cat_edit_id)
	{
		break;
	}
}

$headinclude = "
	<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$('.block_head a[rel*=facebox]').facebox()
			});
		})(jQuery);

		function resortCategories() {			var edit_id = ".$cat_edit_id.";
			var cat_current = ".$current_section_id.";

			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_blocks',
				data:'go=1&edit_id=' + edit_id + '&cat_current=' + cat_current,
				success:function(result) {
					jQuery('#ajaxContent').html(result);
				}
			});

			return false;
		}

		jQuery(document).ready(function($) {
			resortCategories();
		});
	</script>
";

if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("head_title","@@head_general_blocks@@");

?>
<?php

unset($search_addcat);
foreach ($all_categories as $key => $value)
{
	$search_addcat_id = array_search("p_products_add", $value);
	if ($search_addcat_id)
	{
		$smarty->assign("cat_addcat_id", $key);
		$search_addcat = true;
		break;
	}
}

$headinclude = "
	<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
	<script type='text/javascript' src='acpanel/scripts/js/jquery.ajaxupload.js'></script>
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$('.block_head a[rel*=facebox]').facebox()
			});
		})(jQuery);

		function resortProducts() {
			var cat_current = ".$current_section_id.";

			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_products',
				data:'go=1&cat_current=' + cat_current,
				success:function(result) {
					jQuery('#ajaxContent').html(result);
				}
			});

			return false;
		}

		jQuery(document).ready(function($) {
			resortProducts();
		});
	</script>
";

if(isset($error)) $smarty->assign("iserror",$error);
$smarty->assign("head_title","@@head_products@@");

?>
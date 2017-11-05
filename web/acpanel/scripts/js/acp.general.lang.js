(function ($) {
	$(function () {
		// Check / uncheck all checkboxes
		$('.check_all').click(function() {
			$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
		});
	
		$('.delete a[rel*=facebox]').facebox();
	});
})(jQuery);

function remove_row(subjm,txt)
{
	if (confirm(txt))
	{
		jQuery.ajax({
			type:'POST',
			url:'acpanel/ajax.php?do=ajax_general_lang',
			data:({id : subjm,'go' : 3}),
			success:function(result) {
				if( result.indexOf('id="success"') + 1)
				{
					jQuery('.accessMessage').html('');
					humanMsg.displayMsg(result,'success');
					jQuery('tr#' + subjm).remove();
					rePagination(-1);
					jQuery('table').trigger('update');
					jQuery('table').trigger('applyWidgets', 'zebra');
				}
				else
				{
					humanMsg.displayMsg(result,'error');
				}
			}
		});
	}
	return false;
}

jQuery(document).ready(function($) {
	$('.tablesorter').tablesorter({
		widgets: ['zebra'],
		headers: {0:{sorter: false}}
	});

	$('#forma').submit(function() {
		var len = $("#forma tbody input:checked").length;
		if (len > 0)
		{
			if (confirm($('input:submit',this).val() + '?'))
			{
				var arr = new Array();
				$('#forma tbody input:checked').each( function() {
					arr.push($(this).val());
				});

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_general_lang',
					data:({'marked_word[]' : arr,'go' : 4}),
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							$('.accessMessage').html('');
							humanMsg.displayMsg(result,'success');
							rePagination(-arr.length);
							$('table').trigger('update');
							$('table').trigger('applyWidgets', 'zebra');
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}
					}
				});

				$(this).find('input:checkbox').attr('checked', $(this).is(''));
			}
		} else {
			alert($('input:hidden:last',this).val());
		}

		return false;
	});
});
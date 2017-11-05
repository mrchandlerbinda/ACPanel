(function ($) {
	$(function () {
		// Check / uncheck all checkboxes
		$('.check_all').click(function() {
			$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
		});
	});
})(jQuery);

function remove_row(subjm,txt)
{
	if( confirm(txt) )
	{
		jQuery.ajax({
			type:'POST',
			url:'acpanel/ajax.php?do=ajax_cc_commands',
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

function edit_row(subjm)
{
	if( jQuery('tr td[id] input').length > 0 )
	{
		cancelhandler();
	}

	jQuery('tr#' + subjm + ' td[id]').each( function() {
		var valtext = jQuery(this).text();
		var valid = jQuery(this).attr('id');

		jQuery(this).html(jQuery('<input>')
			.attr('type','text')
			.attr('name',valid)
			.attr('value',valtext)
			.addClass('text small')
		).append(jQuery('<input>')
			.attr('type','hidden')
			.attr('name',valid)
			.attr('value',valtext)
		);
	});

	var $actionstr = jQuery('tr#' + subjm + ' td.delete');

	$actionstr.html(jQuery('<a>')
		.click(function() {
			savehandler();
			return false;
		})
		.attr('href','#')
		.addClass('save')
		.text('Save')
	).append(' | ').append(jQuery('<a>')
		.click(function() {
			cancelhandler();
			return false;
		})
		.attr('href','#')
		.addClass('cancel')
		.text('Cancel')
	);

	return false;
}

function cancelhandler()
{
	jQuery('tr td[id] input:hidden').each( function() {
		var tempval = jQuery(this).val();
		var tempname = jQuery(this).attr('name');

		jQuery(this).parent().attr('id',tempname).html(tempval);
	});

	var $b = jQuery('tr td.delete .cancel');
	var idrow = $b.parents('tr').attr("id");

	$b.parent().html(jQuery('<a>')
		.click(function() {
			return edit_row(idrow);
		})
		.attr('href','#')
		.text('Edit')
	).append(' | ').append(jQuery('<a>')
		.click(function() {
			return remove_row(idrow);
		})
		.attr('href','#')
		.text('Delete')
	);
}

function savehandler()
{
	var str = '';

	jQuery('tr td[id] input:text').each( function() {
		var editval = jQuery(this).val();
		var editname = jQuery(this).attr('name');

		str = str + '&' + editname + '=' + editval;
	});

	var $b = jQuery('tr td.delete .save');
	var idrow = $b.parents('tr').attr("id");

	jQuery.ajax({
		type:'POST',
		url:'acpanel/ajax.php?do=ajax_cc_commands',
		data:'go=5&editid=' + idrow + str,
		success:function(result) {
			if( result.indexOf('id="success"') + 1)
			{
				jQuery('.accessMessage').html('');
				humanMsg.displayMsg(result,'success');
				jQuery('tr td[id] input:text').each( function() {
					jQuery(this).parent().attr('id',jQuery(this).attr('name')).html(jQuery(this).val());
				});
				$b.parent().html(jQuery('<a>')
					.click(function() {
						return edit_row(idrow);
					})
					.attr('href','#')
					.text('Edit')
				).append(' | ').append(jQuery('<a>')
					.click(function() {
						return remove_row(idrow);
					})
					.attr('href','#')
					.text('Delete')
				);
			}
			else
			{
				humanMsg.displayMsg(result,'error');
			}
		}
	});
}

jQuery(document).ready(function($) {
	$('.tablesorter').tablesorter({
		widgets: ['zebra'],
		headers: {0:{sorter: false}}
	});

	$('.cancel').click(cancelhandler);
	$('.save').click(savehandler);

	$('#forma').submit(function() {
		var len = $("tbody input:checked").length;
		if (len > 0)
		{
			if (confirm($('input:submit',this).val() + '?'))
			{
				var arr = new Array();
				$('tbody input:checked').each( function() {
					arr.push($(this).val());
				});

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_cc_commands',
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
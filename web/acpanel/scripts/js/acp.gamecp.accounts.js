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
	if (confirm(txt))
	{
		jQuery.ajax({
			type:'POST',
			url:'acpanel/ajax.php?do=ajax_gamecp',
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

function change_status(subjm)
{
	jQuery.ajax({
		type:'POST',
		url:'acpanel/ajax.php?do=ajax_gamecp',
		data:({id : subjm,'go' : 10}),
		success:function(result) {
			if( result.indexOf('id="success"') + 1)
			{
				var st_old = ( jQuery('tr#' + subjm + ' .img-status').hasClass('red') ) ? 'red' : 'on';
				var st_new = ( st_old == 'on' ) ? 'red' : 'on';
				jQuery('.accessMessage').html('');
				humanMsg.displayMsg(result,'success');
				jQuery('tr#' + subjm + ' .img-status').removeClass(st_old).addClass(st_new).attr('src','acpanel/images/status_' + st_new + '.png');
			}
			else
			{
				humanMsg.displayMsg(result,'error');
			}
		}
	});

	return false;
}

jQuery(document).ready(function($) {
	$('.tablesorter').tablesorter({
		widgets: ['zebra'],
		headers: {0:{sorter: false}}
	});

	$('#forma').submit(function() {
		var len = $("tbody input:checked").length;
		if (len > 0)
		{
			var action = $('#forma select[name="select_action"] option:selected').val();
			if (action == 'delete')
			{
				if( confirm($('select[name="select_action"] option:selected',this).text() + '?') )
				{
					var arr = new Array();
					$('tbody input:checked').each( function() {
						arr.push($(this).val());
					});

					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_gamecp',
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
			}
			else if (action == 'active')
			{
				if( confirm($('select[name="select_action"] option:selected',this).text() + '?') )
				{
					var arr = new Array();
					$('tbody input:checked').each( function() {
						arr.push($(this).val());
					});

					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_gamecp',
						data:({'marked_word[]' : arr,'go' : 6}),
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
			}
			else
			{
				if( confirm($('select[name="select_action"] option:selected',this).text() + '?') )
				{
					var arr = new Array();
					$('tbody input:checked').each( function() {
						arr.push($(this).val());
					});

					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_gamecp',
						data:({'marked_word[]' : arr,'go' : 7}),
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
			}

		} else {
			alert($('input:hidden:last',this).val());
		}

		return false;
	});
});
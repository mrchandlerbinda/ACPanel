(function ($) {
	$(function () {
		// Check / uncheck all checkboxes
		$('.check_all').click(function() {
			$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
		});
	
		$('.server-online a[rel*=facebox]').facebox();
	});
})(jQuery);

function remove_row(subjm,txt) {
	if (confirm(txt))
	{
		jQuery.ajax({
			type:'POST',
			url:'acpanel/ajax.php?do=ajax_servers_control',
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
		url:'acpanel/ajax.php?do=ajax_servers_control',
		data:({id : subjm,'go' : 8}),
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

	$('.steam-connect span, .steam-connect a, .user-link span, .user-link a').hover(
		function(e) {
			$(this).parent().addClass('active');
	    },
		function() {
			$(this).parent().removeClass('active');
	    }
	).click(function() {
		var par = $(this).parent('td');
		window.location.href = $('a', par).attr('href');
		return false;
	});

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
					url:'acpanel/ajax.php?do=ajax_servers_control',
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
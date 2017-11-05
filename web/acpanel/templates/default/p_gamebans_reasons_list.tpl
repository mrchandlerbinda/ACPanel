{literal}
<script type='text/javascript'>
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
				url:'acpanel/ajax.php?do=ajax_gamebans',
				data:({id : subjm,'go' : 7}),
				success:function(result) {
					if( result.indexOf('id="success"') + 1)
					{
						jQuery('.accessMessage').html('');
						humanMsg.displayMsg(result,'success');
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
		if(jQuery('tr td[id] input').length > 0)
		{
			cancelhandler();
		}
	
		jQuery('tr#' + subjm + ' td[id]').each( function() {
			var valtext = jQuery(this).text();
			var idtd = jQuery(this).attr("id");
	
			jQuery(this).html(jQuery('<input>')
				.attr('type','text')
				.attr('name',idtd)
				.attr('value',valtext)
				.addClass('text small')
			).append(jQuery('<input>')
				.attr('type','hidden')
				.attr('name',idtd)
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
				return remove_row(idrow, '@@confirm_del@@');
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
			url:'acpanel/ajax.php?do=ajax_gamebans',
			data:'go=8&editid=' + idrow + str,
			success:function(result) {
				if( result.indexOf('id="success"') + 1 )
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
							return remove_row(idrow, '@@confirm_del@@');
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
						url:'acpanel/ajax.php?do=ajax_gamebans',
						data:({'marked_word[]' : arr,'go' : 9}),
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
</script>
{/literal}
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@reason_server@@</th>
				<th>@@reason_text@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$reasons item=item}
			<tr id="{$item.id}">
				<td><input type="checkbox" name="marked_word" value="{$item.id}" /></td>
				<td id="address" class="editable">{$item.address}</td>
				<td id="reason" class="editable">{$item.reason|htmlspecialchars}</td>
				<td class="delete"><a href="#" onclick="return edit_row('{$item.id}')">Edit</a> | <a href="#" onclick="return remove_row('{$item.id}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($reasons)}
			<tfoot>
				<tr class="emptydata"><td colspan="7">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
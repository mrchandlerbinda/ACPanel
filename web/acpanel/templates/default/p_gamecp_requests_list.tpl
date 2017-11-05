{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			// Check / uncheck all checkboxes
			$('.check_all').click(function() {
				$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
			});
		
			$('#forma a[rel*=facebox]').facebox();
		});
	})(jQuery);
	
	function remove_row(subjm,txt)
	{
		if (confirm(txt))
		{
			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:({id : subjm,'go' : 20}),
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
					if (confirm($('select[name="select_action"] option:selected',this).text() + '?'))
					{
						var arr = new Array();
						$('tbody input:checked').each( function() {
							arr.push($(this).val());
						});
	
						$.ajax({
							type:'POST',
							url:'acpanel/ajax.php?do=ajax_gamecp',
							data:({'marked_word[]' : arr,'go' : 21}),
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
					var arr = new Array();
	
					$('tbody input:checked').each( function() {
						if( $(this).parents().eq(1).find('.img-status').attr('src').indexOf('status_off.png') + 1 )
						{
							arr.push($(this).val());
						}
					});
	
					if( arr.length > 0 )
					{
						var name = prompt($('select[name="select_action"] option:selected',this).text() + ' ' + arr.length + ' @@ticket_reason@@');
						if( name != null )
						{
							$.blockUI({ message: null });
		
							$.ajax({
								type:'POST',
								url:'acpanel/ajax.php?do=ajax_gamecp',
								data:({'marked_word[]' : arr,'go' : (action == 'approve') ? 22 : 23,'comment' : name,'username' : $('input[name="uname"]').val()}),
								success:function(result) {
									if( result.indexOf('id="success"') + 1)
									{
										rePagination(0);
										$('table').trigger('update');
										$('table').trigger('applyWidgets', 'zebra');
		
										$.unblockUI({ 
											onUnblock: function() {
												humanMsg.displayMsg(result,'success');
											} 
										});
									}
									else
									{
										if( action == 'approve' )
										{
											rePagination(0);
											$('table').trigger('update');
											$('table').trigger('applyWidgets', 'zebra');
										}
		
										$.unblockUI({ 
											onUnblock: function() {
												humanMsg.displayMsg(result,'error');
											} 
										});								
									}
								}
							});
		
							$(this).find('input:checkbox').attr('checked', $(this).is(''));
						}
					}
					else
					{
						alert('@@selected_incorrect@@');
					}
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
				<th>@@ticket_date@@</th>
				<th>@@ticket_user@@</th>
				<th>@@ticket_action@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$tickets item=ticket}
			<tr id="{$ticket.id}">
				<td><input type="checkbox" name="marked_word" value="{$ticket.id}" /></td>
				<td>{$ticket.timestamp}</td>
				<td>{if $ticket.cnt_hid > 1}<img style="position:relative; top:2px;" src="acpanel/images/warning.png" alt="" /> {/if}<a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$ticket.userid}">{$ticket.username|htmlspecialchars}</a></td>
				<td>{$ticket.ticket_type}</td>
				<td class="delete"><a rel="facebox" href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&id={$ticket.id}">Edit</a> | <a href="#" onclick="return remove_row('{$ticket.id}','@@confirm_del@@')">Delete</a> | <img class="img-status" src="acpanel/images/status_{if $ticket.ticket_status == 1}on{elseif $ticket.ticket_status == 2}red{else}off{/if}.png" alt="{if $ticket.ticket_status == 1}@@approved@@{elseif $ticket.ticket_status == 2}@@disapproved@@{else}@@moderated@@{/if}" title="{if $ticket.ticket_status == 1}@@approved@@{elseif $ticket.ticket_status == 2}@@disapproved@@{else}@@moderated@@{/if}" /></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($tickets)}
			<tfoot>
				<tr class="emptydata"><td colspan="5">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<select name="select_action">
			<option value="delete" selected="selected">@@delete@@</option>
			<option value="approve">@@approve@@</option>
			<option value="disapprove">@@disapprove@@</option>
		</select>
		<input type="hidden" name="uname" value="{$smarty.post.username}" />
		<input type="submit" class="submit tiny" value="@@apply@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
{literal}
<script type='text/javascript'>
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
				url:'acpanel/ajax.php?do=ajax_task_sheduler',
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
			url:'acpanel/ajax.php?do=ajax_task_sheduler',
			data:({id : subjm,'go' : 6}),
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
						url:'acpanel/ajax.php?do=ajax_task_sheduler',
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
</script>
{/literal}
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@cron_expression@@</th>
				<th>@@last_run@@</th>
				<th>@@next_run@@</th>
				<th>@@run_file@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$tasks item=task}
			<tr id="{$task.entry_id}">
				<td><input type="checkbox" name="marked_word" value="{$task.entry_id}" /></td>
				<td>{$task.run_rules}</td>
				<td>{$task.last_run}</td>
				<td>{$task.next_run}</td>
				<td>{$task.cron_file|htmlspecialchars}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&id={$task.entry_id}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$task.entry_id}','@@confirm_del@@')">Delete</a> | <a href="#" onclick="return change_status('{$task.entry_id}')" title="@@click_change_status@@"><img class="img-status {if $task.active == 1}on{else}red{/if}" src="acpanel/images/status_{if $task.active == 1}on{else}red{/if}.png" alt="" /></a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($tasks)}
			<tfoot>
				<tr class="emptydata"><td colspan="6">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
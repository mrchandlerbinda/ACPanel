{literal}
<script type='text/javascript'>
	(function ($) {
		// Check / uncheck all checkboxes
		$('.check_all').click(function() {
			$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
		});

		$(function () {
			$('#forma a[rel*=facebox]').facebox();
		});
	})(jQuery);
	
	function remove_row(subjm,txt)
	{
		if (confirm(txt))
		{
			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_payment',
				data:({id : subjm,'go' : 6}),
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
				if (confirm($('input:submit',this).val() + '?'))
				{
					var arr = new Array();
					$('tbody input:checked').each( function() {
						arr.push($(this).val());
					});
	
					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_payment',
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
				<th>@@group_name@@</th>
				<th>@@group_count_privileges@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$groups item=group}
			<tr>
				<td><input type="checkbox" name="marked_word" value="{$group.gid}" /></td>
				<td>{$group.name|htmlspecialchars}</td>
				<td>{if $group.privileges}<a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_patterns}&t={$group.gid}">{/if}{$group.privileges}{if $group.privileges}</a>{/if}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_edit}&s={$group.gid}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$group.gid}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($groups)}
			<tfoot>
				<tr class="emptydata"><td colspan="4">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
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
			url:'acpanel/ajax.php?do=ajax_general_lang',
			data:({id : subjm,'go' : 8}),
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
				<th>@@template_title@@</th>
				<th>@@count_phrases@@</th>
				<th>@@productid@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$langs item=lang}
			<tr id="{$lang.lp_id}">
				<td><input type="checkbox" name="marked_word" value="{$lang.lp_id}" /></td>
				<td>{$lang.lp_name|htmlspecialchars}</td>
				<td>{if $lang.cnt}<a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.phrases_id}&s=lw_en&t={$lang.lp_id}">{/if}{$lang.cnt}{if $lang.cnt}</a>{/if}</td>
				<td>{$lang.productid|htmlspecialchars}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&s={$lang.lp_id}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$lang.lp_id}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($langs)}
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
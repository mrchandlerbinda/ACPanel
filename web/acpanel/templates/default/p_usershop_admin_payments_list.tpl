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
	
	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra'],
			headers: {0:{sorter: false},3:{sorter: false}}
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
						data:({'marked_word[]' : arr,'go' : 11}),
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
				<th style="width:100px;">@@transaction_id@@</th>
				<th style="width:100px;">@@transaction_amount@@</th>
				<th>@@transaction_memo@@</th>
				<th>@@transaction_user@@</th>
				<th style="width:100px;">@@transaction_enrolled@@</th>
			</tr>
		</thead>
	
		<tbody>
		{foreach from=$transactions item=tr}
			<tr id="{$tr.pid}"{if !$tr.enrolled} class="not-pay"{/if}>
				<td><input type="checkbox" name="marked_word" value="{$tr.pid}" /></td>
				<td>{$tr.pid}</td>
				<td class="{if $tr.currency == "mm"}{if $tr.amount > 0}mm-plus{else}mm-minus{/if}{else}{if $tr.amount > 0}points-plus{else}points-minus{/if}{/if}">{$tr.amount}</td>
				<td>{$tr.memo|htmlspecialchars}</td>
				<td>{$tr.username}</td>
				<td>{if $tr.enrolled}{$tr.enrolled}{else}@@not_pay@@{/if}</td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($transactions)}
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
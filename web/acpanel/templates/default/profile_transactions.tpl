{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra'],
			headers: {2:{sorter: false}}
		});
	});
</script>
{/literal}
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="width:100px;">@@transaction_id@@</th>
			<th style="width:100px;">@@transaction_amount@@</th>
			<th>@@transaction_memo@@</th>
			<th style="width:100px;">@@transaction_enrolled@@</th>
		</tr>
	</thead>

	<tbody>
	{foreach from=$transactions item=tr}
		<tr>
			<td>{$tr.pid}</td>
			<td class="{if $tr.currency == "mm"}{if $tr.amount > 0}mm-plus{else}mm-minus{/if}{else}{if $tr.amount > 0}points-plus{else}points-minus{/if}{/if}">{$tr.amount}</td>
			<td>{$tr.memo|htmlspecialchars}</td>
			<td>{$tr.enrolled}</td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($transactions)}
		<tfoot>
			<tr class="emptydata"><td colspan="4">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
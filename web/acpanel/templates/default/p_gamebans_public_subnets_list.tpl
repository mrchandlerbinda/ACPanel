{literal}
<script type='text/javascript'>	
	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra']
		});
	});
</script>
{/literal}
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>@@subnet_mask@@</th>
			<th>@@subnet_comment@@</th>
		</tr>
	</thead>

	<tbody>
	{foreach from=$subnets item=item}
		<tr id="{$item.id}">
			<td>{$item.subipaddr} [{$item.bitmask}]</td>
			<td>{$item.comment|htmlspecialchars}</td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($subnets)}
		<tfoot>
			<tr class="emptydata"><td colspan="2">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
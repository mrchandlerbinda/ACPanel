{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$('.tablesorter a[rel*=facebox]').facebox();
		});
	})(jQuery);

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
			<th>{$field|htmlspecialchars}</th>
			<th>@@count_bans@@</th>
		</tr>
	</thead>

	<tbody>
	{foreach from=$stats item=item}
		<tr>
			<td>{if $item.flag}{$item.flag}&nbsp;{/if}{$item.value|htmlspecialchars}</td>
			<td>{$item.count}</td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($stats)}
		<tfoot>
			<tr class="emptydata"><td colspan="2">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
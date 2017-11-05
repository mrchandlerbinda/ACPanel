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
			<th>@@phrase@@</th>
			<th>@@phrase_word@@</th>
			<th>@@phrase_tpl@@</th>
			<td width="100">&nbsp;</td>
		</tr>
	</thead>

	<tbody>
	{foreach from=$array_phrases item=phrase}
		<tr id="{$phrase.lw_id}">
			<td>{$phrase.lw_lang|htmlspecialchars}</td>
			<td>{$phrase.lw_word}</td>
			<td>{$phrase.lp_name}</td>
			<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&id={$phrase.lw_id}">Edit</a></td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($array_phrases)}
		<tfoot>
			<tr class="emptydata"><td colspan="4">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
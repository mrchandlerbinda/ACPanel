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
			<th>@@user_name@@</th>
			<th>@@user_email@@</th>
			<th>@@user_reg@@</th>
			<th>@@user_activity@@</th>
			<td>&nbsp;</td>
		</tr>
	</thead>

	<tbody>
	{foreach from=$array_users item=item}
		<tr id="{$item.uid}">
			<td>{$item.username}</td>
			<td>{$item.mail}</td>
			<td>{$item.reg_date}</td>
			<td>{$item.last_visit}</td>
			<td class="delete"><a href="{$home}?cat={$smarty.post.section_current}&do={$smarty.post.edit_cat}&t=0&id={$item.uid}">Edit</a></td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($array_users)}
		<tfoot>
			<tr class="emptydata"><td colspan="5">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
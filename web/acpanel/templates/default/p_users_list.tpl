<script type='text/javascript' src='acpanel/scripts/js/acp.users.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@user_name@@</th>
				<th>@@user_email@@</th>
				<th>@@user_reg@@</th>
				<th>@@user_activity@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$users item=item}
			<tr id="{$item.uid}">
				<td><input type="checkbox" name="marked_word" value="{$item.uid}" /></td>
				<td id="value">{$item.username}</td>
				<td>{$item.mail}</td>
				<td>{$item.reg_date}</td>
				<td>{$item.last_visit}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.section_current}&t={$smarty.post.group}&id={$item.uid}">Edit</a> | <a href="#" onclick="return remove_row('{$item.uid}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($users)}
			<tfoot>
				<tr class="emptydata"><td colspan="{$colums}">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
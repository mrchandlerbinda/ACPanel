<script type='text/javascript' src='acpanel/scripts/js/acp.usergroups.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@group@@</th>
				<th>@@users@@</th>
				<th>@@weight@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$groups item=group}
			<tr id="{$group.usergroupid}">
				<td><input type="checkbox" name="marked_word" value="{$group.usergroupid}" /></td>
				<td id="value">{$group.usergroupname}</td>
				<td>{if $group.users > 0}<a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.section_users}&t={$group.usergroupid}">{$group.users}</a>{else}{$group.users}{/if}</td>
				<td>{$group.weight}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.section_current}&id={$group.usergroupid}">Edit</a> | <a href="#" onclick="return remove_row('{$group.usergroupid}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($groups)}
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
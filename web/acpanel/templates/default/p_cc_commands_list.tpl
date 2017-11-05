<script type='text/javascript' src='acpanel/scripts/js/acp.cc.commands.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@command@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$commands item=command}
			<tr id="{$command.id}">
				<td><input type="checkbox" name="marked_word" value="{$command.id}" /></td>
				<td id="value">{$command.value}</td>
				<td class="delete"><a href="#" onclick="return edit_row('{$command.id}')">Edit</a> | <a href="#" onclick="return remove_row('{$command.id}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($commands)}
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
<script type='text/javascript' src='acpanel/scripts/js/acp.nc.patterns.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@pattern@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$patterns item=pattern}
			<tr id="{$pattern.id}">
				<td><input type="checkbox" name="marked_word" value="{$pattern.id}" /></td>
				<td id="pattern">{$pattern.pattern|htmlspecialchars}</td>
				<td class="delete"><a href="#" onclick="return edit_row('{$pattern.id}')">Edit</a> | <a href="#" onclick="return remove_row('{$pattern.id}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($patterns)}
			<tfoot>
				<tr class="emptydata"><td colspan="3">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<select name="select_action">
			<option value="delete" selected="selected">@@delete@@</option>
			<option value="move">@@move@@</option>
		</select>
		<select name="pattern" style="display: none;">
			{if $get_in != 1}<option value="1">Rename-List</option>{/if}
			{if $get_in != 0}<option value="0">White-List</option>{/if}
		</select>
		<input type="submit" class="submit tiny" value="@@apply@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
<script type='text/javascript' src='acpanel/scripts/js/acp.cc.patterns.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@pattern@@</th>
				{if $get_in != 0 && $get_in != 1}
					<th>@@reason@@</th>
					{if $get_in != 3}
						<th>@@time@@</th>
					{/if}
				{/if}
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$patterns item=pattern}
			<tr id="{$pattern.id}">
				<td><input type="checkbox" name="marked_word" value="{$pattern.id}" /></td>
				<td id="pattern">{$pattern.pattern|htmlspecialchars}</td>
				{if $get_in != 0 && $get_in != 1}
					<td id="reason" class="editable">{$pattern.reason|htmlspecialchars}</td>
					{if $get_in != 3}
						<td id="length" class="editable">{$pattern.length}</td>
					{/if}
				{/if}
				<td class="delete"><a href="#" onclick="return edit_row('{$pattern.id}')">Edit</a> | <a href="#" onclick="return remove_row('{$pattern.id}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($patterns)}
			<tfoot>
				<tr class="emptydata"><td colspan="{if $get_in == 2 || $get_in == 4}5{elseif $get_in == 3}4{else}3{/if}">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<select name="select_action">
			<option value="delete" selected="selected">@@delete@@</option>
			<option value="move">@@move@@</option>
		</select>
		<select name="pattern" style="display: none;">
			{if $get_in != 0}<option value="0">White-List</option>{/if}
			{if $get_in != 1}<option value="1">Hide-List</option>{/if}
			{if $get_in != 2}<option value="2">Ban-List</option>{/if}
			{if $get_in != 3}<option value="3">Kick-List</option>{/if}
			{if $get_in != 4}<option value="4">Notice-List</option>{/if}
		</select>
		<input type="submit" class="submit tiny" value="@@apply@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
<script type='text/javascript' src='acpanel/scripts/js/acp.hm.patterns.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@pattern@@</th>
				<th>@@priority@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$patterns item=pattern}
			<tr>
				<td><input type="checkbox" name="marked_word" value="{$pattern.hud_id}" /></td>
				<td id="pattern">{$pattern.name|htmlspecialchars}</td>
				<td id="priority">{$pattern.priority}</td>
				<td class="delete"><a rel="facebox" href="?cat={$smarty.post.cat_current}&do={$smarty.post.cat_edit}&id={$pattern.hud_id}">Edit</a> | <a href="#" onclick="return remove_row('{$pattern.hud_id}','@@confirm_del@@')">Delete</a></td>
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
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
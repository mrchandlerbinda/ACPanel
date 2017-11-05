<script type='text/javascript' src='acpanel/scripts/js/acp.general.phrases.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@phrase@@</th>
				<th>@@phrase_word@@</th>
				<th>@@phrase_tpl@@</th>
				<td width="100">&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$phrases item=phrase}
			<tr id="{$phrase.lw_id}">
				<td><input type="checkbox" name="marked_word" value="{$phrase.lw_id}" /></td>
				<td id="phrase">{$phrase.lw_lang|htmlspecialchars}</td>
				<td id="phrase-word">{$phrase.lw_word}</td>
				<td id="phrase-tpl">{$phrase.lp_name}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&s={$smarty.post.code}&t={$smarty.post.lp_id}&id={$phrase.lw_id}">Edit</a> | <a href="#" onclick="return remove_row('{$phrase.lw_id}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($phrases)}
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
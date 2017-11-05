<script type='text/javascript' src='acpanel/scripts/js/acp.general.lang.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@lang_title@@</th>
				<th>@@lang_code@@</th>
				<th>@@lang_active@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$langs item=lang}
			<tr id="{$lang.lang_id}">
				<td><input type="checkbox" name="marked_word" value="{$lang.lang_id}" /></td>
				<td id="lang-title">{$lang.lang_title}</td>
				<td id="lang-code">{$lang.lang_code}</td>
				<td id="lang-active">{if $lang.lang_active == 'yes'}@@yes@@{else}@@no@@{/if}</td>
				<td class="delete">
					<a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&s={$lang.lang_id}" rel="facebox">Edit</a>
					 | <a href="#" onclick="return remove_row('{$lang.lang_id}','@@confirm_del@@')">Delete</a>
					{if $smarty.post.cat_current} | <a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.phrases_id}&s={$lang.lang_code}">Phrases</a>{/if}
					 | <a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&xml={$lang.lang_id}">Export</a>
				</td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($langs)}
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
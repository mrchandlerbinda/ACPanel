<script type='text/javascript' src='acpanel/scripts/js/acp.servers.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@hostname@@</th>
				<th>@@address@@</th>
				<th>@@added@@</th>
				<th>@@rating@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$servers item=server}
			<tr id="{$server.id}">
				<td><input type="checkbox" name="marked_word" value="{$server.id}" /></td>
				<td>{$server.country}&nbsp;{$server.hostname}</td>
				<td class="steam-connect"><span></span><a href="steam://connect/{$server.address}" title="@@connect@@">{$server.address}</a></td>
				<td class="user-link"><span></span><a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$server.userid}" title="@@go_to_profile@@">{$server.username|htmlspecialchars}</a></td>
				<td>{$server.rating}</td>
				<td class="delete"><a href="{$home}?cat={$current_cat}&do={$smarty.post.cat_edit}&server={$server.id}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$server.id}','@@confirm_del@@')">Delete</a> | <a href="#" onclick="return change_status('{$server.id}')" title="@@click_change_status@@"><img class="img-status {if $server.active}on{else}red{/if}" src="acpanel/images/status_{if $server.active}on{else}red{/if}.png" alt="" /></a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($servers)}
			<tfoot>
				<tr class="emptydata"><td colspan="6">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
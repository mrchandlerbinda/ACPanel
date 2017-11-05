<script type='text/javascript' src='acpanel/scripts/js/acp.gamecp.accounts.js'></script>
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@data_created@@</th>
				<th>@@player_nick@@</th>
				<th>@@auth_type@@</th>
				<th>@@user_name@@</th>
				<th>@@last_game@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$accounts item=account}
			<tr id="{$account.userid}">
				<td><input type="checkbox" name="marked_word" value="{$account.userid}" /></td>
				<td>{$account.timestamp}</td>
				<td id="player_nick">{$account.player_nick|htmlspecialchars}</td>
				<td id="flag">{if $account.flag == 1}@@auth_nick@@{elseif $account.flag == 2}@@auth_ip@@{elseif $account.flag == 3}@@auth_steam@@{else}@@not_auth_type@@{/if}</td>
				<td id="username">{if $account.cnt_hid > 1}<img style="position:relative; top:2px;" src="acpanel/images/warning.png" alt="" /> {/if}<a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$account.userid}">{$account.username|htmlspecialchars}</a></td>
				<td id="last_time">{$account.last_time}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&id={$account.userid}">Edit</a> | <a href="#" onclick="return remove_row('{$account.userid}','@@confirm_del@@')">Delete</a> | <a href="#" onclick="return change_status('{$account.userid}')" title="@@click_change_status@@"><img class="img-status {if $account.approved == 'yes'}on{else}red{/if}" src="acpanel/images/status_{if $account.approved == 'yes'}on{else}red{/if}.png" alt="" /></a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($accounts)}
			<tfoot>
				<tr class="emptydata"><td colspan="7">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<select name="select_action">
			<option value="delete" selected="selected">@@delete@@</option>
			<option value="active">@@active@@</option>
			<option value="inactive">@@inactive@@</option>
		</select>
		<input type="submit" class="submit tiny" value="@@apply@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
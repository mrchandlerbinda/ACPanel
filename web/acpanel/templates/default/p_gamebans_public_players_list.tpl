{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$('.tablesorter a[rel*=facebox]').facebox();
		});
	})(jQuery);

	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra']
		});
	});
</script>
{/literal}
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>@@ban_created@@</th>
			<th>@@ban_player_name@@</th>
			<th>@@ban_player_reason@@</th>
			<th>@@ban_player_time@@</th>
			{if !$hide_admins}<th>@@ban_player_admin@@</th>{/if}
			<td>&nbsp;</td>
		</tr>
	</thead>

	<tbody>
	{foreach from=$bans item=item}
		<tr>
			<td>{$item.ban_created}</td>
			<td>{$item.country}&nbsp;{$item.player_nick|htmlspecialchars}</td>
			<td>{$item.ban_reason|htmlspecialchars}</td>
			<td{if $item.ban_remain} class="{if $item.unban_admin_uid}ban-removed{else}ban-remain{/if}"{/if}>{$item.ban_length}{$item.ban_remain}</td>
			{if !$hide_admins}<td>{$item.admin_nick|htmlspecialchars}</td>{/if}
			<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.section_current}&bid={$item.bid}" rel="nofollow facebox"><img class="img-status" src="acpanel/images/question.png" alt="@@ban_detail@@" title="@@ban_detail@@" /></a></td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($bans)}
		<tfoot>
			<tr class="emptydata"><td colspan="{if !$hide_admins}6{else}5{/if}">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
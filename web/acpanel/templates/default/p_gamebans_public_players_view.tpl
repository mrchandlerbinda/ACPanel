{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#ban-detail-info table.tablesorter tr').each(function(i, it) {		
			if( i % 2 == 0 )
			{		
				$(this).addClass('odd');
			}
			else
			{
				$(this).addClass('even');
			}		
		})
	});
</script>
{/literal}
<div id="ban-detail-info" style="width: 600px;">
	<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@ban_detail@@ #{$ban.bid}</h3>
	{if $iserror}
		<div class="message errormsg"><p>{$iserror}</p></div>
	{else}
		<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
			<thead></thead>
			<tbody>
				{if $ban.player_nick}
				<tr>
					<td><b>@@ban_player_nick@@</b></td>
					<td>{$ban.player_nick|htmlspecialchars}</td>
				</tr>
				{/if}

				<tr>
					<td><b>@@ban_type@@</b></td>
					<td>{$ban.ban_type}</td>
				</tr>

				{if $ban.player_ip}
				<tr>
					<td><b>@@ban_player_ip@@</b></td>
					<td>{$ban.player_ip}</td>
				</tr>
				{/if}

				{if $ban.player_id}
				<tr>
					<td><b>@@ban_player_steam@@</b></td>
					<td>{$ban.player_id}</td>
				</tr>
				{/if}

				<tr>
					<td><b>@@ban_created@@</b></td>
					<td>{$ban.ban_created}</td>
				</tr>

				<tr>
					<td><b>@@ban_length@@</b></td>
					<td{if $ban.ban_remain} class="{if $ban.unban_admin_uid}ban-removed{else}ban-remain{/if}"{/if}>{$ban.ban_length}<span class="infoMsg note">{$ban.ban_remain}</span></td>
				</tr>

				<tr>
					<td><b>@@ban_reason@@</b></td>
					<td>{$ban.ban_reason|htmlspecialchars}</td>
				</tr>

				<tr>
					<td><b>@@ban_server@@</b></td>
					<td>{$ban.server_name|htmlspecialchars}</td>
				</tr>

				{if !$hide_admins}
				<tr>
					<td><b>@@ban_admin_nick@@</b></td>
					<td>{$ban.admin_nick|htmlspecialchars}</td>
				</tr>
				{/if}
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" style="border: medium none;"><i></i></td>
				</tr>
			</tfoot>
		</table>
	{/if}
</div>
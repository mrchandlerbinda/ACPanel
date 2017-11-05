<div class="info-details-box" style="width: 600px;">
	<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@usershop_admin_patterns_user_detail@@ #{$priv.id}</h3>
	{if empty($priv)}
		<div class="message warning"><p>@@user_privileges_details_not_found@@</p></div>
	{else}
		<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<td><b>@@user@@</b></td>
					<td>{$priv.user}</td>
				</tr>
				<tr>
					<td><b>@@privilege_name@@</b></td>
					<td>{$priv.privilege}</td>
				</tr>
				<tr>
					<td><b>@@effective_period@@</b></td>
					<td{if $time_expired} style="background-color:#F4D7D7;"{/if}>{$priv.lifetime}</td>
				</tr>
				{if $priv.group}
				<tr>
					<td><b>@@override_group@@</b></td>
					<td>{$priv.group}</td>
				</tr>
				{/if}
				{if $priv.account_mask}
				<tr>
					<td><b>@@account_access_mask@@</b></td>
					<td>{$priv.account_mask}</td>
				</tr>
				{/if}
				{if $priv.mask_servers}
				<tr>
					<td><b>@@servers@@</b></td>
					<td>{$priv.mask_servers}</td>
				</tr>
				{/if}
			</tbody>
		</table>
	{/if}
</div>
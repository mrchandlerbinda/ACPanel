{if !empty($admins)}
	{literal}
	<script type='text/javascript'>
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
				<th>@@nick@@</th>
				{if $colums == 4}<th>@@total_bans@@</th>{/if}
				<th>@@group@@</th>
				<th>@@icq@@</th>
			</tr>
		</thead>

		<tbody>
		{foreach from=$admins item=admin}
			<tr>
				<td>{$admin.username|htmlspecialchars}</td>
				{if $colums == 4}<td>{if $admin.total_bans}<a href="{$home}?cat={$cat_bans.cat}&do={$cat_bans.do}&t=0&search=&s=0&a={$admin.uid}">{/if}{$admin.total_bans}{if $admin.total_bans}</a>{/if}</td>{/if}
				<td>{$admin.usergroupname|htmlspecialchars}</td>
				<td>{$admin.icq}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{/if}

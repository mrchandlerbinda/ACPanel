<script type='text/javascript' src='acpanel/scripts/js/acp.nc.logs.js'></script>
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th width="150">@@time@@</th>
			<th>@@checked_nick@@</th>
			<th>@@player_action@@</th>
			<th>@@reason@@</th>
			<td>&nbsp;</td>
		</tr>
	</thead>

	<tbody>
	{foreach from=$messages key=k item=m}
		<tr>
			<td>{$m.timestamp|date_format:"%H:%M:%S %d.%m.%Y"}</td>
			<td>{$m.name|htmlspecialchars}</td>
			<td>{if $m.action == 1}@@join_game@@{else}@@rename@@{/if}</td>
			<td>{if $m.pattern == 1}Rename-List{elseif $m.pattern == -1}@@lock_length@@{elseif $m.pattern == -2}@@lock_repeat@@{else}White-List{/if}</td>
			<td class="delete">
				<a href="#{$k}" rel="facebox">@@info@@</a>
				<div id="{$k}" style="display: none;">
					<h2>@@more_info@@</h2>
					<ul>
						<li><b>@@server_ip@@</b> : {$m.serverip}</li>
						<li><b>@@player_ip@@</b> : {$m.ip}</li>
						<li><b>@@player_steam@@</b> : {$m.authid}</li>
					</ul>
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($messages)}
		<tfoot>
			<tr class="emptydata"><td colspan="3">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>

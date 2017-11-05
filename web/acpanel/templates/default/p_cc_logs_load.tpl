<script type='text/javascript' src='acpanel/scripts/js/acp.cc.logs.js'></script>
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th width="80">@@pattern@@</th>
			<th width="130">@@time@@</th>
			<th>@@message@@</th>
			<td>&nbsp;</td>
		</tr>
	</thead>

	<tbody>
	{foreach from=$messages key=k item=m}
		<tr>
			<td>{if $m.pattern == -1}-{elseif $m.pattern == 0}White-List{elseif $m.pattern == 1}Hide-List{elseif $m.pattern == 2}Ban-List{elseif $m.pattern == 3}Kick-List{else}Notice-List{/if}</td>
			<td>{$m.timestamp}</td>
			<td>
				{if $m.alive == 0 && $m.team != 'Spectator'}*DEAD*&nbsp;{/if}
				{if $m.cmd == 'amx_chat'}( ADMIN ){elseif $m.foradmins == 1}( PLAYER ){elseif $m.cmd == 'say_team'}( TEAM ){/if}
				<font color="{if $m.team == 'Counter-Terrorist'}blue{elseif $m.team == 'Terrorist'}red{else}gray{/if}">{$m.name|htmlspecialchars}</font>
				&nbsp;:&nbsp;&nbsp;{$m.message|htmlspecialchars}
			</td>
			<td class="delete">
				<a href="#{$k}" rel="facebox">@@info@@</a>
				<div id="{$k}" style="display: none;">
					<h2>@@info_more@@</h2>
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

<script type='text/javascript' src='acpanel/scripts/js/acp.vbk.logs.js'></script>
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th width="130">@@time@@</th>
			<th width="130">@@server@@</th>
			<th>@@info_text@@</th>
			<td width="80">&nbsp;</td>
		</tr>
	</thead>

	<tbody>
	{foreach from=$vbk_logs key=k item=m}
		<tr>
			<td>{$m.timestamp|date_format:"%H:%M:%S %d.%m.%Y"}</td>
			<td>{$m.server_ip}</td>
			<td>
				<a href="#starter_{$k}" rel="facebox">{$m.vote_player_nick|htmlspecialchars}</a> {if $m.vote_type == 'ban'}@@vote_info_ban@@{else}@@vote_info_kick@@{/if} <a href="#nom_{$k}" rel="facebox">{$m.nom_player_nick|htmlspecialchars}</a>
				<div id="starter_{$k}" style="display: none;">
					<h2>@@player_info@@</h2>
					<ul>
						<li><b>@@player_nick@@</b> : {$m.vote_player_nick|htmlspecialchars}</li>
						<li><b>@@player_ip@@</b> : {$m.vote_player_ip}</li>
						<li><b>@@player_steam@@</b> : {$m.vote_player_id}</li>
					</ul>
				</div>
				<div id="nom_{$k}" style="display: none;">
					<h2>@@player_info@@</h2>
					<ul>
						<li><b>@@player_nick@@</b> : {$m.nom_player_nick|htmlspecialchars}</li>
						<li><b>@@player_ip@@</b> : {$m.nom_player_ip}</li>
						<li><b>@@player_steam@@</b> : {$m.nom_player_id}</li>
					</ul>
				</div>
			</td>
			<td class="delete">
				<a href="#voteresult_{$k}" rel="facebox">{if $m.vote_result == '1'}<font color="green">@@success@@</font>{else}<font color="red">@@failed@@</font>{/if}</a>
				<div id="voteresult_{$k}" style="display: none;">
					<h2>@@vote_result_info@@</h2>
					<ul>
						<li><b>@@vote_type@@</b> : {if $m.vote_type == 'ban'}@@vote_info_type_ban@@{else}@@vote_info_type_kick@@{/if}</li>
						<li><b>@@vote_who@@</b> : {$m.vote_player_nick|htmlspecialchars}</li>
						<li><b>@@vote_against@@</b> : {$m.nom_player_nick|htmlspecialchars}</li>
						<li><b>@@vote_reason@@</b> : {$m.vote_reason}</li>
						{if $m.vote_type == 'ban'}
						<li><b>@@vote_ban_length@@</b> : {if $m.vote_type == '0'}@@vote_ban_permanent@@{else}{$m.ban_length}{/if}</li>
						{/if}
						<li><b>@@voted@@</b> : {$m.voted_string}</li>
						<li><b>@@vote_result@@</b> : {if $m.vote_result == '1'}{if $m.vote_type == 'ban'}@@vote_success_ban@@{else}@@vote_success_kick@@{/if}{else}@@vote_failed@@{/if}</li>
					</ul>
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($vbk_logs)}
		<tfoot>
			<tr class="emptydata"><td colspan="4">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>

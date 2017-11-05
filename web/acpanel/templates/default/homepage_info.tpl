<div id="players-info" style="width: 600px;">
	<h3 style="border-bottom: 1px solid #dddddd; padding-bottom: 7px; margin-bottom: 20px;">{$server_info.hostname} - {$server_info.ip}</h3>
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>#</th>
				<th>@@nick@@</th>
				<th>@@frags@@</th>
				<th>@@time@@</th>
			</tr>
		</thead>

		<tbody>
		{assign var='frags' value=0}
		{foreach from=$players_info key=num item=player name=players_info}
			{assign var='frags' value=$frags+$player.2}
			<tr class="{cycle values='odd,even'}">
				<td>{$num}</td>
				<td>{$player.1|htmlspecialchars}</td>
				<td>{$player.2}</td>
				<td>{$player.3}</td>
			</tr>
		{/foreach}
		</tbody>
		<tfoot>
		{if empty($players_info)}
			<tr><td colspan="4" style="text-align: center; border-bottom: medium none;">@@empty_data@@</td></tr>
		{else}
			<tr>
				<td colspan="4" style="border: medium none;"><i>@@total_players@@ - {$smarty.foreach.players_info.total}, @@total_frags@@ - {$frags}</i></td>
			</tr>
		{/if}
		</tfoot>
	</table>
</div>
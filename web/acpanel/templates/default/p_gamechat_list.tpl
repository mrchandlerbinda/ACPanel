{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			// Modal boxes - to all links with rel="facebox"
			$('a[rel*=facebox]').facebox()
		});
	})(jQuery);

	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra'],
			headers: {1:{sorter: false}}
		});
	});
</script>
{/literal}
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th width="130">@@time@@</th>
			<th>@@message@@</th>
			<th width="150">@@serverip@@</th>
		</tr>
	</thead>

	<tbody>
	{foreach from=$messages key=k item=m}
		<tr>
			<td>{$m.timestamp}</td>
			<td>
				{if $m.alive == 0 && $m.team != 'Spectator'}*DEAD*&nbsp;{/if}
				{if $m.cmd == 'amx_chat'}( ADMIN ){elseif $m.foradmins == 1}( PLAYER ){elseif $m.cmd == 'say_team'}( TEAM ){/if}
				<font color="{if $m.team == 'Counter-Terrorist'}blue{elseif $m.team == 'Terrorist'}red{else}gray{/if}">{$m.name|htmlspecialchars}</font>
				&nbsp;:&nbsp;&nbsp;{$m.message|htmlspecialchars}
			</td>
			<td>{$m.serverip}</td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($messages)}
		<tfoot>
			<tr class="emptydata"><td colspan="3">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>

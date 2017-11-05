{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra']
		});
	});
</script>
{/literal}
{if !empty($stats)}
	{foreach from=$stats item=item key=k}
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<td colspan="3">
					<h3>
					{if $k == 1}
						@@bans_topstats_server@@
					{elseif $k == 2}
						@@bans_topstats_reason@@
					{elseif $k == 3}
						@@bans_topstats_length@@
					{elseif $k == 4}
						@@bans_topstats_subnet@@
					{elseif $k == 5}
						@@bans_topstats_country@@
					{else}
						@@bans_topstats_admin@@
					{/if}
					[<a href="{$home}?cat={$smarty.post.catid}&do={$smarty.post.doid}&t={$k}">@@view_all@@</a>]
					</h3>
				</td>
			</tr>
		</thead>
	
		<tbody>
		{counter start=1 assign=cnt}
		{foreach from=$item item=i}
			<tr>
				<td width="10">{$cnt}.{counter}</td>
				<td>{if $i.flag}{$i.flag}&nbsp;{/if}{$i.value|htmlspecialchars}</td>
				<td width="30" class="center">{$i.count}</td>			
			</tr>
		{/foreach}
		</tbody>
	</table>
	{/foreach}
{else}
	<div class="message warning"><p>@@empty_table@@</p></div>
{/if}
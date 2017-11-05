<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@block_stats_player_skill@@</h2>
	</div>
	<div class="block_content">
	{/if}
		{if $error_msg}
			<div class="message errormsg"><p>{$error_msg}</p></div>
		{else}
			{if !empty($sts)}
				<div style="padding-bottom:15px;">
					{foreach from=$sts item=player}
						<dl class="pairsInline" style="height: 26px;">
							<dt><img style="height: 20px;width: 20px;vertical-align:middle;" src="{$player.avatar}" alt=""> {$player.username|htmlspecialchars}</dt>
							<dd style="margin-top: 13px;">{$player.skill}</dd>
						</dl>
					{/foreach}
				</div>
			{/if}
		{/if}
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>

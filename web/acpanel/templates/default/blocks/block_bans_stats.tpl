<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@block_bans_stats@@</h2>
	</div>
	<div class="block_content">
	{/if}
		<div style="padding-bottom:15px;">
			<dl class="pairsInline">
				<dt>@@bans_subnets@@</dt>
				<dd>{$bs.0}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_players@@</dt>
				<dd>{$bs.1}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_by_ip@@</dt>
				<dd>{$bs.2}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_by_steam@@</dt>
				<dd>{$bs.3}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_by_nick@@</dt>
				<dd>{$bs.4}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_permanent@@</dt>
				<dd>{$bs.5}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_timed@@</dt>
				<dd>{$bs.6}</dd>
			</dl>
		</div>
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>

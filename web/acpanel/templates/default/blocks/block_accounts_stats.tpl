<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@block_accounts_stats@@</h2>
	</div>
	<div class="block_content">
	{/if}
		<div style="padding-bottom:15px;">
			<dl class="pairsInline">
				<dt>@@accounts_all@@</dt>
				<dd>{$as.0}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_by_ip@@</dt>
				<dd>{$as.3}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_by_steam@@</dt>
				<dd>{$as.4}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_by_nick@@</dt>
				<dd>{$as.2}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_active@@</dt>
				<dd>{$as.1}</dd>
			</dl>
		</div>
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>

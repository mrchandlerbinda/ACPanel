{if !$err}
	<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@my_game_acc@@</h2>
	</div>
	<div class="block_content">
	{/if}
		<div style="padding-bottom:15px;">
			<dl class="pairsInline">
				<dt>@@game_account@@</dt>
				<dd>{$arrUser.player_nick|htmlspecialchars}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@acc_last_online@@</dt>
				<dd>{$arrUser.last_time}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@acc_online_all@@</dt>
				<dd>{$arrUser.online}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@acc_points@@</dt>
				<dd>{$arrUser.points}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@acc_money@@</dt>
				<dd>{$arrUser.money}</dd>
			</dl>
		</div>
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>
{/if}
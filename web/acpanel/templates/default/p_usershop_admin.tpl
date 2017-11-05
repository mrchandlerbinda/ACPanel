<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h1>@@usershop_manage@@</h1>
	</div>
	<div class="block_content">
		{if $iserror}
		<div class="accessMessage">
			<div class="message warning"><p>{$iserror}</p></div>
		</div>
		{else}
		<div class="left-float-box">
			<div style="position:relative;" class="left-box-content" id="chart-payment-box"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		</div>
		<div style="padding-bottom:15px; position:relative;" class="right-float-box">
			<dl class="pairsInline">
				<dt>@@buy_money_all@@</dt>
				<dd>{$stats.ub_buy_mm}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@buy_points_all@@</dt>
				<dd>{$stats.ub_buy_pt}</dd>
			</dl>
		</div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
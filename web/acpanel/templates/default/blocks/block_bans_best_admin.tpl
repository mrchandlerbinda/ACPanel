<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@block_bans_best_admin@@</h2>
	</div>
	<div class="block_content">
	{/if}
		{if $error_msg}
			<div class="message errormsg"><p>{$error_msg}</p></div>
		{else}
			<p style="text-align:center;padding-bottom:5px;" class="profile-link"><img style="margin:0;padding:7px;" src="{$ba.avatar}" alt=""></p>
			<h3 style="text-align:center;">{$ba.username|htmlspecialchars}</h3>
			<p style="text-align:center;"><b>@@bans_today@@: {$ba.count}</b></p>
			{if !empty($bas)}
				<div style="padding-bottom:15px;">
					{foreach from=$bas item=admin}
						<dl class="pairsInline" style="height: 26px;">
							<dt><img style="height: 20px;width: 20px;vertical-align:middle;" src="{$admin.avatar}" alt=""> {$admin.username|htmlspecialchars}</dt>
							<dd style="margin-top: 13px;">{$admin.count}</dd>
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

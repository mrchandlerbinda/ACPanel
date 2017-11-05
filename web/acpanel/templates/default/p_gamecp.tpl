<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h1>@@game_accounts@@</h1>
	</div>
	<div class="block_content">
		{if $iserror}
		<div class="accessMessage">
			<div class="message warning"><p>{$iserror}</p></div>
		</div>
		{else}
		<div class="left-float-box">
			<div style="position:relative;" class="left-box-content" id="chart-regaccounts-box"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		</div>
		<div style="padding-bottom:15px; position:relative;" class="right-float-box">
			<dl class="pairsInline">
				<dt>@@accounts_all@@</dt>
				<dd>{if $stats.ga_total AND $cats.p_gamecp_accounts}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_accounts}&s=0&t=0">{/if}{$stats.ga_total}{if $stats.ga_total AND $cats.p_gamecp_accounts}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_by_nick@@</dt>
				<dd>{if $stats.ga_by_nick AND $cats.p_gamecp_accounts}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_accounts}&s=0&t=1">{/if}{$stats.ga_by_nick}{if $stats.ga_by_nick AND $cats.p_gamecp_accounts}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_by_ip@@</dt>
				<dd>{if $stats.ga_by_ip AND $cats.p_gamecp_accounts}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_accounts}&s=0&t=2">{/if}{$stats.ga_by_ip}{if $stats.ga_by_ip AND $cats.p_gamecp_accounts}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_by_steam@@</dt>
				<dd>{if $stats.ga_by_steam AND $cats.p_gamecp_accounts}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_accounts}&s=0&t=3">{/if}{$stats.ga_by_steam}{if $stats.ga_by_steam AND $cats.p_gamecp_accounts}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@accounts_blocked@@</dt>
				<dd>{if $stats.ga_blocked AND $cats.p_gamecp_accounts}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_accounts}&s=2&t=0">{/if}{$stats.ga_blocked}{if $stats.ga_blocked AND $cats.p_gamecp_accounts}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@tickets_total@@</dt>
				<dd>{if $stats.t_total AND $cats.p_gamecp_requests}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_requests}&s=0">{/if}{$stats.t_total}{if $stats.t_total AND $cats.p_gamecp_requests}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@tickets_open@@</dt>
				<dd>{if $stats.t_open AND $cats.p_gamecp_requests}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_requests}&s=1">{/if}{$stats.t_open}{if $stats.t_open AND $cats.p_gamecp_requests}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@tickets_approved@@</dt>
				<dd>{if $stats.t_approved AND $cats.p_gamecp_requests}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_requests}&s=2">{/if}{$stats.t_approved}{if $stats.t_approved AND $cats.p_gamecp_requests}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@tickets_rejected@@</dt>
				<dd>{if $stats.t_rejected AND $cats.p_gamecp_requests}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_requests}&s=3">{/if}{$stats.t_rejected}{if $stats.t_rejected AND $cats.p_gamecp_requests}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@active_access_masks@@</dt>
				<dd>{if $stats.masks AND $cats.p_gamecp_mask}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamecp_mask}">{/if}{$stats.masks}{if $stats.masks AND $cats.p_gamecp_mask}</a>{/if}</dd>
			</dl>
		</div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
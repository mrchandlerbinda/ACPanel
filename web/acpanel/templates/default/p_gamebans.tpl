<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h1>@@gamebans@@</h1>
	</div>
	<div class="block_content">
		{if $iserror}
		<div class="accessMessage">
			<div class="message warning"><p>{$iserror}</p></div>
		</div>
		{else}
		<div class="left-float-box">
			<div style="position:relative;" class="left-box-content" id="chart-addbans-box"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		</div>
		<div style="padding-bottom:15px; position:relative;" class="right-float-box">
			<dl class="pairsInline">
				<dt>@@bans_subnets_all@@</dt>
				<dd>{if $stats.gb_sub_total AND $cats.p_gamebans_subnets}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamebans_subnets}&t=1">{/if}{$stats.gb_sub_total}{if $stats.gb_sub_total AND $cats.p_gamebans_subnets}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_players_all@@</dt>
				<dd>{if $stats.gb_pl_total AND $cats.p_gamebans_players}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamebans_players}&t=0">{/if}{$stats.gb_pl_total}{if $stats.gb_pl_total AND $cats.p_gamebans_players}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_by_nick@@</dt>
				<dd>{if $stats.gb_by_nick AND $cats.p_gamebans_search}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamebans_search}&search=0&type_all=no&ban_type[]=N">{/if}{$stats.gb_by_nick}{if $stats.gb_by_nick AND $cats.p_gamebans_search}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_by_ip@@</dt>
				<dd>{if $stats.gb_by_ip AND $cats.p_gamebans_search}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamebans_search}&search=0&type_all=no&ban_type[]=SI">{/if}{$stats.gb_by_ip}{if $stats.gb_by_ip AND $cats.p_gamebans_search}</a>{/if}</dd>
			</dl>
			<dl class="pairsInline">
				<dt>@@bans_by_steam@@</dt>
				<dd>{if $stats.gb_by_steam AND $cats.p_gamebans_search}<a href="{$home}?cat={$section_current.id}&do={$cats.p_gamebans_search}&search=0&type_all=no&ban_type[]=S">{/if}{$stats.gb_by_steam}{if $stats.gb_by_steam AND $cats.p_gamebans_search}</a>{/if}</dd>
			</dl>
		</div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
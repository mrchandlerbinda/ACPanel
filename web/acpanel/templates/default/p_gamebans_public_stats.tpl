<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@gb_banlist_stats@@</h2>

		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="t">
						<option value="0"{if $get_stats == 0} selected{/if}>@@bans_stats_top@@</option>
						<option value="1"{if $get_stats == 1} selected{/if}>@@bans_stats_server@@</option>
						<option value="2"{if $get_stats == 2} selected{/if}>@@bans_stats_reason@@</option>
						<option value="3"{if $get_stats == 3} selected{/if}>@@bans_stats_length@@</option>
						<option value="4"{if $get_stats == 4} selected{/if}>@@bans_stats_subnet@@</option>
						<option value="5"{if $get_stats == 5} selected{/if}>@@bans_stats_country@@</option>
						<option value="6"{if $get_stats == 6} selected{/if}>@@bans_stats_admin@@</option>
					</select>
				</form>
			</li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		<div id="ajaxContent"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		{if $get_stats != 0}
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
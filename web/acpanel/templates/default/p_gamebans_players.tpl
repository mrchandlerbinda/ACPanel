<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>

		<ul id="select-list">
			{if $cat_addcat_id}
				<li><a href="{$home}?cat={$section_current.id}&do={$cat_addcat_id}" rel="facebox">@@add_ban@@</a></li>
			{/if}
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="t">
						<option value="0"{if $get_status == 0} selected{/if}>@@all_bans@@</option>
						<option value="1"{if $get_status == 1} selected{/if}>@@bans_active@@</option>
						<option value="2"{if $get_status == 2} selected{/if}>@@bans_passed@@</option>
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
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
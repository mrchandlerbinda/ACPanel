<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@usershop_admin_payments@@</h2>

		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						<option value="-2"{if $get_pattern == -2} selected{/if}>@@all_transactions@@</option>
						<option value="-1"{if $get_pattern == -1} selected{/if}>@@buy_mymoney@@</option>
						<option value="0"{if $get_pattern == 0} selected{/if}>@@exchange_transactions@@</option>
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
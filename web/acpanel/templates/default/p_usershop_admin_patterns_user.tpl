<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@payment_user_privileges@@</h2>

		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="t">
						<option value="0"{if $get_status == 0} selected{/if}>@@all_privileges@@</option>
						<option value="1"{if $get_status == 1} selected{/if}>@@active_privileges@@</option>
						<option value="2"{if $get_status == 2} selected{/if}>@@inactive_privileges@@</option>
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
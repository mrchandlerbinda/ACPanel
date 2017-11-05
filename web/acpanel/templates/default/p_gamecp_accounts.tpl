<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>

		<ul id="select-list">
			{if $cat_addacc_id}<li><a href="{$home}?cat={$section_current.id}&do={$cat_addacc_id}">@@add_account_link@@</a></li>{/if}
			<li>
				<form id="forma-tpl" action="" method="get">
					<select class="styled" name="t">
						<option value="0"{if $get_type == 0} selected{/if}>@@all_types@@</option>
						<option value="1"{if $get_type == 1} selected{/if}>@@auth_nick@@</option>
						<option value="2"{if $get_type == 2} selected{/if}>@@auth_ip@@</option>
						<option value="3"{if $get_type == 3} selected{/if}>@@auth_steam@@</option>
					</select>
				</form>
			</li>
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						<option value="0"{if $get_status == 0} selected{/if}>@@all_status@@</option>
						<option value="1"{if $get_status == 1} selected{/if}>@@active@@</option>
						<option value="2"{if $get_status == 2} selected{/if}>@@inactive@@</option>
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
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>

		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						<option value="0"{if $get_status == 0} selected{/if}>@@all_status@@</option>
						<option value="1"{if $get_status == 1} selected{/if}>@@opened@@</option>
						<option value="2"{if $get_status == 2} selected{/if}>@@approved@@</option>
						<option value="3"{if $get_status == 3} selected{/if}>@@rejected@@</option>
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
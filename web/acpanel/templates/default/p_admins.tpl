<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>

		{if !empty($array_servers)}
		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						<option value="0"{if $get_srv == 0} selected{/if}>@@all_admins@@</option>
						{foreach from=$array_servers key=k item=srv}
							<option value="{$k}"{if $get_srv == $k} selected{/if}>{$srv|htmlspecialchars}</option>
						{/foreach}
					</select>
				</form>
			</li>
		</ul>
		{/if}
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		{if !$iserror}
		<div id="ajaxContent"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
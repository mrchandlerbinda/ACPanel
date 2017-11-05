<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@ban_reasons@@</h2>

		<ul id="select-list">
			{if $cat_addcat_id}
				<li><a href="{$home}?cat={$section_current.id}&do={$cat_addcat_id}" rel="facebox">@@add_reason@@</a></li>
			{/if}
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="t">
						<option value="0"{if $get_srv == 0} selected{/if}>@@all_reasons@@</option>
						{foreach from=$array_servers key=k item=srv}
							<option value="{$k}"{if $get_srv == $k} selected{/if}>{$srv.1}</option>
						{/foreach}
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
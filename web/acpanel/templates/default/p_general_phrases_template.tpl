<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>

		{if $cat_addtpl_id}
		<ul>
			<li><a href="{$home}?cat={$section_current.id}&do={$cat_addtpl_id}" rel="facebox">@@add_template@@</a></li>
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						<option value="0"{if $get_product == 0} selected{/if}>@@all_templates@@</option>
						<option value="ACPanel"{if $get_product == 'ACPanel'} selected{/if}>ACPanel</option>
						{foreach from=$array_product key=k item=ttl}
							<option value="{$k}"{if $get_product == $k} selected{/if}>{$k}</option>
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
		<div id="ajaxContent"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
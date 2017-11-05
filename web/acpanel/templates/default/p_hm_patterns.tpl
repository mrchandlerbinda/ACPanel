<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@hud_manager@@</h2>

		{if $cat_addptrn_id}
			<ul id="select-list">
				<li><a href="?cat={$section_current.id}&do={$cat_addptrn_id}" rel="facebox">@@add_pattern@@</a></li>
			</ul>
		{/if}
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		<div id="ajaxContent"></div>
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
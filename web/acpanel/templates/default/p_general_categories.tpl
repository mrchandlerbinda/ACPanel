<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title}</h2>

		{if $cat_addcat_id}
		<ul>
			<li><a href="{$home}?cat={$section_current.id}&do={$cat_addcat_id}" rel="facebox">@@add_cat@@</a></li>
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
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
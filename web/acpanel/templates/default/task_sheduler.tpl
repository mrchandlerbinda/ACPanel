<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@task_sheduler@@</h2>

		{if $cat_addtask_id}
		<ul>
			<li><a href="{$home}?cat={$section_current.id}&do={$cat_addtask_id}" rel="facebox">@@add_task@@</a></li>
		</ul>
		{/if}
	</div>
	<div class="block_content">
		{if $timeleft}<div class="message info"><p>@@cache_timeleft@@ {$timeleft}</p></div>{/if}
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
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title}</h2>

		{if $edit_access}
		<ul id="editpage">
			<li><a href="#" onclick="return edit_page()">@@edit@@</a></li>
		</ul>
		{/if}
	</div>
	<div class="block_content">
		<div class="accessMessage"></div>
		<div id="rules" style="padding-bottom: 20px;">
			{$page_content}
		</div>
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$block_head}</h2>
	</div>
	<div class="block_content">
	{/if}
		<div>{$block_content}</div>
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>

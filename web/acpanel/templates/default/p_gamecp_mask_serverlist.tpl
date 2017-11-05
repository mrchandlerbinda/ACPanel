<div>
	{if $iserror}
		<div class="message warning"><p>{$iserror}</p></div>
	{else}
		<ul>
			{foreach from=$mask_servers item=mask key=k}
				<li>{$mask|htmlspecialchars}</li>
			{/foreach}
		</ul>
	{/if}
</div>
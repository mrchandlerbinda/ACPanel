<div style="width: 600px;">
	<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@game_shop_servers@@</h3>
	{if $iserror}
		<div class="message errormsg"><p>{$iserror}</p></div>
	{else}
		{if $servers.name}<div style="margin-top:0;" class="message info"><p>@@item@@: "{$servers.name|htmlspecialchars}"</p></div>{/if}
		{if !empty($servers.servers)}
			<ul>
				{foreach from=$servers.servers item=srv}
					<li>{$srv}<li>
				{/foreach}
			</ul>
		{else}
			<div class="message errormsg"><p>@@servers_not_found@@</p></div>
		{/if}
	{/if}
</div>
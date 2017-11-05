<div class="block middle left">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@server@@ #{$server_fields.id}</h2>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		<div id="ajaxContent">
			<div class="center-img-block">
				<img src="acpanel/images/ajax-big-loader.gif" alt="@@loading@@" />
			</div>
		</div>
	</div>
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>

<div class="block small right">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@edit@@</h2>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		<div>
			<form id="forma-edit" action="" method="post">
				<div style="padding-bottom:15px; position:relative;">
					<img class="gtype-img" src="acpanel/images/games/{$server_fields.gametype}.png" alt="" />
					<select class="styled" name="gametype">
						{foreach from=$gtypes key=k item=type}
							<option value="{$k}"{if $k == $server_fields.gametype} selected{/if}>{$type.name}</option>
						{/foreach}
					</select>
				</div>
				<p>
					<label>@@address@@</label><br />
					<input type="hidden" class="text" name="serverid" value="{$server_fields.id}" />
					<input type="hidden" class="text" name="real_address" value="{$server_fields.address|htmlspecialchars}" />
					<input type="hidden" class="text" name="real_gametype" value="{$server_fields.gametype}" />
					<input type="text" class="text small" name="address" value="{$server_fields.address|htmlspecialchars}" />
				</p>
				<p style="padding: 0; margin: 0;">
					<label>@@hostname@@</label>
				</p>
				<p class="p-load">
					<input type="text" class="text small" name="hostname" value="{$server_fields.hostname|htmlspecialchars}" /><span class="ajaxImg"></span>
				</p>
				<p>
					<label>@@server_description@@</label><br />
					<textarea class="text" name="description">{$server_fields.description|htmlspecialchars}</textarea>
				</p>
				<p>
					<label>@@server_active@@</label><br />
					<input class="radio" type="radio" name="active" value="1"{if $server_fields.active} checked="checked"{/if} /> @@yes@@&nbsp;
					<input class="radio" type="radio" name="active" value="0"{if !$server_fields.active} checked="checked"{/if} /> @@no@@
				</p>
				<p>
					<label>@@added_user@@</label><br />
					<input type="text" class="text small" name="userid" value="{$server_fields.username}" />
				</p>
				<p>
					<label>@@added_date@@</label><br />
					<input type="text" class="text date_picker" name="timestamp" value="{$server_fields.timestamp}" />
				</p>
				<p>
					<label>@@rating@@</label><br />
					<input type="text" class="text small" name="rating" value="{$server_fields.rating}" />
				</p>
				<p>
					<label>@@server_vip_time@@</label><br />
					<input type="text" class="text date_picker" name="vip" value="{$server_fields.vip}" />
				</p>
				{if !empty($server_options)}
					{foreach from=$server_options item=item}
						<p>
							<label>{$item.description}</label><br />
							{$item.content}
						</p>
					{/foreach}	
				{/if}
				<p>
					<input type="submit" class="submit mid" value="@@save@@" />
				</p>
			</form>
		</div>
	</div>
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
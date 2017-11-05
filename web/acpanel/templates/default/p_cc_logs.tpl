<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>
	</div>
	<div class="block_content">
		{if $iserror}
			<div class="message errormsg"><p>{$iserror}</p></div>
		{else}
			<div class="accessMessage"></div>
			<form id="forma-search" action="{$action}" method="get">
				<input type='hidden' name="cat" value="{$section_current.id}" />
				<input type='hidden' name="do" value="{$cat_current.id}" />
				<p>
					<label>@@server@@</label><br />
					<select class="styled" name="server">
						<option value="all" selected="selected">@@all_servers@@</option>
						{foreach from=$servers item=n}
							<option value="{$n.address}">{$n.hostname}</option>
						{/foreach}
					</select>
				</p>
				<p>
					<label>@@from@@</label>
					<input type="text" name="begindate" class="text date_picker" />
					&nbsp;&nbsp;
					<label>@@totime@@</label>
					<input type="text" name="enddate" class="text date_picker" />
				</p>
				<p>
					<label>@@msg_type@@</label><br />
					<select class="styled" name="msgtype">
						<option value="all" selected>@@all_messages@@</option>
						<option value="say">@@chat_public@@</option>
						<option value="say_team">@@chat_team@@</option>
						<option value="amx_chat">@@chat_admin@@</option>
					</select>
				</p>
				<p>
					<label>@@msg_status@@</label><br />
					<select class="styled" name="msgstatus">
						<option value="all" selected>@@all_messages@@</option>
						<option value="-1">@@msg_free@@</option>
						<option value="0">@@msg_white@@</option>
						<option value="1">@@msg_hide@@</option>
						<option value="2">@@msg_ban@@</option>
						<option value="3">@@msg_kick@@</option>
						<option value="4">@@msg_notice@@</option>
					</select>
				</p>
				<p>
					<label>@@player_nick@@</label><br />
					<input type="text" name="player_nick" class="text" />
				</p>
				<p>
					<label>@@player_steam@@</label><br />
					<input type="text" name="player_id" class="text" />
				</p>
				<p>
					<label>@@player_ip@@</label><br />
					<input type="text" name="player_ip" class="text" />
				</p>
				<p>
					<label>@@keyword@@</label><br />
					<input type="text" name="message" class="text" />
				</p>
				<p>
					<input type="submit" class="submit small" value="@@search@@" />
					&nbsp;&nbsp;
					<input type="button" class="submit small" value="@@delete@@" />
				</p>
			</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
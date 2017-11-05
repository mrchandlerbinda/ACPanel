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
					<select class="styled" name="server_ip">
						<option value="all" selected="selected">@@all_servers@@</option>
						{foreach from=$servers item=n}
							<option value="{$n.address}">{$n.hostname}</option>
						{/foreach}
					</select>
				</p>
				<p>
					<label>@@search_vote_type@@</label><br />
					<select class="styled" name="vote_type">
						<option value="all" selected="selected">@@all_types@@</option>
						<option value="ban">@@vote_for_ban@@</option>
						<option value="kick">@@vote_for_kick@@</option>
					</select>
				</p>
				<p>
					<label>@@search_vote_result@@</label><br />
					<select class="styled" name="vote_result">
						<option value="all" selected="selected">@@all_results@@</option>
						<option value="1">@@vote_only_success@@</option>
						<option value="0">@@vote_only_failed@@</option>
					</select>
				</p>
				<p>
					<label>@@from@@</label>
					<input type="text" name="startdate" class="text date_picker" />
					&nbsp;&nbsp;
					<label>@@totime@@</label>
					<input type="text" name="enddate" class="text date_picker" />
				</p>
				<p>
					<label>@@vote_player_nick@@</label><br />
					<input type="text" name="vote_player_nick" class="text" />
				</p>
				<p>
					<label>@@vote_player_steam@@</label><br />
					<input type="text" name="vote_player_id" class="text" />
				</p>
				<p>
					<label>@@vote_player_ip@@</label><br />
					<input type="text" name="vote_player_ip" class="text" />
				</p>
				<p>
					<label>@@nom_player_nick@@</label><br />
					<input type="text" name="nom_player_nick" class="text" />
				</p>
				<p>
					<label>@@nom_player_steam@@</label><br />
					<input type="text" name="nom_player_id" class="text" />
				</p>
				<p>
					<label>@@nom_player_ip@@</label><br />
					<input type="text" name="nom_player_ip" class="text" />
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
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
					<label>@@user_login@@</label><br />
					<input type="text" name="user_login" class="text" />
				</p>
				<p>
					<label>@@user_group@@</label><br />
					<select class="styled" name="user_group">
						<option value="all" selected="selected">@@all_groups@@</option>
						{foreach from=$groups item=n}
							<option value="{$n.usergroupid}">{$n.usergroupname}</option>
						{/foreach}
					</select>
				</p>
				<p>
					<label>@@reg_ip@@</label><br />
					<input type="text" name="reg_ip" class="text" />
				</p>
				<p>
					<label>@@from@@</label>
					<input type="text" name="reg_date_begin" class="text date_picker" />
					&nbsp;&nbsp;
					<label>@@totime@@</label>
					<input type="text" name="reg_date_end" class="text date_picker" />
					 <--- <label>@@reg_date@@</label>
				</p>
				<p>
					<label>@@from@@</label>
					<input type="text" name="last_date_begin" class="text date_picker" />
					&nbsp;&nbsp;
					<label>@@totime@@</label>
					<input type="text" name="last_date_end" class="text date_picker" />
					 <--- <label>@@last_visit@@</label>
				</p>
				<p>
					<label>@@user_hid@@</label><br />
					<input type="text" name="user_hid" class="text" />
				</p>
				<p>
					<label>@@user_mail@@</label><br />
					<input type="text" name="user_mail" class="text" />
				</p>
				<p>
					<label>@@user_icq@@</label><br />
					<input type="text" name="user_icq" class="text" />
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
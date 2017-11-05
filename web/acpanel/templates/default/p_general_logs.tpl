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
					<label>@@action@@</label><br />
					<select class="styled" name="action">
						<option value="all" selected="selected">@@all_actions@@</option>
						{foreach from=$actions item=n}
							<option value="{$n}">{$n}</option>
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
					<label>@@user_login@@</label><br />
					<input type="text" name="user_login" class="text" />
				</p>
				<p>
					<label>@@user_ip@@</label><br />
					<input type="text" name="user_ip" class="text" />
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
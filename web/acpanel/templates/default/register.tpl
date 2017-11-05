<div class="block small center login">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>
		<h2>@@register_head@@</h2>

		<ul>
			<li><a href="?do=login">@@login@@</a></li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message errormsg"><p>{$iserror}</p></div>
			{/if}
		</div>
		{if !$iserror}
		<div class="message info"><p>@@register_info@@</p></div>
			<form id="regform" action="{$action}" method="post">
				<p>
					<label>@@login_user@@</label> <br />
					<input name="uname" type="text" class="text" value="" />
				</p>
				<p>
					<label>@@email_user@@</label> <br />
					<input name="umail" type="text" class="text" value="" />
				</p>
				<p>
					<label>@@login_pass@@</label> <br />
					<input name="passwd" type="password" class="text" value="" />
				</p>
				<p>
					<label>@@login_pass_check@@</label> <br />
					<input name="passwd_check" type="password" class="text" value="" />
				</p>
				<div class="QapTcha"></div>
				<p>
					<input type="submit" class="submit mid" value="@@reg_submit@@" /> &nbsp;
				</p>
			</form>
		{/if}
	</div>
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>

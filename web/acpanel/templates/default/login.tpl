{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#recovery-pass').live('click', function() {
			$('#recovery-pass-block').fadeIn('slow');
			return false;
		});	
	});
</script>
{/literal}
<div class="block small center login">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>
		<h2>@@login_head@@</h2>

		{if $reg_type != 1 && !$site_offline}
		<ul>
			<li><a href="?do=register">@@register@@</a></li>
		</ul>
		{/if}
	</div>
	<div class="block_content">
		{if !$iserror}
		<div class="message info"><p>@@login_info@@</p></div>
		{else}
		<div class="message errormsg"><p>{$iserror}</p></div>
		{/if}
		<form name="loginform" action="{$action}" method="post">
			<p>
				<label>@@login_user@@</label> <br />
				<input name="user" type="text" class="text" value="" />
			</p>
			<p>
				<label>@@login_pass@@</label> <br />
				<input name="password" type="password" class="text" value="" />
			</p>
			<p>
				<input name="login" type="submit" class="submit" value="@@login_submit@@" /> &nbsp; <a id="recovery-pass" href="#">@@lost_password@@</a>
			</p>
		</form>
		<div id="recovery-pass-block" style="display:none;">
			<div style="margin: 0 -20px 15px;border-top: 1px solid #CCCCCC;"></div>
			<div class="message info"><p>@@recovery_password_info@@</p></div>
			<form name="recoveryform" action="{$action_recovery}" method="post">
				<p>
					<label>@@email@@</label> <br>
					<input name="email" type="text" class="text" value="">
				</p>
				<p>
					<input name="recovery" type="submit" class="submit" value="@@send@@">
				</p>
			</form>
		</div>
	</div>
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>

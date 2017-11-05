{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {
			if( $('input[name="username"]').val() == '' )
			{				$('.infoMsg').html('@@dont_empty@@');
			}
			else
			{				$('.infoMsg').html('');
				var data = $(this).serialize();

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_users',
					data:data + '&go=5',
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							$('.accessMessage').html('');
							humanMsg.displayMsg(result,'success');
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}
					}
				});
			}

			return false;
		});

		$('input:button').click(function() {
			window.location.href = "{/literal}{$home}?cat={$cat_accounts}&do={$cat_account_add}&u={$array_user.username}{literal}";
		});
	});
</script>
{/literal}
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title} #{$array_user.uid}</h2>

		<ul>
			<li><a href="{$action_uri}">@@back_url@@</a></li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
		{if $iserror}
			<div class="message warning"><p>{$iserror}</p></div>
		{/if}
		</div>
		{if !$iserror}
		<form id="forma-edit" action="" method="post">
			<div class="left-float-box">
				<div class="left-box-content">
					<p>
						<label>@@username@@</label><br />
						<input type="hidden" class="text" name="uid" value="{$array_user.uid}" />
						<input type="text" class="text small" name="username" value="{$array_user.username|htmlspecialchars}" autocomplete="off" /><span class="infoMsg note error"></span>
					</p>
					<p>
						<label>@@password@@</label><br />
						<input type="password" class="text small" name="password" value="" autocomplete="off" />
					</p>
					<p>
						<label>@@usergroup@@</label><br />
						<select class="styled" name="usergroupid">
								{foreach from=$array_groups item=group}
									<option value="{$group.usergroupid}"{if $array_user.usergroupid == $group.usergroupid} selected{/if}>{$group.usergroupname}</option>
								{/foreach}
						</select>
					</p>
					<p>
						<label>@@timezone@@</label><br />
						<select class="styled" name="timezone">
								{foreach from=$array_tz key=k item=tz}
									<option value="{$k}"{if $array_user.timezone == $k} selected{/if}>{$tz}</option>
								{/foreach}
						</select>
					</p>
					<p>
						<label>@@reg_date@@</label><br />
						<input type="text" class="text date_picker" name="reg_date" value="{$array_user.reg_date}" />
					</p>
					<p>
						<label>@@last_visit@@</label><br />
						<input type="text" class="text date_picker" name="last_visit" value="{$array_user.last_visit}" />
					</p>
					<p>
						<label>@@reg_ip@@</label><br />
						<input type="text" class="text small" name="ipaddress" value="{$array_user.ipaddress}" />
					</p>
					<p>
						<label>@@hid@@</label><br />
						<input type="text" class="text small" name="hid" value="{$array_user.hid}" />
					</p>
					<p>
						<label>@@email@@</label><br />
						<input type="text" class="text small" name="mail" value="{$array_user.mail}" />
					</p>
					<p>
						<label>@@icq@@</label><br />
						<input type="text" class="text small" name="icq" value="{$array_user.icq}" />
					</p>
					<p>
						<input type="submit" class="submit mid" value="@@save@@" />
					</p>
				</div>
			</div>
			<div class="right-float-box">
				<p align="center" class="profile-link">
					<img style="margin:0;padding:7px;" src="{$array_user.avatar}">
				</p>
				{if empty($account)}
					{if !$ga_false}
						<p align="center">
							<input style="margin:0;" type="button" class="submit long" value="@@create_account@@">
						</p>	
					{/if}				
				{else}
					<p align="center">
						<a href="{$home}?cat={$cat_accounts}&do={$cat_account_edit}&id={$account.userid}"><b>@@user_account@@</b></a>
					</p>	
					<div id="userInfoBox">
						<ul>
							<li><b>@@acc_status@@</b> {if $account.approved == 'yes'}@@active@@{else}@@inactive@@{/if}</li>
							<li><b>@@acc_reg_date@@</b> {$account.timestamp}</li>
							<li><b>@@acc_last_online@@</b> {$account.last_time}</li>
							<li><b>@@acc_auth@@</b> {if $account.flag == 1}@@auth_by_nick@@{elseif $account.flag == 2}@@auth_by_ip@@{else}@@auth_by_steam@@{/if}</li>
							<li><b>{if $account.flag == 1}@@player_nick@@{elseif $account.flag == 2}@@player_ip@@{else}@@player_steam@@{/if}</b> {if $account.flag == 1}{$account.player_nick}{elseif $account.flag == 2}{$account.player_ip}{else}{$account.steamid}{/if}</li>
							<li><b>@@acc_online@@</b> {$account.online}</li>
							<li><b>@@acc_points@@</b> {$account.points}</li>
						</ul>
					</div>
				{/if}
			</div>
		</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
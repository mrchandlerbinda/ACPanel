{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('form#forma-account select.styled').select_skin();

		$('#forma-account select[name="flag"]').change(function () {
			if($(this).val() == 1)
			{
				$('.player-ip').addClass('hide');
				$('.player-ip input').attr('disabled','disabled');
				$('.player-steam').addClass('hide');
				$('.player-steam input').attr('disabled','disabled');
				$('.player-nickpass').removeClass('hide');
				$('.player-nickpass input[name="player_nick"]').removeAttr('disabled');
				$('.player-nickpass input[name="password"]').removeAttr('disabled');
			}
			else if($(this).val() == 2)
			{
				$('.player-ip').removeClass('hide');
				$('.player-ip input').removeAttr('disabled');
				$('.player-steam').addClass('hide');
				$('.player-steam input').attr('disabled','disabled');
				$('.player-nickpass').addClass('hide');
				$('.player-nickpass input[name="player_nick"]').attr('disabled','disabled');
				$('.player-nickpass input[name="password"]').attr('disabled','disabled');
			}
			else
			{
				$('.player-steam').removeClass('hide');
				$('.player-steam input').removeAttr('disabled');
				$('.player-ip').addClass('hide');
				$('.player-ip input').attr('disabled','disabled');
				$('.player-nickpass').addClass('hide');
				$('.player-nickpass input[name="player_nick"]').attr('disabled','disabled');
				$('.player-nickpass input[name="password"]').attr('disabled','disabled');
			}
		});

		$('#forma-account').submit(function() {
			$.blockUI({ message: null });
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:data + '&go=18',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						rePagination(1);
						$.unblockUI({ 
							onUnblock: function() {
								refreshAccount();
								$('#facebox .close').click();
								humanMsg.displayMsg(result,'success');
							} 
						});
					}
					else if(result.indexOf('id="error"') + 1)
					{
						$.unblockUI({ 
							onUnblock: function() { humanMsg.displayMsg(result,'error'); } 
						});
					}
					else
					{
						$.unblockUI({ 
							onUnblock: function() {
								$('#forma-account input[name="password"]').val('');
								humanMsg.displayMsg(result,'success');
							} 
						});					
					}
				}
			});

			return false;
		});
	});
</script>
{/literal}
<div style="width: 600px;">
	{if $iserror}
		<div class="message errormsg"><p>{$iserror}</p></div>
	{/if}
	{if !empty($iswarn)}
		{foreach from=$iswarn item=w}
			<div class="message warning"><p>{$w}</p></div>
		{/foreach}
	{/if}
	{if !$iserror}
		<form id="forma-account" action="" method="post">
			<p class="p-profile">
				<label>@@auth_type@@</label><br />
				<select class="styled" name="flag">
						{if in_array('by_nick', $ga_access_type) OR $account.flag == 1}<option value="1"{if $account.flag == 1 OR !$account_status} selected{/if}>@@auth_nick@@</option>{/if}
						{if in_array('by_ip', $ga_access_type) OR $account.flag == 2}<option value="2"{if $account.flag == 2 OR (!$account_status AND !in_array('by_nick', $ga_access_type))} selected{/if}>@@auth_ip@@</option>{/if}
						{if in_array('by_steam', $ga_access_type) OR $account.flag == 3}<option value="3"{if $account.flag == 3 OR (!$account_status AND !in_array('by_ip', $ga_access_type) AND !in_array('by_nick', $ga_access_type))} selected{/if}>@@auth_steam@@</option>{/if}
				</select>
			</p>
			{if in_array('by_nick', $ga_access_type) OR $account.flag == 1}
				<p class="p-profile player-nickpass{if $account.flag != 1 AND $account_status} hide{/if}">
					<label>@@player_nick_password@@</label><br />
					<input type="text" class="text tiny" name="player_nick" value="{$account.player_nick}" autocomplete="off"{if $account_status AND ($account.flag != 1 OR $account_status == 1 OR $account_status == 2)} disabled="disabled"{/if} />&nbsp;<input type="password" class="text tiny" name="password" value="" autocomplete="off"{if  $account_status AND ($account.flag != 1 OR $account_status == 1 OR $account_status == 2)} disabled="disabled"{/if} />
				</p>
			{/if}
			{if in_array('by_ip', $ga_access_type) OR $account.flag == 2}
				<p class="p-profile player-ip{if $account.flag != 2 AND ($account_status OR in_array('by_nick', $ga_access_type))} hide{/if}">
					<label>@@player_ip@@</label><br />
					<input type="text" class="text small" name="player_ip" value="{$account.player_ip}"{if ($account.flag != 2 OR $account_status == 1 OR $account_status == 2) AND ($account_status OR in_array('by_nick', $ga_access_type))} disabled="disabled"{/if} />
				</p>
			{/if}
			{if in_array('by_steam', $ga_access_type) OR $account.flag == 3}
				<p class="p-profile player-steam{if $account.flag != 3 AND ($account_status OR in_array('by_ip', $ga_access_type) OR in_array('by_nick', $ga_access_type))} hide{/if}">
					<label>@@player_steam@@</label><br />
					<input type="text" class="text small" name="steamid" value="{$account.steamid}"{if ($account.flag != 3 OR $account_status == 1 OR $account_status == 2) AND ($account_status OR in_array('by_ip', $ga_access_type) OR in_array('by_nick', $ga_access_type))} disabled="disabled"{/if} />
				</p>
			{/if}
			<p class="p-profile">
				<input name="userid" type="hidden" class="text" value="{$account.userid}" />
				<input type="submit" class="submit mid" value="@@save@@" /> &nbsp;
			</p>
		</form>
	{/if}
</div>
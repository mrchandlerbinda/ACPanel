{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('a[rel*=facebox], input[rel*=facebox]').facebox();
	});
</script>
{/literal}
<div class="accessMessage">
	{if $account_status != 0 AND $account_status != 1}
		<div class="message info"><p>@@acc_created@@ {$account.timestamp} | @@acc_online@@ {$account.last_time} | @@acc_online_all@@ {$account.online}</p></div>
	{/if}
	{if $iserror}
		<div class="message errormsg"><p>{$iserror}</p></div>
	{/if}
	{if !empty($iswarn)}
		{foreach from=$iswarn item=w}
			<div class="message warning"><p>{$w}</p></div>
		{/foreach}
	{/if}
</div>
<div>
	{if $account_status != 0 AND $account_status != 1}
		<ul style="padding-bottom:0;">
			<li>@@auth_type@@ {if $account.flag == 1}@@auth_nick@@{elseif $account.flag == 2}@@auth_ip@@{else}@@auth_steam@@{/if}</li>
			<li>{if $account.flag == 1}@@player_nick@@ {$account.player_nick}{elseif $account.flag == 2}@@player_ip@@ {$account.player_ip}{else}@@player_steam@@ {$account.steamid}{/if}</li>
		</ul>
	{/if}
	{if $account_status >= 0}
		<p class="p-profile{if $account_status == 1 OR $account_status == 2} hide{/if}" style="padding-top:15px;padding-bottom:15px;">
			<input type="button" class="submit long" value="{if $account_status < 2}@@create_account@@{else}@@edit_account_info@@{/if}" href="acpanel.php?do=profile&s=2&account=edit" rel="facebox" />
			<a style="border:0; position:absolute; left:185px; top:22px;" href="#auth-info" rel="facebox"><img src="acpanel/images/question.png" alt="" /></a>	
		</p>
		<div id="auth-info" style="display: none; width: 600px;">@@auth_type_info@@</div>
	{/if}
</div>
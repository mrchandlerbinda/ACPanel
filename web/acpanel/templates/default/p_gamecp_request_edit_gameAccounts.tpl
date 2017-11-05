{if !empty($ticket.duplicate)}
	<div class="message warning"><p>@@user_hid_duplicate@@ {foreach from=$ticket.duplicate item=u name=users}<a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$u.uid}">{$u.username|htmlspecialchars}</a>{if !$smarty.foreach.users.last}, {/if}{/foreach}</p></div>
{/if}
<ul>
	<li>@@ticket_date@@ {$ticket.timestamp}</li>
	<li>@@ticket_elapsed@@ {$ticket.elapsed}</li>
	{if $ticket.ticket_status > 0}
		<li>@@ticket_moderator@@ {$ticket.closed_admin|htmlspecialchars}</li>
		{if $ticket.comment}
			<li>@@ticket_comment@@ {$ticket.comment}</li>
		{/if}
	{/if}
	<li>@@ticket_user@@ <a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$ticket.userid}">{$ticket.username|htmlspecialchars}</a></li>
	{if $ticket.ticket_type < 4 OR $ticket.ticket_type > 6}
		<li>@@ticket_flag@@ {if $ticket.fields_update.flag == 1}@@by_nick@@{elseif $ticket.fields_update.flag == 2}@@by_ip@@{else}@@by_steam@@{/if}{if $ticket.ticket_type > 3} <span class="note">(@@flag_curent@@ {if $ticket.fields_update.flag_old == 1}@@by_nick@@{elseif $ticket.fields_update.flag_old == 2}@@by_ip@@{else}@@by_steam@@{/if})</span>{/if}</li>
	{/if}
	<li>{if $ticket.fields_update.flag == 1}@@ticket_flag_nick@@ {$ticket.fields_update.player_nick}{elseif $ticket.fields_update.flag == 2}@@ticket_flag_ip@@ {$ticket.fields_update.player_ip}{else}@@ticket_flag_steam@@ {$ticket.fields_update.steamid}{/if}{if $ticket.ticket_type > 3 AND $ticket.ticket_type < 7} <span class="note">(@@curent_value@@ {if $ticket.fields_update.flag_old == 1}{$ticket.fields_update.player_nick_old}{elseif $ticket.fields_update.flag_old == 2}{$ticket.fields_update.player_ip_old}{else}{$ticket.fields_update.steamid_old}{/if})</span>{/if}</li>
</ul>
{if $acp_bans}
<h3 class="find-bans">@@find_possible_bans@@</h3>
<div class="br-tag"></div>
{/if}
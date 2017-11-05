{literal}
<script type='text/javascript'>
	{/literal}{if $acp_bans}{literal}
	function checkBans() 
	{
		jQuery.ajax({
			type:'POST',
			dataType:'json',
			url:'acpanel/ajax.php?do=ajax_gamecp',
			data:({'nick' : '{/literal}{$ticket.player_nick}{literal}','ip' : '{/literal}{$ticket.player_ip}{literal}','steam' : '{/literal}{$ticket.player_steam}{literal}','go' : 27}),
			success:function(result) {
				if( result.all > 0 )
				{
					jQuery('.find-bans').addClass('warning').html(result.str);
					jQuery('<div>').addClass('bans-list').html('<ul></ul>').insertAfter(jQuery('.find-bans'));
					if( result.nick > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_nick@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&player_nick=!{$ticket.player_nick}{literal}">' + result.nick + ' (' + result.nick_a + ')</a>'));
					if( result.ip > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_ip@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&player_ip={$ticket.player_ip}{literal}">' + result.ip + ' (' + result.ip_a + ')</a>'));
					if( result.cookie > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_cookie@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&cookie_ip={$ticket.player_ip}{literal}">' + result.cookie + ' (' + result.cookie_a + ')</a>'));
					if( result.steam > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_steam@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&player_id={$ticket.player_steam}{literal}">' + result.steam + ' (' + result.steam_a + ')</a>'));
					jQuery('.br-tag').remove();
				}
				else
				{
					jQuery('.find-bans').addClass('success').html(result.str);
				}
			}
		});
	}
	{/literal}{/if}{literal}

	jQuery(document).ready(function($) {
		{/literal}{if $acp_bans}{literal}
		checkBans();
		{/literal}{/if}{literal}

		$('input[name="approved"], input[name="disapproved"]').click(function() {
			var action = $(this).attr('name');
			var name = prompt('@@ticket_moderator_comment@@');
			if( name != null )
			{
				$.blockUI({ message: null });

				var arr = new Array();
				arr.push($('input[name="ticket_id"]').val());

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_gamecp',
					data:({'marked_word[]' : arr,'go' : (action == 'approved') ? 22 : 23,'comment' : name,'username' : $('input[name="uname"]').val()}),
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							rePagination(0);
							$('table').trigger('update');
							$('table').trigger('applyWidgets', 'zebra');

							$.unblockUI({ 
								onUnblock: function() {
									$('#facebox .close').click();
									humanMsg.displayMsg(result,'success');
								} 
							});
						}
						else
						{
							if( action == 'approved' )
							{
								rePagination(0);
								$('table').trigger('update');
								$('table').trigger('applyWidgets', 'zebra');
							}

							$.unblockUI({ 
								onUnblock: function() {
									$('#facebox .close').click();
									humanMsg.displayMsg(result,'error');
								} 
							});								
						}
					}
				});
			}

			return false;
		});

		$('input[name="deleted"]').click(function() {
			var tid = $('input[name="ticket_id"]').val();

			if (confirm('@@question_delete_ticket@@'))
			{
				$.blockUI({ message: null });

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_gamecp',
					data:({id : tid,'go' : 20}),
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							rePagination(-1);
							$('table').trigger('update');
							$('table').trigger('applyWidgets', 'zebra');

							$.unblockUI({ 
								onUnblock: function() {
									$('#facebox .close').click();
									humanMsg.displayMsg(result,'success');
								} 
							});
						}
						else
						{
							$.unblockUI({ 
								onUnblock: function() {
									humanMsg.displayMsg(result,'error');
								} 
							});
						}
					}
				});
			}

			return false;
		});
	});
</script>
{/literal}
<div style="width: 600px;">
	{if $iserror}
		<div class="message errormsg"><p>{$iserror}</p></div>
	{else}
		<h3 style="padding-left: 18px; border-bottom: 1px solid #ddd; margin-bottom: 10px; background: url(acpanel/images/status_{if $ticket.ticket_status == 1}on{elseif $ticket.ticket_status == 2}red{else}off{/if}.png) left center no-repeat;">{$ticket.ticket_type_head}</h3>
		{if $ticket.ticket_status > 0}
			<div class="message warning"><p>@@ticket_closed@@</p></div>
		{/if}
		{assign var="product" value=$ticket.productid}
		{include file="p_gamecp_request_edit_$product.tpl"}
		<p>
			<input type="hidden" name="ticket_id" value="{$ticket.id}" />
			{if $ticket.ticket_status == 0}
				<input type="button" class="submit mid" name="approved" value="@@ticket_approved@@" />&nbsp;<input type="button" class="submit mid" name="disapproved" value="@@ticket_disapproved@@" />&nbsp;
			{/if}
			<input type="button" class="submit mid" name="deleted" value="@@ticket_deleted@@" />
		</p>
	{/if}
</div>
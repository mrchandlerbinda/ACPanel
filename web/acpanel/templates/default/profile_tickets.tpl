{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$('.tablesorter a[rel*=facebox]').facebox()
		});
	})(jQuery);

	function remove_ticket(subjm,txt)
	{
		if (confirm(txt))
		{
			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:({id : subjm,'go' : 16}),
				success:function(result) {
					if( result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
						rePagination(-1);
						jQuery('table').trigger('update');
						jQuery('table').trigger('applyWidgets', 'zebra');
						refreshAccount();
					}
					else
					{
						humanMsg.displayMsg(result,'error');
					}
				}
			});
		}
		return false;
	}

	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra'],
			headers: {2:{sorter: false}}
		});
	});
</script>
{/literal}
<h2 style="border-top:1px dashed #999; margin-top:10px; padding-top:10px;">@@my_tickets@@</h2>
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>@@ticket_date@@</th>
			<th>@@ticket_action@@</th>
			<td>&nbsp;</td>
		</tr>
	</thead>

	<tbody>
	{foreach from=$tickets item=ticket}
		<tr>
			<td>{$ticket.timestamp}</td>
			<td>{$ticket.ticket_type}</td>
			<td style="text-align: right;">
				{if $ticket.ticket_status == 1}@@ticket_approved@@ (<a href="#ticket-details-{$ticket.id}" rel="facebox">@@ticket_details@@</a>){elseif $ticket.ticket_status == 2}@@ticket_denied@@ (<a href="#ticket-details-{$ticket.id}" rel="facebox">@@ticket_details@@</a>){else}@@ticket_moderation@@ (<a href="#" onclick="return remove_ticket('{$ticket.id}','@@confirm_del@@')">@@ticket_withdraw@@</a>){/if}
				| <img class="img-status" src="acpanel/templates/{$tpl}/images/full-time.png" alt="{$ticket.elapsed}" title="{$ticket.elapsed}" />
				| <img class="img-status" src="acpanel/images/status_{if $ticket.ticket_status == 1}on{elseif $ticket.ticket_status == 2}red{else}off{/if}.png" alt="{if $ticket.ticket_status == 1}@@ticket_approved@@{elseif $ticket.ticket_status == 2}@@ticket_denied@@{else}@@ticket_moderation@@{/if}" title="{if $ticket.ticket_status == 1}@@ticket_approved@@{elseif $ticket.ticket_status == 2}@@ticket_denied@@{else}@@ticket_moderation@@{/if}" />
				<div id="ticket-details-{$ticket.id}" style="display:none;">
					<ul>
						<li><b>@@ticket_status@@:</b> {if $ticket.ticket_status == 1}<font color="green">@@ticket_approved@@</font>{elseif $ticket.ticket_status == 2}<font color="red">@@ticket_denied@@</font>{else}<font color="gray">@@ticket_moderation@@</font>{/if}</li>
						<li><b>@@ticket_date@@:</b> {$ticket.timestamp}</li>
						<li><b>@@ticket_action@@:</b> {$ticket.ticket_type}</li>
						<li><b>@@ticket_elapsed@@:</b> {$ticket.elapsed}</li>
						{if $ticket.ticket_status && $ticket.comment}<li><b>@@ticket_comment@@:</b> {$ticket.comment}</li>{/if}
					</ul>
				</div>
			</td>
		</tr>
	{/foreach}
	</tbody>
	{if empty($tickets)}
		<tfoot>
			<tr class="emptydata"><td colspan="3">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
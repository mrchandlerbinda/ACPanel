{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$('#forma a[rel*=facebox]').facebox();
		});
	})(jQuery);
	
	function remove_row(subjm,txt)
	{
		if (confirm(txt))
		{
			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:({id : subjm,'go' : 14}),
				success:function(result) {
					if( result.indexOf('id="success"') + 1)
					{
						jQuery('.accessMessage').html('');
						humanMsg.displayMsg(result,'success');
						jQuery('tr#' + subjm).remove();
						rePagination(-1);
						jQuery('table').trigger('update');
						jQuery('table').trigger('applyWidgets', 'zebra');
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
			headers: {0:{sorter: false}}
		});
	});
</script>
{/literal}
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"></th>
				<th>@@mask_flags@@</th>
				<th>@@mask_servers@@</th>
				<th>@@mask_players@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$masks item=mask}
			{assign var="id" value=$mask.mask_id}
			<tr id="{$mask.mask_id}">
				<td>#{$mask.mask_id}</td>
				<td>{$mask.access_flags}</td>
				<td>
					{if empty($mask_servers.$id)}0{elseif $mask_servers.$id.0.id == 0}@@all@@{else}
						<a href="#mask-info-{$mask.mask_id}" rel="facebox">{count($mask_servers.$id)}</a>
						<div id="mask-info-{$mask.mask_id}" style="display:none;">
							<ul>
								{foreach from=$mask_servers.$id item=v}
								<li>{$v.name}</li>
								{/foreach}
							</ul>
						</div>
					{/if}
				</td>
				<td>{if $mask.players}<a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_search}&username=&mask={$mask.mask_id}">{/if}{$mask.players}{if $mask.players}</a>{/if}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_edit}&s={$mask.mask_id}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$mask.mask_id}','@@confirm_del@@')">Delete</a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($masks)}
			<tfoot>
				<tr class="emptydata"><td colspan="6">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>
</form>
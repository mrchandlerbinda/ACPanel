{literal}
<script type='text/javascript'>
	(function ($) {
		// Check / uncheck all checkboxes
		$('.check_all').click(function() {
			$(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
		});

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
				url:'acpanel/ajax.php?do=ajax_payment',
				data:({id : subjm,'go' : 25}),
				success:function(result) {
					if( result.indexOf('id="success"') + 1)
					{
						jQuery('.accessMessage').html('');
						humanMsg.displayMsg(result,'success');
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

	function change_status(subjm)
	{
		jQuery.ajax({
			type:'POST',
			url:'acpanel/ajax.php?do=ajax_payment',
			data:({id : subjm,'go' : 27}),
			success:function(result) {
				if( result.indexOf('id="success"') + 1)
				{
					var st_old = ( jQuery('tr#' + subjm + ' .img-status').hasClass('red') ) ? 'red' : 'on';
					var st_new = ( st_old == 'on' ) ? 'red' : 'on';
					jQuery('.accessMessage').html('');
					humanMsg.displayMsg(result,'success');
					jQuery('tr#' + subjm + ' .img-status').removeClass(st_old).addClass(st_new).attr('src','acpanel/images/status_' + st_new + '.png');
				}
				else
				{
					humanMsg.displayMsg(result,'error');
				}
			}
		});
	
		return false;
	}
	
	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra'],
			headers: {0:{sorter: false}}
		});

		$('#forma').submit(function() {
			var len = $("tbody input:checked").length;
			if (len > 0)
			{
				if (confirm($('input:submit',this).val() + '?'))
				{
					var arr = new Array();
					$('tbody input:checked').each( function() {
						arr.push($(this).val());
					});
	
					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_payment',
						data:({'marked_word[]' : arr,'go' : 26}),
						success:function(result) {
							if( result.indexOf('id="success"') + 1)
							{
								$('.accessMessage').html('');
								humanMsg.displayMsg(result,'success');
								rePagination(-arr.length);
								$('table').trigger('update');
								$('table').trigger('applyWidgets', 'zebra');
							}
							else
							{
								humanMsg.displayMsg(result,'error');
							}
						}
					});
	
					$(this).find('input:checkbox').attr('checked', $(this).is(''));
				}
			} else {
				alert($('input:hidden:last',this).val());
			}
	
			return false;
		});
	});
</script>
{/literal}
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="10"><input type="checkbox" class="check_all" /></th>
				<th>@@item_name@@</th>
				<th>@@item_cost@@</th>
				<th>@@item_duration@@</th>
				<th>@@item_servers@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$items item=item}
			<tr id="{$item.id}">
				<td><input type="checkbox" name="marked_word" value="{$item.id}" /></td>
				<td>{$item.web_descr|htmlspecialchars}</td>
				<td><span class="price-pt">{$item.cost_info}</span></td>
				<td>{$item.duration}</td>
				<td>{if $item.servers AND $item.server_id != 0}<a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_srv}&id={$item.id}" rel="facebox">{/if}{if $item.server_id == 0}@@all_servers_active@@{else}{$item.servers}{/if}{if $item.servers}</a>{/if}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_edit}&id={$item.id}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$item.id}','@@confirm_del@@')">Delete</a> | <a href="#" onclick="return change_status('{$item.id}')" title="@@click_change_status@@"><img class="img-status {if $item.active == 1}on{else}red{/if}" src="acpanel/images/status_{if $item.active == 1}on{else}red{/if}.png" alt="" /></a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($items)}
			<tfoot>
				<tr class="emptydata"><td colspan="6">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
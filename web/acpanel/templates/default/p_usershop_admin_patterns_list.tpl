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
				data:({id : subjm,'go' : 14}),
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
			data:({id : subjm,'go' : 13}),
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
						data:({'marked_word[]' : arr,'go' : 15}),
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
				<th>@@pattern_name@@</th>
				<th>@@pattern_duration@@</th>
				<th>@@pattern_price@@</th>
				<th>@@pattern_limit@@</th>
				<th>@@pattern_purchased@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$patterns item=pat}
			<tr id="{$pat.id}">
				<td><input type="checkbox" name="marked_word" value="{$pat.id}" /></td>
				<td>{$pat.name|htmlspecialchars}</td>
				<td>{$pat.item_duration}{if $pat.item_duration_select && $pat.duration_type != "date"} <span style="font-weight:600;color:#007F0E;" title="@@choose_period_info@@">+</span>{/if}</td>
				<td>{if $pat.price_mm > 0}<span class="price-mm">{$pat.price_mm_info}</span>{/if}{if $pat.price_points > 0}{if $pat.price_mm > 0}<span> + </span>{/if}<span class="price-pt">{$pat.price_points_info}</span>{/if}</td>
				<td{if $pat.max_sale_items_duration == 'total' && $pat.purchased >= $pat.max_sale_items} class="td-error"{/if}>{if $pat.max_sale_items_info || $pat.max_sale_for_user}{if $pat.max_sale_items_info}<span class="max-sale">{$pat.max_sale_items_info}</span>{/if}{if $pat.max_sale_for_user}{if $pat.max_sale_items_info} | {/if}<span class="max-sale-user">{$pat.max_sale_for_user}</span>{/if}{else}@@no_limit@@{/if}</td>
				<td{if $pat.max_sale_items_duration == 'total' && $pat.purchased >= $pat.max_sale_items} class="td-error"{/if}>{$pat.purchased}</td>
				<td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_edit}&id={$pat.id}">Edit</a> | <a href="#" onclick="return remove_row('{$pat.id}','@@confirm_del@@')">Delete</a> | <a href="#" onclick="return change_status('{$pat.id}')" title="@@click_change_status@@"><img class="img-status {if $pat.active == '1'}on{else}red{/if}" src="acpanel/images/status_{if $pat.active == '1'}on{else}red{/if}.png" alt="" /></a></td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($patterns)}
			<tfoot>
				<tr class="emptydata"><td colspan="7">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@del_selected@@" />
		<input type="hidden" value="@@not_selected@@" />
	</div>
</form>
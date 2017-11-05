{literal}
<script type='text/javascript'>
	function change_active(subjm)
	{
		jQuery.ajax({
			type:'POST',
			url:'acpanel/ajax.php?do=ajax_products',
			data:({id : subjm,'go' : 3}),
			success:function(result) {
				if( result.indexOf('id="success"') + 1)
				{
					jQuery('.accessMessage').html('');
					humanMsg.displayMsg(result,'success');
					resortProducts();
				}
				else
				{
					humanMsg.displayMsg(result,'error');
				}
			}
		});

		return false;
	}

	function remove_row(subjm,txt)
	{
		if (confirm(txt))
		{
			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_products',
				data:({id : subjm,'go' : 2}),
				success:function(result) {
					if( result.indexOf('id="success"') + 1)
					{
						jQuery('.accessMessage').html('');
						humanMsg.displayMsg(result,'success');
						resortProducts();
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
</script>
{/literal}
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th>@@name@@</th>
			<th>@@version@@</th>
			<th>@@description@@</th>
			<td>&nbsp;</td>
		</tr>
	</thead>
	<tbody>
	{foreach from=$array_products item=output}
	<tr>
		<td align='left' style='border-left-width:0;{if !$output.active}text-decoration:line-through;{/if}'>{if !empty($output.url)}<a target="_blank" href="{$output.url}">{/if}{$output.title}{if !empty($output.url)}</a>{/if}</td>
		<td align='left' style='border-left-width:0;{if !$output.active}text-decoration:line-through;{/if}'>{$output.version}</td>
		<td style='border-right-width:0;{if !$output.active}text-decoration:line-through;{/if}'>{$output.description}</td>
		<td width='15%' class="delete"><a href="#" onclick="return change_active('{$output.productid}')">{if !$output.active}Enable{else}Disable{/if}</a> | <a href="#" onclick="return remove_row('{$output.productid}','@@confirm_del@@')">Delete</a></td>
	</tr>
	{/foreach}
	</tbody>
	{if empty($array_products)}
		<tfoot>
			<tr class="emptydata"><td colspan="4">@@empty_data@@</td></tr>
		</tfoot>
	{/if}
</table>
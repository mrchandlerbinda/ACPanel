{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$('.delete a[rel*=facebox]').facebox();
		});
	})(jQuery);

	function remove_row(subjm,txt)
	{
		if (confirm(txt))
		{
			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_blocks',
				data:({id : subjm,'go' : 3}),
				success:function(result) {
					if( result.indexOf('id="success"') + 1)
					{
						jQuery('.accessMessage').html('');
						humanMsg.displayMsg(result,'success');
						resortCategories();
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
		$('#forma-blocks').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_blocks',
				data:data + '&go=5',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
						resortCategories();
					}
					else
					{
						humanMsg.displayMsg(result,'error');
					}
				}
			});

			return false;
		});
	});
</script>
{/literal}
<form id="forma-blocks" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>@@order@@</th>
				<th>@@name@@</th>
				<th>@@description@@</th>
				<td>&nbsp;</td>
			</tr>
		</thead>
		<tbody>
		{foreach from=$array_blocks item=output}
		<tr>
			<td width='10%' align='left' style='border-left-width:0;'>
				<input style="width: 90%;" type="text" class="text" name="order_{$output.blockid}" value="{$output.display_order}" />
			</td>
			<td>{$output.title}</td>
			<td>{$output.description}</td>
			<td width='20%' class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.edit_id}&id={$output.blockid}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$output.blockid}','@@confirm_del@@')">Delete</a></td>
		</tr>
		{/foreach}
		</tbody>
		{if empty($array_blocks)}
			<tfoot>
				<tr class="emptydata"><td colspan="3">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>

	<div class="tableactions">
		<input type="submit" class="submit tiny" value="@@save_order@@" />
	</div>
</form>
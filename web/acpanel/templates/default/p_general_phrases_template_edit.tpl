{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_lang',
				data:data + '&go=10',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						rePagination(0);
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						humanMsg.displayMsg(result,'success');
					}
					else
					{
						humanMsg.displayMsg(result,'error');
					}
				}
			});

			return false;
		});

		// Form select styling
		$('form#forma-edit select.styled').select_skin();
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-edit" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@general_phrases_template_edit@@</h3>
		<p>
			<label>@@template_title@@</label><br />
			<input type="text" class="text small" name="lp_name" value="{$lang_edit.lp_name}" />
		</p>
		<p>
			<label>@@productid@@</label><br />
			<select class="styled" name="productid">
				<option value="ACPanel"{if $lang_edit.productid == 'ACPanel'} selected{/if}>ACPanel</option>
				{foreach from=$array_product key=k item=ttl}
					<option value="{$k}"{if $lang_edit.productid == $k} selected{/if}>{$k}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<input type="hidden" name="lp_id" value="{$lang_edit.lp_id}" />
			<input type="submit" class="submit mid" value="@@apply@@" />
		</p>
	</form>
</div>
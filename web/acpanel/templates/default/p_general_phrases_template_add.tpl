{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_lang',
				data:data + '&go=7',
				success:function(result) {
					if( result.indexOf('id="success"') + 1 )
					{
						rePagination(1);
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						$('.accessMessage').html('');
						humanMsg.displayMsg(result,'success');
						$('#forma-add input:text').not(':disabled').val('');
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
		$('form#forma-add select.styled').select_skin();
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-add" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@general_phrases_template_add@@</h3>
		<p>
			<label>@@template_title@@</label><br />
			<input type="text" class="text small" name="lp_name" />
		</p>
		<p>
			<label>@@productid@@</label><br />
			<select class="styled" name="productid">
				<option value="ACPanel" selected>ACPanel</option>
				{foreach from=$array_product key=k item=ttl}
					<option value="{$k}">{$k}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
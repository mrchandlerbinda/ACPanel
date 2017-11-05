{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_phrases',
				data:data + '&go=2',
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_phrase@@</h3>
		<p>
			<label>@@code@@</label><br />
			<input type="text" class="text" name="code" value="" /><span class="infoMsg note error"></span>
		</p>
		<p style="width: 280px;">
			<label>@@template@@</label><br />
			<select class="styled" name="tpl">
					<option value="0" selected>@@global_phrases@@</option>
				{foreach from=$array_tpl item=tpl}
					<option value="{$tpl.lp_id}">{$tpl.lp_name}</option>
				{/foreach}
			</select>
		</p>
		<p style="width: 280px;">
			<label>@@productid@@</label><br />
			<select class="styled" name="productid">
				<option value="ACPanel" selected>ACPanel</option>
				{foreach from=$array_product key=k item=ttl}
					<option value="{$k}">{$k}</option>
				{/foreach}
			</select>
		</p>
		{foreach from=$array_lang item=lang}
		<p>
			<label>@@translate@@: {$lang.lang_title}</label><br />
			<textarea name="phrase_text[{$lang.lang_code}]"></textarea>
		</p>
		{/foreach}
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
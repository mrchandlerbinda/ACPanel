{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_blocks',
				data:data + '&go=2',
				success:function(result) {
					if(result.indexOf('class="indent"') + 1)
					{						if(result.indexOf('id="success"') + 1)
						{
							humanMsg.addLogs(result);
							resortCategories();
							$('.accessMessage').html('');
							$('#forma-add input').not(':submit').val('');
							$('.infoMsg').html($('<span>').attr('style','color: green;').addClass('fadeMsg').text('@@add_success@@'));
							setTimeout(function() {
								$('.fadeMsg').fadeOut('slow', function() {
									$(this).remove();
								});
							}, 2000);
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}

					}
					else
					{
						$('.infoMsg').html(result);
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_block@@</h3>
		<p>
			<label>@@title@@</label><br />
			<input type="text" class="text" name="title" value="" /><span class="infoMsg note error"></span>
		</p>
		<p>
			<label>@@description@@</label><br />
			<input type="text" class="text" name="description" value="" />
		</p>
		<p>
			<label>@@link@@</label><br />
			<input type="text" class="text" name="link" value="" />
		</p>
		<p>
			<label>@@execute_code@@</label><br />
			<textarea name="execute_code" class="text"></textarea>
		</p>
		<p>
			<label>@@view_in_block@@</label><br />
			<input class="radio" type="radio" name='view_in_block' value="yes" checked="checked" /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='view_in_block' value="no" /> @@no@@
		</p>
		<p>
			<label>@@product@@</label><br />
			<select class="styled" name="productid">
				<option value="ACPanel" selected>ACPanel</option>
				{foreach from=$array_product key=k item=ttl}
					<option value="{$k}">{$k}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@order@@</label><span class="label-info">@@display_order_info@@</span><br />
			<input type="text" class="text" name="display_order" value="10" />
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_blocks',
				data:data + '&go=4',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.addLogs(result);
						resortCategories();
						$('.accessMessage').html('');
						$('.infoMsg').html($('<span>').attr('style','color: green;').addClass('fadeMsg').text('@@edit_success@@'));
						setTimeout(function() {
							$('.fadeMsg').fadeOut('slow', function() {
								$(this).remove();
							});
						}, 2000);
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
		$('form#forma-edit select.styled').select_skin();
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-edit" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@edit_block@@</h3>
		<p>
			<label>@@title@@</label><br />
			<input type="text" class="text" name="title" value="{$blockedit.title}" /><span class="infoMsg note error"></span>
		</p>
		<p>
			<label>@@description@@</label><br />
			<input type="text" class="text" name="description" value="{$blockedit.description}" />
		</p>
		<p>
			<label>@@link@@</label><br />
			<input type="text" class="text" name="link" value="{$blockedit.link}" />
		</p>
		<p>
			<label>@@execute_code@@</label><br />
			<textarea name="execute_code" class="text">{$blockedit.execute_code|htmlspecialchars}</textarea>
		</p>
		<p>
			<label>@@view_in_block@@</label><br />
			<input class="radio" type="radio" name='view_in_block' value="yes"{if $blockedit.view_in_block == "yes"} checked="checked"{/if} /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='view_in_block' value="no"{if $blockedit.view_in_block == "no"} checked="checked"{/if} /> @@no@@
		</p>
		<p>
			<label>@@product@@</label><br />
			<select class="styled" name="productid">
				<option value="ACPanel"{if !$blockedit.productid OR $blockedit.productid == 'ACPanel'} selected{/if}>ACPanel</option>
				{foreach from=$array_product key=k item=ttl}
					<option value="{$k}"{if $blockedit.productid == $k} selected{/if}>{$k}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@order@@</label><span class="label-info">@@display_order_info@@</span><br />
			<input type="text" class="text" name="display_order" value="{$blockedit.display_order}" />
		</p>
		<p>
			<input type="hidden" name="blockid" value="{$blockedit.blockid}" />
			<input type="submit" class="submit mid" value="@@apply@@" />
		</p>
	</form>
</div>
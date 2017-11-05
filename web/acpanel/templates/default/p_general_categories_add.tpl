{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_categories',
				data:data + '&go=2',
				success:function(result) {
					if(result.indexOf('class="indent"') + 1)
					{						if(result.indexOf('id="success"') + 1)
						{
							humanMsg.addLogs(result);
							resortCategories();
							$('.accessMessage').html('');
							$('#forma-add input:text').not(':submit').val('');
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_category@@</h3>
		<p>
			<label>@@title@@</label><br />
			<input type="text" class="text" name="title" value="" /><span class="infoMsg note error"></span>
		</p>
		<p style="width: 280px;">
			<label>@@parent@@</label><br />
			<select class="styled" name="parent">
				<option value="0" selected>@@not_parent@@</option>
				{foreach from=$array_cats item=cat}
					<option value="{$cat.categoryid}">{if $cat.catlevel > 0}---{if $cat.catlevel > 1}---{if $cat.catlevel > 2}---{/if}{/if} {/if}{$cat.translate|default:$cat.title}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@link@@</label><br />
			<input type="text" class="text" name="link" value="" />
		</p>
		<p>
			<label>@@order@@</label><br />
			<input type="text" class="text" name="order" value="10" />
		</p>
		<p style="width: 280px;">
			<label>@@show_blocks@@</label><br />
			<select multiple="multiple" name="blocks[]">
				{foreach from=$array_blocks item=block}
					<option value="{$block.blockid}">{$block.translate|default:$block.title}</option>
				{/foreach}
			</select>
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
			<label>@@url@@</label><br />
			<input type="text" class="text" name="url" value="" />
		</p>
		<p>
			<label>@@meta_description@@</label><br />
			<input type="text" class="text" name="description" value="" />
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
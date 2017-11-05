{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_categories',
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@edit_category@@</h3>
		<p>
			<label>@@title@@</label><br />
			<input type="text" class="text" name="title" value="{$catedit.title}" /><span class="infoMsg note error"></span>
		</p>
		<p style="width: 280px;">
			<label>@@parent@@</label><br />
			<select class="styled" name="parent">
				<option value="0"{if empty($catedit.sectionid)} selected{/if}>@@not_parent@@</option>
				{foreach from=$array_cats item=cat}
					<option value="{$cat.categoryid}"{if $catedit.parentid == $cat.categoryid} selected{/if}>{if $cat.catlevel > 0}---{if $cat.catlevel > 1}---{if $cat.catlevel > 2}---{/if}{/if} {/if}{$cat.translate|default:$cat.title}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@link@@</label><br />
			<input type="text" class="text" name="link" value="{$catedit.link}" />
		</p>
		<p>
			<label>@@order@@</label><br />
			<input type="text" class="text" name="order" value="{$catedit.display_order}" />
		</p>
		<p style="width: 280px;">
			<label>@@show_blocks@@</label><br />
			<select multiple="multiple" name="blocks[]">
				{foreach from=$array_blocks item=block}
					<option value="{$block.blockid}"{if $block.blockid|in_array:$catedit.show_blocks} selected{/if}>{$block.translate|default:$block.title}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@product@@</label><br />
			<select class="styled" name="productid">
				<option value="ACPanel"{if !$catedit.productid OR $catedit.productid == 'ACPanel'} selected{/if}>ACPanel</option>
				{foreach from=$array_product key=k item=ttl}
					<option value="{$k}"{if $catedit.productid == $k} selected{/if}>{$k}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@url@@</label><br />
			<input type="text" class="text" name="url" value="{$catedit.url}" />
		</p>
		<p>
			<label>@@meta_description@@</label><br />
			<input type="text" class="text" name="description" value="{$catedit.description}" />
		</p>
		<p>
			<input type="hidden" name="categoryid" value="{$catedit.categoryid}" />
			<input type="submit" class="submit mid" value="@@apply@@" />
		</p>
	</form>
</div>
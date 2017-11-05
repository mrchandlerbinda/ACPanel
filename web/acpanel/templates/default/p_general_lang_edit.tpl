{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_lang',
				data:data + '&go=5',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.addLogs(result);
						rePagination(1);
						$('.accessMessage').html('');
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
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
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-edit" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@edit_lang@@</h3>
		<p>
			<label>@@lang_title@@</label><br />
			<input type="text" class="text small" name="lang_title" value="{$lang_edit.lang_title}" />
		</p>
		<p>
			<label>@@lang_active@@</label><br />
			<input class="radio" type="radio" name='lang_active' value="yes" {if $lang_edit.lang_active == 'yes'}checked="checked"{/if} /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='lang_active' value="no" {if $lang_edit.lang_active == 'no'}checked="checked"{/if} /> @@no@@
		</p>
		<p>
			<label>@@lang_code@@</label><br />
			<input type="text" class="text small" name="lang_code" value="{$lang_edit.lang_code}" /><span class="infoMsg note error"></span>
		</p>
		<p>
			<input type="hidden" name="lang_code_temp" value="{$lang_edit.lang_code}" />
			<input type="hidden" name="lang_id" value="{$lang_edit.lang_id}" />
			<input type="submit" class="submit mid" value="@@apply@@" />
		</p>
	</form>
</div>
{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_cc_commands',
				data:data + '&go=2',
				success:function(result) {
					if(result.indexOf('class="indent"') + 1)
					{
						humanMsg.addLogs(result);
						rePagination(1);
						$('.accessMessage').html('');
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
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
	<form id="forma-add" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_command@@</h3>
		<p>
			<label>@@command@@</label><br />
			<input type="text" class="text small" name="value" /><span class="infoMsg note error"></span>
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
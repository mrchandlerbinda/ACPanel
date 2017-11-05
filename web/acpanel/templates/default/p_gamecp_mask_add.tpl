{literal}
<script type='text/javascript'>

	jQuery(document).ready(function($) {

		$('[name="servers_all"]').change(function() {
			if( $('[name="servers_all"]:checked').val() == "no" )
			{
				$('#access_servers').fadeIn('fast');
			}
			else
			{
				$('#access_servers').fadeOut('fast');
			}
		});

		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:data + '&go=13',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
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
						if(result.indexOf('id="error"') + 1)
						{
							humanMsg.displayMsg(result,'error');
						}
						else
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_mask@@</h3>
		<p>
			<label>@@mask_flags@@</label><br />
			<input type="text" class="text small" name="access_flags" /><span class="infoMsg note error"></span>
		</p>
		<p>
			<label>@@mask_servers@@</label><br />
			<input class="radio" type="radio" name='servers_all' value="yes" checked="checked" /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='servers_all' value="no" /> @@no@@<br />
			<select id="access_servers" style="display:none;" name="access_servers[]" multiple="multiple">
				{foreach from=$array_servers item=server key=k}
					<option value="{$k}">{$server}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
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

		$('#forma-edit').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:data + '&go=15',
				success:function(result) {
					if(result.indexOf('class="indent"') + 1)
					{
						humanMsg.addLogs(result);
						$('.accessMessage').html('');
						if(result.indexOf('id="success"') + 1)
						{
							rePagination(1);
							$('.tablesorter').trigger('update');
							$('.tablesorter').trigger('applyWidgets', 'zebra');
							$('.infoMsg').html($('<span>').attr('style','color: green;').addClass('fadeMsg').text('@@edit_success@@'));
							setTimeout(function() {
								$('.fadeMsg').fadeOut('slow', function() {
									$(this).remove();
								});
							}, 2000);
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
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-edit" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@edit_mask@@ #{$mask_edit.mask_id}</h3>
		<p>
			<label>@@mask_flags@@</label><br />
			<input type="text" class="text small" name="access_flags" value="{$mask_edit.access_flags}" /><span class="infoMsg note error"></span>
		</p>
		<p>
			<label>@@mask_servers@@</label><br />
			<input class="radio" type="radio" name='servers_all' value="yes" {if in_array("0", $access_servers)}checked="checked"{/if} /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='servers_all' value="no" {if !in_array("0", $access_servers)}checked="checked"{/if} /> @@no@@<br />
			<select id="access_servers"{if in_array("0", $access_servers)} style="display:none;"{/if} name="access_servers[]" multiple="multiple">
				{foreach from=$array_servers item=server key=k}
					<option value="{$k}"{if in_array($k, $access_servers)} selected{/if}>{$server}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<input type="hidden" name="mask_id" value="{$mask_edit.mask_id}" />
			<input type="submit" class="submit mid" value="@@save@@" />
		</p>
	</form>
</div>
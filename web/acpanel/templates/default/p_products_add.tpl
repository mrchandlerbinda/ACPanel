{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {

		var button = $('#uploadButton');

		$.ajax_upload(button, {
			action: 'acpanel/upload.php',
			name: 'userfile',
			data: {type: 'xml'},
			onSubmit: function(file, ext) {
				this.disable();
				$('.infoMsg').html(
					$('<span>')
					.append(
						$('<img>')
						.attr('src','acpanel/images/ajax-bar-loader.gif')
						.attr('alt','@@loading@@')
					)
				);
			},
			onComplete: function(file, response) {
				this.enable();
				setTimeout(function() {
					if(response.indexOf('id="error"') + 1)
					{						$('.infoMsg').html('@@add_failed@@');
						humanMsg.displayMsg(response,'error');
					}
					else
					{						$('.infoMsg').html($('<span>').attr('style','color: green;').text('@@add_success@@'));
						resortProducts();						humanMsg.displayMsg(response,'success');
					}
				}, 2000);
			}
		});
	});
</script>
{/literal}
<div style="width: 600px;">
	<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_product@@</h3>
	<p>
		<button id="uploadButton" class="submit long">@@upload_xml@@</button>
	</p>
	<fieldset style="padding: 10px;"><legend style="padding: 0 3px 0 3px; font-weight: bold;">@@add_result@@</legend><span class="infoMsg note error"></span></fieldset>
</div>
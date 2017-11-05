{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.ajaxupload.js'></script>
<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		var button = $('#uploadButton');

		$.ajax_upload(button, {
			action: 'acpanel/upload.php',
			name: 'userfile',
			data: {type: 'lang'},
			onSubmit: function(file, ext) {
				this.disable();
				$.blockUI({ message: null });
			},
			onComplete: function(file, response) {
				this.enable();
				setTimeout(function() {
					if( response.indexOf('id="success"') + 1 )
					{
						humanMsg.displayMsg(response,'success');
					}
					else
					{
						humanMsg.displayMsg(response,'error');
					}
				}, 2000);
				$.unblockUI();
			}
		});

		$('#forma-add').submit(function() {
			$.blockUI({ message: null });
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_general_lang',
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
					$.unblockUI();
				}
			});

			return false;
		});
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-add" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_lang@@</h3>
		<p>
			<label>@@lang_title@@</label><br />
			<input type="text" class="text small" name="lang_title" />
		</p>
		<p>
			<label>@@lang_active@@</label><br />
			<input class="radio" type="radio" name='lang_active' value="yes" checked="checked" /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='lang_active' value="no" /> @@no@@
		</p>
		<p>
			<label>@@lang_code@@</label><br />
			<input type="text" class="text small" name="lang_code" /><span class="infoMsg note error"></span>
		</p>
		<div style="float:left;"><input type="submit" class="submit mid" value="@@add@@" /></div>
		<div style="float:left;"><button id="uploadButton" class="submit long">@@upload_xml@@</button></div>
		<div style="clear:both;"></div>
	</form>
</div>
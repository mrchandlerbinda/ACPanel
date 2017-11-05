{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			var cursel = $('#forma-select select option:selected').val();
	
			$('#forma-add select option:selected').removeAttr('selected');
			$("#forma-add select option[value='" + cursel +"']").attr('selected', 'yes');
		});
	})(jQuery);

	jQuery(document).ready(function($) {

		$('#forma-add').submit(function() {
			var data = $(this).serialize();
			var p = $('#forma-select option:selected').val();
			var c = $('#forma-add option:selected').val();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_cc_patterns',
				data:data + '&go=2',
				success:function(result) {
					if(result.indexOf('class="indent"') + 1)
					{
						if(c == p)
						{
							humanMsg.addLogs(result);
							rePagination(1);
							$('.accessMessage').html('');
							$('.tablesorter').trigger('update');
							$('.tablesorter').trigger('applyWidgets', 'zebra');
						}

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

		// Form select styling
		$("form#forma-add select.styled").select_skin();
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-add" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_pattern@@</h3>
		<p style="width: 280px;">
			<label>@@dict@@</label><br />
			<select class="styled" name="dict">
				<option value="2" selected>Ban-List</option>
				<option value="3">Kick-List</option>
				<option value="4">Notice-List</option>
				<option value="1">Hide-List</option>
				<option value="0">White-List</option>
			</select>
		</p>
		<p>
			<label>@@pattern@@</label><br />
			<input type="text" class="text small" name="pattern" /><span class="infoMsg note error"></span>
		</p>
		<p>
			<label>@@reason@@</label><br />
			<input type="text" class="text small" name="reason" />
		</p>
		<p>
			<label>@@time@@</label><br />
			<input type="text" class="text small" name="length" />
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
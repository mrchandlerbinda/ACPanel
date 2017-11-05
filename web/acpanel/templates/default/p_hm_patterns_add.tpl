{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_hm_patterns',
				data:data + '&go=2',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.addLogs(result);
						rePagination(1);
						$('.accessMessage').html('');
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						$('#forma-add input').not(':submit').val('');
					}
					else
					{
						humanMsg.displayMsg(result,'error');
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@hud_add_pattern@@</h3>
		<p>
			<label>@@pattern@@</label><br />
			<input type="text" class="text small" name="name" />
		</p>
		<p style="width: 280px;">
			<label>@@flags@@</label><br />
			<select name="flags[]" multiple="multiple">
				<option value="1">@@opt_1@@</option>
				<option value="2">@@opt_2@@</option>
				<option value="4">@@opt_4@@</option>
				<option value="8">@@opt_8@@</option>
				<option value="16">@@opt_16@@</option>
				<option value="32">@@opt_32@@</option>
				<option value="64">@@opt_64@@</option>
				<option value="128">@@opt_128@@</option>
			</select>
		</p>
		<p>
			<label>@@priority@@</label><br />
			<input type="text" class="text small" name="priority" value="10" />
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
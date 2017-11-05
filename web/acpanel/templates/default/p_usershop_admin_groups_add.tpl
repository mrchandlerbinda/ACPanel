{literal}
<script type='text/javascript'>

	jQuery(document).ready(function($) {

		$('#forma-add').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_payment',
				data:data + '&go=8',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
						rePagination(1);
						$('.accessMessage').html('');
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						$('#forma-add input, #forma-add textarea').not(':submit').val('');
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@usershop_admin_groups_add@@</h3>
		<p>
			<label>@@group_name@@</label><br />
			<input type="text" class="text small" name="name" />
		</p>
		<p>
			<label>@@group_description@@</label><br />
			<textarea class="text" name="description"></textarea>
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
{literal}
<script type='text/javascript'>

	jQuery(document).ready(function($) {

		$('#forma-edit').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_payment',
				data:data + '&go=9',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
						$('.accessMessage').html('');
						rePagination(0);
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
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
	<form id="forma-edit" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@usershop_admin_groups_edit@@ #{$group_edit.gid}</h3>
		<p>
			<label>@@group_name@@</label><br />
			<input type="text" class="text small" name="name" value="{$group_edit.name|htmlspecialchars}" />
		</p>
		<p>
			<label>@@group_description@@</label><br />
			<textarea class="text" name="description">{$group_edit.description|htmlspecialchars}</textarea>
		</p>
		<p>
			<input type="hidden" name="gid" value="{$group_edit.gid}" />
			<input type="submit" class="submit mid" value="@@save@@" />
		</p>
	</form>
</div>
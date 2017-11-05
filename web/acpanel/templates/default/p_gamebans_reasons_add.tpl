{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
<script type='text/javascript'>

	jQuery(document).ready(function($) {

		// Form select styling
		$('form#forma-add select.styled').select_skin();

		$('#forma-add').submit(function() {
			$.blockUI({ message: null });
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamebans',
				data:data + '&go=10',
				success:function(result) {
					$('.accessMessage').html('');

					if(result.indexOf('id="success"') + 1)
					{
						rePagination(1);
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						humanMsg.displayMsg(result,'success');
					}
					else
					{
						humanMsg.displayMsg(result,'error');
					}
				},
				complete:function() {
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_ban_reason@@</h3>
		<p>
			<label>@@ban_server@@</label><br />
			<select class="styled" name="address">
				{foreach name=foo from=$array_servers key=k item=srv}
					<option value="{$k}"{if $smarty.foreach.foo.first} selected{/if}>{$srv}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@ban_reason@@</label><br />
			<input type="text" class="text small" name="reason" value="" />
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@save@@" />
		</p>
	</form>
</div>
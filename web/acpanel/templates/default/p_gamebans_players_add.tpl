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
				data:data + '&go=2',
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_ban@@</h3>
		<p>
			<label>@@ban_player_nick@@</label><br />
			<input type="text" class="text small" name="player_nick" value="" />
		</p>
		<p>
			<label>@@ban_type@@</label><br />
			<select class="styled" name="ban_type">
				<option value="N">@@ban_by_nick@@</option>
				<option value="SI" selected>@@ban_by_ip@@</option>
				<option value="S">@@ban_by_steam@@</option>
			</select>
		</p>
		<p>
			<label>@@ban_player_ip@@</label><br />
			<input type="text" class="text small" name="player_ip" value="" />
		</p>
		<p>
			<label>@@ban_cookie_ip@@</label><br />
			<input type="text" class="text small" name="cookie_ip" value="" />
		</p>
		<p>
			<label>@@ban_player_steam@@</label><br />
			<input type="text" class="text small" name="player_id" value="" />
		</p>
		<p>
			<label>@@ban_length@@</label><br />
			<input type="text" class="text small" name="ban_length" value="0" />
		</p>
		<p>
			<label>@@ban_reason@@</label><br />
			<input type="text" class="text small" name="ban_reason" value="" />
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@save@@" />
		</p>
	</form>
</div>
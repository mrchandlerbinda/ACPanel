{literal}
<script type='text/javascript'>

	jQuery(document).ready(function($) {

		// Form select styling
		$('form#forma-add select.styled').select_skin();

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
				url:'acpanel/ajax.php?do=ajax_payment',
				data:data + '&go=28',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
						rePagination(1);
						$('.accessMessage').html('');
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						$('#forma-add input:text').val('');
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@gameshop_items_add@@</h3>
		<p>
			<select name="cmd" class="styled">
				<option value="1" selected>@@item_const_hp@@</option>
				<option value="2">@@item_const_ap@@</option>
				<option value="3">@@item_const_money@@</option>
				<option value="4">@@item_const_glow@@</option>
				<option value="5">@@item_const_resp@@</option>
				<option value="6">@@item_const_god@@</option>
			</select>
		</p>
		<p>
			<label>@@item_web_descr@@</label><br />
			<input type="text" class="text small" name="web_descr" />
		</p>
		<p>
			<label>@@item_game_descr@@</label><br />
			<input type="text" class="text small" name="game_descr" />
		</p>
		<p>
			<label>@@item_cost@@</label><br />
			<input type="text" class="text small" name="cost" value="0" />
		</p>
		<p>
			<label>@@item_duration@@</label><br />
			<input type="text" class="text small" name="duration" />
		</p>
		<p>
			<label>@@item_servers@@</label><br />
			<input class="radio" type="radio" name='servers_all' value="yes" checked="checked" /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='servers_all' value="no" /> @@no@@<br />
			<select id="access_servers" style="display:none;" name="access_servers[]" multiple="multiple">
				{foreach from=$array_servers item=server key=k}
					<option value="{$k}">{$server}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@item_active@@</label><br />
			<input class="radio" type="radio" name="active" value="1" checked="checked" /> @@yes@@&nbsp;
			<input class="radio" type="radio" name="active" value="0" /> @@no@@
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
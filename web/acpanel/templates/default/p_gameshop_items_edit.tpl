{literal}
<script type='text/javascript'>

	jQuery(document).ready(function($) {

		// Form select styling
		$('form#forma-edit select.styled').select_skin();

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
				url:'acpanel/ajax.php?do=ajax_payment',
				data:data + '&go=29',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@gameshop_items_edit@@</h3>
		<p>
			<select name="cmd" class="styled">
				<option value="1"{if $item.cmd == 1} selected{/if}>@@item_const_hp@@</option>
				<option value="2"{if $item.cmd == 2} selected{/if}>@@item_const_ap@@</option>
				<option value="3"{if $item.cmd == 3} selected{/if}>@@item_const_money@@</option>
				<option value="4"{if $item.cmd == 4} selected{/if}>@@item_const_glow@@</option>
				<option value="5"{if $item.cmd == 5} selected{/if}>@@item_const_resp@@</option>
				<option value="6"{if $item.cmd == 6} selected{/if}>@@item_const_god@@</option>
			</select>
		</p>
		<p>
			<label>@@item_web_descr@@</label><br />
			<input type="text" class="text small" name="web_descr" value="{$item.web_descr|htmlspecialchars}" />
		</p>
		<p>
			<label>@@item_game_descr@@</label><br />
			<input type="text" class="text small" name="game_descr" value="{$item.game_descr|htmlspecialchars}" />
		</p>
		<p>
			<label>@@item_cost@@</label><br />
			<input type="text" class="text small" name="cost" value="{$item.cost}" />
		</p>
		<p>
			<label>@@item_duration@@</label><br />
			<input type="text" class="text small" name="duration" value="{$item.duration}" />
		</p>
		<p>
			<label>@@item_servers@@</label><br />
			<input class="radio" type="radio" name='servers_all' value="yes"{if $item.servers AND in_array(0, $item.servers)} checked="checked"{/if} /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='servers_all' value="no"{if !$item.servers OR !in_array(0, $item.servers)} checked="checked"{/if} /> @@no@@<br />
			<select id="access_servers" style="{if $item.servers AND in_array(0, $item.servers)}display:none;{else}display:block;{/if}" name="access_servers[]" multiple="multiple">
				{foreach from=$array_servers item=server key=k}
					<option value="{$k}"{if in_array($k, $item.servers)} selected{/if}>{$server}</option>
				{/foreach}
			</select>
		</p>
		<p>
			<label>@@item_active@@</label><br />
			<input class="radio" type="radio" name="active" value="1"{if $item.active} checked="checked"{/if} /> @@yes@@&nbsp;
			<input class="radio" type="radio" name="active" value="0"{if !$item.active} checked="checked"{/if} /> @@no@@
		</p>
		<p>
			<input type="hidden" name="id" value="{$item.id}" />
			<input type="submit" class="submit mid" value="@@edit@@" />
		</p>
	</form>
</div>
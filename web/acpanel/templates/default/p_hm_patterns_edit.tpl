{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_hm_patterns',
				data:data + '&go=5',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.addLogs(result);
						rePagination(1);
						$('.accessMessage').html('');
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						$('#facebox .close').click();
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@hud_edit_pattern@@</h3>
		<p>
			<label>@@pattern@@</label><br />
			<input type="text" class="text small" name="name" value="{$hud_edit.name|htmlspecialchars}" />
		</p>
		<p style="width: 280px;">
			<label>@@flags@@</label><br />
			<select name="flags[]" multiple="multiple">
				<option value="1"{if 1|in_array:$hud_edit.flags} selected{/if}>@@opt_1@@</option>
				<option value="2"{if 2|in_array:$hud_edit.flags} selected{/if}>@@opt_2@@</option>
				<option value="4"{if 4|in_array:$hud_edit.flags} selected{/if}>@@opt_4@@</option>
				<option value="8"{if 8|in_array:$hud_edit.flags} selected{/if}>@@opt_8@@</option>
				<option value="16"{if 16|in_array:$hud_edit.flags} selected{/if}>@@opt_16@@</option>
				<option value="32"{if 32|in_array:$hud_edit.flags} selected{/if}>@@opt_32@@</option>
				<option value="64"{if 64|in_array:$hud_edit.flags} selected{/if}>@@opt_64@@</option>
				<option value="128"{if 128|in_array:$hud_edit.flags} selected{/if}>@@opt_128@@</option>
			</select>
		</p>
		<p>
			<label>@@priority@@</label><br />
			<input type="text" class="text small" name="priority" value="{$hud_edit.priority}" />
		</p>
		<p>
			<input type="hidden" name="hud_id" value="{$hud_edit.hud_id}" />
			<input type="submit" class="submit mid" value="@@apply@@" />
		</p>
	</form>
</div>
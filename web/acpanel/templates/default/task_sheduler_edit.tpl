{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {
			$.blockUI({ message: null });
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_task_sheduler',
				data:data + '&go=5',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
						rePagination(1);
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
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
	<form id="forma-edit" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@edit_task@@</h3>
		<p>
			<label>@@run_file@@</label><br />
			<span style="position: relative;bottom: -2px;">/acpanel/includes/cron/</span><input style="width: 116px;" type="text" class="text small" name="cron_file" value="{$task_edit.cron_file}" />
		</p>
		<ul>
			<li>
				<select name="minutes">
					<option value="*"{if $task_edit.minutes == '*'} selected{/if}>*</option>
					{foreach from=$minutes item=m}
						<option value="{$m}"{if $m == $task_edit.minutes} selected{/if}>{$m}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@minutes@@</label>
			</li>
			<li>
				<select name="hours">
					<option value="*"{if $task_edit.hours == '*'} selected{/if}>*</option>
					{foreach from=$hours item=h}
						<option value="{$h}"{if $h == $task_edit.hours} selected{/if}>{$h}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@hours@@</label>
			</li>
			<li>
				<select name="days">
					<option value="*"{if $task_edit.days == '*'} selected{/if}>*</option>
					{foreach from=$days item=d}
						<option value="{$d}"{if $d == $task_edit.days} selected{/if}>{$d}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@days@@</label>
			</li>
			<li>
				<select name="months">
					<option value="*"{if $task_edit.months == '*'} selected{/if}>*</option>
					{foreach from=$months item=m}
						<option value="{$m}"{if $m == $task_edit.months} selected{/if}>{$m}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@months@@</label>
			</li>
		</ul>
		<p>
			<label>@@product_id@@</label><br />
			<input type="text" class="text small" name="product_id" value="{$task_edit.product_id}" />
		</p>
		<p>
			<label>@@task_active@@</label><br />
			<input class="radio" type="radio" name='active' value="1"{if $task_edit.active == 1} checked="checked"{/if} /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='active' value="0"{if $task_edit.active == 0} checked="checked"{/if} /> @@no@@
		</p>
		<p>
			<input type="hidden" name="entry_id" value="{$task_edit.entry_id}" />
			<input type="submit" class="submit mid" value="@@apply@@" />
		</p>
	</form>
</div>
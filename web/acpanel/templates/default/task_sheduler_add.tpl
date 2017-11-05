{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			$.blockUI({ message: null });
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_task_sheduler',
				data:data + '&go=2',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						humanMsg.displayMsg(result,'success');
						$('.accessMessage').html('');
						rePagination(1);
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						$('#forma-add input').not(':submit').val('');
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_new_task@@</h3>
		<div class="message info"><p>@@file_need_upload@@</p></div>
		<p>
			<label>@@run_file@@</label><br />
			<span style="position: relative;bottom: -2px;">/acpanel/includes/cron/</span><input style="width: 116px;" type="text" class="text small" name="cron_file" />
		</p>
		<ul>
			<li>
				<select name="minutes">
					<option value="*" selected>*</option>
					{foreach from=$minutes item=m}
						<option value="{$m}">{$m}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@minutes@@</label>
			</li>
			<li>
				<select name="hours">
					<option value="*" selected>*</option>
					{foreach from=$hours item=h}
						<option value="{$h}">{$h}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@hours@@</label>
			</li>
			<li>
				<select name="days">
					<option value="*" selected>*</option>
					{foreach from=$days item=d}
						<option value="{$d}">{$d}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@days@@</label>
			</li>
			<li>
				<select name="months">
					<option value="*" selected>*</option>
					{foreach from=$months item=m}
						<option value="{$m}">{$m}</option>
					{/foreach}
				</select>
				<label style="position:relative;bottom:4px;">@@months@@</label>
			</li>
		</ul>
		<p>
			<label>@@product_id@@</label><br />
			<input type="text" class="text small" name="product_id" />
		</p>
		<p>
			<label>@@task_active@@</label><br />
			<input class="radio" type="radio" name='active' value="1" checked="checked" /> @@yes@@&nbsp;
			<input class="radio" type="radio" name='active' value="0" /> @@no@@
		</p>
		<p>
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
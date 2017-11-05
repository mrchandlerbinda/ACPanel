{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		// Form select styling
		$('form#forma-add select.styled').select_skin();
		$('.ajaxImg').click(function() {			if (!$(this).hasClass('load')) {
				$(this).addClass('load');
				var data = $('#forma-add input[name="address"]').val();
				var tp = $('[name="gametype"]').val();

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_servers_control',
					data:({'address' : data,'type' : tp,'go' : 5}),
					success:function(result) {						setTimeout(function() {
							$('#forma-add input[name="hostname"]').val(result);
							$('.ajaxImg').removeClass('load');
						}, 1000);
					}
				});
			}
		});

		$('#forma-add').submit(function() {

			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_servers_control',
				data:data + '&go=2',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						rePagination(1);
						$('.accessMessage').html('');
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						$('#forma-add input:text').val('');
						humanMsg.displayMsg(result,'success');
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
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@add_server@@</h3>
		<p>
			<select class="styled" name="gametype">
					<option value="0" selected>@@select_gametype@@</option>
					{foreach from=$gtypes key=k item=type}
						<option value="{$k}">{$type.name}</option>
					{/foreach}
			</select>
		</p>
		<p>
			<label>@@address@@</label><br />
			<input type="text" class="text small" name="address" /><span class="infoMsg note error"></span>
		</p>
		<p style="padding: 0; margin: 0;">
			<label>@@hostname@@</label>
		</p>
		<p class="p-load">
			<input type="text" class="text" name="hostname" /><span class="ajaxImg"></span>
		</p>
		<p>
			<input type="hidden" name="uid" value="{$uid}" />
			<input type="submit" class="submit mid" value="@@add@@" />
		</p>
	</form>
</div>
{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {		$('#forma-checknick').submit(function() {			$('.checknickMessage').html('<img style="padding: 23px 0;" src="acpanel/images/loading.gif" alt="" />');
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_nc_patterns',
				data:data + '&go=7',
				success:function(result) {					setTimeout(function() {
						$('.checknickMessage').html(result);

						$('.block .checknickMessage .message').hide().append('<span class=\"close\" title=\"Dismiss\"></span>').fadeIn('slow');
						$('.block .checknickMessage .message .close').hover(
							function() { $(this).addClass('hover'); },
							function() { $(this).removeClass('hover'); }
						);

						$('.block .checknickMessage .message .close').click(function() {
							$(this).parent().fadeOut('slow', function() { $(this).remove(); });
						});
					}, 1000);
				}
			});

			return false;
		});
	});
</script>
{/literal}

<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@checknick_head@@</h2>
	</div>
	<div class="block_content">
	{/if}
		<div class="checknickMessage"></div>
		<div>
			<form action="" method="post" id="forma-checknick">
				<p>
					<label>@@checknick_label@@</label><br />
					<input type="text" class="text" name="checknick" value="" />
				</p>
				<p>
					<input type="submit" class="submit tiny" value="@@check@@" />
				</p>
			</form>
		</div>
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>
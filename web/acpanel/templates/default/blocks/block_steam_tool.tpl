{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {		$('#forma-steam').submit(function() {			$('.steamMessage').html('<img style="padding: 23px 0;" src="acpanel/images/loading.gif" alt="" />');
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_main',
				data:data + '&go=2',
				success:function(result) {					setTimeout(function() {
						$('.steamMessage').html(result);

						$('.block .steamMessage .message').hide().append('<span class=\"close\" title=\"Dismiss\"></span>').fadeIn('slow');
						$('.block .steamMessage .message .close').hover(
							function() { $(this).addClass('hover'); },
							function() { $(this).removeClass('hover'); }
						);

						$('.block .steamMessage .message .close').click(function() {
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

		<h2>@@steam_head@@</h2>
	</div>
	<div class="block_content">
	{/if}
		<div class="steamMessage"></div>
		<div>
			<form id="forma-steam" action="" method="post">
				<p>
					<label>@@steam_label@@</label><br />
					<input type="text" class="text" name="steam" value="" />
				</p>
				<p>
					<input type="submit" class="submit tiny" value="@@convert@@" />
				</p>
			</form>
		</div>
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>

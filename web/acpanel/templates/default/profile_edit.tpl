{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {

		$('#forma-security').submit(function() {
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_homepage',
				data:data + '&url={/literal}{$url}{literal}&go=6',
				success:function(result) {
					if(result.indexOf('class="message errormsg"') + 1)
					{
						$('.result-block').html(result);
					}
					else
					{						$(':input', this).not(':submit, :hidden, [name="umail"]').val('');
						$('.result-block').html(result);
					}
				}
			});

			return false;
		});
	});
</script>
{/literal}
<div style="width: 600px;">
	{if $iserror}
	<div class="message errormsg"><p>{$iserror}</p></div>
	{else}
	<form id="forma-security" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@edit_pass_email@@</h3>
		<div class="result-block"></div>
		<p>
			<label>@@current_pass@@</label> <br />
			<input name="passwd_current" type="password" class="text" value="" />
		</p>
		<p>
			<label>@@new_pass@@</label> <br />
			<input name="passwd_new" type="password" class="text" value="" />
		</p>
		<p>
			<label>@@new_pass_check@@</label> <br />
			<input name="passwd_new_check" type="password" class="text" value="" />
		</p>
		<p>
			<label>@@email_user@@</label> <br />
			<input name="umail" type="text" class="text" value="{$array_user.mail}" />
		</p>
		<p>
			<input name="uid" type="hidden" class="text" value="{$array_user.uid}" />
			<input name="uname" type="hidden" class="text" value="{$array_user.username}" />
			<input type="submit" class="submit mid" value="@@save@@" /> &nbsp;
		</p>
	</form>
	{/if}
</div>
{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			if( $('input[name="usergroupname"]').val() == '' )
			{				$('.infoMsg').html('@@dont_empty@@');
			}
			else
			{				$('.infoMsg').html('');
				var data = $(this).serialize();

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_usergroups',
					data:data + '&go=2',
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							$('.accessMessage').html('');
							humanMsg.displayMsg(result,'success');
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}
					}
				});
			}

			return false;
		});
	});
</script>
{/literal}
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title}</h2>

		<ul>
			<li><a href="{$action_uri}">@@back_url@@</a></li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
		{if $iserror}
			<div class="message warning"><p>{$iserror}</p></div>
		{/if}
		</div>
		{if !$iserror}
		<div>
			<form id="forma-add" action="" method="post">
				<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
					<tr class="even">
						<td width='45%' style='border-right-width:0;'>@@group_name@@</td>
						<td width='55%' align='left' style='border-left-width:0;'>
							<input class='text small' type='text' name='usergroupname' value='' size='30' /><span class="infoMsg note error"></span>
						</td>
					</tr>
					{foreach from=$group_edit item=output}
					<tr class="odd">
						<td colspan="2" width="100%" style="font-weight: bold;">{$output.desc}</td>
					</tr>
						{foreach from=$output.options item=item}
						<tr class="even">
							<td width='45%' style='border-right-width:0;'>{$item.description}</td>
							<td width='55%' align='left' style='border-left-width:0;'>
								{$item.content}
							</td>
						</tr>
						{/foreach}
					{/foreach}
				</table>

				<div class="tableactions">
					<input type="submit" class="submit tiny" value="@@apply@@" />
					<input type="hidden" name="go" value="1" />
				</div>
			</form>
		</div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
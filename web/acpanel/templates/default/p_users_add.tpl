{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-add').submit(function() {
			if( $('input[name="username"]').val() == '' )
			{				$('.infoMsg').html('@@dont_empty@@');
			}
			else
			{				$('.infoMsg').html('');
				var data = $(this).serialize();

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_users',
					data:data + '&go=2',
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							$('.accessMessage').html('');
							humanMsg.displayMsg(result,'success');
							$('#forma-add input:text').not(':disabled').val('');
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
				<p>
					<label>@@username@@</label><br />
					<input type="text" class="text" name="username" value="" autocomplete="off" /><span class="infoMsg note error"></span>
				</p>
				<p>
					<label>@@password@@</label><br />
					<input type="password" class="text" name="password" value="" autocomplete="off" />
				</p>
				<p>
					<label>@@usergroup@@</label><br />
					<select class="styled" name="usergroupid">
							{foreach from=$array_groups item=group}
								<option value="{$group.usergroupid}"{if $group.usergroupid == 1} selected{/if}>{$group.usergroupname}</option>
							{/foreach}
					</select>
				</p>
				<p>
					<label>@@timezone@@</label><br />
					<select class="styled" name="timezone">
							{foreach from=$array_tz key=k item=tz}
								<option value="{$k}"{if $k == $cfg_timezone} selected{/if}>{$tz}</option>
							{/foreach}
					</select>
				</p>
				<p>
					<label>@@reg_date@@</label><br />
					<input type="text" class="text date_picker" name="reg_date" value="" />
				</p>
				<p>
					<label>@@last_visit@@</label><br />
					<input type="text" class="text date_picker" name="last_visit" value="" />
				</p>
				<p>
					<label>@@reg_ip@@</label><br />
					<input type="text" class="text" name="ipaddress" value="" />
				</p>
				<p>
					<label>@@email@@</label><br />
					<input type="text" class="text" name="mail" value="" />
				</p>
				<p>
					<label>@@icq@@</label><br />
					<input type="text" class="text" name="icq" value="" />
				</p>
				<p>
					<input type="submit" class="submit mid" value="@@save@@" />
				</p>
			</form>
		</div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('.advanced-options a').live('click', function(event) {
			if( $('.block-advanced').hasClass('hide') )
			{
				$('.block-advanced select').removeAttr('disabled');
				$('.block-advanced input').removeAttr('disabled');
				$('.block-advanced').removeClass('hide');
				$(this).text('@@hide_advanced_search_options@@');
			}
			else
			{
				$('.block-advanced select').attr('disabled','disabled');
				$('.block-advanced input').attr('disabled','disabled');
				$('.block-advanced').addClass('hide');
				$(this).text('@@show_advanced_search_options@@');
			}

			return false;
		});

		$('[name="type_all"]').change(function() {
			if( $('[name="type_all"]:checked').val() == 'no' )
			{
				$('#ban_type').fadeIn('fast');
			}
			else
			{
				$('#ban_type').fadeOut('fast');
			}
		});

		$('[name="server_all"]').change(function() {
			if( $('[name="server_all"]:checked').val() == 'no' )
			{
				$('#server_ip').fadeIn('fast');
			}
			else
			{
				$('#server_ip').fadeOut('fast');
			}
		});
	});
</script>
{/literal}
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@bans_search@@</h2>
	</div>
	<div class="block_content">
		{if $iserror}
			<div class="message errormsg"><p>{$iserror}</p></div>
		{else}
			<div class="accessMessage"></div>
			<form id="forma-search" action="{$action}" method="get">
				<input type='hidden' name="cat" value="{$section_current.id}" />
				<input type='hidden' name="do" value="{$cat_current.id}" />
				<p>
					<select class="styled" name="search">
						<option value="0" selected="selected">@@search_everywhere@@</option>
						<option value="1">@@search_in_active@@</option>
						<option value="2">@@search_in_history@@</option>
					</select>
				</p>
				<p>
					<input class="radio" type="radio" name="type_all" value="yes" checked="checked" /> @@ban_type_all@@&nbsp;
					<input class="radio" type="radio" name="type_all" value="no" /> @@ban_type_select@@<br />
					<select id="ban_type" style="width:250px;display:none;" name="ban_type[]" multiple="multiple">
						<option value="N">@@ban_by_nick@@</option>
						<option value="SI">@@ban_by_ip@@</option>
						<option value="S">@@ban_by_steam@@</option>
					</select>
				</p>
				<p>
					<label>@@from@@</label>
					<input type="text" name="startdate" class="text date_picker" />
					&nbsp;&nbsp;
					<label>@@totime@@</label>
					<input type="text" name="enddate" class="text date_picker" />
				</p>
				<p>
					<label>@@ban_player_nick@@</label><br />
					<input type="text" class="text small" name="player_nick" value="" />
				</p>
				<p>
					<label>@@ban_player_ip@@</label><br />
					<input type="text" class="text small" name="player_ip" value="" />
				</p>
				<p>
					<label>@@ban_cookie_ip@@</label><br />
					<input type="text" class="text small" name="cookie_ip" value="" />
				</p>
				<p>
					<label>@@ban_player_steam@@</label><br />
					<input type="text" class="text small" name="player_id" value="" />
				</p>
				<div class="block-advanced hide">
					<p>
						<input class="radio" type="radio" name="server_all" value="yes" checked="checked" disabled="disabled" /> @@ban_server_all@@&nbsp;
						<input class="radio" type="radio" name="server_all" value="no" disabled="disabled" /> @@ban_server_select@@<br />
						<select id="server_ip" style="width:250px;display:none;" name="server_ip[]" multiple="multiple" disabled="disabled">
							<option value="0">@@ban_website@@</option>
							{foreach from=$array_servers item=server key=k}
								<option value="{$k}">{$server}</option>
							{/foreach}
						</select>
					</p>
					<p>
						<label>@@ban_length_interval@@</label><br>
						<input style="width:110px;" type="text" name="srok_start" class="text" value="0">
						&nbsp;-&nbsp;
						<input style="width:110px;" type="text" name="srok_end" class="text" value="">
					</p>
					<p>
						<label>@@ban_reason@@</label><br />
						<input type="text" class="text small" name="ban_reason" value="" disabled="disabled" />
					</p>
					<p>
						<label>@@ban_admin_nick@@</label><br />
						<input type="text" class="text small" name="admin_nick" value="" disabled="disabled" />
					</p>
					<p>
						<label>@@ban_admin_ip@@</label><br />
						<input type="text" class="text small" name="admin_ip" value="" disabled="disabled" />
					</p>
					<p>
						<label>@@ban_admin_id@@</label><br />
						<input type="text" class="text small" name="admin_id" value="" disabled="disabled" />
					</p>
				</div>
				<p class="advanced-options">
					<a style="border-bottom: 1px dashed;padding-bottom: 2px;" href="#">@@show_advanced_search_options@@</a>
				</p>
				<p>
					<input type="submit" class="submit small" value="@@search@@" />
					&nbsp;&nbsp;
					<input type="button" class="submit small" value="@@delete@@" />
				</p>
			</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.datetimeentry.pack.js'></script>
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$.datetimeEntry.setDefaults({spinnerImage: 'acpanel/scripts/js/images/spinnerBlue.png',spinnerBigImage: 'acpanel/scripts/js/images/spinnerBlueBig.png'});
			$('.defaultEntry').datetimeEntry({datetimeFormat: 'D-O-Y, H:M'});
		});
	})(jQuery);

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
				$('#flag').fadeIn('fast');
			}
			else
			{
				$('#flag').fadeOut('fast');
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

		<h2>@@ga_search@@</h2>
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
					<label>@@username@@</label><br />
					<input type="text" class="text small" name="username" value="" />
				</p>
				<p>
					<input class="radio" type="radio" name="type_all" value="yes" checked="checked" /> @@auth_type_all@@&nbsp;
					<input class="radio" type="radio" name="type_all" value="no" /> @@auth_type_select@@<br />
					<select id="flag" style="width:250px;display:none;" name="flag[]" multiple="multiple">
						<option value="1">@@auth_nick@@</option>
						<option value="2">@@auth_ip@@</option>
						<option value="3">@@auth_steam@@</option>
					</select>
				</p>
				<p>
					<label>@@player_nick@@</label><br />
					<input type="text" class="text small" name="player_nick" value="" />
				</p>
				<p>
					<label>@@player_ip@@</label><br />
					<input type="text" class="text small" name="player_ip" value="" />
				</p>
				<p>
					<label>@@player_steam@@</label><br />
					<input type="text" class="text small" name="steamid" value="" />
				</p>
				<p>
					<label>@@last_visit_from@@</label><br />
					<input type="text" class="text small defaultEntry" name="startdate" />
				</p>
				<p>
					<label>@@last_visit_totime@@</label><br />
					<input type="text" class="text small defaultEntry" name="enddate" />
				</p>
				<div class="block-advanced hide">
					<p>
						<label>@@access_mask@@</label><br />
						<select class="styled" name="mask" disabled="disabled">
							<option value="0" selected="selected">@@all_masks@@</option>
							{foreach from=$array_masks item=mask key=k}
								<option value="{$k}">#{$k}: {$mask}</option>
							{/foreach}
						</select>
					</p>
					<p>
						<label>@@access_flags@@</label><br />
						<input type="text" class="text small" name="access_flags" value="" disabled="disabled" />
					</p>
					<p>
						<label>@@access_totime@@</label><br />
						<input type="text" class="text small defaultEntry" name="access_expired" disabled="disabled" />
					</p>
					<p>
						<input class="radio" type="radio" name="server_all" value="yes" checked="checked" disabled="disabled" /> @@access_server_all@@&nbsp;
						<input class="radio" type="radio" name="server_all" value="no" disabled="disabled" /> @@access_server_select@@<br />
						<select id="server_id" style="width:250px;display:none;" name="server_ip[]" multiple="multiple" disabled="disabled">
							{foreach from=$array_servers item=server key=k}
								<option value="{$k}">{$server}</option>
							{/foreach}
						</select>
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
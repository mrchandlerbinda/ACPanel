{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('.options-switcher a').click(function() {
			if( $(this).parent().hasClass('off') )
			{
				$(this).text('@@search_bans_options_switch_on@@').parent().removeClass('off').addClass('on');
				$('.advanced-options select').removeAttr('disabled');
				$('.advanced-options').fadeIn();
			}
			else
			{
				$(this).text('@@search_bans_options_switch_off@@').parent().removeClass('on').addClass('off');
				$('.advanced-options').fadeOut();
				$('.advanced-options select').attr('disabled','disabled');
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

		<h1>@@head@@</h1>

		{if !$select_low}
		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="t">
						<option value="0"{if $get_status == 0} selected{/if}>@@all_bans@@</option>
						<option value="1"{if $get_status == 1} selected{/if}>@@bans_active@@</option>
						<option value="2"{if $get_status == 2} selected{/if}>@@bans_passed@@</option>
					</select>
				</form>
			</li>
		</ul>
		{/if}
	</div>
	<div class="block_content">
		{if !$iserror}
		<div id="search-box">
			<form id="forma-search" action="{$action}" method="get">
				<input type='hidden' name="cat" value="{$section_current.id}" />
				<input type='hidden' name="do" value="{$cat_current.id}" />
				<input type='hidden' name="t" value="{$get_status}" />
				<div class="left">
					<div>
						<input type="text" class="text small" name="search" value="" />
					</div>
				</div>
				<div class="right">
					<input type="submit" class="submit mid" value="@@search@@" />
				</div>
				<ul id="search-type">
					<li><input id="searchNick" class="radio" type="radio" name="search_type" value="0" checked="checked" /><label for="searchNick">@@search_nick@@</label></li>
					<li><input id="searchIP" class="radio" type="radio" name="search_type" value="1" /><label for="searchIP">@@search_ip@@</label></li>
					<li><input id="searchSteam" class="radio" type="radio" name="search_type" value="2" /><label for="searchSteam">@@search_steam@@</label></li>
				</ul>
				<div class="options-switcher off">
					<a href="#">@@search_bans_options_switch_off@@</a>
				</div>
				<br style="clear: both;" />
				<div class="advanced-options">
					<ul>
						<li>
							<select class="styled" name="s" disabled="disabled">
								<option value="0" selected>@@all_servers@@</option>
								{foreach from=$servers item=srv key=k}
								<option value="{$k}">{$srv.name|htmlspecialchars}</option>
								{/foreach}
							</select>
						</li>
						<li>
							<select class="styled" name="a" disabled="disabled">
								<option value="0" selected>@@all_admins@@</option>
								{foreach from=$admins item=a key=k}
								<option value="{$k}">{$a|htmlspecialchars}</option>
								{/foreach}
							</select>
						</li>
					</ul>
					<br style="clear: both;" />
				</div>
			</form>
		</div>
		{/if}
		<div class="accessMessage">
			{if $iserror}
				{$iserror}
			{else}
				{$infomsg}
			{/if}
		</div>
		<div id="ajaxContent"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
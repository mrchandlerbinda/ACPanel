{if !$iserror}
{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.datetimeentry.pack.js'></script>
<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$.datetimeEntry.setDefaults({spinnerImage: 'acpanel/scripts/js/images/spinnerBlue.png',spinnerBigImage: 'acpanel/scripts/js/images/spinnerBlueBig.png'});
			{/literal}
			{assign var="cnt_mask" value=count($account.mask)}
			{assign var="cnt_key" value="0"}
			{while $cnt_mask > $cnt_key}
				{assign var="cnt_key" value=$cnt_key+1}
				{literal}$('#defaultEntry-{/literal}{$cnt_key}{literal}').datetimeEntry({datetimeFormat: 'D-O-Y, H:M'});{/literal}
			{/while}
			{literal}
			$('.block_content a[rel*=facebox]').facebox();
		});
	})(jQuery);

	{/literal}{if $acp_bans}{literal}
	function checkBans() 
	{
		jQuery.ajax({
			type:'POST',
			dataType:'json',
			url:'acpanel/ajax.php?do=ajax_gamecp',
			data:({'nick' : '{/literal}{$account.player_nick}{literal}','ip' : '{/literal}{$account.player_ip}{literal}','steam' : '{/literal}{$account.steamid}{literal}','go' : 27}),
			success:function(result) {
				if( result.all > 0 )
				{
					jQuery('.find-bans').addClass('warning').html(result.str);
					jQuery('<div>').addClass('bans-list').html('<ul></ul>').insertAfter(jQuery('.find-bans'));
					if( result.nick > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_nick@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&player_nick=!{$account.player_nick}{literal}">' + result.nick + ' (' + result.nick_a + ')</a>'));
					if( result.ip > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_ip@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&player_ip={$account.player_ip}{literal}">' + result.ip + ' (' + result.ip_a + ')</a>'));
					if( result.cookie > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_cookie@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&cookie_ip={$account.player_ip}{literal}">' + result.cookie + ' (' + result.cookie_a + ')</a>'));
					if( result.steam > 0 )
						jQuery('.bans-list ul').append(jQuery('<li>').html('@@coincidence_of_steam@@ <a href="{/literal}{$home}?cat={$cat_bans_search.cat}&do={$cat_bans_search.do}&search=0&type_all=yes&player_id={$account.steamid}{literal}">' + result.steam + ' (' + result.steam_a + ')</a>'));
				}
				else
				{
					jQuery('.find-bans').addClass('success').html(result.str);
				}
			}
		});
	}
	{/literal}{/if}{literal}

	function checkInputField(e, k, l) 
	{
		var str = jQuery(e).val();
		var lyes = (typeof(l)!='undefined') ? l : 0;

		if(str.length > 0)
		{
			jQuery(e).css('background-color','#FEFEFE');
			return 1;
		}
		else
		{
			jQuery(e).css('background-color','#ffc9c9');
		}
		return 0;
	}

	jQuery(document).ready(function($) {
		{/literal}{if $acp_bans}{literal}
		checkBans();
		{/literal}{/if}{literal}
		$('.block-access-mask select.styled').live("change", function(event) {
			var srvcnt = $('option:selected', this).attr('rel');
			if( !isNaN(+srvcnt) ) srvcnt = '<a href="{/literal}{$home}?cat={$section_current.id}&mask={literal}' + $('option:selected', this).val() + '" rel="facebox">' + srvcnt + '</a>';
			$(this).parents('.block-access-mask').find('.access-servers span').html(srvcnt);
			$(this).parents('.block-access-mask').find('a[rel*=facebox]').facebox();
		});

		$('#delete-account').live("click", function(event) {
			if( confirm('@@question_delete_account@@') )
			{
				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_gamecp',
					data:({'id' : {/literal}{if $account.userid}{$account.userid}{else}0{/if}{literal},'go' : 3}),
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							humanMsg.displayMsg(result,'success');
							setTimeout(function() { window.location.href = '{/literal}{$action_uri}{literal}'; }, 1500 );
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

		$('.add-mask a').live("click", function(event) {
			if( $('.block-access-mask').hasClass('hide') )
			{
				$('.block-access-mask select').removeAttr('disabled');
				$('.block-access-mask').removeClass('hide');
				$(this).text('@@add_one_mask@@');
			}
			else
			{
				if( $('.block-access-mask').length < $('.block-access-mask select:first option').length )
				{
					var optSelected = $('.block-access-mask').last().find('select').val();
					var MaskIDSplitter = $('.block-access-mask').last().find('h2').attr('id').split('-');
					var MaskNewID = MaskIDSplitter[0] + '-' + (MaskIDSplitter[1]++);
					var divclone = $('.block-access-mask').last().clone(true);
					divclone.find('input, select').attr('name', function(i, name) {
						return name.replace(/([0-9]+)/, MaskIDSplitter[1]);
					}); 
					divclone.find('h2, li.nofloat input').attr('id', function(i, id) {
						return id.replace(/([0-9]+)/, MaskIDSplitter[1]);
					}); 
					var selclone = divclone.find('select.styled').clone(true);
					var nextopt = selclone.find("option[value='" + optSelected + "']").removeAttr("selected").next('option').attr("selected","selected").val();
					var srvcnt = selclone.find('option:selected').attr('rel');
					if( !isNaN(+srvcnt) ) srvcnt = '<a href="{/literal}{$home}?cat={$section_current.id}&mask={literal}' + nextopt + '" rel="facebox">' + srvcnt + '</a>';
					divclone.find('.access-servers span').html(srvcnt);
					divclone.find('select').parent().remove();
					divclone.find('li:first').html(selclone);
					divclone.find('select.styled').unbind();
					divclone.find('a[rel*=facebox]').facebox();
					divclone.find('li.nofloat input').datetimeEntry('destroy');
					$('<img style="position:absolute; top:0px; right:0px; cursor:pointer;" src="acpanel/images/error.png" />').appendTo(divclone.find('h2'))
					$('.add-mask').before(divclone);
					$('.block-access-mask:last select.styled').select_skin();
					$('#defaultEntry-' + MaskIDSplitter[1]).datetimeEntry({datetimeFormat: 'D-O-Y, H:M'});
				}
				else
				{
					alert("@@mask_access_limit@@");
				}
			}

			return false;
		});

		$('.block-access-mask h2 img').live("click", function(event) { 
			$(this).parents('.block-access-mask').remove();
		});

		$('[name="player_nick"], [name="password"], [name="player_ip"], [name="steamid"]').keyup(function(event) {
	    		checkInputField(this, event);
		}).change(function(event) {
	    		checkInputField(this, event, 1);
		}).live("click", function(event) { 
			$(this).css('background-color','#FEFEFE');
		});

		$('#forma-edit').submit(function(event) {
			var txt, errr = 0;
			var arrError = new Array();

			$('[name="player_nick"], [name="player_ip"], [name="steamid"]').not(':disabled').each(function (event) {
				var chk = checkInputField(this, event, 1);

				if( !chk )
				{
					txt = $(this).parent('p').find('label').text();
					arrError.push('@@field_not_valid@@ <b>' + txt + '</b>');
				}
			});

			if( arrError.length > 0 )
			{
				var outputError;

				if( arrError.length > 1 )
				{
					outputError = '<br />&raquo;&raquo;&raquo;&nbsp;' + arrError.join('<br />&raquo;&raquo;&raquo;&nbsp;');
				}
				else
				{
					outputError = arrError[0];
				}

				outputError = '<img style="vertical-align:middle;" src="acpanel/templates/{/literal}{$tpl}{literal}/images/error.gif" alt=""><span id="error" class="indent">@@error_list@@&nbsp;' + outputError + '</span>';

				humanMsg.displayMsg(outputError,'error');
			}
			else
			{
				var data = $(this).serialize();

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_gamecp',
					data:data + '&go=5',
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							if( $('.block-access-mask').length > 0 )
							{
								if( !$('.block-access-mask').hasClass('hide') )
								{
									$('.accessMessage').html('');
								}
							}
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

		$('#forma-edit select[name="flag"]').change(function () {
			if($(this).val() == 1)
			{
				$('.player-ip').addClass('hide');
				$('.player-ip input').attr('disabled','disabled');
				$('.player-steam').addClass('hide');
				$('.player-steam input').attr('disabled','disabled');
				$('.player-nickpass').removeClass('hide');
				$('.player-nickpass input[name="player_nick"]').removeAttr('disabled');
				$('.player-nickpass input[name="password"]').removeAttr('disabled');
			}
			else if($(this).val() == 2)
			{
				$('.player-ip').removeClass('hide');
				$('.player-ip input').removeAttr('disabled');
				$('.player-steam').addClass('hide');
				$('.player-steam input').attr('disabled','disabled');
				$('.player-nickpass').addClass('hide');
				$('.player-nickpass input[name="player_nick"]').attr('disabled','disabled');
				$('.player-nickpass input[name="password"]').attr('disabled','disabled');
			}
			else
			{
				$('.player-steam').removeClass('hide');
				$('.player-steam input').removeAttr('disabled');
				$('.player-ip').addClass('hide');
				$('.player-ip input').attr('disabled','disabled');
				$('.player-nickpass').addClass('hide');
				$('.player-nickpass input[name="player_nick"]').attr('disabled','disabled');
				$('.player-nickpass input[name="password"]').attr('disabled','disabled');
			}
		});
	});
</script>
{/literal}
{/if}
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title} #{$account.userid}</h2>

		<ul>
			<li><a href="{$action_uri}">@@back_url@@</a></li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
		{if $iserror}
			<div class="message warning"><p>{$iserror}</p></div>
		{/if}
		{if count($account.mask) == 0}
			<div class="message warning"><p>@@account_not_mask@@</p></div>
		{/if}
		</div>
		{if !$iserror}
		<form id="forma-edit" action="" method="post">
			<div class="left-float-box">
				<div class="left-box-content">
					<ul>
						<li><b>@@account_reg_date@@</b> {$account.timestamp}</li>
						<li><b>@@account_last_time@@</b> {$account.last_time}</li>
					</ul>
					<p>
						<label>@@auth_type@@</label><br />
						<select class="styled" name="flag">
								<option value="1"{if $account.flag == 1} selected{/if}>@@auth_nick@@</option>
								<option value="2"{if $account.flag == 2} selected{/if}>@@auth_ip@@</option>
								<option value="3"{if $account.flag == 3} selected{/if}>@@auth_steam@@</option>
						</select>
					</p>
					<p class="player-nickpass{if $account.flag != 1} hide{/if}">
						<label>@@player_nick_password@@</label><br />
						<input type="text" class="text tiny" name="player_nick" value="{$account.player_nick}" autocomplete="off"{if $account.flag != 1} disabled="disabled"{/if} />&nbsp;<input type="text" class="text tiny" name="password" value="" autocomplete="off"{if $account.flag != 1} disabled="disabled"{/if} />
					</p>
					<p class="player-ip{if $account.flag != 2} hide{/if}">
						<label>@@player_ip@@</label><br />
						<input type="text" class="text small" name="player_ip" value="{$account.player_ip}"{if $account.flag != 2} disabled="disabled"{/if} />
					</p>
					<p class="player-steam{if $account.flag != 3} hide{/if}">
						<label>@@player_steam@@</label><br />
						<input type="text" class="text small" name="steamid" value="{$account.steamid}"{if $account.flag != 3} disabled="disabled"{/if} />
					</p>
					{if count($account.mask) > 0}
						{assign var="access_num" value="1"}
						{foreach from=$account.mask item=umask key=u}
							<div class="block-access-mask">
								<h2 style="position:relative;" id="mask-{$access_num}">@@access_mask@@{if $access_num != 1}<img style="position:absolute; top:0px; right:0px; cursor:pointer;" src="acpanel/images/error.png" />{/if}</h2>
								<ul>
									<li>
										<select class="styled" name="access_mask_{$access_num}">
											{foreach from=$array_masks item=mask key=k}
												<option rel="{$mask.servers}" value="{$k}"{if $umask == $k} selected{assign var="servers" value=$mask.servers}{/if}>#{$k}: {$mask.flags}</option>
											{/foreach}
										</select>
									</li>
									<li class="nofloat">
										<input id="defaultEntry-{$access_num}" type="text" class="text small" name="access_expired_{$access_num}" value="{$account.mask_expired.$u}" />
									</li>
								</ul>
								<div class="access-servers">@@ga_access_servers@@ <span>{if is_numeric($servers)}<a href="{$home}?cat={$section_current.id}&mask={$umask}" rel="facebox">{/if}{$servers}{if is_numeric($servers)}</a>{/if}</span></div>
							</div>
							{assign var="access_num" value=$access_num+1}
						{/foreach}
						<p class="add-mask">
							<a style="border-bottom: 1px dashed;padding-bottom: 2px;" href="#">@@add_one_mask@@</a>
						</p>
					{elseif !empty($array_masks)}
						<div class="block-access-mask hide">
							{assign var="access_num" value="1"}
							<h2 style="position:relative;" id="mask-{$access_num}">@@access_mask@@</h2>
							<ul>
								<li>
									<select disabled="disabled" class="styled" name="access_mask_{$access_num}">
										{foreach from=$array_masks item=mask key=k}
											<option rel="{$mask.expired}" value="{$k}"{if $k == $default_mask} selected{/if}>#{$k}: {$mask.flags}</option>
										{/foreach}
									</select>
								</li>
								<li class="nofloat">
									<input disabled="disabled" type="text" class="text date_picker" name="access_expired_{$access_num}" value="{$array_masks.$default_mask.expired}" />
								</li>
							</ul>
						</div>
						<p class="add-mask">
							<a style="border-bottom: 1px dashed;padding-bottom: 2px;" href="#">@@add_first_mask@@</a>
						</p>
					{/if}
					<p>
						<label>@@approved@@</label><br />
						<input class="radio" type="radio" name='approved' value="yes"{if $account.approved == 'yes'} checked="checked"{/if} /> @@yes@@&nbsp;
						<input class="radio" type="radio" name='approved' value="no"{if $account.approved == 'no'} checked="checked"{/if} /> @@no@@
					</p>
					<p>
						<label>@@player_online@@</label><br />
						<input type="text" class="text small" name="online" value="{$account.online}" />
					</p>
	
					<p>
						<label>@@player_points@@</label><br />
						<input type="text" class="text small" name="points" value="{$account.points}" />
					</p>
					<p>
						<input type="submit" class="submit mid" value="@@save@@" />
						&nbsp;&nbsp;
						<input id="delete-account" type="button" class="submit small" value="@@delete@@" />
					</p>
				</div>
			</div>
			<div class="right-float-box">
				<p>
					<label>@@username@@</label><br />
					<input type="hidden" name="userid" value="{$account.userid}" />
					<input type="text" class="text small" name="username" value="{$account.username|htmlspecialchars}" disabled="disabled" />
				</p>
				<p class="profile-link">
					<img align="left" src="{$account.avatar}">
					<a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$account.userid}">@@user_profile@@</a>
				</p>
				<div id="userInfoBox">
					<ul>
						<li><b>@@user_reg_date@@</b> {$account.reg_date}</li>
						<li><b>@@user_last_visit@@</b> {$account.last_visit}</li>
						<li><b>@@user_mail@@</b> {$account.mail}</li>
						<li><b>@@user_icq@@</b> {if $account.icq}{$account.icq}{else}-{/if}</li>
						<li><b>@@user_hid@@</b> {if $account.hid}{if $account.cnt_hid > 1}<a href="{$home}?cat={$cat_users}&do={$cat_user_search}&user_hid={$account.hid}">{/if}{$account.hid}{if $account.cnt_hid > 1}</a>{/if}{else}-{/if}{if $account.cnt_hid > 1} <img style="position:relative; top:2px;" src="acpanel/images/warning.png" alt="" />{/if}</li>
						<li><b>@@user_group@@</b> {$account.usergroupname|htmlspecialchars}</li>
						<li><b>@@user_reg_ip@@</b> {if $account.ipaddress}{$account.ipaddress}{else}-{/if}</li>
					</ul>
				</div>
				{if $acp_bans}
				<h3 class="find-bans">@@find_possible_bans@@</h3>
				{/if}
			</div>
		</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
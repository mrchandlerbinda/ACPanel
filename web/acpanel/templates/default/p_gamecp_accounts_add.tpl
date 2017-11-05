{if !$iserror}
	{literal}
	<script type='text/javascript' src='acpanel/scripts/js/jquery.datetimeentry.pack.js'></script>
	<script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$.datetimeEntry.setDefaults({spinnerImage: 'acpanel/scripts/js/images/spinnerBlue.png',spinnerBigImage: 'acpanel/scripts/js/images/spinnerBlueBig.png'});
				$('#defaultEntry-1').datetimeEntry({datetimeFormat: 'D-O-Y, H:M'});
				$('.block_content a[rel*=facebox]').facebox();
			});
		})(jQuery);

		function fill(thisValue)
		{
			jQuery('[name="username"]').val(thisValue);
			checkInputField(jQuery('[name="username"]'), window.event, 1);
			setTimeout("jQuery('#usernames').fadeOut();", 600);
		}
	
		function loadUserInfo(uid)
		{
			jQuery('#userInfoBox').html(
				jQuery('<div>')
				.addClass('center-img-block')
				.append(
					jQuery('<img>')
					.attr('src','acpanel/images/ajax-big-loader.gif')
					.attr('alt','@@loading@@')
				)
			);
	
			jQuery.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamecp',
				data:({'uid' : uid,'go' : 9}),
				success:function(result) {
					if( result.indexOf('<ul>') + 1)
					{
						jQuery('#userInfoBox').html(result);
					}
					else
					{
						jQuery('#userInfoBox').html('');
					}
				}
			});
		}
	
		function checkInputField(e, k, l) 
		{
			var str = jQuery(e).val();
			var lyes = (typeof(l)!='undefined') ? l : 0;
	
			if(jQuery(e).attr('name') == "username")
			{
				if(str.length > 0)
				{
					jQuery(e).addClass('load');
					jQuery(e).css('background-color','#FEFEFE');
	
					jQuery.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_gamecp',
						data:({'username' : str,'go' : 8}),
						success:function(result) {
							if(result.length > 0)
							{
								var found = result.match(/;\">(.+?)<\/li/i);
	
								if(str != found[1])
								{
									if( !lyes )
									{
										jQuery('#usernames').fadeIn();
										jQuery('#valuesList').html(result);
									}
	
									jQuery('.infoMsg').removeClass('success').addClass('err').html('');
									jQuery('.profile-link').html('');
									jQuery('#userInfoBox').html('');
								}
								else
								{
									jQuery('#usernames').fadeOut();
									jQuery('#valuesList').html(result);
	
									var uidSplit = jQuery("#valuesList ul").attr('id').split("_");
									var uidVal = uidSplit[1];
	
									jQuery('.infoMsg').removeClass('err').addClass('success').html('');
	
									jQuery('.profile-link').html(
										jQuery('<a>').attr('href','{/literal}{$home}{literal}?cat={/literal}{$cat_users}{literal}&do={/literal}{$cat_user_edit}{literal}&t=0&id=' + uidVal).attr('title','@@user_profile_link@@').attr('target','_blank').html('@@user_profile@@')
									);
									jQuery(e).removeClass('load');
									loadUserInfo(uidVal);
								}
							}
							else
							{
								if( lyes )
								{
									jQuery(e).css('background-color','#ffc9c9');
								}
								jQuery('#usernames').fadeOut();
								jQuery('.infoMsg').removeClass('success').addClass('err').html('');
								jQuery('.profile-link').html('');
								jQuery('#userInfoBox').html('');
							}
							jQuery(e).removeClass('load');
						}
					});  
				}
				else
				{
					jQuery(e).css('background-color','#ffc9c9');
					jQuery('#usernames').fadeOut();
					jQuery('.infoMsg').removeClass('success').addClass('err').html('');
					jQuery('.profile-link').html('');
					jQuery('#userInfoBox').html('');
				}
			}
			else
			{
				if(str.length > 0)
				{
					if( jQuery(e).attr('name') == 'player_nick' || jQuery(e).attr('name') == 'password' )
					{
						if( jQuery(e).hasClass('intro') )
						{
							jQuery(e).css('background-color','#ffc9c9');
						}
						else
						{
							jQuery(e).css('background-color','#FEFEFE');
							return 1;
						}
					}
					else
					{
						jQuery(e).css('background-color','#FEFEFE');
						return 1;
					}
				}
				else
				{
					jQuery(e).css('background-color','#ffc9c9');
				}
				return 0;
			}
		}
	
		jQuery(document).ready(function($) {
			checkInputField($('[name="username"]'), window.event, 1);
	
			$('.block-access-mask select.styled').live("change", function(event) {
				var srvcnt = $('option:selected', this).attr('rel');
				if( !isNaN(+srvcnt) ) srvcnt = '<a href="{/literal}{$home}?cat={$section_current.id}&mask={literal}' + $('option:selected', this).val() + '" rel="facebox">' + srvcnt + '</a>';
				$(this).parents('.block-access-mask').find('.access-servers span').html(srvcnt);
				$(this).parents('.block-access-mask').find('a[rel*=facebox]').facebox();
			});
	
			$('.add-mask a').live("click", function(event) { 
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
	
				return false;
			});
	
			$('.block-access-mask h2 img').live("click", function(event) { 
				$(this).parents('.block-access-mask').remove();
			});
	
			$('[name="username"]').blur(function(event) {
				setTimeout(function(){ $('#usernames').fadeOut(); }, 600);
				checkInputField(this, event, 1);
			});
	
			$('[name="username"], [name="player_ip"], [name="steamid"]').keyup(function(event) {
		    		checkInputField(this, event);
			}).change(function(event) {
		    		checkInputField(this, event, 1);
			}).live("click", function(event) { 
				$(this).css('background-color','#FEFEFE');
			});
	
			$('[name="player_nick"], [name="password"]').blur(function() {
				var str = $(this).val();
				var def = ( $(this).attr('name') == "player_nick" ) ? '@@insert_nick@@' : '@@insert_pass@@';
				if( !str.length )
				{
					$(this).val(def).addClass('intro');
				}
				else
				{
					if( str != def )
					{
						$(this).removeClass('intro');
					}
				}
			}).keyup(function(event) {
				var str = $(this).val();
				if( !str.length )
				{
					$(this).removeClass('intro');
				}
			}).live("click", function(event) { 
				var str = $(this).val();
	
				if( (str == ( $(this).attr('name') == "player_nick" ) ? '@@insert_nick@@' : '@@insert_pass@@') && $(this).hasClass('intro') )
				{
					$(this).css('background-color','#FEFEFE');
					$(this).val('');
					$(this).removeClass('intro');
				}
			});
	
			$('#forma-add').submit(function(event) {
				var txt, errr = 0;
				var arrError = new Array();
	
				$('[name="username"], [name="player_nick"], [name="password"], [name="player_ip"], [name="steamid"]').not(':disabled').each(function (event) {
	
					if( $(this).attr('name') == 'username' )
					{
						if( $('.infoMsg').hasClass('err') )
						{
							txt = $(this).parent('p').find('label').text();
							arrError.push('@@field_not_valid@@ <b>' + txt + '</b>');
						}
					}
					else
					{
						var chk = checkInputField(this, event, 1);

						if( !chk )
						{
							txt = $(this).parent('p').find('label').text();
							arrError.push('@@field_not_valid@@ <b>' + txt + '</b>');
						}
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
						data:data + '&go=2',
						success:function(result) {
							if( result.indexOf('id="success"') + 1)
							{
								$('.profile-link').html('');
								$('#userInfoBox').empty();
								$('#forma-add input:text').not(':disabled').val('');
								$('[name="username"]').css('background-color','#ffc9c9');
								$('.infoMsg').removeClass('success').addClass('err').html('');

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
	
			$('#forma-add select[name="flag"]').change(function () {
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
		<form id="forma-add" action="" method="post">
			<div class="left-float-box">
				<div class="left-box-content">
					<p>
						<label>@@auth_type@@</label><br />
						<select class="styled" name="flag">
								<option value="1" selected>@@auth_nick@@</option>
								<option value="2">@@auth_ip@@</option>
								<option value="3">@@auth_steam@@</option>
						</select>
					</p>
					<p class="player-nickpass">
						<label>@@player_nick_password@@</label><br />
						<input type="text" class="text tiny intro" name="player_nick" value="@@insert_nick@@" autocomplete="off" />&nbsp;<input type="text" class="text tiny intro" name="password" value="@@insert_pass@@" autocomplete="off" />
					</p>
					<p class="player-ip hide">
						<label>@@player_ip@@</label><br />
						<input type="text" class="text small" name="player_ip" value="" disabled="disabled" />
					</p>
					<p class="player-steam hide">
						<label>@@player_steam@@</label><br />
						<input type="text" class="text small" name="steamid" value="" disabled="disabled" />
					</p>
					<div class="block-access-mask">
						{assign var="access_num" value="1"}
						<h2 style="position:relative;" id="mask-{$access_num}">@@access_mask@@</h2>
						<ul>
							<li>
								<select class="styled" name="access_mask_{$access_num}">
									{foreach from=$array_masks item=mask key=k}
										<option rel="{$mask.servers}" value="{$k}"{if $k == $default_mask.mask} selected{/if}>#{$k}: {$mask.flags}</option>
										{if $k == $default_mask.mask}{assign var="servers" value=$mask.servers}{/if}
									{/foreach}
								</select>
							</li>
							<li class="nofloat">
								<input id="defaultEntry-1" type="text" class="text small" name="access_expired_{$access_num}" value="{$default_mask.expired}" />
							</li>
						</ul>
						<div class="access-servers">@@ga_access_servers@@ <span>{if is_numeric($servers)}<a href="{$home}?cat={$section_current.id}&mask={$default_mask.mask}" rel="facebox">{/if}{$servers}{if is_numeric($servers)}</a>{/if}</span></div>
					</div>
					<p class="add-mask">
						<a style="border-bottom: 1px dashed;padding-bottom: 2px;" href="#">@@add_one_mask@@</a>
					</p>
					<p>
						<label>@@approved@@</label><br />
						<input class="radio" type="radio" name='approved' value="yes" checked="checked" /> @@yes@@&nbsp;
						<input class="radio" type="radio" name='approved' value="no" /> @@no@@
					</p>
					<p>
						<label>@@player_online@@</label><br />
						<input type="text" class="text small" name="online" value="0" />
					</p>
	
					<p>
						<label>@@player_points@@</label><br />
						<input type="text" class="text small" name="points" value="0" />
					</p>
					<p>
						<input type="submit" class="submit mid" value="@@save@@" />
					</p>
				</div>
			</div>
			<div class="right-float-box">
				<p>
					<label>@@username@@</label><br />
					<input type="text" class="text small" name="username" value="{$username|htmlspecialchars}" autocomplete="off" /><span class="infoMsg note"></span>
				</p>
				<div class="valuesBox" id="usernames" style="display: none;">
					<img src="acpanel/images/arrow.png" style="position: relative; top: -18px; left: 30px;" alt="" />
					<div class="valuesList" id="valuesList"></div>
				</div>
				<p class="profile-link"></p>
				<div id="userInfoBox"></div>
			</div>
		</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
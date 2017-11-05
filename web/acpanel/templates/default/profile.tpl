{literal}
<script type='text/javascript'>
{/literal}{if $get_in == 3}{literal}
	function checkFields(e, k)
	{
		var str = jQuery(e).val();
		var new_str = s = "";
		var maxPT = parseInt(jQuery('.balance-points span').text());
		var maxMM = parseFloat(jQuery('.balance-value span').text());

		for(var i=0; i < str.length; i++)
		{					
			s = str.substr(i,1);

			if( s != " " && isNaN(s) == false )
				new_str += s;
		}

		if( jQuery('[name="type"]').val() == 1 )
		{
			if( (eval(new_str) * {/literal}{$userbank.money_rate}{literal}).toFixed(2) > maxMM ) { new_str = Math.round(maxMM/{/literal}{$userbank.money_rate}{literal}); }
		}
		else
		{
			if( eval(new_str) > maxPT ) { new_str = maxPT; }
		}

		if(eval(new_str) == 0) { new_str = ''; }

		jQuery(e).val(new_str);

		return new_str;
	}

	function calculateSumm(type, cnt)
	{
		if( type == 1 )
		{
			return (cnt*{/literal}{$userbank.money_rate}{literal}).toFixed(2);
		}
		else
		{
			var comm = '{/literal}{$userbank.commission}{literal}';
			var t = cnt*{/literal}{$userbank.money_rate}{literal};
			if( comm > 0 )
				t = t - (t/100*parseFloat(comm));

			return t.toFixed(2);
		}
	}
{/literal}{/if}{literal}

	jQuery(document).ready(function($) {
		$('#block-tabs li').live('click', function() {
			if( !$(this).hasClass('tab-selected') )
			{
				window.location.href = '{/literal}{$home}{literal}?do=profile&s=' + $(this).attr('rel');
			}
		});
{/literal}{if $get_in == 3}{literal}
		var notebuy = '@@exchange_rate_note@@ {/literal}{$userbank.money_rate} {$userbank.money_suffix}{literal}';
		var notesell = notebuy + '{/literal}{if $userbank.commission > 0} + @@exchange_commission@@ {$userbank.commission}%{/if}{literal}';

		$('#forma-exchange select').change(function () {
			if( $('option:selected', this).val() == 1 )
			{
				$('.exchange-form-select div.note').html(notebuy);
				$('.exchange-form-list span').removeClass('skip-right');
			}
			else
			{
				$('.exchange-form-select div.note').html(notesell);
				$('.exchange-form-list span').removeClass('skip-left');
			}

			if( $('[name="cnt"]').val().length > 0 )
			{
				var cnt = checkFields($('[name="cnt"]'), event);
				if( cnt.length > 0 )
				{
					$('.exchange-form-list span').addClass('skip-' + (($('option:selected', this).val() == 1) ? 'left' : 'right')).text(calculateSumm($('option:selected', this).val(), $('[name="cnt"]').val()) + '{/literal} {$userbank.money_suffix}{literal}');
				}
				else $('.exchange-form-list span').text('');
			}
		});

		$('.all-points').click(function(event) {
			var maxPT = parseInt($('.balance-points span').text());
			var maxMM = parseFloat($('.balance-value span').text());
			var maxCNT = 0;

			if( $('[name="type"]').val() == 1 )
			{
				maxCNT = Math.round(maxMM/{/literal}{$userbank.money_rate}{literal});
			}
			else if( maxPT > 0 )
			{
				maxCNT = maxPT;
			}

			if( parseInt(maxCNT) > 0 )
			{
				$('[name="cnt"]').val(maxCNT);
				$('.exchange-form-list span').addClass('skip-' + (($('[name="type"]').val() == 1) ? 'left' : 'right')).text(calculateSumm($('[name="type"]').val(), maxCNT) + '{/literal} {$userbank.money_suffix}{literal}');
			}
			return false;
		});

		$('[name="cnt"]').live('keyup change', function(event) {
	    		checkFields(this, event);
			if( $(this).val().length == 0 )
			{
				$('.exchange-form-list span').removeClass('skip-left skip-right').text('');
			}
			else
			{
				$('.exchange-form-list span').addClass('skip-' + (($('[name="type"]').val() == 1) ? 'left' : 'right')).text(calculateSumm($('[name="type"]').val(), $(this).val()) + '{/literal} {$userbank.money_suffix}{literal}');
			}
		}).click(function() {
			$(this).css('background', '#FFFFFF');
		});

		$('#forma-exchange').submit(function(e)
		{
			e.preventDefault();

			if( $('[name="cnt"]').val().length > 0 )
			{
				$.blockUI({ message: null });
	
				var dataForm = $(this).serialize();
				var resultPT = ($('[name="type"]').val() == 1) ? parseInt(parseInt($('.balance-points span').text()) + parseInt($('[name="cnt"]').val())) : parseInt(parseInt($('.balance-points span').text()) - parseInt($('[name="cnt"]').val()));
				var resultMM = ($('[name="type"]').val() == 1) ? (parseFloat($('.balance-value span').text()) - parseFloat(calculateSumm(1, $('[name="cnt"]').val()))).toFixed(2) : (parseFloat($('.balance-value span').text()) + parseFloat(calculateSumm(2, $('[name="cnt"]').val()))).toFixed(2);
			
				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_payment',
					data:dataForm,
					success:function(result) {
						if(result.indexOf('id="success"') + 1)
						{
							$('.balance-points span').text(resultPT);
							$('.balance-value span').text(resultMM);
							$('[name="cnt"]').val('');
							$('.exchange-form-list span').removeClass('skip-left skip-right').text('');

							rePagination(2);
							$('.tablesorter').trigger('update');
							$('.tablesorter').trigger('applyWidgets', 'zebra');
							humanMsg.displayMsg(result,'success');
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}
					},
					complete:function() {
						$.unblockUI();
					}
				});
			}
			else
			{
				$('[name="cnt"]').css('background', '#FDC7C7');
			}

			return false;
		});
{/literal}{/if}{literal}
	});
</script>
{/literal}
<div class="block{if $get_in == 3} withsidebar{/if}">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@profile_head@@</h2>
	</div>
	<div class="block_content">
		<div id="block-tabs">
			<ul>
				{foreach from=$profilePageArray key=k item=i}
				<li{if $get_in == $k} class="tab-selected"{/if} rel="{$k}">{$i}</li>
				{/foreach}
			</ul>
		</div>
	{if $get_in == 1}
		<div class="accessMessage">
			<div class="message info"><p>@@username@@ {$array_user.username} | @@usergroup@@ {$array_user.usergroupname} | @@reg_date@@ {$array_user.reg_date}</p></div>
			{if $iserror}
			<div class="message errormsg"><p>{$iserror}</p></div>
			{/if}
			{if !empty($iswarn)}
			{foreach from=$iswarn item=w}
				<div class="message warning"><p>{$w}</p></div>
			{/foreach}
			{/if}
		</div>
		{if !$iserror}
		<div>
			<div class="left-float-block">
				<p class="p-profile" style="padding-top:15px;padding-bottom:15px;">
					<a href="{$home}?do=profile&edit=yes" rel="facebox">@@edit_pass_email@@</a>
					<img style="cursor: pointer;" title="@@avatar_size_info@@ {$avatar_width}x{$avatar_height}" align="right" src="acpanel/images/question.png" alt="@@avatar_size_info@@ {$avatar_width}x{$avatar_height}">
				</p>
				<form id="forma-edit" action="" method="post">
					<p class="p-profile">
						<label>@@timezone@@</label><br />
						<select class="styled" name="timezone">
							{foreach from=$array_tz key=k item=tz}
								<option value="{$k}"{if $array_user.timezone == $k} selected{/if}>{$tz}</option>
							{/foreach}
						</select>
					</p>
					<p class="p-profile">
						<label>@@icq@@</label><br />
						<input name="icq" type="text" class="text tiny" value="{$array_user.icq}" />
					</p>
					<p class="p-profile">
						<input name="uid" type="hidden" class="text" value="{$array_user.uid}" />
						<input type="submit" class="submit mid" value="@@save@@" /> &nbsp;
					</p>
				</form>
			</div>
			<div class="right-static-block avatar-block">
				<div class="avatar-img">
					<img class="map-block{if $array_user.avatar_date} custom-ava{/if}" style="width:{$avatar_width}px;height:{$avatar_height}px;"; src="{$array_user.avatar}" alt="" />
					<ul>
						<li><a href="#" onclick="return delete_avatar('{$array_user.uid}','@@confirm_del_avatar@@')">Delete</a></li>
					</ul>
				</div>
				<button id="uploadButton" class="submit long">@@upload_avatar@@</button>
			</div>
		</div>
		{/if}
	{elseif $get_in == 2}
		<div id="accountInfoBox">
			<div class="accessMessage">
				{if $iserror}
					<div class="message errormsg"><p>{$iserror}</p></div>
				{elseif $account_status != 0 AND $account_status != 1}
					<div class="message info"><p>@@acc_created@@ {$account.timestamp} | @@acc_online@@ {$account.last_time} | @@acc_online_all@@ {$account.online}</p></div>
					{if $account.flag == 1}
						@@account_help_info@@
					{/if}
				{/if}
				{if !empty($iswarn)}
					{foreach from=$iswarn item=w}
						<div class="message warning"><p>{$w}</p></div>
					{/foreach}
				{/if}
			</div>
			{if !$iserror}
			<div>
				{if $account_status != 0 AND $account_status != 1}
					<ul style="padding-bottom:0;">
						<li>@@auth_type@@ {if $account.flag == 1}@@auth_nick@@{elseif $account.flag == 2}@@auth_ip@@{else}@@auth_steam@@{/if}</li>
						<li>{if $account.flag == 1}@@player_nick@@ {$account.player_nick}{elseif $account.flag == 2}@@player_ip@@ {$account.player_ip}{else}@@player_steam@@ {$account.steamid}{/if}</li>
					</ul>
				{/if}
				{if $account_status >= 0}
					<p class="p-profile{if $account_status == 1 OR $account_status == 2} hide{/if}" style="padding-top:15px;padding-bottom:15px;">
						<input type="button" class="submit long" value="{if $account_status < 2}@@create_account@@{else}@@edit_account_info@@{/if}" href="{$home}?do=profile&s=2&account=edit" rel="facebox" />
						<a style="border:0; position:absolute; left:185px; top:22px;" href="#auth-info" rel="facebox"><img src="acpanel/images/question.png" alt="" /></a>	
					</p>
					<div id="auth-info" style="display: none; width: 600px;">@@auth_type_info@@</div>
				{/if}
			</div>
			{/if}
		</div>
		{if !$iserror}
		<div style="padding:5px;" id="ajaxContent"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		<div id="Pagination" style="padding-right:5px;"></div>
		<div id="Searchresult"></div>
		{/if}
	{elseif $get_in == 3}
		<div class="block_incontent">
			<div class="sidebar">
				<ul class="sidemenu">
					<li><a href="#sb1">@@user_services@@</a></li>
					<li><a href="#sb2">@@transaction_history@@</a></li>
					{if $userinfo.points !== FALSE}<li><a href="#sb3">@@exchanger@@</a></li>{/if}
				</ul>			
				<div id="user-balance">
					<div class="balance-label">@@user_balance@@</div>
					{if $userinfo.points !== FALSE}<div class="balance-points"><img src="acpanel/images/points.png" alt="" /> <span>{$userinfo.points}</span> @@points_suffix@@</div>{/if}
					<div class="balance-value"><img src="acpanel/images/money.png" alt="" /> <span>{$userinfo.money}</span> {$userbank.money_suffix}</div>
					{if !$iserror}<input type="button" class="submit mid" value="@@purshase@@" href="{$home}?do=profile&s=3&paygo=yes" rel="facebox">{/if}
				</div>
				{if $userinfo.uid == 1}
				<div id="test-access" style="padding: 9px 9px 5px 9px; text-align:left; font-size:90%; line-height:16px; border-top: 1px solid #CCCCCC; color:#777777;">
					@@admin_phone@@ +7(000)000-00-00<br />
					@@admin_email@@ my@my.com<br />
					@@admin_icq@@ 0000000<br />
				</div>
				{/if}
			</div>
			<div class="sidebar_content" id="sb1">
				{if $info_message}<div class="accessMessage">{$info_message}</div>{/if}
				<div id="ajaxContentS"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
				<div id="PaginationS"></div>
				<div id="SearchresultS"></div>
			</div>
			<div class="sidebar_content" id="sb2">
				<div id="ajaxContent"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
				<div id="Pagination"></div>
				<div id="Searchresult"></div>
			</div>
			{if $userinfo.points !== FALSE}
				<div class="sidebar_content" id="sb3">
					<form id="forma-exchange" method="post">
						<div class="exchange-form-select">
							<select class="styled" name="type">
								<option value="1" selected="selected">@@buy_points@@</option>
								{if $userbank.commission != "-1"}<option value="2">@@sell_points@@</option>{/if}
							</select>
							<div class="note">@@exchange_rate_note@@ {$userbank.money_rate} {$userbank.money_suffix}</div>
						</div>
						<div class="exchange-form-list">
							<label>@@count_points@@ ( <a style="text-transform:uppercase;" class="all-points" href="#">@@count_points_max@@</a> ):</label><input type="text" class="text tiny" name="cnt" value=""><span></span>
						</div>
						<input type="hidden" name='go' value="3" />
						<input type="submit" class="submit mid" value="@@to_exchange@@" />
					</form>
				</div>
			{/if}
		</div>
	{/if}
	</div>
	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
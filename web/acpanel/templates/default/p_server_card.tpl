{if !$iserror}
	{if !$get_in}
		{literal}
		<script type='text/javascript'>
		
			jQuery(document).ready(function($) {
		
				$('.add-favorite a').live('click', function() {
					$.blockUI({ message: null });
					var action = ( $('.add-favorite').hasClass('added') ) ? 0 : 1
					var serverSplitter = $(this).attr('id').split('_');
					var serverID = serverSplitter[1];
	
					$.ajax({
						type:'POST',
						url:'acpanel/ajax.php?do=ajax_homepage',
						data:'go=11&action=' + action + '&server=' + serverID,
						success:function(result) {
							$.unblockUI({
								onUnblock: function() {
									if(result.indexOf('id="success"') + 1)
									{
										if( $('.add-favorite').hasClass('added') )
										{
											$('.add-favorite').removeClass('added');
											$('.add-favorite a').text('@@add_favorites@@');
										}
										else
										{
											$('.add-favorite').addClass('added');
											$('.add-favorite a').text('@@remove_favorites@@');
										}
										humanMsg.displayMsg(result, 'success');
									}
									else
									{
										humanMsg.displayMsg(result, 'error');
									}
								} 
							});
						}
					});
	
					return false;
				});
			});
		</script>
		{/literal}
	{/if}
{/if}
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h1 style="text-transform:none;">{if !$iserror}<img style='vertical-align: middle' src='acpanel/images/status_{if $server_info.status}on{else}off{/if}.png' title='{if $server_info.status}Online{else}Offline{/if}' alt='{if $server_info.status}Online{else}Offline{/if}' /> <img src="acpanel/images/flags/{$server_info.country}" alt="" /> {/if}{$server_info.hostname|htmlspecialchars}</h1>
		{if !$iserror}
		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="t">
						<option value="0"{if $get_in == 0} selected{/if}>@@srv_info@@</option>
						<option value="1"{if $get_in == 1} selected{/if}>@@srv_userbar@@</option>
						<option value="2"{if $get_in == 2} selected{/if}>@@srv_rating@@ ({$server_info.rating})</option>
						<option value="3"{if $get_in == 3} selected{/if}>@@srv_stats@@</option>
					</select>
				</form>
			</li>
		</ul>
		{/if}
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		{if !$iserror}
			{if !$get_in}
				{if $server_info.description}<div class="message info"><p>{$server_info.description|htmlspecialchars}</p></div>{/if}
				<div style="position:relative;overflow:hidden;">
					<div style="background:none; margin-left:0; margin-bottom: 15px; width:60%;" class="block middle left">
						<p>
							<img style="vertical-align:middle;" src="acpanel/images/games/{$server_info.gametype.id}.png" alt=""> <a href="{$home}#srv={$server_info.gametype.id}" style="border-bottom: 1px dashed;color:#444;">{$server_info.gametype.name|htmlspecialchars}</a>
							::
							<a href="{$home}#mod={$server_info.mode_id}" style="border-bottom: 1px dashed;color:#444;">{$server_info.mode}</a>
							::
							<a href="{$home}#city={$server_info.city_id}" style="border-bottom: 1px dashed;color:#444;">{$server_info.city}</a>
						</p>
						<div class="right-float-block">
							<p style="padding-top:0;" class="p-table steam-connect"><span></span><a rel="nofollow" href="steam://connect/{$server_info.address}" title="@@steam_connect@@">{$server_info.address}</a></p>
							<p class="p-table"><b>@@map@@</b> <span id="current-map">{if $server_info.status}{$server_info.map}{else}-{/if}</span></p>
							<p class="p-table"><b>@@players@@</b> {if $server_info.status}<a href="{$home}?server={$server_info.id}&info=players" rel="facebox nofollow" title="@@players_info@@">{$server_info.players}/{$server_info.playersmax}</a>{else}-/-{/if}</p>
							<p class="p-table"><b>@@ping@@</b> {if $server_info.ping}{$server_info.ping} @@ms@@{else}-{/if}</p>
							{if $server_info.site}<p class="p-table"><b>@@website@@</b> <a href="http://{$server_info.site}" rel="nofollow">{$server_info.site}</a></p>{/if}
						</div>
						<div class="left-static-block">
							<img class="map-block" src="{if $server_info.map_path}acpanel/images/maps/{$server_info.gametype.id}/{$server_info.map}.jpg{else}acpanel/images/maps/noimage.jpg{/if}" alt="{if $server_info.map_path}{$server_info.map}{else}@@no_image@@{/if}" />
						</div>
					</div>
					<div style="background:none; margin-left:0; margin-bottom: 15px; width:38%;" class="block small right">
						<div style="margin-bottom:20px;" align="center">
							<div style="float: none;" class="rating">@@rating@@ {$server_info.rating}</div>
							<div style="color:#C30300; font-size:12px;">{$server_info.position} @@position_rating@@</div>
						</div>
						<h3>@@affected_rating@@</h3>
						<ul>
						{if $voted}
						<li style="height:23px; margin-bottom:10px; width:100%;">
							<div style="float:left; margin-right:10px;">@@start_voting@@</div>
							<div style="float:left; position:relative; top:3px;" class="voted">
								{$voted}
							</div>
						</li>
						{/if}
						{if $server_info.vkid}
						<li class="vkontakte" style="margin-bottom:10px; width:100%;">
							{literal}
							<div class="shareControl"><script type="text/javascript">
							  VK.init({
							    apiId: {/literal}{$server_info.vkid}{literal},
							    onlyWidgets: true
							  });
							</script></div>
							<div id="vk_like"></div>
							<script type="text/javascript">
							VK.Widgets.Like("vk_like", {type: "button",height: 18});
							</script>
							{/literal}
						</li>
						{/if}
						<li style="margin-bottom:10px; width:100%; height:18px;">
							<g:plusone size="medium"></g:plusone>
							{literal}
							<script type="text/javascript">
								window.___gcfg = {
									lang: 'ru'
								};

								(function() {
									var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
									po.src = 'https://apis.google.com/js/plusone.js';
									var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
								})();
							</script>
							{/literal}
						</li>
						<li class="add-favorite{if $server_info.favorite} added{/if}" style="margin-bottom:10px; width:100%;"><a id="server_{$server_info.id}" href="#">{if $server_info.favorite}@@remove_favorites@@{else}@@add_favorites@@{/if}</a></li>
						</ul>
					</div>
					<div style="width:1px;padding-left:1px;border-left:1px solid #ccc;position:absolute;height:100%;left:61%;"></div>
				</div>
			{elseif $get_in == 1}
				<div><img src="userbar/mb_{$server_info.id}_BA0014_480014_FFFFFF.png" alt="" /></div>
				<div><textarea class="styled" rows="1" cols="1" readonly="readonly">[url=http://{$smarty.server.SERVER_NAME}{$smarty.server.PHP_SELF}?cat={$section_current.id}&do={$cat_current.id}&server={$server_info.id}][img]http://{$smarty.server.SERVER_NAME}{$smarty.server.PHP_SELF|replace:"{$home}":''}userbar/mb_{$server_info.id}_BA0014_480014_FFFFFF.png[/img][/url]</textarea></div>
			{elseif $get_in == 2}
				<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>@@rating_calc_option@@</th>
							<th width='150px'>@@rating_calc_value@@</th>
							<th width='150px'>@@rating_calc_result@@</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$arrayRatingOptions item=output}
					<tr>
						<td align='left' style='border-left-width:0;'>{$output.option}</td>
						<td align='left' style='border-left-width:0;'>{$output.value}</td>
						<td style='border-right-width:0;'>{$output.result}</td>
					</tr>
					{/foreach}
					</tbody>
					{if empty($arrayRatingOptions)}
						<tfoot>
							<tr class="emptydata"><td colspan="3">@@empty_data@@</td></tr>
						</tfoot>
					{/if}
				</table>
			{elseif $get_in == 3}
				<div id="chart-online-box"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
			{/if}
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>

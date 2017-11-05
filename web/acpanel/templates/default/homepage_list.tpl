{if !empty($servers)}
	{literal}
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$('#servers-list .server-online a[rel*=facebox]').facebox();
			});
		}(jQuery));
	
		function previewScreenshot() {
			var xoffset = 20;
			var	yoffset = 22;
			
			jQuery("a.screenshot").hover(
				function(e) {
					this.t = this.title;
					this.title = "";
					
					jQuery("body").append("<p id='screenshot'><img src='"+ this.rel +"' alt='"+ this.t +"' /></p>");
					jQuery("#screenshot")
						.css("top",(e.pageY - xoffset) + "px")
						.css("left",(e.pageX + yoffset) + "px")
						.fadeIn("fast");
				},
				function() {
					this.title = this.t;
					jQuery("#screenshot").remove();
				}
			).click(function() {
				return false;
			});
			
			jQuery("a.screenshot").mousemove(function(e) {
				jQuery("#screenshot")
					.css("top",(e.pageY - xoffset) + "px")
					.css("left",(e.pageX + yoffset) + "px");
			});
		};
	
		jQuery(document).ready(function($) {
	
			previewScreenshot();
	
			$('.steam-connect span, .steam-connect a').hover(
				function(e) {
					$(this).parent().addClass('active');
			    },
				function() {
					$(this).parent().removeClass('active');
			    }
			).click(function() {
				var par = $(this).parent('td');
				window.location.href = $('a', par).attr('href');
				return false;
			});	
		});
	</script>
	{/literal}
	<table class="tablesorter monitoring" cellpadding="0" cellspacing="0" width="100%">
		<tbody>
		{foreach from=$servers item=server}
			<tr{if $server.vip} class="vip"{/if}>
				<td class="pos"><span title="{$server.position} @@position_in_rating@@">{if $server.position}#{$server.position}{else}{/if}</span></td>
				<td class="server-name">
					<div class="add-favorite"><img src="acpanel/images/favorites{if $server.favorite}_s{/if}.png" alt="@@add_favorite@@" title="@@add_favorite@@" /></div>
					<img src="acpanel/images/flags/{$server.country}" alt="" />&nbsp;<a href="{if !$server_cat}#{else}{$home}?cat={$cat}&do={$server_cat}&server={$server.id}{/if}">{$server.hostname|htmlspecialchars}</a>
				</td>
				<td class="steam-connect"><span></span><a rel="nofollow" href="steam://connect/{$server.address}" title="@@connect@@">{$server.address}</a></td>
				<td class="rating">@@rating@@ {$server.rating}</td>
			</tr>
			<tr id="server_{$server.id}"{if !$server.description} class="descr_no{if $server.vip} vip{/if}"{elseif $server.vip} class="vip"{/if}>
				<td class="gtype"{if $server.description} rowspan="2"{/if}><img src='acpanel/images/games/{$server.gametype}.png' alt='' /></td>
				<td class="server-online" colspan="3">.: <a style="color: #444444; text-decoration: underline;" href="#mod={$server.mode_id}">{$server.mode}</a> .: <a style="color: #444444; text-decoration: underline;" href="#city={$server.city_id}">{$server.city}</a> .: 
				{if !$server.status}
					@@srv_not_resp@@
				{else}
					@@online@@ <a href="{$home}?server={$server.id}&info=players" rel="facebox nofollow" title="@@players_info@@">{$server.players}/{$server.playersmax}</a>
					{if $server.gametype != 'minecraft'}
						@@map@@
						{if $server.map_path}<a style="color: #444444;" href="#" class="screenshot" rel="acpanel/images/maps/{$server.gametype}/{$server.map}.jpg" title="{$server.map}"><u>{else}<span style="color: #444444;">{/if}{$server.map}{if $server.map_path}</u></a>{else}</span>{/if}
					{/if}
				{/if}
					{if $rating}
					<div class="voted">
						{getVote serverid=$server.id}
					</div>
					{/if}
				</td>
			</tr>
			{if $server.description}
			<tr class="descr{if $server.vip} vip{/if}"><td colspan="3">{$server.description|htmlspecialchars}</td></tr>
			{/if}
		{/foreach}
		</tbody>
	</table>
{else}
	<div class="message errormsg"><p>@@empty_data_servers@@</p></div>
{/if}
{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {

		$('a[rel*=facebox]').facebox();

		$('.refresh').click(function() {
			var srv = $('#forma-edit input[name="real_address"]').val();
			var tp = $('#forma-edit input[name="real_gametype"]').val();

			$('#ajaxContent').html(
				$('<div>')
				.addClass('center-img-block')
				.append(
					$('<img>')
					.attr('src','acpanel/images/ajax-big-loader.gif')
					.attr('alt','@@refresh@@')
				)
			);

			$('#forma-edit input[type="submit"]').attr('disabled','disabled');

			refreshServerInfo(srv, tp);

			return false;
		});

		var button = $('#uploadButton');
		var maplink = $('img.map-block').attr('src');
		var mapname = $('span#current-map').text();
		var gametype = $('#forma-edit input[name="real_gametype"]').val();

		$.ajax_upload(button, {
			action: 'acpanel/upload.php',
			name: 'userfile',
			data: {map: mapname,type: 'img',gtype: gametype},
			onSubmit: function(file, ext) {
				this.disable();
				$("img.map-block").attr("src", "acpanel/images/ajax-160-120-upload.gif");
			},
			onComplete: function(file, response) {
				this.enable();
				setTimeout(function() {
					if(response.indexOf('id="error"') + 1)
					{
						humanMsg.displayMsg(response,'error');
						$("img.map-block").attr("src", maplink);
					}
					else
					{
						$("img.map-block").attr("src", response).attr("alt", mapname);
					}
				}, 2000);
			}
		});

		$('.left-static-block input[name="userfile"]').click(function() {
			if(maplink.indexOf('noimage.jpg') + 1)
			{
				return true;
			}
			else
			{
				if (confirm('@@already_map_pre@@ ' + mapname + ' @@already_map_post@@'))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		});
	});
</script>
{/literal}
<div class="right-float-block">
	<p class="p-table"><b>@@status@@</b> {$server_info.status} {$server_info.os} {$server_info.pass} {$server_info.vac}<a class="refresh" href="#">@@refresh@@</a></p>
	<p class="p-table"><b>@@country@@</b> {$server_info.country}</p>
	<p class="p-table"><b>@@ping@@</b> {$server_info.ping}</p>
	<p class="p-table"><b>@@map@@</b> <span id="current-map">{$server_info.map}</span></p>
	<p class="p-table"><b>@@players@@</b> {$server_info.online}</p>
	<div id="players-info" style="width: 600px; display: none;">
		<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th>@@nick@@</th>
					<th>@@frags@@</th>
					<th>@@time@@</th>
				</tr>
			</thead>

			<tbody>
			{assign var='frags' value=0}
			{foreach from=$players_info key=num item=player name=players_info}
				{assign var='frags' value=$frags+$player.2}
				<tr class="{cycle values='odd,even'}">
					<td>{$num}</td>
					<td>{$player.1|htmlspecialchars}</td>
					<td>{$player.2}</td>
					<td>{$player.3}</td>
				</tr>
			{/foreach}
			</tbody>
			<tfoot>
			{if empty($players_info)}
				<tr><td colspan="4" style="text-align: center;">@@empty_data@@</td></tr>
			{else}
				<tr>
					<td colspan="4" style="border: medium none;"><i>@@total_players@@ - {$smarty.foreach.players_info.total}, @@total_frags@@ - {$frags}</i></td>
				</tr>
			{/if}
			</tfoot>
		</table>
	</div>
</div>
<div class="left-static-block">
	<img class="map-block" src="{$server_info.map_path}" alt="{$server_info.map_info}" />
	<button id="uploadButton" class="submit long">@@upload_map@@</button>
</div>
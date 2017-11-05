<div class="block{if $no_decor} nodecor{/if}">
	{if !$no_decor}
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@cloud_head@@</h2>
	</div>
	<div class="block_content">
	{/if}
		<div align="center" id="nocontent">
			<p>@@cloud_flash_msg@@</p>
			<p>
				<a href="http://www.adobe.com/go/getflashplayer">
					<img src="acpanel/images/get_flash_player.gif" width="112" height="33" border="0" title="@@cloud_get_flash@@" />
				</a>
			</p>
		</div>
		{literal}
			<script type="text/javascript">
				var flash_tag = new SWFObject("acpanel/scripts/flashtags/tagcloud.swf", "tagcloud", "{/literal}{$cloud_width}{literal}", "{/literal}{$cloud_height}{literal}", "9", "#6fffff");
				var flash_tag_temp = encodeURIComponent("<tags>{/literal}{$tpl_tags_flash}{literal}</tags>")
				flash_tag.addParam("wmode", "transparent");
				flash_tag.addVariable("tcolor", "0x111111");
				flash_tag.addVariable("tcolor2", "0xe0e0e0");
				flash_tag.addVariable("hicolor", "0x0991E8");
				flash_tag.addVariable("tspeed", "{/literal}{$cloud_speed}{literal}");
				flash_tag.addVariable("distr", "true");
				flash_tag.addVariable("mode", "tags");
				flash_tag.addVariable("tagcloud", flash_tag_temp);
				flash_tag.write("nocontent");
			</script>
		{/literal}
	{if !$no_decor}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
	{/if}
</div>

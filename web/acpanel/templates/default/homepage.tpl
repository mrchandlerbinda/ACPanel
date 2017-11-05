<div style="position: relative;" class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>

		<ul>
			<li class="refresh"><a href="#">@@refreshing@@</a></li>
		</ul>
	</div>
	<div class="block_content">
		<div id="monitoring-tab">
			<ul class="tabmenu">
				<li class="selected"><img src="acpanel/images/bar_chart.png" alt="" /> @@server_rating@@</li>
				<li class="view_favorites"><img src="acpanel/images/favorites_s.png" alt="" /> @@my_favorites@@</li>
			</ul>
			<br style="clear:both;" />
		</div>
		<div id="servers-list">
			<div class="accessMessage">
				{if $iserror}
					<div class="message warning"><p>{$iserror}</p></div>
				{/if}
			</div>
			<div id="ajaxContent">{$servers_list}</div>
			<div id="Pagination"></div>
			<div id="Searchresult"></div>
		</div>
		<div style="display:none;" id="my-favorites">
			{if $favorites_list}
				{$favorites_list}
			{else}
				<div class="message warning"><p>@@no_favorites@@</p></div>
			{/if}
		</div>
	</div>

	<div id="filters">
		<form id="forma-filter" action="" method="get">
			<ul class="filters-list">
				<li style="padding-left: 0;" class="filters-item">
					<select class="chosen" name="srv" data-placeholder="@@all_servers@@...">
						<option value="0"></option>
						{foreach from=$filter_types key=k item=i}
						<option value="{$k}">{$i.name} ({$i.cnt})</option>
						{/foreach}
					</select>
				</li>
				<li class="filters-item">
					<select class="chosen" name="mod" data-placeholder="@@all_mods@@...">
						<option value="0"></option>
						{foreach from=$filter_modes item=i}
						<option value="{$i.id}">{$i.name} ({$i.cnt})</option>
						{/foreach}
					</select>
				</li>
				<li class="filters-item">
					<select class="chosen" name="city" data-placeholder="@@all_city@@...">
						<option value="0"></option>
						{foreach from=$filter_cities item=i}
						<option value="{$i.id}">{$i.name} ({$i.cnt})</option>
						{/foreach}
					</select>
				</li>
			</ul>
		</form>
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>

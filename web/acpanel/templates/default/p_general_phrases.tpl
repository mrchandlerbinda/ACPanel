<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title}</h2>

		<ul id="select-list">
			{if $cat_addphrase_id}
				<li><a href="{$home}?cat={$section_current.id}&do={$cat_addphrase_id}" rel="facebox">@@add_phrase@@</a></li>
			{/if}
			<li>
				<form id="forma-tpl" action="" method="get">
					<select class="styled" name="s">
						<option value="0"{if $get_tpl == 0} selected{/if}>@@global_phrases@@</option>
						{foreach from=$array_tpl item=tpl}
							<option value="{$tpl.lp_id}"{if $get_tpl == $tpl.lp_id} selected{/if}>{$tpl.lp_name}</option>
						{/foreach}
					</select>
				</form>
			</li>
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						{foreach from=$array_lang item=lang}
							<option value="{$lang.lang_code}"{if $get_in == $lang.lang_code} selected{/if}>{$lang.lang_title}</option>
						{/foreach}
					</select>
				</form>
			</li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		<div id="ajaxContent"><div class="center-img-block"><img src="acpanel/images/ajax-big-loader.gif" alt="" /></div></div>
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
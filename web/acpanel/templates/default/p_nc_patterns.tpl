<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>

		<ul id="select-list">
			{if $cat_addptrn_id}<li><a href="{$home}?cat={$section_current.id}&do={$cat_addptrn_id}" rel="facebox">@@add_pattern@@</a></li>{/if}
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						<option value="1"{if $get_in == 1} selected{/if}>Rename-List</option>
						<option value="0"{if $get_in == 0} selected{/if}>White-List</option>
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
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@head@@</h2>
	</div>
	<div class="block_content">
		{if $iserror}
			<div class="message errormsg"><p>{$iserror}</p></div>
		{else}
			<div class="accessMessage"></div>
			<form id="forma-search" action="{$action}" method="get">
				<input type='hidden' name="cat" value="{$section_current.id}" />
				<input type='hidden' name="do" value="{$cat_current.id}" />
				<p>
					<label>@@search_word@@</label><br />
					<input type="text" name="word" class="text" />
				</p>
				<p>
					<label>@@search_word_code@@</label><br />
					<input type="text" name="code" class="text" />
				</p>
				<p>
					<label>@@pattern@@</label><br />
					<select class="styled" name="tpl">
						<option value="-1" selected="selected">@@all_phrases@@</option>
						<option value="0">@@general_phrases@@</option>
						{foreach from=$tpls item=n}
							<option value="{$n.lp_id}">{$n.lp_name}</option>
						{/foreach}
					</select>
				</p>
				<p>
					<label>@@productid@@</label><br />
					<select class="styled" name="productid">
						<option value="0" selected>@@all_products@@</option>
						<option value="ACPanel">ACPanel</option>
						{foreach from=$array_product key=k item=ttl}
							<option value="{$k}">{$k}</option>
						{/foreach}
					</select>
				</p>
				<p>
					<input type="submit" class="submit small" value="@@search@@" />
				</p>
			</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
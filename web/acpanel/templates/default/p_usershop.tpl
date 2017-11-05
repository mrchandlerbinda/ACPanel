<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h1>@@usershop_privileges_services@@</h1>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		{if !$iserror}
		<div id="ajaxContent">
			<table class="tablesorter store-item-list" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<td></td>
						<th>@@store_item_name@@</th>
						<th>@@store_item_duration@@</th>
						<th>@@store_item_price@@</th>
						<td></td>
					</tr>
				</thead>
		
				<tbody>
				{foreach from=$patterns item=pat}
					{cycle values='odd,even' assign='rowcolor'}
					<tr class="{$rowcolor}{if !$pat.description} no-desc{/if}">
						<td class="item-image"{if $pat.description} rowspan="2"{/if}><img src="acpanel/images/shop/{if $pat.image}{$pat.id}.jpg?{$pat.image}{else}noimage.png{/if}" alt="{$pat.name|htmlspecialchars}" /></td>
						<td class="item-name"><span>{$pat.name|htmlspecialchars}</span></td>
						<td>{$pat.item_duration}{if $pat.item_duration_select && $pat.duration_type != "date"} <span style="font-weight:600;color:#007F0E;" title="@@choose_period_info@@">+</span>{/if}</td>
						<td>{if $pat.price_mm == 0 AND $pat.price_points == 0}@@item_sale_free@@{else}{if $pat.price_mm > 0}<span class="price-mm">{$pat.price_mm_info}</span>{/if}{if $pat.price_points > 0}{if $pat.price_mm > 0}<span> + </span>{/if}<span class="price-pt">{$pat.price_points_info}</span>{/if}{/if}</td>
						<td class="buy-button"><button class="submit small" href="{$home}?cat={$section_current.id}&do={$cat_buy}&id={$pat.id}" rel="facebox">@@buy@@</button></td>
					</tr>
					{if $pat.description}
					<tr class="item-desc {$rowcolor}">
						<td style="width:150%;" colspan="4">{$pat.description|htmlspecialchars}</td>
					</tr>
					{/if}
				{/foreach}
				</tbody>
				{if empty($patterns)}
					<tfoot>
						<tr class="emptydata"><td colspan="5">@@empty_data@@</td></tr>
					</tfoot>
				{/if}
			</table>
		</div>
		<div id="Pagination"></div>
		<div id="Searchresult"></div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
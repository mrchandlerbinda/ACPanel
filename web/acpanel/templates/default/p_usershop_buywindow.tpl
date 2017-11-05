{if !$iserror}
{literal}
<script type='text/javascript'>
	function calculatePrice(e)
	{
		var priceBlock = jQuery('#calc-price');
		var priceMM = priceBlock.find('.price-mm-num').length;
		var pricePT = priceBlock.find('.price-pt-num').length;

		if( priceMM > 0 || pricePT > 0 )
		{
			var str = jQuery(e).val();
			var err = 0;

			if( str )
			{

				if( jQuery(e).attr('name') == 'servers_select' )
				{
					var srv = str.length;
					var srok = ( jQuery('[name="duration_select"]').val() ) ? parseInt(jQuery('[name="duration_select"]').val()) / parseInt(jQuery('[name="def_duration"]').val()) : 1;
				}
				else
				{
					var srok = ( jQuery('[name="def_duration"]').val() ) ? parseInt(str) / parseInt(jQuery('[name="def_duration"]').val()) : 1;
					var srv = ( jQuery('[name="servers_select"]').val() ) ? jQuery('[name="servers_select"]').val().length : 1;
				}

				if( priceMM > 0 )
				{
					priceMM = parseInt(srok * srv * parseInt(jQuery('[name="def_price_mm"]').val()));
					priceBlock.find('.price-mm-num').text(priceMM);
					if( priceMM > parseInt({/literal}{$user.money}{literal}) )
					{
						priceBlock.find('.price-mm').addClass('err-limit');
						err++;
					}
					else if( priceBlock.find('.price-mm').hasClass('err-limit') )
					{
						priceBlock.find('.price-mm').removeClass('err-limit');
					}
				}
		
				if( pricePT > 0 )
				{
					pricePT = parseInt(srok * srv * parseInt(jQuery('[name="def_price_pt"]').val()));
					priceBlock.find('.price-pt-num').text(pricePT);
					if( pricePT > {/literal}{$user.points}{literal} )
					{
						priceBlock.find('.price-pt').addClass('err-limit');
						err++;
					}
					else if( priceBlock.find('.price-pt').hasClass('err-limit') )
					{
						priceBlock.find('.price-pt').removeClass('err-limit');
					}
				}

				if( err ) jQuery('.err-limit').show();
				else jQuery('.err-limit').hide();
			}
		}
	}

	jQuery(document).ready(function($) {

		$('#buy-window .chosen').chosen({allow_single_deselect:true}).change(function(e) {
			calculatePrice(this);
		});

		$('#forma-buy').submit(function() {
			$.blockUI({ message: null });
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_payment',
				data:data + '&go=22',
				success:function(result) {
					if(result.indexOf('id="success"') + 1)
					{
						$.unblockUI({ 
							onUnblock: function() {
								$('#facebox .close').click();
								humanMsg.displayMsg(result,'success');
							} 
						});
					}
					else
					{
						$.unblockUI({ 
							onUnblock: function() {
								humanMsg.displayMsg(result,'error');
							} 
						});
					}
				}
			});

			return false;
		});

		$('#more-servers').click(function() {
			if( $('#sale-item-servers li').hasClass('hide') )
			{
				$('#sale-item-servers li.hide').removeClass('hide').addClass('show');
				$(this).text('@@hide_more@@');
			}
			else
			{
				$('#sale-item-servers li.show').removeClass('show').addClass('hide');
				$(this).text('@@show_more@@');
			}
			return false;
		});
	});
</script>
{/literal}
{/if}
<div id="buy-window" style="width: 600px;">	
	<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@usershop_buywindow_title@@{if !$iserror}: "{$item.name|htmlspecialchars}"{/if}</h3>
	{if $iserror}
		<div class="message errormsg"><p>{$iserror}</p></div>
	{else}
		{if $item.description}<div class="message info"><p>{$item.description|htmlspecialchars}</p></div>{/if}
		<div id="action-form">			
			<form id="forma-buy" action="" method="post">
				<div id="sale-item-servers">
					{if $item.enable_server_select}
						<select class="chosen" name="servers_select" data-placeholder="@@store_select_server@@"  multiple="multiple">
							<option value="0"></option>
							{foreach from=$item.servers_access item=it key=k}
								<option value="{$k}">{$it.hostname} ({$it.address})</option>
							{/foreach}
						</select>
					{else}
						{if !empty($item.servers_access)}
							<label>@@store_servers@@{if count($item.servers_access) > 3} (<a id="more-servers" class="toggle-hide" href="#">@@show_more@@</a>){/if}</b></label><br />
							<ul style="padding-bottom:0;padding-top:5px;">
								{foreach name=disp from=$item.servers_access item=it key=k}
									<li{if $smarty.foreach.disp.iteration > 3} class="hide"{/if}>{$it.hostname} ({$it.address})</li>
								{/foreach}		
							</ul>
						{/if}
					{/if}
				</div>
				<div id="sale-item-duration">
					<label>@@store_item_duration@@</label><br />
					{if $item.item_duration_select}
						<select class="chosen" name="duration_select">
							<option value="{$item.item_duration}" selected>{$item.item_duration_info}</option>
							{foreach from=$array_durations item=it key=k}
								<option value="{$k}">{$it}</option>
							{/foreach}
						</select>
					{else}
						<input type="text" class="text small" name="duration_select" value="{$item.item_duration_info}" disabled="disabled" />
					{/if}
				</div>
				<div>
					<input type="hidden" name="id" value="{$item.id}" />
					<input type="hidden" name="def_duration" value="{$item.item_duration}" />
					<input type="hidden" name="def_price_mm" value="{$item.price_mm}" />
					<input type="hidden" name="def_price_pt" value="{$item.price_points}" />
					<input type="submit" class="submit mid" value="@@buy_item@@" />
					<span id="calc-price">{if $item.price_mm > 0}<span class="price-mm"><span class="price-mm-num">{$item.price_mm}</span> {$item.price_mm_info}</span>{/if}{if $item.price_points > 0}{if $item.price_mm > 0}<span> + </span>{/if}<span class="price-pt"><span class="price-pt-num">{$item.price_points}</span> {$item.price_points_info}</span>{/if}</span>
					<sup class="err-limit" style="display:none;">@@you_enough_money@@</sup>
				</div>
			</form>
		</div>
	{/if}
</div>
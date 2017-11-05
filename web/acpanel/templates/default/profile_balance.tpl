{literal}
<script type='text/javascript'>
	function checkPayField(e, k)
	{
		var str = jQuery(e).val();
		var new_str = s = "";

		for(var i=0; i < str.length; i++)
		{					
			s = str.substr(i,1);

			if( s != " " && isNaN(s) == false )
				new_str += s;
		}

		if(eval(new_str) == 0) { new_str = ''; }

		jQuery(e).val(new_str);

		return new_str;
	}

	jQuery(document).ready(function($)
	{
		$('[name="OutSum"], [name="cost"]').live('keyup change', function(event) {
	    		checkPayField(this, event);
		});

		$('#robokassa.noselect img, #a1pay.noselect img').hover(
			function () {
				$(this).parent().addClass('hover');
			},
			function () {
				$(this).parent().removeClass('hover');
			}
		);

		$('#robokassa.noselect img, #a1pay.noselect img').click(function() {
			var th = $(this).parent();
			var method = th.attr('id');
			var namesum = (method == 'robokassa') ? 'OutSum' : 'cost';
			var formaction = (method == 'robokassa') ? '{/literal}{$payment.robokassa.action}{literal}' : '{/literal}{$payment.a1pay.action}{literal}';

			th.removeClass('noselect').addClass('selected');
			th.parent().find('li').not(th).removeClass('selected').addClass('noselect');
			$('.' + method).removeAttr('disabled');
			$('#forma-payment').find('input:hidden').not('.' + method).attr('disabled', 'disabled');
			$('#forma-payment').find('input:text').attr('name', namesum).removeAttr('disabled');
			$('#forma-payment').attr('rel', method).attr('action', formaction);
			$('#payment-method').hide();

			return false;
		});

		$('#forma-payment').submit(function(e, data)
		{
			if( data == 'silent' ) return true;
			e.preventDefault();
			var form = $(this);
			var minPay = '{/literal}{$min_payment}{literal}';
			var method = form.attr('rel');

			if( method.length == 0 )
			{
				return false;
			}
			else if( (method == 'robokassa' && $('input[name=OutSum]', form).val().length == 0) || (method == 'a1pay' && $('input[name=cost]', form).val().length == 0) )
			{
				alert('@@payment_not_summ@@');
			}
			else if( (method == 'robokassa' && parseInt($('input[name=OutSum]', form).val()) < parseInt(minPay)) || (method == 'a1pay' && parseInt($('input[name=cost]', form).val()) < parseInt(minPay)) )
			{
				alert('@@payment_not_minimum@@');
			}
			else
			{
				var dataForm = form.serialize();
			
				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_payment',
					dataType: 'json',
					data:dataForm + '&method=' + method + '&go=1',
					success:function(result) {
						if( result.error ) alert(result.error);
						else
						{
							if( method == 'robokassa' ) $('input[name=InvId]', form).val(result.pid);
							else if( method == 'a1pay' ) $('input[name=order_id]', form).val(result.pid);

							if( method == 'robokassa' ) $('input[name=SignatureValue]', form).val(result.sig);
							else if( method == 'a1pay' ) $('input[name=key]', form).val(result.secretkey);

							form.trigger('submit', 'silent');
						}
					}
				});
			}

			return false;
		});
	});
</script>
{/literal}
<div style="width: 600px;">
	{if $iserror}
		<div class="message errormsg"><p>{$iserror}</p></div>
	{else}
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 15px;">@@purshase_balance@@</h3>
		{if count($payment) == 1}
			{foreach from=$payment item=item key=k}
				<form id="forma-payment" action="{$item.action}" method="post" rel="{$k}"{if $item.method == 'a1pay'} accept-charset="UTF-8"{/if}>
					{if $min_payment > 0}<div style="margin-top:0;" class="message info"><p>@@minimum_payment@@ {$min_payment} @@rub_suffix@@</p></div>{/if}
					<p>
						<label>@@payment_summ@@</label><br />
						<input type="text" class="text small" name="{if $item.method == 'robokassa'}OutSum{elseif $item.method == 'a1pay'}cost{/if}" value="{$min_payment}" />
					</p>
					<p>
						{if $item.method == 'robokassa'}
							<input type="hidden" name='MrchLogin' value="{$item.login}" />
							<input type="hidden" name='InvId' value="" />
							<input type="hidden" name='Desc' value="{$item.memo}" />
							<input type="hidden" name='SignatureValue' value="" />
							<input type="hidden" name='IncCurrLabel' value="{$item.currency}" />
							<input type="hidden" name='Culture' value="ru" />
							<input type="hidden" name='Email' value="{$item.email}" />
						{elseif $item.method == 'a1pay'}
							<input type="hidden" name="key" value="" />
							<input type="hidden" name="name" value="{$item.name}" />
							<input type="hidden" name="default_email" value="{$item.default_email}" />
							<input type="hidden" name="order_id" value="" />
						{/if}
						<input type="submit" class="submit mid" value="@@purshase@@" />
					</p>
				</form>
			{/foreach}
		{else}
			<form id="forma-payment" action="" method="post" rel="">
				{if $min_payment > 0}<div style="margin-top:0;" class="message info"><p>@@minimum_payment@@ {$min_payment} @@rub_suffix@@</p></div>{/if}
				<div style="position:relative; margin-bottom:5px;">
					<label>@@payment_method_select@@</label><br />
					<ul style="padding:0; margin:0;">
					{foreach from=$payment item=item key=k}
						<li id="{$k}" class="noselect"><img src="acpanel/images/shop/{$k}.jpg" alt="" /></li>
					{/foreach}
					</ul>
				</div>
				<div style="position:relative; clear:both;">
					<p>
						<label>@@payment_summ@@</label><br />
						<input type="text" class="text small" name="itemcost" value="{$min_payment}" disabled="disabled" />
					</p>
					<p>
						{foreach from=$payment item=item key=k}
							{if $item.method == 'robokassa'}
								<input class="robokassa" type="hidden" name='MrchLogin' value="{$item.login}" disabled="disabled" />
								<input class="robokassa" type="hidden" name='InvId' value="" disabled="disabled" />
								<input class="robokassa" type="hidden" name='Desc' value="{$item.memo}" disabled="disabled" />
								<input class="robokassa" type="hidden" name='SignatureValue' value="" />
								<input class="robokassa" type="hidden" name='IncCurrLabel' value="{$item.currency}" disabled="disabled" />
								<input class="robokassa" type="hidden" name='Culture' value="ru" disabled="disabled" />
								<input class="robokassa" type="hidden" name='Email' value="{$item.email}" disabled="disabled" />
							{elseif $item.method == 'a1pay'}
								<input class="a1pay" type="hidden" name="key" value="" disabled="disabled" />
								<input class="a1pay" type="hidden" name="name" value="{$item.name}" disabled="disabled" />
								<input class="a1pay" type="hidden" name="default_email" value="{$item.default_email}" disabled="disabled" />
								<input class="a1pay" type="hidden" name="order_id" value="" disabled="disabled" />
							{/if}
						{/foreach}
						<input type="submit" class="submit mid" value="@@purshase@@" />
					</p>
					<div id="payment-method"></div>
				</div>
			</form>
		{/if}
	{/if}
</div>
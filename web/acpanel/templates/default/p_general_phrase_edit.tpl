{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {
		$('#forma-edit').submit(function() {			if( $('input[name="code"]').val() == '' )
			{				$('.infoMsg').html('@@dont_empty@@');
			}
			else
			{				$('.infoMsg').html('');
				var data = $(this).serialize();

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_general_phrases',
					data:data + '&go=5',
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							$('.accessMessage').html('');
							humanMsg.displayMsg(result,'success');
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}
					}
				});
			}

			return false;
		});
	});
</script>
{/literal}
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title}</h2>

		<ul>
			<li><a href="{$action_uri}">@@back_url@@</a></li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{else}
		</div>
		<div>
			<form id="forma-edit" action="" method="post">
				<p>
					<label>@@code@@</label><br />
					<input type="hidden" class="text" name="lw_id" value="{$phrase.lw_id}" />
					<input type="text" class="text" name="code" value="{$phrase.lw_word}" /><span class="infoMsg note error"></span>
				</p>
				<p>
					<label>@@template@@</label><br />
					<select class="styled" name="tpl">
								<option value="0"{if !$phrase.lw_page} selected{/if}>@@global_phrases@@</option>
							{foreach from=$array_tpl item=tpl}
								<option value="{$tpl.lp_id}"{if $phrase.lw_page == $tpl.lp_id} selected{/if}>{$tpl.lp_name}</option>
							{/foreach}
					</select>
				</p>
				<p>
					<label>@@productid@@</label><br />
					<select class="styled" name="productid">
						<option value="ACPanel"{if !$phrase.productid OR $phrase.productid == 'ACPanel'} selected{/if}>ACPanel</option>
						{foreach from=$array_product key=k item=ttl}
							<option value="{$k}"{if $phrase.productid == $k} selected{/if}>{$k}</option>
						{/foreach}
					</select>
				</p>
				{foreach from=$array_lang item=lang}
				<p>
					<label>@@translate@@: {$lang.lang_title}</label><br />
					<textarea name="phrase_text[{$lang.lang_code}]">{$phrase.{$lang.lang_code}}</textarea>
				</p>
				{/foreach}
				<p>
					<input type="submit" class="submit mid" value="@@save@@" />
				</p>
			</form>
		</div>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
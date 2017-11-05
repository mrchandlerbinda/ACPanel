{literal}
	<script type='text/javascript' src='acpanel/scripts/js/jquery.datetimeentry.pack.js'></script>
	<script type='text/javascript'>
		(function ($) {
			$(function () {
				$.datetimeEntry.setDefaults({spinnerImage: 'acpanel/scripts/js/images/spinnerBlue.png',spinnerBigImage: 'acpanel/scripts/js/images/spinnerBlueBig.png'});
				$('#defaultEntry-1').datetimeEntry({datetimeFormat: 'D-O-Y, H:M'});
			});
		})(jQuery);

		jQuery(document).ready(function($) {
			$('#forma-add').submit(function(event) {

				var data = $(this).serialize();

				$.ajax({
					type:'POST',
					url:'acpanel/ajax.php?do=ajax_payment',
					data:data + '&go=17',
					success:function(result) {
						if( result.indexOf('id="success"') + 1)
						{
							$('#forma-add input:text').not(':disabled').val('');

							humanMsg.displayMsg(result,'success');
						}
						else
						{
							humanMsg.displayMsg(result,'error');
						}
					}
				});
	
				return false;
			});

			$('#forma-add select[name="duration_type"]').change(function() {
				if($(this).val() == "date")
				{
					$('.duration-length').addClass('hide');
					$('.duration-select').addClass('hide');
					$('.duration-length input').attr('disabled','disabled');
					$('.duration-select input').attr('disabled','disabled');
					$('.duration-date').removeClass('hide');
					$('.duration-date input').removeAttr('disabled');
				}
				else
				{
					$('.duration-length').removeClass('hide');
					$('.duration-select').removeClass('hide');
					$('.duration-length input').removeAttr('disabled');
					$('.duration-select input').removeAttr('disabled');
					$('.duration-select input:first').attr('checked','checked');
					$('.duration-date').addClass('hide');
					$('.duration-date input').attr('disabled','disabled');
				}
			});
		});
	</script>
{/literal}
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>@@usershop_admin_patterns_add@@</h2>

		<ul>
			<li><a href="{$action_uri}">@@back_url@@</a></li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
		{if $iserror}
			<div class="message warning"><p>{$iserror}</p></div>
		{/if}
		</div>
		{if !$iserror}
		<form id="forma-add" action="" method="post">
			<p>
				<label>@@pattern_group@@</label><br />
				<select class="styled" name="group">
					<option value="0" selected>@@pattern_group_not@@</option>
					{foreach from=$array_groups item=item key=k}
						<option value="{$k}">{$item}</option>
					{/foreach}
				</select>
			</p>
			<p>
				<label>@@pattern_name@@</label><br />
				<input type="text" class="text small" name="name" value="" />
			</p>
			<p>
				<label>@@pattern_description@@</label><br />
				<textarea class="text" name="description"></textarea>
			</p>
			<div class="block-pattern">
				<h2 style="position:relative;">@@pattern_price@@</h2>
				<p>
					<label>@@pattern_price_mm@@</label><br />
					<input type="text" class="text small" name="price_mm" value="0" />
				</p>
				<p>
					<label>@@pattern_price_points@@</label><br />
					<input type="text" class="text small" name="price_points" value="0" />
				</p>
			</div>
			<div class="block-pattern">
				<h2 style="position:relative;">@@pattern_duration@@</h2>
				<p>
					<select class="styled" name="duration_type">
						<option value="date" selected>@@duration_type_date@@</option>
						<option value="day">@@duration_type_day@@</option>
						<option value="month">@@duration_type_month@@</option>
						<option value="year">@@duration_type_year@@</option>
					</select>
				</p>
				<p class="duration-date">
					<input id="defaultEntry-1" type="text" class="text small" name="item_duration_date" value="" />
				</p>
				<p class="duration-length hide">
					<input type="text" class="text small" name="item_duration" value="" disabled="disabled" />
				</p>
				<p class="duration-select hide">
					<label>@@pattern_duration_select@@</label><br />
					<input class="radio" type="radio" name="item_duration_select" value="1" checked="checked" disabled="disabled" /> @@yes@@&nbsp;
					<input class="radio" type="radio" name="item_duration_select" value="0" disabled="disabled" /> @@no@@
				</p>
			</div>
			<div class="block-pattern">
				<h2 style="position:relative;">@@pattern_sale_items@@</h2>
				<p>
					<select class="styled" name="max_sale_items_duration">
						<option value="total" selected>@@per_all@@</option>
						<option value="day">@@per_day@@</option>
						<option value="week">@@per_week@@</option>
						<option value="month">@@per_month@@</option>
					</select>
				</p>
				<p>
					<input type="text" class="text small" name="max_sale_items" value="0" />
				</p>
			</div>
			<div class="block-pattern">
				<h2 style="position:relative;">@@pattern_sale_items_for_user@@</h2>
				<p>
					<select class="styled" name="max_sale_for_user_duration">
						<option value="total" selected>@@per_all@@</option>
						<option value="day">@@per_day@@</option>
						<option value="week">@@per_week@@</option>
						<option value="month">@@per_month@@</option>
					</select>
				</p>
				<p>
					<input type="text" class="text small" name="max_sale_for_user" value="0" />
				</p>
			</div>
			<p>
				<label>@@pattern_new_usergroup@@</label><br />
				<select class="styled" name="new_usergroup_id">
					<option value="0" selected>@@pattern_new_usergroup_not@@</option>
					{foreach from=$array_usergroups item=item key=k}
						<option value="{$k}">{$item}</option>
					{/foreach}
				</select>
			</p>
			{if !empty($array_servers)}
			<p>
				<label>@@pattern_servers_access@@</label><br />
				<select name="servers_access[]" multiple="multiple">
					{foreach from=$array_servers item=item key=k}
						<option value="{$k}">{$item.hostname} ({$item.address})</option>
					{/foreach}
				</select>
			</p>
			{/if}
			<p>
				<label>@@pattern_enable_server_select@@</label><br />
				<input class="radio" type="radio" name="enable_server_select" value="1" checked="checked" /> @@yes@@&nbsp;
				<input class="radio" type="radio" name="enable_server_select" value="0" /> @@no@@
			</p>
			<p>
				<label>@@pattern_add_flags@@</label><br />
				<input type="text" class="text small" name="add_flags" value="" />
			</p>
			<p>
				<label>@@pattern_add_points@@</label><br />
				<input type="text" class="text small" name="add_points" value="0" />
			</p>
			<p>
				<label>@@pattern_do_php_exec@@</label><br />
				<textarea class="text" name="do_php_exec"></textarea>
			</p>
			<p>
				<label>@@pattern_usergroups_access@@</label><br />
				<select name="usergroups_access[]" multiple="multiple">
					{foreach from=$array_usergroups item=item key=k}
						<option value="{$k}">{$item}</option>
					{/foreach}
				</select>
			</p>
			<p>
				<label>@@pattern_active@@</label><br />
				<input class="radio" type="radio" name="active" value="1" checked="checked" /> @@yes@@&nbsp;
				<input class="radio" type="radio" name="active" value="0" /> @@no@@
			</p>
			<p>
				<input type="submit" class="submit mid" value="@@save@@" />
			</p>
		</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
{literal}
<script type='text/javascript'>
	(function ($) {
		$(function () {
			$('#forma a[rel*=facebox]').facebox();
		});
	})(jQuery);
	
	jQuery(document).ready(function($) {
		$('.tablesorter').tablesorter({
			widgets: ['zebra'],
			headers: {3:{sorter: false}}
		});
	});
</script>
{/literal}
<form id="forma" action="" method="post">
	<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>@@user_name@@</th>
				<th>@@pattern_name@@</th>
				<th>@@date_privileges_start@@</th>
				<th>@@time_expired_pre@@</th>
				<td style="width:130px;">&nbsp;</td>
			</tr>
		</thead>

		<tbody>
		{foreach from=$privs item=pat}
			<tr>
				<td>{$pat.username}</td>
				<td>{$pat.pattern_name}</td>
				<td>{$pat.date_start}</td>
				<td{if $current_time > $pat.date_end AND $pat.date_end > 0} style="background-color:#F4D7D7;"{/if}>{$pat.time_expired}</td>
				<td class="delete">
					<a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_detail}&id={$pat.id}" rel="facebox">@@info_details@@</a>
				</td>
			</tr>
		{/foreach}
		</tbody>
		{if empty($privs)}
			<tfoot>
				<tr class="emptydata"><td colspan="5">@@empty_data@@</td></tr>
			</tfoot>
		{/if}
	</table>
</form>
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title}</h2>
	</div>
	<div class="block_content">
		{if $iserror}
			<div class="message errormsg"><p>{$iserror}</p></div>
		{else}
			<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>@@name@@</th>
						<td>&nbsp;</td>
					</tr>
				</thead>
				<tbody>
				{foreach from=$array_opt item=output}
					<tr>
						<td align='left'>{$output.title}</td>
						<td width='20%' class="delete"><a href="{$home}?cat={$section_current.id}&do={$output.categoryid}" rel="facebox">@@run_script@@</a></td>
					</tr>
				{/foreach}
				</tbody>
				{if empty($array_opt)}
					<tfoot>
						<tr class="emptydata"><td colspan="2">@@empty_data@@</td></tr>
					</tfoot>
				{/if}
			</table>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
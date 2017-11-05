{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {

	});
</script>
{/literal}
<div style="width: 100%;">
	<h2 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@check_tree_result@@</h2>
	{foreach from=$result key=key item=item}
	<div class="li-class"><font color="#0000ff">@@rule@@ {$key}:</font> {$item.title}</div>
	{if empty($item.result)}
	<div style="margin-left: 30px;" class="message success"><p>@@not_found@@</p></div>
	{else}
	<div style="margin-left: 30px;" class="message errormsg">
	{foreach from=$item.result item=output name=one}
		<p>
			{$output}
		</p>
	{/foreach}
	</div>
	{/if}
	{/foreach}
</div>
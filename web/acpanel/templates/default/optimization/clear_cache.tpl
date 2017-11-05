{literal}
<script type='text/javascript'>
	jQuery(document).ready(function($) {

	});
</script>
{/literal}
<div style="width: 100%;">
	<h2 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@clear_cache@@</h2>
	{if $result}
	<div class="message success"><p>@@clear_cache_success@@</p></div>
	{else}
	<div class="message errormsg"><p>@@clear_cache_error@@</p></div>
	{/if}
</div>
			{if !empty($sql_debug) && $debug_info}
			<div style="clear: both; display: block; margin-bottom: 20px; font-size: 9px; font-weight: normal;" class="message info">
				{foreach from=$sql_debug item=res}
					{$res}
				{/foreach}
			</div>
			{/if}	
		</div>
		<div style="clear: both;height: 80px;"></div>
	</div>
	<div id="footer">
		<p class="left">
			@@footer_page_gen@@ {$genpage} @@footer_gen_sec@@ (PHP - {$php_gen}%, SQL [{$count_query}] - {$sql_gen}%)
		</p>		
	</div>
</body>
</html>
<div class="block">
	<div class="block_head">
		<div class="bheadl"></div>
		<div class="bheadr"></div>

		<h2>{$head_title}</h2>

		<ul id="select-list">
			<li>
				<form id="forma-select" action="" method="get">
					<select class="styled" name="s">
						{foreach from=$general_sections item=section}
							<option value="{$section.section}"{if $get_in == $section.section} selected{/if}>{$section.label}</option>
						{/foreach}
					</select>
				</form>
			</li>
		</ul>
	</div>
	<div class="block_content">
		<div class="accessMessage">
			{if $iserror}
				<div class="message warning"><p>{$iserror}</p></div>
			{/if}
		</div>
		{if !$iserror}
			<form id="forma-options" action="" method="post">
				<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
					{foreach from=$general_options item=output}
					<tr>
						<td width='45%' style='border-right-width:0;'>{$output.description}</td>
						<td width='50%' align='left' style='border-left-width:0;'>
							{$output.content}
						</td>
						<td width='5%' align='right' style='border-right-width:0;'><a href="#help-{$output.id}" rel="facebox"><img src="acpanel/images/question.png" alt="@@help@@" title="@@help@@"></a><div id="help-{$output.id}" style="display: none; width: 600px;"><h3>{$output.description}</h3><p>{$output.help}</p></div></td>
					</tr>
					{/foreach}
				</table>

				<div class="tableactions">
					<input type="submit" class="submit tiny" value="@@apply@@" />
					<input type="hidden" name="go" value="1" />
				</div>
			</form>
		{/if}
	</div>

	<div class="bendl"></div>
	<div class="bendr"></div>
</div>
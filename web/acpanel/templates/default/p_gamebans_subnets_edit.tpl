{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
<script type='text/javascript'>

	function checkSubnet()
	{
		var ip = jQuery('[name="subipaddr"]').val();
		var mask = jQuery('[name="bitmask"]').val();

		jQuery.ajax({
			type:'POST',
			url:'acpanel/ajax.php?do=ajax_gamebans',
			data:'go=23&ip=' + ip + '&mask=' + mask,
			dataType:'json',
			success:function(result) {
				jQuery('#calc-range').text(result.range);
				jQuery('#calc-count').text(result.cnt);
			}
		});
	}

	jQuery(document).ready(function($) {

		// Form select styling
		$('form#forma-edit select.styled').select_skin();

		checkSubnet();

		$('[name="subipaddr"], [name="bitmask"]').keyup(function(event) {
	    		checkSubnet();
		}).change(function(event) {
	    		checkSubnet();
		});

		$('#forma-edit').submit(function() {
			$.blockUI({ message: null });
			var data = $(this).serialize();

			$.ajax({
				type:'POST',
				url:'acpanel/ajax.php?do=ajax_gamebans',
				data:data + '&go=15',
				success:function(result) {
					$('.accessMessage').html('');

					if(result.indexOf('id="success"') + 1)
					{
						rePagination(0);
						$('.tablesorter').trigger('update');
						$('.tablesorter').trigger('applyWidgets', 'zebra');
						humanMsg.displayMsg(result,'success');
					}
					else
					{
						humanMsg.displayMsg(result,'error');
					}
				},
				complete:function() {
					$.unblockUI();
				}
			});

			return false;
		});
	});
</script>
{/literal}
<div style="width: 600px;">
	<form id="forma-edit" action="" method="post">
		<h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@subnet_edit@@ #{$subnet_edit.id}</h3>
		<p>
			<label>@@subnet_mask@@</label><br />
			<input type="text" class="text small" name="subipaddr" value="{$subnet_edit.subipaddr}" />&nbsp;<input style="width: 50px;" type="text" class="text small" name="bitmask" value="{$subnet_edit.bitmask}" />
		</p>
		<ul>
			<li>@@ip_range@@ <span id="calc-range">-</span></li>
			<li>@@ip_count@@ <span id="calc-count">-</span></li>
		</ul>
		<p>
			<label>@@subnet_comment@@</label><br />
			<input type="text" class="text small" name="comment" value="{$subnet_edit.comment|htmlspecialchars}" />
		</p>
		<p>
			<label>@@subnet_active@@</label><br />
			<input class="radio" type="radio" name="approved" value="1"{if $subnet_edit.approved} checked="checked"{/if} /> @@yes@@&nbsp;
			<input class="radio" type="radio" name="approved" value="0"{if !$subnet_edit.approved} checked="checked"{/if} /> @@no@@
		</p>
		<p>
			<input type="hidden" name="id" value="{$subnet_edit.id}" />
			<input type="submit" class="submit mid" value="@@save@@" />
		</p>
	</form>
</div>
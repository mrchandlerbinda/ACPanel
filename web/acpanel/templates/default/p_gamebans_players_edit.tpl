{literal}
<script type='text/javascript' src='acpanel/scripts/js/jquery.datetimeentry.pack.js'></script>
<script type='text/javascript' src='acpanel/scripts/js/jquery.blockUI.js'></script>
<script type='text/javascript'>
    (function ($) {
        $(function () {
            $.datetimeEntry.setDefaults({spinnerImage: 'acpanel/scripts/js/images/spinnerBlue.png',spinnerBigImage: 'acpanel/scripts/js/images/spinnerBlueBig.png'});
            $('#defaultEntry').datetimeEntry({datetimeFormat: 'D-O-Y, H:M'});
        });
    })(jQuery);
 
    jQuery(document).ready(function($) {
 
        // Form select styling
        $('form#forma-edit select.styled').select_skin();
 
        $('.ajaxImg').click(function() {
            if (!$(this).hasClass('load')) {
                $(this).addClass('load');
                var data = $('#forma-edit input[name="server_ip"]').val();
 
                $.ajax({
                    type:'POST',
                    url:'acpanel/ajax.php?do=ajax_servers_control',
                    data:({address : data,'go' : 5}),
                    success:function(result) {
                        setTimeout(function() {
                            $('#forma-edit input[name="server_name"]').val(result);
                            $('.ajaxImg').removeClass('load');
                        }, 1000);
                    }
                });
            }
        });
 
        $('#forma-edit').submit(function() {
            $.blockUI({ message: null });
            var data = $(this).serialize();
 
            $.ajax({
                type:'POST',
                url:'acpanel/ajax.php?do=ajax_gamebans',
                data:data + '&go=5',
                success:function(result) {
                    $('.accessMessage').html('');
 
                    if(result.indexOf('id="success"') + 1)
                    {
                        rePagination(0);
                        $('.tablesorter').trigger('update');
                        $('.tablesorter').trigger('applyWidgets', 'zebra');
                        humanMsg.displayMsg(result,'success');
                        $('[name="ban_status_old"]').val($('[name="ban_status_new"]').val());
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
{/literal}
        {if array_key_exists($ban_edit.server_ip, $array_servers)}
{literal}
        $('[name="server_ip"]').change(function() {
            var vl = $(this).val();
            var nm = $('option[value="' + vl + '"]', this).text();
            $('[name="server_name"]').val(nm);
        });
{/literal}
        {/if}
{literal}
        $('[name="ban_status_new"]').change(function() {
            if( $(this).val() != 1 )
            {
                $('.unban-reason').fadeIn('fast');
            }
            else
            {
                $('.unban-reason').fadeOut('fast');
            }
        });
    });
</script>
{/literal}
<div style="width: 600px;">
    <form id="forma-edit" action="" method="post">
        <h3 style="border-bottom: 1px solid #ddd; padding-bottom: 7px; margin-bottom: 20px;">@@ban_edit@@ #{$ban_edit.bid}</h3>
        <p>
            <select class="styled" name="ban_status_new">
                <option value="1"{if !$ban_edit.unban_created} selected{/if}>@@ban_active@@</option>
                <option value="0"{if $ban_edit.unban_created} selected{/if}>@@ban_passed@@</option>
            </select>
        </p>
        <p class="unban-reason"{if !$ban_edit.unban_created} style="display:none;"{/if}>
            <label>@@unban_reason@@</label><br />
            <input type="text" class="text small" name="unban_reason" value="{if $ban_edit.unban_reason}{$ban_edit.unban_reason|htmlspecialchars}{/if}" />
        </p>
        <p>
            <label>@@ban_player_nick@@</label><br />
            <input type="text" class="text small" name="player_nick" value="{$ban_edit.player_nick|htmlspecialchars}" />
        </p>
        <p>
            <label>@@ban_type@@</label><br />
            <select class="styled" name="ban_type">
                <option value="N"{if $ban_edit.ban_type == 'N'} selected{/if}>@@ban_by_nick@@</option>
                <option value="SI"{if $ban_edit.ban_type == 'SI'} selected{/if}>@@ban_by_ip@@</option>
                <option value="S"{if $ban_edit.ban_type == 'S'} selected{/if}>@@ban_by_steam@@</option>
            </select>
        </p>
        <p>
            <label>@@ban_player_ip@@</label><br />
            <input type="text" class="text small" name="player_ip" value="{$ban_edit.player_ip}" />
        </p>
        <p>
            <label>@@ban_cookie_ip@@</label><br />
            <input type="text" class="text small" name="cookie_ip" value="{$ban_edit.cookie_ip}" />
        </p>
        <p>
            <label>@@ban_player_steam@@</label><br />
            <input type="text" class="text small" name="player_id" value="{$ban_edit.player_id}" />
        </p>
        <p>
            <label>@@ban_created@@</label><br />
            <input id="defaultEntry" type="text" class="text small" name="ban_created" value="{$ban_edit.ban_created}" />
        </p>
        <p>
            <label>@@ban_length@@</label><br />
            <input type="text" class="text small" name="ban_length" value="{$ban_edit.ban_length}" /><span class="infoMsg note">{$ban_edit.ban_remain}</span>
        </p>
        <p>
            <label>@@ban_reason@@</label><br />
            <input type="text" class="text small" name="ban_reason" value="{$ban_edit.ban_reason|htmlspecialchars}" />
        </p>
        <p>
            <label>@@ban_server@@</label><br />
            {if array_key_exists($ban_edit.server_ip, $array_servers)}
            <select class="styled" name="server_ip">
                {foreach from=$array_servers item=server key=k}
                    <option value="{$k}"{if $k == $ban_edit.server_ip} selected{/if}>{$server}</option>
                {/foreach}
            </select>
            {else}
            <input type="text" class="text small" name="server_ip" value="{$ban_edit.server_ip}" />
            {/if}
        </p>
        {if !array_key_exists($ban_edit.server_ip, $array_servers)}
        <p style="padding: 0; margin: 0;">
            <label>@@ban_server_name@@</label>
        </p>
        <p class="p-load">
            <input type="text" class="text small" name="server_name" value="{$ban_edit.server_name}" /><span class="ajaxImg"></span>
        </p>
        {/if}
        <p>
            <label>@@ban_admin_nick@@</label><br />
            <input type="text" class="text small disabled" name="admin_nick" value="{$ban_edit.admin_nick|htmlspecialchars}" disabled="disabled" />{if $ban_edit.admin_uid}<span class="infoMsg note"><a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$ban_edit.admin_uid}" title="@@go_to_profile@@">@@go_to_profile@@</a></span>{/if}
        </p>
        <p>
            <label>@@ban_admin_ip@@</label><br />
            <input type="text" class="text small disabled" name="admin_ip" value="{$ban_edit.admin_ip}" disabled="disabled" />
        </p>
        <p>
            <label>@@ban_admin_id@@</label><br />
            <input type="text" class="text small disabled" name="admin_id" value="{$ban_edit.admin_id}" disabled="disabled" />
        </p>
        <p>
            {if array_key_exists($ban_edit.server_ip, $array_servers)}
            <input type="hidden" name="server_name" value="{$ban_edit.server_name}" />
            {/if}
            <input type="hidden" name="bid" value="{$ban_edit.bid}" />
            <input type="hidden" name="ban_status_old" value="{if $ban_edit.unban_created}0{else}1{/if}" />
            <input type="submit" class="submit mid" value="@@save@@" />
        </p>
    </form>
</div>
{literal}
<script type='text/javascript'>
    (function ($) {
        $(function () {
            // Check / uncheck all checkboxes
            $('.check_all').click(function() {
                $(this).parents('form').find('input:checkbox').attr('checked', $(this).is(':checked'));
            });
 
            // Modal boxes - to all links with rel="facebox"
            $('#forma a[rel*=facebox]').facebox()
        });
    })(jQuery);
 
    function remove_row(subjm,txt)
    {
        if (confirm(txt))
        {
            jQuery.ajax({
                type:'POST',
                url:'acpanel/ajax.php?do=ajax_gamebans',
                data:({id : subjm,'go' : 3}),
                success:function(result) {
                    if( result.indexOf('id="success"') + 1)
                    {
                        jQuery('.accessMessage').html('');
                        humanMsg.displayMsg(result,'success');
                        jQuery('tr#' + subjm).remove();
                        rePagination(-1);
                        jQuery('table').trigger('update');
                        jQuery('table').trigger('applyWidgets', 'zebra');
                    }
                    else
                    {
                        humanMsg.displayMsg(result,'error');
                    }
                }
            });
        }
        return false;
    }
 
    jQuery(document).ready(function($) {
        $('.tablesorter').tablesorter({
            widgets: ['zebra'],
            headers: {0:{sorter: false}}
        });
 
        $('#forma').submit(function() {
            var len = $("tbody input:checked").length;
            if (len > 0)
            {
                if (confirm($('input:submit',this).val() + '?'))
                {
                    var arr = new Array();
                    $('tbody input:checked').each( function() {
                        arr.push($(this).val());
                    });
   
                    $.ajax({
                        type:'POST',
                        url:'acpanel/ajax.php?do=ajax_gamebans',
                        data:({'marked_word[]' : arr,'go' : 4}),
                        success:function(result) {
                            if( result.indexOf('id="success"') + 1)
                            {
                                $('.accessMessage').html('');
                                humanMsg.displayMsg(result,'success');
                                rePagination(-arr.length);
                                $('table').trigger('update');
                                $('table').trigger('applyWidgets', 'zebra');
                            }
                            else
                            {
                                humanMsg.displayMsg(result,'error');
                            }
                        }
                    });
   
                    $(this).find('input:checkbox').attr('checked', $(this).is(''));
                }
            } else {
                alert($('input:hidden:last',this).val());
            }
   
            return false;
        });
    });
</script>
{/literal}
<form id="forma" action="" method="post">
    <table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th width="10"><input type="checkbox" class="check_all" /></th>
                <th>@@ban_created@@</th>
                <th>@@ban_player_name@@</th>
                <th>@@ban_player_reason@@</th>
                <th>@@ban_player_time@@</th>
                <th>@@ban_player_admin@@</th>
                <td>&nbsp;</td>
            </tr>
        </thead>
 
        <tbody>
        {foreach from=$bans item=item}
            <tr>
                <td><input type="checkbox" name="marked_word" value="{$item.bid}" /></td>
                <td>{$item.ban_created}</td>
                <td>{$item.country}&nbsp;{$item.player_nick|htmlspecialchars}</td>
                <td>{$item.ban_reason|htmlspecialchars}</td>
                <td{if $item.ban_remain} class="{if $item.unban_admin_uid}ban-removed{else}ban-remain{/if}"{/if}>{$item.ban_length}{$item.ban_remain}</td>
                <td>{if $item.admin_uid}<a href="{$home}?cat={$cat_users}&do={$cat_user_edit}&t=0&id={$item.admin_uid}">{/if}{$item.admin_nick|htmlspecialchars}{if $item.admin_uid}</a>{/if}</td>
                <td class="delete"><a href="{$home}?cat={$smarty.post.cat_current}&do={$smarty.post.cat_edit}&id={$item.bid}" rel="facebox">Edit</a> | <a href="#" onclick="return remove_row('{$item.bid}','@@confirm_del@@')">Delete</a></td>
            </tr>
        {/foreach}
        </tbody>
        {if empty($bans)}
            <tfoot>
                <tr class="emptydata"><td colspan="7">@@empty_data@@</td></tr>
            </tfoot>
        {/if}
    </table>
 
    <div class="tableactions">
        <input type="submit" class="submit tiny" value="@@del_selected@@" />
        <input type="hidden" value="@@not_selected@@" />
    </div>
</form>
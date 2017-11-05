<?php
 
if( !isset($_GET['search']) )
{
    $smarty->assign("action",$_SERVER['PHP_SELF']);
 
    $sub = $db->Query("SELECT id,hostname,address FROM `acp_servers` ORDER BY `id`", array(), $config['sql_debug']);
 
    if( FALSE === $sub )
    {
        $error = '@@tbl_srv_error@@';
    }
    else
    {
        if( is_null($sub) )
        {
            $error = '@@tbl_srv_empty@@';
        }
        else
        {
            foreach( $sub as $obj )
            {
                $servers[$obj->address] = $obj->hostname;
            }
 
            $smarty->assign("array_servers",$servers);
 
            $headinclude = "
                <link href='acpanel/templates/".$config['template']."/css/date_input.css' rel='stylesheet' type='text/css' />
                <script type='text/javascript' src='acpanel/scripts/js/jquery.date_input.js'></script>
                <script type='text/javascript'>
                    (function ($) {
                        $(function () {
                            // Date picker
                            $('input.date_picker').date_input();
                        });
                    })(jQuery);
 
                    jQuery(document).ready(function($) {
                        $('input:button').click(function() {
                            var data = $('#forma-search').serialize();
 
                            $.ajax({
                                type:'POST',
                                url:'acpanel/ajax.php?do=ajax_gamebans',
                                data:data + '&go=17',
                                success:function(result) {
                                    if( result.indexOf('id=\"success\"') + 1 )
                                    {
                                        $('.accessMessage').html('');
                                        humanMsg.displayMsg(result,'success');
                                        $('#forma-search').get(0).reset();
                                        $('#forma-search .cmf-skinned-select').each(function() {
                                            $('.cmf-skinned-text',this).text($('option:selected',this).text());
                                        });
                                    }
                                    else
                                    {
                                        $('.accessMessage').html('');
                                        humanMsg.displayMsg(result,'error');
                                    }
                                }
                            });
 
                            return false;
                        });
                    });
                </script>
            ";
        }
    }
}
else
{
    $go_page = "p_gamebans_search_result";
    $sqlconds = 'WHERE 1=1';
    $postout = array();
 
    if( is_array($_GET) )
    {
        date_default_timezone_set('UTC');
        foreach( $_GET as $var => $value )
        {
            switch($var)
            {
                case "type_all":
 
                    if( $value != 'yes' && isset($_GET['ban_type']) )
                    {
                        $sqlconds .= " AND ban_type IN ('{ban_type}')";                       
                        $postout['ban_type'] = $_GET['ban_type'];
                    }
 
                    break;
 
                case "server_all":
 
                    if( $value != 'yes' && isset($_GET['server_ip']) )
                    {
                        if( ($srch = array_search(0, $_GET['server_ip'])) === FALSE )
                            $sqlconds .= " AND server_ip IN ('{server_ip}')";
                        else
                            $sqlconds .= " AND (server_ip IN ('{server_ip}') OR server_name = 'website')";
                        $postout['server_ip'] = $_GET['server_ip'];
                    }
 
                    break;
 
                case "startdate":
 
                    $value = trim($value);
                    if( $value )
                    {
                        $sqlconds .= " AND ban_created >= '{startdate}'";
                        $postout[$var] = get_datetime(strtotime($value), false, true);
                    }
                    break;
 
                case "enddate":
 
                    $value = trim($value);
                    if( $value )
                    {
                        $sqlconds .= " AND ban_created <= '{enddate}'";
                        $postout[$var] = strtotime($value);
                    }
                    break;
 
                case "srok_start":
 
                    $value = trim($value);
                    if( $value == "" )
                    {
                        $sqlconds .= " AND ban_length = 0";
                        $postout[$var] = -1;
                    }
                    elseif( is_numeric($value) && $value > 0 )
                    {
                        $sqlconds .= " AND ban_length >= {srok_start}";
                        $postout[$var] = $value;
                    }
                    break;
 
                case "srok_end":
 
                    $value = trim($value);
                    if( is_numeric($value) && $value > 0 )
                    {
                        $sqlconds .= " AND ban_length <= {srok_end} AND ban_length != 0";
                        $postout[$var] = $value;
                    }
                    break;
 
                case "player_nick":
                case "admin_nick":
 
                    $value = trim($value);
                    if( $value && ($value{0} != '!' || ($value{0} == '!' && strlen($value) > 1)) )
                    {
                        if( $config['charset'] != 'utf-8' )
                        {
                            $value = iconv('utf-8', $config['charset'], $value);
                        }
 
                        $sqlconds .= ($value{0} != '!') ? " AND ".$var." LIKE '%".mysql_real_escape_string($value)."%'" : " AND ".$var." = '".mysql_real_escape_string(substr($value, 1))."'";
                        $postout[$var] = $value;
                    }
 
                    break;
 
                case "ban_reason":
                case "player_ip":
                case "admin_ip":
                case "cookie_ip":
                case "player_id":
                case "admin_id":
 
                    $value = trim($value);
                    if( $value )
                    {
                        $sqlconds .= " AND ".$var." LIKE '%{".$var."}%'";
                        $postout[$var] = $value;
                    }
 
                    break;
 
                case "search":
 
                    $search_where = $_GET['search'];
                    $postout[$var] = $value;
                    break;
            }
        }
    }
 
    if( $search_where )
    {
        $total_items = $db->Query("SELECT count(*) FROM ".(($search_where == 1) ? '`acp_bans`' : '`acp_bans_history`')." $sqlconds", $postout, $config['sql_debug']);
    }
    else
    {
        $total_items = $db->Query("SELECT count(*) FROM (
                (SELECT bid FROM `acp_bans_history` $sqlconds)
                UNION ALL
                (SELECT bid FROM `acp_bans` $sqlconds)
            ) temp", $postout, $config['sql_debug']);
    }
 
    foreach( $all_categories as $key => $value )
    {
        if( $search_editcat_id = array_search("p_gamebans_players_edit", $value) )
        {
            $postout['cat_edit'] = $key;
            break;
        }
    }
 
    $postout['go'] = 18;
    $postout['cat_current'] = $current_section_id;
    $postout = json_encode($postout);
 
    $headinclude = "
        <script type='text/javascript' src='acpanel/scripts/js/facebox.js'></script>
        <script type='text/javascript' src='acpanel/scripts/js/jquery.pagination.js'></script>
        <script type='text/javascript' src='acpanel/scripts/js/jquery.tablesorter.js'></script>
        <script type='text/javascript'>
 
            function pageselectCallback(page_id, total, jq) {
                jQuery('#ajaxContent').html(
                    jQuery('<div>')
                    .addClass('center-img-block')
                    .append(
                        jQuery('<img>')
                        .attr('src','acpanel/images/ajax-big-loader.gif')
                        .attr('alt','@@refreshing@@')
                    )
                );
 
                var pg_size = ".$config['pagesize'].";
                var first = (page_id*pg_size)+1, second = (page_id*pg_size)+pg_size;
 
                if(total < second)
                {
                    second = total;
                }
 
                if(!total)
                {
                    jQuery('#Searchresult').html('@@showing@@ 0 @@to@@ 0 @@of@@ <span>0</span>');
                }
                else
                {
                    jQuery('#Searchresult').html('@@showing@@ ' + first + ' @@to@@ ' + second + ' @@of@@ <span>' + total + '</span>');
                }
 
                jQuery.ajax({
                    type:'POST',
                    url:'acpanel/ajax.php?do=ajax_gamebans&offset=' + first + '&limit=' + pg_size,
                    data:".$postout.",
                    success:function(result) {
                        jQuery('#ajaxContent').html(result);
                    }
                });
 
                return false;
            }
 
            function rePagination(diff) {
                var total = parseInt(jQuery('#Searchresult span').text()) + diff;
 
                if(total == 0)
                {
                    jQuery('.tablesorter').append(jQuery('<tfoot>')
                        .append(jQuery('<tr>').addClass('emptydata')
                            .append(jQuery('<td>').attr('colspan', '6').html('@@empty_data@@'))
                        )
                    );
                }
 
                var pg_size = ".$config['pagesize'].";
                var set_page = parseInt(jQuery('.pagination span.active').not('.prev, .next').text()) - 1;
                var count_row = jQuery('.tablesorter tbody tr').length + diff;
 
                if(count_row <= 0 && diff < 0 && total && set_page)
                {
                    set_page = set_page - 1;
                }
 
                jQuery('#Pagination').pagination( total, {
                    num_edge_entries: 2,
                    num_display_entries: 8,
                    callback: pageselectCallback,
                    items_per_page: pg_size,
                    current_page: set_page
                });
            }
 
            jQuery(document).ready(function($) {
                $('#Pagination').pagination( ".$total_items.", {
                    num_edge_entries: 2,
                    num_display_entries: 8,
                    callback: pageselectCallback,
                    items_per_page: ".$config['pagesize']."
                });
            });
        </script>
    ";
 
    if(!$total_items) {
        $error = '@@search_empty@@';
    }
}
 
if(isset($error)) $smarty->assign("iserror",$error);
 
?>
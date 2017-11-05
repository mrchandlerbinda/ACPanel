<?php
 
$go_page = "page";
 
$arguments = array('catid'=>$cat_current['id']);
$result = $db->Query("SELECT * FROM `acp_pages` WHERE catid = '{catid}' LIMIT 1", $arguments, $config['sql_debug']);
 
if( is_array($result) )
{
    foreach ($result as $obj)
    {
        $arrContent = (array)$obj;
    }
 
    $smarty->assign("page_content",$arrContent['pagetext']);
}
 
if( $userinfo['edit_pages'] == 'yes' )
{
    $headinclude = "
            <link href='acpanel/templates/".$config['template']."/css/wysiwyg.css' rel='stylesheet' type='text/css' />
            <link href='acpanel/templates/".$config['template']."/css/farbtastic.css' rel='stylesheet' type='text/css' />
            <link href='acpanel/templates/".$config['template']."/css/wysiwyg.modal.css' rel='stylesheet' type='text/css' />
            <script type='text/javascript' src='acpanel/scripts/js/wysiwyg/farbtastic.js'></script>
            <script type='text/javascript' src='acpanel/scripts/js/jquery.wysiwyg.js'></script>
            <script type='text/javascript' src='acpanel/scripts/js/wysiwyg/wysiwyg.colorpicker.js'></script>
            <script type='text/javascript' src='acpanel/scripts/js/wysiwyg/wysiwyg.link.js'></script>
            <script type='text/javascript' src='acpanel/scripts/js/wysiwyg/wysiwyg.cssWrap.js'></script>
            <script type='text/javascript' src='acpanel/scripts/js/wysiwyg/wysiwyg.image.js'></script>
            <script type='text/javascript' src='acpanel/scripts/js/wysiwyg/wysiwyg.table.js'></script>
            <script type='text/javascript'>
 
                function edit_page()
                {
                    var valtxt = jQuery('div#rules').html();
 
                    jQuery('div#rules').html(
                        jQuery('<textarea>')
                        .attr('id','wysiwyg')
                        .attr('name','rulesreal')
                        .css('width','100%')
                        .html(valtxt)
                    );
 
                    jQuery('ul#editpage').html(jQuery('<li>')
                        .append(
                            jQuery('<a>')
                            .click(function() {
                                savehandler();
                                return false;
                            })
                            .attr('href','#')
                            .text('@@save@@')
                        )
                    ).append(jQuery('<li>')
                        .append(
                            jQuery('<a>')
                            .click(function() {
                                cancelhandler();
                                return false;
                            })
                            .attr('href','#')
                            .text('@@cancel@@')
                        )
                    );
 
                    jQuery('.block_head ul').each(function() { jQuery('li:first', this).addClass('nobg'); });
                    jQuery('#wysiwyg').wysiwyg({
                        plugins: {
                            autoload: true
                        },
                        controls: {
                            code: { visible: false },
                            undo: { visible: false },
                            redo: { visible: false },
                            colorpicker: { visible: true },
                            removeFormat: { visible: false },
                            html: { visible: true },
                            highlight: { visible: true }                               
                        }
                    });
 
                    return false;
                }
 
                function cancelhandler()
                {
                    var tempval = jQuery('div#rules textarea').text();
 
                    jQuery('div#rules').html(tempval);
 
                    jQuery('ul#editpage').html(jQuery('<li>')
                        .append(
                            jQuery('<a>')
                            .click(function() {
                                return edit_page();
                            })
                            .attr('href','#')
                            .text('@@edit@@')
                        )
                    );
 
                    jQuery('.block_head ul').each(function() { jQuery('li:first', this).addClass('nobg'); });
                }
 
                function savehandler()
                {
 
                    var txt = jQuery('#wysiwyg').wysiwyg('getContent');
                    var pageid = ".$arrContent['id'].";
 
                    jQuery('div#rules').html(txt);
 
                    jQuery.ajax({
                        type:'POST',
                        url:'acpanel/ajax.php?do=ajax_homepage',
                        data:{'go':2,'id':pageid,'content':txt},
                        success:function(result) {
                            if( result.indexOf('id=\"success\"') + 1)
                            {
                                humanMsg.displayMsg(result,'success');
 
                                jQuery('ul#editpage').html(jQuery('<li>')
                                    .append(
                                        jQuery('<a>')
                                        .click(function() {
                                            return edit_page();
                                        })
                                        .attr('href','#')
                                        .text('@@edit@@')
                                    )
                                );
 
                                jQuery('.block_head ul').each(function() { jQuery('li:first', this).addClass('nobg'); });
                            }
                            else
                            {
                                humanMsg.displayMsg(result,'error');
                            }
                        }
                    });
                }
 
            </script>
    ";
 
    $smarty->assign("edit_access", "true");
}
 
$smarty->assign("head_title", $cat_current['title']);
 
?>
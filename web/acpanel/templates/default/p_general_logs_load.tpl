<script type='text/javascript' src='acpanel/scripts/js/acp.general.logs.js'></script>
<table class="tablesorter" cellpadding="0" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th width="150">@@time@@</th>
            <th>@@user_login@@</th>
            <th>@@user_ip@@</th>
            <th>@@action@@</th>
            <th>@@info@@</th>
        </tr>
    </thead>
 
    <tbody>
    {foreach from=$array_logs key=k item=m}
        <tr>
            <td>{$m.timestamp}</td>
            <td>{$m.username|htmlspecialchars}</td>
            <td>{$m.ip}</td>
            <td>{$m.action}</td>
            <td>{$m.remarks|htmlspecialchars}</td>
        </tr>
    {/foreach}
    </tbody>
    {if empty($array_logs)}
        <tfoot>
            <tr class="emptydata"><td colspan="5">@@empty_data@@</td></tr>
        </tfoot>
    {/if}
</table>
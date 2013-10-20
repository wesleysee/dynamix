<table class="share_status small">
<thead><tr><td>Name</td><td width='15%'>Size</td><td width='15%'>Last Modified</td><td width='5%'>Delete</td></tr></thead>
<tbody>
<?foreach (glob("/var/log/lighttpd/*.log") as $log):?>
  <tr><td><a href='/update.htm?cmd=/usr/bin/tail%20-n%201000%20-f%20<?=$log?>&forkCmd=Start' id='openlog' rel='shadowbox;height=600;width=800;' title='<?=$log?>'><?=$log?></a></td>
  <td><?=filesize($log)?></td><td><?=my_time(filemtime($log),"%F {$display['time']}")?></td>
  <td><a href='/plugins/dynamix/include/DeleteLogFile.php?log=<?=$log?>' target='progressFrame'><img src='/plugins/dynamix/images/delete.png' title='Delete file'></a></td></tr>
<?endforeach;?>
</tbody>
</table>
<div style="position:fixed;bottom:36px;font-style:italic"><?=exec("$fName -v|awk '/^lighttpd/ {print $1}'|sed 's/\// version: /'")?> &bullet; <?=exec(" php -v|awk '/^PHP/ {print $1,\"version:\",$2}'")?></div>
<?PHP
/* Copyright 2013, Bergware International.
 * Styles modified by Andrew Hamer-Adams
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
parse_str($argv[1],$_GET);
$plugin = $_GET['plugin'];
$ini = parse_ini_file("state/plugins/$plugin.ini");
$warn = $_GET['warn'];
?>
<script>
<?if ($warn):?>
$(function() {
  $('form').each(function(){$(this).change(function() {
    $.jGrowl("You have uncommitted form changes", {sticky: false, theme: "bottom", position: "bottom", life: 10000});});
  });
});
<?endif;?>
</script>
<form name="host_names" method="POST" action="/update.php" target="progressFrame" <?if ($warn):?>onsubmit="$('div.jGrowl-notification').trigger('jGrowl.close')"<?endif;?>>
<input type="hidden" name="#plugin"  value="<?=$plugin?>">
<input type="hidden" name="#cleanup" value="true">
<table class="settings">
  <tr><td style="font-size:12px;font-weight:bold">IP Address</td><td style="font-size:12px;font-weight:bold">User Name</td></tr>
<?
  $online = array();
  exec("lsof -i -n -P|awk -F'>' '/ESTABLISHED/ {print $2}'|cut -d':' -f1", &$online);
  foreach ($online as $host) {
    $ip = str_replace('.','_',$host);
    if (!isset($ini[$ip])) $ini[$ip] = "";
  }
  ksort($ini);
  foreach ($ini as $ip => $name) {
    echo "<tr><td style='font-weight:normal'>".str_replace('_','.',$ip)."</td><td><input type='text' name='$ip' value='$name'></td></tr>";
  }
?>
  <tr><td></td><td><input type="submit" name="#apply" value="Apply"></td></tr>
</table>
</form>
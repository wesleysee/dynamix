<?PHP
/* Copyright 2013, Bergware International.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
$plugin = 'dynamix.active.streams';
$ini = "state/plugins/$plugin.ini";
?>
<script>
function active_streams() {
  $('#streams').load('/plugins/dynamix/include/StreamList.php',"plugin=<?=$plugin?>",function(){setTimeout(active_streams,1000);});
}

$(function() {
  active_streams();
});
</script>
<link type='text/css' rel='stylesheet' href='/plugins/webGui/styles/browse.css'>

<table class='share_status small'>
<thead><tr><td width='11%'>User</td><td width='9%'>Share</td><td width='5%'>Type</td><td width='auto'>Stream</td><td width='8%'>Duration</td><td width='8%'>Size</td><td width='3%' style='text-align:center'>Halt</td></tr></thead>
<tbody id="streams"></tbody>
</table>
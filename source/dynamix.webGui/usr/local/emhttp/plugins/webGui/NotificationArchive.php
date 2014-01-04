<?PHP
/* Copyright 2013, Andrew Hamer-Adams, http://www.pixeleyes.co.nz.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
/* Adapted by Bergware International (December 2013) */
?>
<table class="share_status small">
<tr><td>Importance</td><td>Time</td><td>Plugin</td><td>Title</td><td>Description</td></tr>
<?
$path = $ini['path'];
$files = glob("$path/archive/*.notify");
$datetime = $ini['date'].' '.$ini['time'];

if (!empty($files)) {
  foreach($files as $file) {
    $ini_array[$i] = parse_ini_file($file);
    $ini_array[$i]['timestamp'] = date($datetime, $ini_array[$i]['timestamp']);
    echo '<tr>';
    echo '<td>'.$ini_array[$i]['importance'].'</td>';
    echo '<td>'.$ini_array[$i]['timestamp'].'</td>';
    echo '<td>'.$ini_array[$i]['plugin'].'</td>';
    echo '<td>'.$ini_array[$i]['subject'].'</td>';
    echo '<td>'.$ini_array[$i]['description'].'</td>';
    echo '</tr>';
    $i++;
  }
} else {
  echo '<tr><td></td><td colspan="4">No notifications available</td></tr>';
}
?>
</table>
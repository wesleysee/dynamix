<?PHP
/* Copyright 2013, Bergware International.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
$var = parse_ini_file("state/var.ini");
$ini = parse_ini_file("state/plugins/dynamix.webGui.ini",true);
$display = &$ini['display'];

switch ($var['fsState']) {
case 'Stopped':
  echo '<span class="red"><strong>Array Stopped</strong></span>'; break;
case 'Starting';
  echo '<span class="orange"><strong>Array Starting</strong></span>'; break;
default:
  echo '<span class="green"><strong>Array Started</strong></span>'; break;
}
if ($var['mdResync']!=0) {
  echo '&bullet;<span class="orange"><strong>';
  if ($var['mdNumInvalid']==0) {
    echo 'Parity-Check:';
  } else {
    if ($var['mdInvalidDisk']==0) {echo 'Parity-Sync:';} else {echo 'Data-Rebuild:';}
  }
  echo ' '.number_format(($var['mdResyncPos']/($var['mdResync']/100+1)),1,substr($display['number'],0,1),'').' %</strong></span>';
}
?>
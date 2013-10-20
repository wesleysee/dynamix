<?PHP
/* Copyright 2013, Bergware International
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
function my_temp($val) {
  global $unit;
  return "<span class='probe'>".($unit=='C' ? $val : round(9/5*$val+32))." &deg;$unit</span>";
}

parse_str($argv[1], $_GET);
$unit = $_GET['unit'];
unset($temp);
exec("sensors -A | awk '/^(CPU|M\/?B) Temp/ {print $3*1}'", &$temp);
if (isset($temp[0])) echo "<img src='/plugins/dynamix/icons/cpu.png' title='Processor' class='icon'>".my_temp($temp[0]).'&nbsp;';
if (isset($temp[1])) echo "<img src='/plugins/dynamix/icons/mb.png' title='Motherboard' class='icon'>".my_temp($temp[1]);
?>
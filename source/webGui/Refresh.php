<?PHP
/* Copyright 2013, Bergware International
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
parse_str($argv[1],$_GET);
$var = parse_ini_file("state/var.ini");
if (substr($var['fsState'],-3)!='ing') {
  echo '0';
} else {
  switch ($_GET['state']) {
  case 'Copying':
    echo "<strong>Copying, {$var['fsCopyPrcnt']}% complete...</strong>";
    break;
  case 'Clearing':
    echo "<strong>Clearing, {$var['fsClearPrcnt']}% complete...</strong>";
    break;
  default:
    echo '1';
    break;
  }
}
?>
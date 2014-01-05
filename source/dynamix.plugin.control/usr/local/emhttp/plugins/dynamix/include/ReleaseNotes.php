<?PHP
/* Copyright 2013, Bergware International & Andrew Hamer-Adams.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<link type="text/css" rel="stylesheet" href="/plugins/webGui/fonts/dynamix.css">
<link type="text/css" rel="stylesheet" href="/plugins/webGui/styles/template.css">
<div style="margin:20px">
<?
parse_str($argv[1],$_GET);
$contents = explode("\n",file_get_contents($_GET['file']));
foreach ($contents as $line) echo "$line<br>";
exec("rm -f {$_GET['file']}");
?>
</div>
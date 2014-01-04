<?PHP
/* Copyright 2013, Bergware International.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
parse_str($argv[1],$_GET);
$plugin = $_GET['plugin'];
$ini = parse_ini_file("boot/config/plugins/dynamix/$plugin.cfg");

function autoscale($size) {
  $units = array('B','KB','MB','GB','TB');
  $base = $size?floor(log($size,1000)):0;
  $unit = $units[$base];
  $size = round($size/pow(1000,$base),2);
  return number_format($size,(($size-intval($size)==0 || $size>=100) ? 0 : ($size>=10 ? 1 : 2)))." $unit";
}
function duration($time) {
  $days = floor($time/86400);
  $time -= $days*86400;
  $hour = floor($time/3600);
  $mins = $time/60%60;
  $secs = $time%60;
  return ($days ? $days.'-':'').$hour.':'.($mins>9 ? '':'0').$mins.':'.($secs>9 ? '':'0').$secs;
}
function select($item,$array) {
  foreach ($array as $entry) {if (strpos($entry,$item)!==false) return $entry;}
  return "";
}
$data=array(); $time=array(); $stat=array(); $list=array();
exec("LANG='en_US.UTF8' lsof /mnt/user|awk -F/ 'NF>4 && $0 !~ /\.AppleD(B|ouble)/'",&$data);
exec("LANG='en_US.UTF8' smbstatus -L|awk 'NR>3 {print}'|cut -c76-",&$time);
exec("lsof -i -n -P|awk '/ESTABLISHED/ {print $2,$9}'",&$stat);
$row = 0;
$now = exec("date +%s");

foreach ($data as $entry) {
  if (!strlen($entry)) continue;
  $info = explode('/',$entry,5);
  if (!is_dir('/mnt/user/'.$info[3].'/'.$info[4]) && !in_array($info[4],$list,true)) {
    $list[] = $info[4];
    $pid = preg_split('/\s+/',$info[0]);
    $line = select($pid[1],$stat);
    if ($line) {
      $x = strpos($line,'>')+1;
      $host = substr($line,$x,strpos($line,':')-$x);
    }
    $ip = str_replace('.','_',$host);
    $user = isset($ini[$ip])?$ini[$ip]:$host;
    $duration = "unavailable";
    $line = select($info[4],$time);
    if ($line) {
      $start = exec("echo \"$line\"|awk '{print $(NF-2),$(NF-3),$(NF-0),$(NF-1)}'");
      $duration = duration($now-exec("date -d \"$start\" +%s"));
    }
    $file = pathinfo($info[4]);
    echo "<tr class='tr_row".($row^=1)."'><td>".($user?$user:$host)."</td><td>{$info[3]}</td>";
    echo "<td><div class='icon-file icon-".strtolower($file['extension'])."' style='margin-left:6px;'></div></td>";
    echo "<td>".str_replace('/',' &bullet; ',$file['dirname'])." &bullet; ".$file['filename']."</td><td>$duration</td><td>".autoscale($pid[6])."</td>";
    echo "<td style='text-align:center'><a href='/plugins/dynamix/include/StreamKill.php?pid={$pid[1]}' target='progressFrame'><img src='/plugins/dynamix/images/halt.png' title='Stop stream'></a></td></tr>";
  }
}
if (!count($list)) echo "<tr class='tr_row1'><td colspan='3'></td><td colspan='4'>No active streams</td></tr>";
?>
<?PHP
/* Copyright 2013, LimeTech & Bergware International.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
parse_str($argv[1],$_GET);
$path    = $_GET['path'];
$var     = parse_ini_file("state/var.ini");
$devs    = parse_ini_file("state/devs.ini",true);
$disks   = parse_ini_file("state/disks.ini",true);
$ini     = parse_ini_file("state/plugins/dynamix.webGui.ini",true);
$display = &$ini['display'];
$screen  = '/tmp/screen_buffer';

$temps=0; $counts=0; $fsSize=0; $fsUsed=0; $fsFree=0; $reads=0; $writes=0; $errors=0;

include "plugins/webGui/include/Helpers.php";

function device_info($disk) {
  global $path, $var, $display, $screen;
  $href = $disk['name'];
  if ($href != 'preclear') {
    $name = my_disk($href);
    $type = strpos($href,'disk')===false ? $name : "Data";
  } else {
    $name = $disk['device'];
    $type = 'Preclear';
    $href = "{$disk['device']}&file=$screen";
  }
  $action = strpos($disk['color'],'blink')===false ? "down" : "up";
  $spin_disk = "";
  $icon = "icon gap";
  if ($display['spin']) {
    if ($var['fsState']=="Started") {
      if ($href != 'cache' && isset($disk['idx'])) {
        $cmd = "/root/mdcmd&arg1=spin{$action}&arg2={$disk['idx']}";
      } else {
        $cmd = ($action=='up' ? "smartctl&arg1=-H" : "hdparm&arg1=-y")."&arg2=/dev/{$disk['device']}";
      }
      $_a = "<a href='update.htm?cmd={$cmd}&runCmd=Apply' target='progressFrame'>"; $a_ = "</a>";
      $title = "Spin ".ucfirst($action);
    } else {
      $_a = ""; $a_ = "";
      $title = "Unavailable";
    }
    $spin_disk = "{$_a}<img src='/plugins/webGui/images/$action.png' title='$title' class='icon gap'>{$a_}";
    $icon = "icon";
  }
  $ball = "/plugins/webGui/images/{$disk['color']}.png";
  $blink = str_replace('on', 'blink', $ball);
  $optional_indicator = strpos($disk['color'],'grey')===false ? "<img src='$blink' class='icon'>Disk spun-down<br>" : "";
  if ($type != 'Flash') {
    $status = "<a href='#' class='info' onClick='return false'>
    <img src='$ball' class='$icon'><span>
    <img src='/plugins/webGui/images/green-on.png' class='icon'>Normal operation<br>
    <img src='/plugins/webGui/images/yellow-on.png' class='icon'>Invalid data content<br>
    <img src='/plugins/webGui/images/red-on.png' class='icon'>Disabled disk<br>
    <img src='/plugins/webGui/images/blue-on.png' class='icon'>New disk, not in array<br>
    <img src='/plugins/webGui/images/grey-off.png' class='icon'>No disk present<br>
    $optional_indicator
    </span></a>$spin_disk";
  } else {
    $icon = $display['spin'] ? "icon wide" : "icon gap";
    $status = "<img src='$ball' class='$icon'>";
  }
  $link = strpos($disk['status'], '_NP')===false ? "<a href='$path/$type?name=$href'>$name</a>" : $name;
  return $status.$link;
}
function device_browse($disk) {
  global $path;
  if ($disk['fsStatus'] == 'Mounted'):
    $dir = $disk['name']=="flash" ? "/boot" : "/mnt/{$disk['name']}";
    return "<a href='$path/Browse?dir=$dir'><img src='/plugins/webGui/images/explore.png' title='Browse $dir'></a>";
  else:
    return $disk['name']!="parity" ? "<img src='/plugins/webGui/images/noview.png'>" : "";
  endif;
}
function device_desc($disk) {
  global $display;
  return "{$disk['id']} ({$disk['device']})".($display['size'] ? " {$disk['size']}" : "");
}
function assignment($disk) {
  global $devs, $screen;
  $out = "<form method='POST' name=\"{$disk['name']}Form\" action='/update.htm' target='progressFrame'><input type='hidden' name='changeDevice' value='Apply'>";
  $out .= "<select name=\"slotId.{$disk['idx']}\" onChange=\"{$disk['name']}Form.submit()\">";
  $empty = ($disk['idSb']!="" ? "no device" : "unassigned");
  if ($disk['id']!=""):
    $out .= "<option value=\"{$disk['id']}\" selected>".device_desc($disk)."</option>";
    $out .= "<option value=''>$empty</option>";
  else:
    $out .= "<option value='' selected>$empty</option>";
  endif;
  foreach ($devs as $dev):
    if (!file_exists("$screen_{$dev['device']}")) $out .= "<option value=\"{$dev['id']}\">".device_desc($dev)."$warning</option>";
  endforeach;
  $out .= "</select></form>";
  return $out;
}
function disk_stopped($disk) {
  static $row = 0;
  echo "<tr class='tr_row".($row^=1)."'>";
  switch ($disk['status']) {
  case "DISK_NP":
    echo "<td>".device_info($disk)."</td>";
    echo "<td colspan='9'>".assignment($disk)."</td>";
  break;
  case "DISK_OK":
    echo "<td>".device_info($disk)."</td>";
    echo "<td>".assignment($disk)."</td>";
    echo "<td>".my_temp($disk['temp'])."</td>";
    echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  break;
  case "DISK_INVALID":
    echo "<td>".device_info($disk)."</td>";
    echo "<td>".assignment($disk)."</td>";
    echo "<td>".my_temp($disk['temp'])."</td>";
    echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  break;
  case "DISK_DSBL":
    echo "<td>".device_info($disk)."</td>";
    echo "<td>".assignment($disk)."</td>";
    echo "<td>".my_temp($disk['temp'])."</td>";
    echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  break;
  case "DISK_DSBL_NP":
  if ($disk['name']=="parity") {
    echo "<td>".device_info($disk)."</td>";
    echo "<td colspan='9'>".assignment($disk)."</td>";
  } else {
    echo "<td>".device_info($disk)."<span class='diskinfo'><em>Not installed</em></span></td>";
    echo "<td>".assignment($disk)."<em>{$disk['idSb']}</em></td>";
    echo "<td>-</td>";
    echo "<td><em>".my_scale($disk['sizeSb']*1024, $units).' '.$units."</em></td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  }
  break;
  case "DISK_DSBL_NEW":
    echo "<td>".device_info($disk)."</td>";
    echo "<td>".assignment($disk)."</td>";
    echo "<td>".my_temp($disk['temp'])."</td>";
    echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  break;
  case "DISK_NP_MISSING":
    echo "<td>".device_info($disk)."<span class='diskinfo'><em>Missing</em></span></td>";
    echo "<td>".assignment($disk)."<em>{$disk['idSb']}</em></td>";
    echo "<td>-</td>";
    echo "<td><em>".my_scale($disk['sizeSb']*1024, $units).' '.$units."</em></td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  break;
  case "DISK_WRONG":
    echo "<td>".device_info($disk)."<span class='diskinfo'><em>Wrong</em></span></td>";
    echo "<td>".assignment($disk)."<em>{$disk['idSb']}</em></td>";
    echo "<td>".my_temp($disk['temp'])."</td>";
    echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."<br><em>".my_scale($disk['sizeSb']*1024, $units).' '.$units."</em></td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  break;
  case "DISK_NEW":
    echo "<td>".device_info($disk)."</td>";
    echo "<td>".assignment($disk)."</td>";
    echo "<td>".my_temp($disk['temp'])."</td>";
    echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td></td>";
  break;
  }
  echo "</tr>";
}
function disk_started($disk) {
  global $temps, $counts, $fsSize, $fsUsed, $fsFree, $reads, $writes, $errors;
  static $row = 0;
  if (is_numeric($disk['temp'])) {
    $temps += $disk['temp'];
    $counts += 1;
  }
  $reads += $disk['numReads'];
  $writes += $disk['numWrites'];
  $errors += $disk['numErrors'];
  if (isset($disk['fsFree']) && $disk['name']!='parity') {
    $disk['fsUsed'] = $disk['size'] - $disk['fsFree'];
    $fsSize += $disk['size'];
    $fsFree += $disk['fsFree'];
    $fsUsed += $disk['fsUsed'];
  }
  $none = $disk['name']=="parity" ? '-' : '';
  echo "<tr class='tr_row".($row^=1)."'>";
  switch ($disk['status']) {
  case "DISK_NP":
    echo "<td>".device_info($disk)."</td>";
    echo "<td colspan='9'>Not installed</td>";
  break;
  case "DISK_DSBL_NP":
  if ($disk['name']=="parity") {
    echo "<td>".device_info($disk)."</td>";
    echo "<td colspan='9'>Not installed</td>";
  } else {
    echo "<td>".device_info($disk)."</td>";
    echo "<td><em>Not installed</em></td>";
    echo "<td>-</td>";
    echo "<td><em>".my_scale($disk['sizeSb']*1024, $units).' '.$units."</em></td>";
    echo "<td><em>".($disk['fsStatus']=='Mounted' ? my_scale($disk['fsUsed']*1024, $units).' '.$units : $none)."</em></td>";
    echo "<td><em>".($disk['fsStatus']=='Mounted' ? my_scale($disk['fsFree']*1024, $units).' '.$units : $disk['fsStatus'])."</em></td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>-</td>";
    echo "<td>".device_browse($disk)."</td>";
  }
  break;
  default:
    echo "<td>".device_info($disk)."</td>";
    echo "<td>".device_desc($disk)."</td>";
    echo "<td>".my_temp($disk['temp'])."</td>";
    echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."</td>";
    echo "<td>".($disk['fsStatus']=='Mounted' ? my_scale($disk['fsUsed']*1024, $units).' '.$units : $none)."</td>";
    echo "<td>".($disk['fsStatus']=='Mounted' ? my_scale($disk['fsFree']*1024, $units).' '.$units : $disk['fsStatus'])."</td>";
    echo "<td>".my_number($disk['numReads'])."</td>";
    echo "<td>".my_number($disk['numWrites'])."</td>";
    echo "<td>".my_number($disk['numErrors'])."</td>";
    echo "<td>".device_browse($disk)."</td>";
  break;
  }
  echo "</tr>";
}
function end_time($time) {
  $days = floor($time/1440);
  $time -= $days*1440;
  $hour = floor($time/60);
  $mins = $time%60;
  return plus($days,'day',($hour==0&&$mins==0)).plus($hour,'hour',($mins==0)).plus($mins,'minute',true);
}
switch ($_GET['device']):
case 'array':
  switch ($var['fsState']):
  case 'Started':
    foreach ($disks as $disk) {if ($disk['name']!='flash' && $disk['name']!='cache') disk_started(&$disk);}
    if ($display['total']) {
      $icon = $display['spin'] ? "icon wide" : "icon gap";
      echo "<tr class='tr_last'>";
      echo "<td><img src='/plugins/webGui/images/sum.png' class='$icon'>Total</td>";
      echo "<td>".my_count($var['mdNumProtected'])."</td>";
      echo "<td>".($counts>0?my_temp(round($temps/$counts, 1)):'*')."</td>";
      echo "<td>".my_scale($fsSize*1024, $units).' '.$units."</td>";
      echo "<td>".my_scale($fsUsed*1024, $units).' '.$units."</td>";
      echo "<td>".my_scale($fsFree*1024, $units).' '.$units."</td>";
      echo "<td>".my_number($reads)."</td>";
      echo "<td>".my_number($writes)."</td>";
      echo "<td>".my_number($errors)."</td>";
      echo "<td></td>";
      echo "</tr>";
    }
  break;
  case 'Stopped':
    foreach ($disks as $disk) {if ($disk['name']!='flash' && $disk['name']!='cache') disk_stopped(&$disk);}
  break;
  endswitch;
break;
case 'flash':
  $disk = &$disks['flash'];
  $disk['fsUsed'] = $disk['size'] - $disk['fsFree'];
  echo "<tr class='tr_row1'>";
  echo "<td>".device_info($disk)."</td>";
  echo "<td>".device_desc($disk)."</td>";
  echo "<td>*</td>";
  echo "<td>".my_scale($disk['size']*1024, $units).' '.$units."</td>";
  echo "<td>".my_scale($disk['fsUsed']*1024, $units).' '.$units."</td>";
  echo "<td>".my_scale($disk['fsFree']*1024, $units).' '.$units."</td>";
  echo "<td>".$disk['numReads']."</td>";
  echo "<td>".$disk['numWrites']."</td>";
  echo "<td>".$disk['numErrors']."</td>";
  echo "<td>".device_browse($disk)."</td>";
  echo "</tr>";
break;
case 'cache':
  if ($var['fsState']=='Stopped')
    disk_stopped(&$disks['cache']);
  else
    disk_started(&$disks['cache']);
break;
case 'open':
  $status = file_exists("/var/log/plugins/dynamix.disk.preclear") ? '' : '_NP';
  foreach ($devs as $dev) {
    $dev['name'] = 'preclear';
    $dev['color'] = 'blue-on';
    $dev['status'] = $status;
    echo "<tr class='tr_row".($row^=1)."'>";
    echo "<td>".device_info($dev)."</td>";
    echo "<td>".device_desc($dev)."</td>";
    echo "<td>*</td>";
    echo "<td>".my_scale($dev['size']*1024, $units).' '.$units."</td>";
    if (file_exists("/tmp/preclear_stat_{$dev['device']}")) {
      $text = exec("cut -d'|' -f3 /tmp/preclear_stat_{$dev['device']} | sed 's:\^n:\<br\>:g'");
      if (strpos($text,'Total time')===false) $text = 'Preclear in progress... '.$text;
      echo "<td colspan='6' style='text-align:right'><strong><em>$text</em></strong></td>";
    } else
      echo "<td colspan='6'></td>";
    echo "</tr>";
  }
break;
case 'parity':
  $data = array();
  $data[] = my_scale($var['mdResync']*1024, $units)." $units";
  $data[] = my_scale($var['mdResyncPos']*1024, $units)." $units (".number_format(($var['mdResyncPos']/($var['mdResync']/100+1)),1,substr($display['number'],0,1),'')." %)";
  $data[] = my_scale(($var['mdResyncDt']?$var['mdResyncDb']/$var['mdResyncDt']:0)*1024, $units, 1)." $units/sec";
  $data[] = end_time(max(1,round(((($var['mdResyncDt']*(($var['mdResync']-$var['mdResyncPos'])/($var['mdResyncDb']/100+1)))/100)/60),0)));
  $data[] = $var['sbSyncErrs'];
  echo implode(';',$data);
break;
endswitch;
?>
<?PHP
/* Copyright 2010, Lime Technology LLC.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
/* Adapted by Bergware International (December 2013) */
?>
<?
//Helper functions
include "plugins/webGui/include/Helpers.php";

function mk_option($select, $value, $text, $extra = "") {
  return "<option value='$value'".($value==$select ? " selected" : "").(strlen($extra) ? " $extra" : "").">$text</option>";
}
function mk_option_check($name, $value, $text = "") {
  if ($text) {
    $checked = strpos("$name,", "$value,")===false ? "" : " selected";
    return "<option value='$value'$checked>$text</option>";
  }
  if (strpos($name, 'disk')!==false) {
    $checked = strpos("$value,", "$name,")===false ? "" : " selected";
    return "<option value='$name'$checked>".my_disk($name)."</option>";
  }
}
function my_key() {
  $keyfile = exec("find /boot/config -name '*.key'");
  return strlen($keyfile) ? my_time(filemtime($keyfile)) : "";
}
function urlencode_path($path) {
  return str_replace("%2F", "/", urlencode($path));
}
function tab_title($text) {
  global $page;
  $file = "{$page['Root']}/icons/".strtolower(str_replace(' ','',$text)).".png";
  if (!file_exists("/usr/local/emhttp/$file")) $file = "plugins/webGui/icons/default.png";
  return "<img src='/$file' class='icon'>".my_disk($text);
}
// Return sorted set of pages on the indicated menu.
function find_pages($menu, $all = FALSE) {
  global $page_array;
  $pages = array();
  foreach ($page_array as $page) {
    if (!$page['Enabled'] && !$all) continue;
    $tok = strtok($page['Menu'], " ");
    while ($tok !== false) {
      $delim = strpos($tok, ":");
      if ($delim) {
        $t = substr($tok, 0, $delim);
        if ($t == $menu) {
          $key = substr($tok, $delim+1).$page['Name'];
          $pages[$key] = $page;
          break;
        }
      } else {
        if ($tok == $menu) {
          $pages[$page['Name']] = $page;
          break;
        }
      }
      $tok = strtok(" ");
    }
  }
  ksort( $pages);
  return $pages;
}
// Suppose we want to render the page "http://tower/Main/Disk?name=disk1"
// emhttp calls 'popen(cmdline)' where cmdline is:

// "cd /usr/local/emhttp; /usr/bin/php /plugins/webGui/template.php name=disk1&path=Main/Disk"
//                                     argv[0]                      argv[1]

// The output of popen() (i.e., the output generated by template.php) is written to
// the http socket (after http headers have been output).

// Parse the 'querystring'
// variables provided by emhttp:
//   path=<path>   page path, e.g., path=Main/Disk
//   prev=<path>   prev path, e.g., prev=Main (used to determine if page was refreshed)
parse_str($argv[1]);

// The current "task" is the first element of the path
$task = strtok($path, "/");
$page = null;

// Read emhttp status
$var     = parse_ini_file("state/var.ini");
$sec     = parse_ini_file("state/sec.ini",true);
$devs    = parse_ini_file("state/devs.ini",true);
$disks   = parse_ini_file("state/disks.ini",true);
$users   = parse_ini_file("state/users.ini",true);
$shares  = parse_ini_file("state/shares.ini",true);
$sec_nfs = parse_ini_file("state/sec_nfs.ini",true);
$sec_afp = parse_ini_file("state/sec_afp.ini",true);

// Dynamix additions
$dynamix = parse_ini_file("boot/config/plugins/dynamix/dynamix.webGui.cfg",true);
$confirm = &$dynamix['confirm'];
$display = &$dynamix['display'];

// Build the pages
$page_array = array();
foreach (glob("plugins/*/*.page", GLOB_NOSORT) as $entry) {
  $page = parse_ini_file($entry);
  $page['Name'] = basename($entry, ".page");
  $page['Root'] = dirname($entry);
  $page['Plugin'] = basename($page['Root']);

// assign defaults
  if (!isset($page['Author'])) $page['Author'] = "anonymous";
  if (!isset($page['Version'])) $page['Version'] = "unknown";
  if (!isset($page['Title'])) $page['Title'] = $page['Name'];
  if (!isset($page['Type'])) $page['Type'] = "Not much here.";
  if (!isset($page['Icon'])) $page['Icon'] = "/plugins/webGui/images/default.png"; else $page['Icon'] = "/{$page['Root']}/{$page['Icon']}";
  if (!isset($page['Menu'])) $page['Menu'] = "";

// handle conditional inclusion on menus
  if (isset($page['Cond']))
    eval("\$page['Enabled']=$page[Cond];");
  else
    $page['Enabled'] = true;

// add to page_array
  $page_array[$page['Name']] = $page;
}
// Here's the page we're rendering
$myPage = $page_array[basename($path)];

// Gittyup
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<title><?=$var['NAME']?>/<?=$myPage['Name']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="/plugins/webGui/fonts/dynamix.css">
<link type="text/css" rel="stylesheet" href="/plugins/webGui/styles/template.css">
<link type="text/css" rel="stylesheet" href="/plugins/webGui/styles/dynamix.css">
<link type="image/gif" rel="shortcut icon" href="/plugins/webGui/images/<?=$var['mdColor']?>.png">

<?if (!$display['icons']):?>
<style>.tab [type=radio]+label img.icon {display:none;}</style>
<?endif;?>

<script type="text/javascript" src="/plugins/webGui/scripts/dynamix.js"></script>
<?if ($display['banner'] && $display['snow']):?>
<script type="text/javascript" src="/plugins/webGui/scripts/snowstorm.js"></script>
<script>
snowStorm.targetElement = 'header';
snowStorm.flakeBottom = 92;
snowStorm.vMaxX = 2;
snowStorm.vMaxY = 1;
snowStorm.show();
<?else:?>
<script>
<?endif;?>

Shadowbox.init({skipSetup:true});

// server uptime & update period
var uptime = <?=strtok(exec("cat /proc/uptime"),' ')?>;
var period = 1; //seconds

function plus(value, label, last) {
  return value>0 ? (value+' '+label+(value!=1?'s':'')+(last?'':', ')) : '';
}
function updateTime() {
  days = parseInt(uptime/86400);
  hour = parseInt(uptime/3600%24);
  mins = parseInt(uptime/60%60);
  $('#uptime').html(((days|hour|mins)?plus(days,'day',(hour|mins)==0)+plus(hour,'hour',mins==0)+plus(mins,'minute',true):'less than a minute'));
  uptime += period;
  setTimeout(updateTime,period*1000);
}
function disableInput() {
  for (var i=0,input; input=top.document.getElementsByTagName('input')[i]; i++) { input.disabled = true; }
  for (var i=0,button; button=top.document.getElementsByTagName('button')[i]; i++) { button.disabled = true; }
  for (var i=0,select; select=top.document.getElementsByTagName('select')[i]; i++) { select.disabled = true; }
  for (var i=0,link; link=top.document.getElementsByTagName('a')[i]; i++) { link.style.color = "gray"; } //fake disable
}
function enableInput() {
  for (var i=0,input; input=top.document.getElementsByTagName('input')[i]; i++) { input.disabled = false; }
  for (var i=0,button; button=top.document.getElementsByTagName('button')[i]; i++) { button.disabled = false; }
  for (var i=0,select; select=top.document.getElementsByTagName('select')[i]; i++) { select.disabled = false; }
  for (var i=0,link; link=top.document.getElementsByTagName('a')[i]; i++) { link.style.color = "#3B5998"; }
  for (var i=0,link; link=top.document.getElementById("menu").getElementsByTagName('a')[i]; i++) { link.style.color = "#FFFFFF"; }
  for (var i=0,link; link=top.document.getElementById("header").getElementsByTagName('a')[i]; i++) { link.style.color = "#6FA239"; }
}
function refresh() {
  disableInput();
  location = location;
}
function settab(tab) {
  if ($.cookie('one')==null) {$.cookie('tab',tab,{path:'/'})} else {if ($.cookie('tab')==null) $.removeCookie('one',{path:'/'});}
}
function done() {
  var path = location.pathname;
  var x = path.indexOf("/",1);
  if (x!=-1) path = path.substring(0,x);
  $.removeCookie('one',{path:'/'});
  location.replace(path);
}
function chkDelete(form, button) {
  button.value = form.confirmDelete.checked ? 'Delete' : 'Apply';
}
function openWindow(url, name) {
  var width=((screen.width*2)/3)|0;
  var height=((screen.height*2)/3)|0;
  var features="resizeable=yes,scrollbars=yes,width="+width+",height="+height;
  var myWindow=window.open(url, name, features);
  myWindow.focus();
  return myWindow;
}
function notifier() {
  $.ajax({url:'/plugins/webGui/include/Notify.php',data:{cmd:'get'},success:function(data) {
  if (data) {
    var json = $.parseJSON(data);
    $.each(json, function(i, object) {
     var notification = $.parseJSON(object);
     $.jGrowl(notification.subject+'<br>'+notification.description, {
      sticky: true,
      position: "<?=$dynamix['notify']['position']?>",
      header: notification.plugin+': '+notification.timestamp,
      theme: notification.importance+' '+notification.file,
      beforeOpen: function(e,m,o) {if ($('.jGrowl-notification').hasClass(notification.file)) {return(false);}},
      close: function(e,m,o) {$.post("/plugins/webGui/include/Notify.php", {cmd:"archive", file:notification.file});}
     });
    });
  }}, error: function(){}
  });
<?if ($display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0)):?>
  setTimeout(notifier,5000);
<?endif;?>
}
<?if (file_exists("/var/log/plugins/dynamix.system.temp")):?>
function systemTemp() {
  $.ajax({url:'/plugins/dynamix/include/SystemTemp.php',data:{unit:'<?=$display['unit']?>'},success:function(data) {
    if (data) $('#nav-temp').html(data);
<?if ($display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0)):?>
    setTimeout(systemTemp,10000);
<?endif;?>
  }});
}
setTimeout(systemTemp,50);
<?endif;?>

function arrayStatusbar() {
  $.ajax({url:'/plugins/webGui/include/ArrayStatusbar.php',data:{dot:'<?=substr($display['number'],0,1)?>'},success:function(data) {
    if (data) $('#statusbar').html(data);
<?if ($display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0)):?>
    setTimeout(arrayStatusbar,<?=abs($display['refresh'])?>);
<?endif;?>
  }});
}
setTimeout(arrayStatusbar,50);

$(function() {
  var tab = $.cookie('one')||$.cookie('tab')||'tab1';
  if ($('#'+tab).length==0) tab = 'tab1';
  $('#'+tab).attr('checked', true);
  updateTime();
  notifier();
  $.jGrowl.defaults.closer = false;
  Shadowbox.setup('a.sb-enable', {onClose:function() {enableInput();}});
<?if ($confirm['warn']):?>
  $('form').each(function() {$(this).change(function() {$.jGrowl('You have uncommitted form changes',{sticky:false,theme:'bottom',position:'bottom',life:5000});});});
<?endif;?>
});

var mobiles=['ipad','iphone','ipod','android'];
var device=navigator.platform.toLowerCase();
for (var i=0,mobile; mobile=mobiles[i]; i++) {
  if (device.indexOf(mobile)>=0) {$('#footer').css('position','static'); break;}
}
</script>
</head>
<body>
 <div id="template">
  <div id="header" class="<?=$display['banner']?>">
   <div class="logo">
   <a href="http://lime-technology.com"><img src="/plugins/webGui/images/logo.png" title="unRAID" border="0"/><br/>
   <strong>unRAID Server <?=$var['regTy']?></strong></a>
   </div>
   <div class="block"><span class="text-left">
   Server<br/>
   Description<br/>
   Version<br/>
   Uptime
   </span>
   <span class="text-right">
   <?=$var['NAME'].($var['IPADDR'] ? " &bullet; {$var['IPADDR']}" : "")?><br/>
   <?=$var['COMMENT']?><br/>
   <?=$var['version']?><br/>
   <div id="uptime"></div>
   </span>
   </div>
  </div>
  <div id="menu">
   <div id="nav-block">
    <div id="nav-left">
<?  $pages = find_pages("Tasks");
    foreach ($pages as $page):
     $link = "/{$page['Name']}";
?>   <div id="nav-item"<?=$page['Name']==$task?' class="active"':''?>><a href="<?=$link?>" onclick="$.removeCookie('one',{path:'/'});$.removeCookie('tab',{path:'/'})"><?=$page['Name']?></a></div>
<?  endforeach;
?>  </div>
    <div id="nav-right">
     <div id="nav-temp"></div>
     <div id="nav-item"><a href="/update.htm?cmd=tail%20-n%2040%20-f%20/var/log/syslog&forkCmd=Start" rel="shadowbox;height=600;width=800" title="System Log" class="sb-enable"><img src="/plugins/webGui/icons/log.png" class="system">Log</a></div>
     <div id="nav-item"><a href="/plugins/webGui/SystemInformation.php" rel="shadowbox;height=460;width=430" title="System Information" class="sb-enable"><img src="/plugins/webGui/icons/info.png" class="system">Info</a></div>
    </div>
   </div>
  </div>
  <div class="tabs">
<?if ($myPage['Type']=="xmenu"):
    $pages = find_pages($myPage['Name']);
  else:
    $pages = array();
    $pages[$myPage['Name']] = $myPage;
  endif;
  $tab = 1;
  foreach ($pages as $page):
   eval("\$title=\"{$page['Title']}\";");
?> <div class="tab"><input type="radio" id="tab<?=$tab?>" name="tabs" onclick="settab(this.id)"><label for="tab<?=$tab++?>"><?=tab_title($title)?></label>
   <div class="content">
<? if ($page['Type']=="menu"):
    $pgs = find_pages($page['Name']);
    foreach ($pgs as $pg):
     $link = "$path/{$pg['Name']}";
?>   <div class="Panel">
     <a href="<?=$link?>" onclick="$.cookie('one','tab1',{path:'/'})"><img class="PanelImg" src="<?=$pg['Icon']?>" title="<?=$pg['Title']?>"><br>
     <div class="PanelText"><?=$pg['Title']?></div></a>
     </div>
<?  endforeach;
   elseif ($page['Type']=="php"):
    include "{$page['Root']}/{$page['Name']}.php";
   else:
    passthru($page['Type']);
   endif;
?> </div></div>
<?endforeach;
?></div>
 </div>
 <iframe id="progressFrame" name="progressFrame" frameborder="0"></iframe>
 <div id="footer">
  <span id="statusbar"></span>&bullet;&nbsp;<small><?=ucfirst(exec('cat log/plugins/dynamix.webGui'))?></small>
  <span id="copyright"><small>Author: <?=$myPage['Author']?>. Version: <?=$myPage['Version']?>.&nbsp;&nbsp;unRAID&#8482; webGui &copy; 2010-2013 Lime Technology LLC.</small></span>
 </div>
</body>
</html>
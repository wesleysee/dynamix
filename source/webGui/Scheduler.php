<?PHP
/* Copyright 2013, Bergware International.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
$ini = &$dyn_ini['parity'];
$sName = 'crontab';
$mode = array('Disabled','Daily','Weekly','Monthly','Yearly','Every');
$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
$months = array('January','February','March','April','May','June','July','August','September','October','November','December');
$memory = '/tmp/memory.tmp';
if (file_exists($memory)) {
  parse_str(file_get_contents($memory), $ini);
  if (!isset($ini['hour'])) $ini['hour'] = "";
  if (!isset($ini['day'])) $ini['day'] = "";
  if (!isset($ini['dotm'])) $ini['dotm'] = "";
  if (!isset($ini['month'])) $ini['month'] = "";
  if (!isset($ini['write'])) $ini['write'] = "";
  exec("rm -f $memory");
}
?>
<script>
<?if ($ini['mode']==5):?>
$(function() {
  $("#s1").dropdownchecklist({emptyText:'Every day', width:140, explicitClose:'...close'});
  $("#s2").dropdownchecklist({emptyText:'Every month', width:140, explicitClose:'...close'});
});
// Fool scheduler by simulating a single input field
function prepareParity(form) {
  var days = '';
  for (var i=0,item; item=form.day.options[i]; i++) {
    if (item.selected) {
      if (days.length) days += ',';
      days += item.value;
      item.selected = false;
    }
  }
  item = form.day.options[0];
  item.value = days || '*';
  item.selected = true;
  var months = '';
  for (var i=0,item; item=form.month.options[i]; i++) {
    if (item.selected) {
      if (months.length) months += ',';
      months += item.value;
      item.selected = false;
    }
  }
  item = form.month.options[0];
  item.value = months || '*';
  item.selected = true;
}
<?else:?>
function prepareParity(form) {
// do nothing
}
<?endif;?>
$(function() {
  $.ajax({url:'/plugins/webGui/include/ProcessStatus.php', data:'name=<?=$sName?>',success:function(status){$(".tabs").append(status);}});
  presetParity(document.parity_settings);
});
function presetParity(form) {
  var mode = form.mode.value;
  form.day.disabled = mode!=2 && mode!=5;
  form.dotm.disabled = mode<3;
  form.hour.disabled = mode==0;
  form.month.disabled = mode<4;
  form.write.disabled = mode==0;
}
function resetParity(form) {
  form.write.selectedIndex = 0;
}
</script>
<form name="parity_settings" method="POST" action="/update.php" target="progressFrame" onsubmit="prepareParity(this)">
<input type="hidden" name="#plugin" value="dynamix.webGui">
<input type="hidden" name="#section" value="parity"/>
<input type="hidden" name="#include" value="update.parity.php"/>
<table class="settings">
  <tr>
  <td>Scheduled parity check:</td>
  <td><select name="mode" size="1" onchange="submit()">
<?for ($m=0; $m<count($mode); $m++):?>
<?=mk_option($ini['mode'], strval($m), $mode[$m])?>
<?endfor;?>
  </select></td>
  </tr>
  <tr>
  <td>Day of the week:</td>
<?if ($ini['mode']==2):?>
  <td><select name="day" size="1">
<?for ($d=0; $d<count($days); $d++):?>
<?=mk_option($ini['day'], strval($d), $days[$d])?>
<?endfor;?>
<?elseif ($ini['mode']==5):?>
  <td><select id="s1" name="day" size="1" multiple="multiple" style="display:none">
<?for ($d=0; $d<count($days); $d++):?>
<?=mk_option_check($ini['day'], strval($d), $days[$d])?>
<?endfor;?>
<?else:?>
  <td><select name="day" size="1">
<?=mk_option($ini['day'], "*", "--------")?>
<?endif;?>
  </select></td>
  </tr>
  <tr>
<?if ($ini['mode']<5):?>
  <td>Day of the month:</td>
<?else:?>
  <td>Week of the month:</td>
<?endif;?>
  <td><select name="dotm" size="1">
<?if ($ini['mode']>=3):?>
<?if ($ini['mode']==5):?>
<?=mk_option($ini['dotm'], "*", "Every week")?>
<?=mk_option($ini['dotm'], "W1", "First week")?>
<?=mk_option($ini['dotm'], "W2", "Second week")?>
<?=mk_option($ini['dotm'], "W3", "Third week")?>
<?=mk_option($ini['dotm'], "W4", "Fourth week")?>
<?=mk_option($ini['dotm'], "WL", "Last week")?>
<?else:?>
<?=mk_option($ini['dotm'], "1", "First day")?>
<?=mk_option($ini['dotm'], "28-31", "Last day")?>
<?for ($d=2; $d<=31; $d++):?>
<?=mk_option($ini['dotm'], strval($d), sprintf("%02d", $d))?>
<?endfor;?>
<?endif;?>
<?else:?>
<?=mk_option($ini['dotm'], "*", "--------")?>
<?endif;?>
  </select></td>
  </tr>
  <tr>
  <td>Time of the day:</td>
  <td><select name="hour" size="1">
<?if ($ini['mode']>0):?>
<?for ($h=0; $h<24; $h++):?>
<?=mk_option($ini['hour'], sprintf("0 %d", $h), sprintf("%02d:00", $h))?>
<?=mk_option($ini['hour'], sprintf("30 %d",$h), sprintf("%02d:30", $h))?>
<?endfor;?>
<?else:?>
<?=mk_option($ini['hour'], "*", "--------")?>
<?endif;?>
  </select></td>
  </tr>
  <tr>
  <td>Month of the year:</td>
<?if ($ini['mode']>=4):?>
<?if ($ini['mode']==5):?>
  <td><select id="s2" name="month" size="1" multiple="multiple" style="display:none">
<?for ($m=0; $m<count($months); $m++):?>
<?=mk_option_check($ini['month'], strval($m+1), $months[$m])?>
<?endfor;?>
<?else:?>
  <td><select name="month" size="1">
<?for ($m=0; $m<count($months); $m++):?>
<?=mk_option($ini['month'], strval($m+1), $months[$m])?>
<?endfor;?>
<?endif;?>
<?else:?>
  <td><select name="month" size="1">
<?=mk_option($ini['month'], "*", "--------")?>
<?endif;?>
  </select></td>
  </tr>
  <tr>
  <td>Write corrections to parity disk:</td>
  <td><select name="write" size="1">
<?=mk_option($ini['write'], "", "Yes")?>
<?=mk_option($ini['write'], "NOCORRECT", "No")?>
  </select></td>
  </tr>
  <tr>
  <td><button type="button" onclick="resetParity(this.form);">Default</button></td>
  <td><input type="submit" name="#apply" value="Apply"><button type="button" onclick="done();">Done</button></td>
  </tr>
</table>
</form>
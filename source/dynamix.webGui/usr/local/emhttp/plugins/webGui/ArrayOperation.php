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
// Helper functions
function maintenance_button() {
  echo "<tr>";
  echo "<td><input type='checkbox' name='startMode' value='Maintenance'>Maintenance mode</td>";
  echo "<td></td>";
  echo "<td><strong>Maintenance mode</strong> - if checked, Start array but do not mount disks.</td>";
  echo "</tr>";
}
function day_count($time) {
  global $var;
  $days = floor($var['currTime']/86400)-floor($time/86400);
  switch (true) {
  case ($days<0):
    return "";
  case ($days==0):
    return " (today)";
  case ($days==1):
    return " (yesterday)";
  case ($days<=31):
    return " (".my_word($days)." days ago)";
  case ($days<=61):
    return " <span class='orange-text'>($days days ago)</span>";
  case ($days>61):
    return " <span class='red-text'>($days days ago)</span>";
  }
}
function my_check($time) {
  global $disks;
  if (!$time) return "unavailable (system reboot or log rotation)";
  $days = floor($time/86400);
  $time -= $days*86400;
  $hour = floor($time/3600);
  $mins = $time/60%60;
  $secs = $time%60;
  return plus($days,'day',($hour|$mins|$secs)==0).plus($hour,'hour',($mins|$secs)==0).plus($mins,'minute',$secs==0).plus($secs,'second',true).". Average speed: ".(isset($disks['parity']['sizeSb'])?my_scale($disks['parity']['sizeSb']*1024/$time,$unit,1)." $unit/sec":"unknown");
}
function my_error($code) {
  switch ($code) {
  case -4:
    return "<em>user abort</em>";
  default:
    return "<strong>$code</strong>";
  }
}
?>
<?if ($display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0)):?>
<style>input[type=button][value=Refresh] {display:none;}</style>
<?endif;?>

<script>
function parity_status() {
  $.ajax({url:'/plugins/webGui/include/DeviceList.php',data:{path:'<?=$path?>',device:'parity',timer:timer},success:function(data) {
    if (data) {
      if (<?=$var['mdResync']?>>0) {$.each(data.split(';'),function(k,v) {if ($('#line'+k).length>0) $('#line'+k).html(v);});}
<?if ($display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0)):?>
      else {setTimeout(refresh,0);}
<?endif;?>
    }
<?if ($display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0)):?>
    var x = typeof(cache_status)=='function' ? 4 : 3;
    if (typeof(open_status)=='function') x++;
    if ($('#tab'+x).is(':checked')) timer = setTimeout(parity_status,<?=abs($display['refresh'])?>);
    if (!data && $('#cancelButton').length>0 && $('#cancelButton').val()=='Cancel'){
      $('#cancelButton').prop('type','button').val('Done').bind({click:function(){refresh();}});
      $('#cancelText').html('');
      $('#line4').html('completed');
    }
<?endif;?>
  }});
}
parity_status();
<?if ($display['refresh']>0 || ($display['refresh']<0 && $var['mdResync']==0)):?>
var x = typeof(cache_status)=='function' ? 4 : 3;
if (typeof(open_status)=='function') x++;
$('#tab'+x).bind({click:function() {clearTimeout(timer); parity_status();}});
<?if (substr($var['fsState'],-3)=='ing'):?>
function reload_page() {
  $.ajax({url:'/plugins/webGui/include/ReloadPage.php',success:function(data) {
    switch (data) {
    case 'wait':
      setTimeout(reload_page,10000);
      break;
    case 'stop':
      setTimeout(refresh,0);
      break;
    default:
      if (data) $('#fsState').html(data);
      setTimeout(reload_page,<?=abs($display['refresh'])?>);
      break;
    }
  }});
}
reload_page();
<?endif;?>
<?endif;?>
</script>
<form name="arrayOps" method="POST" action="/update.htm" target="progressFrame">
<input type="hidden" name="startState" value="<?=$var['mdState']?>">
<table class="array_status">
<?switch ($var['fsState']):
  case "Started":
?>  <tr>
    <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Started<?=(($var['startMode']=='Maintenance')?' - Maintenance Mode':'')?></strong></td>
    <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStop" value="Stop"<?=($confirm['stop']||$var['mdResync'])?' disabled':''?>></td>
    <td><strong>Stop</strong> will take the array off-line.
<?  if ($confirm['stop']):?>
    <br><input type="checkbox" name="confirmStop" value="OFF" onClick="arrayOps.cmdStop.disabled=!arrayOps.confirmStop.checked"<?=$var['mdResync']==0?'':' disabled'?>><small>Yes I want to do this</small>
<?  endif;?>
    </td>
    </tr>
<?  if ($var['fsNumUnformatted']>0):?>
      <tr>
      <td>Unformatted disk(s) present</td>
      <td><input type="submit" id="cmdFormat" name="cmdFormat" value="Format" disabled><input type="hidden" name="unformatted_mask" value="<?=$var['fsUnformattedMask']?>"></td>
      <td><strong>Format</strong> will create a file system in all <strong>Unformatted</strong> disks, discarding all data currently on those disks.<br>
      <input type="checkbox" name="confirmFormat" value="OFF" onClick="arrayOps.cmdFormat.disabled=!arrayOps.confirmFormat.checked"><small>Yes I want to do this</small>
      </td>
      </tr>
<?  endif;
    if ($var['mdResync']==0):
      if ($var['mdNumDisabled']==0):
        if ($var['mdNumInvalid']==0):
?>        <tr>
          <td>Parity is valid.</td>
          <td><input type="submit" name="cmdCheck" value="Check"></td>
          <td><strong>Check</strong> will start a Parity-Check.<br><input type="checkbox" name="optionCorrect" value="correct" checked><small>Write corrections to parity disk</small></td>
          </tr>
<?        if ($var['sbSynced']==0):?>
            <tr>
            <td></td>
            <td></td>
            <td><em>Parity has not been checked yet.<em></td>
            </tr>
<?        else:?>
            <tr>
            <td></td>
            <td></td>
<?          unset($time);
            exec("awk '/sync completion/ {gsub(\"(time=|sec)\",\"\",x);print x;print \$NF};{x=\$NF}' /var/log/syslog|tail -2", &$time);
            if (!count($time)) $time = array_fill(0,2,0);
            if ($time[1]==0):
?>            <td>Last checked on <strong><?=my_time($var['sbSynced']).day_count($var['sbSynced'])?></strong>, finding <strong><?=$var['sbSyncErrs']?></strong> errors.
              <br><small>&forall; Duration: <?=my_check($time[0])?></small>
<?          else:?>
              <td>Last check incomplete on <strong><?=my_time($var['sbSynced']).day_count($var['sbSynced'])?></strong>, finding <strong><?=$var['sbSyncErrs']?></strong> errors.
              <br><small>&forall; Error code: <?=my_error($time[1])?></small>
<?          endif;?>
            </td></tr>
<?        endif;
        else:
          if ($var['mdInvalidDisk']==0):
?>          <tr>
            <td>Parity is invalid.</td>
            <td><input type="submit" name="cmdCheck" value="Sync"></td>
            <td><strong>Sync</strong> will start Parity-Sync.</td>
            </tr>
<?        else:?>
            <tr>
            <td>Data is invalid.</td>
            <td><input type="submit" name="cmdCheck" value="Rebuild"></td>
            <td><strong>Rebuild</strong> will start Data-Rebuild.</td>
            </tr>
<?        endif;
        endif;
      endif;
    else:
      if ($var['mdNumInvalid']==0):
        $checked = $var['mdResyncCorr']==0 ? "" : "checked";
?>      <tr>
        <td>Parity-Check in progress.</td>
        <td><input type="submit" name="cmdNoCheck" id="cancelButton" value="Cancel"></td>
         <td id="cancelText"><strong>Cancel</strong> will stop the Parity-Check.<br>
        <input type="checkbox" name="optionCorrect" value="correct" <?=$checked?> disabled><small>Write corrections to parity disk</small>
        </td>
        </tr>
<?    else:
        if ($var['mdInvalidDisk']==0):
?>        <tr>
          <td>Parity-Sync in progress.</td>
          <td><input type="submit" name="cmdNoCheck" id="cancelButton" value="Cancel"></td>
          <td id="cancelText"><strong>Cancel</strong> will stop Parity-Sync.
          <br>WARNING: canceling Parity-Sync will leave the array unprotected!
          </td>
          </tr>
<?      else:?>
          <tr>
          <td>Data-Rebuild in progress.</td>
          <td><input type="submit" name="cmdNoCheck" id="cancelButton" value="Cancel"></td>
          <td id="cancelText"><strong>Cancel</strong> will stop Data-Rebuild.
          <br>WARNING: canceling Data-Rebuild will leave the array unprotected!
          </td>
          </tr>
<?      endif;
      endif;
?>    <tr>
      <td>Total size:</td>
      <td id="line0"></td>
      <td></td>
      </tr>
      <tr>
      <td>Elapsed time:</td>
      <td id="line1"></td>
      <td></td>
      </tr>
      <tr>
      <td>Current position:</td>
      <td id="line2"></td>
      <td></td>
      </tr>
      <tr>
      <td>Estimated speed:</td>
      <td id="line3"></td>
      <td></td>
      </tr>
      <tr>
      <td>Estimated finish:</td>
      <td id="line4"></td>
      <td></td>
      </tr>
<?    if ($var['mdNumInvalid']==0):?>
      <tr>
      <td>Sync errors <?if ($var['mdResyncCorr']==0):?>detected:<?else:?>corrected:<?endif;?></td>
      <td id="line5"></td>
      <td></td>
      </tr>
<?    endif;
    endif;
    break;
  case "Mounting":
?>  <tr>
    <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Starting...</strong></td>
    <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
    <td></td>
    </tr>
<?  break;
  case "Formatting":
?>  <tr>
    <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Started, formatting...</strong></td>
    <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
    <td></td>
    </tr>
<?  break;
  case "Copying":
?>  <tr>
    <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><span id="fsState"><strong>Copying, <?=$var['fsCopyPrcnt']?>% complete...</strong></span></td>
    <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdNoCopy" value="Cancel"></td>
    <td></td>
    </tr>
<?  break;
  case "Clearing":
?>  <tr>
    <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><span id="fsState"><strong>Clearing, <?=$var['fsClearPrcnt']?>% complete...</strong></span></td>
    <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdNoClear" value="Cancel"></td>
    <td></td>
    </tr>
<?  break;
  case "Stopping":
?>  <tr>
    <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopping...</strong></td>
    <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStop" value="Stop" disabled></td>
    <td></td>
    </tr>
<?  break;
  case "Stopped":
    switch ($var['mdState']):
    case "STOPPED":
      if ($var['mdNumInvalid']==0):
        if ($var['sbClean']=="yes"):
?>        <tr>
          <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Configuration valid.</td>
          <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start"></td>
          <td><strong>Start</strong> will bring the array on-line.</td>
          </tr>
<?      else:?>
          <tr>
          <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Unclean shutdown detected.</td>
          <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start"></td>
          <td><strong>Start</strong> will bring the array on-line and start a Parity-Check.</td>
          </tr>
<?      endif;
      else:
        if ($var['mdNumDisabled']!=0):
?>        <tr>
          <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Configuration valid.</td>
          <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start"></td>
          <td><strong>Start</strong> will bring the array on-line (array will be unprotected).</td>
          </tr>
<?      else:
          if ($var['mdInvalidDisk']==0):
?>          <tr>
            <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Configuration valid.</td>
            <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start"></td>
            <td><strong>Start</strong> will bring the array on-line and start Parity-Sync.</td>
            </tr>
<?        else:?>
            <tr>
            <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Configuration valid.</td>
            <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start"></td>
            <td><strong>Start</strong> will bring the array on-line and start Data-Rebuild.</td>
            </tr>
<?        endif;
        endif;
      endif;
      maintenance_button();
      break;
    case "NEW_ARRAY":
      if ($disks['parity']['status']=="DISK_NP"):
?>    <tr>
      <td><img src="plugins/webGui/style/<?=$var['mdColor'];?>.png" class="icon"><strong>Stopped</strong>. Initial configuration</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start"></td>
      <td><strong>Start</strong> will record all disk information and bring the array on-line.
      <br>The array will be immediately available, but <strong>unprotected</strong> since <em>parity</em> has not been assigned.</td>
      </tr>
<?    else:?>
      <tr>
      <td><img src="plugins/webGui/style/<?=$var['mdColor'];?>.png" class="icon"><strong>Stopped</strong>. Initial configuration</td>
      <td><input type="button" value="Refresh" onclick="refresh();"><input type="submit" name="cmdStart" value="Start"></td>
      <td><strong>Start</strong> will record all disk information, bring the array on-line, and start Parity-Sync.
      <br>The array will be immediately available, but <strong>unprotected</strong> until Parity-Sync completes.
      <br><input type="checkbox" name="md_invalidslot" value="99">Parity is already valid.</td>
      </tr>
<?    endif;
      maintenance_button();
      break;
    case "UNPROTECTED_EXPANSION":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Found <?=$var['mdNumNew']?> new disk(s).</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start"></td>
      <td><strong>Start</strong> will record the new disk information and bring the expanded array on-line.</td>
      </tr>
<?    maintenance_button();
      break;
    case "PROTECTED_EXPANSION":
      if ($var['mdNumErased']==$var['mdNumNew']):
?>      <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Found <?=$var['mdNumNew']?> new erased disk(s).</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will record the new disk information and bring the expanded array on-line.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?      maintenance_button();
      else:
?>      <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Found <?=$var['mdNumNew']?> new disk(s).</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdClear" value="Clear" disabled></td>
        <td><strong>Clear</strong> will completely clear (set to zero) the new disk(s).
        <br>Once clear completes, the array may be Started, expanding the array to include the new disk(s).
        <br><strong>Caution: any data on the new disk(s) will be erased!</strong>
        <br>If you want to preserve the data on the new disk(s), reset the array configuration and rebuild parity instead.
        <br><input type="checkbox" name="confirmClear" value="OFF" onClick="arrayOps.cmdClear.disabled=!arrayOps.confirmClear.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    endif;
      break;
    case "DISABLE_DISK":
      if ($var['mdMissingDisk']==0):
?>      <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Missing <em>parity</em>.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will disable the <em>parity</em> disk and then bring the array on-line.
        <br>The array will be unprotected; install a new <em>parity</em> disk as soon as possible.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    else:?>
        <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Missing disk.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will disable the missing disk and then bring the array on-line.
        <br>The disks data will be available, but the array will be unprotected; install a new disk as soon as possible.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    endif;
      maintenance_button();
      break;
    case "RECON_DISK":
      if ($var['mdDisabledDisk']==0):
?>      <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. New <em>parity</em> disk installed.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will bring the array on-line and start Parity-Sync.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    else:?>
        <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Disabled disk replaced.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will bring the array on-line, start Data-Rebuild, then expand the file system (if possible).
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    endif;
      maintenance_button();
      break;
    case "UPGRADE_DISK":
      if ($var['mdMissingDisk']==0):
?>      <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Upgrading <em>parity</em>.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will bring the array on-line and start Parity-Sync.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    else:?>
        <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Upgrading disk.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will bring the array on-line, start Data-Rebuild, and then expand the file system.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    endif;
      maintenance_button();
      break;
    case "SWAP_DSBL":
      if ($var['fsCopyPrcnt']=="100"):
?>      <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Ugrading disk/swapping parity.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
        <td><strong>Start</strong> will expand the file system of the data disk (if possible); then bring the array on-line and start Data-Rebuild.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?      maintenance_button();
      else:
?>      <tr>
        <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Ugrading disk/swapping parity.</td>
        <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdCopy" value="Copy" disabled></td>
        <td><strong>Copy</strong> will copy the parity information to the new <em>parity</em> disk.
        <br>Once copy completes, the array may be Started, to initiate Data-Rebuild of the disabled disk.
        <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdCopy.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
        </tr>
<?    endif;
      break;
    case "RECORD_DISKS":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Two or more disks are wrong.</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
      <td><strong>Start</strong> will just record the new disk positions and bring the array on-line.
      <br>We recommend you start a Parity-Check afterwards just to be safe.
      <br><input type="checkbox" name="confirmStart" value="OFF" onClick="arrayOps.cmdStart.disabled=!arrayOps.confirmStart.checked"><small>Yes I want to do this</small></td>
      </tr>
<?    maintenance_button();
      break;
    case "ERROR:INVALID_EXPANSION":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Invalid expansion.</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
      <td>You may not add new disks when there exists missing, wrong, or disabled disk(s).</td>
      </tr>
<?    break;
    case "ERROR:NEW_DISK_TOO_SMALL":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Replacement disk is too small.</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
      <td>The replacement disk must be as big or bigger than the original.</td>
      </tr>
<?    break;
    case "ERROR:PARITY_NOT_BIGGEST":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Disk in parity slot is not biggest.</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
      <td>If this is a new array, move the largest disk into the <em>parity</em> slot.
      <br>If you are adding a new disk or replacing a disabled disk, try Parity-Swap.</td>
      </tr>
<?    break;
    case "ERROR:TOO_MANY_MISSING_DISKS":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. Invalid configuration.</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
      <td>Too many wrong and/or missing disks!</td>
      </tr>
<?    break;
    case "ERROR:NO_DATA_DISKS":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. No data disks.</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
      <td>No array data disks have been assigned!</td>
      </tr>
<?    break;
    case "ERROR:NO_RAID_DISKS":
?>    <tr>
      <td><img src="/plugins/webGui/images/<?=$var['mdColor']?>.png" class="icon"><strong>Stopped</strong>. No devices.</td>
      <td><input type="button" value="Refresh" onclick="refresh()"><input type="submit" name="cmdStart" value="Start" disabled></td>
      <td>No array disk devices have been assigned!</td>
      </tr>
<?    break;
    endswitch;
  endswitch;
?><tr><td></td><td></td><td></td></tr>
</table>
</form>
<form name="otherOps" method="POST" action="/update.htm" target="progressFrame">
<input type="hidden" name="startState" value="<?=$var['mdState']?>">
<table class="array_status">
<?if ($var['fsState']=="Started"):?>
  <tr>
  <td></td>
  <td><input type="submit" name="cmdSpinDownAll" value="Spin Down"<?=$var['mdResync']==0 ? '':' disabled'?> style="width:80px"><input type="submit" name="cmdSpinUpAll" value="Spin Up"<?=$var['mdResync']==0 ? '':' disabled'?> style="width:80px"></td>
  <td><strong>Spin Down</strong> will immediately spin down all disks.<br><strong>Spin Up</strong> will immediately spin up all disks.</td>
  </tr>
<?endif;?>
<?if ($var['fsState']=="Stopped"):?>
  <tr>
  <td></td>
  <td><input type="submit" name="cmdIdentify" value="Identify"></td>
  <td><strong>Identify</strong> will briefly read from each disk in order.</td>
  </tr>
  <tr>
  <td></td>
  <?if ($confirm['down']):?>
   <td><input type="submit" name="reboot" value="Reboot" disabled></td>
   <td><strong>Reboot</strong> will activate a system reset.
   <br><input type="checkbox" name="confirmReboot" value="OFF" onClick="otherOps.reboot.disabled=!otherOps.confirmReboot.checked"><small>Yes I want to do this</small>
   </tr>
   <tr>
   <td></td>
   <td><input type="submit" name="shutdown" value="Power down" disabled></td>
   <td><strong>Power down</strong> will activate a <em>clean</em> power down.
   <br><input type="checkbox" name="confirmShutdown" value="OFF" onClick="otherOps.shutdown.disabled=!otherOps.confirmShutdown.checked"><small>Yes I want to do this</small>
   </tr>
  <?else:?>
   <td><input type="submit" name="reboot" value="Reboot"></td>
   <td><strong>Reboot</strong> will activate a system reset.
   </tr>
   <tr>
   <td></td>
   <td><input type="submit" name="shutdown" value="Power down"></td>
   <td><strong>Power down</strong> will activate a <em>clean</em> power down.
   </tr>
  <?endif;?>
<?else:?>
  <tr>
  <td></td>
  <td><input type="submit" name="clearStatistics" value="Clear Statistics"></td>
  <td><strong>Clear Statistics</strong> will immediately clear all disk statistics.</td>
  </tr>
<?endif;?>
</table>
</form>
<?
if (file_exists("/var/log/plugins/dynamix.s3.sleep")) include 'plugins/dynamix/Sleep.php';
$confirm['warn'] = false
?>
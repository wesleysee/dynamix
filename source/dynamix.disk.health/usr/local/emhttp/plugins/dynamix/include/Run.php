<?PHP
/* Copyright 2013, Bergware International
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<?
parse_str($argv[1], $_GET);
$port = isset($_GET['port']) ? $_GET['port'] : '';
$spin = exec("hdparm -C /dev/$port | grep 'active'");

switch ($_GET['cmd']):
case "short":
  exec("smartctl -t short /dev/$port");
  break;
case "long":
  exec("smartctl -t long /dev/$port");
  break;
case "stop":
  exec("smartctl -X /dev/$port");
  break;
case "update":
  $smart = "SMART self-test progress:";
  $result = "Last SMART test result:";
  if (!$spin):
    echo "<td>$result</td><td>Unavailable</td>";
    break;
  endif;
  if (exec("smartctl -l selftest /dev/$port | grep 'in progress'")):
    $percent = 100 - exec("smartctl -l selftest /dev/$port | grep 'in progress' | sed 's:^# :#0:' | awk '{print $8}' | sed 's:%::'");
    echo "<td>$smart</td><td><img src='/plugins/webGui/images/loading.gif'>&nbsp;&nbsp;$percent % complete</td>";
  elseif (exec("smartctl -c /dev/$port | grep 'execution status' | grep 'in progress'")):
    $percent = 100 - exec("smartctl -c /dev/$port | grep -A 1 'execution status' | grep -v 'execution status' | cut -d% -f1");
    echo "<td>$smart</td><td><img src='/plugins/webGui/images/loading.gif'>&nbsp;&nbsp;$percent % complete</td>";
  elseif (exec("smartctl -l selftest /dev/$port | grep '# 1' | grep 'Completed without error'")):
    echo "<td>$result</td><td><span class='passed'>Completed without errors</span></td>";
  elseif ((!exec("smartctl -l selftest /dev/$port | grep '# 1'")) || (exec("smartctl -l selftest /dev/$port | grep '# 1' | grep 'No self-tests'"))):
    echo "<td>$result</td><td>No self-tests logged on this disk</td>";
  elseif (exec("smartctl -l selftest /dev/$port | grep '# 1' | grep 'Aborted'")):
    echo "<td>$result</td><td><span class='warning'>Test aborted</span></td>";
  elseif (exec("smartctl -l selftest /dev/$port | grep '# 1' | grep 'Interrupted'")):
    echo "<td>$result</td><td><span class='warning'>Test interrupted</span></td>";
  elseif (!exec("smartctl -l selftest /dev/$port | grep '# 1' | tail -c -2 | grep '-'")):
    echo "<td>$result</td><td><span class='failed'>Errors occurred - Check logs</span></td>";
  else:
    echo "<td>$result</td><td>Unknown</td>";
  endif;
  break;
case "health":
  $poll = $_GET['poll'] ? '' : '-n standby';
  echo exec("smartctl $poll -q silent -H /dev/$port")==0 ? ($spin ? 'on' : 'off') : ($spin ? 'fail' : 'fail off');
  break;
case "status":
  if (exec("smartctl -q silent -H /dev/$port")==0):
    echo "<span class='passed'>Passed</span>";
  else:
    echo "<span class='failed'>Failed</span><br>Get data off disk and run reports below to obtain more information";
  endif;
  break;
case "identity":
  echo "<pre>".shell_exec("smartctl -i /dev/$port | awk 'NR>4'")."</pre>";
  break;
case "attributes":
  echo "<pre>".shell_exec("smartctl -A /dev/$port | awk 'NR>6'")."pre>";
  break;
case "capabilities":
  echo "<pre>".shell_exec("smartctl -c /dev/$port | awk 'NR>4'")."</pre>";
  break;
case "selftest":
  echo "<pre>".shell_exec("smartctl -l selftest /dev/$port | awk 'NR>5'")."</pre>";
  break;
case "errorlog":
  echo "<pre>".shell_exec("smartctl -l error /dev/$port | awk 'NR>5'")."</pre>";
  break;
endswitch;
?>
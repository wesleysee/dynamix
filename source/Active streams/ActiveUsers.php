<?PHP
/* Copyright 2013, Bergware International.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.
 */
?>
<script>
$(function() {
  $('#names').load('/plugins/dynamix/include/NameList.php', "plugin=<?=$plugin?>&warn=<?=$confirm['warn']?>");
  $('#tab2').bind({click:function(){$('#names').load('/plugins/dynamix/include/NameList.php', "plugin=<?=$plugin?>&warn=<?=$confirm['warn']?>");}});
});
</script>

<div id="names"></div>
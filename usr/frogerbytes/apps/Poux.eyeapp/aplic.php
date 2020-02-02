<?php
if (!defined ('USR')) return;
?>
<center>
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" width="480" height="298" id="poux" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="<? echo $appinfo['appdir']; ?>poux.swf" />
<param name="menu" value="false" />
<param name="quality" value="high" />
<param name="bgcolor" value="#000000" />
<embed src="<? echo $appinfo['appdir']; ?>poux.swf" menu="false" quality="high" bgcolor="#000000" width="480" height="298" name="poux" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
</object>
</center>
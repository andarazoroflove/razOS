<?
if(!defined('USR'))
  return ;
?>
<div style="position:absolute;width:10px;height:10px;border:1px solid #cacaca;overflow:hidden;">
<applet
  archive = "painter.jar"
  code     = "painter.img.class"
  codebase = "<?=$appinfo['appdir']?>"
  width    = "600"
  height   = "500"
  hspace   = "0"
  vspace   = "0"
  align    = "middle"
  id="Painter" 
  name="Painter" 
>
</applet>
</div>
<!--
optional for applet:
<param name="image" value="path_to_local_file">
//-->

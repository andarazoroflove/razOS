<?php

 if ( !defined('USR') ) return;

 //List clickable path
 addActionBar("<strong><span style='border-right:1px solid #999; padding-right:5px; padding-left:5px;'><a href='?path='>HOME</a></span>","center");
 $link = '';
 foreach (explode ('/' , substr($path, strlen(realpath(HOMEDIR.USR.'/')))) as $i)
   if (!empty($i)) addActionBar("<span style='border-right:1px solid #999; padding-right:5px; padding-left:5px;'><a href='?path=".($link .= "$i/")."'>$i</a></span>","center");
 addActionBar("</strong>","center");

if($open=opendir($dir)) {
 echo "
 <script LANGUAGE=\"JavaScript\">
   function estassegurpaperera() {
     var agree = confirm(\""._L('File will be permanently deleted. Continue?')."\");
   return agree; }
 </script>
 <table width='98%' border='0' cellpadding='3'>";

 $compte = 0;
 while ($f = readdir($open) ) {
  if ($f == ".." ||  $f == ".") continue;
  $compte++;
  $mod = date("d-m-Y H:i", filemtime($dir . $f) + TOFFSET);
  $size = round(filesize($dir . $f) / 1024);
  $ext = strtolower(substr(strrchr($f, "."), 1));
  $icon = "file.png";
  $image_ext = Array("png","jpg","jpeg","bmp","gif","tiff","svg");
  $edit_ext = Array("html","htm","txt");
  if (in_array($ext, $image_ext)) $icon = "image.png";
  if (in_array($ext, $edit_ext)) $icon = "text.png";

  if(is_dir($dir.$f)) {

  echo "<tr>
  <td colspan='4'><strong><a href='?a=$eyeapp&path=$udir$f/'><img border='0' src='${appinfo['appdir']}gfx/folder.png'> &nbsp; ".urldecode($f)."</a></strong></td>
  <td align='right' width='30'><a onclick='return estassegurpaperera()' href='?a=$eyeapp&type=removedir&dirname=$f&path=$udir'>
        <img style='margin-top: 4px;' border='0' src='".findGraphic ('', "btn/delete.png")."'>
      </a>
  </td>
  </tr>";
  continue;
  }

  if ( substr($ext, 0, 3) == "php" ) die("ERROR");
  echo "<tr>
  <td width='30'><img border='0' src='${appinfo['appdir']}gfx/$icon' /></td>
  <td align='left'>";
if (in_array($ext, $image_ext) || in_array($ext, $edit_ext))
  echo "<a href='?a=eyeViewer.app($udir$f)'>".urldecode($f)."</a>";
else
  echo "<a href='".SYSDIR."baixar.php?fabaixar=$udir$f'>".urldecode($f)."</a>";

 echo "</td>
  <td align='right' width='70'>$size kb.</td>
  <td align='right' width='170'>$mod</td>
  <td align='right' width='25'>
      <a onclick='return estassegurpaperera()' href='?a=$eyeapp&type=remove&file=$f&path=$udir'>
        <img style='margin-top: 4px;' border='0' src='".findGraphic ('', "btn/delete.png")."'>
      </a>
  </td>
 </tr>";
        
 } //end while

echo "</table>";
closedir($open);
}
if ($compte == 0)
echo "<div align='right'><h4>" . _L('This directory is empty') . "</h4></div>";


?>

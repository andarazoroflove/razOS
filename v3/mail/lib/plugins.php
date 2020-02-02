<?php
/* ------------------------------------------------------------------------- */
/* lib/plugins.php -> PHlyMail LE 1.2.0+ Plugin handler                      */
/* (c) 2001-2002 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail LE Common Path                                                   */
/* v0.0.5                                                                    */
/* ------------------------------------------------------------------------- */
$xbase=$WP_core['page_path']."/plugged";
// Nur Filter laden, wenn das control file vorhanden ist
if(file_exists("$xbase/plug.control.wpop"))
{
 $file=file("$xbase/plug.control.wpop"); reset($file);
 // Iteratives include() der Dateien
 while(list($i,$l)=each($file))
 {
  // Skip comments
  if(substr($l,0,1)=="#") continue;
  $l=explode(";;",$l);
  if(("*"==$l[0] || $action==$l[0]) && (is_readable("$xbase/$l[2]/$l[3]")))
  {
   $WP_core['plugin_path']="$xbase/$l[2]"; include("$xbase/$l[2]/$l[3]");
  }
 }
 unset($file);
}
?>
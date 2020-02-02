<?php
/* ------------------------------------------------------------------------- */
/* lib/mime.handler.php - PHlyMail LE 2.0.1+                                 */
/* Mapping MIME types, Readable Type, Encoding <> DOSlike type extensions    */
/* (c) 2002-2003 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* v0.1.7mod1                                                                */
/* ------------------------------------------------------------------------- */

$WP_libload['mimehandler']=TRUE;

_MIME_LoadTable();

function MIME_GetFromName($filename='',$safemode=1)
{
 preg_match('/\.([^\.]+)$/i',$filename,$found); $suff=$found[1];
 foreach($GLOBALS['WP_MIME'] as $buffer)
 {
  list($x,$m,$e,$n)=explode(';;',$buffer);
  if(strtolower($x)==strtolower($suff)) { $return=array($m,$n); break; }
 }
 if(!isset($return) && 1==$safemode)
 {
  $return=array('application/octet-stream',$GLOBALS['WP_msg']['nofiletype']);
 }
 return $return;
}

function MIME_GetFromType($mimetype='',$safemode=1)
{
 $found=0;
 foreach($GLOBALS['WP_MIME'] as $buffer)
 {
  list($x,$m,$e,$n)=explode(';;',$buffer);
  if(strtolower($m)==strtolower($mimetype)) {$return=$n; break;}
 }
 if(!isset($return) && 1==$safemode) $return=$GLOBALS['WP_msg']['nofiletype'];
 return $return;
}

function MIME_GetEncodingFromType($mimetype='',$safemode=1)
{
 $found=0;
 foreach($GLOBALS['WP_MIME'] as $buffer)
 {
  list($x,$m,$e,$n)=explode(';;',$buffer);
  if(strtolower($m)==strtolower($mimetype)) { $return=$e; break; }
 }
 if(!isset($return) && 1==$safemode) $return='b';
 return $return;
}

function _MIME_LoadTable()
{
 if(isset($GLOBALS['WP_MIME'])) return;
 if(file_exists($GLOBALS['WP_core']['conf_files'].'/mime.map.wpop'))
 {
  $fid=fopen($GLOBALS['WP_core']['conf_files'].'/mime.map.wpop','r');
  while(!feof($fid))
  {
   $buffer=trim(fgets($fid,4096)); if($buffer{0}=='#') continue;
   $GLOBALS['WP_MIME'][]=$buffer;
  }
 }
}

?>
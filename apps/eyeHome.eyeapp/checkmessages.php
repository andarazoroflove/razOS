<?php

if ( !defined('USR') ) return;

function checkmessages()
{
  $mdir = USRDIR.USR."/Inbox/";
  if (!is_dir($mdir)) return FALSE;

  if ( file_exists(USRDIR.USR."/eyeMessages.eyeapp.xml")) {
    $opxml = parse_info(USRDIR.USR."/eyeMessages.eyeapp.xml");
    $lrxml = $opxml["state.lastread"];
  } else $lrxml = "0";

  $r = opendir ($mdir);
    while ($m = readdir ($r)) {
      if (is_file ($mdir . $m) && (filemtime ($mdir . $m) > $lrxml))
        return TRUE;
    }
 closedir ($r);
 return FALSE;
}

?>

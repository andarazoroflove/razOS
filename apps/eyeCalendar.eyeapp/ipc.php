<?php
  if (! empty ($SYSipcmsg) && defined ('USR')) {
    
//  error_log ("L $eyeapp : $SYSipcmsg");

    $dir = USRDIR.USR."/eyeCalendar/";
    $dmy = substr ($SYSipcmsg, 1+ strpos ($SYSipcmsg, '='), 8);
    @$ymd = substr ($dmy, 4, 8) . '-' . substr ($dmy, 2, 2) . '-' . substr ($dmy, 0, 2);
    
    switch (strtolower (substr ($SYSipcmsg, 0, strpos ($SYSipcmsg, '=')))) {
    case 'cald':  
      $my_eyecal = substr ($dmy, 2, 6) . '.eyeCal';
      for ($md = 32, $mdates = 0; --$md;)
        if (is_file ($dir.substr ("0$md$my_eyecal", -15)))
          $mdates |= 1 << $md;
      echo "$dmy;$mdates;".date ('W', strtotime (substr($ymd, 0, 7).'-01'));    
      return;  

    case 'note':  
      echo ($dateinfo = parse_info ("$dir$dmy.eyeCal", true)) ? 
        $dateinfo ['content'] : _L ('Cannot find note %0', $dmy);
      return;  

    case 'create':     
      if (is_dir ($dir) || mkdir ($dir, 0777)) {
        createXML ("$dir$dmy.eyeCal", $eyeapp, array (
	      'author' => USR,
	      'date' => time()-2,
	      'content' => substr (userinput($SYSipcmsg), 15)
	      ), true);
		    echo "OK$dmy";
      }
      else
        echo _L ('Error : Failed to save note');
      return;  

    case 'delete':    
      if (unlink ("$dir$dmy.eyeCal"))
		    echo "OK$dmy";
      else
        echo _L('Cannot find note %0', $dmy);
      return;
    }    
    error_log ("E $SYSipcapp bad message : " . $SYSipcmsg);
    return;
  }
?>

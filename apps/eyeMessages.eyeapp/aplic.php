<?php
/* Calendar items should also show up as CAL items

also the display of the different message types :
        <types> msg,npublic,nprivate,cal,mboard</types>
*/

if (defined ('USR') && !function_exists ('eyeMessages')) {

function eyeMessages ($eyeapp, &$appinfo) {
	
  if ($autorun = (@$appinfo['argv'][0] == 'check')) 
    array_shift ($appinfo['argv']);
    
  $msgsrc = explode (',', !empty ($appinfo['argv']) ? $appinfo['argv'][0]: MSGSRCS);
  array_shift ($appinfo['argv']);

  if (!isset($appinfo['state.lastread']))
    $appinfo['state.lastread'] = 0;

  if ($autorun) {   
    $autorun = false;	   
    foreach ($msgsrc as $msrc) {
      if (is_dir ($msrc = filename($msrc)) && ($md = opendir ($msrc))) {
        while ($mf = readdir ($md))
          if ((substr($mf, 0,1) != '.') && $autorun = (is_file ("$msrc$mf") && (filemtime ("$msrc$mf") > $appinfo['state.lastread'])))
	          break;
	      closedir ($md);
      } else
	    $autorun = (is_file ($msrc) && (filemtime ($msrc) > $appinfo['state.lastread']));
	      
	    if ($autorun)  
        break;
    }
	 
    if (!$autorun)
      return 'exit';
  }
  
   addActionBar (_L('It is %0 %1', date(empty ($appinfo['param.date']) ? SYSFMT_DATE : $appinfo['param.date'])), 'center');
   addActionBar ("<div class='Gclock' GcFormat='".(empty ($appinfo['param.time']) ? '%H:%i' : $appinfo['param.time'])."' style='display: inline;'></div>", 'right');

  //Sending message
   if (isset ($_REQUEST['missatgeaenviar']) && isset ($_REQUEST['type'])) {
      switch ($_REQUEST['type']) {
      case 'msg':  // desti is a user 
         if (file_exists (($diraqui = USRDIR.$_REQUEST['desti'].'/').USRINFO)) {
	    if (!is_dir ($diraqui .= MSGDIR))
               mkdir($diraqui, 0777);
            $msgfile = $diraqui.USR.time ();
            $_REQUEST['missatgeaenviar'] .= @$appinfo['param.msg_sig'];
	 }
	 break;

      case 'npublic':
         if (is_dir ($_REQUEST['desti']) && in_array ($_REQUEST['desti'], $msgsrc)) {
            $msgfile = $_REQUEST['desti'].USR.time();
            $_REQUEST['missatgeaenviar'] .= @$appinfo['param.note_sig'];
	 }
	 break;

      case 'nprivate':
	 if (!is_dir (USRDIR.NOTEDIR))
            mkdir(USRDIR.NOTEDIR, 0777);
	 if (is_dir (USRDIR.NOTEDIR)) 
            $msgfile = USRDIR.NOTEDIR.time ();
         break;

      case 'mboard':
         if (file_exists ($_REQUEST['desti']) && in_array ($_REQUEST['desti'], $msgsrc)) {
            $msgfile = $_REQUEST['desti'];
            $_REQUEST['missatgeaenviar'] .= @$appinfo['param.mboard_sig'];
	 }
	 else
	 break;
      }
      
      if (!empty ($msgfile)) {
         createXML ($msgfile, 'eyeMessage', array (
	    'from' => USR,
	    'date' => time()-1,
	    'type' => userinput($_REQUEST['type']),
            'title' => !empty ($_REQUEST['title']) ? userinput($_REQUEST['title']) : _L('From %0', USR),
	    'keywords' => '',
	    'fmt' => !empty ($_REQUEST['fmt']) ? $_REQUEST['fmt'] : 'text',
	    'text' => macro_substitute(userinput($_REQUEST['missatgeaenviar']))), 1);
          msg(_L('Message sent'));
      }
      else
         msg (_L('Could not create message %1 for %0', @$_REQUEST['desti'], @$_REQUEST['type']));
   }
   
   if (isset ($_REQUEST['msgborrar'])) { // (Delete message) 
      if (is_file ($_REQUEST['msgborrar'])) {
        $filetoremove = basename($_REQUEST['msgborrar']);
      if (is_file(USRDIR.USR."/Inbox/".$filetoremove)) {
         unlink (USRDIR.USR."/Inbox/".$filetoremove);
         msg (_L('Message deleted'));
      }
      }
      else 
         msg (_L('Message not found'));
   }

   if (isset ($_REQUEST['msgveure'])) { // (View message)	
      if (false !== ($msginfo = parse_info ($_REQUEST['msgveure']))) {
         $msginfo['date'] = filemtime ($_REQUEST['msgveure']);	    

         if (!empty ($msginfo['motd'])) $msginfo['text'] = $msginfo['motd']; 	    
         if (!empty ($msginfo['user'])) $msginfo['from'] = $msginfo['user'];
         if (empty ($msginfo['title'])) { 
	    $msginfo['title'] = _L('Public message board');
	    $msginfo['type'] = 'mboard';
	 }
	 else
	       addActionBar ("<a href='?a=$eyeapp&msgborrar=${_REQUEST['msgveure']}'>
      <img border='0' alt='"._L('Delete message')."' title='"._L('Delete message')."' src='".findGraphic ('', "btn/delete.png")."' />
    </a>");

	 if (isset ($msginfo['author'])) $msginfo['from'] = $msginfo['author'];
	 if (isset ($msginfo['publicprivate'])) {
            $arx = $_REQUEST['msgveure'];
            if (empty ($msginfo['text']) && (substr ($arx, -4) == '.xml') && (is_file ($arx = substr($arx, 0, strlen($arx-4)))))
	       $msginfo['text'] = file_get_contents ($arx);
	 
	    $msginfo['type'] = ($msginfo['publicprivate'] == 'pr') ? 'nprivate' : 'npublic';
	    if (empty ($msginfo['fmt'])) $msginfo['fmt'] = 'html'; 
         }
	 elseif (empty ($msginfo['fmt'])) $msginfo['fmt'] = 'text';
	 
	 if (empty($msginfo['text'])) $msginfo['text'] = '';
	 
	 $editable = $msginfo['type'] != 'msg';
	    addActionbar ("       
    <a href='?a=$eyeapp'>
      <img border='0' alt='"._L('Show messages')."' title='"._L('Show messages')."' src='".findGraphic ('', "btn/back.png")."' />
    </a>");
	 echo "
  <div align='center'>
    <form action='desktop.php' method='post'>
      <h2>${msginfo['title']} - <a href='desktop.php?a=$eyeapp&enviarmsg&enviarusr=${msginfo['from']}'>Reply</a></h2>
      ".(($msginfo['type'] == 'msg') ?
        _L('From %0, %1', "<strong>${msginfo['from']}</strong>", dater ($msginfo['date'])) :
        _L('Last updated by %0, %1', "<strong>${msginfo['from']}</strong>", dater ($msginfo['date'])))."
      <textarea 
        class='llibreta' 
	name='missatgeaenviar' 
	style='width:80%; height:58%;' 
	".($editable ? '' : 'readonly')."
	".(defined ('BROWSER_IE') ? "cols='${appinfo['param.cols']}' rows='${appinfo['param.rows']}'" : '')."
	>$msginfo[text]</textarea>";
          if ($editable)
            echo "
      <input type='hidden' name='desti' value='${_REQUEST['msgveure']}' />
      <input type='hidden' name='type' value='${_REQUEST['type']}' />
      <br />
      <input type='button' name='Send' value='"._L('Send')."' />";
      
          echo "
    </form>
  </div>";
      }
   }
   elseif (isset ($_REQUEST['enviarmsg'])) { // (New Message)

   addActionbar ("       
    <a href='?a=$eyeapp'>
      <img border='0' alt='"._L('Show messages')."' title='"._L('Show messages')."' src='".findGraphic ('', "btn/back.png")."' />
    </a>");
   addActionBar ("
    <a href='javascript:document.getElementById(\"enviarmsg\").submit();'>
      <img border='0' alt='"._L('Send message')."' title='"._L('Send message')."' src='${appinfo['appdir']}gfx/send.png' />
    </a>");

      echo"<br />
  <form id='enviarmsg' action='desktop.php?a=$eyeapp' method='post'><div align='left' style='margin-left: 30px;'> "._L('To')." :";
$selected = @$_REQUEST['enviarusr'];
if (SHOW_USER_LISTS == "yes")
{
echo "<select name='desti'>";

      if ($d = opendir (USRDIR)) {
         while ($un = readdir ($d))
            if (is_file (USRDIR."$un/".USRINFO))
	       if ((USR == $un) || ($un != $selected)) 
                  echo "<option value='$un'>$un</option>";
	       elseif (empty ($selected) || ($selected == $un))
                  echo "<option value='$un' selected>".($selected = $un)."</option>";
         closedir ($d);
      }

      echo " 
    </select> ";
}
else echo " <input type='text' name='desti' size=52' value='$selected' /><br />";

echo _L('Subject')." :
    <input type='text' name='title' size=48' />
    <input type='hidden' name='type' value='msg' />
    <br /><br /></div><div align='center'>
    <textarea 
      class='llibreta' 
      name='missatgeaenviar' 
      style='width:90%; height:55%;'
      ".(defined ('BROWSER_IE') ? "cols='${appinfo['param.cols']}' rows='${appinfo['param.rows']}'" : '')."
      ></textarea>
  </form>
  </div>";
   } else { // (Show messages) 
      addActionBar ("
    <a href='?a=$eyeapp&enviarmsg'>
      <img border='0' alt='"._L('New Message')."' title='"._L('New Message')."' src='".findGraphic ('', "btn/new.png")."' />
    </a>");

      $msgs = array ();
      if (!empty ($appinfo['param.types']))
         $types = explode (',', str_replace (' ', '', $appinfo['param.types']));
      
      foreach ($msgsrc as $msrc)
         if (is_dir ($msrc =  filename ($msrc)) && ($dir = @opendir ($msrc))) {
            while ($arx = readdir ($dir))
               if (($arx <> '.') && ($arx <> '..') && (false !== ($msginfo = parse_info ("$msrc$arx")))) {
	          $msginfo['date'] = filemtime ("$msrc$arx");	    
	          $msginfo['arx'] = "$msrc$arx";
		  
         	  if (isset ($msginfo['author'])) $msginfo['from'] = $msginfo['author'];
	          if (isset ($msginfo['publicprivate'])) {
		     if (empty ($msginfo['text']) && (substr ($arx, -4) == '.xml') && (is_file ($arx = $msrc.(substr($arx, 0, strlen($arx-4))))))
		        $msginfo['text'] = file_get_contents ($arx);
	             $msginfo['type'] = ($msginfo['publicprivate'] == 'pr') ? 'nprivate' : 'npublic';	      
	          }

		  if (!empty ($types))
		     $msginfo ['tno'] = array_search ($msginfo['type'], $types);
		     
     	          // Put in msgs array only if selected : author, timestamp, keywords, type
	          $msgs[] = $msginfo;
	       }
	    closedir ($dir);   
         }
	 else if (is_file ($msrc) && (false !== ($msginfo = parse_info ($msrc)))) {
            $msginfo['date'] = filemtime ($msrc);	    
            $msginfo['arx'] = $msrc;
            if (!empty ($msginfo['motd'])) $msginfo['text'] = $msginfo['motd']; 	    
            if (!empty ($msginfo['user'])) $msginfo['from'] = $msginfo['user'];
            if (empty ($msginfo['title'])) $msginfo['title'] = _L('Public message board');
	    
	    $msginfo['type'] = 'mboard';
   	    // Put in msgs array only if selected : author, timestamp, keywords, type
            $msgs[] = $msginfo;
	 }
	 
      if (count ($msgs)) {
	 $tablecols = empty ($appinfo['param.columns']) ? 
	    array ('del','from','title','date','type') :
	    explode (',', str_replace (' ' , '', $appinfo['param.columns']));

         if (empty ($_REQUEST['msort']))
	    $_REQUEST['msort'] = !empty($appinfo['param.sort']) ? $appinfo['param.sort'] : 'date';

	 // Sort by :  timestamp, author, title, type, keywords?
         usort ($msgs, create_function ('$a,$b',
	    "\$dir = (\$_REQUEST['msort']{0} == '-') ? -1 : 1;
	     switch (\$sf = ltrim(\$_REQUEST['msort'], '+-')) {
	     case 'type':
	     case 'from':
	     case 'title':
	        return \$dir * strcmp(strtolower (\$a[\$sf]), strtolower(\$b[\$sf]));
	    }
            return \$dir*(\$b['date']-\$a['date']);"));

         echo "
  <br />	 
  <table align='center' width='90%' cellspacing='0'>
    <tr align='left' valign='bottom'>";
    
         $sortimg = "<img border='0' src='".findGraphic('b', ($_REQUEST['msort']{0}=='-') ? 'btn/sortUp.png' : 'btn/sortDn.png')."' />";
    
         foreach ($tablecols as $col) 
	    switch ($col) {
	    case 'del':
	       echo "
      <td width='1'>&nbsp;</td>";
               break;

      	    case 'type':
	       echo "
      <td width='1' align='right'>".((ltrim($_REQUEST['msort'],'+-') == $col) ? $sortimg : '&nbsp;')."</td>	       
      <td width='1'>
	<a href='?msort=".(($_REQUEST['msort'] == 'type') ? '-' : '')."type'>
          "._L('Type')."
	</a>  
      </td>";
               break;

      	    case 'title':
	       echo "
      <td width='10px' align='right'>".((ltrim($_REQUEST['msort'],'+-') == $col) ? $sortimg : '&nbsp;')."</td>	       
      <td >
  	<a href='?msort=".(($_REQUEST['msort'] == 'title') ? '-' : '')."title'>
          "._L('Title')."
	</a>  
      </td>";
               break;

      	    case 'from':
	       echo "
      <td width='10px' align='right'>".((ltrim($_REQUEST['msort'],'+-') == $col) ? $sortimg : '&nbsp;')."</td>	       
      <td >
	<a href='?msort=".(($_REQUEST['msort'] == 'from') ? '-' : '')."from'>
          "._L('From')."
	</a>
      </td>";
               break;

      	    case 'date':
	       echo "
      <td width='10px' align='right'>".((ltrim($_REQUEST['msort'],'+-') == $col) ? $sortimg : '&nbsp;')."</td>	       
      <td>
	<a href='?msort=".(($_REQUEST['msort'] == 'date') ? '-' : '')."date'>
          "._L('When sent')."
	</a>
      </td>";
               break;
	    }
         echo "
    </tr>
    <tr><td colspan='16'><hr/></td></tr>";

         $row = 1;
         foreach ($msgs as $msginfo) {
	    echo "
    <tr valign='top' class='message ".(($row++ % 2) ? 'oddrow' : 'evenrow')."'>";

            foreach ($tablecols as $col)
	       switch ($col) {
	       case 'del':
	          echo "
      <td width='1'>";
               if (($msginfo['type'] == 'msg') || (($msginfo['type'] != 'mboard') && ($msginfo['from'] == USR)) || (USR == ROOTUSR))
	          echo"
        <a href='javascript:if(confirm(\""._L('Delete message \"%0\" from %1',$msginfo['title'],$msginfo['from'])."?\"))window.location=\"desktop.php?a=$eyeapp&msgborrar=${msginfo['arx']}\";void 0;'>
	  <img border='0' src='".findGraphic ('', "btn/deletefile.png")."'>
	</a>";
                  echo "
      </td>";
                  break;

      	       case 'type':
	          echo "
      <td width='1' colspan='2' align='right'>
        ".(empty ($msginfo['type']) ? '&nbsp;' : "<span class='message ${msginfo['type']}'>&nbsp;&nbsp;&nbsp;&nbsp;</span>")."
      </td>";
                  break;

      	       case 'title':
	          echo "
      <td colspan='2'>
        <a href='desktop.php?a=$eyeapp&msgveure=${msginfo['arx']}&type=${msginfo['type']}'>
	${msginfo['title']}
        </a>
      </td>";
                  break;

      	       case 'from':
	          echo "
      <td width='1' colspan='2' align='center'><strong>&nbsp;&nbsp;${msginfo['from']}&nbsp;&nbsp;</strong></td>";
                  break;

      	       case 'date':
	          echo "
      <td width='1' colspan='2'".(($msginfo['date'] > $appinfo['state.lastread']) ? " class='message recent'" : '').">
        &nbsp;".str_replace(' ', '&nbsp;', dater ($msginfo['date']))."&nbsp;
      </td>";
               }
            echo "
    </tr>
<tr><td colspan='4'> </td></tr>
";
	 }

         echo "
  </table>";
      }
      else
         echo "<blockquote>"._L("There are no messages")."</blockquote>";
   }

   $_SESSION['apps'][$eyeapp]['wrapup'] = "\$_SESSION['apps']['$eyeapp']['state.lastread'] = ".time ().";";
   return '';       
}
}

$appfunction = 'eyeMessages';
?>

<?php

if (defined ('USR') && !function_exists ('eyeApp')) {
function eyeApp ($eyeapp, $appinfo) {

  foreach (array ('global', 'skin', 'autorun') as $tab)
    if (isset ($_REQUEST[$tab]))
      foreach ($_REQUEST[$tab] as $k => $v)
        if ($v) 
          $aparam[$k][$tab] = $v;

  $appsInstallable = (APP_INSTALLATION == 2) || ((APP_INSTALLATION == 1) && (USR == ROOTUSR));  

  //Upload and install process
  if ($appsInstallable && !empty ($_REQUEST['loadid']) && ($appinfo['id']['load'] == $_REQUEST['loadid']) && !empty ($_FILES)) {
    include_once SYSDIR.'archive.php';	   
    $nompaquet = $_FILES['file']['name'];
    if (is_string ($ar = ar_open ($_FILES['file']['tmp_name'], $nompaquet)))
      $errmsg = $ar;
    else {
 	    $appdir = null;
	    $appfiles = array ();
	    while ($af = ar_nextfile ($ar)) {
	      $appfiles[] = $af = $af['name'];	 
	      if ($appdir === null)
	        $appdir = dirname ($af);
	      else
	        while ($appdir && (FALSE === strpos ($af, $appdir)))
		        $appdir = dirname ($appdir);
      }
	 
//	  echo "<pre>appdir : $appdir\n"; print_r ($appfiles); echo '</pre>';

	    if (!in_array ("$appdir/aplic.php", $appfiles) ||
	        !in_array ("$appdir/ico_a.png", $appfiles) ||
	        !in_array ("$appdir/".APP_INFO, $appfiles))
	      $errmsg  = _L('%0 does not conform to an eyeOS application', basename ($appdir));  

	    if ($appdir) {
	      $abaselen = strlen ($appdir)+1;
        $appdir = (((USR==ROOTUSR) && (!empty ($_REQUEST['sysapp']))) ? SYSAPPS : USRAPPS).$appdir.'/';
	    } else {
	      $appdir = (((USR==ROOTUSR) && (!empty ($_REQUEST['sysapp']))) ? SYSAPPS : USRAPPS).substr ($nompaquet, 0, strlen ($nompaquet) - 7).'/';
        $abaselen = 0;	    
	    }

	    if (empty ($errmsg) && !is_dir ($appdir = filename ($appdir))) {
	      ar_rewind ($ar);
        foreach ($appfiles as $af) {
	        makedir (dirname ($fname = $appdir.substr ($af, $abaselen)));
	        if ($fd = fopen ($fname, 'wb')) {
	          ar_extractfile($ar, $fd);
	          fclose ($fd);
	        }
	      }
	      msg (_L('Application %0 successfully installed', basename ($appdir)));

	    } elseif (empty ($errmsg))
	      $errmsg = _L('Application %0 already exists', basename ($appdir));
	   
	    ar_close ($ar);  
    } 
  }
   
  // SET NEW APPS IN DOCK BAR---------------------------------------------
   if (!empty ($_REQUEST['dockid']) && ($appinfo['id']['dock'] == $_REQUEST['dockid'])) {
     if (!empty ($_REQUEST['iconbar'])) {
       $newicons = array ();	      
       foreach ($_REQUEST['iconbar'] as $app)
         if (makeApp ($app, '', false))
	         $newicons[] = $app;
		
       parse_update (USRDIR.USR.'/'.USRINFO, 'apps', $_SESSION['usrinfo']['apps'] = implode(',', $newicons));
     }
      
     if (!empty ($aparam)) {
	     $autorun = '';
       foreach (array_keys ($aparam) as $app)
	       if (false !== ($aInfo = makeApp ($app, '', false))) {
	         if (!empty ($aparam[$app]['autorun']))
	           $autorun .= ";$app(".trim ($aparam[$app]['autorun'], '()').')';
		  
	         if (!empty ($aparam[$app]['skin']) && ($aInfo['skin'] != $aparam[$app]['skin'])) {
	           parse_update (USRDIR.USR.'/'.basename($app).'.xml', 'skin', $aparam[$app]['skin'], str_replace('.', '_', basename($app)));
		         if (isset ($_SESSION['apps'][$app])) {
		           $aInfo = makeApp ($app, '', false);
		           foreach (array ('skin', 'cssfiles', 'scriptfiles') as $f)
                 @$_SESSION['apps'][basename($app)][$f] = $aInfo[$f];
		           $restart = true;	
		         }
	         }
	       }
	    
       if ($_SESSION['usrinfo']['autorun'] != $autorun) {		 
         $_SESSION['usrinfo']['autorun'] = $autorun;		 
         parse_update (USRDIR.USR.'/'.USRINFO, 'autorun', $autorun);
	     }
     }

     if (!empty ($restart))
       echo "<script language='javascript'>window.location.href = 'desktop.php';</script>";
      
     msg ('App preferences saved');
   }
   
   // DELETE AN APPLICATION PROCESS
  if ($erasingapp = basename (@$_REQUEST['app2erase'])) {
	  $uadir = USRDIR . USR."/apps/";
	  if ((USR == ROOTUSR) && isset ($_REQUEST['systemwideapp'])) 
      $uadir = SYSAPPS;
    if (is_dir ("$uadir$erasingapp/")) {
      function eraseapp($appforerasing) {
        if (empty($appforerasing))
          return true;
           
        if (!$directoriborrant = opendir ($appforerasing))
          return false;
             
        while ($arxiusdeapp = readdir ($directoriborrant))
          if ($arxiusdeapp != '.' && $arxiusdeapp != '..') {
            if (!is_dir ($appforerasing . $arxiusdeapp))
              @unlink ($appforerasing . $arxiusdeapp);
            else
              eraseapp ("$appforerasing$arxiusdeapp/");
          }

        closedir ($directoriborrant);
        return rmdir($appforerasing);
      }

      if (eraseapp ("$uadir$erasingapp/"))
        msg ('Application %0 deleted successfully', $erasingapp);
    }
  } 

  if (empty ($_SESSION['apps'][$eyeapp]['argv'])) {
    if ($appsInstallable) 
      addActionBar("
     <a href='?a=$eyeapp(installer)'>
      <img border='0' alt='"._L('Install Apps')."' title='"._L('Install Apps')."' src='".findGraphic('','btn/new.png')."' \>
     </a>");
     
    addActionBar("<strong>"._L('Application Manager')."</strong>",'center');
    if (!empty ($errmsg)) 
      echo "
        <div style='color:red; text-align:center '>$errmsg</div>";
    echo "
    <form name='icons' method='get' action='desktop.php'>
      <input type='hidden' name='dockid' value='".($_SESSION['apps'][$eyeapp]['id']['dock']=mt_rand())."'/>
      <table width='90%' align='center'>
        <tr>
	  <td>&nbsp</td>
	  <td><strong>"._L('Name')."</strong></td>
	  <td align='center'><strong>"._L('Icon')."</strong></td>
	  <td align='center'><strong>"._L('Auto')."</strong></td>
	</tr>
	<tr>
	  <td colspan='10'><hr /></td>
	</tr>
	<tr>
	  <td>";
    $maxicons = MAXICONS - 1;
    $arun = array ();	   
    foreach (@explode (';', $_SESSION['usrinfo']['autorun']) as $app)
      if (($app = trim ($app)) && preg_match ('!^(.+)\s*\((.*)\)!', $app, $args))
        $arun[trim ($args[1])] = '('.trim($args[2]).')';
	    else
	      $arun[$app] = '()';
	 
    foreach (explode (',', APPDIRS) as $nadir) {
      if ($directory = @opendir ($nadir = filename ($nadir))) {
        while ($pgm = readdir ($directory))
	        if (($pgm != basename(APPMANAGER)) && 
	            (false !== ($aInfo = makeApp ("$nadir$pgm", '', false))) && 
		           appIcon ("$nadir$pgm")) {
            echo"
	  </td>
	  <td>$pgm</td>
	  <td align='center'>
            <input type='checkbox' name='iconbar[]' value='$nadir$pgm' onclick='
              ic=0;
              for (var app in document.icons.elements) 
                if (document.icons.elements[app].checked) ic++;
	      if (ic>$maxicons) { 
	        this.checked=false;
	          alert(\""._L('At most %0 apps may be selected', $maxicons)."\");}'
              ".(in_array($nadir.$pgm, explode (',', @$_SESSION['usrinfo']['apps'])) ? " checked" : '')." />
	  </td>
 	  <td align='center'>
            <input type='text' size='15' name='autorun[$nadir$pgm]' value='".
	       (!empty ($arun[$nadir.$pgm]) ? $arun[$nadir.$pgm] : '')."') />
	  </td> 
    <td> 
      <img 
        src='${appinfo['appdir']}gfx/restore.png' 
        alt='"._L('Restore %0',$pgm)."' 
        title='"._L('Restore %0',$pgm)."'
        style='cursor:pointer;'
        onclick='restoreApp(\"$pgm\")'
        border='0' />
     </td>";
	  
	          if (count ($aInfo['skins']) > 1) {
		          echo "<td>
	    <select name='skin[$nadir$pgm]' >";
	            foreach ($aInfo['skins'] as $s)
		            echo "
	       <option".((@basename($aInfo['skin']) == $s) ? ' selected' : '').">$s</option>";
	              echo " 
	    </select></td>";
		        } else
		          echo "&nbsp";
		     
	          echo "
	  	  <script LANGUAGE=\"JavaScript\">
function WRemoveApp() {
var agree=confirm(\""._L('File will be permanently deleted. Continue?')."\");
if (agree) return true; else return false ; }
</script>\n
"; 
	          if ($nadir == USRDIR . USR."/apps/") 
		          echo "<td><a onclick=\"return WRemoveApp()\" href='desktop.php?a=$eyeapp&app2erase=$pgm'><img border=\"0\" src=\"".findGraphic('', 'btn/delete.png')."\"></a></td>";

	          if (($nadir == SYSAPPS) && (USR == ROOTUSR)) 
		          echo "<td><a onclick=\"return WRemoveApp()\" href='desktop.php?a=$eyeapp&app2erase=$pgm&systemwideapp'><img border=\"0\" src=\"".findGraphic('', 'btn/delete.png')."\"></a></td>";

	          echo "
	 </tr>
	<tr>
	  <td>";
	        }
          closedir ($directory);
        }
      }
      echo "
          </td>
	</tr>
      </table>
      <br />
      <div align='center'>
        <input type='hidden' name='iconbar[]' value='".APPMANAGER."' />
        <input name='Submit' type='submit' value='"._L('Update app configuration')."' />
      </div>
    </form>";

      $_SESSION['apps'][$eyeapp]['title'] = _L('Application Manager');
      $window = 'manager';
   }

   elseif ((@$appinfo['argv'][0] == 'installer') && $appsInstallable) {
    addActionBar("
     <a href='?a=$eyeapp'>
      <img border='0' alt='"._L('Go Back')."' title='"._L('Go Back')."' src='".findGraphic('','btn/back.png')."' \>
     </a>");
    addActionBar("<strong>"._L('Application Installer')."</strong>",'center');
      echo "
    <div align='center'> 
    <br />";
    
      include_once SYSDIR.'archive.php';	   
      if (0 == count ($ar = ar_support ()))
         echo _L('Sorry, your configuration does not support any archive format');
      else {


if (!isset($_SESSION['apps'][$eyeapp]['id']['load'])) $_SESSION['apps'][$eyeapp]['id']['load'] = mt_rand();
     echo "
    <form action='?a=$eyeapp' enctype='multipart/form-data' method='post'>
      <input type='hidden' name='loadid' value='".$_SESSION['apps'][$eyeapp]['id']['load']."'/>";

         echo "
      ". ((count ($ar)== 1) ?      
         _L('Your system supports only the %0 archive format<br/>', $ar[0]) :
         _L('Your system supports the following archive formats :<br/>%0<br/>', implode (', ', $ar)));

	 $xl = '';
	 $xapp = array ();
	 
	 if (!empty ($appinfo['param.appsite']) && !empty ($appinfo['param.applist']) && ($applist = @file_get_contents ($appinfo['param.applist']))) {
	    echo _L('The following apps are available from %0', $appinfo['param.appsite'])."
	 <table width='80%' align='center'>";
	 
	    foreach (xmlParse ($applist, array ('tree' => 0, 'discard' => true)) as $k => $v) {
               $kv = explode ('/', $k);		 
   	       if ($xl != $kv[1]) {
	          if (count ($xapp) && !empty ($xapp['title'])) {
	             echo "
	   <tr>
	     <td>
	        <input type='checkbox' name='eyeApp[]' value='${xapp['title']}' />
	     </td>	
	     <td>${xapp['title']}</td>
	     <td>${xapp['version']}</td>
     	     <td>${xapp['updated']}</td>
      	     <td>${xapp['category']}</td>
     	     <td>${xapp['tech']}</td>

	   </tr>";
	          }
	          $xapp = array (); 	  
	          $xl = $kv[1];
	       } 
               $xapp[$kv[2]] = $v;
	    }
	 }
	 
	 echo "
	 </table><br/>";
	 
         echo "      
      <br />	 
      "._L('Select application package')." 
      <br /><br />
      <input name='file' type='file' size='30'>
      <br>
      <br>";

        if (USR == ROOTUSR) 
           echo "
      "._L('Install as system application (for all users)? ')."
      <input type='checkbox' name='sysapp'>
      <br/><br />
      ";

        echo "
      <input name='Submit' style='border: 0; background-color: transparent; color: #929292;' TYPE='image' SRC='".findGraphic ('', "btn/upload.png")."'>
    </form>
    </div>";
      }
      $_SESSION['apps'][$eyeapp]['title'] = _L('Application Installer');
      $window = 'installer';
   }
}
}
$appfunction = 'eyeApp';
?>

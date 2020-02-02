<?PHP
/*                              eyeOS project
                     Internet Based Operating System
                               Version 0.9
                     www.eyeOS.org - www.eyeOS.info
       -----------------------------------------------------------------
                  Pau Garcia-Mila Pujol - Hans B. Pufal
       -----------------------------------------------------------------
          eyeOS is released under the GNU General Public License - GPL
               provided with this release in DOCS/gpl-license.txt
                   or via web at www.gnu.org/licenses/gpl.txt

         Copyright 2005-2006 Pau Garcia-Mila Pujol (team@eyeos.org)

          To help continued development please consider a donation at
            http://sourceforge.net/donate/index.php?group_id=145027         */

   if (!defined ('OSVERSION')) require_once 'sysdefs.php';  // if not autoprepended
   
   if (!isset ($_SESSION['usr']) || !is_file (USRDIR.ROOTUSR.'/'.USRINFO) || !is_file (SYSINFO)) {
      session_destroy ();
      include 'index.php';
      exit;
   }
   
   if (!empty ($_SESSION['browser_ie']))
      define ('BROWSER_IE', 1);

   define ('TOFFSET', $_SESSION['Toffset']);   
   define ('USR', $usr = $_SESSION['usr']);
   define ('RAX_SESSION', $_SESSION['rax']);
   unset ($_SESSION['reqkey']);

   if (!empty ($_REQUEST['pos']) && isset ($_SESSION['apps'][$app = $_REQUEST['pos']])) {
//   error_log ("Pos : $_REQUEST[pos] : $_REQUEST[x],$_REQUEST[y] $_REQUEST[w],$_REQUEST[h] $_REQUEST[z]");  
     $_SESSION['apps'][$app]['window.x_pos'] = trim ($_REQUEST['x'], 'px');
     $_SESSION['apps'][$app]['window.y_pos'] = trim ($_REQUEST['y'], 'px');
     $_SESSION['apps'][$app]['window.height'] = trim ($_REQUEST['h'], 'px');
     $_SESSION['apps'][$app]['window.width'] = trim ($_REQUEST['w'], 'px');
     $_SESSION['apps'][$app]['window.zindex'] = trim ($_REQUEST['z']);
     exit;        
   }

   @include_once SYSDIR.LANGFILE;

   if (empty ($_SESSION['sysinfo']['syscalldisable']))
     include_once SYSDIR.'syscall.php';
   
   
   // Start an application 	    
   foreach (explode (';', @"$a;${_REQUEST['a']}") as $app) {
      $argv = array ();	   
      if (($app = trim ($app)) && preg_match ('!^(.+?)\s*\((.*)\)$!', $app, $args)) {
         $app = trim ($args[1]);     
         foreach (explode (',', trim ($args[2])) as $arg)
            $argv[] = trim ($arg);
      }

      if ($app && !makeApp ($app, $argv))
         foreach (explode (',', APPDIRS) as $dir)
            if (makeApp (filename ($dir.$app), $argv))
               break;
   }
   
   if (sizeof ($_SESSION['apps']) > 1) // sort apps by zindex      
      uksort ($_SESSION['apps'], create_function ('$a,$b', 
         'return $_SESSION["apps"][$a]["window.zindex"] < $_SESSION["apps"][$b]["window.zindex"] ? -1 : 1;'));

echo "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\" >
<html>
<head>
  <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
  <title>". USR . ' @ ' . $_SESSION['sysinfo']['os'] ." </title>
";

if (file_exists(SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/default.css')) {
   $cssfiles = array (SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/default.css');
   if (defined ('BROWSER_IE') && file_exists(SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/IE/default.css')) $cssfiles[] = SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/IE/default.css';
}elseif (file_exists(THEMESDIR.$_SESSION['usrinfo']["theme"].'/default.css')) {
   $cssfiles = array (THEMESDIR.$_SESSION['usrinfo']["theme"].'/default.css');
   if (defined ('BROWSER_IE')&& file_exists(THEMESDIR.$_SESSION['usrinfo']["theme"].'/IE/default.css')) $cssfiles[] = THEMESDIR.$_SESSION['usrinfo']["theme"].'/IE/default.css';
} else {
   $cssfiles = array (SYSDIR.'themes/default/default.css');
}
   $scriptfiles = array ();
   foreach (array (SCRIPTDIR, @$_SESSION['sysinfo']['skin']) as $sdir)  
      if ($adir = opendir ($sdir = filename ($sdir))) {
         while ($f = readdir ($adir))
            if (strtolower (substr ($f, -3)) == '.js')
	       array_unshift ($scriptfiles, $sdir.$f);
	    elseif (strtolower (substr ($f, -4)) == '.css')
	       array_unshift ($cssfiles, $sdir.$f);    
         closedir ($adir);
      }

   foreach ($_SESSION['apps'] as $app) {
      if (!empty ($app['scriptfiles']))	   
         $scriptfiles = array_merge ($scriptfiles, $app['scriptfiles']);
      if (!empty ($app['cssfiles']))	   
         $cssfiles = array_merge ($cssfiles, $app['cssfiles']);
   }
   
   foreach (array_unique ($scriptfiles) as $f)
      echo "
  <script language='JavaScript' type='text/javascript' src='$f'></script>";
  
   foreach (array_unique ($cssfiles) as $f)
      echo "
  <link rel='stylesheet' type='text/css' href='$f' />";

   echo "
  <link rel='icon' href='".findGraphic ('', "icon.gif")."' type='image/x-icon' />  
  <link rel='shortcut icon' href='".findGraphic ('', "icon.gif")."' type='image/x-icon' />
</head>
<body
   style='overflow:hidden;".
   (empty ($_SESSION['fondoescollit']) ? '' : "
    background: url(${_SESSION['fondoescollit']}); 
    background-repeat: no-repeat; 
    background-attachment: fixed; 
    background-position: center center;") . "'>";

    if (DEBUG & 2)
      echo "
         <div id='eyeOSdbg' 
	   style='
	     background-color:#d0d0d0;
	     color: 0;
	     position:absolute; 
	     padding :5px;
	     width: 250px;
	     height: 350px;
	     left:800px;
	     top:50px;
	     z-index:1000;
	     overflow:auto;'>
	   <h3 align=center>Debug panel<h3></div>
	   <script>
	     xEnableDrag ('eyeOSdbg');
	     xShow ('eyeOSdbg');
	   </script> 
      ";

   if (!empty ($_SESSION['apps']))   
      foreach ($_SESSION['apps'] as $eyeapp => $appinfo) {
          
            if (!win ($eyeapp, $appinfo))
              unset ($_SESSION['apps'][$eyeapp]);
	    else
               if (DEBUG & 2) { echo "<pre>$eyeapp : \n"; print_r ($_SESSION['apps'][$eyeapp]); echo '</pre>'; }

      }

if (file_exists(SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/desktop/exit.png'))
   $closesessimg = SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/desktop/exit.png';

elseif (file_exists(THEMESDIR.$_SESSION['usrinfo']["theme"].'/desktop/exit.png'))
   $closesessimg = THEMESDIR.$_SESSION['usrinfo']["theme"].'/desktop/exit.png';

else
   $closesessimg = SYSDIR.'themes/default/desktop/exit.png';

   echo "<div class='panel'></div>
    <div class='botosortir'>
      <img 
        alt='"._L('Close session')."' 
        title='"._L('Close session')."' 
        border='0' 
        src='".$closesessimg."'
        style='cursor:pointer;'
        onclick='closeAll ()'
        />
    </div>
    <div class='rlltg Gclock' Gclock='format:%g~%i %a;'></div>";

   // Check trash bin
   $iconapaperera = 'empty';
   if (is_dir (USRDIR."$usr/Trash") && $directori = opendir (USRDIR."$usr/Trash")) {
      while ($icopap = readdir ($directori))
         if ($icopap <> '..' && $icopap <> '.') {
            $iconapaperera = 'full';
            break;
         }
      closedir ($directori);
   }

   echo "
  <div class='icnbrossa'>";
  if (is_dir (SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/apps/eyeTrash.eyeapp'))
    showAppIcon (SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/apps/eyeTrash.eyeapp/'.$iconapaperera.'.png', 'eyeTrash.eyeapp');
  elseif (is_dir (THEMESDIR.$_SESSION['usrinfo']["theme"].'/apps/eyeTrash.eyeapp'))
    showAppIcon (THEMESDIR.$_SESSION['usrinfo']["theme"].'/apps/eyeTrash.eyeapp/'.$iconapaperera.'.png', 'eyeTrash.eyeapp');
  else
  showAppIcon ("apps/eyeTrash.eyeapp/".$iconapaperera.".png", 'eyeTrash.eyeapp');
  echo " </div>
  
  <div id='barraicones'>";  //---> icon bar

   if (!empty ($_SESSION['usrinfo']['apps'])) {
      foreach (explode (',', $_SESSION['usrinfo']['apps']) as $app)
         if ($app = filename (trim ($app))) {
	    if (is_dir (SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/'.$app))
		  appIcon ($app, SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"].'/');
	    elseif (is_dir (THEMESDIR.$_SESSION['usrinfo']["theme"].'/'.$app))
		  appIcon ($app, THEMESDIR.$_SESSION['usrinfo']["theme"].'/');

	    elseif (is_dir ($app))
               appIcon ($app);
	    else   
	       foreach (explode (',', APPDIRS) as $appdir)
	          if (is_dir ($appdir.$app))
		     appIcon ($app, $appdir);
	          elseif (is_dir ($appdir.$app.'.eyeapp'))
		     appIcon ($app.'.eyeapp', $appdir);
         }
   } else {
      foreach (explode (',', APPDIRS) as $appdir)
         if ($adir = @opendir ($appdir = filename ($appdir)))
	    while ($app = readdir ($adir))
                  appIcon ($app, $appdir);
   }
   echo "

<div id='eyeTitles' class='coloriconstext'></div>
</div>";  //--->end of icon bar

   if ((defined ('MICROAPPS') && MICROAPPS) || (isset ($_SESSION['sysinfo']['MICROAPPS']) || isset ($_SESSION['usrinfo']['MICROAPPS'])))
      foreach (explode (',', APPDIRS) as $appdir)
         if ($adir = @opendir ($appdir = filename ($appdir)))
            while ($app = readdir ($adir))
	       if ((strtolower (substr ($app, -4)) == '.app') && (false !== $appinfo = parse_info ($appdir.$app))) {
	          if (empty ($appinfo['applet.run'])) 	       
	             echo "
   <button onClick='window.location=\"?a=${appinfo['applet.run']}\"'>
     ".$app."
   </button>";
	       }
	    
   foreach (explode (',', @$_SESSION['sysinfo']['sysapps']) as $eyeapppath) 
      if ($eyeapppath = trim ($eyeapppath)) {
         $eyeapp = basename($eyeapppath);
         include "$eyeapppath/".APP_CODE; 
      }
   
   echo "
</body>
</html>";
   exit;
?>

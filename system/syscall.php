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
            
// eyeOS SysCall module

   // As new modules are loaded, they are added to this array, the key is the
   // module name, the value is the name of the fucntion implementing that module

   $sysModule = array ( );

   function installModule ($mod, $func) {
      // Mod is the moudle name, func the handler. This routine chaks that 
      // the module is not already installed, and that the function exists
      // before adding the module to the list
      global $sysModule;	   
      $mod = strtolower (trim ($mod));
      if (! isset ($sysModule[$mod])) {
         if (function_exists ($func))
	    $sysModule[$mod] = $func;
	 else
            error_log ("Err SysCall module $mod function $func missing"); 
	 
      } else
         error_log ("Err SysCall module $mod already installed"); 
   }
   

   function sysCallRegister ($app, $service) {
      // Applications need to register the names of the system calls that
      // they are prepared to receive. This function adds the named srvice
      // to the list of those accepted by the application app.
      $_SESSION['apps'][$app]['syscall'] .= ', ' . strtolower ($service);  	   
   }

   
   function sysCall ($syscall) {
      // This funcgtion is called on reception of an AJAX messages from the
      // client. A mesage is composed of several elments  :
      //    [ e0][ e1 ][ e2 ] ....
      // Each eleement is taken in turn and the rest are passed on to the 
      // succeeding levels
      // Here we take the first element, look it up in the module list and
      // call the module handler if it exists.
      
      global $sysModule;
      $syscall = explode ('][', trim ($syscall, '[]'));
      if (DEBUG & 16) error_log ('sysCall "' . implode ('", "', $syscall) . '"');

      if ('remote' == ($m = strtolower (trim (array_shift ($syscall))))) {
         define ('REMOTE', true);
	 $m = strtolower (trim (array_shift ($syscall)));
      }
      else
         define ('REMOTE', false);
      
      if (isset ($sysModule[$m]))
        call_user_func ($sysModule[$m], $syscall);
      else { 
        error_log ("Err SysCall module '$m' not available : " . implode ('", "', $syscall) . '"');
        header("HTTP/1.0 404 Not Found");
      }
   }


  function SysCall_sys ($msg) {
    // Here we implement all the base system functions.	   
    switch (strtolower (trim ($call = array_shift($msg)))) {
    case 'app': 
      if (!$appinfo = $_SESSION['apps'][$SYSipcapp = array_shift ($msg)]) {
        error_log ("Error : $SYSipcapp not running");     
        break;
      }
      
      $appinfo = $_SESSION['apps'][$eyeapp = $SYSipcapp];
      @include $appinfo['appdir'].LANGFILE;
	    
      switch (strtolower (trim ($call = array_shift($msg)))) {
      case 'ipc': // IPC sends a message to running application
        if (false !== strpos (strtolower ($_SESSION['apps'][$SYSipcapp]['syscall']), 'sys/ipc')) {
	        $SYSipcmsg = (sizeof ($msg) == 1) ? $msg[0] : $msg;
	        include $appinfo['appdir'].APP_CODE;
  	      return;   
	      }
        elseif (is_file ($appinfo['appdir'].'ipc.php')) {
	        $SYSipcmsg = (sizeof ($msg) == 1) ? $msg[0] : $msg;
	        include $appinfo['appdir'].'ipc.php';
  	      return;   
        }
        
        error_log ("Err sys/ipc app $SYSipcapp not available : \"".implode ('", "', $msg) . '"');
        break;

      case 'getparams': // get app parameters
        $pstruct = array ();
        foreach ($appinfo as $p => $v) {
          $p = explode('.', $p);
          if ($p[0] == 'param') {
            @!$pstruct[$p[1]] && ($pstruct[$p[1]] = "name:$p[1];");
            if (count ($p) == 2)
              $pstruct[$p[1]] .= "value:$v;";
            if ((count ($p) == 3) && ($p[2] == 'args')) {
              foreach (explode (';', $v) as $pt) {
//                echo "args : $p[1] : $pt\n";
                if (preg_match ('/^([a-z0-9-_]+)(:(.*))?$/i', trim ($pt), $pt) && ($pt[1] == 'label') && $pt[3])
                  $v = str_replace ($pt[3], _L(trim($pt[3])), $v);
              }
              $pstruct[$p[1]] .= "$v;";
            }
          }
        }
  
        $sep = '';
        foreach ($pstruct as $p => $v) {  
          echo "$sep$v";
          $sep = '://:';
        }
        return;
      
      case 'setparams': // set app parameters
        $p = split ('://:', $msg[0]);
        $rs = '';
        $ap = array ();
        foreach ($p as $ps)
          if (preg_match ('/^(.+?)=(.*)$/', $ps, $ps))
            $_SESSION['apps'][$SYSipcapp]["param.$ps[1]"] = $ap["param/$ps[1]"] = $ps[2];
        if (count ($ap)) {
          if (parse_update (USRDIR.USR."/$SYSipcapp.xml", $ap, null, $SYSipcapp))
            echo 'OK';
          else {
            error_log ("Error : $SYSipcapp parameter update :".USRDIR.USR."/$SYSipcapp.xml : $ap");
            echo _L('Error updating %0 parameters', $SYSipcapp);
          }
        }
        return;  

      default:
        error_log ("Error : $SYSipcapp $call not recognized function");
      }
      break;
      
    case 'wclose': // close window
      if ('*' == ($app = $SYSipcapp = array_shift ($msg))) {
        foreach (array_keys ($_SESSION['apps']) as $app)
          closeApp ($app);
          
        session_destroy ();
        break;
      }
      
      if (!isset ($_SESSION['apps'][$app])) {
        error_log ("Error : $SYSipcapp not running");     
        break;
      }

      closeApp ($app);
      break;

    case 'wrestore' : // Restore app window size and position
      if ($app = $SYSipcapp = array_shift ($msg))
        foreach (explode (',', ','.APPDIRS) as $dir)
          if (is_dir ($dir .= rtrim ($app, "/\\").'/') && (false !== ($appDefaults = parse_info ($dir.APP_INFO))))
            break;

      if (@$appDefaults) {
        if (!$_SESSION['apps'][$app])
          parse_update (USRDIR.USR."/$app.xml", 'state/window', 
            $appDefaults['window.width'].','.$appDefaults['window.height'].',-1,-1');

        echo 'x:100; y:100; w:', $appDefaults['window.width'], '; h:', $appDefaults['window.height'];
      } else {
        error_log ("Error : wrestore cannot find app $app");
        echo 'error:'._L('Cannot find %0 for restore', str_replace ('.eyeapp', '', $app)).';';
      }

      break;

    case 'ver': // Request system version
      echo file_get_contents (SYSINFO);
	    return;
   
    default:
      error_log ("Err sys/$call : no such action : \"".implode ('", "', $msg) . '"');
      break;
    }
    
    header ("HTTP/1.0 404 Not Found");
    return;
  }

  
  installModule ('sys', 'SysCall_sys');
   
  if (!empty ($_REQUEST['syscall'])) {
//    error_log ('syscall : ' . $_REQUEST['syscall']);	   
    sysCall ($_REQUEST['syscall']);
    exit;
  }
?>

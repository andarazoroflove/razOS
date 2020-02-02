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

if (strtolower(ini_get('safe_mode')) == 'on' || ini_get('safe_mode') == 1) {
      echo "
      <html>
      <head>
         <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
         <link rel='stylesheet' href='".CSSDIR."install.css' type='text/css'>
         <link rel='icon' href='".findGraphic ('', "icon.gif")."' type='image/x-icon' />  
         <link rel='shortcut icon' href='".findGraphic ('', "icon.gif")."' type='image/x-icon' />
         <title>eyeOS Install ".OSVERSION."</title>
      </head>
      <body>
         <div style='margin-left: 50px; margin-right: 50px; text-align: center'>
         <br /><h1>eyeOS Warning</h1>

         <br /> <span style='color:red;'><strong>Your web sever configuration does not allow eyeOS to run.</strong><br /><br />
         <div align='center' style='margin-left: 100px; margin-right:100px;'> This is due to the parameter '<strong>safe_mode</strong>' is set to '<strong>on</strong>' in your '<strong>php.ini</strong>' file. If you have access to that file, you could change 'safe_mode' to 'Off' and restart your web server in order to use eyeOS. <br /> <br />
         We apologize for this situation which is beyond our control.</div></span>

      </body>
      </html>
"; 
	exit();
}
   if (!is_file ('sysdefs.php') && is_file ('funcions.php')) {
      header ('Location: ../index.php');
      exit;
   }

   if (!defined ('OSVERSION')) include_once 'sysdefs.php';  // if not autoprepended
   if (is_file (USRDIR.ROOTUSR.'/'.USRINFO) && is_file (SYSINFO)) {
      include 'index.php';
      exit;
   }

   if (!defined ('DEBUG')) {
     define ('DEBUG', !empty ($_REQUEST['debug']) ? $_REQUEST['debug'] : 0);
     error_reporting (DEBUG ? E_ALL : 0);
   }

   $sysdefs = file_get_contents ('sysdefs.php');
   $md5sysdefs = md5 ($sysdefs);
   if (preg_match ('!^//>>(.*?)$!m', $sysdefs, $sysvars))
      $sysvars = explode (',', $sysvars[1]);

   switch (@$_REQUEST['mode']) {
   case "installing":
      if (empty($_REQUEST['systemname']) || empty($_REQUEST['langselected']) || empty($_REQUEST['rpw1']) || empty($_REQUEST['rpw2']))
         $msg = _L('Please enter all information requested');
      elseif ($_REQUEST['rpw1'] != $_REQUEST['rpw2'])
         $msg = _L('Passwords do not match, please reenter');
      else 
         while (1) {
            createXML (SYSINFO, 'eyeOS', array (
	       'hostname' => $_REQUEST['systemname'],
	       'lang' => $_REQUEST['langselected'],
	       'os' => 'eyeOS',
	       'ver' => OSVERSION
	    )); 	      

	    if (!is_file (SYSINFO)) {
	       $msg = _L('Failed to create file %0', SYSINFO); 
	       break;
	    }
	    
/* Build list of available apps 	 
	    $applist = array ();
            foreach (explode (',', APPDIRS) as $appdir)
               if ($adir = @opendir ($appdir = filename ($appdir)))
	          while ($app = readdir ($adir))
                     if (($app{0} != '.') && is_dir ($appdir.$app) && file_exists ($appdir."$app/".APP_ICON) && 
                        file_exists ($appdir."$app/".APP_INFO) && file_exists ($appdir."$app/".APP_CODE))
		        $applist[] = $appdir.$app;
*/
            @mkdir (USRDIR.ROOTUSR);
            @chmod (USRDIR.ROOTUSR, 0777);
	    if (!file_exists (USRDIR.ROOTUSR)) {
	       $msg = _L('Failed to create directory %0', USRDIR.ROOTUSR); 
	       break;
	    }

	    createXML (USRDIR.ROOTUSR.'/'.USRINFO, ROOTUSR, array (
	       'hostname' => $_REQUEST['systemname'],
	       'pwd' => md5 ($_REQUEST['rpw1']),
	       'real' => 'root',
	       'usr' => 'usr',
	       'wllp' => SYSDIR."themes/default/eyeos.jpg",
	       'theme' => 'default',
	       'lang' => $_REQUEST['langselected'],
         'email' => $_REQUEST['email'],
   	     'apps' => 'apps/eyeHome.eyeapp,apps/eyeEdit.eyeapp,apps/eyeCalendar.eyeapp,apps/eyePhones.eyeapp,apps/eyeCalc.eyeapp,apps/eyeMessages.eyeapp,apps/eyeBoard.eyeapp,apps/eyeNav.eyeapp,apps/eyeRSS.eyeapp,apps/eyeOptions.eyeapp,apps/eyeInfo.eyeapp,apps/eyeApps.eyeapp'
//	     'apps' => implode (',', $applist)
// or add eyeApps to run_once list ??
	    ));

	    if (!file_exists (USRDIR.ROOTUSR.'/'.USRINFO)) {
	       $msg = _L('Failed to create file %0', USRDIR.ROOTUSR.'/'.USRINFO); 
	       break;
	    }
	    
      @mkdir (HOMEDIR.ROOTUSR);
      @chmod (HOMEDIR.ROOTUSR, 0777);
	    if (!file_exists (HOMEDIR.ROOTUSR)) {
	       $msg = _L('Failed to create directory %0', HOMEDIR.ROOTUSR); 
	       break;
	    }

      if (!empty ($_REQUEST['email'])) {
        $key = sprintf ("%X%08X", rand(), time());  

        if (!mail ($_REQUEST['email'], $_REQUEST['systemname'], 
          _L('%0 eyeOS root user email validation : %1', 
          $_REQUEST['systemname'], 
          "http://$_SERVER[SERVER_NAME]/".trim($_SERVER['REQUEST_URI'], '/')."?validate=$key&newuser=".ROOTUSR),
          "From: $_REQUEST[email]\r\nReply-To: $_REQUEST[email]\r\nX-Mailer: PHP/" . phpversion())) {
          $mag = _L('Sending of validation email failed');
          break;
        }

        if (!parse_update (USRDIR.ROOTUSR.'/'.USRINFO, 'validate', $key))
          log_error ('parse_update ROOT USRINFO failed');
      }
      
	    $_REQUEST['usr'] = ROOTUSR;
	    $_REQUEST['pwd'] = $_REQUEST['rpw1'];
	    include 'index.php';
	    exit;
	 }
      break;
      
   case 'setsecurity' :
      $msg = '';
      if (!empty ($sysvars) && ($_REQUEST['id'] == $md5sysdefs)) {
        foreach ($sysvars as $v)
          if (!empty ($_REQUEST[$v = strtoupper (trim($v))])) {
	          $n = $_REQUEST[$v];
          
          if ((substr ($v, -3) == 'DIR') && (substr ($n, -1) != '/'))
	          $n .= '/';

          switch ($v) {  
          case 'STATSDIR':
            if ($n) {   
              if (!is_dir($n))
                @mkdir ($n);
              if (!is_writable ($n))
        	      $msg =_L ("Statistics directory, %0, not created or not writeable", $n);
            }
            break;
            
         case 'CREATE_ACCOUNTS':
         case 'SHOW_USER_LISTS':
            $n = (strtolower ($n) == 'yes') ? 'yes' : 'no';
            break;
          }   
          
         $sysdefs = preg_replace ( "!(define\s*\\(\'$v\',\s*)\'.*?\'(\);)!", "\$1'$n'\$2", $sysdefs);
	    }
	    
      if (!$msg && $fd = fopen ('sysdefs.php', 'w')) {
  	    fwrite ($fd, $sysdefs);
	      fclose ($fd);
      
        header ('Location: index.php');
        exit;
      }
	  else
	     $msg ="Cannot write to system definition file";
      }
   }

   echo "
<html>
  <head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <link rel='stylesheet' href='".CSSDIR."install.css' type='text/css'>
    <link rel='icon' href='".findGraphic ('', "icon.gif")."' type='image/x-icon' />  
    <link rel='shortcut icon' href='".findGraphic ('', "icon.gif")."' type='image/x-icon' />
    <title>eyeOS Install ".OSVERSION."</title>
  </head>
  <body>
    <div style='margin-left: 50px; margin-right: 50px; text-align: center'>
      <div class='titoltaronja'>eyeOS ".OSVERSION." Installation Script</div>";
   if (!empty ($msg))      
      echo "
      <br /><h2 style='color:red'>$msg</h2><br />";

   foreach (array (USRDIR, HOMEDIR, dirname(SYSINFO) . '/') as $d) {
      if (!file_exists ($d)) {
         @mkdir ($d);
         @chmod ($d, 0777);
	 }
      if (is_dir($d)) {
         @chmod ($d, 0777);	      
         if (!is_writable ($d))
   	    $setwritable[] = $d;
      }
      else
         $create[] = $d;
   }

   if (@count ($create) || @count ($setwritable)) {
      echo "
      <div align='center'>";
      if (@count ($create)) 
         echo "
        <span style='color:red;'>
          "._L('Please create the following diretories manually setting their permissions to 0777 :')."
	  <br />
	  <ul>
	    <li>". implode ('</li><li>', $create)."</li>
          </ul>
        </span>";
      if (@count ($setwritable))
         echo "
        <span style='color:red;'>
          "._L('Please set permissions of 0777 on the following directories : ')."
	  <br />
	  <ul>
	    <li>". implode ('</li><li>', $setwritable)."</li>
          </ul>
        </span>";
      
      echo "
        "._L("Then user your browser reload button to retry the installation")."
      </div>";
   } else {
 
      if (!empty ($sysvars)) {
         echo "   
       <div align='center' id='Security_params' style='display:none'>
	 <button onclick='
	   document.getElementById(\"Security_params\").style.display=\"none\";
	   document.getElementById(\"Install_params\").style.display=\"block\";' >
	   Back to Installation parameters
	 </button>
	 <br /><br />";
	 
	 if (!is_writeable ('sysdefs.php'))
	    echo "
         <p style='color:red'>The system definition file sysdefs.php is not writeable. In order to 
	 change these parameters you wil need to change the permissions on that file
	 to allow PHP to write to it. You may reset the permissions after installation completes</p>
	 ";
	 
	 echo "
         <form action='index.php'>
	   <input type='hidden' name='mode' value='setsecurity' />
	   <input type='hidden' name='id' value='$md5sysdefs' /> 
           <table width='80%' align='center'>";
      
         foreach ($sysvars as $v) {
	    $v = strtoupper (trim ($v));
 	    if (preg_match ("!define\s*\\('$v',\s*'(.*?)'.*?//\s*(.*)?\$!m", $sysdefs, $svar))  
            echo "
           <tr>
	     <td>$v</td>
	     <td><input type='text' name='$v' value='${svar[1]}' /></td>
	     <td>${svar[2]}</td>
	   </tr>";	 
         }
      
	 if (is_writeable ('sysdefs.php'))
            echo "
	   <br />
	   <tr>
	     <td colspan='3' align='right'> 
	       <br />
	       <button type='submit'>"._L('Set security values')."</button>
             </td>
	   </tr>";
	   
	 echo "  
           </table>
	 </form>
       </div>";
      }

      echo "	
        <div align='center' id='Install_params' style='display:block'>
        <p>".
          _L('Welcome to eyeOS installation script.')."<br/>". 
          _L('Before using your new eyeOS system, you need specify some system parameters.')."<br/>".
          _L('The install process will be completed in a minute!')."<br/>
        </p>
        <br/>";
	
      if (!empty ($sysvars)) 	
      	 echo "
	  <button onclick='
	    document.getElementById(\"Security_params\").style.display=\"block\";
	    document.getElementById(\"Install_params\").style.display=\"none\";' >
	    Advanced security parameters
	  </button>
	 <br /> <br />";
      echo "	 
      <form 
         action='index.php' 
         method='post'>
	<input type=hidden name='mode' value='installing'> 
          <table border='0' width='80%'>
            <tr>
	      <td>"._L('Specify a name for your new eyeOS system :')."</td>
	      <td>
	        <input 
		   type='text' 
		   name='systemname' 
		   size='30' 
		   maxlenght='30' 
		   value='".(empty ($_REQUEST['systemname']) ? 'eyeOS' : $_REQUEST['systemname'])."' />
	      </td>
            </tr>
            <tr>
	      <td>"._L('Choose the default language :')."</td>
	      <td>
	        <select name='langselected'>";
      preg_match_all ("!^'(.+?)'\s*\=\>\s*array\s*\(!m", file_get_contents (SYSDIR.LANGFILE), $langs);
      foreach ($langs[1] as $l)
         echo "<option".($l == @$_REQUEST['langselected'] ? ' selected' : '').">$l</option>";
      echo "		  
	        </select>
	      </td>
            </tr>
            <tr>
	      <td>"._L('Root user email address (see note below) :')."</td>
	      <td>
	        <input type='input' name='email' size='30' maxlenght='30' />
	      </td>
            </tr>
            <tr>
	      <td>"._L('Specify the password for user \'%0\', the root user :', ROOTUSR)."</td>
	      <td>
	        <input type='password' name='rpw1' size='30' maxlenght='30' />
	      </td>
            </tr>
            <tr>
	      <td>"._L('Retype root user password :')."</td>
	      <td>
	        <input type='password' name='rpw2' size='30' maxlenght='30' />
	      </td>
            </tr>
            <tr>
              <td colspan='2' align='right'>
	        <br />
	        <button type='submit'>"._L('Install eyeOS >>')."</button>
	      </td>
            </tr>
          </table>
        </form>
        <p> Email address is needed only if you intend to enable email 
          validation of user created accounts. <br/>This feature is enabled if
          the root user email address is validated and <br/>if the file 
          validate.txt is present in the login directory. </p>
      </div>
    </div>
  </body>
</html>";
   }
   exit;
?>

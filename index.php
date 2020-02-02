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

  if (!defined ('OSVERSION')) 
    include_once 'sysdefs.php';  // if not autoprepended
    
  if (!is_file (USRDIR.ROOTUSR.'/'.USRINFO) || !is_file (SYSINFO)) {
    @session_destroy ();
    include SYSDIR.'install.php';
    exit ;
  }

  $logon_err = $logon_msg = '';
  
  if (!isset ($_SESSION['sysinfo']) || isset ($_REQUEST['exit']) || 
      ($_SESSION['remote_addr'] != $_SERVER['REMOTE_ADDR'])) {
    @session_destroy ();
    session_start ();

    if (false === $_SESSION['sysinfo'] = parse_info (SYSINFO, false))
      createXML (SYSINFO, 'eyeOS', $_SESSION['sysinfo'] = array (
	      'lang' => DEFAULTLANG,
	      'os' => 'eyeOS',
	      'ver' => OSVERSION )); 	      

    $_SESSION['sysinfo'] = array_merge ( array (
      'lang' => DEFAULTLANG,
	    'os' => 'eyeOS',
	    'ver' => OSVERSION,
      'hostname' => 'eyeOS',
	    'errors' => 0 ), $_SESSION['sysinfo']);
      
    $_SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];    
  }

  $_SESSION['lang'] = empty ($_REQUEST['newlang']) ? 
    $_SESSION['sysinfo']['lang'] : $_REQUEST['newlang'];
  include SYSDIR.LANGFILE;
  include CONFIG;

  if (empty ($_SESSION['usr']) || empty ($_SESSION['usrinfo'])) {
    if (!empty ($_REQUEST['rax'])) {
      $raxkey = explode ('.', $_REQUEST['rax']);
      $raxfn = basename ($raxkey[1]);
      $usr = basename ($raxkey[0]);
      $raxkey = md5 ($raxkey[2]);
      
      if (($_SESSION['usrinfo']['raxkey'] == $raxkey) && 
          (false !== ($_SESSION['usrinfo'] = 
          parse_info (USRDIR."$usr/rax/$raxfn.xml")))) {
        $_SESSION['usr'] = $usr;
      } else {
        session_destroy ();
        exit;
      }
    }
    
    elseif (!empty ($_REQUEST['newuser'])) {
      $usr = basename ($_REQUEST['newuser']);
      if (!empty ($_REQUEST['validate'])) {
        if ((false !== ($usrinfo = parse_info (USRDIR."$usr/".USRINFO))) && 
            (@$usrinfo['validate'] == $_REQUEST['validate'])) {
          parse_update (USRDIR."$usr/".USRINFO, 'validate', null); 
          $logon_msg = _L("Account validated, please login");
        } else
          $logon_err = _L("Account validation error");
      }
    
      elseif ((CREATE_ACCOUNTS == 'yes') && !empty ($_POST['newpwd']) && !empty ($_POST['newmail']) &&
          strlen ($_POST['newuser']) <= 50 && strlen($_POST['newpwd']) <= 50 &&
          strlen($_POST['newmail']) <= 200 && 
          $_POST['reqkey'] == $_SESSION['reqkey'])
        while (1) {    
          if (!preg_match ("/^[a-z0-9]+$/i", $_POST['newuser'])) {
            $logon_err = _L("User name may contain only letters or numbers");
            break;
          }

          if (is_dir ($usrdir = USRDIR . "$usr/")) {  // Should we give out this info - how not to ?? 
            $logon_err = _L("Username %0 unavailable", $_POST['newuser']); 
	          break;
          }

          mkdir ($usrdir, 0777);
          createXML ($usrdir . USRINFO, 'eyeOSuser', $_SESSION['usrinfo'] = array (
	          'lang' => $_SESSION['lang'],
	          'pwd' => md5 ($_POST['newpwd']),
	          'real' => $usr,
	          'usr' => 'usr',
	          'wllp' => SYSDIR."themes/default/eyeos.jpg",
	          'theme' => 'default',
            'email' => $_POST['newmail'],
            'apps' => 'apps/eyeHome.eyeapp,apps/eyeEdit.eyeapp,apps/eyeCalendar.eyeapp,apps/eyePhones.eyeapp,apps/eyeCalc.eyeapp,apps/eyeMessages.eyeapp,apps/eyeBoard.eyeapp,apps/eyeNav.eyeapp,apps/eyeOptions.eyeapp,apps/eyeInfo.eyeapp,apps/eyeApps.eyeapp'
           )); 
        
          if ((file_exists ($fp = dirname (SYSINFO).'/infousers.txt') || 
               file_exists ($fp = 'infousers.txt')) && 
               ($fp = fopen_exclusive ($fp, 'r+'))) {    
            $counter = trim (fread ($fp, 50));
            rewind ($fp);
            fwrite ($fp, ++$counter);
            fclose ($fp);
          }
        
          $rootEmail = parse_info (USRDIR.ROOTUSR.'/'.USRINFO);
          $rootEmail =  ($rootEmail && empty ($rootEmail['validate']) &&
            defined ('CREATE_ACCOUNTS') && (CREATE_ACCOUNTS == 'yes') &&
            file_exists ($fn = 'login/validate.txt')) ? $rootEmail['email'] : '';
          
          if ($rootEmail) {
            $key = sprintf ("%X%08X", rand(), time());  
            if (mail ($_POST['newmail'], $_SESSION['sysinfo']['hostname'] . ' ' . _L('new account validation'),
              str_replace (
                array (
                  '&host;',
                  '&validate;', 
                  '&usr;', 
                  '&ipaddr;',
                  '&eyeos;' ), 
                array (
                  $_SESSION['sysinfo']['hostname'],
                  'http://'.$_SERVER['SERVER_NAME'].'/'.trim($_SERVER['REQUEST_URI'], '/')."?validate=$key&newuser=$usr",
                  $usr, 
                  $_SERVER['REMOTE_ADDR'],
                  $_SESSION['sysinfo']['hostname']), file_get_contents ($fn)),
                  "From: $rootEmail\r\nReply-To: $rootEmail\r\nX-Mailer: PHP/" . phpversion())) {
              if (!parse_update ($usrdir . USRINFO, 'validate', $key))
                log_error ("parse_update ${usrdir}USRINFO failed");
              
              if ($fp = fopen_exclusive (dirname (SYSINFO).'/vaccounts.txt', 'a+')) {
                fwrite ($fp, time().":$usr\n");
                fclose ($fp);
              }
              $logon_msg = _L('Your account validation has been e-mailed');
              $usr = '';               
            } else {
              unlink ($usrdir . USRINFO);
              rmdir ($usrdir);
              $logon_err = 'Sorry, cannot email account validation';
            }
          }
          else
            $_SESSION['usr'] = $usr;
          break;
        }
    }
    
    elseif ($usr = basename (@$_REQUEST['usr'])) {
      // SECURITY check: $usr is used as a directory name => users input passed through basename 
      if ((false !== ($_SESSION['usrinfo'] = parse_info (USRDIR."$usr/".USRINFO)))
        && ((@$_SESSION['usrinfo']['pwd'] == md5 ($_REQUEST['pwd'])) || (defined ('GUESTPWD') && GUESTPWD && @$_SESSION['usrinfo']['pwd'] == GUESTPWD))) {
        if (($usr == ROOTUSR) || empty ($_SESSION['usrinfo']['validate']))      
          $_SESSION['usr'] = $usr;
        else
          $logon_msg = _L ("Account waiting validation");
      } else	 
        $logon_err = _L ("Your username/password cannot be found");
    }
  
    elseif (empty ($_REQUEST['usr'])) {
      if (!isset ($_REQUEST['exit']))	      
        foreach (explode (',', @$_SESSION['sysinfo']['autologon']) as $autolog) {
          $autolog = @explode (':', $autolog);
          $usr = @trim ($autolog[0]);
          if (($_SERVER['REMOTE_ADDR'] == @trim ($autolog[1])) && (false !== ($_SESSION['usrinfo'] = parse_info (USRDIR."$usr/".USRINFO)))) {
            $_SESSION['usr'] = $usr;
            break;
          }
        }
    }
    else
      $logon_msg = _L("Please specify a username and password");
      
    if (!empty ($_SESSION['usr']) && !empty ($_SESSION['usrinfo'])) { //login successful
      
      $usr = $_SESSION['usr'];
      
      $_SESSION['Toffset'] = empty ($_REQUEST['Toffset']) ? 0 : 
        ((round(($_REQUEST['Toffset'] - eval ('return '.date ('H * 60 + i').';'))) / 5) * 300);
      if (empty ($_SESSION['usrinfo']['raxkey'])) { 
        $a = @$_SESSION['usrinfo']['autorun'] .';'. @$_SESSION['sysinfo']['autorun'] . ';' . @$_SESSION['usrinfo']['run_once'];

        if (empty ($_SESSION['usrinfo']['create'])) $_SESSION['usrinfo']['create'] = time (); 
        if (@date ("Ymd", $_SESSION['usrinfo']['lastlogin']) != date ("Ymd")) @$_SESSION['usrinfo']['logindays']++;
        if (! parse_update (USRDIR."$usr/".USRINFO, array (
          'create' => $_SESSION['usrinfo']['create'],
          'run_once' => null, 
          'logins' => @++$_SESSION['usrinfo']['logins'], 
          'logindays' => $_SESSION['usrinfo']['logindays'],
          'lastlogin' =>  $_SESSION['usrinfo']['lastlogin'] = ($ltime = time()) ))) 
          error_log ("$usr : user info parse error");
        $_SESSION['rax'] = false;
      } 
      
      else {
        $_SESSION['rax'] = true;  
        unset ($_SESSION['usrinfo']['raxkey']);
        $a = $_SESSION['usrinfo']['autorun'];
      }

      if (STATSDIR && (($fp = fopen_exclusive (STATSDIR . date('Y-m-d', $ltime) . '.php', 'a')))) {
        fwrite ($fp, '<?PHP //' . date ('H i s', $ltime) . " $usr ?>\n");
        fclose ($fp);
      } 

      $_SESSION['apps'] = array ();

      if (false !== strpos (strtolower ($_SERVER['HTTP_USER_AGENT']), 'msie'))
        $_SESSION['browser_ie'] = 1;
    }
  }
  
  if (!empty ($_SESSION['usr']) && !empty ($_SESSION['usrinfo'])) {
    
    if (empty ($_REQUEST['newlang']) && !empty ($_SESSION['usrinfo']['lang'])) {
	    $_SESSION['lang'] = $_SESSION['usrinfo']['lang'];
      include SYSDIR.LANGFILE;
    }

    if (!empty ($_SESSION['usrinfo']['wllp']) && file_exists($_SESSION['usrinfo']['wllp']))
      $_SESSION['fondoescollit'] = $_SESSION['usrinfo']['wllp'];

    if (DEBUG & 1) {
      $_SESSION['sysinfo']['errors'] = E_ALL;
      echo '<pre>'; print_r ($_SESSION['sysinfo']); echo '</pre>'; 
    }

    header('Location: desktop.php' . (@$a ? '?a='.urlencode($a) : ''));
    exit;
  }

/* No usr : pwd or authentication has failed, construct the login screen */

  if (file_exists ($fn = dirname (SYSINFO).'/infousers.txt') || file_exists ($fn = 'infousers.txt'))
    $usr_count = trim (file_get_contents ($fn));
    
    if ($logon_err) {
      $logon_msg = "<span style='color:red;'>$logon_err</span>";
      $usr = '';
    }
    
    $rootEmail = parse_info (USRDIR.ROOTUSR.'/'.USRINFO);
    $rootEmail =  ($rootEmail && empty ($rootEmail['validate']) &&
      defined ('CREATE_ACCOUNTS') && (CREATE_ACCOUNTS == 'yes') &&
      file_exists ('login/validate.txt')) ? @$rootEmail['email'] : '';
     
  include 'login/index.php';
?>

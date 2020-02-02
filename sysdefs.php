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
            
  foreach ($_REQUEST as $k => $v) {
    if (is_array($v)) { 
      foreach ($v as $vk => $vv)
        if (preg_match ('!(\<\s*(script|\?|\?php))|(\\.\\.(\\\\|/))|(&\\()<!i', $vv)) {
	        error_log ("Attack attempt : $k[$vk] '".htmlspecialchars($vv)."'");
	        unset ($_REQUEST[$k][$kv]);
        }
    }
    elseif (preg_match ('!(\<\s*(script|\?|\?php))|(\\.\\.(\\\\|/))|(&\\()<!i', $v)) {
	    error_log ("Attack attempt : $k '".htmlspecialchars($v)."'");
	    unset ($_REQUEST[$k]);
    }

    ##!! Caution when get_magic_quots_sybase is set only ' is escaped as ''
      
    if (get_magic_quotes_gpc ())
      $_REQUEST[$k] = str_replace (array ("\\\\", "\\'", "\\\""), array ("\\", "'", '"'), $v);
  }

// The following may be modified for security reasons, but ONLY before installation   
// they are modifiable from the advanced security panel in the installation script

//>>  SYSINFO, BBOARD, HOMEDIR, USRDIR, ROOTUSR, USRINFO, MSGDIR, NOTEDIR, SYSAPPS, STATSDIR, CREATE_ACCOUNTS, SHOW_USER_LISTS, USER_QUOTA

define ('SYSINFO', 'etc/system.php');  // System parameter file name
define ('BBOARD', 'etc/taulell.php');  // System bulletin board file

define ('HOMEDIR', 'home/');		  // Directory for all user files	
define ('USRDIR', 'usr/');		  // Directory for user control files

define ('ETCDIR', dirname (SYSINFO).'/');

define ('ROOTUSR', 'root');		  // Name of root user
define ('USRINFO', 'usrinfo.php');	  // Name of file for user parameters	

define ('MSGDIR', 'Inbox/');      // Directory name for messages
define ('NOTEDIR', 'eyeEdit/');        // Directory name for notes
define ('SYSAPPS',  'etc/apps/');	// Directory name for system-wide new apps installed 
define ('THEMESDIR',  'etc/themes/');

define ('STATSDIR', '/etc/stats');    // Directory for statistics, blank to disable, suggest etc/stats/ 
define ('CREATE_ACCOUNTS', 'no');     // Set to 'Yes' to allow auto accunt creation
define ('SHOW_USER_LISTS', 'yes');     // Show user lists instead of empty text fields

// The following may be changed even after system installation

   define ('APP_INSTALLATION', 2);		// Controls app installation, 
                                                //    value 0 => no posibility
						//    value 1 => root user only
						//    value 2 => any user
						
// Do not make changes after here without due consideration of the consequences

   define ('OSVERSION', '0.9.0-4');

   define ('SYSDIR', 'system/');
   define ('CSSDIR', SYSDIR.'css/');
   define ('SCRIPTDIR', SYSDIR.'scripts/');
   define ('GFXDIR', SYSDIR.'gfx/');
   define ('CONFIG', SYSDIR.'config.php');

   define ('USRAPPS', '~usr/apps/');
   define ('APPDIRS', USRAPPS.','.SYSAPPS.',apps/');	// Comma separated list of app dirs
   define ('APPMANAGER', 'apps/eyeApps.eyeapp'); 
   define ('APPSKIN', 'gfx/');
   
   define ('DEFAULTLANG', 'english');
   define ('LANGFILE', 'lang.php');

   define ('USER_QUOTA', '');   //Max quota per user, in MB , blank to disable
   define ('MAXICONS', 15);    //Number of icons allowed in user dock, suggest 15 (for a 1024x768 resolution)

   define ('APP_CODE', 'aplic.php');        // Application code file
   define ('APP_ICON', 'ico_c.png');	    // Application icon (not running)
   define ('APP_RUNICON', 'ico_b.png');	    // Application icon when running
   define ('APP_INFO', 'propietats.xml');   // Application info file
   
// For the windowing system,
   # default heights and widths
   define ('WINDOW_HEIGHT', 200);
   define ('WINDOW_WIDTH', 350);
   # Non fixed window position start and inc 
   define ('WINDOW_START', 60);
   define ('WINDOW_INC', 40);
   
   define ('SYSFMT_DATE', 'd/m/Y');
   define ('SYSFMT_TIME', 'H:i');
     
   define ('MACRO_OPEN', '/!');
   
   define ('MSGSRCS', '~usr/'.MSGDIR);

   require_once SYSDIR.'funcions.php';

   session_start ();
   
  if (false !== strpos (strtoupper (OSVERSION), 'X')) { 
    @define ('DEBUG', $_SESSION['debug'] = !empty ($_REQUEST['debug']) ? 
      $_REQUEST['debug'] : (!empty ($_SESSION['sysinfo']['debug']) ? 
      $_SESSION['sysinfo']['debug'] : @$_SESSION['debug']));
    error_reporting (E_ALL);
  } else {
    @define ('DEBUG', 0);
    error_reporting (0);
  }
  
  header ('Content-Type: text/html; charset=UTF-8'); //if not set
  ini_set('session.cache_expire',     2000000);
  ini_set('session.cookie_lifetime',  2000000);
  ini_set('session.gc_maxlifetime',   200000);
?>

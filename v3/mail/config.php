<?php
/* ------------------------------------------------------------------------- */
/* Configure tool for PHlyMail 1.2.0+                                        */
/* (c) 2003-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* v0.1.0mod2                                                                */
/* ------------------------------------------------------------------------- */
// Save the user from trying to run me under an ancient PHP
if (intval(str_replace('.', '', phpversion())) < 410) {
    die('PHlyMail works with PHP 4.1.0 and above only');
}

// Do not use cookies for session management
@ini_set('session.use_cookies', 'Off');
@ini_set('url_rewriter.tags', '');
// Implizites Starten oder Wiederaufnehmen einer Session
session_start();
// Lade benötigte Files
require(dirname(__FILE__).'/choices.inc.php');

// Allow config/ to be renamed or moved to another place
if (!isset($WP_core['config_path'])) $WP_core['config_path'] = 'config';
$WP_core['config_path'] = preg_replace('!/$!', '', $WP_core['config_path']);
define('CONFIGPATH', $WP_core['config_path']);

include(CONFIGPATH.'/lib/init.script.php');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if ('logout' == $action) {
    include (CONFIGPATH.'/lib/logout.php');
}

// Greifen alle Setup-Module drauf zu
$link_base = $_SERVER['PHP_SELF'].'?'.give_passthrough(1).'&action=';

if (!isset($_SESSION['WPs_uid']) || !isset($_SESSION['WPs_username']) || !isset($_SESSION['WPs_adminsession'])) {
    include(CONFIGPATH.'/mod.auth.php');
} else {
    include(CONFIGPATH.'/lib/menu.php');
}

switch ($action) {
case '':
case 'home':
case 'menu':         include(CONFIGPATH.'/setup.home.php');         break;
case 'diag':         include(CONFIGPATH.'/setup.diag.php');         break;
case 'advanced':     include(CONFIGPATH.'/setup.advanced.php');     break;
case 'general':      include(CONFIGPATH.'/setup.general.php');      break;
case 'security':     include(CONFIGPATH.'/setup.security.php');     break;
case 'AU':           include(CONFIGPATH.'/setup.au.php');           break;
case 'users':        include(CONFIGPATH.'/setup.users.php');        break;
case 'plugins':      include(CONFIGPATH.'/setup.plugins.php');      break;
case 'regnow':       include(CONFIGPATH.'/setup.regnow.php');       break;
case 'driver':       include(CONFIGPATH.'/setup.driver.php');       break;
case 'config':       include(CONFIGPATH.'/setup.config.php');       break;
case 'config.users': include(CONFIGPATH.'/setup.config.users.php'); break;
}

//
// Output the skin
//
// Use gzip
if (isset($WP_core['gzip_config']) && $WP_core['gzip_config']) {
    ob_start('ob_gzhandler');
}
if ((!isset($pure) || $pure != 'true') && $action != 'saug') {
    require(CONFIGPATH.'/lib/skins.php');
} elseif (isset($tpl)) {
    if (is_object($tpl)) $tpl->display();
    else echo $tpl;
}
?>
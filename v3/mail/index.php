<?php
/* ------------------------------------------------------------------------- */
/* PHlyMail, a PHP4 based POP3 to Web client                                 */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* v2.1.5                                                                    */
/* ------------------------------------------------------------------------- */
// Save the user from trying to run me under an ancient PHP
if (intval(str_replace('.', '', phpversion())) < 410) {
    die('Unfortunately PHlyMail works with PHP 4.1.0 and above only');
}

// Do not use cookies for session management
@ini_set('session.use_cookies', 'Off');
@ini_set('url_rewriter.tags', '');
// Implizites Starten oder Wiederaufnehmen einer Session
session_start();
// Lade benötigte Files
require(dirname(__FILE__).'/choices.inc.php');
require($WP_core['page_path'].'/lib/init.script.php');

if (!isset($action) || !$action) $action = 'inbox';
if (!isset($mail))   $mail = FALSE;

// React on given state
if ('logout' == $action) {
    include ($WP_core['page_path'].'/lib/logout.php');
    header ('Location: '.$_SERVER['PHP_SELF'].'?'.give_passthrough(1).'&WP_return='.$WP_return);
    exit;
}
if (!isset($_SESSION['WPs_uid']) || !isset($_SESSION['WPs_username'])) {
    include($WP_core['page_path'].'/mod.auth.php');
}
if ('plugged' == $action)   $tpl = &$WP_core['plug_output'];

if ('markread_set' == $action || 'markread_unset' == $action) {
    $mode = $action;
    $action= 'inbox';
}

if ('setup' == $action)     include($WP_core['page_path'].'/mod.setup.php');
if ('inbox' == $action)     include($WP_core['page_path'].'/mod.inbox.php');
if ('kill' == $action)      include($WP_core['page_path'].'/mod.kill.php');
if ('bounce' == $action)    include($WP_core['page_path'].'/mod.bounce.php');
if ('send' == $action)      include($WP_core['page_path'].'/mod.send.php');
if ('read' == $action)      include($WP_core['page_path'].'/mod.read.php');
if ('saug' == $action)      include($WP_core['page_path'].'/mod.suck.php');
//
// Output the skin
//
// Use gzip
if (isset($WP_core['gzip_frontend']) && $WP_core['gzip_frontend']) {
    ob_start('ob_gzhandler');
}
if (!isset($pure) && $action != 'saug') {
    require($WP_core['page_path'].'/lib/skins.php');
} elseif (isset($tpl)) {
    if (is_object($tpl)) $tpl->display(); else echo $tpl;
}
?>
<?php
/* ------------------------------------------------------------------------- */
/* lib/clientswitch.php -> Autoerkennung des Client-Typs (HTML,i-mode,...)   */
/* (c) 2002-2003 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail LE Common Path                                                   */
/* v0.0.5                                                                    */
/* ------------------------------------------------------------------------- */

if (isset($client) && ($client)) {
    $client = strtolower($client);
} else {
    $client = strtolower($_SERVER['QUERY_STRING']);
}

switch ($client) {
case 'pda':
    $_SESSION['WPs_tpl_scheme'] = 'cHTML';
    error('Client: Switching to cHTML');
    break;
case 'imode':
case 'i-mode':
    $_SESSION['WPs_tpl_scheme'] = 'iHTML';
    error('Client: Switching to iHTML');
    break;
case 'wap':
case 'wml':
    $_SESSION['WPs_tpl_scheme'] = 'WML';
    error('Client: Switching to WML');
    break;
case 'html':
case 'desktop':
    $_SESSION['WPs_tpl_scheme'] = 'HTML';
    error('Client: Switching to HTML');
    break;
}

if (isset($_SESSION['WPs_tpl_scheme']) && $_SESSION['WPs_tpl_scheme']) {
    $WP_core['tpl_scheme'] = &$_SESSION['WPs_tpl_scheme'];
} else {
    $_SESSION['WPs_tpl_scheme'] = 'HTML';
    $WP_core['tpl_scheme'] = &$_SESSION['WPs_tpl_scheme'];
}
?>
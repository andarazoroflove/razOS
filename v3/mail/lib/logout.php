<?php
/* ------------------------------------------------------------------------- */
/* lib/logout.php -> Destroying a PHlyMail session                           */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.8                                                                    */
/* ------------------------------------------------------------------------- */
if (isset($_SESSION['WPs_tpl_scheme'])) {
    switch ($_SESSION['WPs_tpl_scheme']) {
    case 'cHTML': $urladd = 'PDA';   break;
    case 'iHTML': $urladd = 'iMode'; break;
    case 'WML':   $urladd = 'WAP';   break;
    default:      $urladd = '';      break;
    }
} else {
    $urladd = '';
}
if (isset($_SESSION['WPs_uid'])) $DB->set_logouttime($_SESSION['WPs_uid']);
$_SESSION = array();

header('Location: '.$_SERVER['PHP_SELF'].'?'.$urladd);
exit;

?>
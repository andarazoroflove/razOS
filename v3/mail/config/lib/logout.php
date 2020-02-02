<?php
/* ------------------------------------------------------------------------- */
/* lib/logout.php -> PHlyMail 1.2.0+ "Logout" (Delete session file(s)        */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.2                                                                    */
/* ------------------------------------------------------------------------- */

if (isset($_SESSION['WPs_uid'])) $DB->set_admlogouttime($_SESSION['WPs_uid']);
$_SESSION = array();

if (isset($_REQUEST['redir']) && $_REQUEST['redir'] == 'index') {
    header('Location: index.php?action=login');
    exit;
}

header('Location: '.$_SERVER['PHP_SELF']);
exit;

?>
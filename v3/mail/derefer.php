<?php
/* ------------------------------------------------------------------------- */
/* derefer.php - PHlyMail 2.1.0+  Hide session from outside referers         */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.3                                                                    */
/* ------------------------------------------------------------------------- */
if (isset($_REQUEST[session_name()])) {
    header('Location: '.$_SERVER['PHP_SELF'].'?go='.($_REQUEST['go']));
    exit;
}

if (!isset($_REQUEST['go'])) exit;
$go = $_REQUEST['go'];
if (!preg_match('!^(http://|https://|ftp://)!', $go)) $go = 'http://'.$go;

header('Location: '.$go);

?>
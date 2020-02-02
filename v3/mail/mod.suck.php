<?php
/* ------------------------------------------------------------------------- */
/* mod.suck.php -> Extrahieren eines Attachments (zum Download)              */
/* (c) 2001-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v1.4.1                                                                    */
/* ------------------------------------------------------------------------- */
include_once($WP_core['page_path'].'/lib/message.decode.php');

if (isset($mail)) {
    include_once($WP_core['page_path'].'/lib/pop3.inc.php');
    $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
    if ($POP->check_connected() == 'unconnected') {
        $WP_exit = true;
        $error = $POP->get_last_error();
    } else {
        if (!$POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop'])) {
            $WP_exit = true;
            $error = $POP->get_last_error();
        }
    }
    if (isset($WP_exit)) {
        $tpl = new fxl_template($WP_core['skin_path'].'/templates/all.general.tpl');
        $tpl->assign('output', (isset($error)) ? $error : $WP_msg['noconnect'].' '.$_SESSION['WPs_popverbose'].'<br />');
        return;
    }
    $POP->retrieve($mail);
    explode_mime_body($POP, $attach, 0, 0);
}
?>
<?php
/* ------------------------------------------------------------------------- */
/* mod.auth.php -> PHlyMail 1.2.0+ authentication module                     */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.3.5mod1                                                                */
/* ------------------------------------------------------------------------- */
$WPloggedin = 0;

if (isset($WPuser) && isset($WPpass)) {
    $still_blocked = 0;
    $maintained = 0;
    $unusable = 0;
    list ($uid, $realpass) = $DB->authenticate($WPuser);

    if (isset($_REQUEST['secure']) && $_REQUEST['secure']) {
        $soll = md5($realpass.$_SESSION['otp']);
        if ($soll != $_REQUEST['secure']) {
            $uid = FALSE;
        }
        unset($_SESSION['otp']);
    } elseif ($WPpass) {
        if (md5($WPpass) != $realpass) {
            $uid = FALSE;
        }
    } else {
        $uid = FALSE;
    }

    if ($uid != FALSE) {
        $_SESSION['WPs_uid'] = $uid;
        $_SESSION['WPs_username'] = $WPuser;
        $WPloggedin = 1;
        header('Location: '.$_SERVER['PHP_SELF'].'?'.give_passthrough(1));
        exit();
    } else {
        $error = $WP_msg['wrongauth'];
        sleep($WP_core['waitonfail']);
    }
}

if ($WPloggedin != 1) {
    $_SESSION['otp'] = md5($_SERVER['REMOTE_ADDR']).time().getmypid();
    $action = 'auth';
    $WP_once['load_tpl_auth'] = 'do.it!';
    $tpl = new FXL_Template($WP_core['skin_path'].'/templates/auth.login.tpl');
    $tpl->assign(array
            ('PHP_SELF' => $_SERVER['PHP_SELF'].'?'.give_passthrough()
            ,'msg_authenticate' => $WP_msg['authenticate']
            ,'msg_popuser' => $WP_msg['popuser'], 'msg_poppass' => $WP_msg['poppass']
            ,'msg_login' => $WP_msg['login'], 'otp' => $_SESSION['otp']
            ));
    if (isset($error) && $error) {
        $tpl->assign_Block('error');
        $tpl->assign('error', $error);
    }
}
?>
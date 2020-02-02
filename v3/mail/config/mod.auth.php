<?php
/* ------------------------------------------------------------------------- */
/* mod.auth.php -> PHlyMail 1.2.0+ Config authentication module              */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Configuration tool                                               */
/* v0.3.7mod1                                                                */
/* ------------------------------------------------------------------------- */
$WPloggedin = 0;
$still_blocked = 0;

if (isset($WPuser) && isset($WPpass)) {
    list($uid, $realpass) = $DB->adm_auth($WPuser);
    // MOD
    if (isset($_REQUEST['secure']) && $_REQUEST['secure']) {
        $soll = md5($realpass.$_SESSION['otp']);
        if ($soll != $_REQUEST['secure']) {
            if ($still_blocked != 1) $DB->set_admfail($uid);
            $uid = FALSE;
        }
        unset($_SESSION['otp']);
    } elseif ($WPpass) {
        if (md5($WPpass) != $realpass) {
            if ($still_blocked != 1) $DB->set_admfail($uid);
            $uid = FALSE;
        }
    } else {
        $uid = FALSE;
    }
    if ($still_blocked == 1) $error = $WP_msg['stillblocked'];
    elseif ($uid != FALSE) {
        $_SESSION['WPs_uid'] = $uid;
        $_SESSION['WPs_username'] = $WPuser;
        $_SESSION['WPs_adminsession'] = 'true';
        $WPloggedin = 1;
        $DB->set_admlogintime($_SESSION['WPs_uid']);
        $PHM = $DB->get_admdata($uid);
        unset($PHM['password']);
        list($read, $write) = unserialize(base64_decode($PHM['permissions']));
        // Permissions of that administrative user
        $_SESSION['WPs_perm_read']  = (isset($read) && is_array($read))   ? array_flip($read)  : array();
        $_SESSION['WPs_perm_write'] = (isset($write) && is_array($write)) ? array_flip($write) : array();
        // Might be a super admin
        $_SESSION['WPs_superroot'] = TRUE; // MOD /
        header('Location: '.$_SERVER['PHP_SELF'].'?'.give_passthrough(1));
        exit();
    } else {
        $error = $WP_msg['wrongauth'];
        sleep($WP_core['waitonfail']);
    }
}

if ($WPloggedin != 1) {
    $action = 'auth';
    $WP_once['load_tpl_auth'] = 'do.it!';
    $_SESSION['otp'] = md5($_SERVER['REMOTE_ADDR']).time().getmypid();

    $tpl = new FXL_Template(CONFIGPATH.'/templates/auth.login.tpl');
    $tpl->assign(array('PHP_SELF' => $_SERVER['PHP_SELF'].'?'.give_passthrough()
                      ,'msg_authenticate' => $WP_msg['authenticate']
                      ,'msg_popuser' => $WP_msg['popuser']
                      ,'msg_poppass' => $WP_msg['poppass']
                      ,'msg_login' => $WP_msg['login']
                      ,'otp' => $_SESSION['otp']
                      ));
    if (isset($error) && $error != '') {
        $tpl_e = $tpl->getBlock('error');
        $tpl_e->assign('error', $error);
        $tpl->assign('error', $tpl_e);
    }
}
?>
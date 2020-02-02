<?php
/* ------------------------------------------------------------------------- */
/* mod.bounce.php -> bounce mail list given (from inbox)                     */
/* (c) 2003 blue birdy, Berlin (http://bluebirdy.de)                         */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.9                                                                    */
/* ------------------------------------------------------------------------- */

// 1. Step: Pump all UIDLs in to Session
// 2. Step: Pull UIDL by UIDL from Session an push maildata into mod.send.php
// 3. Step: If user requested to delete the original, do that before returning
//          control to mod.inbox.php

// Is sending allowed after all?
$useraccounts = $DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']);
if (empty($useraccounts)) {
    $action = isset($_SESSION['origaction']) ? $_SESSION['origaction'] : 'inbox';
    header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action.'&mail='.$mail.'&'.
           give_passthrough(1).'&WP_return='.$WP_msg['notsend']);
    exit;
}

// Bounce all mails? No no...
if ('kill_all' == $kill_mode) {
     $WP_return = $WP_msg['bounce_disabled'];
     $action = 'inbox';
     return;
}

if (!isset($_SESSION['origaction'])) $_SESSION['origaction'] = isset($oldaction) ? $oldaction : 'inbox';

if (isset($break_bounce) && 'reset' == $break_bounce) {
    $action = isset($_SESSION['origaction']) ? $_SESSION['origaction'] : 'inbox';
    unset($_SESSION['bouncelist'], $_SESSION['bounceddeletelist'], $_SESSION['bounce_lastknown_uidl']
         ,$_SESSION['dootherslist'], $_SESSION['origaction']);
    header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action.'&'.give_passthrough(1));
    exit;
}

// Step 1
if (!isset($_SESSION['bouncelist'])) {
    // Workaround to catch a single mail to be deleted after bouncing it
    if (isset($deleorig) && $deleorig && isset($_SESSION['singlebouncer'])) {
        $_SESSION['bounceddeletelist'] = $_SESSION['singlebouncer'];
        unset($_SESSION['singlebouncer']);
    }
    // Step 3
    if (isset($_SESSION['bounceddeletelist'])) {
        $action = isset($_SESSION['origaction']) ? $_SESSION['origaction'] : 'inbox';
        $link = $_SERVER['PHP_SELF'].'?action=kill&oldaction='.$action;
        foreach ($_SESSION['bounceddeletelist'] as $kmail => $uidl) {
            $link .= '&kmail['.$kmail.']='.$kmail.'&uidl['.$kmail.']='.$uidl;
        }
        $link .= '&answergiven=1&yesiwantto=1&'.give_passthrough(1);
        unset($_SESSION['bouncelist'], $_SESSION['bounceddeletelist'], $_SESSION['bounce_lastknown_uidl']
             ,$_SESSION['dootherslist'], $_SESSION['origaction']);
        header('Location: '.$link);
        exit;
    } elseif ('kill_page' == $kill_mode) {
        foreach ($uidl as $key => $value) {
            $_SESSION['bouncelist'][$key] = $value;
        }
        unset($uidl);
    } elseif (isset($kmail) && sizeof($kmail) != 0) {
        if (sizeof($kmail) == 1) {
            foreach ($kmail as $value) break;
            $_SESSION['singlebouncer'][$value] = $_SESSION['bouncelist'][$value] = $uidl[$value];
        } else {
            foreach ($kmail as $value) {
               $_SESSION['bouncelist'][$value] = $uidl[$value];
            }
        }
        unset($kmail);
    }
    if (isset($_SESSION['bouncelist'])) {
        $action = 'bounce';
    } else {
        $action = isset($_SESSION['origaction']) ? $_SESSION['origaction'] : 'inbox';
    }
    header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action.'&mail='.$mail.'&'.give_passthrough(1));
    exit;
// Step 2
} else {
    if (isset($deleorig) && $deleorig) {
        $_SESSION['bounceddeletelist'][$mail] = $_SESSION['bounce_lastknown_uidl'];
        unset($_SESSION['bounce_lastknown_uidl']);
    }
    if (isset($doothers) && $doothers) {
        foreach ($_SESSION['bouncelist'] as $kmail => $uidl) {
            $_SESSION['dootherslist'][$kmail] = $_SESSION['bounceto'];
            if (isset($deleorig) && $deleorig) {
                $_SESSION['bounceddeletelist'][$kmail] = $_SESSION['bouncelist'][$kmail];
            }
        }
    }
    foreach ($_SESSION['bouncelist'] as $mail => $uidl) break;
    unset($_SESSION['bouncelist'][$mail]);
    $_SESSION['bounce_lastknown_uidl'] = $uidl;
    if (empty($_SESSION['bouncelist'])) unset($_SESSION['bouncelist']);
    header('Location: '.$_SERVER['PHP_SELF'].'?action=send&mail='.$mail
          .'&WP_send[bounce]=1&WP_send[active_part]=-1&oldaction=bounce&uidl='.$uidl.'&'.give_passthrough(1));
    exit;
}

?>
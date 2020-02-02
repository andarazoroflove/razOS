<?php
/* ------------------------------------------------------------------------- */
/* mod.kill.php -> Angegebene Mails vom POP3-Server löschen                  */
/* (c) 2001-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.7.0mod1                                                                */
/* ------------------------------------------------------------------------- */

if (!isset($yesiwantto))  $yesiwantto = false;
if (!isset($answergiven)) $answergiven = false;
if (!isset($kill_mode))   $kill_mode = false;
if (!isset($kill_page))   $kill_page = false;
if (!isset($kill_all))    $kill_all = false;
if (!isset($kmail))       $mail = false;
$WP_return = false;

if (!$yesiwantto && !$answergiven && ($kmail || $kill_mode != 'kill_selection')) {
    $tpl = new FXL_Template($WP_core['skin_path'].'/templates/kill.general.tpl');
    $tpl->assign('yes', $WP_msg['yes']);
    $tpl->assign('no', $WP_msg['no']);
    $tpl->assign('PHP_SELF', $_SERVER['PHP_SELF']);
    $tpl_n = $tpl->getBlock('hidden');
    // Alle Mails löschen?
    if ('kill_page' == $kill_mode) {
        $tpl_n->assign(array('name' => 'answergiven', 'value' => 'yes'));
        $tpl->assign('hidden', $tpl_n); $tpl_n->clear();
        $tpl_n->assign(array('name' => 'kill_page', 'value' => 'true'));
        $tpl->assign('hidden', $tpl_n); $tpl_n->clear();
        $tpl_n->assign(array('name' => 'oldaction', 'value' => $oldaction));
        $tpl->assign('hidden', $tpl_n); $tpl_n->clear();
        $tpl_n->assign(array('name' => 'mail', 'value' => $mail));
        $tpl->assign('hidden', $tpl_n); $tpl_n->clear();
        foreach ($uidl as $key => $value) {
            $tpl_n->assign('name', 'uidl['.$key.']'); $tpl_n->assign('value', $value);
            $tpl->assign('hidden', $tpl_n);
            $tpl_n->clear();
        }
        foreach (give_passthrough(3) as $k => $v) {
            $tpl_n->assign('name', $k); $tpl_n->assign('value', $v);
            $tpl->assign('hidden', $tpl_n);
            $tpl_n->clear();
        }
        $anzahl = sizeof($uidl);
        if (1 < $anzahl) {
            $tpl->assign('kill_request', str_replace('$1', $anzahl, $WP_msg['killmore']));
        } else {
            $tpl->assign('kill_request', $WP_msg['killone']);
        }
    } elseif ('kill_all' == $kill_mode) {
        // Killing all mails wihtout knowing any of the UIDLs ... *evil*
        $anzahl = $inboxcount;
        $tpl_n = $tpl->getBlock('hidden');
        $tpl_n->assign('name', 'answergiven');
        $tpl_n->assign('value', 'yes');
        $tpl->assign('hidden', $tpl_n);
        $tpl_n->clear();

        $tpl_n->assign('name', 'kill_all');
        $tpl_n->assign('value', 'true');
        $tpl->assign('hidden', $tpl_n);
        $tpl_n->clear();

        $tpl_n->assign('name', 'oldaction');
        $tpl_n->assign('value', $oldaction);
        $tpl->assign('hidden', $tpl_n);
        $tpl_n->clear();

        $tpl_n->assign('name', 'mail');
        $tpl_n->assign('value', $mail);
        $tpl->assign('hidden', $tpl_n);
        $tpl_n->clear();
        $tpl_n->assign('name', 'inboxcount');
        $tpl_n->assign('value', $inboxcount);
        $tpl->assign('hidden', $tpl_n);
        $tpl_n->clear();

        $tpl->assign('kill_request', str_replace('$1', $anzahl, $WP_msg['killall']));
        foreach (give_passthrough(3) as $k => $v) {
            $tpl_n->assign('name', $k); $tpl_n->assign('value', $v);
            $tpl->assign('hidden', $tpl_n);
            $tpl_n->clear();
        }
    } else {
        // Wahre Schönheit kommt von innen
        $anzahl = sizeof($kmail);
        if (1 < $anzahl) {
            $tpl->assign('kill_request', str_replace('$1', $anzahl, $WP_msg['killmore']));
        } else {
            $tpl->assign('kill_request', $WP_msg['killone']);
        }
        // Bestätigungs-Strings zusammenbauen
        $tpl_n->assign('name', 'answergiven'); $tpl_n->assign('value', 'yes');      $tpl->assign('hidden', $tpl_n); $tpl_n->clear();
        $tpl_n->assign('name', 'oldaction');   $tpl_n->assign('value', $oldaction); $tpl->assign('hidden', $tpl_n); $tpl_n->clear();
        $tpl_n->assign('name', 'mail');        $tpl_n->assign('value', $mail);      $tpl->assign('hidden', $tpl_n); $tpl_n->clear();
        foreach ($kmail as $key => $value) {
            $tpl_n->assign('name', 'uidl['.$value.']');
            $tpl_n->assign('value', $uidl[$value]);
            $tpl->assign('hidden', $tpl_n);
            $tpl_n->clear();
            $tpl_n->assign('name', 'kmail[]');
            $tpl_n->assign('value', $value);
            $tpl->assign('hidden', $tpl_n);
            $tpl_n->clear();
        }
        foreach (give_passthrough(3) as $k => $v) {
            $tpl_n->assign('name', $k); $tpl_n->assign('value', $v);
            $tpl->assign('hidden', $tpl_n);
            $tpl_n->clear();
        }
    }
} elseif (!$yesiwantto) {
    header('Location: '.$_SERVER['PHP_SELF'].'?action='.$oldaction.'&'.give_passthrough(1));
    exit;
} else {
    if ('true' == $kill_page) {
        // Alle Mails auf der Seite löschen?
        $WP_exit = FALSE;
        include_once($WP_core['page_path'].'/lib/pop3.inc.php');
        $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
        if ($POP->check_connected() == 'unconnected') {
            $WP_exit = TRUE;
            $WP_return = $POP->get_last_error();
        } else {
            if (!$POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop'])) {
                $WP_exit = TRUE;
                $WP_return = $POP->get_last_error();
            }
        }
        if (!$WP_exit) {
            foreach($uidl as $key => $id) {
                // Hole Unique Mail-ID
                if ($POP->uidl($key) == base64_decode($uidl[$key])) {
                    $POP->delete($key);
                    $DB->markread_unset($_SESSION['WPs_uid'], $_SESSION['WPs_profileID'], md5($uidl[$key]));
                } else $WP_return .= str_replace('$1', $key, $WP_msg['cantkill']).'<br />'.LF;
            }
            // Disconnect
            $POP->close();
            // Prevent blocked reopening afterwards
            if ($_SESSION['WPs_killsleep'] > 0) sleep($_SESSION['WPs_killsleep']);
        }
    } elseif ('true' == $kill_all) {
        // Alle Mails löschen?
        $WP_exit = FALSE;
        include_once($WP_core['page_path'].'/lib/pop3.inc.php');
        $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
        if ($POP->check_connected() == 'unconnected') {
            $WP_exit = TRUE;
            $WP_return = $POP->get_last_error();
        } else {
            if (!$POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop'])) {
                $WP_exit = TRUE;
                $WP_return = $POP->get_last_error();
            }
        }
        if (!$WP_exit) {
            // Nur löschen, wenn inboxcount stimmt
            $line = $POP->stat();
            if ($line['mails'] == $inboxcount) {
                for ($i = 1; $i <= $inboxcount; ++$i) {
                    $DB->markread_unset($_SESSION['WPs_uid'], $_SESSION['WPs_profileID'], md5($POP->uidl($i)));
                    $POP->delete($i);
                }
            } else $WP_return = $WP_msg['nokillchange'].'<br />'.LF;
            // Disconnect
            $POP->close();
            // Prevent blocked reopening afterwards
            if ($_SESSION['WPs_killsleep'] > 0) sleep($_SESSION['WPs_killsleep']);
        }
    } elseif (isset($kmail) && sizeof($kmail) != 0) {
        // Oder wurde Liste an Mails übergeben?
        $WP_exit = FALSE;
        include_once($WP_core['page_path'].'/lib/pop3.inc.php');
        $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
        if ($POP->check_connected() == 'unconnected') {
            $WP_exit = TRUE;
            $WP_return = $POP->get_last_error();
        } else {
            if (!$POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop'])) {
                $WP_exit = TRUE;
                $WP_return = $POP->get_last_error();
            }
        }
        if (!$WP_exit) {
            foreach ($kmail as $key => $id)  {
                // Hole Unique Mail-ID
                if ($POP->uidl($id) == base64_decode($uidl[$id])) {
                    $POP->delete($id);
                    // MOD
                } else $WP_return .= str_replace('$1', $id, $WP_msg['cantkill']).'<br />'.LF;
            }
            // Disconnect
            $POP->close();
            // Prevent blocked reopening afterwards
            if ($_SESSION['WPs_killsleep'] > 0) sleep($_SESSION['WPs_killsleep']);
        }
    }
    header('Location: '.$_SERVER['PHP_SELF'].'?action=inbox&WP_return='.base64_encode($WP_return).'&'.give_passthrough(1));
    exit;
}
?>
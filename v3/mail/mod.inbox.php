<?php
/* ------------------------------------------------------------------------- */
/* mod.inbox.php -> Kontoauswahl und Anzeige Posteingang                     */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.2.5                                                                    */
/* ------------------------------------------------------------------------- */

include_once($WP_core['page_path'].'/lib/message.decode.php');

$tpl = new fxl_template($WP_core['skin_path'].'/templates/inbox.general.tpl');

if (file_exists($WP_core['conf_files'].'/global.MOTD.wpop') && '1' == $WP_core['show_motd']) {
    if (!isset($_SESSION['WPs_motd_shown'])) {
        $WP_core['MOTD'] = nl2br(stripslashes(join('', file($WP_core['conf_files'].'/global.MOTD.wpop'))));
        $_SESSION['WPs_motd_shown'] = TRUE;
        $motd = $tpl->getblock('MOTD');
        $motd->assign('MOTD', str_replace('$1', $_SESSION['WPs_username'], $WP_core['MOTD']));
        $tpl->assign('MOTD', $motd);
    }
}

if (isset($WP_ext['inbox_prof_right']) && $WP_ext['inbox_prof_right']) {
    $tpl->assign('ext_inbox_profright', $WP_ext['inbox_prof_right']);
}


// Make sure to erase any information of a multi bounce
foreach (array
        ('bouncelist','bounceddeletelist','bounce_lastknown_uidl'
        ,'dootherslist', 'origaction', 'singlebouncer') as $k) {
    if (isset($_SESSION[$k])) unset($_SESSION[$k]);
}

// Preparation, if mails could become marked
if (isset($mode) && 'kill_selection' == $kill_mode) {
    $kmail = array_flip($kmail);
}
if (isset($mode) && 'kill_all' == $kill_mode) {
     $WP_return = base64_encode($WP_msg['bounce_disabled']);
     unset($mode);
}

$useraccounts = $DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']);

if (isset($profile)) { // Profil wurde übergeben
    $sel_profile = $profile;
    // Profil unterschiedlich zu dem in der Session hinterlegten
    if (isset($_SESSION['WPs_profileID']) && $_SESSION['WPs_profileID'] != $profile) {
        $_SESSION['WPs_profileID'] = $profile;
        $_SESSION['WPs_pagenum'] = 0;
    }
} elseif (isset($_SESSION['WPs_profileID'])) { // Profil ist in der Session gepseichert
    $sel_profile = $_SESSION['WPs_profileID'];

} else { // Nix...
    $sel_profile = FALSE;
}

if (isset($WP_core_pagenum)) $_SESSION['WPs_pagenum'] = $WP_core_pagenum;
if (isset($WP_core_jumppage)) $_SESSION['WPs_pagenum'] = $WP_core_jumppage-1;
if (!isset($_SESSION['WPs_pagenum'])) $_SESSION['WPs_pagenum'] = 0;

// Try to find out, which profile is the current one, so we can connect to
// it right away, in case there's already an user selected profile, we
// try to reconnect to its maildrop
$active_profs = array();
$activecnt = 0;
$last_active = FALSE;
if (is_array($useraccounts)) {
    foreach ($useraccounts as $k => $profilenm) {
        $profiledata = $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);
        if (1 == trim($profiledata['acc_on'])) {
            // Find out, wether there's sensible data in that profile
            $connect = $DB->get_popconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);
            // If not - skip it
            if (!$connect['popserver'] || !$connect['popuser'] || !$connect['poppass']) continue;
            //
            ++$activecnt;
            $active_profs[$k] = $profilenm;
            $last_active = $k;
        }
    }
}
// If we have only one active profile, this is considered the currently selected one
if (count($active_profs) == 1) $sel_profile = $last_active;
// Make sure, the "default profile" setting is initialised
if (!isset($WP_core['default_profile'])) $WP_core['default_profile'] = 0;

$t_b = $tpl->getBlock('on_account');
foreach ($active_profs as $k => $profilenm) {
    $t_l = $t_b->getBlock('menline');
    if (($sel_profile and $sel_profile == $k) || (!$sel_profile && $k == $WP_core['default_profile'])) {
        $t_l->assign_block('sel');
        $connect     = $DB->get_popconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);
        $smtpconn    = $DB->get_smtpconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);

        $_SESSION['WPs_popverbose'] = $profilenm;
        $_SESSION['WPs_popapop']    = $connect['popnoapop'];
        $_SESSION['WPs_popserver']  = $connect['popserver'];
        $_SESSION['WPs_popport']    = (isset($connect['popport']) && $connect['popport'])
                                    ? $connect['popport'] : 110;
        $_SESSION['WPs_popuser']    = $connect['popuser'];
        $_SESSION['WPs_poppass']    = $connect['poppass'];
        $_SESSION['WPs_killsleep']  = $connect['killsleep'];
        $_SESSION['WPs_profileID']  = $k;
        // If we have SMTP connection data for this profile, put this into session, else try to use the default
        // connection data
        if (isset($smtpconn['smtpserver']) && $smtpconn['smtpserver']) {
            $_SESSION['WPs_smtpserver']   = $smtpconn['smtpserver'];
            $_SESSION['WPs_smtpport']     = ($smtpconn['smtpport']) ? $smtpconn['smtpport'] : 25;
            $_SESSION['WPs_smtpuser']     = $smtpconn['smtpuser'];
            $_SESSION['WPs_smtppass']     = $smtpconn['smtppass'];
            $_SESSION['WPs_smtpafterpop'] = $smtpconn['smtpafterpop'];
        } elseif (isset($WP_core['fix_smpt_host']) && $WP_core['fix_smpt_host']) {
            $_SESSION['WPs_smtpserver'] = $WP_core['fix_smpt_host'];
            $_SESSION['WPs_smtpport']   = ($WP_core['fix_smpt_port']) ? $WP_core['fix_smpt_port'] : 25;
            $_SESSION['WPs_smtpuser']   = (isset($WP_core['fix_smpt_user'])) ? $WP_core['fix_smpt_user'] : FALSE;
            $_SESSION['WPs_smtppass']   = (isset($WP_core['fix_smpt_pass'])) ? $WP_core['fix_smpt_pass'] : FALSE;
        }
    }
    $t_l->assign('value', $k);
    $t_l->assign('text', $profilenm);
    $t_b->assign('menline', $t_l);
    $t_l->clear();
}
if (1 < $activecnt) {
    $t_b->assign(array
        ('PHP_SELF' => $_SERVER['PHP_SELF']
        ,'msg_profile' => $WP_msg['profile']
        ,'passthrough_2' => give_passthrough(2)
        ,'mail' => $mail
        ,'msg_login' => $WP_msg['login']
        ));
    $tpl->assign('on_account', $t_b);
}

$passthrough = htmlspecialchars(give_passthrough(1));

if (isset($_SESSION['WPs_profileID'])) {
    $DB->set_poplogintime($_SESSION['WPs_uid'], $_SESSION['WPs_profileID']);
    include_once($WP_core['page_path'].'/lib/pop3.inc.php');
    $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
    if ($POP->check_connected() == 'unconnected') {
        $error = $WP_msg['noconnect'].' '.$_SESSION['WPs_popserver'].' ('.$POP->get_last_error().')';
    } elseif (!$POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop'])) {
        $error = $WP_msg['wrongauth'].' '.$_SESSION['WPs_popserver'].' ('.$POP->get_last_error().')';
    }
} else {
    $WP_exit = TRUE;
    $t_nmb = $tpl->getblock('nomailblock');
    if (!$activecnt && 'true' == $WP_core['conf_acc']) {
        $t_nmb->assign('nonewmail', $WP_msg['CreateProfileFirst']);
        $t_pm = $t_nmb->getblock('profman');
        $t_pm->assign(array
                ('msg_profman' => $WP_msg['accounts']
                ,'link_profman' => $_SERVER['PHP_SELF'].'?action=setup&amp;'.$passthrough.'&amp;mode=edit'
                ));
        $t_nmb->assign('profman', $t_pm);
    } else {
        $t_nmb->assign('nonewmail', $WP_msg['NoSrvrSelected']);
    }
    $tpl->assign('nomailblock', $t_nmb);
}

if (!isset($WP_exit)) {
    // --- Custom Logging of logins and login attempts
    if ('yes' == $WP_core['log_popauth']) {
        $loginlog = create_logdir($WP_core['log_dirlipop'] . '/' . $WP_core['log_basename']);
        // Do the logging now
        $lf = fopen($loginlog, 'a');
        @flock($lf, LOCK_EX);
        fputs($lf, 'li '.$WP_core['timestamp'].' \''.$popuser.'\' '.getenv('REMOTE_ADDR').LF);
        fclose($lf);
    }
    // ---
    if (isset($error) && $error) {
        $tpl->assign_block('errorblock');
        $tpl->assign('error', $error);
        $eingang = 0;
    } else {
        if (isset($WP_return) && $WP_return) {
            $tpl->assign_block('returnblock');
            $tpl->assign('return', base64_decode($WP_return));
        }
        // How many mails are on the server? Size of 'em?
        $groesse = $POP->stat();
        $eingang = isset($groesse['mails']) ? $groesse['mails'] : 0;
        $all_size = isset($groesse['size']) ? $groesse['size'] : 0;
    }
    if (!isset($WP_exit)) {
        if (0 == $WP_core['pagesize']) {
            $displayend = $i = $eingang;
            $displaystart = 1;
            $i2 = 0;
        } else {
            if ($_SESSION['WPs_pagenum'] < 0) $_SESSION['WPs_pagenum'] = 0;
            if ($WP_core['pagesize'] * $_SESSION['WPs_pagenum'] > $eingang) {
                $_SESSION['WPs_pagenum'] = ceil($eingang/$WP_core['pagesize']) - 1;
            }
            $i = $eingang - ($WP_core['pagesize'] * $_SESSION['WPs_pagenum']);
            $i2 = $i - $WP_core['pagesize'];
            if ($i2 < 0) $i2 = 0;
            $displaystart = $WP_core['pagesize'] * $_SESSION['WPs_pagenum'] + 1;
            $displayend = $WP_core['pagesize'] * ($_SESSION['WPs_pagenum'] + 1);
            if ($displayend > $eingang) $displayend = $eingang;
        }

        if ($eingang == 0) {
            $tpl->assign_block('nomailblock');
            $tpl->assign('nonewmail', $WP_msg['nonewmail']);
        } else {
            $tpl_n = $tpl->getBlock('mailblock');
            if ($eingang == 1) $plural = $WP_msg['newmail']; else $plural = $WP_msg['newmails'];
            $tpl_n->assign('neueingang', $eingang);
            $tpl_n->assign('plural', $plural);
            $tpl_n->assign('newmails', $WP_msg['newmails']);
            $tpl_n->assign('PHP_SELF', $_SERVER['PHP_SELF']);
            $tpl_n->assign('displaystart', $displaystart);
            $tpl_n->assign('displayend', $displayend);

            if ($_SESSION['WPs_pagenum'] > 0) {
                $t_bck = $tpl_n->getBlock('blstblk');
                $t_bck->assign(array
                        ('link_last' => $_SERVER['PHP_SELF'].'?action='.$action
                                       .'&amp;'.$passthrough.'&amp;WP_core_pagenum='
                                       .($_SESSION['WPs_pagenum']-1)
                        ,'but_last' => '&lt;&lt;'
                        ,'skin_path' => $WP_core['skin_path']
                        ));
                $tpl_n->assign('blstblk', $t_bck);
            }
            if ($displayend < $eingang) {
                $t_fwd = $tpl_n->getBlock('bnxtblk');
                $t_fwd->assign(array
                        ('link_next' => $_SERVER['PHP_SELF'].'?action='.$action
                                       .'&amp;'.$passthrough.'&amp;WP_core_pagenum='
                                       .($_SESSION['WPs_pagenum']+1)
                        ,'but_next' => '&gt;&gt;'
                        ,'skin_path' => $WP_core['skin_path']
                        ));
                $tpl_n->assign('bnxtblk', $t_fwd);
            }

            $valid_email = 1;
            $tpl_lines = $tpl_n->getBlock('maillines');
            $sumgroess = 0;
            while ($i > $i2) {
                // Hole Unique Mail-ID
                $uidl[$i] = base64_encode($POP->uidl($i));
                // Mailgröße, muß nur gezogen werden, wenn nicht schon oben bekommen
                if (!isset($groeese) || !$groesse) $groesse = $POP->get_list($i);
                if ($groesse > 0) {
                    $sumgroess += $groesse;
                } else {
                    $groesse = '-';
                    $size = '';
                }
                // Und nun den Mailheader
                $mail_header = explode_822_header($POP->top($i));
                if (!isset($mail_header['mime'])) $mail_header['mime'] = FALSE;
                if (!isset($mail_header['content_type'])) $mail_header['content_type'] = FALSE;

                // Find Reply-To and use it
                if (isset($mail_header['replyto']) && $mail_header['replyto']) {
                    $from2 = parse_email_address($mail_header['from'], $WP_skin['length_links']);
                    $from = parse_email_address($mail_header['replyto']);
                } else {
                    $from = parse_email_address($mail_header['from']);
                    $from2 = parse_email_address($mail_header['from'], $WP_skin['length_links']);
                }
                $tpl_lines->assign('from_1', mailto_2_send('mailto:'.$from[0]));
                $tpl_lines->assign('from_2', $from2[2]);
                $tpl_lines->assign('from_3', $from2[1]);
                $thema_title = '';
                $thema = stripslashes($mail_header['subject']);
                if (strlen($thema) > $WP_skin['length_links']) {
                    $thema_title = htmlspecialchars($thema);
                    $thema = substr($thema, 0, ($WP_skin['length_links'] - 3)).'...';
                }
                $thema = htmlspecialchars($thema);
                if (!$thema || $thema == CRLF) $thema = $WP_msg['nosubj'];
                $mail_header['date'] = strtotime($mail_header['date']);
                if (-1 == $mail_header['date']) {
                    $short_datum = $datum = '---';
                } else {
                    $datum = htmlspecialchars(date($WP_msg['dateformat'], $mail_header['date']));
                    if (date('Y', $mail_header['date']) == date('Y')) {
                        $short_datum = htmlspecialchars(date($WP_msg['dateformat_new'], $mail_header['date']));
                    } else {
                        $short_datum = htmlspecialchars(date($WP_msg['dateformat_old'], $mail_header['date']));
                    }
                }

                if (!preg_match('!^text/(plain|html)!i', $mail_header['content_type'])
                        && '1.0' == trim($mail_header['mime'])) {
                    $t_l_a = $tpl_lines->getBlock('attach');
                    $t_l_a->assign('attach', $WP_core['skin_path'].'/attach.gif');
                    $t_l_a->assign('title', $WP_msg['attachs']);
                    $tpl_lines->assign('attach', $t_l_a);
                }
                if ('1' == $mail_header['importance']) {
                    $t_l_p = $tpl_lines->getBlock('prio');
                    $t_l_p->assign('prio', $WP_core['skin_path'].'/hprio.gif');
                    $t_l_p->assign('title', $WP_msg['prio'].': '.$WP_msg['high']);
                    $tpl_lines->assign('prio', $t_l_p);
                } elseif('5' == $mail_header['importance']) {
                    $t_l_p = $tpl_lines->getBlock('prio');
                    $t_l_p->assign('prio',$WP_core['skin_path'].'/lprio.gif');
                    $t_l_p->assign('title',$WP_msg['prio'].': '.$WP_msg['low']);
                    $tpl_lines->assign('prio',$t_l_p);
                }
                if (isset($WP_core['use_markread']) && $WP_core['use_markread']) {
                    // Drop down selection mark / unmark mails
                    if (isset($mode) && ('markread_set' == $mode || 'markread_unset' == $mode)) {
                        if ('kill_page' == $kill_mode) {
                            $DB->$mode($_SESSION['WPs_uid'], $_SESSION['WPs_profileID'], md5($uidl[$i]));
                        } elseif (isset($kmail[$i])) {
                            $DB->$mode($_SESSION['WPs_uid'], $_SESSION['WPs_profileID'], md5($uidl[$i]));
                        }
                    }
                    // Get status of mail and mark it accordingly in the inbox
                    if ($DB->markread_status($_SESSION['WPs_uid'], $_SESSION['WPs_profileID'], md5($uidl[$i]))) {
                        $tpl_lines->assign_block('mark_read');
                    } else {
                        $tpl_lines->assign_block('mark_unread');
                    }
                }
                $tpl_lines->assign(array
                            ('viewlink' => $_SERVER['PHP_SELF'].'?action=read&mail='.$i.'&amp;uidl='
                                          .$uidl[$i].'&amp;'.$passthrough
                            ,'subject' => $thema
                            ,'uidl' => $uidl[$i]
                            ,'id' => $i
                            ,'skin_path' => $WP_core['skin_path']
                            ,'subj_title' => $thema_title
                            ,'date' => $datum
                            ,'short_date' => $short_datum
                            ,'size' => ($groesse > $WP_core['big_mark'])
                                     ? '<span class="bigmark">'.size_format($groesse).'</span>'
                                     : size_format($groesse)
                            ,'rawsize' => number_format($groesse, 0, $WP_msg['dec'], $WP_msg['tho'])
                            ));
                if (isset($WP_ext['head_size'])) {
                    eval($WP_ext['head_size']);
                    $tpl_lines->assign('eval_size', $WP_ext['head_size']);
                }

                $tpl_n->assign('maillines',$tpl_lines);
                $tpl_lines->clear();
                --$i;
            }
            // Disconnect
            $POP->close();
            //
            if ('1' == $WP_core['killall']) {
                $tpl_n->assign_block('ifkillall');
                $tpl_n->assign('all', $WP_msg['all']);
            }
            if (isset($WP_ext['head_menu'])) {
                eval($WP_ext['head_menu']);
                $tpl_n->assign('eval_headmenu',$WP_ext['head_menu']);
            }
            if (isset($WP_ext['head_asize'])) {
                eval($WP_ext['head_asize']);
                $tpl_n->assign('eval_headsize',$WP_ext['head_asize']);
            }
            if (isset($WP_core['use_markread']) && $WP_core['use_markread']) {
                $tpl_n->assign_block('markread_ops');
            }
            // Handle Jump to Page Form
            if ($WP_core['pagesize']) {
                $max_page = ceil($eingang / $WP_core['pagesize']);
            } else {
                $max_page = 0;
            }
            $jumpsize = strlen($max_page);
            $tpl_n->assign(array
                    ('rawsumsize' => number_format($sumgroess, 0, $WP_msg['dec'], $WP_msg['tho'])
                    ,'sumsize' => size_format($sumgroess)
                    ,'rawallsize' => number_format($all_size, 0, $WP_msg['dec'], $WP_msg['tho'])
                    ,'allsize' => size_format($all_size)
                    ,'hsubject' => $WP_msg['subject']
                    ,'hfrom' => $WP_msg['from']
                    ,'hdate' => $WP_msg['date']
                    ,'hsize' => $WP_msg['size']
                    ,'del' => $WP_msg['del']
                    ,'bounce' => $WP_msg['bounce']
                    ,'go' => $WP_msg['goto']
                    ,'size' => $jumpsize
                    ,'maxlen' => $jumpsize
                    ,'page' => $_SESSION['WPs_pagenum'] + 1
                    ,'msg_page' => $WP_msg['page']
                    ,'boxsize' => $max_page
                    ,'passthrough_2' => give_passthrough(2)
                    ,'selection' => $WP_msg['selection']
                    ,'allpage' => $WP_msg['allpage']
                    ,'msg_markreadset' => $WP_msg['markread_set']
                    ,'msg_markreadunset' => $WP_msg['markread_unset']
                    ));

            $tpl->assign('mailblock',$tpl_n);
        }
        $t_re = $tpl->get_block('refresh');
        $t_re->assign(array
                ('msg_refresh' => $WP_msg['refresh']
                ,'link_refresh' => $_SERVER['PHP_SELF'].'?action=inbox&mail='.$mail.'&'.$passthrough
                ));
        $tpl->assign('refresh', $t_re);
        $tpl->assign(array
                ('passthrough_2' => give_passthrough(2)
                ,'neueingang' => $eingang, 'msg_none' => $WP_msg['selNone']
                ,'msg_all' => $WP_msg['selAll'], 'PHP_SELF' => $_SERVER['PHP_SELF']
                ,'action' => $action, 'msg_rev' => $WP_msg['selRev'], 'oldaction' => $action
                ));
    }
}
?>
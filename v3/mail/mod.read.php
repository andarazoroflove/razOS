<?php
/* ------------------------------------------------------------------------- */
/* mod.read.php -> Anzeige einer gewählten Mail                              */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v2.0.1                                                                    */
/* ------------------------------------------------------------------------- */

include_once($WP_core['page_path'].'/lib/message.decode.php');

$passthrough1 = give_passthrough(1);

if (isset($savemail)) {
    $tpl = new FXL_Template($WP_core['skin_path'].'/templates/read.savemenu.tpl');
    $tpl->assign(array
            ('linkbase' => $_SERVER['PHP_SELF'].'?'.$passthrough1.'&amp;attach='.$attach
            ,'action' => $action, 'mail' => $mail, 'msg_cancel' => $WP_msg['cancel']
            ,'msg_save' => $WP_msg['save'], 'msg_choose' => $WP_msg['save_choose']
            ,'msg_complete' => $WP_msg['save_complete'], 'msg_body' => $WP_msg['save_body']
            ,'msg_shead' => $WP_msg['save_sheader'], 'msg_ahead' => $WP_msg['save_aheader']
            ,'msg_alist' => $WP_msg['save_attlist']
            ));
} elseif (isset($mail)) {
    if (!isset($save_as))  $save_as  = FALSE;
    if (!isset($save_opt)) $save_opt = FALSE;
    if (!isset($what))     $what     = FALSE;

    $attach = (isset($attach)) ? $attach : '';
    $linkbase = $_SERVER['PHP_SELF'].'?'.$passthrough1.'&amp;attach='.$attach;

    // Wichtigkeiten
    $WP_prio = array(1 => $WP_msg['high'], 3 => $WP_msg['normal'], 5 => $WP_msg['low']);
    // Textauszeichnung / Headerausgabe / HTML-Mails
    if (isset($teletype))       $_SESSION['WPs_tt']            = $teletype;
    if (isset($viewallheaders)) $_SESSION['WPs_vheaders']      = $viewallheaders;
    if (isset($sanitize_html))  $_SESSION['WPs_sanitize_html'] = $sanitize_html;
    if (!isset($_SESSION['WPs_tt']))       $_SESSION['WPs_tt']       = $WP_core['teletype'];
    if (!isset($_SESSION['WPs_vheaders'])) $_SESSION['WPs_vheaders'] = 0;
    if (!isset($_SESSION['WPs_sanitize_html'])) {
        $_SESSION['WPs_sanitize_html'] = isset($WP_core['sanitize_html'])
                ? $WP_core['sanitize_html']
                : TRUE;
    }

    include_once($WP_core['page_path'].'/lib/pop3.inc.php');
    $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
    if ($POP->check_connected() == 'unconnected') {
        $WP_exit = TRUE;
        $error = $POP->get_last_error();
    } else {
        if (!$POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop'])) {
            $WP_exit = TRUE;
            $error = $POP->get_last_error();
        }
    }
    if (isset($WP_exit)) {
        $tpl = new FXL_Template($WP_core['skin_path'].'/templates/all.general.tpl');
        $tpl->assign
                ('output', (isset($error))
                    ? $error
                    : $WP_msg['noconnect'].' '.$_SESSION['WPs_popverbose'].'<br />'
                );
        return;
    }

    // How many mails are on the server?
    $details = $POP->stat();
    $neueingang = $details['mails'];
    // Ist die angegebene Mail überhaupt da?
    if ($mail < 1) $mail = 1;
    if ($mail > $neueingang) $mail = $neueingang;
    // UIDL
    $uidl = base64_encode($POP->uidl($mail));
    // Mark the mail as read
    if (isset($WP_core['use_markread']) && $WP_core['use_markread']) {
        $DB->markread_set($_SESSION['WPs_uid'], $_SESSION['WPs_profileID'], md5($uidl));
    }

    $WP_core['output'] = 'true';

    $groesse = $POP->get_list($mail);
    $POP->retrieve($mail);
    $startzeit = get_microtime();
    // Ansehen des Quelltextes oder Speichern der Mail im Rohformat
    if (isset($viewsrc) || 'raw' == $save_as) {
        if ($save_as) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=phlymail.eml');
        } else header('Content-Type: text/plain');
        while ($line = $POP->talk_ml()) { echo $line; }
       exit();
    } else {
        // Go get some Mail
        if ($groesse > $WP_core['big_noshow']) {
            list($mail_header) = explode_mime_body($POP, -1, 0, 1);
            $decode_mime = 0;
            $mailbody = &$WP_msg['toobigtoshow'];
        } elseif (isset($WP_core['readsource']) && 'inline' == $WP_core['readsource']) {
            list($mail_header, $mimebody) = explode_mime_body($POP, $attach, 1, 0);
        } else list($mail_header, $mimebody) = explode_mime_body($POP, -1, 0, 0);
        //
        if ('print' != $what && !$save_as) {
            // Find Reply-To and use it
            if (isset($mail_header['replyto'])) {
                $von2 = parse_email_address($mail_header['from']);
                $von = parse_email_address($mail_header['replyto']);
            } else $von2 = $von = parse_email_address($mail_header['from']);
            $eval_from = $mail_header['from'];
            $eval_to = $mail_header['to'];
            $mail_header['from'] = '<a href="'.mailto_2_send('mailto:'.$von[0]).'" title="'.$von2[2].'">'.$von2[1].'</a>';
            $mail_header['to'] = multi_address($mail_header['to']);
            $mail_header['subject'] = htmlspecialchars(stripslashes($mail_header['subject']));
            $mail_header['date'] = htmlspecialchars(@date($WP_msg['dateformat'], @strtotime($mail_header['date'])));
        }
        if ('print' == $what) {
            $von2 = parse_email_address($mail_header['from']);
            $mail_header['from'] = htmlspecialchars($von2[2]);
            $mail_header['to'] = htmlspecialchars($mail_header['to']);
            $mail_header['subject'] = htmlspecialchars(stripslashes($mail_header['subject']));
            $mail_header['date'] = htmlspecialchars(@date($WP_msg['dateformat'], @strtotime($mail_header['date'])));
        }
        if (!$mail_header['date']) $mail_header['date'] = '---';
        if (in_array($save_as, array('html', 'txt', 'xml'))) {
            $tpl = new FXL_Template($WP_core['skin_path'].'/templates/read.save.'.$save_as.'.tpl');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=phlymail.'.$save_as);
        }
        if (!$save_as) {
            $tpl = new FXL_Template($WP_core['skin_path'].'/templates/read.general.tpl');
            if ($what == 'print') {
                $t_h = $tpl->getBlock('printhead');
                $t_h->assign('pagetitle', $WP_msg['printview']);
                $t_h->assign('printview', $WP_msg['printview']);
                $tpl->assign('printhead', $t_h);
                $tpl->assign_block('printfoot');
            } else {
                if (!isset($WP_core['readsource']) || 'inline' != $WP_core['readsource']) {
                    $tpl_std = $tpl->getblock('standard');

                    if (!$save_as) {
                        if ('sys' == $_SESSION['WPs_tt']) {
                            $tpl_std->assign_block('teletype_pro');
                            $tpl_std->assign(array
                                    ('link_teletype' => $linkbase.'&amp;action='.$action.'&amp;teletype=pro&amp;mail='.$mail
                                    ,'but_teletype' => $WP_msg['txt_prop']
                                    ));
                        } else {
                            $tpl_std->assign_block('teletype_sys');
                            $tpl_std->assign(array
                                    ('link_teletype' => $linkbase.'&amp;action='.$action.'&amp;teletype=sys&amp;mail='.$mail
                                    ,'but_teletype' => $WP_msg['txt_syst']
                                    ));
                        }
                    }
                    if ($_SESSION['WPs_vheaders'] == 1 || 'complete' == $save_opt) {
                        if (!$save_as) $tpl_std->assign_block('fullheader');
                        $tpl_std->assign(array
                                ('but_header' => $WP_msg['woheader']
                                ,'link_header' => $linkbase.'&amp;action='.$action.'&amp;viewallheaders=0&amp;mail='.$mail));
                    } else {
                        if (!$save_as) $tpl_std->assign_block('normalheader');
                        $tpl_std->assign(array
                                ('but_header' => $WP_msg['wheader']
                                ,'link_header' => $linkbase.'&amp;action='.$action.'&amp;viewallheaders=1&amp;mail='.$mail));
                    }

                    if ($mail < $neueingang) $tpl_std->assign_block('blstblk');
                    if ($mail > 1) $tpl_std->assign_block('bnxtblk');
                    if ('true' == $WP_core['allow_send']) {
                        $tpl_ons = $tpl_std->get_block('onsend');
                        if (isset($WP_core['show_dismiss']) && $WP_core['show_dismiss']) {
                            $tpl_ons->assign_block('dismiss');
                        }
                        $tpl_std->assign('onsend', $tpl_ons);
                    }
                    $tpl_std->assign(array
                            ('size' => strlen($neueingang)
                            ,'maxlen' => strlen($neueingang)
                            ,'boxsize' => $neueingang
                            ,'goto' => $WP_msg['goto']
                            ));
                    $tpl->assign('standard', $tpl_std);
                }
            }
        }
        if (1 == $decode_mime) {
            if (sizeof($mimebody['part_type']) == 0) {
                $mimebody['part_type'][0] = &$mail_header['content_type'];
                $mimebody['part_encoding'][0] = &$mail_header['content_encoding'];
                $mimebody['part_detail'][0] = &$mail_header['content_type_pad'];
                if (strtolower($mail_header['content_type']) == 'text/plain') {
                    $parts_text = 'true';
                } elseif(strtolower($mail_header['content_type']) == 'text/html') {
                    $parts_html = 'true';
                } else {
                    $mimebody['part_attached'][0] = '1';
                    $parts_attach = 'true';
                }
            }
            if (!isset($mimebody['part_attached']) || sizeof($mimebody['part_attached']) < 1) {
                $parts_attach = 'false';
            }
        } else {
            $active_part = -1;
            $mimebody['part_type'][-1] = &$mail_header['content_type'];
            $mimebody['part_encoding'][-1] = &$mail_header['content_encoding'];
        }
        // Sichtbaren Teil dekodieren und für Ausgabe vorbereiten
        if (strtolower($mimebody['part_encoding'][$active_part]) == 'quoted-printable') {
            $mailbody = quoted_printable_decode(str_replace('='.CRLF, '', $mailbody));
        } elseif(strtolower($mimebody['part_encoding'][$active_part]) == 'base64') {
            $mailbody = base64_decode($mailbody);
        }
        if (strtolower($mimebody['part_type'][$active_part]) == 'text/html') {
            if ('HTML' != $WP_core['tpl_scheme']) {
                $mailbody = strip_tags($mailbody);
            } else {
                // Depending on global / current setting: Clean Up HTML
                if ($_SESSION['WPs_sanitize_html']) {
                    // Remove almost everything
                    // Kill: Head, Script and Style areas, iframes,
                    // many unwanted HTML tags, background images, colour settings,
                    // comments, images
                    $sanit_search = array
                            ('/<!--.*?-->/s', '!\ +!','!<head>.+</head>!si'
                            ,'!<style.*?>.+</style>!si', '!<script.*?>.+</script>!si'
                            ,'!<iframe.*?>.*?</iframe>!si'
                            ,'!</?(html|body|\!doctype|font|img|span|nobr).*?>!si', '!&nbsp;!i'
                            ,'!(style|background|color|bgcolor|width|height)=(".+?"|.+?)(?=\ |>)!si'
                            );
                    $sanit_repl = array('',' ','','','','', ' ',' ','');
                } else {
                    // Wipe out less data from original mail (leaving images and background images)
                    $sanit_search = array
                            ('/<!--.*?-->/s', '!\ +!','!<head>.+</head>!si'
                            ,'!<style.*?>.+</style>!si', '!<script.*?>.+</script>!si'
                            ,'!<iframe.*?>.*?</iframe>!si'
                            ,'!</?(html|body|\!doctype|font|span|nobr).*?>!si', '!&nbsp;!i'
                            ,'!(style|color|bgcolor|width|height)=(".+?"|.+?)(?=\ |>)!si'
                            );
                    $sanit_repl = array('',' ','','','','',' ',' ','');
                }
                $mailbody = preg_replace($sanit_search, $sanit_repl, links($mailbody, 'html'));
            }
        } else {
            if('sys' == $_SESSION['WPs_tt']) {
                $mailbody = '<tt>'.nice_view($mailbody, $_SESSION['WPs_tt']).'</tt>';
            } else $mailbody = nice_view($mailbody);
        }
        if (isset($WP_skin['length_body']) && $WP_skin['length_body'] > 0) {
            $mailbody = substr($mailbody, 0, $WP_skin['length_body']);
        }
        $endzeit = get_microtime();
        if ('body' == $save_as) {
            header('Content-Type: text/plain; name=clipboard.txt');
            header('Content-Disposition: attachment; filename=clipboard.txt');
            echo un_html(strip_tags($mailbody));
        } else {
            if ('txt' == $save_as) {
                $tpl->assign('mailbody', un_html(strip_tags($mailbody)));
            } elseif ('xml' == $save_as) {
                $tpl->assign('mailbody', strip_tags($mailbody));
            } else $tpl->assign('mailbody', $mailbody);
            $tpl->assign('time_taken', 'Mail parsed in '.number_format($endzeit-$startzeit,2).' seconds');
            // Wenn speichern ohne Attachmentliste
            if (isset($save_att) && 'yes' != $save_att) unset($mimebody['part_attached']);
            //
            if (isset($parts_attach) && $parts_attach == 'true'
                    && isset($mimebody['part_attached']) && is_array($mimebody['part_attached'])) {
                include_once($WP_core['page_path'].'/lib/phm_mime_handler.php');
                $MIME = new phm_mime_handler($WP_core['conf_files'].'/mime.map.wpop');
                $tpl_a = $tpl->getblock('attachblock');
                $return = get_visible_attachments($mimebody, $attach, 'links', $WP_core['skin_path'].'/mime');
                $tpl_al = $tpl_a->getblock('attachline');
                foreach ($return['img'] as $key => $value) {
                    $tpl_al->assign('att_icon', $WP_core['skin_path'].'/mime/'.$value);
                    $tpl_al->assign('att_num', $return['attid'][$key]);
                    if (preg_match('!^message/!', $return['img_alt'][$key])) {
                        $tpl_al->assign('link_target', 'action=read&amp;WP_core[readsource]=inline');
                    } else $tpl_al->assign('link_target', 'action=saug&amp;pure=true');
                    $tpl_al->assign('att_icon_alt', $return['img_alt'][$key]);
                    $tpl_al->assign('att_name', $return['name'][$key]);
                    if ('xml' == $save_as || 'txt' == $save_as) {
                        $tpl_al->assign('att_size', str_replace('&nbsp;', ' ', $return['size'][$key]));
                    } else $tpl_al->assign('att_size', $return['size'][$key]);
                    $tpl_al->assign('msg_att_type', $WP_msg['filetype']);
                    $tpl_al->assign('att_type', ($return['filetype'][$key])
                                               ? $return['filetype'][$key]
                                               : $WP_msg['nofiletype']
                            );
                    $tpl_a->assign('attachline', $tpl_al);
                    $tpl_al->clear();
                }
                $tpl_a->assign('msg_attachs', $WP_msg['attachs']);
                $tpl->assign('attachblock', $tpl_a);
            }
            $tpl_hl = $tpl->getblock('headerlines');
            if ($_SESSION['WPs_vheaders'] == 1 || 'complete' == $save_opt) {
                foreach ($mail_header['complete'][1] as $key => $value) {
                    $tpl_hl->assign('hl_key', $value);
                    $tpl_hl->assign('hl_val', ('txt' == $save_as)
                            ? stripslashes($mail_header['complete'][2][$key])
                            : stripslashes(htmlspecialchars($mail_header['complete'][2][$key]))
                            );
                    $tpl_hl->assign('hl_add', '');
                    $tpl->assign('headerlines', $tpl_hl);
                    $tpl_hl->clear();
                }
            } else {
               $tpl_hl = $tpl->getblock('headerlines');
               $keylist = array('from', 'to', 'cc', 'date', 'prio', 'subject', 'comment');
               $vallist = array('from', 'to', 'cc', 'date', 'importance', 'subject', 'comment');
               foreach ($vallist as $key => $value) {
                   if (isset($mail_header[$value]) && $mail_header[$value]) {
                       $tpl_hl->assign('hl_key', $WP_msg[$keylist[$key]]);
                       $tpl_hl->assign('hl_val', $mail_header[$value]);
                       // Mail Importance setting
                       if ($value == 'importance' && isset($mail_header[$value])) {
                           if (1 == $mail_header[$value]) {
                               $tpl_hl->assign('hl_add', ' class="prio_high"');
                               $tpl_hl->reassign('hl_val', $WP_msg['high']);
                           } elseif (5 == $mail_header[$value]) {
                               $tpl_hl->assign('hl_add', ' class="prio_low"');
                               $tpl_hl->reassign('hl_val', $WP_msg['low']);
                           } elseif (3 == $mail_header[$value]) {
                               $tpl_hl->clear();
                               continue;
                           }
                       }
                       if ('from' == $value && isset($WP_ext) && $WP_ext) {
                           eval($WP_ext['read_from']);
                           $tpl_hl->assign('hl_eval', $WP_ext['read_from']);
                       }
                       $tpl->assign('headerlines', $tpl_hl);
                       $tpl_hl->clear();
                   }
               }
            }
            $tpl->assign(array
                    ('msg_mail' => $WP_msg['mail'], 'but_answer' => $WP_msg['answer']
                    ,'but_answerAll'=> $WP_msg['answerAll'], 'but_print' => $WP_msg['prnt']
                    ,'but_forward' => $WP_msg['forward'], 'but_bounce' => $WP_msg['bounce']
                    ,'but_save' => $WP_msg['savemail'], 'but_pure' => $WP_msg['source']
                    ,'but_dismiss' => isset($WP_msg['dismissmail']) ? $WP_msg['dismissmail'] : ''
                    ,'link_answer' => $_SERVER['PHP_SELF'].'?action=send&amp;WP_send[answer]=1&amp;'.$passthrough1
                                    .'&amp;WP_send[active_part]='.$active_part.'&amp;mail='.$mail
                    ,'link_answerAll' => $_SERVER['PHP_SELF'].'?action=send&amp;&amp;WP_send[answerAll]=1&amp;'.$passthrough1
                                       .'&amp;WP_send[active_part]='.$active_part.'&amp;mail='.$mail
                    ,'link_forward' => $_SERVER['PHP_SELF'].'?action=send&amp;WP_send[forward]=1&amp;'.$passthrough1
                                     .'&amp;WP_send[active_part]='.$active_part.'&amp;mail='.$mail
                    ,'link_bounce' => $_SERVER['PHP_SELF'].'?action=bounce&amp;'.$passthrough1
                                    .'&amp;kmail[]='.$mail.'&amp;uidl['.$mail.']='.$uidl
                    ,'link_dismiss' => $_SERVER['PHP_SELF'].'?action=dismiss&amp;'.$passthrough1
                                     .'&amp;kmail[]='.$mail.'&amp;uidl['.$mail.']='.$uidl
                    ,'link_dele' => $linkbase.'&amp;action=kill&amp;uidl['.$mail.']='.$uidl.'&amp;kmail[]='
                                  .$mail.'&amp;oldaction='.$action.'&amp;mail='.$mail
                    ,'link_save' => $linkbase.'&amp;action='.$action.'&amp;mail='.$mail.'&amp;savemail=1'
                    ,'link_print' => $linkbase.'&amp;action='.$action.'&amp;mail='.$mail.'&amp;what=print&amp;pure=true'
                    ,'link_rawdata' => $linkbase.'&amp;action='.$action.'&amp;mail='.$mail.'&amp;viewsrc=1'
                    ,'link_inbox' => $linkbase.'&amp;action=inbox&amp;mail='.$mail
                    ,'link_next' => $linkbase.'&amp;action='.$action.'&amp;mail='.($mail-1)
                    ,'but_next' => '&gt;&gt;', 'but_last' => '&lt;&lt;'
                    ,'link_last' => $linkbase.'&amp;action='.$action.'&amp;mail='.($mail+1)
                    ,'but_up' => '^', 'but_dele' => $WP_msg['del']
                    ,'action' => $action, 'mail' => $mail, 'uidl' => $uidl
                    ,'active_part' => $active_part
                    ,'PHP_SELF' => $_SERVER['PHP_SELF']
                    ,'skin_path' => $WP_core['skin_path']
                    ,'passthrough' => $passthrough1
                    ,'passthrough_2' => give_passthrough(2)
                    ));
        }
    }
    // Disconnect
    $POP->close();
}
?>
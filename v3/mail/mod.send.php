<?php
/* ------------------------------------------------------------------------- */
/* mod.send.php -> Versand einer Mail (+Forward, Answer, Bounce)             */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v2.0.2                                                                    */
/* ------------------------------------------------------------------------- */

// Ist das Senden überhaupt erlaubt?
$useraccounts = $DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']);
$may_send = true;
if (!is_array($useraccounts) || empty($useraccounts)) {
    $may_send = false;
}
if (is_array($useraccounts)) {
    $activecnt = 0;
    foreach ($useraccounts as $k => $profilenm) {
        $profiledata = $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);
        if (1 == trim($profiledata['acc_on'])) {
            ++$activecnt;
        }
    }
    if (!$activecnt) $may_send = false;
}
if (!$may_send) {
    $tpl = new FXL_Template($WP_core['skin_path'].'/templates/inbox.general.tpl');
    $t_nmb = $tpl->getblock('nomailblock');
    if (!$activecnt && 'true' == $WP_core['conf_acc']) {
        $t_nmb->assign('nonewmail', $WP_msg['CreateProfileFirst']);
        $t_pm = $t_nmb->getblock('profman');
        $t_pm->assign(array
                ('msg_profman' => $WP_msg['accounts']
                ,'link_profman' => $_SERVER['PHP_SELF'].'?action=setup&amp;'.give_passthrough(1).'&amp;mode=edit'
                ));
        $t_nmb->assign('profman', $t_pm);
    } else {
        $t_nmb->assign('nonewmail', $WP_msg['NoSrvrSelected']);
    }
    $tpl->assign('nomailblock', $t_nmb);
    return;
}
$WP_return = false;

include_once($WP_core['page_path'].'/lib/message.encode.php');
include_once($WP_core['page_path'].'/lib/message.decode.php');
include_once($WP_core['page_path'].'/lib/pop3.inc.php');
include_once($WP_core['page_path'].'/lib/phm_streaming_smtp.php');
include_once($WP_core['page_path'].'/lib/phm_streaming_sendmail.php');
include_once($WP_core['page_path'].'/lib/phm_mime_handler.php');
$MIME = new phm_mime_handler($WP_core['conf_files'].'/mime.map.wpop');

// Magic Quotes deaktivieren!
@ini_set('magic_quotes_runtime', '0');
set_magic_quotes_runtime(0);

// Push array values into the keys
if (isset($WP_send['attach']) && is_array($WP_send['attach'])) {
    foreach ($WP_send['attach'] as $k => $v) {
        $WP_attach[$v] = 1;
    }
    unset($WP_send['attach']);
    $WP_send['attach'] = &$WP_attach;
}

if (isset($WP_send['send_action'])) {
    // Instantiate IDN class
    $IDN = new idna_convert();

    if ('bounce' == $WP_send['sendway']) {
        $to = parse_email_address($WP_send['to'], 0, true);

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
            $tpl = new FXL_Template($WP_core['skin_path'].'/templates/all.general.tpl');
            $tpl->assign
                    ('output', (isset($error)) ? $error
                                               : $WP_msg['noconnect'].' '.$_SESSION['WPs_popverbose'].'<br />'
                    );
            return;
        }

        $POP->retrieve($mail);
        while (substr($line = $POP->talk_ml(), 0, 2) != chr(13).chr(10)) {
            $mail_header .= $line;
        }
        // Parse den Header der Mail
        $mail_header = explode_822_header($mail_header, 0, 1);
        $WP_send['header'] = preg_replace('/\r\nEnvelope-To: ([^\r^\n]+)/i', '', $mail_header['prepared']);
        $WP_send['header'] = 'Envelope-To: '.$IDN->encode($to[0]).CRLF.$WP_send['header'];
        $WP_send['header'] = preg_replace('/\r\nReturn-Receipt-To: ([^\r^\n]+)/i', '', $WP_send['header']);
        // Durchreichen der Daten an sendmail / SMTP
        if ($WP_core['send_method'] == 'sendmail') {
            // Allow -f $1, where $1 is the From: Address
            $from = parse_email_address($mail_header['from']);
            $sendmail = preg_replace('!\ \-t!', '', str_replace('$1', $from[0], $WP_core['sendmail'])) . ' ' . $to[0];
            $sm = new phm_streaming_sendmail($sendmail);
            if ($moep = $sm->get_last_error() && $moep) $WP_return .= $moep.'<br />';
        }
        if ($WP_core['send_method'] == 'smtp') {
            $from = parse_email_address($mail_header['from'], 0, true);
            // If we have SMTP connection data for this profile, put this into session, else try to use the default
            // connection data
            if (isset($_SESSION['WPs_smtpserver']) && $_SESSION['WPs_smtpserver']) {
                $smtp_host     = $_SESSION['WPs_smtpserver'];
                $smtp_port     = ($_SESSION['WPs_smtpport']) ? $_SESSION['WPs_smtpport'] : 25;
                $smtp_user     = ($_SESSION['WPs_smtpuser']) ? $_SESSION['WPs_smtpuser'] : false;
                $smtp_pass     = ($_SESSION['WPs_smtppass']) ? $_SESSION['WPs_smtppass'] : false;
            } elseif (isset($WP_core['fix_smtp_host']) && $WP_core['fix_smtp_host']) {
                $smtp_host     = $WP_core['fix_smtp_host'];
                $smtp_port     = ($WP_core['fix_smtp_port']) ? $WP_core['fix_smtp_port'] : 25;
                $smtp_user     = (isset($WP_core['fix_smtp_user'])) ? $WP_core['fix_smtp_user'] : false;
                $smtp_pass     = (isset($WP_core['fix_smtp_pass'])) ? $WP_core['fix_smtp_pass'] : false;
            }
            //
            $sm = new phm_streaming_smtp($smtp_host, $smtp_port, $smtp_user, $smtp_pass);
            $sm->open_server($from[0], array($to[0]));
        }
        if ($sm) {
            $sm->put_data_to_stream($WP_send['header']);
            // Body von POP3 holen und an sendmail weiterreichen
            while ($line = $POP->talk_ml()) {
                $sm->put_data_to_stream($line);
            }
            // Make sure, there's a finalising CRLF.CRLF
            $sm->finish_transfer();
            if ($WP_core['send_method'] == 'sendmail') {
                if (!$sm->close()) {
                    $WP_return .= $WP_msg['nomailsent'].' ('.$sm->get_last_error().')<br />'.LF;
                } else {
                    $WP_return .= $WP_msg['mailsent'].'<br />'.LF;
                }
            }
            if ($WP_core['send_method'] == 'smtp') {
                if ($sm->check_success()) $WP_return .= $WP_msg['mailsent'].'<br />'.LF;
                else $WP_return .= $WP_msg['nomailsent'].'<br />'.LF;
                $sm->close();
            }
        } else $WP_return .= $WP_msg['nomailsent'].'<br />'.LF;
        $POP->close();

        $action = (isset($oldaction) && $oldaction) ? $oldaction : 'inbox';
        if ('bounce' == $action) {
            $pathadd = '&deleorig='.$deleorig.'&doothers='.$doothers;
            $_SESSION['bounceto'] = $WP_send['to'];
        } else $pathadd = '';
        header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action.'&mail='.$mail
                           .'&WP_return='.base64_encode($WP_return).'&'.give_passthrough(1).$pathadd);
        exit;
    } else {
        if (!isset($WP_send['from_profile']) && isset($_SESSION['WPs_profileID'])) {
            $WP_send['from_profile'] = $_SESSION['WPs_profileID'];
        }
        if (isset($WP_send['from_profile'])) {
            $useraccounts = $DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']);
            $connect = $DB->get_smtpconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $WP_send['from_profile'])
                     + $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $WP_send['from_profile']);
            $WP_send['from'] = $connect['address'];
            $real_name       = $connect['real_name'];
            // If we have SMTP connection data for this profile, put this into session, else try to use the default
            // connection data
            if (isset($connect['smtpserver']) && $connect['smtpserver']) {
                $smtp_host     = $connect['smtpserver'];
                $smtp_port     = ($connect['smtpport']) ? $connect['smtpport'] : 25;
                $smtp_user     = $connect['smtpuser'];
                $smtp_pass     = $connect['smtppass'];
                $smtpafterpop  = $connect['smtpafterpop'];
            } elseif (isset($WP_core['fix_smtp_host']) && $WP_core['fix_smtp_host']) {
                $smtp_host     = $WP_core['fix_smtp_host'];
                $smtp_port     = ($WP_core['fix_smtp_port']) ? $WP_core['fix_smtp_port'] : 25;
                $smtp_user     = (isset($WP_core['fix_smtp_user'])) ? $WP_core['fix_smtp_user'] : false;
                $smtp_pass     = (isset($WP_core['fix_smtp_pass'])) ? $WP_core['fix_smtp_pass'] : false;
                $smtpafterpop  = (isset($WP_core['fix_smtpafterpop'])) ? $WP_core['fix_smtpafterpop'] : false;
            }
            //
            if (1 == $smtpafterpop) {
                $connect = $DB->get_popconnect
                        ($_SESSION['WPs_uid']
                        ,$_SESSION['WPs_username']
                        ,$WP_send['from_profile']
                        );
                $popserver = $connect['popserver']; $popport = $connect['popport'];
                $popuser   = $connect['popuser'];   $poppass = $connect['poppass'];
                $killsleep = $connect['killsleep']; $profile = $WP_send['from_profile'];
                $popnoapop = $connect['popnoapop'];
            }
            if (!isset($WP_send['from'])) $error .= $WP_msg['notemail'].'<br />'.LF;
            elseif (isset($real_name)) $WP_send['from'] = $WP_send['from'].' ('.$real_name.')';
        } else {
            if (!isset($WP_send['from'])) $error .= '<b>'.$WP_msg['nofrom'].'</b><br />'.LF;
        }
        if (!isset($WP_send['to'])) $error.='<b>'.$WP_msg['noto'].'</b><br />'.LF;
        if (isset($error) && $error) {
            $WP_return .= $error;
            unset($error, $WP_send['send_action'], $mail_header);
            $WP_send[$WP_send['sendway']] = 'true';
        } else {
            if ('forward' == $WP_send['sendway']) {
                if (isset($WP_send['attach'])) {

                    $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
                    if ($POP->check_connected() == 'unconnected') {
                        $WP_exit = true;
                        $error = $POP->get_last_error();
                    } else {
                        if (!$POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'],
                                1 - $_SESSION['WPs_popapop'])) {
                            $WP_exit = true;
                            $error = $POP->get_last_error();
                        }
                    }
                    if (isset($WP_exit) && $WP_exit) {
                        $tpl = new FXL_Template($WP_core['skin_path'].'/templates/all.general.tpl');
                        $tpl->assign('output', (isset($error))
                                             ? $error
                                             : $WP_msg['noconnect'].' '.$_SESSION['WPs_popverbose'].'<br />');
                        return;
                    }
                    $POP->retrieve($mail);
                    // Ziehe den Header und parse ihn
                    list ($mail_header) = explode_mime_body($POP, -1, 0, 1);
                    if ('1.0' == trim($mail_header['mime'])) $decode_mime = 1;
                    $decode_type = $mail_header['content_type'];
                    $decode_detail = trim($mail_header['content_type_pad']);
                    if (strtolower($decode_type) == 'text/plain') $parts_text = 'true';
                    elseif (strtolower($decode_type) == 'text/html') $parts_html='true';
                    elseif (preg_match('!^multipart/!i', $decode_type)) { /* blubber */ }
                    elseif (strlen(trim($decode_type)) < 1) {
                        $decode_type = 'text/plain';
                        $parts_text = 'true';
                    }
                    $mail_encoding = trim(strtolower($mail_header['content_encoding']));
                    $decode_boundary = $mail_header['boundary'];
                    if (!isset($decode_boundary) && (isset($parts_text) || isset($parts_html))) $decode_mime = 0;
                }
            }
            // Wash given strings
            foreach (array('from', 'to', 'cc', 'bcc', 'subj', 'body', 'sign') as $k) {
                if (!isset($WP_send[$k])) continue;
                $WP_send[$k] = stripslashes(un_html($WP_send[$k]));
            }
            // Word Wrap body, append signature, find out content-type
            if (isset($WP_send['bodytype']) && 'text/html' == $WP_send['bodytype']) {
                if ('' != trim($WP_send['sign'])) {
                    $WP_send['body'] = trim($WP_send['body']).'<br /><br />'.nl2br($WP_send['sign']);
                }
                if ('true' == $WP_core['use_provsig']) {
                    $WP_send['body'] .= '<br />'.LF.'<br />'.LF;
                    $suf = fopen($WP_core['conf_files'].'/forced.signature.wpop', 'r');
                    while ($line = fgets($suf, 1024)) {
                        $WP_send['body'] .= nl2br($line);
                    }
                    fclose($suf);
                }
                $WP_send['body'] = str_replace(CRLF, LF, $WP_send['body']);
            } else {
                if ('' != trim($WP_send['sign'])) {
                     $WP_send['body'] = trim($WP_send['body']).LF.' '.LF.$WP_send['sign'];
                }
                if ('true' == $WP_core['use_provsig']) {
                    $WP_send['body'] .= ' '.LF;
                    $suf = fopen($WP_core['conf_files'].'/forced.signature.wpop', 'r');
                    while ($line = fgets($suf, 1024)) {
                        $WP_send['body'] .= $line;
                    }
                    fclose($suf);
                }
                // Ensure, there's just LF in the body
                $WP_send['body'] = str_replace(CRLF, LF, $WP_send['body']);
                $WP_send['bodytype'] = 'text/plain';
            }
            if ('1' == $WP_core['send_wordwrap'] && function_exists('Wordwrap')) {
                $WP_send['body'] = wordwrap($WP_send['body']);
            }
            // Kopie ins Postfach??
            if (isset($save_sent) && $save_sent) {
                $mycopy = preg_replace('!\(.+\)?!', '', $WP_send['from']);
                $WP_send['bcc'] = (isset($WP_send['bcc']) && $WP_send['bcc'])
                                ? $WP_send['bcc'] . ',' .$mycopy
                                : $mycopy;
                unset($mycopy);
            }
            if (isset($receipt_out) && $receipt_out) {
                if (!isset($the_body['additional'])) $the_body['additional'] = '';
                $the_body['additional'] = 'Return-Receipt-To: '.$WP_send['from'].CRLF.$the_body['additional'];
            }
            // On answering mails, refer to the original message ID
            if ('answer' == $WP_send['sendway'] && isset($_SESSION['in_reply_to'])) {
                if (!isset($the_body['additional'])) $the_body['additional'] = '';
                $the_body['additional'] = 'In-Reply-To: '.$_SESSION['in_reply_to'].CRLF.$the_body['additional'];
                unset($_SESSION['in_reply_to']);
            }
            if (!isset($the_body['additional'])) $the_body['additional'] = '';
            $the_body['additional'] = set_prio_headers($WP_send['importance']).$the_body['additional'];

            $mime_boundary = '_---_next_part_--_'.time().'==_';
            if (isset($WP_send['attach']) && is_array($WP_send['attach'])) {
                $mime_encoding = 1;
                $attachments = 1;
            }

            // Do we have uploaded files? Find at least one
            foreach ($_FILES['WP_upload']['tmp_name'] as $check) {
                if (is_uploaded_file($check)) {
                    $mime_encoding = 1;
                    $attachments = 1;
                    break;
                }
            }
            if (preg_match('/[\x80-\xff]/', $WP_send['body'])) {
                $mime_encoding = 1;
                $bodylines = explode(LF, $WP_send['body']);
                $WP_send['body'] = '';
                foreach ($bodylines as $value) {
                    $WP_send['body'] .= quoted_printable_encode($value);
                }
                unset($bodylines);
                $body_qp = 'true';
            }
            $WP_send['header'] = create_messageheader
                    ($WP_send['from'], $WP_send['to'], $WP_send['cc']
                    ,$WP_send['bcc'], $WP_send['subj'], $the_body['additional']
                    );
            if ($WP_core['send_method'] == 'sendmail') {
                $LE = LF;
                $WP_send['header'] = str_replace(CRLF, LF, $WP_send['header']);
                $WP_send['body']   = str_replace(CRLF, LF, $WP_send['body']);
                // Allow -f $1, where $1 is the From: Address
                $from = parse_email_address($WP_send['from']);
                $sendmail = str_replace('$1', $from[0], trim($WP_core['sendmail']));
                $sm = new phm_streaming_sendmail($sendmail);
                $moep = $sm->get_last_error();
                if ($moep) {
                    $sm = false;
                    $WP_return .= $moep.'<br />';
                }
            } elseif($WP_core['send_method'] == 'smtp') {
                $LE = CRLF;
                $to = explode(', ', gather_addresses(array
                      (
                       trim($WP_send['to']), trim($WP_send['cc']), trim($WP_send['bcc'])
                      )));
                $from = parse_email_address($WP_send['from'], 0, true);
                // SMTP after POP?
                if (1 == $smtpafterpop) {
                    $connect = $DB->get_popconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username']
                            ,$WP_send['from_profile']);
                    $POP = new pop3($connect['popserver'], $connect['popport'], $_SESSION['WPs_killsleep']);
                    if ($POP->check_connected() == 'unconnected') {
                        $WP_exit = true;
                        $WP_reutrn .= $POP->get_last_error();
                    } else {
                        if (!$POP->login($connect['popuser'], $connect['poppass'], 1 - $connect['popnoapop'])) {
                            $WP_exit = true;
                            $WP_reutrn .= $POP->get_last_error();
                        }
                    }
                    $POP->close();
                    unset($POP);
                }
                $sm = new phm_streaming_smtp($smtp_host, $smtp_port, $smtp_user, $smtp_pass);
                $server_open = $sm->open_server($from[0], $to);
                if (!$server_open) {
                    $WP_return .= $sm->get_last_error().'<br />'.LF;
                    $sm = false;
                }
            }
            if ($sm) {
                $sm->put_data_to_stream($WP_send['header']);
                if (isset($attachments) && 1 == $attachments)  {
                    $sm->put_data_to_stream('MIME-Version: 1.0'.$LE);
                    $sm->put_data_to_stream('Content-Type: multipart/mixed; boundary="'.$mime_boundary.'"'.$LE);
                    $sm->put_data_to_stream($LE);
                    $sm->put_data_to_stream('This is a multipart message in MIME format.'.$LE);
                    $sm->put_data_to_stream($LE.'--'.$mime_boundary.$LE);
                    if (isset($body_qp) && 'true' == $body_qp) {
                        $sm->put_data_to_stream('Content-Type: '.$WP_send['bodytype'].'; charset=iso-8859-1'.$LE);
                        $sm->put_data_to_stream('Content-Transfer-Encoding: quoted-printable'.$LE.$LE);
                    } else {
                        $sm->put_data_to_stream('Content-Type: '.$WP_send['bodytype'].'; charset=iso-8859-1'.$LE.$LE);
                    }
                    $sm->put_data_to_stream($WP_send['body']);
                    if (isset($WP_send['attach']) && is_array($WP_send['attach'])) {
                        pipe_mime_part(array
                                ('in' => $POP, 'out' => $sm, 'LE' => $LE
                                ,'in_boundary' => $mail_header['boundary']
                                ,'out_boundary' => $mime_boundary
                                ,'attach_list' => $WP_send['attach']
                                ));
                    }
                    // We allow numerous file uploads
                    foreach ($_FILES['WP_upload']['tmp_name'] as $key => $upload) {
                        if (is_uploaded_file($upload)) {
                            $sm->put_data_to_stream($LE.'--'.$mime_boundary.$LE);
                            put_attach_stream($sm, $upload, $_FILES['WP_upload']['type'][$key]
                                             ,$_FILES['WP_upload']['name'][$key], $LE);
                        }
                    }
                    $sm->put_data_to_stream($LE.'--'.$mime_boundary.'--'.$LE);
                } else {
                    if (isset($body_qp) && 'true' == $body_qp) {
                        $sm->put_data_to_stream('MIME-Version: 1.0'.$LE);
                        $sm->put_data_to_stream('Content-Type: '.$WP_send['bodytype'].'; charset=iso-8859-1'.$LE);
                        $sm->put_data_to_stream('Content-Transfer-Encoding: quoted-printable'.$LE);
                    }
                    $sm->put_data_to_stream($LE);
                    $sm->put_data_to_stream($WP_send['body']);
                }
                // Make sure, there's a finalising CRLF.CRLF
                $sm->finish_transfer();
                if ($WP_core['send_method'] == 'sendmail') {
                    if (!$sm->close()) {
                        $WP_return .= $WP_msg['nomailsent'].' ('.$sm->get_last_error().')<br />'.LF;
                        $success = false;
                    } else {
                        $WP_return .= $WP_msg['mailsent'].'<br />'.LF;
                        $success = true;
                    }
                }
                if ($WP_core['send_method'] == 'smtp') {
                    if ($sm->check_success()) {
                        $WP_return .= $WP_msg['mailsent'].'<br />'.LF;
                        $success = true;
                    } else {
                        $WP_return .= $WP_msg['nomailsent'].' ('.$sm->get_last_error().')<br />'.LF;
                        $success = false;
                    }
                    $sm->close();
                }
                if ($success) {
                    $action = (isset($oldaction) && $oldaction) ? $oldaction : 'inbox';
                    header('Location: '.$_SERVER['PHP_SELF'].'?action='.$action.'&mail='.$mail
                            .'&WP_return='.base64_encode($WP_return).'&'.give_passthrough(1));
                    exit;
                } else {
                    unset($WP_send['send_action']);
                }
            } else {
                $WP_return .= $WP_msg['nomailsent'].'<br />'.LF;
                unset($WP_send['send_action']);
            }
        }
    }
}

if (isset($WP_send['send_sig']) || isset($WP_send['send_valid'])) {
    $WP_send['reload'] = '1';
    // Reinsert protected user input
    foreach (array('importance', 'attach_mode', 'copytobox', 'receipt_out', 'subj'
            ,'body', 'from', 'to', 'cc', 'bcc') as $k) {
        if (!isset($WP_send[$k])) continue;
        $WP_save[$k] = stripslashes($WP_send[$k]);
        unset($WP_send[$k]);
    }
    $WP_save_attach = isset($WP_send['attach']) ? $WP_send['attach'] : false;
    if (isset($WP_send['send_valid'])) {
        $WP_send['invalid'] = email_check_validity(array
                (trim($WP_save['to']), trim($WP_save['cc']), trim($WP_save['bcc'])));
    }
}
if (isset($WP_send['bounce'])) {
    $WP_send['sendway'] = 'bounce';
    $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
    $POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop']);
    $mail_header = explode_822_header($POP->top($mail));
    $POP->close();
} elseif (isset($WP_send['forward']) || isset($WP_send['answer']) || isset($WP_send['answerAll'])
        || isset($WP_send['reload'])) {
    if (isset($mail) && $mail) {
        $WP_core['output'] = 'true';
        unset($WP_send['body'], $mimebody);
        if (isset($WP_send['forward']) && $WP_send['forward']) $WP_send['sendway'] = 'forward';
        if ((isset($WP_send['answer']) && $WP_send['answer'])
                || (isset($WP_send['answerAll']) && $WP_send['answerAll'])) {
            $WP_send['sendway'] = 'answer';
        }
        $POP = new pop3($_SESSION['WPs_popserver'], $_SESSION['WPs_popport'], $_SESSION['WPs_killsleep']);
        $POP->login($_SESSION['WPs_popuser'], $_SESSION['WPs_poppass'], 1 - $_SESSION['WPs_popapop']);
        $POP->retrieve($mail);
        list($mail_header, $mimebody) = explode_mime_body($POP, -1, 0, 0);

        $mail_header['date'] = strtotime($mail_header['date']);
        if (-1 == $mail_header['date']) {
            $mail_header['date'] = '---';
        } else {
            $mail_header['date'] = htmlspecialchars(date($WP_msg['dateformat'], $mail_header['date']));
        }
        $to = parse_email_address($mail_header['to']);
        $mail_to = $to[0];
        if (isset($to[1])) $mail_to .= ' ('.$to[1].')';
        // Find Reply-To and use it
        if (isset($mail_header['replyto']) && $mail_header['replyto']) $mail_header['from'] = &$mail_header['replyto'];
        if (isset($WP_send['answerAll'])) {
            $mail_from = gather_addresses(array(trim($mail_header['to'])
                        ,trim($mail_header['from']), trim($mail_header['cc'])));
            $WP_send['answer'] = $WP_send['answerAll'];
            unset($WP_send['answerAll']);
        } else {
            $from = parse_email_address($mail_header['from']);
            $mail_from = $from[0];
            if (isset($from[1]) && $from[1]) $mail_from .= ' ('.$from[1].')';
        }
        $WP_send['importance'] = $mail_header['importance'];
        $WP_send['subj'] = $mail_header['subject'];
        if (1 == $decode_mime) {
            if (sizeof($mimebody['part_type']) == 0) {
                $mimebody['part_type'][0] = $mail_header['content_type'];
                $mimebody['part_encoding'][0] = &$mail_header['content_encoding'];
                if (strtolower($mail_header['content_type']) == 'text/plain') $parts_text = 'true';
                elseif (strtolower($mail_header['content_type']) == 'text/html') $parts_html = 'true';
                else {
                    $mimebody['part_attached'][0] = '1';
                    $parts_attach = 'true';
                }
            }
            if (sizeof($mimebody['part_attached']) < 1) $parts_attach = 'false';
        } else {
            $active_part = -1;
            $mimebody['part_type'][-1] = $mail_header['content_type'];
            $mimebody['part_encoding'][-1] = $mail_header['content_encoding'];
        }
        // Sichtbaren Teil dekodieren und für Ausgabe vorbereiten
        if (strtolower($mimebody['part_encoding'][$active_part]) == 'quoted-printable') {
            $mailbody = quoted_printable_decode(str_replace('='.CRLF, '', $mailbody));
        } elseif (strtolower($mimebody['part_encoding'][$active_part]) == 'base64') {
            $mailbody = base64_decode($mailbody);
        }

        $WP_send['body'] = '> '.preg_replace('!'.CRLF.'!', CRLF.'> ', $mailbody);

        // Filter der Sendeaktion
        if (isset($WP_send['forward']) || isset($WP_send['answer'])) {
            $WP_send['body'] = $WP_msg['headoldmsg'].CRLF.$WP_msg['date'].': '.$mail_header['date']
                              .CRLF.$WP_msg['from'].': '.$mail_header['from'].CRLF.$WP_msg['to']
                              .': '.$mail_header['to'].CRLF.$WP_msg['subject'].': '.$WP_send['subj'].CRLF
                              .' '.CRLF.$WP_send['body'];
            $WP_send['subj'] = $WP_msg['fwd'].': '.preg_replace('!(Re|AW|WG|Fwd):( ){0,1}!i', '', $WP_send['subj']);
            if (isset($WP_send['answer'])) {
                $WP_send['subj'] = $WP_msg['re'].': '.preg_replace('!(Re|AW|WG|Fwd):( ){0,1}!i','',$WP_send['subj']);
                $WP_send['to'] = $mail_from;
                $parts_attach = 'false';
                unset($part_attached);
                if (isset($mail_header['message_id']))
                $_SESSION['in_reply_to'] = trim($mail_header['message_id']);
            }
        }
    }
} else {
    // Wash request data
    foreach (array('from', 'to', 'cc', 'bcc', 'subj', 'body', 'sign') as $k) {
        if (!isset($WP_send[$k])) continue;
        $WP_send[$k] = stripslashes(un_html($WP_send[$k]));
    }
}

if (!isset($WP_send['send_action']) || !$WP_send['send_action']) {
    if (isset($WP_send['reload'])) {
        unset($WP_send['sign']);
        foreach (array('body', 'from', 'to', 'cc', 'bcc', 'importance', 'subj'
                ,'attach_mode', 'importance', 'copytobox', 'receipt_out') as $k) {
            if (!isset($WP_save[$k])) continue;
            $WP_send[$k] = $WP_save[$k];
            unset($WP_save[$k]);
        }
        $WP_send['attach'] = &$WP_save_attach;
        if (isset($WP_send['sendway']) && $WP_send['sendway'] == 'answer') {
            $parts_attach = 'false';
            unset($part_attached);
        }
    }
    if (isset($body_qp) && $body_qp == 'true' && isset($WP_send['body'])) {
        $WP_send['body'] = quoted_printable_decode($WP_send['body']);
    }

    if (!isset($WP_send['from_profile'])) {
        if (isset($_SESSION['WPs_profileID']) && $_SESSION['WPs_profileID']) {
            $WP_send['from_profile'] = $_SESSION['WPs_profileID'];
        } else {
            foreach ($DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']) as $thefirst => $x) break;
            $_SESSION['WPs_profileID'] = $WP_send['from_profile'] = $thefirst;
        }
    }
    if (!isset($WP_send['sign']) && isset($WP_send['from_profile']))  {
        if ($connect = $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $WP_send['from_profile'])) {
            $sig_on    = $connect['sig_on'];
            $signature = $connect['signature'];
        }
        if (isset($sig_on) && $sig_on) $WP_send['sign'] = $signature;
    }
    if (isset($WP_send['invalid']) && !empty($WP_send['invalid'])) {
        foreach ($WP_send['invalid'] as $k => $val) {
            if ('' == $val) continue;
            $WP_return .= '<strong>'.$WP_msg['invalidemail'].'</strong>: '.$val.'<br />'.LF;
        }
    }
    $tpl = new FXL_Template($WP_core['skin_path'].'/templates/send.general.tpl');
    if (isset($WP_return) && $WP_return) {
        $t_error = $tpl->getBlock('error');
        $t_error->assign('error', $WP_return);
        $tpl->assign('error', $t_error);
    }
    $t_putMeBack = (isset($WP_send['sendway']) && 'bounce' == $WP_send['sendway']) ? 'on_bounce' : 'full';
    $t_current = $tpl->getBlock($t_putMeBack);
    if (!isset($WP_send['sendway']) || 'bounce' != $WP_send['sendway']) {
        if ((!isset($WP_send['from_profile']) || !$WP_send['from_profile'])
                && (!isset($WP_send['from']) || !$WP_send['from'])
                && isset($mail_to) && $mail_to) {
            $WP_send['from'] = $mail_to;
        }
        $t_current->assign('msg_from', $WP_msg['from']);
        $useraccounts = $DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']);
        if (is_array($useraccounts)) {
            if (sizeof($useraccounts) > 1 ||
                    (sizeof($useraccounts) > 0
                    && isset($WP_core['allow_man'])
                    && 'true' == $WP_core['allow_man'])
                    ) {
                $t_acc = $t_current->getBlock('on_account');
                $t_men = $t_acc->getBlock('accmenu');
                foreach ($useraccounts as $k => $profilenm) {
                    $profiledata = $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);
                    if (1 == trim($profiledata['acc_on'])) {
                        $t_men->assign('counter', $k);
                        $t_men->assign('profilenm', $profilenm);
                        if ($WP_send['from_profile'] != '' && $WP_send['from_profile'] == $k) {
                            $t_men->assign_block('selected');
                        }
                        $t_acc->assign('accmenu', $t_men);
                        $t_men->clear();
                    }
                }
                $t_acc->assign('msg_sigload', $WP_msg['sigload']);
                $t_current->assign('on_account', $t_acc);
            } else {
                $t_acc = $t_current->getBlock('one_account');
                foreach ($useraccounts as $k => $profilenm) break;
                $profiledata = $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);
                $t_acc->assign('from', $profilenm);
                $t_acc->assign('address', $profiledata['address']);
                $t_acc->assign('profile', $k);
                if (!isset($WP_send['sign'])) $WP_send['sign'] = $profiledata['signature'];
                $t_current->assign('one_account', $t_acc);
            }
        }
        $t_current->assign(array
                ('cc' => isset($WP_send['cc']) ? htmlspecialchars($WP_send['cc']) : ''
                ,'bcc' => isset($WP_send['bcc']) ? htmlspecialchars($WP_send['bcc']) : ''
                ,'msg_subject' => $WP_msg['subject']
                ,'subject' => isset($WP_send['subj']) ? htmlspecialchars($WP_send['subj']) : ''
                ,'msg_copytobox' => $WP_msg['copytobox']
                ,'msg_prio' => $WP_msg['prio']
                ,'msg_receipt_out' => $WP_msg['receipt_out']
                ,'msg_confirm_no_subject' => $WP_msg['confirm_no_subject']
                ));

        if (!isset($WP_send['reload'])) $WP_send['reload'] = false;
        if (!isset($WP_send['importance'])) $WP_send['importance'] = false;
        if (!isset($WP_send['body'])) $WP_send['body'] = false;
        if (!isset($WP_send['sign'])) $WP_send['sign'] = false;
        if (!isset($receipt_out)) $receipt_out = false;
        if (!isset($save_Sent)) $save_sent = false;

        if ($WP_send['reload'] == 1 && !$save_sent) $WP_core['save_sent'] = false;
        if (1 == $WP_core['save_sent'] || 1 == $save_sent) $t_current->assign_block('savesent_check');
        if ($WP_send['reload'] == 1 && !$receipt_out) $WP_core['receipt_out'] = false;
        if (1 == $WP_core['receipt_out'] || 1 == $receipt_out) $t_current->assign_block('receipt_check');
        if (isset($WP_core['allow_checkAdr']) && $WP_core['allow_checkAdr']
                && function_exists('getmxrr')) {
            $t_check = $t_current->getBlock('addrcheck');
            $t_check->assign('msg_valid', $WP_msg['checkvalidity']);
            $t_current->assign('addrcheck', $t_check);
        }
        $t_pmen = $t_current->getBlock('priomen');
        $t_pmen->assign('prioval', ''); $t_pmen->assign('priotxt', $WP_msg['egal']);
        $t_pmen->getBlock('priosel'); $t_pmen->assign('priosel','');
        $t_current->assign('priomen', $t_pmen);
        $t_pmen->clear();
        $t_pmen->assign('prioval','1'); $t_pmen->assign('priotxt', $WP_msg['high']);
        if ($WP_send['importance'] == 1) $t_pmen->assign_block('priosel');
        $t_current->assign('priomen', $t_pmen);
        $t_pmen->clear();
        $t_pmen->assign('prioval','3'); $t_pmen->assign('priotxt', $WP_msg['normal']);
        if ($WP_send['importance'] == 3) $t_pmen->assign_block('priosel');
        $t_current->assign('priomen', $t_pmen);
        $t_pmen->clear();
        $t_pmen->assign('prioval','5'); $t_pmen->assign('priotxt', $WP_msg['low']);
        if ($WP_send['importance'] == 5) $t_pmen->assign_block('priosel');
        $t_current->assign('priomen', $t_pmen);
        $t_pmen->clear();

        $t_current->assign(array
            ('body' => htmlspecialchars($WP_send['body'])
            ,'msg_del' => $WP_msg['del']
            ,'maxupload' => $WP_core['maxupload']
            ,'sign' => htmlspecialchars($WP_send['sign'])
            ,'msg_attach' => $WP_msg['attach']
            ,'msg_sig' => $WP_msg['sig']
        ));
        if (isset($WP_ext['send_attach'])) {
            eval($WP_ext['send_attach']);
            $t_current->assign('ext_send_attach', $WP_ext['send_attach']);
        }
        if (isset($parts_attach) && $parts_attach == 'true') {
            $tpl_a = $t_current->getblock('attachblock');
            $return = get_visible_attachments
                    ($mimebody, (isset($attach) ? $attach : false), 'boxes', $WP_core['skin_path'].'/mime'
                    );
            $tpl_al = $tpl_a->getblock('attachline');
            foreach ($return['img'] as $key => $value) {
               if (isset($WP_send['attach']) && $WP_send['attach'][$key]) $tpl_al->assign_block('attsel');
                $tpl_al->assign('att_icon', $WP_core['skin_path'].'/mime/'.$value);
                $tpl_al->assign('att_num', $key);
                $tpl_al->assign('att_icon_alt', $return['img_alt'][$key]);
                $tpl_al->assign('att_name', $return['name'][$key]);
                $tpl_al->assign('att_size', $return['size'][$key]);
                $tpl_al->assign('msg_att_type', $WP_msg['filetype']);
                $tpl_al->assign('att_type', $return['filetype'][$key]);
                $tpl_a->assign('attachline', $tpl_al);
                $tpl_al->clear();
            }
            $tpl_a->assign('msg_attachs', $WP_msg['attachs']);
            $tpl_a->assign('msg_selection', $WP_msg['selection']);
            $tpl_a->assign('msg_all', $WP_msg['all']);
            $tpl_a->assign('msg_none', $WP_msg['none']);
            $t_current->assign('attachblock', $tpl_a);
        }
    }
    $t_current->assign(array
            ('to' => isset($WP_send['to']) ? htmlspecialchars($WP_send['to']) : ''
            ,'bounceto' => isset($WP_send['to']) ? htmlspecialchars($WP_send['to']) : ''
            ,'input_sendto' => isset($WP_core['input_sendto']) ? $WP_core['input_sendto'] : ''
            ));
    if (isset($WP_ext['send_to'])) {
        eval($WP_ext['send_to']);
        $t_current->assign('ext_send_to', $WP_ext['send_to']);
    }
    $t_current->assign('msg_to', $WP_msg['to']);
    $t_current->assign('msg_bounceto', $WP_msg['bounce_to']);
    $t_current->assign('msg_bounce', $WP_msg['bounce']);
    $t_current->assign('deleorig', $WP_msg['killorig']);
    // Bouncey, bouncey
    if (isset($WP_send['sendway']) && 'bounce' == $WP_send['sendway']) {
        // DO OTHERS aktiv
        if (isset($_SESSION['dootherslist'])) {
            foreach ($_SESSION['dootherslist'] as $mail => $WP_send['to']) break;
            unset($_SESSION['dootherslist'][$mail]);
            $link = $_SERVER['PHP_SELF'].'?action=send&oldaction=bounce&mail='.$mail
                   .'&WP_send[sendway]=bounce&WP_send[to]='.urlencode($WP_send['to'])
                   .'&WP_send[send_action]=1&'.give_passthrough(1);
            if (!isset($WP_skin['metainfo'])) $WP_skin['metainfo'] = '';
            $WP_skin['metainfo'] .= '<meta http-equiv="refresh" content="5; URL='.$link.'">'.LF;
            $t_redi = $t_current->getblock('redir');
            $t_redi->assign('msg_redir', $WP_msg['bounce_redirect']);
            $t_current->assign('redir', $t_redi);
            $t_current->assign('bounceto', $WP_send['to']);
        } elseif (isset($_SESSION['bouncelist']) && !empty($_SESSION['bouncelist'])) {
            $t_oth = $t_current->getblock('forothers');
            $t_oth->assign('msg_doothers', $WP_msg['bounce_others']);
            $t_current->assign('forothers', $t_oth);
        }
        if (isset($_SESSION['bounceddeletelist'][$mail])) {
            $t_current->assign_block('delesel');
        }
        if (strlen($mail_header['subject']) > $WP_skin['length_links']) {
            $mail_header['subject'] = substr($mail_header['subject'], 0, ($WP_skin['length_links'] - 3)) . '...';
        }
        $t_break = $tpl->getblock('bouncebreaker');
        $t_break->assign(array
                 ('cancel' => $WP_msg['cancel']
                 ,'link_target' => $_SERVER['PHP_SELF'].'?action=bounce&break_bounce=reset&oldaction='
                                  .$oldaction.'&'.give_passthrough(1)
                 ));
        $tpl->assign('bouncebreaker', $t_break);
        $t_head = $t_current->getblock('header');
        $t_head->assign(array
                ('msg_from' => $WP_msg['from'], 'from' => htmlspecialchars($mail_header['from'])
                ,'msg_to' => $WP_msg['to'], 'msg_subj' => $WP_msg['subject']
                ,'to' => htmlspecialchars($mail_header['to']), 'msg_still' => $WP_msg['bounce_stillinlist']
                ,'still' => isset($_SESSION['bouncelist']) ? sizeof($_SESSION['bouncelist']) : 0
                ,'subj' => htmlspecialchars($mail_header['subject'])
                ));
        $t_current->assign('header', $t_head);
    }
    $tpl->assign(array
            ($t_putMeBack => $t_current
            ,'PHP_SELF' => $_SERVER['PHP_SELF']
            ,'msg_send' => $WP_msg['send']
            ,'action' => isset($action) ? $action : ''
            ,'oldaction' => isset($oldaction) ? $oldaction : ''
            ,'passthrough_2' => give_passthrough(2)
            ,'mail' => isset($mail) ? $mail : ''
            ,'from_profile' => $WP_send['from_profile']
            ,'sendway' => (isset($WP_send['sendway'])) ? $WP_send['sendway'] : ''
            ));
}

?>
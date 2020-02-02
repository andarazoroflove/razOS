<?php
/* ------------------------------------------------------------------------- */
/* lib/message.decode.php - PHlyMail Pro 1.0.0+                              */
/* Routines related to decoding emails                                       */
/* (c) 2001-2005 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Yokohama Default Branch                                          */
/* v1.6.6                                                                    */
/* ------------------------------------------------------------------------- */

$WP_libload['decode'] = TRUE;

function parse_email_address ($address = '', $shorten_to = 0, $encode = FALSE)
{
    $idn_method = ($encode) ? 'encode' : 'decode';
    // Instantiate IDNA class
    $IDN = new idna_convert();

    $address = str_replace('"', '', $address);
    if (preg_match('!^(.+)<(.+)>$!', trim($address), $found)) {
        // Real Name <Em@il>
        if ($shorten_to && strlen($found[1]) > $shorten_to) {
            $found[1] = substr($found[1], 0, ($shorten_to - 3)) . '...';
        }
        return array
                (0 => ($encode) ? trim($IDN->{$idn_method}($found[2])) : trim($found[2])
                ,1 => trim($found[1])
                ,2 => trim($found[1].' <'.$IDN->{$idn_method}($found[2]).'>')
                );
    } elseif (preg_match('!(.+)[\(](.+?)[\)]!U', trim($address), $found)) {
        // Em@il (Real Name)
        if ($shorten_to && strlen($found[2]) > $shorten_to) {
            $found[2] = substr($found[2], 0, ($shorten_to - 3)) . '...';
        }
        return array
                (0 => ($encode) ? trim($IDN->{$idn_method}($found[1])) : trim($found[1])
                ,1 => trim($found[2])
                ,2 => trim($found[2].' <'.$IDN->{$idn_method}($found[1]).'>')
                );
    } else {
        $address = preg_replace('![<>]!', '', trim($address));
        $return[0] = $return[1] = $return[2] = $IDN->{$idn_method}($address);
        if ($shorten_to && strlen($return[1]) > $shorten_to) {
            $return[1] = substr($return[1], 0, ($shorten_to-3)) . '...';
        }

        return $return;
    }
}

function decode_1522_line_q($coded = '')
{
    if (!$coded) return '';
    return quoted_printable_decode(str_replace('_', '=20', $coded));
}

function decode_1522_line_b($coded = '')
{
    if (!$coded) return '';
    return base64_decode($coded);
}

function explode_mime_header($mime_head = '')
{
    // Beachte RFC 1522 (MIME-extended Mail header lines)
    $mime_head = preg_replace
                 (array('/\=\?[^\s]+\?q\?([^\r\n]*)\?\=/Uie', '/\=\?[^\s]+\?b\?([^\r\n]*)\?\=/Uie')
                 ,array("decode_1522_line_q('\\1')", "decode_1522_line_b('\\1')")
                 ,$mime_head
                 );
    // Unfolding langer Zeilen
    $mime_head = preg_replace('/\r\n([\ \t]+)/', ' ', $mime_head);

    if (preg_match('/^Content-Type: ([-\/\.0-9a-z]+)(; ([^\r\n\t]+))?/mi', $mime_head, $found)) {
        $return['content_type'] = $found[1];
        $return['content_type_pad'] = (isset($found[3]) && $found[3]) ? trim($found[3]) : false;
    } else {
        $return['content_type'] = $return['content_type_pad'] = false;
    }
    if (preg_match('/^Content-Disposition: ([-\/\.0-9a-z]+)(;\s([^\s\r\n;]+))?/mi', $mime_head, $found)) {
        $return['content_disposition'] = trim($found[1]);
        $return['content_dispo_pad'] = (isset($found[3]) && $found[3]) ? trim($found[3]) : false;
    } else {
        $return['content_disposition'] = $return['content_dispo_pad'] = false;
    }
    foreach (array
            (array('!^Content-Description:\ (.+)$!mi',       'content_description', 1)
            ,array('!^Content-Transfer-Encoding:\ (.+)$!mi', 'content_encoding',    1)
            ,array('/boundary=(\"*)([^\r^\n^\"]+)(\"*)/i',   'boundary',            2)
            ,array('!^Comment:\ (.+)$!mi',                   'comment',             1)
            ) as $needle) {
        if (preg_match($needle[0], $mime_head, $found)) {
            $return[$needle[1]] = trim($found[$needle[2]]);
        } else {
            $return[$needle[1]] = false;
        }
    }
    // Für Rohansichten -> kompletter, aber dekodierter und unfolded Header
    $return['complete'] = $mime_head;
    return $return;
}

function explode_822_header($mail_head = '', $fill_empty_fields = 1, $return_prepared = 0)
{
    // Pay attention to RFC 1522 (MIME-extended Mail header lines)
    $mail_head = preg_replace
                 (array('/\=\?[^\s]+\?q\?([^\r\n]*)\?\=/Uie', '/\=\?[^\s]+\?b\?([^\r\n]*)\?\=/Uie')
                 ,array("decode_1522_line_q('\\1')", "decode_1522_line_b('\\1')")
                 ,$mail_head
                 );
    // Unfolding long header lines
    $mail_head = preg_replace('/\r\n([\ \t]+)/', ' ', $mail_head);

    // Special case: We also need the optinal additional information
    if (preg_match('/\r\nContent-Type: ([-\/\.0-9a-z]+)(; ([^\r\n\t]+))*/i', $mail_head, $found)) {
        $return['content_type'] = trim($found[1]);
        if (isset($found[3])) {
            $return['content_type_pad'] = trim($found[3]);
        }
    } else {
        $return['content_type'] = $return['content_type_pad'] = false;
    }
    // Find the various header fields, if not matched, initialise at least the array offset
    foreach (array
            (array('!^MIME-Version:\ (.+)$!mi',              'mime',             1)
            ,array('!^Content-Transfer-Encoding:\ (.+)$!mi', 'content_encoding', 1)
            ,array('/boundary=(\"*)([^\r^\n^\"]+)(\"*)/i',   'boundary',         2)
            ,array('!^Subject:(\ )? (.+)$!mi',               'subject',          2)
            ,array('!^From:\ (.+)$!mi',                      'from',             1)
            ,array('!^Reply-To:\ (.+)$!mi',                  'replyto',          1)
            ,array('!^To:\ (.+)$!mi',                        'to',               1)
            ,array('!^Date:\ (.+)$!mi',                      'date',             1)
            ,array('!^Delivery-Date:\ (.+)$!mi',             'delivery_date',    1)
            ,array('!^X-Mailer:\ (.+)$!mi',                  'mailer',           1)
            ,array('!^Comment:\ (.+)$!mi',                   'comment',          1)
            ,array('!^Message-ID:\ (.+)$!mi',                'message_id',       1)
            /* By parsing the common priority fields in this order, the more
               standardized ones take precedence, but none is left out */
            ,array('!^X-MSMail-Priority:\ (.+)$!mi',         'importance',       1)
            ,array('!^Importance:\ (.+)$!mi',                'importance',       1)
            ,array('!^X-Priority:\ (.+)$!mi',                'importance',       1)
            ) as $needle) {
        if (preg_match($needle[0], $mail_head, $found)) {
            $return[$needle[1]] = trim($found[$needle[2]]);
        } else {
            $return[$needle[1]] = false;
        }
    }

    // The MIME version *must* be 1.0, so drop everything not a number / a dot
    $return['mime'] = preg_replace('![^0-9.]!', '', $return['mime']);

    // Priority settings should be integer values between 1 and 5
    switch ($return['importance']) {
    case 'High':   $return['importance'] = 1; break;
    case 'Normal': $return['importance'] = 3; break;
    case 'Low':    $return['importance'] = 5; break;
    default:       $return['importance'] = 3; break;
    }
    // For raw views -> complete, but decoded and unfolded header
    if ($return_prepared == 0) {
        // making up for key => value pairs, unfolded
        preg_match_all('!^([-a-z0-9]+):\ ?(.+)$!mi', $mail_head, $return['complete']);
    } else {
        // Just unfolded and decoded
        $return['prepared'] = $mail_head;
    }
    return $return;
}

function explode_mime_body(&$filehandle, $ReturnThis = -1, $is_inline = 0, $HeaderOnly = 0, $Enclosing = '')
{
    if (!isset($GLOBALS['mailbody'])) $GLOBALS['mailbody'] = FALSE;
    if ($ReturnThis != -1) {
        if (strstr('.', $ReturnThis)) {
            list ($ReturnThis, $PassOn) = explode('.', $ReturnThis, 2);
        }
    }
    if (!isset($PassOn) || !$PassOn) $PassOn = -1;
    if ($filehandle) {
        $GLOBALS['mailbody'] = $GLOBALS['active_part'] = $GLOBALS['decode_mime'] = FALSE;
        // Ziehe den Header
        $mail_header = '';
        while (substr($line = $filehandle->talk_ml(), 0, 2) != chr(13).chr(10)) {
            $mail_header .= $line;
        }
        // Parse den Header der Mail
        $mail_header = explode_822_header($mail_header);
        if ('1.0' == trim($mail_header['mime'])) $GLOBALS['decode_mime'] = 1;
        if (strtolower($mail_header['content_type']) == 'text/plain') {
            $parts_text = 'true';
        } elseif (strtolower($mail_header['content_type']) == 'text/html') {
            $parts_html = 'true';
        } elseif (preg_match('!^multipart/!i', $mail_header['content_type'])) {
            if (!$GLOBALS['decode_mime']) {
                $GLOBALS['x_robust_warning'] = 'Warning! This mail is not standards conformant!';
            }
            $GLOBALS['decode_mime'] = 1;
            $boundary_stack = array();
        } elseif (trim($mail_header['content_type']) == '') {
            $mail_header['content_type'] = 'text/plain';
            $parts_text = 'true';
        }

        $boundary = isset($mail_header['boundary']) ? $mail_header['boundary'] : FALSE;
        if (!$boundary && (isset($parts_text) || isset($parts_html))) $GLOBALS['decode_mime'] = 0;
        if (isset($mail_header['content_encoding'])) {
            $mail_header['content_encoding'] = trim(strtolower($mail_header['content_encoding']));
        }
        if (isset($mail_header['mailer'])) {
            $mail_header['mailer'] = htmlspecialchars($mail_header['mailer']);
        }
        if (isset($mail_header['comment'])) {
            $mail_header['comment'] = htmlspecialchars($mail_header['comment']);
        }
        // Ende Header-Parsing

        // Zurück, wenn nur Header geparst werden sollte
        if (1 == $HeaderOnly) return array($mail_header, FALSE);
        // Ab dafür
        $proc_mode = 'none';
        $id = 0;
        $GLOBALS['active_part'] = -1;
        $end_reached = 0;
        $bytes_read = 0;
        $bytes_block = 8192;
        $next_mode = false;
        // Exception One Attachment only Email
        if (!$boundary && 1 == $GLOBALS['decode_mime'] && 0 == $ReturnThis) {
            $mimebody['part_type'][0]     = $mail_header['content_type'];
            $mimebody['part_detail'][0]   = $mail_header['content_type_pad'];
            $mimebody['part_encoding'][0] = $mail_header['content_encoding'];
            $proc_mode = 'outhead';
        }
        while ($end_reached == 0) {
            if ('finalise' != $proc_mode) {
                $line = $filehandle->talk_ml();
                if (!$line) $proc_mode = 'finalise';
                elseif ('' != $Enclosing) {
                    if ($line == '--'.$Enclosing.CRLF) {
                        $proc_mode = 'finalise';
                    } elseif ($line == '--'.$Enclosing.'--'.CRLF) {
                        $proc_mode = 'finalise';
                    }
                }
            }
            if (1 == $GLOBALS['decode_mime']) {
                if ('leaveout' == $proc_mode) $proc_mode = $next_mode;
                if ('noop' == $proc_mode) continue;
                if ('none' == $proc_mode) {
                    if ('--'.$boundary == trim($line)) $proc_mode = 'addhead';
                    $mimebody['text'][$id] = isset($mimebody['text'][$id])
                                           ? $mimebody['text'][$id] + strlen($line)
                                           : strlen($line);
                }
                if ('parsehead' == $proc_mode) {
                    if (isset($head) && trim($head) != '') {
                        $head = explode_mime_header(CRLF.$head.CRLF);
                        $mimebody['part_type'][$id] = strtolower($head['content_type']);
                        if (isset($head['content_type_pad'])) {
                            $mimebody['part_detail'][$id] = $head['content_type_pad'];
                        }
                        if (isset($head['content_disposition'])) {
                            $mimebody['dispo'][$id] = $head['content_disposition'];
                        }
                        if (isset($head['content_dispo_pad'])) {
                            $mimebody['dispo_pad'][$id] = $head['content_dispo_pad'];
                        }
                        $mimebody['part_encoding'][$id] = isset($head['content_encoding'])
                                                        ? strtolower($head['content_encoding']) : false;
                        if (isset($head['content_description'])) {
                            $mimebody['part_description'][$id] = $head['content_description'];
                        }
                        if (preg_match('/^multipart/i', $mimebody['part_type'][$id])) {
                            $boundary_stack[] = $boundary;
                            $boundary = $head['boundary'];
                        }
                        unset($head);
                        if ($ReturnThis == $id) {
                            if (-1 != $PassOn or 1 == $is_inline) {
                                $proc_mode = 'outbody';
                            } else {
                                $proc_mode = 'outhead';
                            }
                        } else {
                            $proc_mode = 'addbody';
                        }
                    } else {
                        $proc_mode = 'none';
                        continue;
                    }
                }
                if ('finalise' == $proc_mode) {
                    if (isset($mimebody['part_type'][$id])) $proc_mode = 'parsebody';
                    $end_reached = 1;
                }
                if ('outhead' == $proc_mode) {
                    if (isset($mimebody['part_detail'][$id])
                            && preg_match('/name="(.*)"/i', $mimebody['part_detail'][$id], $found)) {
                        $filename = $found[1];
                    } elseif (isset($mimebody['dispo_pad'][$id])
                            && preg_match('/name="(.*)"/i', $mimebody['dispo_pad'][$id], $found)) {
                        $filename = $found[1];
                    } else {
                        $filename = 'noname';
                    }
                    header('Content-Type: '.$mimebody['part_type'][$id]);
                    header('Content-Disposition: filename='.$filename);
                    $proc_mode = 'outbody';
                }
                if ('parsebody' == $proc_mode) {
                    if ($GLOBALS['mailbody'] && $GLOBALS['active_part'] == -1)  {
                        $GLOBALS['active_part'] = $id;
                    } elseif(preg_match('/^multipart/i', $mimebody['part_type'][$id])) {
                    } else {
                        $GLOBALS['parts_attach'] = 'true';
                        $mimebody['part_attached'][$id] = 1;
                    }
                    ++$id;
                    if ('leaveout' == $next_mode) {
                        $proc_mode = $next_mode;
                        $next_mode = 'addhead';
                    } else $proc_mode = 'addhead';
                }
                if ('addhead' == $proc_mode) {
                    if (CRLF == $line) {
                        $proc_mode = 'parsehead';
                        continue;
                    } else {
                        $head = (isset($head)) ? $head.$line : $line;
                    }
                }
                if ('outbody' == $proc_mode) {
                    if ('--'.$boundary.'--' == trim($line)) {
                        if (!empty($boundary_stack)) {
                            $boundary = array_pop($boundary_stack);
                        }
                        $proc_mode = 'noop';
                        continue;
                    } elseif ('--'.$boundary == trim($line)) {
                        $proc_mode = 'noop';
                        continue;
                    } elseif (-1 != $PassOn or 1 == $is_inline) {
                        list($mail_header, $mimebody) = explode_mime_body
                                ($filehandle, $PassOn, $is_inline, $HeaderOnly, $boundary);
                        $proc_mode = 'noop';
                        continue;
                    } elseif ($mimebody['part_encoding'][$id] == 'quoted-printable') {
                        echo quoted_printable_decode(str_replace('='.CRLF, '', $line));
                        continue;
                    } elseif($mimebody['part_encoding'][$id] == 'base64') {
                        echo base64_decode(trim($line));
                        continue;
                    } else {
                        echo $line;
                        continue;
                    }
                }
                if ('addbody' == $proc_mode) {
                    $line = rtrim($line);
                    if ('--'.$boundary.'--' == $line) {
                        if (!empty($boundary_stack)) {
                            $boundary = array_pop($boundary_stack);
                        }
                        $proc_mode = 'parsebody';
                        $next_mode = 'leaveout';
                        continue;
                    } elseif ('--'.$boundary == $line) {
                        $proc_mode = 'parsebody';
                        continue;
                    } elseif (substr($mimebody['part_type'][$id], 0, 5) == 'text/' && $GLOBALS['active_part'] == -1) {
                        $GLOBALS['mailbody'] .= $line.CRLF;
                        continue;
                    } elseif ($mimebody['part_encoding'][$id] == 'quoted-printable') { // Implicit exact sizes!
                        $line = preg_replace('!=\r\n$!', '', $line);
                        if (!isset($mimebody['text'][$id])) $mimebody['text'][$id] = 0;
                        $mimebody['text'][$id] += strlen($line);
                        $mimebody['text'][$id] -= (3 * substr_count($line, '='));
                        continue;
                    } elseif ($mimebody['part_encoding'][$id] == 'base64') {
                        if (!isset($mimebody['text'][$id])) $mimebody['text'][$id] = 0;
                        $mimebody['text'][$id] += (strlen($line) * 0.75);
                        continue;
                    } else {
                        if (!isset($mimebody['text'][$id])) $mimebody['text'][$id] = 0;
                        $mimebody['text'][$id] += strlen($line);
                        continue;
                    }
                }
            } else {
                if ('finalise' == $proc_mode) $end_reached = 1;
                else $GLOBALS['mailbody'] .= $line;
            }
        }
    }
    return array(isset($mail_header) ? $mail_header : FALSE, isset($mimebody) ? $mimebody : FALSE);
}

function get_visible_attachments($mimebody, $attach = '', $do_link = 'links', $icon_path)
{
     $length_links = isset($GLOBALS['WP_skin']['length_links']) && $GLOBALS['WP_skin']['length_links']
                   ? $GLOBALS['WP_skin']['length_links']
                   : false;

    if (is_array($mimebody['part_attached'])) {
        $addendum = (isset($attach) && $attach) ? $attach.'.' : '';
        foreach ($mimebody['part_attached'] as $num => $name) {
            unset($mime_readable);
            if (isset($mimebody['part_detail'][$num])
                    && preg_match('!name=("?)(.+)(\1)!i', $mimebody['part_detail'][$num], $found)) {
                $filename = $found[2];
                $leaf = explode('.', $filename);
                if (!isset($leaf[1])) $leaf[1] = '';
                if ($length_links && strlen($leaf[0]) + strlen($leaf[1]) > $length_links)  {
                    $filename = substr($leaf[0], 0, ($length_links - strlen($leaf[1]))).'...'.$leaf[1];
                }
            } elseif (isset($mimebody['dispo_pad'][$num])
                    && preg_match('!name=("?)(.+)(\1)!i', $mimebody['dispo_pad'][$num], $found)) {
                $filename = $found[2];
                $leaf = explode('.', $filename);
                if ($length_links && strlen($leaf[0]) + strlen($leaf[1]) > $length_links) {
                    $filename = substr($leaf[0], 0, ($length_links - strlen($leaf[1]))) . '...' . $leaf[1];
                }
            } elseif (isset($mimebody['part_description'][$num])
                    && strlen($mimebody['part_description'][$num]) > 0) {
                if ($length_links) {
                    $filename = substr($mimebody['part_description'][$num], 0, $length_links);
                } else {
                    $filename = $mimebody['part_description'][$num];
                }
            } elseif ((count($mimebody['part_attached']) == 1) &&
                    preg_match
                            ('!name=("?)(.+)(\1)!i'
                            ,isset($GLOBALS['decode_detail']) ? $GLOBALS['decode_detail'] : ''
                            ,$found
                            )) {
                $filename = $found[2];
                $leaf = explode('.', $filename);
                if ($length_links && strlen($leaf[0]) + strlen($leaf[1]) > $length_links) {
                    $filename = substr($leaf[0], 0, ($length_links - strlen($leaf[1]))).'...'.$leaf[1];
                }
            } elseif (preg_match('/^message/i', $mimebody['part_type'][$num])) {
                $filename = $GLOBALS['WP_msg']['inlinemail'];
            } elseif ('text/html' == strtolower($mimebody['part_type'][$num])) {
                $filename = $GLOBALS['WP_msg']['htmledit'];
            } else {
                $filename = $GLOBALS['WP_msg']['undeffile'];
            }
            // Involve MIME handler
            $mime_rewritten = trim($mimebody['part_type'][$num]);
            if (($mime_rewritten == '' || preg_match('/^application.+$/i',$mime_rewritten))
                    && $filename != $GLOBALS['WP_msg']['undeffile']) {
                list ($mime_rewritten, $mime_readable) = $GLOBALS['MIME']->get_type_from_name($filename);
            }
            $mime_rewritten = str_replace('/', '_', $mime_rewritten);
            $mime_rewritten = preg_replace('/^message_.+$/i', 'message__', $mime_rewritten);
            if (file_exists($icon_path.'/'.$mime_rewritten.'.png')) {
                $return['img'][$num] = $mime_rewritten.'.png';
            } else {
                $return['img'][$num] = '__.png';
            }
            $return['img_alt'][$num] = $mimebody['part_type'][$num];
            // Beachte RFC 1522 (MIME-extended Mail header lines)
            $return['name'][$num] = $filename;
            // MIME handler involvement, part two :)
            if (!isset($mime_readable)) {
                    $mime_readable = $GLOBALS['MIME']->get_typename_from_type($mimebody['part_type'][$num]);
            }
            $return['size'][$num] = size_format($mimebody['text'][$num]);
            $return['filetype'][$num] = $mime_readable;
            $return['attid'][$num] = $addendum.$num;
        }
        return $return;
    }
}
?>
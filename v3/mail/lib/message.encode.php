<?php
/* ------------------------------------------------------------------------- */
/* lib/message.encode.php - PHlyMail 1.2.0+                                  */
/* Routines related to encoding emails                                       */
/* (c) 2001-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* v1.3.3                                                                    */
/* ------------------------------------------------------------------------- */

$WP_libload['encode'] = TRUE;

function create_messageheader($from = '', $to = '', $cc = '', $bcc = '', $subj = '', $additional = '')
{
    $return = '';
    // Instantiate IDNA class
    $IDN = new idna_convert();
    $in = array ('from' => $from, 'to' => $to, 'cc' => $cc, 'bcc' => $bcc, 'subj' => $subj);
    foreach ($in as $key => $value) {
        if (!$value) continue;
        if (trim($value) && !$value) continue;
        // Suche nach QP-Teilen
        if (preg_match('/[\x80-\xff]/', $value)) {
            switch ($key) {
            case 'subj': $cmode = 'g'; break;
            case 'from': case 'to': case 'cc': case 'bcc':
                $cmode = '@';
                // Replace IDNs
                $address = preg_replace('!\(.+\)!U', '', $value);
                $address = split(',', str_replace(' ', '', $address));
                foreach ($address as $v) {
                    $value = str_replace($v, $IDN->encode($v), $value);
                }
            break;
            default: $cmode = 'n'; break;
            }
            $in[$key] = encode_1522_line_q(rtrim($value), $cmode);
        }
    }
    if($from) $return .= 'From: '.$in['from'].CRLF;
    if($to)   $return .= 'To: '.$in['to'].CRLF;
    if($cc)   $return .= 'Cc: '.$in['cc'].CRLF;
    if($bcc)  $return .= 'Bcc: '.$in['bcc'].CRLF;
    if($subj) $return .= 'Subject: '.$in['subj'].CRLF;
    $return .= 'X-Mailer: PHlyMail (http://phlymail.com)'.CRLF;
    // Create Message ID
    if (isset($in['from']) && $in['from']) {
        $addi = parse_email_address($in['from'], 0, FALSE);
        $dom = strstr($addi[0], '@');
    } elseif(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME']) {
        $dom = '@'.$_SERVER['SERVER_NAME'];
    } else { // This is failsafe only
        $dom = '@phlymail.local';
    }
    $return .= 'Message-ID: <'.uniqid(time().'.').$dom.'>'.CRLF;
    $return .= 'Date: '.date('r').CRLF;
    if ($additional) {
        $return .= rtrim(preg_replace('!('.CRLF.')+!', CRLF, $additional)).CRLF;
    }
    return $return;
}

function encode_1522_line_q($coded = '', $cmode = 'g')
{
    if ('g' == $cmode) {
        $coded = str_replace(' ', '_', quoted_printable_encode($coded));
        $zeilen = explode(CRLF, $coded);
        $coded = '';
        foreach ($zeilen as $key => $value) {
            if (!$value) continue;
            if ($key > 0) $coded .= "\t";
            $coded .= '=?iso-8859-1?Q?'.$value.'?='.CRLF;
        }
        return rtrim($coded);
    } elseif ('@' == $cmode) {
        $zeilen = explode(CRLF, $coded);
        $coded = '';
        foreach ($zeilen as $key => $value) {
            if (!$value) continue;
            if ($key > 0) $coded .= "\t";
            unset ($words);
            $words = explode(' ', $value, 2);
            foreach ($words as $k => $word) {
                if (preg_match('/[\x80-\xff]/', $word) && preg_match('/\(|\)/', $word)) {
                    $words[$k] = preg_replace
                            ('/^(\()?([^\)]+)(\))?$/ie'
                            ,"'(=?iso-8859-1?Q?'.rtrim(quoted_printable_encode(str_replace(' ', '_', '\\2'))).'?=)'"
                            ,$word
                            );
                }
            }
            $coded .= join(' ', $words).CRLF;
        }
        return rtrim($coded);
    } else {
        $zeilen = explode(CRLF, $coded);
        $coded = '';
        foreach ($zeilen as $key => $value) {
            if (!$value) continue;
            if ($key > 0) $coded .= "\t";
            unset ($words);
            $words = explode(' ', $value);
            foreach ($words as $k => $word) {
                if (preg_match('/[\x80-\xff]/', $word)) {
                    $words[$k] = '=?iso-8859-1?Q?'.rtrim(quoted_printable_encode($word)).'?=';
                }
            }
            $coded .= join(' ', $words).CRLF;
        }
        return rtrim($coded);
    }
}

function put_attach_stream (&$stream, $filename = '', $type = 'application/octet-stream', $name = 'unknown', $LE = CRLF)
{
    $type = trim($type);
    $name = basename(trim($name));
    $bytes_block = 57;
    // This should use the filename for finding the correct MIME type
    if ('application/octet-stream' == $type && 'unknown' != $name) {
        $ntype = $GLOBALS['MIME']->get_type_from_name($name, false);
        if ($ntype[0]) $type = $ntype[0];
    }
    $encoding = $GLOBALS['MIME']->get_encoding_from_type($type);
    $fh_src = fopen($filename, 'r');
    if ($fh_src) {
        $stream->put_data_to_stream('Content-Type: '.$type.'; name="'.$name.'"'.$LE);
        $stream->put_data_to_stream('Content-Disposition: attachment; filename="'.$name.'"'.$LE);
        switch ($encoding) {
        case 'q':
            $stream->put_data_to_stream('Content-Transfer-Encoding: quoted-printable'.$LE.$LE);
            while ($line = fgets($fh_src)) $stream->put_data_to_stream(quoted_printable_encode($line));
            break;
        case '8':
        case '7':
            $stream->put_data_to_stream('Content-Transfer-Encoding: '.$encoding.'bit'.$LE.$LE);
            while ($line = fgets($fh_src)) $stream->put_data_to_stream($line);
            break;
        default:
            $stream->put_data_to_stream('Content-Transfer-Encoding: base64'.$LE.$LE);
            while ($line = fread($fh_src, $bytes_block)) $stream->put_data_to_stream(base64_encode($line).$LE);
            break;
        }
        fclose($fh_src);
        return TRUE;
    } else return FALSE;
}

// Pipe a MIME part from an original mail to one currently sent out
// This function takes an associative array of input parameters
// in           => the object handle of the input stream, usually a POP3 strem
// out          => the object handle of the output stream, usually SMTP or sendmail
// in_boundary  => the boundary of the mail, where the original MIME part should be read from
// out_boundary => the boundary of the mail, where the MIME part is put to
// attach_list  => array of numbers of MIME parts of the original mail, which should be piped to
//                 the new mail, the number (starting from 0 for the first attachment) is stored
//                 in the array keys
// LE           => the line ending to use, usually CRLF (\r\n)
function pipe_mime_part($data)
{
    if (!$data['in'])  return FALSE;
    if (!$data['out']) return FALSE;
    if (!isset($data['in_boundary'])) $data['in_boundary'] = '';
    if (!isset($data['attach_list'])) return FALSE;
    $LE = (isset($data['LE'])) ? $data['LE'] : CRLF;
    $proc_mode = 'none';
    $next_mode = false;
    $boundary_stack = false;
    $id = 0;
    $end_reached = 0;
    while ($end_reached == 0) {
        if ('finalise' != $proc_mode) {
            $line = $data['in']->talk_ml();
            if (!$line || $line == '.'.CRLF) $proc_mode = 'finalise';
        }
        if ('leaveout' == $proc_mode) $proc_mode = $next_mode;
        if ('noop' == $proc_mode) continue;
        if ('none' == $proc_mode) {
            if ('--'.$data['in_boundary'] == trim($line)) {
                $proc_mode = 'addhead';
            }
        }
        if ('parsehead' == $proc_mode) {
            if (trim($head) != '') {
                $head = explode_mime_header(CRLF.$head.CRLF);
                $mimebody['part_type'][$id] = strtolower($head['content_type']);
                $mimebody['part_detail'][$id] = isset($head['content_type_pad']) ? $head['content_type_pad'] : FALSE;
                $mimebody['dispo_pad'][$id] = isset($head['content_dispo_pad']) ? $head['content_dispo_pad'] : FALSE;
                $mimebody['part_encoding'][$id] = strtolower($head['content_encoding']);
                if (preg_match('/^multipart/i', $mimebody['part_type'][$id])) {
                    $boundary_stack[] = $data['in_boundary'];
                    $data['in_boundary'] = $head['boundary'];
                }
                unset($head);
                $proc_mode = (isset($data['attach_list'][$id])) ? 'outhead' : 'addbody';
            } else $proc_mode = 'none';
        }
        if ('finalise' == $proc_mode) {
            if ($mimebody['part_type'][$id]) $proc_mode = 'parsebody';
            $end_reached = 1;
        }
        if ('outhead' == $proc_mode) {
            if (preg_match('/name="(.*)"/i', $mimebody['part_detail'][$id], $found)) {
                $filename = $found[1];
            } elseif (preg_match('/name="(.*)"/i', $mimebody['dispo_pad'][$id], $found)) {
                $filename = $found[1];
            } else {
                $filename = 'noname';
            }
            $data['out']->put_data_to_stream($LE.'--'.$data['out_boundary'].$LE);
            $data['out']->put_data_to_stream('Content-Type: '.$mimebody['part_type'][$id].$LE);
            $data['out']->put_data_to_stream('Content-Disposition: attachment; filename="'.$filename.'"'.$LE);
            if ($mimebody['part_encoding'][$id]) {
                $data['out']->put_data_to_stream('Content-Transfer-Encoding: '.$mimebody['part_encoding'][$id].$LE);
            }
            $data['out']->put_data_to_stream($LE);
            $proc_mode = 'outbody';
        }
        if ('parsebody' == $proc_mode) {
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
            if ('--'.$data['in_boundary'].'--' == trim($line)) {
                if (is_array($boundary_stack)) $data['in_boundary'] = array_pop($boundary_stack);
                $proc_mode = 'noop';
                continue;
            } elseif ('--'.$data['in_boundary'] == trim($line)) {
                ++$id;
                $proc_mode = 'addhead';
                continue;
            } else {
                $data['out']->put_data_to_stream($line);
                continue;
            }
        }
        if ('addbody' == $proc_mode) {
            $line = trim($line);
            if ('--'.$data['in_boundary'].'--' == $line) {
                if (!empty($boundary_stack)) {
                    $boundary = array_pop($boundary_stack);
                }
                $proc_mode = 'parsebody';
                $next_mode = 'leaveout';
                continue;
            } elseif('--'.$data['in_boundary'] == $line) {
                $proc_mode = 'parsebody';
                continue;
            }
        }
    }
}

function quoted_printable_encode($return = '')
{
    $schachtel = '';
    // Ersetzen der lt. RFC 1521 nötigen Zeichen
    $return = preg_replace('/([^\t\x20\x2E\041-\074\076-\176])/ie', "sprintf('=%2X',ord('\\1'))", $return);
    $return = preg_replace('!=\ ([A-F0-9])!', '=0\\1', $return);
    // Einfügen von QP-Breaks (=\r\n)
    if (strlen($return) > 75) {
        $length = strlen($return); $offset = 0;
        do {
            $step = 76;
            $add_mode = (($offset+$step) < $length) ? 1 : 0;
            $auszug = substr($return, $offset, $step);
            if (preg_match('!\=$!', $auszug))   $step = 75;
            if (preg_match('!\=.$!', $auszug))  $step = 74;
            if (preg_match('!\=..$!', $auszug)) $step = 73;
            $auszug = substr($return, $offset, $step);
            $offset += $step;
            $schachtel .= $auszug;
            if (1 == $add_mode) $schachtel.= '='.CRLF;
        } while ($offset < $length);
        $return = $schachtel;
    }
    $return = preg_replace('!\.$!', '. ', $return);
    return preg_replace('!(\r\n|\r|\n)$!', '', $return).CRLF;
}

function set_prio_headers($return = '')
{
    switch ($return) {
    case '1':
        return 'X-Priority: 1'.CRLF.'Importance: High'.CRLF;
        break;
    case '3':
        return 'X-Priority: 3'.CRLF.'Importance: Normal'.CRLF;
        break;
    case '5':
        return 'X-Priority: 5'.CRLF.'Importance: Low'.CRLF;
        break;
    }
    return $return;
}

function get_sending_attachments($mimebody, $attach = FALSE)
{
    global $WP_msg, $WP_core, $mail, $profile; // Wozzat?
    $addendum = ($attach) ? $attach.'.' : '';
    foreach ($mimebody['part_attached'] as $num => $name) {
        if (preg_match('/^multipart/i', $mimebody['part_type'][$num])) {
            $inserter = get_sending_attachments($mimebody['text'][$num], $addendum.$num);
            if (isset($inserter)) {
                foreach ($inserter as $key => $val) {
                    $return[] = $val;
                    unset($inserter[$key]);
                }
            }
        } else {
            // Nicht benötigte Parts rauswerfen
            $checker = $addendum.$num;
            if ($GLOBALS['WP_core']['attach'][$checker] == 1) {
                if (!preg_match('/name=\"(.*)\"/i', $mimebody['part_detail'][$num])) {
                    $mimebody['part_detail'][$num] = 'unknown';
                } elseif (preg_match('/name=\"(.*)\"/i', $mimebody['part_detail'][$num], $found)) {
                    $mimebody['part_detail'][$num] = $found[1];
                }
                $return[] = 'Content-Type: '.$mimebody['part_type'][$num].CRLF
                           .'Content-Disposition: attachment; filename="'.$mimebody['part_detail'][$num].'"'.CRLF
                           .'Content-Transfer-Encoding: '.$mimebody['part_encoding'][$num].CRLF.CRLF
                           .$mimebody['text'][$num];
                unset($mimebody['text'][$num]);
                unset($mimebody['part_type'][$num]);
                unset($mimebody['part_detail'][$num]);
                unset($mimebody['part_encoding'][$num]);
            }
        }
    }
    return $return;
}

function gather_addresses($addresses)
{
    $address = join(',', $addresses);
    $address = preg_replace('/,$/', '', preg_replace('/,+/', ',', $address));
    $duration = strlen($address);
    $mode = '';
    $j = 1;
    for ($i = 0; $i <= $duration; ++$i) {
        $test = substr($address, $i, 1);
        if ('comment' == $mode) {
            if (')' == $test)  {
                $mode = '';
                continue;
            }
        }
        if ('string' == $mode) {
            if ('"' == $test) {
                $mode = '';
                continue;
            }
        }
        if ('' == $mode) {
            if ('(' == $test) {
                $mode = 'comment';
                continue;
            }
            if ('"' == $test) {
                $mode = 'string';
                continue;
            }
            if (',' == $test) {
                $found[$j] = $i;
                $j++;
            }
        }
    }
    $found[0] = 0;
    $found[$j] = $duration;
    $return = '';
    for ($k = 0; $k < $j; ++$k) {
        $l = $k + 1;
        if (0 != $k) ++$found[$k];
        $build = substr($address, $found[$k], ($found[$l]-$found[$k]));
        $build = parse_email_address($build, 0, TRUE);
        if (0 != $k) $return .= ', ';
        $return .= $build[0];
    }
    return $return;
}

function email_check_validity($addresses)
{
    if (is_array($addresses) && !empty($addresses)) {
        $addresses = explode(', ', gather_addresses($addresses));
        foreach ($addresses as $k => $val) {
            if (!preg_match('!^[a-z0-9_\.]+\@[a-z0-9-\.]+\.[a-z]+$!i', $val)) {
                $return[] = $val;
                continue;
            }
            list(, $domain) = explode('@', $val, 2);
            if (getmxrr($domain, $mx, $weight) == 0) $return[] = $val;
        }
        return $return;
    }
}
?>
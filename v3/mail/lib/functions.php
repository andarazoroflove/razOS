<?php
/* ------------------------------------------------------------------------- */
/* lib/functions.php - PHlyMail 1.2.0+  General purpose functions            */
/* (c) 2001-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.8.9mod3                                                                */
/* ------------------------------------------------------------------------- */
// Prevent $WP_core[] request variables from destroying the predefined array
if (@ini_get('register_globals') != 1) {
    if (isset($_REQUEST['WP_core'])) $WP_core_request = $_REQUEST['WP_core'];
    unset($_REQUEST['WP_core']);
    extract($_REQUEST);
    if (isset($WP_core_request)) $WP_core = $WP_core + $WP_core_request;
}

function version_format($version = '')
{
    $return = '';
    for ($i = strlen($version); $i >= 0; $i--) {
        if (((strlen($version) - $i) % 2 == 1) && (strlen($version) - $i) != 1) $return = '.' . $return;
        $return = substr($version, $i, 1) . $return;
    }
    return $return;
}

// Recursive stripslashes of a given data structure
function PHM_stripslashes($return = '')
{
    if (is_array($return)) {
        foreach ($return as $k => $v) {
            if(is_array($v)) $return[$k] = PHM_stripslashes($return);
            else $return[$k] = stripslashes($return[$k]);
        }
    } else $return = stripslashes($return);
    return $return;
}

function mailto_2_send($return = '')
{
    $mail = isset($GLOBALS['mail']) ? '&amp;mail='.$GLOBALS['mail'] : '';
    return preg_replace
           (
            '!(mailto:)([^\s<>"?]+)(\?subject=([^\s<>"]+))?!i'
           ,$_SERVER['PHP_SELF'].'?action=send'.$mail
            .'&amp;'.give_passthrough(1).'&amp;WP_send[to]=\2&amp;WP_send[subj]=\4'
           ,$return
           );
}

function links($return = '', $mode = 'text')
{
    $mail = isset($GLOBALS['mail']) ? '&amp;mail='.$GLOBALS['mail'] : '';
    // Plain text...
    if('text' == $mode) {
        // Emailadressen
        $return = preg_replace
                ('!(mailto:)([^\s<>"?]+)(\?subject=([^\s<>"]+))?!i'
                ,'<a href="'.$_SERVER['PHP_SELF'].'?action=send'.$mail
                 .'&amp;'.give_passthrough(1).'&amp;WP_send[to]=\2&amp;WP_send[subj]=\4">\2\3</a>'
                ,$return
                );
        // Internet-Protokolle:
        $return = preg_replace
                ('!(http://|https:|ftp://|gopher://|news:|www\.)(.+)(?=<|>|\W[\s\n\r]|[\s\r\n])!Umie'
                ,"'<a href=\"".addslashes($GLOBALS['WP_core']['page_path'])
                 ."/derefer.php?go='.derefer('\\1\\2').'\" target=\"_blank\">'.links_linebreak('\\1\\2').'</a>'"
                ,$return
                );
        return $return;
    } else {
        // HTML ... Alles.... wird aber nochmal gefiltert
        // target="_blank"
        $return = preg_replace
                (array('!target="?\w+"?!i', '!(\<a\s)(.+\>)!Ui')
                ,array('', '\1target="_blank" \2')
                ,$return
                );
        // Derefer
        return preg_replace
                ('!(?<=href="|href=|src=|src=")([^\s<>"]+)!es'
                ,"'".addslashes($GLOBALS['WP_core']['page_path'])."/derefer.php?go='.links_html('\\1')"
                ,$return
                );
    }
}

function links_html($return = '')
{
    if (substr(strtolower($return), 0, 7) == 'mailto:') {
        return mailto_2_send($return);
    } else {
        return derefer($return);
    }
}

function derefer($return = '')
{
    return htmlentities(urlencode(un_html($return)));
}

function links_linebreak($return = '')
{
    if ('0' != $GLOBALS['WP_core']['read_wordwrap']
            && preg_match('/([^\s]{'.$GLOBALS['WP_core']['read_wordwrap'].',})/', $return)) {
        return htmlspecialchars(preg_replace('/([^\s]{'.$GLOBALS['WP_core']['read_wordwrap'].'})/', '\\1 ',
                un_html($return)));
    } else return $return;
}

function un_html($return = '')
{
    return preg_replace
           (array('!&gt;!i', '!&lt;!i', '!&quot;!i', '!&amp;!i', '!&nbsp;!i', '!&copy;!i')
           ,array('>', '<', '"', '&', ' ', '(c)')
           ,$return
           );
}

function multi_address ($address = '')
{
    $return = '';
    $duration = strlen($address);
    $mode = '';
    $j = 1;
    for($i = 0; $i <= $duration; ++$i) {
        $test = substr($address, $i, 1);
        if ('comment' == $mode) {
            if (')' == $test) { $mode = ''; continue; }
        }
        if ('string' == $mode) {
            if ('"' == $test) { $mode = ''; continue; }
        }
        if ('' == $mode) {
            if ('(' == $test) { $mode = 'comment'; continue; }
            if ('"' == $test) { $mode = 'string'; continue; }
            if (',' == $test) { $found[$j] = $i; $j++; }
        }
    }
    $found[0] = 0;
    $found[$j] = $duration;
    for ($k = 0; $k < $j; ++$k) {
        $l = $k + 1;
        if (0 != $k) ++$found[$k];
        $build = substr($address, $found[$k], ($found[$l] - $found[$k]));
        $build = parse_email_address($build, 0);
        $build = '<a href="' . mailto_2_send('mailto:'.$build[0]) . '" title="' .$build[2] . '">' . $build[1] . '</a>';
        if (0 != $k) $return .= ', ';
        $return .= $build;
    }
    return $return;
}

function give_passthrough($mode = 1)
{
    $return = '';
    if (isset($GLOBALS['WP_core']['pass_through'])) {
        if (1 == $mode) {
            foreach ($GLOBALS['WP_core']['pass_through'] as $key => $value) {
                if (0 < $key) $return .= '&';
                if (is_array($GLOBALS[$value])) {
                    $i = 0;
                    foreach ($GLOBALS[$value] as $ke2 => $valu2) {
                        if (0 < $i) $return .= '&';
                        $return .= $value.'['.$ke2.']='.$valu2;
                        ++$i;
                    }
                }  else $return.=$value.'='.$GLOBALS[$value];
            }
        } elseif (2 == $mode) {
            foreach ($GLOBALS['WP_core']['pass_through'] as $key => $value) {
                if (is_array($GLOBALS[$value])) {
                    foreach ($GLOBALS[$value] as $ke2 => $valu2) {
                        if ('wml' == $GLOBALS['WP_skin']['client_type']) {
                            $return .= '<postfield name="'.$value.'['.$ke2.']" value="'.$valu2.'" />'.LF;
                        } else {
                            $return .= '<input type="hidden" name="'.$value.'['.$ke2.']" value="'.$valu2.'">'.LF;
                        }
                    }
                } else {
                    if ('wml' == $GLOBALS['WP_skin']['client_type']) {
                        $return .= '<postfield name="'.$value.'" value="'.$GLOBALS[$value].'" />'.LF;
                    } else {
                        $return .= '<input type="hidden" name="'.$value.'" value="'.$GLOBALS[$value].'">'.LF;
                    }
                }
            }
        } elseif (3 == $mode) {
            foreach ($GLOBALS['WP_core']['pass_through'] as $key => $val) {
                if (is_array($GLOBALS[$value]))  {
                    foreach($GLOBALS[$value] as $ke2 => $valu2) {
                        $return[$value.'['.$ke2.']'] = $valu2;
                    }
                } else $return[$value] = $GLOBALS[$value];
            }
        }
    }
    if (1 == $mode) {
        if ($return != '') $return .= '&';
        $return .= SESS_NAME.'='.SESS_ID;
    } elseif (2 == $mode) {
        if ('wml' == $GLOBALS['WP_skin']['client_type']) {
            $return .= '<postfield name="'.SESS_NAME.'" value="'.SESS_ID.'" />'.LF;
        } else $return .= '<input type=hidden name="'.SESS_NAME.'" value="'.SESS_ID.'">'.LF;
    } elseif (3 == $mode) $return[SESS_NAME] = SESS_ID;
    return $return;
}

function size_format($s = '', $p = '')
{
    if ('true' == $p) $n = ' '; else $n = '&nbsp;';
    if (floor($s/1048576) > 0) {
        return number_format(($s/1048576), 1, $GLOBALS['WP_msg']['dec'], $GLOBALS['WP_msg']['tho']) . $n . 'MB';
    } elseif (floor($s/1024) > 0) {
        return number_format(($s/1024), 1, $GLOBALS['WP_msg']['dec'], $GLOBALS['WP_msg']['tho']) . $n . 'KB';
    } else return trim(floor($s)) . $n . 'B';
}

function nice_view($return = '', $teletype = '')
{
    $lines = explode(CRLF, $return);
    if(!count($lines)) return '';
    foreach ($lines as $ky => $val) {
        if ('0' != $GLOBALS['WP_core']['read_wordwrap'] &&
            preg_match('/([^\s]{'.$GLOBALS['WP_core']['read_wordwrap'].',})/', $val)) {
            if(!preg_match('!(http://|https://|ftp://|gopher://|mailto:|news:)!', $val)) {
                $val = preg_replace('/([^\s]{'.$GLOBALS['WP_core']['read_wordwrap'].'})/', '\\1 ', $val);
            }
        }
        $val = htmlspecialchars($val);
        unset($found);
        if (preg_match_all('!^(\ ?(\&gt;\ ?)+)!i', $val, $found)) {
            $farbe = (substr_count($found[0][0], '&gt;') % 4);
            if (0 == $farbe) $farbe = 4;
            $lines[$ky] = '<span id="'.substr_count($found[0][0], '&gt;').'" class="quote_'.$farbe.'">';
            if ('sys' == $teletype) $lines[$ky] .= '<tt>'.$val.'</tt>'; else $lines[$ky] .= $val;
            $lines[$ky] .= '</span>';
        } else $lines[$ky] = $val;
    }
    return links(join('<br />'.LF, $lines));
}

function save_config($file, $tokens, $tokval)
{
    if (!file_exists($file)) {
        touch($file);
        chmod($file, $GLOBALS['WP_core']['umask']);
    }
    $GlChFile = join('', file($file));
    // Remove PHP tags
    $GlChFile = preg_replace('!^\<\?php\ die\(\);\ \?\>'.LF.'!', '', $GlChFile);
    foreach ($tokens as $k => $v) {
        if (preg_match('/^'.$tokens[$k].';;[^\r^\n]*/mi', $GlChFile)) {
            $tokval[$k] = str_replace('$', '\$', $tokval[$k]); // Treat '$' literally
            $GlChFile = preg_replace('/^'.$tokens[$k].';;[^\r^\n]*/mi', $tokens[$k].';;'.$tokval[$k], $GlChFile);
        } else $GlChFile .= $tokens[$k].';;'.$tokval[$k].LF;
    }
    $suf = fopen($file, 'w');
    if ($suf) {
        fputs($suf, '<?php die(); ?>'.LF.$GlChFile);
        fclose($suf);
        return TRUE;
    } else return FALSE;
}

function wash_size_field($size = '0')
{
    $size = preg_replace('![\ \.,]!', '', $size);
    $size = preg_replace('!^([^0-9]*)([0-9]+)(m|k){0,1}!i', '\\2 \\3', $size);
    list ($number, $faktor) = split(' ', $size, 2);
    switch ($faktor) {
    case 'M':
    case 'm':
        $size = $number * 1024 * 1024;
        break;
    case 'k':
    case 'K':
        $size = $number*1024;
        break;
    default:
        $size = $number;
    }
    return $size;
}

function get_microtime()
{
    list ($usec, $sec) = explode(' ', microtime());
    return ((float)$usec+(float)$sec);
}

function error($my_error = '')
{
    $ef = fopen($GLOBALS['WP_core']['conf_files'].'/error.log', 'a');
    fputs($ef, date('Y-m-d H:i:s') . ' ' . $my_error.LF);
    fclose($ef);
}

// Encrypt a string
// Input:   confuse(string $data, string $key);
// Returns: encrypted string
function confuse($data = '', $key = '')
{
    $encoded = ''; $DataLen = strlen($data);
    if (strlen($key) < $DataLen) $key = str_repeat($key, ceil($DataLen/strlen($key)));
    for ($i = 0; $i < $DataLen; ++$i) {
        $encoded .= chr((ord($data{$i}) + ord($key{$i})) % 256);
    }
    return base64_encode($encoded);
}

// Decrypt a string
// Input:   deconfuse(string $data, string $key);
// Returns: decrypted String
function deconfuse($data = '', $key = '')
{
    $data = base64_decode($data);
    $decoded = '';  $DataLen = strlen($data);
    if (strlen($key) < $DataLen) $key = str_repeat($key, ceil($DataLen/strlen($key)));
    for($i = 0; $i < $DataLen; ++$i) {
        $decoded .= chr((256 + ord($data{$i}) - ord($key{$i})) % 256);
    }
    return $decoded;
}

?>
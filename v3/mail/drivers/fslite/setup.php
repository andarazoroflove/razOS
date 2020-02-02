<?php
/* ------------------------------------------------------------------------- */
/* fslite/setup.php -> PHlyMail DB; Driver FSlite; setup module              */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* v0.2.2                                                                    */
/* ------------------------------------------------------------------------- */

$WP_DBset_action = isset($_REQUEST['WP_DBset_action']) ? $_REQUEST['WP_DBset_action'] : false;

if (file_exists($WP_core['driver_dir'].'/lang.'.$WP_msg['language'].'.php')) {
    include($WP_core['driver_dir'].'/lang.'.$WP_msg['language'].'.php');
} else include($WP_core['driver_dir'].'/lang.en.php');

if (isset($_REQUEST['WP_DBset_action'])) $WP_DBset_action = $_REQUEST['WP_DBset_action'];
if (isset($_REQUEST['WP_DB'])) $WP_DB = $_REQUEST['WP_DB'];

$WPDB['conf_file'] = $WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php';

if ('do' == $WP_DBset_action) {
    if (!file_exists($WPDB['conf_file'])) {
        touch($WPDB['conf_file']);
        chmod($WPDB['conf_file'], $WP_core['umask']);
    }
    $suf = fopen($WPDB['conf_file'], 'w');
    if ($suf) {
        // Remove any trailing slash or newline character
        $WP_DB['fslite_prefix'] = preg_replace('!/*\r*\n*$!', '', $WP_DB['fslite_prefix']);
        fputs($suf, '<?php die(); ?>'.LF);
        fputs($suf, 'fslite_prefix;;'.$WP_DB['fslite_prefix'].LF);
        fclose($suf);
    }
    $conf_output = '';

    // If the object $DB is initialized, this script is run in context of the Config
    // Interface, otherwise it is run in context of the installer
    if (is_object($DB)) {
        if ($WP_DB['pw1'] == $WP_DB['pw2']) {
            $erroneous = 0;
            $payload['user'] = $WP_DB['user'];
            if (isset($WP_DB['pw1']) && $WP_DB['pw1']) {
                $payload['pass'] = md5($WP_DB['pw1']);
            }
            // Hier wird die fslite.user.php geschrieben...
            $check = $DB->_write_file($DB->DB['file_user'], $payload, TRUE);
            if ($check) {
                list($uid, $pass) = $DB->authenticate($WP_DB['adm_name']);
                if ($pass == md5($WP_DB['pw1'])) {
                    $conf_output .= '<span style="color:darkgreen">'.$WP_drvmsg['saved'].'</span><br>';
                } else {
                    $conf_output .= '<span style="color:darkred">FSlite error:&nbsp;'.$WP_drvmsg['nowrite'].'</span><br>';
                }
            } else {
                $conf_output .= '<span style="color:darkred">FSlite error:&nbsp;'.$WP_drvmsg['nowrite'].'</span><br>';
            }
        } else {
            $conf_output .= '<span style="color:darkred">'.$WP_drvmsg['nonequal'].'</span><br>';
        }
    }

    unset($WP_DBset_action);
}

if (!$WP_DBset_action) {
    if (!isset($conf_output) || !$conf_ouput) $conf_output = '';
    if (!isset($WP_DB['fslite_prefix'])) {
        if (!file_exists($WPDB['conf_file'])) {
            $WP_DB['fslite_prefix'] = $WP_core['conf_files'];
        } elseif (is_object($DB)) {
            $WP_DB = parse_ini_file($DB->DB['fslite_prefix'].'/fslite.user.php');
            $WP_DB['fslite_prefix'] = $DB->DB['fslite_prefix'];
        }
    }
    $conf_output .= $WP_drvmsg['HeadGen'].'<br />'.LF
            .'<table border="0" cellspacing="0" cellpadding="2">'.LF
            .'<tr><td align="left" class="body">'.$WP_drvmsg['fslite_path'].'</td>'
            .'<td align="left" class="body"><input type="text" name="WP_DB[fslite_prefix]" value="'.$WP_DB['fslite_prefix'].'" size="32" /></td></tr>'
            .LF.'<tr><td colspan="2">&nbsp;</td></tr>'.LF;
    if (is_object($DB)) {
        $conf_output .= '<tr><td align="left" class="body">'.$WP_drvmsg['fslite_admname'].'</td>'
                .'<td align="left" class="body"><input type="text" name="WP_DB[user]" value="'.$WP_DB['user'].'" size="16" /></td></tr>'.LF
                .'<tr><td align="left" class="body">'.$WP_drvmsg['fslite_admpass'].'</td>'
                .'<td align="left" class="body"><input type="password" name="WP_DB[pw1]" value="" size="16" /></td></tr>'.LF
                .'<tr><td align="left" class="body">'.$WP_drvmsg['fslite_admpass2'].'</td>'
                .'<td align="left" class="body"><input type="password" name="WP_DB[pw2]" value="" size="16" /></td></tr>'.LF;
    }
    $conf_output .= '<tr><td class="body" colspan="2" align="right"><input type="hidden" name="WP_DBset_action" value="do" />'
            .'<input type="submit" value="'.$WP_drvmsg['save'].'" /></td></tr></table>';
}
?>
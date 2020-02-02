<?php
/* ------------------------------------------------------------------------- */
/* lib/init.script.php -> Choices laden und weitere Initialisierungen        */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.2.5                                                                    */
/* ------------------------------------------------------------------------- */
error_reporting($WP_core['diagnosis']);
// Which PHP version do we use?
$phpversion = preg_replace('/^(\d+)\..+$/', '\1', phpversion());

define('SESS_NAME', session_name());
define('SESS_ID', session_id());
define('CRLF', "\r\n");
define('LF', "\n");
require($WP_core['page_path'].'/lib/functions.php');
// Global Choices
if (file_exists($WP_core['conf_files'].'/global.choices.php')) {
    foreach(file($WP_core['conf_files'].'/global.choices.php') as $l) {
        if ($l{0} == '#') continue;
        if (substr($l, 0, 15) == '<?php die(); ?>') continue;
        list($k, $v) = explode(';;', trim($l), 2);
        $WP_core[$k] = $v;
    }
}
// Config Choices
if (file_exists(CONFIGPATH.'/conf/choices.php')) {
    foreach(file(CONFIGPATH.'/conf/choices.php') as $l) {
        if (!$l) continue;
        if ($l{0} == '#') continue;
        if (substr($l, 0, 15) == '<?php die(); ?>') continue;
        $k = explode(';;', trim($l), 2);
        $WP_conf[$k[0]] = isset($k[1]) ? $k[1] : false;
    }
} else {
    // In case we do not have any settings in the above file
    $WP_conf = array
            ('scheme' => 'default'
            ,'language' => 'de'
            ,'allow_ip' => 0
            );
}

require($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/driver.php');
require($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/admin.php');
$DB = new admin($WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php');
// require($WP_core['page_path'].'/lib/user.choices.php');
require(CONFIGPATH.'/messages/'.$WP_conf['language'].'.php');
if ($phpversion < 5) {
    require($WP_core['page_path'].'/lib/fxl_template.inc.php');
} else {
    require($WP_core['page_path'].'/lib/fxl_template.inc5.php');
}
include($WP_core['page_path'].'/lib/plugins.php');
// Skin-Pfad setzen
$WP_core['skin_dir'] = $WP_core['skin_path'];
$WP_core['skin_path'].= '/'.$WP_core['skin_name'];
// Skin choices
if (file_exists($WP_core['skin_path'].'/choices.ini.php')) {
    $WP_skin = parse_ini_file($WP_core['skin_path'].'/choices.ini.php');
}

// Rise security of the config interface by blocking everything but allowed IPs
if (isset($WP_conf['allow_ip']) && '1' == $WP_conf['allow_ip']) {
    if (!isset($_SESSION['allowed_ips'])) {
        if (file_exists(CONFIGPATH.'/conf/allowed_ips.php') && is_readable(CONFIGPATH.'/conf/allowed_ips.php')) {
            $allowed_ips = join('', file(CONFIGPATH.'/conf/allowed_ips.php'));
            $_SESSION['allowed_ips'] = explode(LF, trim(str_replace('<?php die(); ?>', '', $allowed_ips)));
        } else {
            $_SESSION['allowed_ips'] = array(getenv('REMOTE_ADDR'));
        }
    }
    if (isset($_SESSION['allowed_ips']) && is_array($_SESSION['allowed_ips'])) {
        $treffer = 0;
        $client_ip = getenv('REMOTE_ADDR');
        foreach ($_SESSION['allowed_ips'] as $test) {
            if (!$test) continue;
            if (substr($client_ip, 0, strlen($test)) == $test) {
                $treffer = 1;
                break;
            }
        }
        if (0 == $treffer) $_SESSION = array('blocked' => 'IP');
    }
}


?>
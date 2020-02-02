<?php
/* ------------------------------------------------------------------------- */
/* lib/init.script.php -> Choices laden und weitere Initialisierungen        */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.2.8mod1                                                                */
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
    foreach (file($WP_core['conf_files'].'/global.choices.php') as $l) {
        if (strlen(trim($l)) == 0) continue;
        if ($l{0} == '#') continue;
        if (substr($l, 0, 15) == '<?php die(); ?>') continue;
        $parts = explode(';;', trim($l));
        $WP_core[$parts[0]] = isset($parts[1]) ? $parts[1] : false;
    }
}

require($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/driver.php');
$DB = new driver($WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php');
require($WP_core['page_path'].'/messages/'.$WP_core['language'].'.php');
if ($phpversion < 5) {
    require($WP_core['page_path'].'/lib/fxl_template.inc.php');
} else {
    require($WP_core['page_path'].'/lib/fxl_template.inc5.php');
}
include($WP_core['page_path'].'/lib/clientswitch.php');
include($WP_core['page_path'].'/lib/idna_convert.class.php');
// Skin-Pfad setzen
$WP_core['skin_dir'] = $WP_core['skin_path'];
$WP_core['skin_path'] .= '/'.$WP_core['skin_name'];
// Skin choices
if (file_exists($WP_core['skin_path'].'/choices.ini.php')) {
    $WP_skin = parse_ini_file($WP_core['skin_path'].'/choices.ini.php');
}
// MOD: Init default profile
$usr = $DB->get_usrdata();
$WP_core['default_profile'] = (isset($usr['default_profile']) && $usr['default_profile'])
                            ? $usr['default_profile']
                            : FALSE;
// END MOD

$WP_broad['login_type'] = $WP_msg['offline'];
if (isset($_SESSION['WPs_popverbose'])) {
    $WP_broad['profile'] = $_SESSION['WPs_popverbose'];
} else {
    $WP_broad['profile'] = $WP_msg['profno'];
}
include($WP_core['page_path'].'/lib/plugins.php');

?>
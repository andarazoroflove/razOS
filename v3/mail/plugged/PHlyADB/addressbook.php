<?php
/* ------------------------------------------------------------------------- */
/* PHlyMail PlugIn: Adreßbuch (Basic)                                        */
/* (c) 2001-2003 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* v1.0.1                                                                    */
/* ------------------------------------------------------------------------- */
if (file_exists($WP_core['plugin_path'].'/lang.'.$WP_msg['language'].'.php')) {
    include($WP_core['plugin_path'].'/lang.'.$WP_msg['language'].'.php');
} else include($WP_core['plugin_path'].'/lang.en.php');

if (!isset($WP_plug['adb_action'])) $WP_plug['adb_action'] = FALSE;

switch ($WP_plug['adb_action']) {
case 'select':
case 'main':
case 'add':
    include($WP_core['plugin_path'].'/mod.'.$WP_plug['adb_action'].'.php');
    break;
}

include($WP_core['plugin_path'].'/linker.php');
?>
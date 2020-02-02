<?php
/* ------------------------------------------------------------------------- */
/* mod.main.php -> Anzeige des ADB und einzelner Einträge                    */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Adreßbuch Basic                                                  */
/* v0.1.8                                                                    */
/* ------------------------------------------------------------------------- */

if ('menu' == $WP_plug['adb_mode']) $WP_plug['adb_mode'] = 'adr';
if (!isset($WP_core['plug_output'])) $WP_core['plug_output'] = '';
if (!isset($action)) $action = FALSE;
if (!isset($mail))   $mail = FALSE;


require_once($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/phlyadb.php');
$ADB = new phlyadb($WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php');

if (isset($WP_plug['adb_mode']) && $WP_plug['adb_mode']) {
    $WP_adb['baselink'] = $_SERVER['PHP_SELF'].'?action='.$action.'&mail='.$mail.'&'.give_passthrough(1)
            .'&WP_plug[adb_action]=main&WP_plug[adb_mode]=';
    $out = new fxl_template($WP_core['skin_path'].'/templates/adb.header.tpl');
    $out->assign(array
            ('link_adr' => htmlspecialchars($WP_adb['baselink'].'adr')
            ,'link_grp' => htmlspecialchars($WP_adb['baselink'].'groups')
            ,'link_exch' => htmlspecialchars($WP_adb['baselink'].'exch')
            ,'msg_adr' => $WP_adbmsg['HAdr']
            ,'msg_grp' => $WP_adbmsg['HGrp']
            ,'msg_exch' => $WP_adbmsg['HIEx']
            ));
    $WP_core['plug_output'] .= $out->get_output();
    unset($out);

    if (is_readable($WP_core['plugin_path'].'/inc.'.$WP_plug['adb_mode'].'.php')) {
        $WP_adb['modbaslink'] = $WP_adb['baselink'].$WP_plug['adb_mode'];
        include($WP_core['plugin_path'].'/inc.'.$WP_plug['adb_mode'].'.php');
    }
    $WP_core['plug_output'] .= $out->get_output();
    $action = 'plugged';
}
?>
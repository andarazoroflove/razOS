<?php
/* ------------------------------------------------------------------------- */
/* linker.php -> Steuerung der PlugIn-Einbindung                             */
/* (c) 2001-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Adreﬂbuch Basic                                                  */
/* v0.2.7                                                                    */
/* ------------------------------------------------------------------------- */
if (!isset($action)) $action = '';

switch ($action) {
case 'send':
    if (!isset($WP_ext['send_to'])) $WP_ext['send_to'] = '';
    $WP_ext['send_to'] .= '$WP_ext["send_to"]="<input type=hidden name=\"WP_plug[adb_action]\" '
            .'value=\"select\"><input type=\"image\" onMouseOver=\"nocheck=true;\" onMouseOut=\"nocheck=false;\" '
            .'name=\"WP_plug[send_adb]\" src=\"".$WP_core["skin_path"]."/images/'
            .'adb_sel.png\" border=0 title=\"".$WP_adbmsg["adbSel"]."\"></a><br />";';
    break;

case 'sms':
    if (!isset($WP_ext['sms_to'])) $WP_ext['sms_to'] = '';
    $WP_ext['sms_to'] .= '$WP_ext["sms_to"]="<input type=hidden name=\"WP_plug[adb_action]\" '
            .'value=\"select\"><input type=hidden name=\"WP_plug[adb_special]\" '
            .'value=\"mobile\"><input type=\"image\" name=\"WP_plug[send_adb]\" '
            .'src=\"".$WP_core["skin_path"]."/images/'
            .'adb_sel.png\" border=0 title=\"".$WP_adbmsg["adbSel"]."\"></a><br />";';
    break;

case 'setup':
    if (!isset($WP_ext['setup_menu']['link'])) $WP_ext['setup_menu']['link'] = array();
    $WP_ext['setup_menu'][] = array
            ('link' => $_SERVER['PHP_SELF'].'?WP_plug[adb_action]=main&WP_plug[adb_mode]=menu&'.give_passthrough(1)
            ,'text' => $WP_adbmsg['adbOrga']);
    break;

case 'read':
    if ((isset($what) && $what) || (isset($save_as) && $save_as)) return;
    if (!isset($WP_ext['read_to'])) $WP_ext['read_to'] = '';
    $WP_ext['read_to'] .= '$WP_ext["read_to"]="&nbsp;<a href=\"'.$_SERVER['PHP_SELF'].'?'
            .'WP_plug[adb_action]=main&WP_plug[adb_mode]=adr&WP_plug[adb_do]=add&'.give_passthrough(1)
            .'&mail=$mail&action=$action&WP_plug[adb_adr]=".base64_encode($eval_to)."\">'
            .'<img src=\"'.$WP_core['skin_path'].'/images/'
            .'adb_add.png\" border=0 title=\"'.$WP_adbmsg['adbAdd'].'\" align=absmiddle></a><br />";';

    if (!isset($WP_ext['read_from'])) $WP_ext['read_from'] = '';
    $WP_ext['read_from'] .= '$WP_ext[\'read_from\']="&nbsp;<a href=\"'.$_SERVER['PHP_SELF'].'?'
            .'WP_plug[adb_action]=main&WP_plug[adb_mode]=adr&WP_plug[adb_do]=add&'.give_passthrough(1)
            .'&mail=$mail&action=$action&WP_plug[adb_adr]=".base64_encode($eval_from)."\">'
            .'<img src=\"'.$WP_core['skin_path'].'/images/'
            .'adb_add.png\" border=0 title=\"'.$WP_adbmsg['adbAdd'].'\" align=absmiddle></a><br />";';
    break;
/* Disabled due to errors
case 'inbox':
    include($WP_core['plugin_path'].'/mod.inbox.php');
    break;
*/
}
?>
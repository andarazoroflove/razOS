<?php
/* ------------------------------------------------------------------------- */
/* inc.groups.php -> Verwaltung der Gruppen (Anlegen, Editieren, Löschen)    */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail LE Adreßbuch (Basic)                                             */
/* v0.0.3                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($WP_plug['adb_do']))   $WP_plug['adb_do']   = false;
if (!isset($WP_plug['adb_done'])) $WP_plug['adb_done'] = false;

if ('kill' == $WP_plug['adb_do']) {
    if ('yes' == $WP_plug['adb_done']) {
        if (isset($WP_plug['adb_yesiwant'])) {
            $ADB->adb_dele_group($id);
            unset($WP_plug['adb_do']);
        } else $WP_plug['adb_do'] = false;
    } else {
        $out = new fxl_template($WP_core['skin_path'].'/templates/adb.delegroup.tpl');
        $out->assign(array
                ('target' => $_SERVER['PHP_SELF']
                ,'msg_dele' => $WP_adbmsg['DelGrp']
                ,'adb_action' => $WP_plug['adb_action']
                ,'adb_do' => $WP_plug['adb_do']
                ,'adb_mode' => $WP_plug['adb_mode']
                ,'id' => $id
                ,'action' => $action
                ,'mail' => $mail
                ,'passthrough' => give_passthrough(2)
                ,'msg_yes' => $WP_adbmsg['yes']
                ,'msg_no' => $WP_adbmsg['no']
                ));
    }
}
if ('edit' == $WP_plug['adb_do'] || 'add' == $WP_plug['adb_do']) {
    if ('yes' == $WP_plug['adb_done']) {
        $error = FALSE;
        if (strlen($WPadb_grpname) < 1 || strlen($WPadb_grpname) > 32) $error = $WP_adbmsg['ELenGrpName'].'<br />'.LF;
        if ('edit' == $WP_plug['adb_do'] && !$error) {
            $exists = $ADB->adb_checkfor_groupname($_SESSION['WPs_uid'], $WPadb_grpname);
            if ($exists && $exists != $id) $error .= $WP_adbmsg['EGrpNameExists'].'<br />'.LF;
            if (!$error) {
                $ADB->adb_update_group($_SESSION['WPs_uid'], $id, $WPadb_grpname);
            }
        }
        if ('add' == $WP_plug['adb_do'] && !$error) {
            $exists = $ADB->adb_checkfor_groupname($_SESSION['WPs_uid'], $WPadb_grpname);
            if ($exists) $error .= $WP_adbmsg['EGrpNameExists'].'<br />'.LF;
            if (!$error) {
                $ADB->adb_add_group($_SESSION['WPs_uid'], $WPadb_grpname);
            }
        }
        if ($error) $WP_plug['adb_done'] = FALSE; else $WP_plug['adb_do'] = FALSE;
    }
    if (!$WP_plug['adb_done']) {
        if ('edit' == $WP_plug['adb_do']) {
            $vorbelegt = $ADB->adb_get_group($_SESSION['WPs_uid'], $id);
        }
        $out = new fxl_template($WP_core['skin_path'].'/templates/adb.editgroup.tpl');
        if (isset($error) && $error) {
            $out_e = $out->getblock('error');
            $out_e->assign('error', $error);
            $out->assign('error', $out_e);
        }
        $out->assign(array
                ('target' => $_SERVER['PHP_SELF']
                ,'msg_name' => $WP_adbmsg['name']
                ,'adb_action' => $WP_plug['adb_action']
                ,'adb_do' => $WP_plug['adb_do']
                ,'adb_mode' => $WP_plug['adb_mode']
                ,'id' => ('edit' == $WP_plug['adb_do']) ? $id : ''
                ,'action' => $action
                ,'mail' => $mail
                ,'name' => isset($vorbelegt) ? $vorbelegt : ''
                ,'passthrough' => give_passthrough(2)
                ,'msg_save' => $WP_adbmsg['save']
                ));
    }
}
if (!$WP_plug['adb_do']) {
    $out = new fxl_template($WP_core['skin_path'].'/templates/adb.groupoverview.tpl');
    $out->assign(array
            ('addlink' => $WP_adb['modbaslink'].'&amp;WP_plug[adb_do]=add'
            ,'msg_add' => $WP_adbmsg['addEntry']
            ));
    $i = 0;
    $out_l = $out->getblock('groupline');
    foreach ($ADB->adb_get_grouplist($_SESSION['WPs_uid'], 0) as $v) {
        $out_l->assign(array
                ('group' => $v['name']
                ,'editlink' => $WP_adb['modbaslink'].'&WP_plug[adb_do]=edit&id='.$v['gid']
                ,'delelink' => $WP_adb['modbaslink'].'&WP_plug[adb_do]=kill&id='.$v['gid']
                ,'msg_edit' => $WP_adbmsg['edit']
                ,'msg_dele' => $WP_adbmsg['dele']
                ));
        $out->assign('groupline', $out_l);
        $out_l->clear();
        $i++;
    }
}
?>
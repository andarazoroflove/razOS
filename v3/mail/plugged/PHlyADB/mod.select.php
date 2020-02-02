<?php
/* ------------------------------------------------------------------------- */
/* mod.select.php -> Auswahl von Adressen für den Versand                    */
/* (c) 2001-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Adreßbuch Basic                                                  */
/* v0.2.8                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($WP_core['sms_global_prefix'])) $WP_core['sms_global_prefix'] = FALSE;

if (isset($WP_plug['send_adb']) && $WP_plug['send_adb']) {
    require_once($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/phlyadb.php');
    $ADB = new phlyadb($WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php');
    if ($ADB->adb_get_adrcount($_SESSION['WPs_uid'], 0) > 0) {
        //
        $mail = isset($_REQUEST['mail']) ? $_REQUEST['mail'] : '';
        // Save us the hassle of hauling this data thorugh the forms
        if (!isset($_SESSION['saveform'])) $_SESSION['saveform'] = array
                ('body' => base64_encode($WP_send['body'])
                ,'from' => isset($WP_send['from']) ? $WP_send['from'] : ''
                ,'to' => isset($WP_send['to']) ? $WP_send['to'] : ''
                ,'from_profile' => isset($WP_send['from_profile']) ? $WP_send['from_profile'] : ''
                ,'cc' => isset($WP_send['cc']) ? $WP_send['cc'] : ''
                ,'bcc' => isset($WP_send['bcc']) ? $WP_send['bcc'] : ''
                ,'importance' => isset($WP_send['importance']) ? $WP_send['importance'] : ''
                ,'subj' => isset($WP_send['subj']) ? $WP_send['subj'] : ''
                ,'copytobox' => isset($_REQUEST['save_sent']) ? $_REQUEST['save_sent'] : ''
                ,'receipt_out' => isset($_REQUEST['receipt_out']) ? $_REQUEST['receipt_out'] : ''
                ,'sendway' => isset($WP_send['sendway']) ? $WP_send['sendway'] : ''
                );
        //
        
        if (!isset($WP_send['copytobox']) && isset($_REQUEST['save_sent'])) {
            $WP_send['copytobox'] = $_REQUEST['save_sent'];
        }
        if (isset($WP_plug['adb_special']) && 'mobile' == $WP_plug['adb_special']) {
            $out = new fxl_template($WP_core['skin_path'].'/templates/adb.select.sms.tpl');
        } else {
            $out = new fxl_template($WP_core['skin_path'].'/templates/adb.select.tpl');
        }
        $out->assign(array
                ('target' => $_SERVER['PHP_SELF']
                ,'content' => $WP_adbmsg['adrbContent']
                ,'msg_to' => $WP_adbmsg['To']
                ,'msg_cc' => $WP_adbmsg['Cc']
                ,'msg_bcc' => $WP_adbmsg['Bcc']
                ,'passthrough' => give_passthrough(2)
                ,'insert' => $WP_adbmsg['Insert']
                ,'action' => $action
                ,'mail' => $mail
                ));
        // Apply ordering
        $ord_by  = (isset($_REQUEST['ord_by']) && $_REQUEST['ord_by'])   ? $_REQUEST['ord_by']  : false;
        $ord_dir = (isset($_REQUEST['ord_dir']) && $_REQUEST['ord_dir']) ? $_REQUEST['ord_dir'] : false;
        
        $grp_link_sort = 'ord_by=group&ord_dir=asc';
        $rec_link_sort = 'ord_by=displayname&ord_dir=asc';
        
        $sort_base_link = '?action='.$action.'&mail='.$mail.'&WP_plug[send_adb]=1&WP_plug[adb_action]=select&'.give_passthrough();
        if (isset($WP_plug['adb_special'])) $sort_base_link .= '&WP_plug[adb_special]='.$WP_plug['adb_special'];
        
        if (!$ord_by) {
            $getblock_grp = 'ord_grp_none';
            $getblock_rec = 'ord_rec_none';
        } elseif ($ord_by == 'displayname') {
            $getblock_grp = 'ord_grp_none';
            if ($ord_dir == 'asc') {
                $getblock_rec = 'ord_rec_down';
                $rec_link_sort = 'ord_by=displayname&ord_dir=desc';
            } else {
                $getblock_rec = 'ord_rec_up';
            }
        } else {
            $getblock_rec = 'ord_rec_none';
            if ($ord_dir == 'asc') {
                $getblock_grp = 'ord_grp_down';
                $grp_link_sort = 'ord_by=group&ord_dir=desc';
            } else {
                $getblock_grp = 'ord_grp_up';
            }            
        }    
        $out_ord_grp = $out->getblock($getblock_grp);
        $out_ord_rec = $out->getblock($getblock_rec);
        
        $out_ord_grp->assign(array
                ('msg_sort' => $WP_adbmsg['SortByThis']
                ,'skin_path' => $WP_core['skin_path']
                ,'link_sort' => $_SERVER['PHP_SELF'].htmlspecialchars($sort_base_link.'&'.$grp_link_sort)
                ,'msg_group' => $WP_adbmsg['group']
                ));
        $out_ord_rec->assign(array
                ('msg_sort' => $WP_adbmsg['SortByThis']
                ,'skin_path' => $WP_core['skin_path']
                ,'link_sort' => $_SERVER['PHP_SELF'].htmlspecialchars($sort_base_link.'&'.$rec_link_sort)
                ,'msg_receiver' => $WP_adbmsg['Receiver']
                ));
                        
        $out->assign(array($getblock_grp => $out_ord_grp, $getblock_rec => $out_ord_rec));        

                
        $key = 0;
        $t_ent = $out->get_block('entry');
        $out_name = $t_ent->get_block('name');
        $out_sel  = $t_ent->get_block('selection');
        if (isset($WP_plug['adb_special']) && 'mobile' == $WP_plug['adb_special']) {
            foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0, '', '', 0, 0, $ord_by, $ord_dir) as $line) {
                $fetched = array();
                // Find valid phone numbers
                foreach (array('cellular', 'tel_private', 'tel_business') as $field) {
                    if (!$line[$field]) continue;
                    $test = $line[$field];
                    // Automatically add country code, if needed
                    if (!preg_match('!^(\+|00)!', $test) && $WP_core['sms_global_prefix']) {
                        $test = preg_replace('!^0(?=[1-9]+)!', $WP_core['sms_global_prefix'], $test);
                    }
                    $test = preg_replace('!^\+!', '00', $test);
                    $test = preg_replace('![^0-9]!', '', $test);
                    if (!preg_match('!^00!', $test)) continue;
                    $fetched[$test] = $line[$field];
                }
                if (empty($fetched)) continue;
                //
                // Only attach Groupname, if one is given
                $groupstring = ($line['group']) ? $line['group'] : '';
                $out_name->assign('group', $groupstring);
                $out_name->assign('nickname', ($line['nick'])
                                            ? $line['nick']
                                            : $line['firstname'].' '.$line['lastname']);
                $t_ent->assign('name', $out_name);
                $out_name->clear();
                foreach ($fetched as $nice => $raw) {
                    $out_sel->assign(array
                            ('key' => $key, 'value' => $raw, 'mobile' => $raw
                            ,'skin_path' => $WP_core['skin_path']
                            ,'msg_sel' => $WP_adbmsg['Insert']
                            ));
                    $t_ent->assign('selection', $out_sel);
                    $out_sel->clear();
                    ++$key;
                }
                $out->assign('entry', $t_ent);
                $t_ent->clear();
            }
        } else {
            foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0, '', '', 0, 0, $ord_by, $ord_dir) as $line) {
                if ($line['email1'] == '' && $line['email2'] == '') continue;
                // Only attach Groupname, if one is given
                $groupstring = ($line['group']) ? $line['group'] : '';
                
                $out_name->assign('nickname', ($line['nick']) ? $line['nick'] : $line['firstname'].' '.$line['lastname']);
                $out_name->assign('group', $groupstring);
                $t_ent->assign('name', $out_name);
                $out_name->clear();
                if ($line['email1']) {
                    $out_sel->assign(array('key' => $key, 'email' => $line['email1']));
                    $t_ent->assign('selection', $out_sel);
                    $out_sel->clear();
                }
                if ($line['email2']) {
                    ++$key;
                    $out_sel->assign(array('key' => $key, 'email' => $line['email2']));
                    $t_ent->assign('selection', $out_sel);
                    $out_sel->clear();
                }
                $out->assign('entry', $t_ent);
                $t_ent->clear();
                ++$key;
            }
        }
        $action = 'plugged';
        $WP_core['plug_output'] = $out->get_output();
        unset($out);
    } else {
        $WP_send['reload'] = '1';
        $WP_send['send_sig'] = 'true';
    }
}
if ((isset($WP_plug['done_adb']) && $WP_plug['done_adb']) || isset($WP_plug['adb_to'])) {
    foreach ($_SESSION['saveform'] as $k => $v) {
        $WP_send[$k] = $v;
    }
    unset($_SESSION['saveform']);    
    $WP_send['reload']   = '1';
    $WP_send['send_sig'] = 'true';
    $WP_send['body']     = base64_decode($WP_send['body']);

    if (isset($WP_plug['adb_to']) && is_array($WP_plug['adb_to'])) {
        foreach ($WP_plug['adb_to'] as $key => $val) {
            $WP_send['to'] .= ','.$WP_plug['adb_email'][$key];
        }
        $WP_send['to'] = preg_replace('!^(\ )*,!', '', $WP_send['to']);
    }
    if (isset($WP_plug['adb_cc']) && is_array($WP_plug['adb_cc'])) {
        foreach ($WP_plug['adb_cc'] as $key => $val) {
            $WP_send['cc'] .= ',' . $WP_plug['adb_email'][$key];
        }
        $WP_send['cc'] = preg_replace('/^(\ )*,/', '', $WP_send['cc']);
    }
    if (isset($WP_plug['adb_bcc']) && is_array($WP_plug['adb_bcc'])) {
        foreach ($WP_plug['adb_bcc'] as $key => $val) {
            $WP_send['bcc'] .= ',' . $WP_plug['adb_email'][$key];
        }
        $WP_send['bcc'] = preg_replace('/^(\ )*,/', '', $WP_send['bcc']);
    }
}
?>
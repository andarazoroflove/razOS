<?php
/* ------------------------------------------------------------------------- */
/* inc.adr.php                                                               */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Adreßbuch Basic                                                  */
/* v0.3.8                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($WP_plug['adb_do'])) $WP_plug['adb_do'] = FALSE;

$WP_core['length_links'] = (!isset($WP_core['length_links']) || !$WP_core['length_links'])
        ? 32
        : $WP_core['length_links'];

if ('add' == $WP_plug['adb_do'] || 'edit' == $WP_plug['adb_do']) {
    if (isset($WP_plug['adb_done'])) {
        $WP_plug_adbfield['owner'] = $_SESSION['WPs_uid'];
        $WP_plug_adbfield['aid'] = isset($id) ? $id : 0;
        $WP_plug_adbfield['birthday']['year'] = (isset($WP_plug_adbfield['birthday']['year']))
                                              ? ($WP_plug_adbfield['birthday']['year']+0) : '0000';
        $WP_plug_adbfield['birthday'] = $WP_plug_adbfield['birthday']['year']
                                       .'-'
                                       .(isset($WP_plug_adbfield['birthday']['month'])
                                              ? $WP_plug_adbfield['birthday']['month']+0
                                              : '0'
                                        )
                                       .'-'
                                       .(isset($WP_plug_adbfield['birthday']['day'])
                                              ? $WP_plug_adbfield['birthday']['day']+0
                                              : '0'
                                        );
        if ('edit' == $WP_plug['adb_do']) {
            $ADB->adb_update_address($WP_plug_adbfield);
        } else {
            $ADB->adb_add_address($WP_plug_adbfield);
        }
        $WP_plug['adb_do'] = ('edit' == $WP_plug['adb_do']) ? 'view' : FALSE;
        header('Location: '.$WP_adb['baselink'].$WP_plug['adb_mode'].'&WP_plug[adb_do]='.$WP_plug['adb_do'].'&id='.$id);
    }
    if (!isset($WP_plug['adb_done'])) {
        if ('edit' == $WP_plug['adb_do']) {
            if (!isset($WP_plug_adbfield)) {
                $line = $ADB->adb_get_address($id);
                foreach ($line as $k => $v) $line[$k] = htmlspecialchars($v);
            } else {
                $line = &$WP_plug_adbfield;
            }
        } else {
            include_once($WP_core['page_path'].'/lib/message.decode.php');
            if (!isset($WP_plug_adbfield['email1']) && isset($WP_plug['adb_adr'])) {
                $WP_plug['adb_adr'] = parse_email_address(base64_decode($WP_plug['adb_adr']));
                $WP_plug_adbfield['email1'] = $WP_plug['adb_adr'][0];
                $WP_plug_adbfield['nick']   = $WP_plug['adb_adr'][1];
            }
            $line = &$WP_plug_adbfield;
            $id = FALSE;
        }
    }
    $out = new fxl_template($WP_core['skin_path'].'/templates/adb.editaddress.tpl');
    $out->assign(array
            ('target' => $_SERVER['PHP_SELF']
            ,'msg_adbadd' => $WP_adbmsg['adbAdd']
            ,'msg_group' => $WP_adbmsg['group']
            ,'msg_none' => $WP_adbmsg['none']
            ,'msg_nick' => $WP_adbmsg['nick']
            ,'msg_fnam' => $WP_adbmsg['fnam']
            ,'msg_snam' => $WP_adbmsg['snam']
            ,'msg_email1' => $WP_adbmsg['emai1']
            ,'msg_email2' => $WP_adbmsg['emai2']
            ,'msg_www' => $WP_adbmsg['www']
            ,'msg_address' => $WP_adbmsg['address']
            ,'msg_fon' => $WP_adbmsg['fon']
            ,'msg_fon2' => $WP_adbmsg['fon2']
            ,'msg_cell' => $WP_adbmsg['cell']
            ,'msg_fax' => $WP_adbmsg['fax']
            ,'msg_bday' => $WP_adbmsg['bday']
            ,'msg_bday_format' => $WP_adbmsg['bday_format']
            ,'msg_cmnt' => $WP_adbmsg['cmnt']
            ,'nick' => isset($line['nick']) ? $line['nick'] : ''
            ,'firstname' => isset($line['firstname']) ? $line['firstname'] : ''
            ,'lastname' => isset($line['lastname']) ? $line['lastname'] : ''
            ,'email1' => isset($line['email1']) ? $line['email1'] : ''
            ,'email2' => isset($line['email2']) ? $line['email2'] : ''
            ,'www' => isset($line['www']) ? $line['www'] : ''
            ,'address' => isset($line['address']) ? $line['address'] : ''
            ,'tel_private' => isset($line['tel_private']) ? $line['tel_private'] : ''
            ,'tel_business' => isset($line['tel_business']) ? $line['tel_business'] : ''
            ,'cellular' => isset($line['cellular']) ? $line['cellular'] : ''
            ,'fax' => isset($line['fax']) ? $line['fax'] : ''
            ,'comments' => isset($line['comments']) ? $line['comments'] : ''
            ,'passthrough' => give_passthrough(2)
            ,'action' => $action
            ,'mail' => $mail
            ,'id' => $id
            ,'adb_action' =>  $WP_plug['adb_action']
            ,'adb_do' => $WP_plug['adb_do']
            ,'adb_mode' => $WP_plug['adb_mode']
            ,'msg_save' => $WP_adbmsg['save']
            ));
    $out_l = $out->getblock('groupline');
    foreach ($ADB->adb_get_grouplist($_SESSION['WPs_uid'], 0) as $v) {
        $out_l->assign(array('id' => $v['gid'], 'name' => $v['name']));
        if (isset($line['gid']) && $v['gid'] == $line['gid']) $out_l->assign_block('selected');
        $out->assign('groupline', $out_l);
        $out_l->clear();
    }
    // Handle Birthday
    if (isset($line['birthday']) && $line['birthday']) {
        list($byear, $bmonth, $bday) = split('-', $line['birthday']);
        $byear  = (int) $byear;
        $bmonth = (int) $bmonth;
        $bday   = (int) $bday;
    } else {
        $byear = $bmonth = $bday = FALSE;
    }
    // Output Days of month
    $out_bd = $out->getblock('bday_dayline');
    foreach (range(0, 31) as $day) {
        $out_bd->assign('day', $day);
        if ($bday && $bday == $day) {
            $out_bd->assign_block('selected');
        }
        $out->assign('bday_dayline', $out_bd);
        $out_bd->clear();
    }
    // Output Months of year
    $out_bm = $out->getblock('bday_monthline');
    foreach (range(0, 12) as $month) {
        $out_bm->assign('month', $month);
        if ($bmonth && $bmonth == $month) {
            $out_bm->assign_block('selected');
        }
        $out->assign('bday_monthline', $out_bm);
        $out_bm->clear();
    }
    $out->assign('birthday_year', ($byear) ? $byear : '');
    $action = 'plugged';
    if (isset($WP_plug['adb_done'])) {
        $WP_plug['adb_do'] = ('edit' == $WP_plug['adb_do']) ? 'view' : FALSE;
    }
}
if ('kill' == $WP_plug['adb_do']) {
    if (!isset($WP_plug['adb_done'])) {
        $out = new fxl_template($WP_core['skin_path'].'/templates/adb.deleaddress.tpl');
        $out->assign(array
                ('target' => $_SERVER['PHP_SELF']
                ,'msg_dele' => $WP_adbmsg['DelAdr']
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
    } elseif ('yes' == $WP_plug['adb_done'] && isset($WP_plug['adb_yesiwant'])) {
        $ADB->adb_dele_address($id);
        $WP_plug['adb_do'] = FALSE;
    } else $WP_plug['adb_do'] = 'view';
}
if ('view' == $WP_plug['adb_do']) {
    $line = $ADB->adb_get_address($id);
    $line['group'] = $ADB->adb_get_group($_SESSION['WPs_uid'], $line['gid']);
    if ('' == $line['group']) $line['group'] = '&lt; '.$WP_adbmsg['none'].' &gt;';
    // Handle birthday entry
    $birthday = explode('-', $line['birthday']);
    $byear  = isset($birthday[0]) ? (int) $birthday[0] : false;
    $bmonth = isset($birthday[1]) ? (int) $birthday[1] : false;
    $bday   = isset($birthday[2]) ? (int) $birthday[2] : false;
    if ($byear && !$bmonth && !$bday) $line['birthday'] = $byear;
    elseif ($byear && ($bmonth || $bday)) {
        $line['birthday'] = date($WP_msg['dateformat_old'], mktime(0, 0, 0, $bmonth, $bday, $byear));
    } elseif (!$byear && $bmonth && $bday) {
        $line['birthday'] = date($WP_msg['dateformat_daymonth'], mktime(0, 0, 0, $bmonth, $bday));
    } else {
        $line['birthday'] = '';
    }
    // Make email addresses turn into links to send a mail to
    if ($line['email1']) {
        $line['email1'] = '<a href="'.mailto_2_send('mailto:'.$line['email1']).'">'.$line['email1'].'</a>';
    }
    if ($line['email2']) {
        $line['email2'] = '<a href="'.mailto_2_send('mailto:'.$line['email2']).'">'.$line['email2'].'</a>';
    }
    //
    $out = new fxl_template($WP_core['skin_path'].'/templates/adb.adrview.tpl');
    $out->assign(array
            ('msg_nick' => $WP_adbmsg['nick']
            ,'msg_fnam' => $WP_adbmsg['fnam']
            ,'msg_snam' => $WP_adbmsg['snam']
            ,'msg_email1' => $WP_adbmsg['emai1']
            ,'msg_email2' => $WP_adbmsg['emai2']
            ,'msg_www' => $WP_adbmsg['www']
            ,'msg_address' => $WP_adbmsg['address']
            ,'msg_fon' => $WP_adbmsg['fon']
            ,'msg_fon2' => $WP_adbmsg['fon2']
            ,'msg_cell' => $WP_adbmsg['cell']
            ,'msg_fax' => $WP_adbmsg['fax']
            ,'msg_bday' => $WP_adbmsg['bday']
            ,'msg_cmnt' => $WP_adbmsg['cmnt']
            ,'msg_group' => $WP_adbmsg['group']
            ,'nick' => $line['nick']
            ,'group' => $line['group']
            ,'firstname' => $line['firstname']
            ,'lastname' => $line['lastname']
            ,'email1' => $line['email1']
            ,'email2' => $line['email2']
            ,'www' => $line['www']
            ,'address' => nl2br($line['address'])
            ,'tel_private' => $line['tel_private']
            ,'tel_business' => $line['tel_business']
            ,'cellular' => $line['cellular']
            ,'fax' => $line['fax']
            ,'birthday' => $line['birthday']
            ,'comments' => nl2br($line['comments'])
            ,'link_edit' => $WP_adb['modbaslink'].'&amp;WP_plug[adb_do]=edit&amp;id='.$id
            ,'link_dele' => $WP_adb['modbaslink'].'&amp;WP_plug[adb_do]=kill&amp;id='.$id
            ,'link_overview' => $WP_adb['modbaslink']
            ,'msg_edit' => $WP_adbmsg['edit']
            ,'msg_dele' => $WP_adbmsg['dele']
            ,'msg_overview' => $WP_adbmsg['toOver']
            ));
    list ($prev, $next) = $ADB->adb_get_prevnext($id);
    if ($prev) {
        $out_l = $out->getblock('skimleft');
        $out_l->assign(array
                ('link' => $WP_adb['modbaslink'].'&amp;WP_plug[adb_do]=view&amp;id='.$prev
                ,'skin_path' => $WP_core['skin_path']
                ,'but_last' => '&lt;&lt;'
                ));
        $out->assign('skimleft', $out_l);
    }
    if ($next) {
        $out_r = $out->getblock('skimright');
        $out_r->assign(array
                ('link' => $WP_adb['modbaslink'].'&amp;WP_plug[adb_do]=view&amp;id='.$next
                ,'skin_path' => $WP_core['skin_path']
                ,'but_next' => '&gt;&gt;'
                ));
        $out->assign('skimright', $out_r);
    }
}
if (!$WP_plug['adb_do']) {
    $out = new fxl_template($WP_core['skin_path'].'/templates/adb.adroverview.tpl');
    $out->assign(array
             ('msg_name' => $WP_adbmsg['name']
             ,'msg_email' => $WP_adbmsg['emai1']
             ,'msg_group' => $WP_adbmsg['group']
             ,'add_link' => $WP_adb['modbaslink'].'&amp;WP_plug[adb_do]=add'
             ,'msg_add' => $WP_adbmsg['addEntry']
             ));
    if (!$ADB->adb_get_adrcount($_SESSION['WPs_uid'], 0)) {
        $out_n = $out->getblock('noentry');
        $out_n->assign('msg_noentry', str_replace('$1', 0, $WP_adbmsg['adbEntries']));
        $out->assign('noentry', $out_n);
    } else {
        $out_l = $out->getblock('line');
        foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0) as $line) {
            if (!$line['group']) $line['group'] = '&lt;'.$WP_adbmsg['none'].'&gt;';
            if (trim($line['displayname']) != '') {
                $name = $line['displayname'];
            } elseif (trim($line['nick']) != '') {
                $name = $line['nick'];
            } elseif (strlen(trim($line['firstname'])) > 1 || strlen(trim($line['lastname'])) > 1) {
                $name = $line['firstname'].' '.$line['lastname'];
            } else {
                $name = '&lt;'.$WP_adbmsg['unnamed'].'&gt;';
            }
            if (strlen($name) > $WP_core['length_links']) {
                $name = '<span title="'.htmlspecialchars($name).'">'
                        .htmlspecialchars(substr($name, 0, $WP_core['length_links']-3)).'...</span>';
            }
            // Make email addresses turn into links to send a mail to
            if ($line['email1']) {
                $viewmail = (strlen($line['email1']) > $WP_core['length_links'])
                        ? substr($line['email1'], 0, $WP_core['length_links']-3).'...'
                        : $line['email1'];
                $line['email1'] = '<a href="'.mailto_2_send('mailto:'.$line['email1'])
                        .'" title="'.$line['email1'].'">'.$viewmail.'</a>';
            }
            if ($line['email2']) {
                $viewmail = (strlen($line['email2']) > $WP_core['length_links'])
                        ? substr($line['email2'], 0, $WP_core['length_links']-3).'...'
                        : $line['email2'];
                $line['email2'] = '<a href="'.mailto_2_send('mailto:'.$line['email2'])
                        .'" title="'.$line['email2'].'">'.$viewmail.'</a>';
            }
            $out_l->assign(array
                    ('viewlink' => $WP_adb['modbaslink'].'&amp;WP_plug[adb_do]=view&amp;id='.$line['aid']
                    ,'name' => $name
                    ,'email' => ($line['email1']) ? $line['email1'] : $line['email2']
                    ,'group' => $line['group']
                    ));
            $out->assign('line', $out_l);
            $out_l->clear();
        }
    }
}

?>
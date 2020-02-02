<?php
/* ------------------------------------------------------------------------- */
/* inc.exch.php -> Import/Export of Addresses                                */
/* (c) 2002-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Adreßbuch Basic                                                  */
/* v0.3.1                                                                    */
/* ------------------------------------------------------------------------- */

/*
MicroSoft makes up for a lot of noise again. They do change the format of their
address book file from version to version and these do even differ between
Outlook and Outlook Express.
That makes it almost impossible to support a useful im/export feature for the
Outlook family of products.
*/

if (!isset($WP_plug['adb_do'])) $WP_plug['adb_do'] = false;

if ('export' == $WP_plug['adb_do']) {
    switch ($WP_plug['adb_exform']) {
    case 'LDIF':
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=PHlyMailAddresses.ldif');
        foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0) as $line) {
            echo 'dn: cn='.$line['firstname'].' '.$line['lastname'],',mail='.$line['email1'].LF;
            echo 'objectclass: top'.LF.'objectclass: person'.LF;
            echo 'objectclass: organizationalPerson'.LF.'objectclass: inetOrgPerson'.LF;
            echo 'cn: '.$line['firstname'].' '.$line['lastname'].LF;
            echo 'xmozillanickname: '.$line['nick'].LF;
            echo 'mail: '.$line['email1'].LF;
            echo 'givenname: '.$line['firstname'].LF;
            echo 'sn: '.$line['lastname'].LF;
            echo 'description:: '.base64_encode($line['comments']).LF;
            echo 'postaladdress:: '.base64_encode($line['address']).LF;
            echo 'telephonenumber: '.$line['tel_business'].LF;
            echo 'homephone: '.$line['tel_private'].LF;
            echo 'facsimiletelephonenumber: '.$line['fax'].LF;
            echo 'cellphone: '.$line['cellular'].LF;
            echo 'homeurl: '.$line['www'].LF.LF;
        }
        echo LF;
        break;
    case 'MSOutl':
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=PHlyMailAddresses.csv');
        foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0) as $line) {
            foreach ($line as $k => $v) {
                $line[$k] = str_replace('"', "'", $v);
                $line[$k] = preg_replace('!('.CRLF.'|'.LF.')!', ' ', $line[$k]);
            }
            echo '"'.$line['nick'].'","'.$line['firstname'].'","'.$line['lastname'].'","'
                    .$line['address'].'","","","","'.$line['birthday'].'","'.$line['comments'].'","'
                    .$line['www'].'","'.$line['email1'].'","'.$line['email2'].'","'
                    .$line['tel_private'].'","'.$line['tel_business'].'","'.$line['cellular'].'","'
                    .$line['fax'].'"'.LF;
        }
        break;
    case 'MSOutlEx':
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=PHlyMailAddresses.csv');
        foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0) as $line) {
            foreach ($line as $k => $v) {
                $line[$k] = str_replace('"', "'", $v);
                $line[$k] = preg_replace('!('.CRLF.'|'.LF.')!', ' ', $line[$k]);
            }
            echo '"'.$line['nick'].'";"'.$line['firstname'].'";"'.$line['lastname'].'";"'
                    .$line['address'].'";"";"";"";"'.$line['birthday'].'";"'.$line['comments'].'";"'
                    .$line['www'].'";"'.$line['email1'].'";"'.$line['email2'].'";"'
                    .$line['tel_private'].'";"'.$line['tel_business'].'";"'.$line['cellular'].'";"'
                    .$line['fax'].'"'.LF;
        }
        break;
    case 'PHlyADB':
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=PHlyMailAddresses.ldif');
        foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0) as $line) {
            echo 'dn: cn='.$line['firstname'].' '.$line['lastname'],',mail='.$line['email1'].LF;
            echo 'objectclass: top'.LF.'objectclass: person'.LF;
            echo 'objectclass: organizationalPerson'.LF.'objectclass: inetOrgPerson'.LF;
            echo 'cn: '.$line['firstname'].' '.$line['lastname'].LF;
            echo 'xmozillanickname: '.$line['nick'].LF;
            echo 'mail: '.$line['email1'].LF;
            echo 'xphlymailemail2: '.$line['email2'].LF;
            echo 'givenname: '.$line['firstname'].LF;
            echo 'sn: '.$line['lastname'].LF;
            echo 'description:: '.base64_encode($line['comments']).LF;
            echo 'postaladdress:: '.base64_encode($line['address']).LF;
            echo 'telephonenumber: '.$line['tel_business'].LF;
            echo 'homephone: '.$line['tel_private'].LF;
            echo 'facsimiletelephonenumber: '.$line['fax'].LF;
            echo 'cellphone: '.$line['cellular'].LF;
            echo 'xphlymailbirthday: '.$line['birthday'].LF;
            echo 'xphlymailcompany:: '.base64_encode($line['company']).LF;
            echo 'xphlymailfree1:: '.base64_encode($line['free1']).LF;
            echo 'xphlymailfree2:: '.base64_encode($line['free2']).LF;
            echo 'xphlymailgroup: '.$line['company'].LF;
            echo 'homeurl: '.$line['www'].LF.LF;
        }
        echo LF;
        break;
    case 'CSV':
        $db_fieldlist = array
                (0 => 'nick'
                ,1 => 'firstname'
                ,2 => 'lastname'
                ,3 => 'company'
                ,4 => 'address'
                ,5 => 'email1'
                ,6 => 'email2'
                ,7 => 'tel_private'
                ,8 => 'tel_business'
                ,9 => 'cellular'
                ,10 => 'fax'
                ,11 => 'www'
                ,12 => 'birthday'
                ,13 => 'comments'
                );

        $db_fieldnames = array
                (0 => $WP_adbmsg['nick']
                ,1 => $WP_adbmsg['fnam']
                ,2 => $WP_adbmsg['snam']
                ,3 => $WP_adbmsg['company']
                ,4 => $WP_adbmsg['address']
                ,5 => $WP_adbmsg['emai1']
                ,6 => $WP_adbmsg['emai2']
                ,7 => $WP_adbmsg['fon']
                ,8 => $WP_adbmsg['fon2']
                ,9 => $WP_adbmsg['cell']
                ,10 => $WP_adbmsg['fax']
                ,11 => $WP_adbmsg['www']
                ,12 => $WP_adbmsg['bday']
                ,13 => $WP_adbmsg['cmnt']
                );
        if (isset($selected_fields)) {
            if (!isset($delimiter) || !$delimiter) $delimiter = ';';
            $delimiter = $delimiter{0}; // Make sure just to use a single char delimiter

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=PHlyMailAddresses.csv');

            // If requested, output a descriptive line containing the field names
            // These depend on the selected frontend language
            if (isset($fieldnames) && $fieldnames) {
                $out = false;
                foreach ($selected_fields as $dbfield) {
                    if ($out) echo $delimiter;
                    echo isset($is_quoted)
                            ? '"'.$db_fieldnames[$dbfield].'"'
                            : $db_fieldnames[$dbfield];
                    $out = true;
                }
                echo LF;
            }

            foreach ($ADB->adb_get_adridx($_SESSION['WPs_uid'], 0) as $line) {
                $out = false;
                foreach ($selected_fields as $dbfield) {
                    if ($out) echo $delimiter;
                    if ($dbfield == -1) {
                        $field = '';
                    } elseif (isset($db_fieldlist[$dbfield])) {
                        $field = $line[$db_fieldlist[$dbfield]];
                    } else {
                        $field = '';
                    }
                    echo isset($is_quoted)
                            ? '"'.$field.'"'
                            : $field;
                    $out = true;
                }
                echo LF;
            }
        } else {
            $out = new fxl_template($WP_core['skin_path'].'/templates/adb.exch_expcsv.tpl');
            if (isset($fieldnames) && $fieldnames) {
                $out->assign_block('if_fieldnames');
            }
            if (isset($is_quoted) && $is_quoted) {
                $out->assign_block('if_quoted');
            }
            $out->assign(array
                    ('about_selection' => $WP_adbmsg['csvImAboutSelection']
                    ,'sel_size' => count($db_fieldlist)
                    ,'msg_select' => $WP_adbmsg['select']
                    ,'legend_selection' => $WP_adbmsg['csvImLegendSelection']
                    ,'msg_in_csv' => $WP_adbmsg['csvExInCSV']
                    ,'msg_from_db' => $WP_adbmsg['csvExFromDB']
                    ,'msg_space' => $WP_adbmsg['csvExSpace']
                    ,'msg_add_space' => $WP_adbmsg['csvExAddSpace']
                    ,'delimiter' => $delimiter
                    ,'msg_save' => $WP_adbmsg['Export']
                    ,'form_action' => htmlspecialchars
                            ($_SERVER['PHP_SELF'].'?action='.$action.'&mail='.$mail
                            .'&'.give_passthrough(1).'&WP_plug[adb_action]=main&WP_plug[adb_mode]=exch'
                            .'&WP_plug[adb_do]=export&WP_plug[adb_exform]=CSV')
                    ));
            $t_csv = $out->get_block('dbline');
            foreach ($db_fieldlist as $k => $v) {
                $t_csv->assign(array('id' => $k, 'value' => $db_fieldnames[$k]));
                $out->assign('dbline', $t_csv);
                $t_csv->clear();
            }
            return;
        }
        break;
    default:
        if (!isset($error)) $error = false;
        $error .= $WP_adbmsg['unkExpFrmt'].LF;
        $WP_plug['adb_do'] = false;
        break;
    }
    if (!isset($error)) exit;
}
if ('import' == $WP_plug['adb_do']) {
    $imported = 0;
    if (isset($_FILES['WP_plug_adb_imfile']) || isset($_SESSION['WP_impfile'])) {
        if (!isset($_FILES['WP_plug_adb_imfile']) && isset($_SESSION['WP_impfile'])) {
            $file = $_SESSION['WP_impfile'];
            unset($_SESSION['WP_impfile']);
        } elseif (is_uploaded_file($_FILES['WP_plug_adb_imfile']['tmp_name'])) {
            $file = file($_FILES['WP_plug_adb_imfile']['tmp_name']);
        }
        switch ($WP_plug['adb_imform']) {
        case 'LDIF':
        case 'PHlyADB':
            $file = explode(LF.LF, preg_replace('!(\r\n|\r|\n)!', LF, join('', $file)));
            foreach ($file as $key => $value) {
                $save = false;
                foreach (array
                        ('nick' => 'xmozillanickname'
                        ,'firstname' => 'givenname'
                        ,'lastname' => 'sn'
                        ,'company' => 'xphlymailcompany'
                        ,'address' => 'postaladdress'
                        ,'email1' => 'mail'
                        ,'email2' => 'xphlymailemail2'
                        ,'tel_private' => 'homephone'
                        ,'tel_business' => 'telephonenumber'
                        ,'cellular' => 'cellphone'
                        ,'fax' => 'facsimiletelephonenumber'
                        ,'www' => 'homeurl'
                        ,'birthday' => 'xphlymailbirthday'
                        ,'comments' => 'description'
                        ,'free1' => 'xphlymailfree1'
                        ,'free2' => 'xphlymailfree2'
                        ,'group' => 'xphlymailgroup'
                        ) as $field => $needle) {
                    if ($needle && preg_match('!^'.$needle.':(:?)\ ?(.+)$!im', $value, $found)) {
                        if ($found[1]) $found[2] = base64_decode($found[2]);
                        if ($found[2]) $save[$field] = $found[2];
                    }
                }
                if (!empty($save)) {
                    $save['owner'] = $_SESSION['WPs_uid'];
                    $ADB->adb_add_address($save);
                    ++$imported;
                }
            }
            break;
        case 'MSOutl':
            break;
        case 'MSOutlEx':
            break;
        case 'MSOutlEx6':
            foreach ($file as $key => $value) {
                $line = explode(';', str_replace('"', '', trim($value)));
                $save = array();
                foreach (array
                        ('nick' => 3
                        ,'firstname' => array(0, 2)
                        ,'lastname' => 1
                        ,'company' => 25
                        ,'address' => array(6, 7, 8, 9, 10)
                        ,'email1' => 5
                        ,'email2' => false
                        ,'tel_private' => 12
                        ,'tel_business' => 22
                        ,'cellular' => 14
                        ,'fax' => 23
                        ,'www' => 15
                        ,'birthday' => false
                        ,'comments' => 29
                        ,'free1' => false
                        ,'free2' => false
                        ,'group' => false
                        ) as $field => $needle) {
                    if (false === $needle) {
                        // Not mapped
                        continue;
                    } elseif (is_array($needle)) {
                        // Collected fields: Stored in one field within PHlyADB, but in
                        // various fields of the source
                        $save[$field] = false;
                        foreach ($needle as $part) {
                            if ($line[$part]) $save[$field][] = $line[$part];
                        }
                        if (!empty($save[$field])) {
                            $save[$field] = join(' ', $save[$field]);
                        } else {
                            unset($save[$field]);
                        }
                    } else {
                        // 1:1 translation
                        if ($line[$needle]) $save[$field] = $line[$needle];
                    }
                }
                if (!empty($save)) {
                    $save['owner'] = $_SESSION['WPs_uid'];
                    $ADB->adb_add_address($save);
                    ++$imported;
                }
            }
            break;
        case 'CSV':
            $db_fieldlist = array
                    (0 => 'nick'
                    ,1 => 'firstname'
                    ,2 => 'lastname'
                    ,3 => 'company'
                    ,4 => 'address'
                    ,5 => 'email1'
                    ,6 => 'email2'
                    ,7 => 'tel_private'
                    ,8 => 'tel_business'
                    ,9 => 'cellular'
                    ,10 => 'fax'
                    ,11 => 'www'
                    ,12 => 'birthday'
                    ,13 => 'comments'
                    );

            $db_fieldnames = array
                    (0 => $WP_adbmsg['nick']
                    ,1 => $WP_adbmsg['fnam']
                    ,2 => $WP_adbmsg['snam']
                    ,3 => $WP_adbmsg['company']
                    ,4 => $WP_adbmsg['address']
                    ,5 => $WP_adbmsg['emai1']
                    ,6 => $WP_adbmsg['emai2']
                    ,7 => $WP_adbmsg['fon']
                    ,8 => $WP_adbmsg['fon2']
                    ,9 => $WP_adbmsg['cell']
                    ,10 => $WP_adbmsg['fax']
                    ,11 => $WP_adbmsg['www']
                    ,12 => $WP_adbmsg['bday']
                    ,13 => $WP_adbmsg['cmnt']
                    );
            if (isset($selected_fields)) {
                foreach ($file as $k => $line) {
                    if (0 == $k && isset($fieldnames) && $fieldnames) {
                        continue;
                    }
                    if (isset($is_quoted) && $is_quoted) {
                        $line = str_replace('"', '', $line);
                    }
                    if (!isset($delimiter) || !$delimiter) $delimiter = ';';
                    $delimiter = $delimiter{0}; // Make sure just to use a single char delimiter
                    $line = explode($delimiter, trim($line));
                    $save = array();
                    foreach ($selected_fields as $dbfield => $csvfield) {
                        $save[$db_fieldlist[$dbfield]] = $line[$csvfield];
                    }
                    if (!empty($save)) {
                        $save['owner'] = $_SESSION['WPs_uid'];
                        $ADB->adb_add_address($save);
                        ++$imported;
                    }
                }
            } else {
                $out = new fxl_template($WP_core['skin_path'].'/templates/adb.exch_impcsv.tpl');
                $_SESSION['WP_impfile'] = $file;
                $file = isset($file[0]) ? trim($file[0]) : false;
                if (!$file) {
                    // File not readable / non exstant -> return to input mask
                    break;
                }
                if (isset($fieldnames) && $fieldnames) {
                    $out->assign_block('if_fieldnames');
                }
                if (isset($is_quoted) && $is_quoted) {
                    $out->assign_block('if_quoted');
                    $file = str_replace('"', '', $file);
                }
                if (!isset($delimiter) || !$delimiter) $delimiter = ';';
                $delimiter = $delimiter{0}; // Make sure just to use a single char delimiter
                $file = explode($delimiter, $file);
                $out->assign(array
                        ('about_selection' => $WP_adbmsg['csvImAboutSelection']
                        ,'legend_source' => $WP_adbmsg['csvImLegendSource']
                        ,'msg_select' => $WP_adbmsg['select']
                        ,'legend_selection' => $WP_adbmsg['csvImLegendSelection']
                        ,'msg_from_csv' => $WP_adbmsg['csvImFromCSV']
                        ,'msg_in_db' => $WP_adbmsg['csvImInDB']
                        ,'delimiter' => $delimiter
                        ,'msg_save' => $WP_msg['save']
                        ,'form_action' => htmlspecialchars
                                ($_SERVER['PHP_SELF'].'?action='.$action.'&mail='.$mail
                                .'&'.give_passthrough(1).'&WP_plug[adb_action]=main&WP_plug[adb_mode]=exch'
                                .'&WP_plug[adb_do]=import&WP_plug[adb_imform]=CSV')
                        ));

                $t_csv = $out->get_block('csvline');
                foreach ($file as $k => $v) {
                    $t_csv->assign(array('id' => $k, 'value' => $v));
                    $out->assign('csvline', $t_csv);
                    $t_csv->clear();
                }
                $t_csv = $out->get_block('dbline');
                foreach ($db_fieldlist as $k => $v) {
                    $t_csv->assign(array('id' => $k, 'value' => $db_fieldnames[$k]));
                    $out->assign('dbline', $t_csv);
                    $t_csv->clear();
                }
                return;
            }
            break;
        default:
            $WP_core['plug_output'] .= '<strong>'.$WP_adbmsg['unkImpFrmt'].'</strong><br />'.LF;
            break;
        }
    }
    $WP_plug['adb_do'] = false;
}
if (!$WP_plug['adb_do']) {
    if (isset($imported) && $imported) {
        $WP_core['plug_output'] .= '<strong>'.str_replace('$1', $imported, $WP_adbmsg['ImpNum']).'</strong><br />'.LF;
    }

    $out = new fxl_template($WP_core['skin_path'].'/templates/adb.exchmenu.tpl');
    $passthrough2 = give_passthrough(2);
    $out->assign(array
            ('target' => $_SERVER['PHP_SELF']
            ,'msg_select' => $WP_adbmsg['plsSel']
            ,'passthrough' => $passthrough2
            ,'adb_action' => $WP_plug['adb_action']
            ,'adb_mode' => $WP_plug['adb_mode']
            ,'action' => $action
            ,'about_import' => $WP_adbmsg['AboutImport']
            ,'leg_import' => $WP_adbmsg['Import']
            ,'msg_file' => $WP_adbmsg['filename']
            ,'msg_format' => $WP_adbmsg['format']
            ,'msg_csv_only' => $WP_adbmsg['LegendCSV']
            ,'msg_fieldnames' => $WP_adbmsg['csvFirstLine']
            ,'msg_csv_quoted' => $WP_adbmsg['csvIsQuoted']
            ,'msg_field_delimiter' => $WP_adbmsg['csvFieldDelimiter']
            ));
    $imop = $out->get_block('imoption');
    foreach (array
            ('LDIF' => 'LDIF' /*
            ,'MSOutl' => 'Micosoft Outlook'
            ,'MSOutlEx' => 'Microsoft Outlook Express' */
            ,'MSOutlEx6' => 'Microsoft Outlook Express 6'
            ,'CSV' => $WP_adbmsg['csvMenuOption']
            ,'PHlyADB' => 'PHlyADB (PHlyMail 2.1+)'
            ) as $val => $name) {
        $imop->assign(array('value' => $val, 'name' => $name));
        $out->assign('imoption', $imop);
        $imop->clear();
    }
    if ($ADB->adb_get_adrcount($_SESSION['WPs_uid'], 0)) {
        $out_exp = $out->get_block('export');
        $out_exp->assign(array
                ('target' => $_SERVER['PHP_SELF']
                ,'msg_select' => $WP_adbmsg['plsSel']
                ,'passthrough' => $passthrough2
                ,'adb_action' => $WP_plug['adb_action']
                ,'adb_mode' => $WP_plug['adb_mode']
                ,'action' => $action
                ,'about_export' => $WP_adbmsg['AboutExport']
                ,'leg_export' => $WP_adbmsg['Export']
                ,'msg_csv_only' => $WP_adbmsg['LegendCSV']
                ,'msg_fieldnames' => $WP_adbmsg['csvFirstLine']
                ,'msg_csv_quoted' => $WP_adbmsg['csvIsQuoted']
                ,'msg_field_delimiter' => $WP_adbmsg['csvFieldDelimiter']
                ,'msg_format' => $WP_adbmsg['format']
                ));
        $exop = $out_exp->get_block('exoption');
        foreach (array
                ('LDIF' => 'LDIF' /*
                ,'MSOutl' => 'Micosoft Outlook'
                ,'MSOutlEx' => 'Microsoft Outlook Express'
                ,'MSOutlEx6' => 'Microsoft Outlook Express 6' */
                ,'CSV' => $WP_adbmsg['csvMenuOption']
                ,'PHlyADB' => 'PHlyADB (PHlyMail 2.1+)'
                ) as $val => $name) {
            $exop->assign(array('value' => $val, 'name' => $name));
            $out_exp->assign('exoption', $exop);
            $exop->clear();
        }
        $out->assign('export', $out_exp);
    }
}

?>
<?php
/* ------------------------------------------------------------------------- */
/* mod.setup.php -> FrontEnd User Setup                                      */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.9.1                                                                    */
/* ------------------------------------------------------------------------- */

$link_base = $_SERVER['PHP_SELF'].'?action=setup&'.give_passthrough(1).'&mode=';
// BEGIN MOD
$WP_core['conf_acc'] = 'true';
// ADD MOD
if (!isset($mode)) $mode = FALSE;

if (!$mode) {
    $link_base = htmlspecialchars($link_base);
    $tpl = new FXL_Template($WP_core['skin_path'].'/templates/setup.menu.tpl');
    $t_l = $tpl->getblock('menline');
    // BEGIN MOD
    $t_l->assign(array
            ('link_target' => 'config.php'
            ,'msg_line' => $WP_msg['setgen']
            ));
    $tpl->assign('menline', $t_l);
    $t_l->clear();
    $t_l->assign(array
            ('link_target' => $link_base.'edit'
            ,'msg_line' => $WP_msg['accounts']
            ));
    $tpl->assign('menline', $t_l);
    $t_l->clear();
    // END MOD
    if (is_array($WP_ext['setup_menu']) && count($WP_ext['setup_menu'])) {
        $t_p = $tpl->getBlock('plugged');
        foreach ($WP_ext['setup_menu'] as $k => $v) {
            if (!isset($v['link'])) continue;
            $t_p->assign('link_target', htmlspecialchars($v['link']));
            $t_p->assign('icon_line', isset($v['icon']) ? $v['icon'] : '');
            $t_p->assign('msg_line', isset($v['text']) ? $v['text'] : '');
            $tpl->assign('plugged', $t_p);
            $t_p->clear();
        }
    }
    $tpl->assign('msg_setup', $WP_msg['setup']);
    $tpl->assign('msg_choosemen', $WP_msg['chosmen']);
}

if (($mode == 'saveold' || $mode == 'savenew') && 'true' == $WP_core['conf_acc']) {
    $userdata = $DB->get_usrdata($_SESSION['WPs_uid']);
    $error = '';
    if ('' == $popname)   $error .= $WP_msg['enterProfname'].'<br />';
    if ('' == $popserver) $error .= 'POP3: '.$WP_msg['enterPOPserver'].'<br />';
    if ('' == $popuser)   $error .= 'POP3: '.$WP_msg['enterPOPuser'].'<br />';
    if ('saveold' == $mode) {
        $check_accid = $DB->checkfor_accname($userdata['username'], $popname);
        if (isset($check_accid) && $account != $check_accid && $check_accid != '') {
            $error .= $account.'/'.$check_accid.': '.$WP_msg['SuPrfExists'];
        }
    } else {
        if ($DB->checkfor_accname($userdata['username'], $popname)) $error .= $WP_msg['SuPrfExists'];
    }
    if (!$error) {
        if ('savenew' == $mode) {
            $account = $DB->add_account(array
                 ('uid' => $_SESSION['WPs_uid'], 'accname' => $popname
                 ,'accid' => $DB->get_maxaccid($_SESSION['WPs_uid'])
                 ,'acc_on' => isset($acc_on) ? $acc_on : 0, 'sig_on' => isset($sig_on) ? $sig_on : 0
                 ,'popserver' => $popserver
                 ,'popport' => $popport, 'popuser' => $popuser, 'poppass' => $poppass
                 ,'popnoapop' => $popapop, 'real_name' =>$real_name, 'address' => $address
                 ,'killsleep' => $killsleep, 'smtpafterpop' => isset($smtpafterpop) ? $smtpafterpop : 0
                 ,'smtpserver' => $smtp_host, 'smtpport' => $smtp_port
                 ,'smtpuser' => $smtp_user, 'smtppass' => $smtp_pass
                 ,'signature' => $signature));
            if (!$account) {
                $mode = 'add';
            } else {
                unset($account);
                $mode = 'edit';
            }
        }
        if ('saveold' == $mode) {
            $mode = 'edit';
            if ($DB->upd_account(array
                    ('uid' => $_SESSION['WPs_uid'], 'accid' => $account, 'accname' => $popname
                    ,'acc_on' => isset($acc_on) ? $acc_on : 0, 'sig_on' => isset($sig_on) ? $sig_on : 0
                    ,'popserver' => $popserver
                    ,'popport' => $popport, 'popuser' => $popuser, 'poppass' => $poppass
                    ,'popnoapop' => $popapop, 'real_name' => $real_name, 'address' => $address
                    ,'killsleep' => $killsleep, 'smtpafterpop' => isset($smtpafterpop) ? $smtpafterpop : 0
                    ,'smtpserver' => $smtp_host, 'smtpport' => $smtp_port
                    ,'smtpuser' => $smtp_user, 'smtppass' => $smtp_pass
                    ,'signature' => $signature))) {
                unset($account);
            }
        }
    } else {
      $mode = ($mode == 'savenew') ? 'add' : 'edit';
    }
}

if ('kill' == $mode && 'true' == $WP_core['conf_acc']) {
    if (!isset($yesiwantto)) $yesiwantto = FALSE;
    if (!isset($answergiven)) $answergiven = FALSE;
    if (!isset($noidonotwantto)) $noidonotwantto = FALSE;
    if (isset($account)) {
        if (!$yesiwantto && !$answergiven) {
            $tpl = new FXL_Template($WP_core['skin_path'].'/templates/account.kill.tpl');
            $tpl->assign('yes', $WP_msg['yes']);
            $tpl->assign('no', $WP_msg['no']);
            $tpl->assign('PHP_SELF', $_SERVER['PHP_SELF']);
            $tpl->assign('kill_request', $WP_msg['deleAccount']);
            $tpl_n = $tpl->getBlock('hidden');
            foreach (array('answergiven' => 'yes', 'mail' => $mail, 'mode' => $mode, 'account' => $account)
                    as $k => $v) {
                $tpl_n->assign('name', $k);
                $tpl_n->assign('value', $v);
                $tpl->assign('hidden', $tpl_n);
                $tpl_n->clear();
            }
            foreach (give_passthrough(3) as $key => $val) {
                $tpl_n->assign('name', $key);
                $tpl_n->assign('value', $val);
                $tpl->assign('hidden', $tpl_n);
                $tpl_n->clear();
            }
        } elseif ($noidonotwantto && 'yes' == $answergiven) {
            $mode = 'edit';
            unset($account);
        } elseif ($yesiwantto && 'yes' == $answergiven) {
            $DB->delete_account($_SESSION['WPs_username'], $account);
            $mode = 'edit';
            unset($account);
        }
    }
}

if ($mode == 'setdefacc' && 'true' == $WP_core['conf_acc']) {
    // BEGIN MOD (Other place to save that setting
    $GlChFile = $DB->get_usrdata();
    $GlChFile['default_profile'] = $_REQUEST['def_prof'];
    if ($DB->upd_user($GlChFile)) $WP_return = $WP_msg['optssaved'];
    else $WP_return = $WP_msg['optsnosave'];
    // END MOD
    header('Location: '.$link_base.'edit&WP_return='.urlencode($WP_return));
}

if (('edit' == $mode or 'add' == $mode) && 'true' == $WP_core['conf_acc']) {
    if (!isset($account) && $mode != 'add') {
        $tpl = new FXL_Template($WP_core['skin_path'].'/templates/account.list.tpl');
        $acclist = $DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']);
        $counter = sizeof($acclist);
        if ($counter > 0 && is_array($acclist)) {
            $t_b = $tpl->getBlock('menline');
            foreach($acclist as $k => $v) {
                $pd = $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k)
                        + $DB->get_popconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $k);
                $t_b->assign(array
                        ('counter' => $k, 'profilenm' => $acclist[$k]
                        ,'acc_on' => (isset($pd['acc_on']) && $pd['acc_on']) ? $WP_msg['yes'] : $WP_msg['no']
                        ,'popapop' => (trim($pd['popnoapop']) == '1') ? $WP_msg['no'] : $WP_msg['auto']
                        ,'msg_del' => $WP_msg['del']
                        ));
                $tpl->assign('menline', $t_b);
                $t_b->clear();
                // Save data for default account selection below
                if (isset($pd['acc_on']) && $pd['acc_on']) $defacc[$k] = $acclist[$k];
            }
        }
        $tpl->assign(array
                ('msg_account' => $WP_msg['account']
                ,'msg_menu' => $WP_msg['menu']
                ,'msg_backLI' => $WP_msg['cancel']
                ,'msg_addacct' => $WP_msg['addacct']
                ,'counter' => ''
                ,'PHP_SELF' => $_SERVER['PHP_SELF']
                ,'passthrough' => give_passthrough(1)
                ,'form_target' => $link_base.'setdefacc'
                ,'msg_defacc' => $WP_msg['default_account']
                ,'about_defacc' => str_replace('$1', $WP_msg['notdef'], $WP_msg['about_defacc'])
                ,'msg_notdef' => $WP_msg['notdef']
                ,'msg_save' => $WP_msg['save']
                ));
        // Selection of default account
        if (isset($defacc) && !empty($defacc)) {
            $t_da = $tpl->get_block('profline');
            foreach ($defacc as $k => $v) {
                $t_da->assign(array('id' => $k, 'name' => $v));
                if (isset($WP_core['default_profile']) && $WP_core['default_profile'] == $k) {
                    $t_da->assign_block('sel');
                }
                $tpl->assign('profline', $t_da);
                $t_da->clear();
            }
        }
    } else {
        if ('edit' == $mode) {
            $acclist = $DB->get_accidx($_SESSION['WPs_uid'], $_SESSION['WPs_username']);
            $accdata = $DB->get_accdata($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $account);
            if (is_array($accdata)) {
                $pd = $accdata
                        + $DB->get_popconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $account)
                        + $DB->get_smtpconnect($_SESSION['WPs_uid'], $_SESSION['WPs_username'], $account);
                $pd['profilename'] = $acclist[$account];
                unset($accdata);
            } else {
                $pd = array();
            }
        }
        // Overload data with previous user input
        foreach (array('accid', 'acc_on', 'sig_on', 'popserver', 'popport', 'popuser', 'poppass',
                'popnoapop', 'real_name', 'address', 'killsleep', 'smtpafterpop',  'smtpserver',
                'smtpport', 'smtpuser', 'smtppass', 'signature') as $k) {
            if (!isset(${$k})) continue;
            $pd[$k] = ${$k};
        }
        if (isset($popname)) $pd['profilename'] = $popname ;

        $tpl = new FXL_Template($WP_core['skin_path'].'/templates/account.edit.tpl');
        if (isset($error) && $error) {
            $tpl_ret=$tpl->getBlock('returnblock');
            $tpl_ret->assign('return', $error);
            $tpl->assign('returnblock', $tpl_ret);
        }
        $tpl->assign(array
                ('mode' => ('edit' == $mode) ? 'saveold' : 'savenew'
                ,'PHP_SELF' => $_SERVER['PHP_SELF']
                ,'msg_profile' => $WP_msg['profile']
                ,'msg_popserver' => $WP_msg['popserver']
                ,'msg_popport' => $WP_msg['popport']
                ,'msg_popuser' => $WP_msg['popuser']
                ,'msg_poppass' => $WP_msg['poppass']
                ,'msg_email' => $WP_msg['email']
                ,'msg_realname' => $WP_msg['realname']
                ,'msg_popapop' => $WP_msg['popapop']
                ,'msg_auto' => $WP_msg['auto']
                ,'msg_no' => $WP_msg['no']
                ,'msg_inmenu' => $WP_msg['inmenu']
                ,'msg_killsleep' => $WP_msg['setKillSleep']
                ,'msg_sigon' => $WP_msg['sigOn']
                ,'msg_save' => $WP_msg['save']
                ,'msg_cancel' => $WP_msg['cancel']
                ,'msg_smtphost' => $WP_msg['optsmtphost']
                ,'msg_smtpport' => $WP_msg['optsmtpport']
                ,'msg_smtpuser' => $WP_msg['optsmtpuser']
                ,'msg_smtppass' => $WP_msg['optsmtppass']
                ,'smtp_host' => isset($pd['smtpserver']) ? $pd['smtpserver'] : ''
                ,'smtp_port' => isset($pd['smtpport']) ? $pd['smtpport'] : ''
                ,'smtp_user' => isset($pd['smtpuser']) ? $pd['smtpuser'] : ''
                ,'smtp_pass' => isset($pd['smtppass']) ? $pd['smtppass'] : ''
                ,'profilename' => isset($pd['profilename']) ? $pd['profilename'] : ''
                ,'popserver' => isset($pd['popserver']) ? $pd['popserver'] : ''
                ,'popport' => isset($pd['popport']) ? $pd['popport'] : ''
                ,'popuser' => isset($pd['popuser']) ? $pd['popuser'] : ''
                ,'poppass' => isset($pd['poppass']) ? $pd['poppass'] : ''
                ,'email' => isset($pd['address']) ? $pd['address'] : ''
                ,'realname' => isset($pd['real_name']) ? $pd['real_name'] : ''
                ,'killsleep' => isset($pd['killsleep']) ? $pd['killsleep'] : ''
                ,'signature' => isset($pd['signature']) ? $pd['signature'] : ''
                ,'passthrough_2' => give_passthrough(2)
                ,'passthrough' => give_passthrough(1)
                ,'action' => isset($action) ? $action : ''
                ,'account' => isset($account) ? $account : ''
                ,'skin_path' => $WP_core['skin_path']
                ,'copy_smtp' => $WP_msg['copy_smtp']
                ,'copy_pop3' => $WP_msg['copy_pop3']
                ));

        if (isset($pd['popnoapop']) && $pd['popnoapop'])       $tpl->assign_block('apopsel');
        if (isset($pd['sig_on']) && $pd['sig_on'])             $tpl->assign_block('sig_ck');
        if (isset($pd['smtpafterpop']) && $pd['smtpafterpop']) $tpl->assign_block('smtpafterpop');
        if ((isset($pd['acc_on']) && $pd['acc_on']) || 'add' == $mode) {
            $tpl->assign_block('acc_ck');
        }
    }
}

?>
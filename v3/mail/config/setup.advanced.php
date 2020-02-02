<?php
/* ------------------------------------------------------------------------- */
/* setup.advanced.php -> Setup Of Advanced Choices                           */
/* (c) 2003-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.1.0mod1                                                                */
/* ------------------------------------------------------------------------- */

if (!isset($_SESSION['WPs_perm_read']['advanced_']) && !$_SESSION['WPs_superroot']) {
    $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
    $tpl->assign('msg_no_access', $WP_msg['no_access']);
    return;
}

$tpl = new FXL_Template(CONFIGPATH.'/templates/setup.advanced.tpl');
if (isset($whattodo) && 'save' == $whattodo) {
    // Schreibberechtigung
    if (!isset($_SESSION['WPs_perm_write']['advanced_']) && !$_SESSION['WPs_superroot']) {
        $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
        $tpl->assign('msg_no_access', $WP_msg['no_access']);
        return;
    }
    foreach (array
            ('WP_newallowsend' => 'false', 'WP_newkillall' => '0', 'WP_newaddrcheck' => '0'
            ,'WP_usegzip' => '0') as $k => $v) {
        ${$k} = (isset($_REQUEST[$k])) ? $_REQUEST[$k] : $v;
    }
    // Wash size fields
    $WP_newbigmark      = wash_size_field($WP_newbigmark);
    $WP_newnoshow       = wash_size_field($WP_newnoshow);
    $WP_newmaxupload    = wash_size_field($WP_newmaxupload);
    $WP_newpagesize     = wash_size_field($WP_newpagesize);
    $WP_newkillsleep    = wash_size_field($WP_newkillsleep);
    $WP_newprovidername = stripslashes($WP_newprovidername);
     // Add the mandatory -t flag to the sendmail path
     if ($WP_newsendmail && !preg_match('!\s-t[\s\Z\w]!', $WP_newsendmail)) {
         $WP_newsendmail = preg_replace('!^(.+)(?=\s|\Z)!U', '\1 -t', $WP_newsendmail);
     }
    // BEGIN MOD
    $truth = save_config(
          $WP_core['conf_files'].'/global.choices.php',
          array('send_method','sendmail','pagesize','killall','killsleep','big_mark','big_noshow','maxupload'
               ,'allow_checkAdr','fix_smtp_host','fix_smtp_port','fix_smtp_user','fix_smtp_pass'
               ,'provider_name', 'gzip_frontend'),
          array($WP_newsendmethod,$WP_newsendmail,$WP_newpagesize,$WP_newkillall,$WP_newkillsleep
               ,$WP_newbigmark,$WP_newnoshow,$WP_newmaxupload,$WP_newaddrcheck,$WP_newsmtphost
               ,$WP_newsmtpport,$WP_newsmtpuser,$WP_newsmtppass,$WP_newprovidername, $WP_usegzip)
               );
    // END MOD
    if ($truth) {
        // MOD
        $WP_return = $WP_msg['optssaved'];
    } else $WP_return = $WP_msg['optsnosave'];
    header('Location: '.$link_base.'advanced&WP_return='.base64_encode($WP_return));
    exit();
}
$tpl->assign(array
        ('head_text' => $WP_msg['SuHeadAdv']
        ,'link_base' => htmlspecialchars($link_base)
        ,'WP_return' => isset($WP_return) ? base64_decode($WP_return) : ''
        ,'target_link' => htmlspecialchars($link_base.'advanced&whattodo=save')
        ,'msg_optsendmethod' => $WP_msg['optsendmethod'], 'msg_path' => $WP_msg['optsendmail']
        ,'msg_fillin_sm' => $WP_msg['FillinSendMail'], 'msg_fillin_smtp' => $WP_msg['FillinSMTP']
        ,'sendmail' => $WP_core['sendmail'], 'msg_smtphost' => $WP_msg['optsmtphost']
        ,'msg_smtpport' => $WP_msg['optsmtpport'], 'msg_smtpuser' => $WP_msg['optsmtpuser']
        ,'msg_smtppass' => $WP_msg['optsmtppass']
        ,'smtphost' => isset($WP_core['fix_smtp_host']) ? $WP_core['fix_smtp_host'] : ''
        ,'smtpport' => isset($WP_core['fix_smtp_port']) ? $WP_core['fix_smtp_port'] : ''
        ,'smtpuser' => isset($WP_core['fix_smtp_user']) ? $WP_core['fix_smtp_user'] : ''
        ,'smtppass' => isset($WP_core['fix_smtp_pass']) ? $WP_core['fix_smtp_pass'] : ''
        ,'size_limit' => $WP_msg['optsizelimit']
        ,'sizeexample' => $WP_msg['optsizeexample'], 'msg_bigmark' => $WP_msg['optbigmark']
        ,'msg_noshow' => $WP_msg['optnoshow'], 'noshow' => $WP_core['big_noshow']
        ,'title_noshow' => size_format($WP_core['big_noshow'])
        ,'title_bigmark' => size_format($WP_core['big_mark'])
        ,'title_maxupload' => size_format($WP_core['maxupload'])
        ,'bigmark' => $WP_core['big_mark'], 'msg_pagesize' => $WP_msg['SuOptPagesize']
        ,'pagesize' => $WP_core['pagesize'], 'msg_maxupload' => $WP_msg['optmaxupload']
        ,'maxupload' => $WP_core['maxupload'], 'msg_save' => $WP_msg['save']
        ,'msg_cancel' => $WP_msg['cancel'], 'msg_addrcheck' => $WP_msg['optaddrcheck']
        ,'msg_onlineyes' => $WP_msg['SuOnlineYes']
        ,'msg_onlineno' => $WP_msg['SuOnlineNo'], 'msg_online' => $WP_msg['SuOptOnline']
        ,'msg_allowsend' => $WP_msg['SuOptAllowSend'], 'msg_confacc' => $WP_msg['SuOptConfAcc']
        ,'msg_allowconf' => $WP_msg['SuOptAllowConf'], 'msg_killall' => $WP_msg['SuOptKillall']
        ,'msg_killsleep' => $WP_msg['SuOptKillSleep'], 'killsleep' => $WP_core['killsleep']
        ,'msg_usegzip' => $WP_msg['SuUseGZipFE'], 'msg_providername' => $WP_msg['SuNameOfService']
        ,'providername' => isset($WP_core['provider_name']) ? htmlspecialchars($WP_core['provider_name']) : ''
        ,'about_online' => $WP_msg['AboutOnline']
        ,'about_providername' => $WP_msg['AboutProvName']
        ,'about_sendmethod' => $WP_msg['AboutSendMethod']
        ,'about_sizelimit' => $WP_msg['AboutSizeLimit']
        ,'about_pagesize' => $WP_msg['AboutPagesize']
        ,'about_killsleep' => $WP_msg['AboutKillSleep']
        ,'leg_online' => $WP_msg['LegOnline']
        ,'leg_misc' => $WP_msg['LegMisc']
        ,'leg_fsig' => $WP_msg['LegFSig']
        ,'leg_motd' => $WP_msg['LegMOTD']
        ,'leg_providername' => $WP_msg['LegName']
        ));
switch ($WP_core['send_method']) {
case 'sendmail': $tpl->assign_block('methsmsel'); break;
case 'smtp': $tpl->assign_block('methsmtpsel'); break;
}
switch ($WP_core['online_status']) {
case 'yes': $tpl->assign_block('online_yes'); break;
case 'no': $tpl->assign_block('online_no'); break;
}
if (isset($WP_core['allow_checkAdr']) && $WP_core['allow_checkAdr'])               $tpl->assign_block('addrcheck');
if (isset($WP_core['killall']) && $WP_core['killall'])                             $tpl->assign_block('killall');
if (isset($WP_core['show_motd']) && $WP_core['show_motd'])                         $tpl->assign_block('showmotd');
if (isset($WP_core['allow_send']) && 'true' == $WP_core['allow_send'])             $tpl->assign_block('allowsend');
if (isset($WP_core['conf_acc']) && 'true' == $WP_core['conf_acc'])                 $tpl->assign_block('confacc');
if (isset($WP_core['allow_user_setup']) && 'true' == $WP_core['allow_user_setup']) $tpl->assign_block('allowconf');
if (isset($WP_core['use_provsig']) && 'true' == $WP_core['use_provsig'])           $tpl->assign_block('use_provsig');
if (isset($WP_core['use_markread']) && $WP_core['use_markread'])                   $tpl->assign_block('markread');
if (isset($WP_core['gzip_frontend']) && $WP_core['gzip_frontend'])                 $tpl->assign_block('usegzip');
?>
<?php
/* ------------------------------------------------------------------------- */
/* setup.genral.php -> Setup General Settings                                */
/* (c) 2003-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.6                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($_SESSION['WPs_perm_read']['general_']) && !$_SESSION['WPs_superroot']) {
    $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
    $tpl->assign('msg_no_access', $WP_msg['no_access']);

    return;
}

if (!isset($whattodo)) $whattodo = false;
$WP_return = false;

if ('save' == $whattodo) {
    if (!isset($_SESSION['WPs_perm_write']['general_']) && !$_SESSION['WPs_superroot']) {
        $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
        $tpl->assign('msg_no_access', $WP_msg['no_access']);

        return;
    }
    $tokens = array
            ('skin_name' => 'WP_newskin'
            ,'language' => 'WP_newlang'
            ,'save_sent' => 'WP_newsavesent'
            ,'receipt_out' => 'WP_newreceiptout'
            ,'send_wordwrap' => 'WP_newsendwordwrap'
            ,'teletype' => 'WP_newtele'
            );
    $tokvar = array();
    $tokval = array();
    foreach ($tokens as $k => $v) {
        if (isset($_REQUEST[$v])) {
            $tokvar[] = $k;
            $tokval[] = $_REQUEST[$v];
        }
    }

    $truth = save_config($WP_core['conf_files'].'/global.choices.php', $tokvar, $tokval);
    $WP_return = ($truth) ? $WP_msg['optssaved'] : $WP_msg['optsnosave'];
    header('Location: '.$link_base.'general&WP_return='.base64_encode($WP_return));
    exit();
}

$tpl = new FXL_Template(CONFIGPATH.'/templates/setup.general.tpl');
if (isset($WP_return)) $tpl->assign_block('return');
$tpl->assign(array
      ('target_link' => htmlspecialchars($link_base.'general&whattodo=save')
      ,'WP_return' => base64_decode($WP_return)
      ,'head_text' => $WP_msg['SuHeadGen']
      ,'leg_general' => $WP_msg['general']
      ,'msg_optskin' => $WP_msg['optskin']
      ,'msg_optlang' => $WP_msg['optlang']
      ,'msg_opttele' => $WP_msg['opttele']
      ,'msg_txt_prop' => $WP_msg['txt_prop']
      ,'msg_txt_syst' => $WP_msg['txt_syst']
      ,'msg_optcopybox' => $WP_msg['optcopybox']
      ,'leg_copytobox' => $WP_msg['LegCopyBox']
      ,'about_copytobox' => $WP_msg['AboutCopyBox']
      ,'msg_optreceipt' => $WP_msg['optreceipt']
      ,'leg_receipt' => $WP_msg['LegReceipt']
      ,'about_receipt' => $WP_msg['AboutReceipt']
      ,'msg_optwrap' => $WP_msg['optwrap']
      ,'leg_wrap' => $WP_msg['LegWrap']
      ,'about_wrap' => $WP_msg['AboutWrap']
      ,'msg_save' => $WP_msg['save']
      ));

$d_ = opendir($WP_core['skin_dir']);
while (false !== ($skinname = readdir($d_))) {
    if ($skinname == '.' or $skinname == '..') continue;
    if (!is_dir($WP_core['skin_dir'].'/'.$skinname)) continue;
    if (file_exists($WP_core['skin_dir'].'/'.$skinname.'/main.tpl')) $skins[] = $skinname;
}
closedir($d_);
sort($skins);
$t_s = $tpl->getBlock('skinline');
foreach($skins as $skinname) {
    if (file_exists($WP_core['skin_dir'].'/choices.ini.php')) {
        $skin_properties = parse_ini_file($WP_core['skin_dir'].'/choices.ini.php');
        if (isset($skin_properties['client_type'])) {
            switch ($skin_properties['client_type']) {
            case 'pda':
                $client_type = 'cHTML';
                break;
            case 'imode':
            case 'i-mode':
                $client_type = 'iHTML';
                break;
            case 'wap':
            case 'wml':
                $client_type = 'WML';
                break;
            case 'html':
            case 'desktop':
                $client_type = 'HTML';
                break;
            }
        } else {
            $client_type = 'HTML';
        }
    } else {
            $client_type = 'HTML';
    }
    $t_s->assign(array('key' => $skinname,  'skinname' => $skinname.' ('.$client_type.')'));
    if ($skinname == $WP_core['skin_name']) $t_s->assign_block('sel');
    $tpl->assign('skinline',$t_s);
    $t_s->clear();
}
$d_ = opendir($WP_core['page_path'].'/messages/');
while(false !== ($langname = readdir($d_))) {
    if ($langname == '.' || $langname == '..') continue;
    if (!preg_match('/\.php$/i', trim($langname))) continue;
    $langname = preg_replace('/\.php$/i', '', trim($langname));
    $langs[] = $langname;
}
closedir($d_);
sort($langs);
$t_s = $tpl->getBlock('langline');
foreach($langs as $langname) {
    $t_s->assign(array('key' => $langname, 'langname' => str_replace('_', ' ', $langname)));
    if ($langname == $WP_core['language']) $t_s->assign_block('sel');
    $tpl->assign('langline', $t_s);
    $t_s->clear();
}
if (isset($WP_core['teletype']) && 'pro' == $WP_core['teletype']) {
    $tpl->assign_block('teleprosel');
} else {
    $tpl->assign_block('telesyssel');
}
// MOD
if (isset($WP_core['save_sent']) && '1' == $WP_core['save_sent']) $tpl->assign_block('savesent');
if (isset($WP_core['receipt_out']) && '1' == $WP_core['receipt_out']) $tpl->assign_block('receipt');
if (isset($WP_core['send_wordwrap']) && '1' == $WP_core['send_wordwrap']) $tpl->assign_block('wordwrap');
// END MOD
?>
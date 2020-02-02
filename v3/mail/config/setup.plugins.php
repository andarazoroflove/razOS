<?php
/* ------------------------------------------------------------------------- */
/* setup.plugins.php -> Setup Plugins                                        */
/* (c) 2003-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.7                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($_SESSION['WPs_perm_read']['plugins_']) && !$_SESSION['WPs_superroot']) {
    $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
    $tpl->assign('msg_no_access', $WP_msg['no_access']);

    return;
}
if (!isset($whattodo)) $whattodo = false;
$WP_return = '';

if ('save' == $whattodo) {
    if (!isset($_SESSION['WPs_perm_write']['plugins_']) && !$_SESSION['WPs_superroot']) {
        $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
        $tpl->assign('msg_no_access', $WP_msg['no_access']);

        return;
    }

    $skel = @file($WP_core['page_path'].'/lib/plug.control.skel');
    if (!$skel) $WP_return .= base64_encode($WP_msg['optnomainskel']);
    else $skel = join('', $skel);
    if (isset($acti_pi)) {
        foreach($acti_pi as $l => $v) {
            $skeline = @file($WP_core['page_path'].'/plugged/'.$v.'/plug.control.wpop');
            if ($skeline) {
                $skeline = join('', $skeline);
                $skel .= $skeline;
            } else {
                $WP_return .= base64_encode(str_replace('$1', $v, $WP_msg['optnoplugctrl']));
            }
        }
    }
    $out = fopen($WP_core['page_path'].'/plugged/plug.control.wpop', 'w');
    fputs($out, $skel);
    fclose($out);
}

$tpl = new FXL_Template(CONFIGPATH.'/templates/setup.plugins.tpl');
if (isset($_SESSION['WPs_perm_write']['plugins_']) || $_SESSION['WPs_superroot']) {
    $tpl->assign_block('maysave');
}
$tpl->assign(array('target_link' => htmlspecialchars($link_base.'plugins&whattodo=save')
            ,'link_base' => htmlspecialchars($link_base)
            ,'WP_return' => base64_decode($WP_return)
            ,'msg_optactive' => $WP_msg['optactive']
            ,'msg_optplugin' => $WP_msg['optplugin']
            ,'msg_optdescr' => $WP_msg['optdescr']
            ,'msg_save' => $WP_msg['save']
            ));
if (file_exists($WP_core['page_path'].'/plugged/plug.control.wpop')) {
    foreach(file($WP_core['page_path'].'/plugged/plug.control.wpop') as $l) {
        // Skip comments
        if (trim($l) == '') continue;
        if ($l{0} == '#') continue;
        $l = explode(';;', $l);
        $plugname = $l[2];
        $active_plugins[$plugname] = '1';
    }
}
$d_ = opendir($WP_core['page_path'].'/plugged/');
$i = 0;
$t_l = $tpl->getBlock('plugline');
while (false !== ($plugname = readdir($d_))) {
    if ($plugname == '.' || $plugname == '..') continue;
    if (!is_dir($WP_core['page_path'].'/plugged/'.$plugname)) continue;

    $WP_tmp['plugdir'] = $WP_core['page_path'].'/plugged/'.$plugname;
    if ($i%2 != 0) $t_l->assign_block('odd');
    ++$i;
    $t_l->assign('plugname', $plugname);
    if (isset($active_plugins[$plugname]) && $active_plugins[$plugname] == '1') $t_l->assign_block('sel');
    if (file_exists($WP_tmp['plugdir'].'/icon.png')) $t_l->assign('icon_link',$WP_tmp['plugdir'].'/icon.png');
    elseif (file_exists($WP_tmp['plugdir'].'/icon.gif')) $t_l->assign('icon_link',$WP_tmp['plugdir'].'/icon.gif');
    elseif (file_exists($WP_tmp['plugdir'].'/icon.jpg')) $t_l->assign('icon_link',$WP_tmp['plugdir'].'/icon.jpg');
    else $t_l->assign('icon_link', $WP_core['page_path'].'/lib/plug.icon.png');
    if (file_exists($WP_tmp['plugdir'].'/description.'.$WP_msg['language'].'.html')) {
        $t_l->assign('description', join('', file($WP_tmp['plugdir'].'/description.'.$WP_msg['language'].'.html')));
    } elseif (file_exists($WP_tmp['plugdir'].'/description.html')) {
        $t_l->assign('description', join('', file($WP_tmp['plugdir'].'/description.html')));
    } else $t_l->assign('description', '&nbsp;');
    $tpl->assign('plugline', $t_l);
    $t_l->clear();
}
closedir($d_);

?>
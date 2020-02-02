<?php
/* ------------------------------------------------------------------------- */
/* setup.driver.php -> Setup Database Driver(s)                              */
/* (c) 2003-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.6                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($_SESSION['WPs_perm_read']['driver_']) && !$_SESSION['WPs_superroot']) {
    $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
    $tpl->assign('msg_no_access', $WP_msg['no_access']);
    return;
}

if (!isset($whattodo)) $whattodo = FALSE;

$tpl = new FXL_Template(CONFIGPATH.'/templates/setup.driver.tpl');
if ('chgdrv' == $whattodo) {
    if (isset($_SESSION['WPs_perm_write']['driver_']) || $_SESSION['WPs_superroot']) {
        $truth = save_config($WP_core['conf_files'].'/global.choices.php', array('database') ,array($new_driver));
        if ($truth) {
            $error = $WP_msg['optssaved'];
            $WP_core['database'] = $new_driver;
        } else $error = $WP_msg['optsnosave'];
    } else {
        $error = $WP_msg['no_access'];
    }
}

$tpl = new FXL_Template(CONFIGPATH.'/templates/setup.driver.tpl');
$tpl->assign(array('target_link' => htmlspecialchars($link_base.'driver&whattodo=chgdrv')
                  ,'link_base' => htmlspecialchars($link_base)
                  ,'head_text' => $WP_msg['SuHeadDB']
                  ,'msg_currdrvr' => $WP_msg['SuDBCurrDrvr']
                  ,'msg_settings' => $WP_msg['settings']));
if (isset($error)) {
    $tpl->assign_block('error');
    $tpl->assign('error', $error);
}
$d_ = opendir($WP_core['page_path'].'/drivers/');
while (false !== ($drivername = readdir($d_))) {
    if ($drivername == '.' || $drivername == '..') continue;
    if (!file_exists($WP_core['page_path'].'/drivers/'.$drivername.'/driver.php')) continue;
    $drivers[] = $drivername;
}
closedir($d_);
sort($drivers);
switch(sizeof($drivers)) {
case 0:
    $go_on = 0;
    $tpl_d = $tpl->getBlock('one_no_driver');
    $tpl_d->assign('output', '-');
    $tpl->assign('one_no_driver', $tpl_d);
    break;
case 1:
    $go_on = 1;
    $tpl_d=$tpl->getBlock('one_no_driver');
    $tpl_d->assign('output', $drivers[0]);
    $tpl->assign('one_no_driver', $tpl_d);
    break;
default:

    $go_on = 1;
    $tpl_d = $tpl->getBlock('drivermenu');
    $tpl_l = $tpl_d->getBlock('menuline');
    foreach($drivers as $drivername) {
        $tpl_l->assign('drivername', $drivername);
        if ($drivername == $WP_core[database]) $tpl_l->assign_block('selected');
        $tpl_d->assign('menuline', $tpl_l);
        $tpl_l->clear();
    }
    $tpl_d->assign('msg_save', $WP_msg['save']);
    $tpl->assign('drivermenu', $tpl_d);
    break;
}

if ($go_on == 0) $tpl->assign('conf_output', $WP_msg['SuDBnoDriver']);
else {
    if (!file_exists($WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php')) {
        $tpl->assign('conf_output', $WP_msg['SuDBnotConfd']);
    }
    if (file_exists($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/setup.php')) {
        $WP_core['driver_dir'] = $WP_core['page_path'].'/drivers/'.$WP_core['database'];
        include($WP_core['driver_dir'].'/setup.php');
        $tpl->assign('conf_output', $conf_output);
    } else {
        $tpl->assign('conf_output', str_replace('$1', $WP_core['database'], $WP_msg['SuDBnoMod']));
    }
}
?>
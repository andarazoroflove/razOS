<?php
/* ------------------------------------------------------------------------- */
/* setup.security.php -> Setup Security of PHlyMail FrontEnd                 */
/* (c) 2003-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.0.7                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($_SESSION['WPs_perm_read']['security_']) && !$_SESSION['WPs_superroot']) {
    $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
    $tpl->assign('msg_no_access', $WP_msg['no_access']);

    return;
}

if (isset($whattodo) && 'save' == $whattodo) {
    if (!isset($_SESSION['WPs_perm_write']['security_']) && !$_SESSION['WPs_superroot']) {
        $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
        $tpl->assign('msg_no_access', $WP_msg['no_access']);

        return;
    }
    if (!isset($WP_newsessionip)) $WP_newsessionip = 0;
    $truth = save_config
             (
              $WP_core['conf_files'].'/global.choices.php'
             ,array('tie_session_ip', 'waitonfail','countonfail', 'lockonfail')
             ,array($WP_newsessionip, (int) $WP_newwaitfail, (int) $WP_newcountfail, (int) $WP_newlockfail)
             );
    if (TRUE == $truth) $WP_return = $WP_msg['optssaved'];
    else $WP_return = $WP_msg['optsnosave'];
    header('Location: '.$link_base.'security&WP_return='.base64_encode($WP_return));
    exit();
}
$tpl = new FXL_Template(CONFIGPATH.'/templates/setup.security.tpl');
$tpl->assign(array('target_link' => htmlspecialchars($link_base.'security&whattodo=save')
                  ,'link_base' => htmlspecialchars($link_base)
                  ,'WP_return' => isset($WP_return) ? base64_decode($WP_return) : ''
                  ,'head_text' => $WP_msg['SuHeadSec']
                  ,'msg_waitonfail' => $WP_msg['SuOptWaitOnFail']
                  ,'msg_lockonfail' => $WP_msg['SuOptLockOnFail']
                  ,'waitonfail' => $WP_core['waitonfail']
                  ,'msg_save' => $WP_msg['save']
                  ,'leg_wronglogin' => $WP_msg['LegWrongLogin']
                  ,'about_wronglogin' => $WP_msg['AboutWrongLogin']
                  ));

?>
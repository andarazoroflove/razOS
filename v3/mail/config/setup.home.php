<?php
/* ------------------------------------------------------------------------- */
/* setup.menu.php -> Setup Main Menu                                         */
/* (c) 2003-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.0.6mod1                                                                */
/* ------------------------------------------------------------------------- */

$tpl = new FXL_Template(CONFIGPATH.'/templates/setup.home.tpl');

$tpl->assign('head_text', $WP_msg['SuHeadHome']);

// Check, wether there's settings causing problems
$problems = FALSE;
// Send method and missing settings
switch ($WP_core['send_method']) {
case 'sendmail':
    if (!isset($WP_core['sendmail']) || !$WP_core['sendmail']) {
        $problems['sendmail'] = 1;
    }
    break;
case 'smtp':
    if (!isset($WP_msg['optsmtphost']) || !$WP_msg['optsmtphost']) {
        $problems['smtp'] = 1;
    }
    break;
}
// Size Limits
if (isset($WP_core['big_noshow']) && isset($WP_core['big_mark'])) {
    if ($WP_core['big_mark'] > $WP_core['big_noshow']) $problems['sizelimit'] = 1;
}

// Problems found -> output them
if ($problems) {
    $t_c = $tpl->getblock('checks');
    $t_c->assign(array
            ('msg_foundprob' => $WP_msg['CkSFoundProb']
            ,'leg_check' => $WP_msg['Leg_CkSet']
            ));
    $t_p = $t_c->getblock('probline');
    $plist = array
            ('smtp' => array('mod' => 'advanced', 'name' => 'setadv', 'msg' => 'CkSSMTP')
            ,'sendmail' => array('mod' => 'advanced', 'name' => 'setadv', 'msg' => 'CkSSndMl')
            ,'sizelimit' => array('mod' => 'advanced', 'name' => 'setadv', 'msg' => 'CkSSLimit')
            );
    foreach ($plist as $k => $v) {
        if (isset($problems[$k])) {
            $t_p->assign(array
                   ('msg_module' => $WP_msg['CkSModule']
                   ,'module' => $WP_msg[$v['name']], 'msg_descr' => $WP_msg[$v['msg']]
                   ,'link_module' => htmlspecialchars($link_base.$v['mod'])
                   ));
            $t_c->assign('probline', $t_p);
            $t_p->clear();
        }
    }
    $tpl->assign('checks', $t_c);
}

?>
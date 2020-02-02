<?php
/* ------------------------------------------------------------------------- */
/* setup.menu.php -> Setup Main Menu                                         */
/* (c) 2003-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.0.8mod1                                                                */
/* ------------------------------------------------------------------------- */

$global_menu_itms = array
    (
      0 => array('name' => 'Home',                  'action' => '',                       'type' => 'i'),
      1 => array('name' => $WP_msg['setgen'],       'action' => 'general',                'type' => 'i'),
      2 => array('name' => $WP_msg['setadv'],       'action' => 'advanced',               'type' => 'i'),
      3 => array('name' => $WP_msg['setSec'],       'action' => 'security',               'type' => 'i'),
      4 => array('name' => $WP_msg['setuser'],      'action' => 'users',                  'type' => 'd'),
      5 => array('name' => $WP_msg['setplugs'],     'action' => 'plugins',                'type' => 'i'),
      6 => array('name' => $WP_msg['setDB'],        'action' => 'driver',                 'type' => 'i'),
      7 => array('name' => $WP_msg['setAU'],        'action' => 'AU',                     'type' => 'd'), /*
      8 => array('name' => 'Applikation',           'action' => 'AU', 'screen' => 'app',  'type' => 's'),
      9 => array('name' => 'Skins',                 'action' => 'AU', 'screen' => 'skin', 'type' => 's'),
     10 => array('name' => 'Plugins',               'action' => 'AU', 'screen' => 'plug', 'type' => 's'),
     11 => array('name' => 'Einstellungen',         'action' => 'AU', 'screen' => 'set',  'type' => 's'), */
     12 => array('name' => $WP_msg['setregnow'],    'action' => 'regnow',                 'type' => 'd'),
     13 => array('name' => $WP_msg['diagnosis'],    'action' => 'diag',                   'type' => 'i'),
     14 => array('name' => $WP_msg['MenuConfig'],   'action' => '-',                      'type' => 'd'),/*
     15 => array('name' => $WP_msg['MenuSettings'], 'action' => 'config',                 'type' => 's'),
     16 => array('name' => $WP_msg['MenuUsers'],    'action' => 'config.users',           'type' => 's'),  */
     17 => array('name' => 'SMS',                   'action' => 'sms',                    'type' => 'd')
    );

$types = array('i' => 'item', 's' => 'subitem', 'm' => 'menu', 'd' => 'disabled');

$Menu = new FXL_Template(CONFIGPATH.'/templates/menu.tpl');
$L = $Menu->getblock('line');

if (!isset($action)) $action = '';
if (!isset($screen)) $screen = '';

foreach ($global_menu_itms as $k) {
    if (!isset($k['action'])) $k['action'] = '';
    $k['screen'] = (isset($k['screen'])) ? '&screen='.$k['screen'] : '';
    $C = $L->getblock($types[$k['type']]);
    $C->assign(array('link_target' => htmlspecialchars($link_base.$k['action'].$k['screen'])
                    ,'msg_line' => $k['name']));
    if ($action == $k['action'] && $screen == $k['screen']) {
        $C->assign_block('active_'.$k['type']);
    } else {
        $C->assign_block('inactive_'.$k['type']);
    }
    $L->assign($types[$k['type']], $C);
    $L->assign('confpath', CONFIGPATH);
    $Menu->assign('line', $L);
    $L->clear();
}

?>
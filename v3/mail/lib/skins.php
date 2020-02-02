<?php
/* ------------------------------------------------------------------------- */
/* lib/skins.php - PHlyMail 1.2.0+  Skin handler                             */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.5.2                                                                    */
/* ------------------------------------------------------------------------- */

if(!isset($WP_mode['content_type'])) $WP_mode['content_type'] = 'text/html';
if (!isset($action)) $action = FALSE;

// Use Font Encoding from language file
if (!isset($WP_skin['metainfo'])) $WP_skin['metainfo'] = '';

if (isset($WP_msg['iso_encoding'])) {
    $WP_skin['metainfo'] .= '<meta http-equiv="content-type" content="'.$WP_mode['content_type']
                           .'; charset='.$WP_msg['iso_encoding'].'">'.LF;
}

if (isset($WP_core['provider_name']) && $WP_core['provider_name'] != '') {
    $WP_skin['version'] = $WP_core['provider_name'];
} elseif (file_exists($WP_core['page_path'].'/lib/inc.version.html')) {
    $WP_skin['version'] = trim(join('', file($WP_core['page_path'].'/lib/inc.version.html')));
} else $WP_skin['version'] = 'PHlyMail';

// In case we should load the authentication screen ...
if (isset($WP_once['load_tpl_auth'])) {
    // Load skin template
    $t_skin = new FXL_Template($WP_core['skin_path'].'/auth.tpl');
    $t_skin->assign(array('version' => $WP_skin['version'], 'metainfo' => $WP_skin['metainfo']
                         ,'phlymail_content' => $tpl, 'skin_path' => $WP_core['skin_path']
                         ,'lang' => $WP_msg['language']
                         ));

    header('Content-Type: '.$WP_mode['content_type']);
    $t_skin->display();
    return; // Ignore the rest of the module since we are done here
}


// Build up links
$link_base = $_SERVER['PHP_SELF'].'?'.give_passthrough(1);

// Load skin template
$t_skin = new FXL_Template($WP_core['skin_path'].'/main.tpl');

// Add info about current profile
if (isset($WP_broad['profile']) && $WP_broad['profile']) $t_skin->assign('broad_profile', $WP_broad['profile']);

// Inbox
if ($action != 'inbox' && $action) {
    $t_skin->assign_block('inbox_i');
} else $t_skin->assign_block('inbox_a');
$t_skin->assign('link_inbox', $link_base.'&amp;action=inbox');
$t_skin->assign('msg_inbox', $WP_msg['alt_inbox']);

// Display a mail
if ($action != 'read') {
    if (isset($_SESSION['WPs_popserver']) && $_SESSION['WPs_popserver'] && isset($mail) && $mail)  {
        $t_skin->assign_block('view_i');
    } else {
        $t_skin->assign_block('view_d');
    }
} else $t_skin->assign_block('view_a');
$t_skin->assign('link_view', $link_base.'&amp;action=read&amp;mail='.$mail);
$t_skin->assign('msg_view', $WP_msg['alt_view']);

// Send a mail
if ($WP_core['allow_send'] == 'true') {
    if ($action != 'send') $t_skin->assign_block('send_i');
    else $t_skin->assign_block('send_a');
} else $t_skin->assign_block('send_d');
$t_skin->assign('link_send', $link_base.'&amp;action=send');
$t_skin->assign('msg_send', $WP_msg['alt_send']);

// Send a short message (only visible, if setting defined)
if (isset($WP_core['sms_active'])) {
    if ($WP_core['sms_active']) {
        if ($action != 'sms') $t_skin->assign_block('sms_i');
        else $t_skin->assign_block('sms_a');
    } else $t_skin->assign_block('sms_d');
    $t_skin->assign('link_sms', $link_base.'&amp;action=sms');
    $t_skin->assign('msg_sms', $WP_msg['alt_sms']);
}

// Setup
if (isset($WP_core['allow_user_conf']) && $WP_core['allow_user_conf'] == 'true') {
    if ($action != 'setup') $t_skin->assign_block('setup_i');
    else $t_skin->assign_block('setup_a');
} else $t_skin->assign_block('setup_d');
$t_skin->assign('link_setup', $link_base.'&amp;action=setup');
$t_skin->assign('msg_setup', $WP_msg['alt_setup']);

// Logout Link
if (isset($_SESSION['WPs_username'])) {
    if ($action != 'logout') $t_skin->assign_block('logout_i');
    else $t_skin->assign_block('logout_a');
} else $t_skin->assign_block('logout_d');
$t_skin->assign('link_logout', $_SERVER['PHP_SELF'].'?'.give_passthrough(1).'&amp;action=logout');
$t_skin->assign('msg_logout', $WP_msg['alt_logout']);

// Some genral placeholders
$t_skin->assign(array
         ('version' => $WP_skin['version'], 'metainfo' => $WP_skin['metainfo']
         ,'skin_path' => $WP_core['skin_path'], 'lang' => $WP_msg['language']
         ,'phlymail_content' => $tpl, 'link_base' => $link_base
         ));
header('Content-Type: '.$WP_skin['content_type']);
$t_skin->display();

?>
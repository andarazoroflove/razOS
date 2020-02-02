<?php
/* ------------------------------------------------------------------------- */
/* lib/skins.php - PHlyMail Config Skin handler                              */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.4.7                                                                    */
/* ------------------------------------------------------------------------- */

$WP_mode['content_type']='text/html';

// Use Font Encoding from language file
if (isset($WP_msg['iso_encoding'])) {
    if (!isset($WP_skin['metainfo'])) $WP_skin['metainfo'] = FALSE;
    $WP_skin['metainfo'] .= '<meta http-equiv="content-type" content="'.$WP_mode['content_type']
                           .'; charset='.$WP_msg['iso_encoding'].'">'.LF;
}
if (isset($WP_core['provider_name']) && $WP_core['provider_name'] != '') {
    $WP_skin['version'] = $WP_core['provider_name'];
} elseif (file_exists($WP_core['page_path'].'/lib/inc.version.html')) {
    $WP_skin['version'] = trim(join('', file($WP_core['page_path'].'/lib/inc.version.html')));
} else $WP_skin['version'] = 'PHlyMail';

// Decide, which main template to process
if(isset($WP_once['load_tpl_auth'])) {
    // Load skin template
    $t_skin = new FXL_Template(CONFIGPATH.'/templates/auth.tpl');
    $t_skin->assign(array
             ('version' => $WP_skin['version']
             ,'metainfo' => $WP_skin['metainfo']
             ,'confpath' => CONFIGPATH
             ,'phlymail_content' => $tpl
             ,'scheme' => (isset($WP_conf['scheme']) && file_exists(CONFIGPATH.'/schemes/'.$WP_conf['scheme'].'.css'))
                          ? $WP_conf['scheme']
                          : 'default'
             ,'link_frontend' => $_SERVER['PHP_SELF'].'?'.htmlspecialchars(give_passthrough(1)
                                .'&action=logout&redir=index')
             ,'msg_frontend' => $WP_msg['go_frontend']
             ,'provider_name' => $WP_skin['version']
             ));
} else {
    // Build up links
    $link_logout   = $_SERVER['PHP_SELF'].'?'.htmlspecialchars(give_passthrough(1).'&action=logout');
    $link_frontend = $link_logout.'&amp;redir=index';
    // Load skin template
    $t_skin = new FXL_Template(CONFIGPATH.'/templates/main.tpl');

    $t_skin->assign(array
             ('link_logout' => $link_logout
             ,'msg_logout' => $WP_msg['logout']
             ,'link_frontend' => $link_frontend
             ,'msg_frontend' => $WP_msg['go_frontend']
             ,'version' => $WP_skin['version']
             ,'metainfo' => $WP_skin['metainfo']
             ,'confpath' => CONFIGPATH
             ,'phlymail_content' => $tpl, 'menu' => $Menu
             ,'skin_path' => 'config'
             ,'scheme' => (isset($WP_conf['scheme']) && file_exists(CONFIGPATH.'/schemes/'.$WP_conf['scheme'].'.css'))
                          ? $WP_conf['scheme']
                          : 'default'
             ,'provider_name' => $WP_skin['version']
             ));
}

header('Content-Type: '.$WP_mode['content_type']);
$t_skin->display();

?>
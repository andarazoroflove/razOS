<?php
/* ------------------------------------------------------------------------- */
/* setup.diag.php -> PHlyMail 1.20.00+                                       */
/* (c) 2001-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.1.7                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($_SESSION['WPs_perm_read']['diag_']) && !$_SESSION['WPs_superroot']) {
    $tpl = new FXL_Template(CONFIGPATH.'/templates/setup.noaccess.tpl');
    $tpl->assign('msg_no_access', $WP_msg['no_access']);

    return;
}

if (isset($pure) && 'true' == $pure) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="diagnosis.txt"');
    $tpl = new fxl_template(CONFIGPATH.'/templates/diagnosis.txt.tpl');
} else {
    $tpl = new fxl_template(CONFIGPATH.'/templates/diagnosis.general.tpl');
}

$t_o = $tpl->getBlock('outline');
$t_o->assign('head_text', $WP_msg['DiagHeadBasic']);
$t_l = $t_o->getBlock('content');
$t_n = $t_l->getBlock('normal');
$t_s = $t_l->getBlock('special');

$t_n->assign('key', $WP_msg['DiagKversion']);
if (file_exists($WP_core['conf_files'].'/current.build') && function_exists('version_format')) {
    $t_n->assign('value', 'PHlyMail '.version_format(join('', file($WP_core['conf_files'].'/current.build'))));
} elseif (file_exists($WP_core['page_path'].'/lib/inc.version.html')) {
    $t_n->assign('value', trim(join('', file($WP_core['page_path'].'/lib/inc.version.html'))));
} else {
    $t_n->assign('value', $WP_msg['DiagUnkVers']);
}
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_s->assign('text', $WP_msg['DiagKwhere']); $t_l->assign('special', $t_s);
$t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_s->assign('text', dirname(__FILE__)); $t_l->assign('special', $t_s);
$t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagFSize']);
$size = mod_diagnosis_recurse_size($WP_core['page_path']);
$t_n->assign('value', size_format(array_sum($size), (isset($pure) && $pure) ? true : false));
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagConfRel']);
if (isset($WP_core['pop_files'])) {
    $t_n->assign('value', $WP_core['pop_files']);
} elseif (isset($WP_core['conf_files'])) {
    $t_n->assign('value', $WP_core['conf_files']);
} else {
    $t_n->assign('value', $WP_msg['DiagNoLoc']);
}
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagSkinRel']);
if (isset($WP_core['skin_dir'])) {
    $t_n->assign('value', $WP_core['skin_dir']);
} else {
    $t_n->assign('value', $WP_msg['DiagNoLoc']);
}
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagPHPVer']); $t_n->assign('value', phpversion());
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagPHPMem']);
$size = @ini_get('memory_limit');
if ($size != '') $t_n->assign('value', $size);
else $t_n->assign('value', 'unknown');
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', 'Register globals:');
$size = @ini_get('register_globals');
$t_n->assign('value', ($size == '1') ? 'On' : 'Off');
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', 'Safe Mode:');
$size = @ini_get('safe_mode');
$t_n->assign('value', ($size == '1') ? 'On' : 'Off');
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', 'IP:');
$IP = $_SERVER['SERVER_ADDR'];
$t_n->assign('value', ($IP) ? $IP : $WP_msg['nofiletype']);
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagServSoft']);
$t_n->assign('value', (isset($_ENV['SERVER_SOFTWARE'])) ? $_ENV['SERVER_SOFTWARE'] : '');
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagOS']);
$t_n->assign('value', @php_uname());
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagSAPI']);
$t_n->assign('value', @php_sapi_name());
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$t_n->assign('key', $WP_msg['DiagMTM']);
if ($WP_core['send_method'] == 'smtp') {
    $t_n->assign('value', 'SMTP');
} elseif ($WP_core['send_method'] == 'sendmail') {
    $t_n->assign('value', 'Sendmail');
} else {
    $t_n->assign('value', $WP_msg['DiagUnkSet']);
}
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

if (isset($WP_core['pop_files'])) {
    $scandir = $WP_core['pop_files'];
    $dirname = 'access';
} elseif (isset($WP_core['conf_files'])) {
    $scandir = $WP_core['conf_files'];
    $dirname = 'conf';
} else {
   $scandir = FALSE;
   $dirname =  $WP_msg['DiagNoLoc'];
}
$t_n->assign('key', str_replace('$1', $dirname, $WP_msg['DiagWriteTest']));
if ($scandir && touch($scandir.'/diagnosistest')) {
    if (unlink($scandir.'/diagnosistest')) {
        $t_n->assign('value', $WP_msg['DiagSucc']);
    } else {
        $t_n->assign('value', $WP_msg['DiagNoDel']);
    }
} else {
    $t_n->assign('value', $WP_msg['DiagFail']);
}
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

if (!isset($WP_skin['copyright'])) $WP_skin['copyright'] = '';
$t_n->assign('key', $WP_msg['DiagCurrSkin']);
$t_n->assign('value', $WP_core['skin_name'].' ('.links($WP_skin['copyright']).')');
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

if (!isset($WP_msg['msg_copyright'])) $WP_msg['msg_copyright'] = '';
$t_n->assign('key', $WP_msg['DiagCurrLang']);
$t_n->assign('value', $WP_core['language'].' ('.links($WP_msg['msg_copyright']).')');
$t_l->assign('normal', $t_n); $t_o->assign('content', $t_l);
$t_l->clear(); $t_n->clear(); $t_s->clear();

$tpl->assign('outline', $t_o); $t_o->clear();
$t_o->assign('head_text', $WP_msg['DiagHeadModVers']);

$searchpaths = array($WP_core['page_path'], $WP_core['page_path'].'/lib');
foreach ($searchpaths as $v) {
    $dh = opendir($v);
    $files = array();
    while (false !== ($fn = readdir($dh)) ) {
        if ($fn == '.' or $fn == '..') continue;
        if (!preg_match('/php$/i', $fn)) continue;
        $files[] = $fn;
    }
    sort($files);
    foreach ($files as $fn) {
        $fh = join('', file($v.'/'.$fn));
        $t_n->assign('key', $v.'/'.$fn);
        if (preg_match('!\/\*(\ +)v([^\ \*]+)(\ +)\*\/!i', $fh, $match)) $t_n->assign('value', $match[2]);
        else $t_n->assign('value', $WP_msg['DiagNoVer']);
        $t_l->assign('normal', $t_n);
        $t_o->assign('content', $t_l);
        $t_l->clear(); $t_n->clear(); $t_s->clear();
    }
    closedir($dh);
}

if (isset($_REQUEST['pure']) && $_REQUEST['pure']) {
    $tpl->assign('outline', $t_o);
    ob_start();
    $tpl->display();
    $tpl = un_html(strip_tags(ob_get_contents()));
    ob_end_clean();
    echo $tpl;
    exit();
}

$tpl->assign(array
      ('export' => $WP_msg['DiagExport']
      ,'target' => $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&amp;pure=true'
      ));
$tpl->assign('outline', $t_o);

function mod_diagnosis_recurse_size($curr_dir = '')
{
    $size = array();
    if (!file_exists($curr_dir) || !is_readable($curr_dir)) return $size;
    $dh = opendir($curr_dir);
    while (false !== ($fn = readdir($dh))) {
        if ($fn == '.' or $fn == '..') continue;
        $effective = $curr_dir . '/' . $fn;
        if (is_dir($effective)) mod_diagnosis_recurse_size($effective);
        else $size[] = filesize($effective);
    }
    closedir($dh);
    return $size;
}
?>
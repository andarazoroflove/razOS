<?php
/* ------------------------------------------------------------------------- */
/* mod.inbox.php -> Erzeuge Content für die Inbox von PHlyMail               */
/* (c) 2004 blue birdy, Berlin (http://bluebirdy.de)                         */
/* All rights reserved                                                       */
/* PHlyMail Adreßbuch Basic                                                  */
/* v0.0.1                                                                    */
/* ------------------------------------------------------------------------- */

if (!isset($WP_ext['inbox_prof_right'])) $WP_ext['inbox_prof_right'] = '';

$range = 7;

require_once($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/phlyadb.php');
$ADB = new phlyadb($WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php');

// Retrieve list of upcoming bdays for that user and create a simple table (not yet templated!)
$bdays = $ADB->adb_get_bday_list($_SESSION['WPs_uid'], FALSE, $range);

if (!empty($bdays)) {
    $link = $_SERVER['PHP_SELF'].htmlspecialchars('?WP_plug[adb_action]=main&WP_plug[adb_mode]=adr&'.give_passthrough(1).'&WP_plug[adb_do]=view&id=');
    $out = new fxl_template($WP_core['skin_path'].'/templates/adb.bday_list.inbox.tpl');
    $out->assign('msg_bday_head', str_replace('$1', $range, $WP_adbmsg['org_bday_head']));
    $outl = $out->get_block('entry');
    foreach ($bdays as $line) {
        if (trim($line['nick']) != '') {
            $name = $line['nick'];
        } elseif (strlen(trim($line['firstname'])) > 1 || strlen(trim($line['lastname'])) > 1) {
            $name = $line['firstname'].' '.$line['lastname'];
        } else {
            $name = '&lt; '.$WP_adbmsg['unnamed'].' &gt;';
        }      
        
        // Handle birthday entry
        list($byear, $bmonth, $bday) = explode('-', $line['birthday']);
        $byear  = (int) $byear;
        $bmonth = (int) $bmonth;
        $bday   = (int) $bday;
        if ($byear && !$bmonth && !$bday) $line['birthday'] = $byear;
        elseif ($byear && ($bmonth || $bday)) {
            $line['birthday'] = date($WP_msg['dateformat_old'], mktime(0, 0, 0, $bmonth, $bday, $byear));
        } elseif (!$byear && $bmonth && $bday) {
            $line['birthday'] = date($WP_msg['dateformat_daymonth'], mktime(0, 0, 0, $bmonth, $bday));
        }        
        $outl->assign(array
                ('link_entry' => $link.$line['aid']
                ,'entry' => $name
                ,'bday' => $line['birthday']
                ,'age' => $line['age']
                ));
        $out->assign('entry', $outl);
        $outl->clear();
    }
    $WP_ext['inbox_prof_right'] .= $out->get_output();
}
?>
    
    
    

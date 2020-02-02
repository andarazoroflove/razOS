<?php
/* ------------------------------------------------------------------------- */
/* fslite/install.php -> PHLyMail DB; Driver FSlite; install module          */
/* (c) 2002-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* v0.0.5                                                                    */
/* ------------------------------------------------------------------------- */

if (file_exists($WP_core['driver_dir'].'/lang.'.$WP_msg['language'].'.php')) {
    include($WP_core['driver_dir'].'/lang.'.$WP_msg['language'].'.php');
} else include($WP_core['driver_dir'].'/lang.en.php');

if (isset($_REQUEST['WP_DBset_action'])) $WP_DBset_action = $_REQUEST['WP_DBset_action'];
if (isset($_REQUEST['WP_DB'])) $WP_DB = $_REQUEST['WP_DB'];

include_once($WP_core['page_path'].'/drivers/'.$WP_core['database'].'/driver.php');
$DB = new driver($WP_core['conf_files'].'/driver.'.$WP_core['database'].'.conf.php');
if ('do' == $WP_DBset_action) {
    if ($WP_DB['pw1'] == $WP_DB['pw2']) {
        $erroneous = 0;
        // Hier wird die fslite.user.php geschrieben...
        $check = $DB->_write_file
                ($DB->DB['file_user']
                ,array('user' => $WP_DB['adm_name'], 'pass' => md5($WP_DB['pw1']))
                );
        if ($check) {
            list($uid, $pass) = $DB->authenticate($WP_DB['adm_name']);
            if ($pass == md5($WP_DB['pw1'])) {
                echo '<span style="color:darkgreen">Installation okay</span><br>';
            } else {
                echo '<span style="color:darkred">FSlite error:&nbsp;',$WP_drvmsg['notinstalled'],'</span><br>';
            }
            $DB->close();
        } else {
            echo '<span style="color:darkred">FSlite error:&nbsp;',$WP_drvmsg['nowrite'],'</span><br>';
        }
    } else {
        echo '<span style="color:darkred">',$WP_drvmsg['nonequal'],'</span><br>';
    }
    unset($WP_DBset_action);
}

if (!$WP_DBset_action) {
    echo $WP_drvmsg['HeadInst'],'<br /><table class="body">';
?>
<tr>
 <td align="left"><?php echo $WP_drvmsg['fslite_admname']; ?></td>
 <td align="left">
  <input type="text" name="WP_DB[adm_name]" value="<?php echo $WP_DB['adm_name']; ?>" size=16 />
 </td>
</tr>
<tr>
 <td align="left"><?php echo $WP_drvmsg['fslite_admpass']; ?></td>
 <td align="left">
  <input type="password" name="WP_DB[pw1]" value="<?php echo $WP_DB['pw1']; ?>" size=16 />
 </td>
</tr>
<tr>
 <td align="left"><?php echo $WP_drvmsg['fslite_admpass2']; ?></td>
 <td align="left">
  <input type="password" name="WP_DB[pw2]" value="<?php echo $WP_DB['pw2']; ?>" size=16 />
 </td>
</tr>
<tr>
 <td colspan="2" align="right">
  <input type="hidden" name="WP_DBset_action" value="do" />
  <input type="submit" value="<?php echo $WP_drvmsg['save']; ?>" />
 </td>
</tr>
</table><?php
}
?>
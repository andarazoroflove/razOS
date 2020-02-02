<?php
/* ------------------------------------------------------------------------- */
/* drivers/fslite/driver.php - PHlyMail 1.2.0+                               */
/* Proivdes storage functions for use with the file system                   */
/* (c) 2003-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.0.9                                                                    */
/* ------------------------------------------------------------------------- */

class driver {
 // This holds all config options
 var $DB = array();
 // Constructor
 // Read the config and open the DB
 function driver($Conf)
 {
     // Initialise database driver choices
     if (file_exists($Conf)) {
         foreach(file($Conf) as $l) {
             if ($l{0} == '#') continue;
             if (substr($l, 0, 15) == '<?php die(); ?>') continue;
             list($k, $v) = explode(';;', $l); $this->DB[$k] = trim($v);
         }
         unset($l, $k, $v, $Conf);

         $this->DB['file_user']     = $this->DB['fslite_prefix'].'/fslite.user.php';
         $this->DB['file_profiles'] = $this->DB['fslite_prefix'].'/fslite.profiles.php';
         $this->DB['file_adb_adr']  = $this->DB['fslite_prefix'].'/fslite.adb_adr.php';
         $this->DB['file_adb_grp']  = $this->DB['fslite_prefix'].'/fslite.adb_group.php';
         // WARNING! This setting is PHlyMail specific...
         $this->DB['umask']  = $GLOBALS['WP_core']['umask'];


         // Check existance of the user file
         if (file_exists($this->DB['file_user'])
                 && is_readable($this->DB['file_user'])) {
             return TRUE;
         }
     }
     return FALSE;
 }

 // Close the DB connection
 // Input  : void
 // Returns: $return    TRUE on success, FALSE otherwise
 function close()
 {
     return TRUE;
 }

 // Check wether a username:password combination matches a valid user of the system
 // Input  : authenticate(string user name)
 // Returns: $return     array data on success, FALSE otherwise
 //          $return[0] uid
 //          $return[1] MD5 hash of user's password
 function authenticate($un = '')
 {
     if (!file_exists($this->DB['file_user']) || !is_readable($this->DB['file_user'])) return array(0, time());
     $ini = parse_ini_file($this->DB['file_user']);
     if ($ini['user'] != $un) return array(0, time());
     return array(1, $ini['pass']);
 }

  // Return the basic user data for an user ID
 // Input  : get_usrdata(integer user id)
 // Returns: $return    array data on success, FALSE otherwise
 //          $return['accname'][id]  Display name of the account(s)
 function get_usrdata($uid = 0)
 {
     $ini = parse_ini_file($this->DB['file_user']);
     $return = array();
     foreach (array('username', 'externalemail', 'active', 'login_time', 'logout_time', 'default_profile') as $v) {
         $return[$v] = isset($ini[$v]) ? $ini[$v] : FALSE;
     }
     return $return;
 }


 // Get POP3 connection data of a certain user
 // Input  : get_popconnect(integer user id, string user name, integer account number)
 // Returns: $return    array data on success, FALSE otherwise
 //          $return['popserver']  string POP3 server
 //          $return['popport']    string POP3 port
 //          $return['popuser']    string POP3 user name
 //          $return['poppass']    string POP3 password
 //          $return['popnoapop']  use APOP, where 1 means no, 0 auto
 //          $return['killsleep']  seconds to wait after deleting mails to return
 function get_popconnect($uid = 0, $username = '', $accid = 1)
 {
     if (!file_exists($this->DB['file_profiles']) || !is_readable($this->DB['file_profiles'])) return array();
     $ini = parse_ini_file($this->DB['file_profiles'], 1);
     $ini[$accid]['poppass'] = $this->deconfuse
             ($ini[$accid]['poppass']
             ,$ini[$accid]['popserver'].$ini[$accid]['popport'].$ini[$accid]['popuser']
             );
     foreach (array('popserver', 'popport', 'popuser', 'poppass', 'popnoapop', 'killsleep') as $v) {
         $return[$v] = isset($ini[$accid][$v]) ? $ini[$accid][$v] : FALSE;
     }
     return $return;
 }

 // Get SMTP connection data of a certain user
 // Input  : get_smtpconnect(integer user id, string user name, integer account number)
 // Returns: $return    array data on success, FALSE otherwise
 //          $return['smtpserver']   string SMTP server
 //          $return['smtpport']     string SMTP port
 //          $return['smtpuser']     string SMTP user name
 //          $return['smtppass']     string SMTP password
 //          $return['smtpafterpop'] do SMTP-after-POP, where 1 means yes, 0 means no
 function get_smtpconnect($uid = 0, $username = '', $accid = 0)
 {
     if (!file_exists($this->DB['file_profiles']) || !is_readable($this->DB['file_profiles'])) return array();
     $ini = parse_ini_file($this->DB['file_profiles'], 1);
     $ini[$accid]['smtppass'] = $this->deconfuse
             ($ini[$accid]['smtppass']
             ,$ini[$accid]['smtpserver'].$ini[$accid]['smtpport'].$ini[$accid]['smtpuser']
             );
     foreach (array('smtpserver', 'smtpport', 'smtpuser', 'smtppass', 'smtpafterpop') as $v) {
         $return[$v] = isset($ini[$accid][$v]) ? $ini[$accid][$v] : FALSE;
     }
     return $return;
 }

 function get_usrfail($uid = FALSE)
 {
     return array(0, time());
 }

 function set_usrfail($uid = FALSE)
 {
     return TRUE;
 }

 function reset_usrfail($uid = FALSE)
 {
     return TRUE;
 }

 function set_logintime($uid = FALSE)
 {
     return TRUE;
 }

 function set_logouttime($uid = FALSE)
 {
     return TRUE;
 }

 function set_poplogintime($uid = FALSE, $accid = FALSE)
 {
     return TRUE;
 }

 // Get index for all accounts of a certain user
 // Input  : get_accidx(integer user id, string user name)
 // Returns: $return      array data on success, FALSE otherwise
 //          $return[id]  Display name of the account(s)
 function get_accidx($uid = 0, $username = '')
 {
     if (!file_exists($this->DB['file_profiles']) || !is_readable($this->DB['file_profiles'])) return array();
     $ini = parse_ini_file($this->DB['file_profiles'], 1);
     $return = array();
     foreach ($ini as $k => $v) {
         if (isset($v['accname'])) {
             $return[$k] = $v['accname'];
         }
     }
     return $return;
 }

 // Get the highest account id in use for a specific user
 // Input  : get_maxaccid(integer user id)
 // Returns: integer next possible profile id
 function get_maxaccid($uid = 0)
 {
     if (!file_exists($this->DB['file_profiles']) || !is_readable($this->DB['file_profiles'])) return 1;
     $ini = parse_ini_file($this->DB['file_profiles'], 1);
     return ( max(array_keys($ini)) + 1 );
 }

 // Get personal data of a certain user
 // Input  : get_accdata(integer user id, string user name, integer account number)
 // Returns: $return    array data on success, FALSE otherwise
 //          $return['acc_on']     integer is this account active?
 //          $return['sig_on']     integer is the signature active?
 //          $return['real_name']  string real name of the user
 //          $return['address']    string email address to use for sending
 //          $return['signature']  blob signature
 function get_accdata($uid = 0, $username = '', $accid = 0)
 {
     if (!file_exists($this->DB['file_profiles']) || !is_readable($this->DB['file_profiles'])) return array();

     $ini = parse_ini_file($this->DB['file_profiles'], 1);
     $return = array();
     foreach (array('acc_on', 'sig_on', 'real_name', 'address', 'signature') as $v) {
         $return[$v] = isset($ini[$accid][$v]) ? $ini[$accid][$v] : FALSE;
     }
     if (isset($return['signature'])) $return['signature'] = base64_decode($return['signature']);
     return $return;
 }

 // Insert new account for an user into the database
 // Input  : $input  array containing user data
 //          $input['uid']          ID of the user
 //          $input['accid']        ID of the account
 //          $input['accname']      display name of the account
 //          $input['acc_on']       '0' for inactive, '1' for active
 //          $input['sig_on']       '0' for inactive, '1' for active
 //          $input['popserver']    name or IP of the POP3 server
 //          $input['popport']      port number of the POP3 server
 //          $input['popuser']      user name for the POP3 account
 //          $input['poppass']      password for the POP3 account
 //          $input['popnoapop']    APOP: '0' for usee, '1' for do not use it
 //          $input['smtpserver']   name or IP of the SMTP server
 //          $input['smtpport']     port number of the SMTP server
 //          $input['smtpuser']     user name for the SMTP account
 //          $input['smtppass']     password for the SMTP account
 //          $input['smtpafterpop'] do SMTP-after-POP, where 1 means yes, 0 means no
 //          $input['real_name']    real name of the POP3 user for sending mails
 //          $input['address']      email address of the POP3 user for sending mails
 //          $input['signature']    signature of the POP3 user for sending mails
 //          $input['killsleep']    Seconds to wait after a delete operation before reconnect
 // Returns: $return  Record ID of created account on success, FALSE otherwise
 function add_account($input)
 {
     $input['accid'] = $this->get_maxaccid();
     if (!$input['accid']) $input['accid'] = 1;
     return $this->upd_account($input);
 }

 // Update the record of a user in the database
 // Input  : $input  array containing user data
 //          $input['uid']            UserID to update
 //          $input['username']       Login name
 //          $input['password']       Password (Omit if unchanged)
 //          $input['externalemail']  Email address for notifications
 //          $input['active']         '0' for no, '1' for yes (Omit if unchanged)
 // Returns: $return  TRUE on success, FALSE otherwise
 function upd_user($input)
 {
     if (isset($input['uid'])) unset($input['uid']);
     if (isset($input['active'])) unset($input['active']);
     if (isset($input['password']) && $input['password']) {
         $input['password'] = md5($input['password']);
     } else {
         unset($input['password']);
     }
     return $this->_write_file($this->DB['file_user'], $input, TRUE);
 }

 // Update the record of an account for an user into the database
 // Input  : $input  array containing user data
 //          $input['uid']         ID of the user
 //          $input['accid']       ID of the account
 //          $input['accname']      display name of the account
 //          $input['acc_on']       '0' for inactive, '1' for active
 //          $input['sig_on']       '0' for inactive, '1' for active
 //          $input['popserver']    name or IP of the POP3 server
 //          $input['popport']      port number of the POP3 server
 //          $input['popuser']      user name for the POP3 account
 //          $input['poppass']      password for the POP3 account, FALSE to keep current
 //          $input['popnoapop']    APOP: '0' for usee, '1' for do not use it
 //          $input['smtpserver']   name or IP of the SMTP server
 //          $input['smtpport']     port number of the SMTP server
 //          $input['smtpuser']     user name for the SMTP account
 //          $input['smtppass']     password for the SMTP account, FALSE to keep current
 //          $input['smtpafterpop'] do SMTP-after-POP, where 1 means yes, 0 means no
 //          $input['real_name']    real name of the POP3 user for sending mails
 //          $input['address']      email address of the POP3 user for sending mails
 //          $input['signature']    signature of the POP3 user for sending mails
 //          $input['killsleep']    Seconds to wait after a delete operation before reconnect
 // Returns: $return  TRUE on success, FALSE otherwise
 function upd_account($input)
 {
     if (isset($input['uid'])) unset($input['uid']);
     if ($input['poppass'] != FALSE) {
         $input['poppass'] = $this->confuse($input['poppass'], $input['popserver'].$input['popport'].$input['popuser']);
     }
     if ($input['smtppass'] != FALSE) {
         $input['smtppass'] = $this->confuse($input['smtppass'],
                                             $input['smtpserver'].$input['smtpport'].$input['smtpuser']);
     }
     if (isset($input['signature'])) {
         $input['signature'] = base64_encode($input['signature']);
     }
     foreach ($input as $k => $v) {
         if ($k == 'accid') continue;
         $save[$input['accid']][$k] = $v;
     }
     return $this->_write_file($this->DB['file_profiles'], $save, TRUE);
 }

 // Delete an account of a given user from database
 // Input:  delete_account(string username, integer accountID)
 // Return: TRUE on success, FALSE otherwise
 function delete_account($un = '', $accountnumber = '')
 {
     if (!$accountnumber) return FALSE;
     if (!file_exists($this->DB['file_profiles']) || !is_readable($this->DB['file_profiles'])) return array();
     $ini = parse_ini_file($this->DB['file_profiles'], 1);
     unset($ini[$accountnumber]);
     return $this->_write_file($this->DB['file_profiles'], $ini, FALSE);
 }

 // Switch activity status of a user's account
 // Input:  onoff_user(string username, integer accountID, integer status) status[0|1]
 // Return: TRUE on success, FALSE otherwise
 function onoff_account($un, $accid, $active)
 {
     $save[$accid]['acc_on'] = $active;
     return $this->_write_file($this->DB['file_profiles'], $save, TRUE);
 }

 // Check, if a given username (already) exists in the database
 // Input  : checkfor_username(string username)
 // Returns: Account ID if exists, FALSE otherwise
 function checkfor_accname($un, $accname = '')
 {
     if (file_exists($this->DB['file_profiles'])) {
         $ini = parse_ini_file($this->DB['file_profiles'], 1);
         foreach ($ini as $k => $v) {
             if (isset($v['accname']) && $v['accname'] == $accname) return $k;
         }
     }
     return FALSE;
 }

 // Encrypt a string
 // Input:   confuse(string $data, string $key);
 // Returns: encrypted string
 function confuse($data = '', $key = '')
 {
     $encoded = '';
     $DataLen = strlen($data);
     if (strlen($key) < $DataLen) $key = str_repeat($key, ceil($DataLen/strlen($key)));
     for ($i = 0; $i < $DataLen; ++$i) {
         $encoded .= chr((ord($data{$i}) + ord($key{$i})) % 256);
     }
     return base64_encode($encoded);
 }

 // Decrypt a string
 // Input:   deconfuse(string $data, string $key);
 // Returns: decrypted String
 function deconfuse($data = '', $key = '')
 {
     $data = base64_decode($data);
     $decoded = '';
     $DataLen = strlen($data);
     if (strlen($key) < $DataLen) $key = str_repeat($key, ceil($DataLen/strlen($key)));
     for($i = 0; $i < $DataLen; ++$i) {
         $decoded .= chr((256 + ord($data{$i}) - ord($key{$i})) % 256);
     }
     return $decoded;
 }

 // Internal function
 // Updates (or creates) the given file
 // Input:  _write_file(string path to the file, array payload to write, boolean replace)
 //         if replace is TRUE, the existing file will stay as is, but the payload will overwrite existing
 //         parameters, if set to FALSE, we will start with an empty file, containing only the payload data
 // Return: TRUE on success, FALSE on failure
 function _write_file($file, $data, $replace = TRUE)
 {
     if ($replace) {
         if (file_exists($file) && is_readable($file)) {
             $original = parse_ini_file($file, 1);
         } else {
             $original = array();
         }
         foreach ($data as $k => $v) {
             if (is_array($v)) {
                 foreach($v as $k2 => $v2) {
                     $original[$k][$k2] = $v2;
                 }
             } else {
                 $original[$k] = $v;
             }
         }
     } else {
         $original = &$data;
     }
     if (!file_exists($file)) {
         touch($file);
         chmod($file, $this->DB['umask']);
     }
     $fid = fopen($file, 'w');
     if (!is_resource($fid)) return FALSE;
     // Trying to lock the file... But don't care, if not possible. Prevents the user from a lot of PITA
     @flock($fid, LOCK_EX);
     fputs($fid, ';<?php die(); ?>'.LF);
     foreach ($original as $k => $v) {
         if (is_array($v)) {
             fputs($fid, '['.$k.']'.LF);
             foreach ($v as $k2 => $v2) {
                 fputs($fid, $k2.' = ');
                 fputs($fid, (preg_match('![^a-z0-9]!i', $v2)) ? '"'.$v2.'"'.LF : $v2.LF);
             }
         } else {
             fputs($fid, $k.' = ');
             fputs($fid, (preg_match('![^a-z0-9]!i', $v)) ? '"'.$v.'"'.LF : $v.LF);
         }
     }
     fclose($fid);
     return TRUE;
 }

}
?>
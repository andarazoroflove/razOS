<?php
// choices.inc.php
// PHlyMail 2.1.0+
// Please be aware, that most of the allday settings may be changed via the setup module
//
// Access mask for folders / files to create
$WP_core['umask'] = 0755;
// Language to use
$WP_core['language'] = 'en';
// The name of the skin used?
$WP_core['skin_name'] = 'Santiago';
// Where does PHlyMail looks for its files?
$WP_core['page_path'] = '.';
// Path to detail files (server profiles, choices etc.)
$WP_core['conf_files'] = 'conf';
// Name of the used database driver
$WP_core['database'] = 'fslite';
// Path to skins folder
$WP_core['skin_path'] = 'skins';
// Method to send mails (sedmail|smtp|system)
$WP_core['send_method'] = 'smtp';
// Path to sendmail
$WP_core['sendmail'] = '';
// Put a copy of outgoing mails in your mailbox? (Send, Forward, Answer)
$WP_core['save_sent'] = '1';
// Request return receipt on sending?
$WP_core['receipt_out'] = '0';
// Word wrap text to 75 chars on sending?
$WP_core['send_wordwrap'] = '1';
// Wordwrap width on display, '0' -> ignore
$WP_core['read_wordwrap'] = '80';
// Size of emails to get marked as 'big' [red] in the inbox
$WP_core['big_mark'] = '8388608'; // 8M
// Size of emails to not display due to their size
$WP_core['big_noshow'] = '12582912'; //12M
// Allow sending (Send, Forward, Answer, Via)
$WP_core['allow_send'] = 'true';
// Quoted printable attachments: render size net or gross?
$WP_core['exact_sizes'] = '1';
// Configuration of PHlyMail via frontend allowed?
$WP_core['allow_user_conf'] = 'true';
// Configuration of accounts via frontend allowed?
$WP_core['conf_acc'] = 'true';
// FrontEnd active or not?
$WP_core['online_status'] = 'yes';

// New settings for mailbox view
$WP_core['pagesize'] = 50;
$WP_core['killall']  = 1;

// Seconds to wait after killing; 0 to disable
$WP_core['killsleep'] = 1;

// Handling of failed Logins
// - Seconds to wait (both system and POP3 login)
$WP_core['waitonfail'] = 3;

// Maximum upload size of attachments
$WP_core['maxupload'] = 250000;

// Logfile Settings
$WP_core['log_sysauth']  = 'no';
$WP_core['log_popauth']  = 'no';
$WP_core['log_pagehits'] = 'no';
$WP_core['log_basename'] = date('Y').'/'.date('m').'/'.date('d').'.log';
$WP_core['log_dirphits'] = 'logging/pagehits';
$WP_core['log_dirlipop'] = 'logging/poplogin';
$WP_core['log_dirlisys'] = 'logging/syslogin';

// Only change this value if so requested
$WP_core['diagnosis'] = 0;

?>
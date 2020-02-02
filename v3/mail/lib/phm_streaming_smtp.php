<?php
/* ------------------------------------------------------------------------- */
/* phm_streaming_smtp.php -> Class to send mails through SMTP                */
/* (c) 2003-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.3.6                                                                    */
/* ------------------------------------------------------------------------- */

// Possible improvements:
// - more tolerant against bogus servers (some use the optional initial-response
//   argument for AUTH PLAIN, others do not)
// - Implement DIGEST-MD5 SASL mechanism

// Missing:
// - Sth. like $this->read_from_file(), where one specifies a filehandle from
//   which a mail can be read and immediately put to the open stream


class phm_streaming_smtp {

 // Define standard line endings (CRLF and LF)
 var $CRLF = "\r\n";
 var $LF   = "\n";

 // Init Error (query with $this->get_last_error()
 var $error = false;

 // Multiple errors can be returned in either HTML linebreaks or plain LF
 var $error_nl = 'HTML';

 // Init server to use for connection
 var $server = false;

 // Init port number to use for connection
 var $port = false;

 // Init resource handle for connection
 var $smtp = false;

 // Default port number to use, if not specified
 var $def_port = 25;

 // If set to yes, only valid SMTP AUTH connections will be used
 var $authonly = 'no';

 // List of SASL mechanisms we support
 var $_SASL = array('cram_md5' , 'login', 'plain');

 /**
 * The phm_streaming_smtp constructor method
 *
 * If called with a server name [and optionally a port number], it
 * tries to connect to that specific server immediately.
 * If called in a void context (without arguments), you can
 * connect by actually calling the method open_server()
 * and let this negotiate the correct server by itself
 * If you pass a username and a password here, these will be used
 * for SMTP AUTH (if supported by server)
 *
 * @param    string    Servername or IP address
 * @param    [integer   Port number, if omitted, 25 is used]
 * @param    [string    Username for SMTP AUTH]
 * @param    [string    Password for SMTP AUTH]
 */
 function phm_streaming_smtp($server = '', $port = 25, $username = false, $password = false)
 {
     if ($server != '') {
         if ($this->_connect($server, $port)) {
             $this->server = $server;
             $this->port = $port;
             if ($username) $this->username = $username;
             if ($password) $this->password = $password;
             return true;
         } else return false;
     } else return true;
 }

 /**
 * Sets a new option value. Available options and values:
 * [authonly - use SMTP AUTH only ('yes', 'no')]
 * [error_nl - use HTML linebreaks ('HTML') or plain LF ('LF')]
 *
 * @param    string    Parameter to set
 * @param    string    Value to use
 * @return   boolean   TRUE on success, FALSE otherwise
 * @access   public
 */
 function set_parameter($option, $value = false)
 {
     switch ($option) {
     case 'authonly':
         if ('yes' == $value) $this->authonly = 'yes';
         elseif ('no' == $value) $this->authonly = 'no';
         else {
             $this->error .= 'Illegal option value for "authonly".'.$this->LF;
             return false;
         }
         break;
     case 'error_nl':
         if ('HTML' == $value) $this->error_nl = 'HTML';
         elseif ('LF' == $value) $this->error_nl = 'LF';
         else {
             $this->error .= 'Illegal option value for "error_nl".'.$this->LF;
             return false;
         }
         break;
     default:
         $this->error .= 'Unknown option '.$option.$this->LF;
         return false;
     }
     return true;
 }

 /**
 * Read out the last error that occured
 *
 * @param    void
 * @return   string    Returns the last error, if one exists, else an emtpy string
 * @access   public
 */
 function get_last_error()
 {
     $error = ($this->error) ? $this->error : '';
     $this->error = false;
     return ($this->error_nl == 'HTML') ? nl2br($error) : $error;
 }

 /**
 * Open a server connection
 *
 * If you've specified username and password on construction, these will be used here,
 * if you specified no server and port on construction, this method will negotiate
 * the server to be used by querying the MX root record for the first TO: address
 * passed.
 * Be aware, that using multiple TO: addresses with a negotiated SMTP server might
 * result in TO: addresses rejected due to server's No-Relay policy
 * This method makes use of the "authonly" setting
 *
 * @param    string    FROM: address
 * @param    array     TO: addres(ses)
 * @return   boolean   Returns TRUE on success, FALSE otherwise
 * @access   public
 */
 function open_server($from = false, $to = false)
 {
     if (!$from) {
         $this->error .= 'You must specify a from address'.$this->LF;
         return false;
     }
     if (!$to) {
         $this->error .= 'You must specify at least one recipient address'.$this->LF;
         return false;
     }
     if (!is_array($to)) {
         $to = array($to);
     }

     list(,$this->helodomain) = explode('@', $from);

     if ($this->server) {
         // We either use the global setting for the server to use (if given)...
         $mx[0]   = &$this->server;
         $port[0] = isset($this->port) ? $this->port : $this->def_port;
         $user[0] = isset($this->username) ? $this->username : false;
         $pass[0] = isset($this->password) ? $this->password : false;
     } else {
         // ... or try to negotiate on our own
         list(,$querydomain) = explode('@', $to[0], 2);
         // On Windows systems this function is not available
         if (!function_exists('getmxrr')) {
             $this->error .= 'No SMTP servers for '.$querydomain.' found'.$this->LF;
             return false;
         }
         if (getmxrr($querydomain, $mx, $weight) == 0) {
             $this->error .= 'No SMTP servers for '.$querydomain.' found'.$this->LF;
             return false;
         }
         array_multisort($mx, $weight);
     }
     // Now trying to find one server to talk to... first come, first serve
     foreach ($mx as $id => $host) {
         if (!isset($port[$id])) $port[$id] = $this->def_port;
         // If we can't connect, try next server in list
         if (!$this->smtp && !$this->_connect($host, $port[$id])) continue;

         // Some SMTP servers behave funny by rejecting the first line, when it is EHLO / HELO, so we put
         // nonsense to the stream as our first line
         $this->talk('*');

         // We've got credentials and try to use SMTP AUTH autoamgically
         if (isset($user[$id]) && $user[$id]) {
             $response = $this->talk('EHLO '.$this->helodomain);
             if (substr($response, 0, 3) == '250') {
                 // Server supports SMTP AUTH... try the supported mechanisms to authenticate
                 $supported = $this->_get_supported_sasl_mechanisms($response);
                 // Find the mechanisms supported on both sides
                 $SASL = array_intersect($this->_SASL, $supported);
                 
                 // Initialise state of authentication
                 $this->error .= 'Could not use SMTP AUTH'.$this->LF;
                 $this->is_auth = false;
                 foreach ($SASL as $v) {
                     $function_name = '_auth_'.$v;
                     if ($this->{$function_name}($user[$id], $pass[$id])) {
                         $this->error = false;
                         $this->is_auth = true;
                         break;
                     }
                 }
                 if (!$this->is_auth) {
                     if ($this->authonly == 'yes') {
                         $this->close();
                         $this->error .= 'SMTP-AUTH failed. Aborting connection'.$this->LF;
                         return false;
                     } else {
                         $response = $this->talk('HELO '.$this->helodomain);
                         if (substr($response, 0, 3) != '250') {
                             $this->close();
                             $this->error .= 'HELO '.$this->helodomain.' failed. Aborting connection'.$this->LF;
                             return false;
                         }
                     }
                 }
             } else {
                 if ($this->authonly == 'yes') {
                     $this->close();
                     $this->error .= 'EHLO '.$this->helodomain.' failed. Aborting connection'.$this->LF;
                     return false;
                 } else {
                     $response = $this->talk('HELO '.$this->helodomain);
                     if (substr($response, 0, 3) != '250') {
                         $this->close();
                         $this->error .= 'HELO '.$this->helodomain.' failed. Aborting connection'.$this->LF;
                         return false;
                     }
                 }
             }
         } else {
             $response = $this->talk('HELO '.$this->helodomain);
             if (substr($response, 0, 3) != '250') {
                 $this->close();
                 $this->error .= 'HELO '.$this->helodomain.' failed. Aborting connection'.$this->LF;
                 return false;
             }
         }
         return $this->init_mail_transfer($from, $to);
     }
     return false;
 }

 function init_mail_transfer($from = false, $to = false)
 {
     if (!$from) {
         $this->error .= 'You must specify a from address'.$this->LF;
         return false;
     }
     if (!$to) {
         $this->error .= 'You must specify at least one recipient address'.$this->LF;
         return false;
     }
     if (!is_array($to)) {
         $to = array($to);
     }

     $response = $this->talk('MAIL FROM: <'.$from.'>');
     if (substr($response, 0, 3) != '250') {
         $this->close();
         $this->error .= 'FROM address '.$from.' rejected by server: '.$response.$this->LF;
         return false;
     }
     $accepted = 0;
     foreach ($to as $k => $val) {
         $response = $this->talk('RCPT TO: <'.$val.'>');
         // All return codes of 25* mean, that the address is accepted
         if (substr($response, 0, 2) == '25') $accepted = 1;
         else $failed[] = $this->LF.'- '.$val.': '.trim($response);
     }
     if (0 == $accepted) {
         $this->close();
         $this->error .= 'None of the TO addresses were accepted: '.join(',', $failed).$this->LF;
         return false;
     }
     $response = $this->talk('DATA');
     if (substr($response, 0, 3) != '354') {
         $this->close();
         $this->error .= 'Server rejected the DATA command: '.trim($response).$this->LF;
         return false;
     } else {
         if (isset($failed)) {
             $this->error .= 'Some of the TO addresses were rejected: '.join(',', $failed).$this->LF;
         }
         return true;
     }
 }

 /**
 * Write to the SMTP stream opened before by open_server()
 *
 * @param    string    Line of data to put to the stream
 * @return   boolean   Returns TRUE on success, FALSE otherwise
 * @access   public
 */
 function put_data_to_stream($line = false)
 {
     if (!is_resource($this->smtp)) return false;
     if (!$line) return false;
     fwrite($this->smtp, $line);
     return true;
 }

 /**
 * Finishing a mail transfer to the server
 * Use this method, if your application doesn't automatically
 * put the final CRLF.CRLF to the SMTP stream after
 * putting al the mail data to it.
 * This method implicitly calls check_success().
 *
 * @param    void
 * @return   boolean    Return state of check_success()
 * @access   public
 */
 function finish_transfer()
 {
     fwrite($this->smtp, $this->CRLF.'.'.$this->CRLF);
 }

 /**
 * Call this method after putting your last mail line to the server
 *
 * @param    void
 * @return   boolean   Returns TRUE on success, FALSE otherwise
 * @access   public
 */
 function check_success()
 {
     $line = fgets($this->smtp, 4096);
     if (substr($line, 0, 3) != '250') {
         $this->error .= 'Wrong DATA: '.trim($line).$this->LF;
         return false;
     }
     return true;
 }

 /**
 * Talk to the SMTP server directly (for things not covered by this class)
 *
 * @param    string    Command to pass to the server
 * @return   string    Answer of the server
 * @access   public
 */
 function talk($input)
 {
     $output = false;
     fputs($this->smtp, $input.$this->CRLF);
     $end = 0;
     while (0 == $end) {
         $line = fgets($this->smtp, 4096);
         if (' ' == substr($line, 3, 1)) $end = 1;
         $output.= $line;
     }
     return $output;
 }

 /**
 * Close a previously opened connection
 * Although it doesn't return you something, you can query the state by using
 * get_last_error()
 *
 * @param    void
 * @return   void
 * @access   public
 */
 function close()
 {
     if (is_resource($this->smtp)) {
         $this->talk('QUIT');
         fclose($this->smtp);
         $this->smtp = false;
         $this->error .= 'Connection closed'.$this->LF;
     }
     else {
         $this->error .= 'No connection to close. Did nothing.'.$this->LF;
     }
 }

 /**
 * Open socket to an SMTP server
 *
 * @param    string    Server name or IP address
 * @param    integer   Port number
 * @return   boolean   TRUE on success, FALSE otherwise
 * @access   private
 */
 function _connect($server, $port)
 {
     $response = false;
     $smtp = @fsockopen($server, $port, $errno, $errstr, 15);
     if (!$smtp) {
         $this->error .= 'No connect to '.$server.':'.$port.' ('.$errno.' '.$errstr.')'.$this->LF;
         return false;
     }
     $this->smtp = $smtp;

     $end = 0;
     while (0 == $end) {
         $line = fgets($this->smtp, 4096);
         if (' ' == substr($line, 3, 1)) $end = 1;
         $response.= $line;
     }
     if (!$response || substr($response, 0, 3) != '220') {
         $this->close();
         $this->error .= 'Connecting to '.$server.':'.$port.' failed ('.$response.')'.$this->LF;
         return false;
     }
     return true;
 }

 /**
 * Find out about SASL mechanisms a specific SMTP server supports
 *
 * @param    string    Server answer to EHLO command
 * @return   array     list of supported SASL mechanisms
 * @access   private
 */
 function _get_supported_sasl_mechanisms($response)
 {
     if (preg_match('!^250(\ |\-)AUTH(\ |\=)([\w\s-_]+)$!Umi', $response, $found)) {
         $found[3] = strtolower(str_replace('-',  '_',  trim($found[3])));
         return explode(' ', $found[3]);
     } else {
         return array();
     }
 }

 /**
 * Implementation of SASL mechanism CRAM-MD5
 *
 * @param    string    Username
 * @param    string    Password
 * @return   boolean   TRUE on successful authentication, FALSE otherwise
 * @access   private
 */
 function _auth_cram_md5($user = '', $pass = '')
 {
     // See RFC2104 (HMAC, also known as Keyed-MD5)
     $response = $this->talk('AUTH CRAM-MD5');
     if (substr($response, 0, 3) == '334') {
         // Get the challenge from the server
         $challenge = base64_decode(substr(trim($response), 4));
         // Secret to use
         $secret = $pass;
         // Rightpad with NUL bytes to have 64 chars
         if (strlen($secret) < 64) {
             $secret = $secret.str_repeat(chr(0x00), 64 - strlen($secret));
         }
         // In case, the secret is longer than 64 chars, md5() it
         if (strlen($secret) > 64) {
             $secret = md5($secret);
         }

         $ipad = str_repeat(chr(0x36), 64);
         $opad = str_repeat(chr(0x5c), 64);

         $shared = bin2hex(pack('H*', md5(($secret ^ $opad).pack('H*', md5(($secret ^ $ipad).$challenge)))));

         $response = $this->talk(base64_encode($user.' '.$shared));
         if (substr($response, 0, 3) != '334') {
             $this->error .= 'AUTH CRAM-MD5 failed:'.trim($response).$this->LF;
             return false;
         }
         return true;
     } else {
         $this->error .= 'AUTH CRAM-MD5 rejected: '.trim($response).$this->LF;
         return false;
     }
 }

 /**
 * Implementation of SASL mechanism LOGIN
 *
 * @param    string    Username
 * @param    string    Password
 * @return   boolean   TRUE on successful authentication, FALSE otherwise
 * @access   private
 */
 function _auth_login($user = '', $pass = '')
 {
     $response = $this->talk('AUTH LOGIN');
     if (substr($response, 0, 3) == '334') {
         $response = $this->talk(base64_encode($user));
         if (substr($response, 0, 3) != '334') {
             $this->error .= 'AUTH LOGIN failed, wrong username? Aborting authentication.'.$this->LF;
             return false;
         }
         $response = $this->talk(base64_encode($pass));
         if (substr($response, 0, 3) != '235') {
             $this->error .= 'AUTH LOGIN failed, wrong password? Aborting authentication.'.$this->LF;
             return false;
         }
         return true;
     } else {
         $this->error .= 'AUTH LOGIN rejected: '.trim($response).$this->LF;
         return false;
     }
 }

 /**
 * Implementation of SASL mechanism PLAIN
 *
 * @param    string    Username
 * @param    string    Password
 * @return   boolean   TRUE on successful authentication, FALSE otherwise
 * @access   private
 */
 function _auth_plain($user = '', $pass = '')
 {
     $response = $this->talk('AUTH PLAIN '.base64_encode(chr(0).$user.chr(0).$pass));
     if (substr($response, 0, 3) != '235') {
         $this->error .= 'AUTH PLAIN failed. Aborting authentication.'.$this->LF;
         return false;
     }
     return true;
 }

}
?>
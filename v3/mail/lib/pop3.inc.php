<?php
/* ------------------------------------------------------------------------- */
/* POP3 Driver - PHlyMail 1.2.0+                                             */
/* Proivdes connect and retrieve functions for the POP3 protocol             */
/* (c) 2003 blue birdy, Berlin (http://bluebirdy.de)                         */
/* All rights reserved                                                       */
/* v0.1.5                                                                    */
/* ------------------------------------------------------------------------- */

// Still missing: Capabilities regarding RFC 2449

class pop3 {

 var $error            = false;
 var $CRLF             = "\r\n";
 var $LF               = "\n";
 var $append_errors    = false;
 var $timestamp_errors = false;
 var $reconnect_sleep  = 1;

 function pop3($server = '', $port = 110, $recon_slp = 0)
 {
     if (!$port) $port = 110;
     if ($this->_connect($server, $port)) {
         $this->server = $server;
         $this->port = $port;
         $this->reconnect_sleep = isset($recon_slp) ? $recon_slp : 0;
         $this->return = 'connected';
     } else $this->return = 'unconnected';
     return true;
 }

 // Sole aim is, to know, wether we are connected or not
 // since we cannot return something useful on construction
 // of the object
 function check_connected()
 {
     return $this->return;
 }


 // Set an internal parameter
 function set_parameter($option, $value = false)
 {
     switch ($option) {
     case 'append_errors':
         if ('yes' == $value) $this->append_errors = true;
         elseif ('no' == $value) $this->append_errors = false;
         break;
     case 'timestamp_errors':
         if ('yes' == $value) $this->timestamp_errors = true;
         elseif ('no' == $value) $this->timestamp_errors = false;
         break;
     case 'reconnect_sleep':
         $this->reconnect_sleep = ($value+0);
         break;
     default:
         $this->_set_error('Unknown option '.$option);
         return false;
     }
     return true;
 }

 function get_last_error()
 {
     $return = ($this->error) ? $this->error : '';
     unset($this->error);
     return $return;
 }

 // Log in to POP3 server
 function login($username = '', $password = '', $apop = 1)
 {
     // Issue empty command - some POP3 server constantly drop the first command sent to them
     // This is somewhat violating RFC1939 but who is mistaking here?
     $this->talk('NOOP');
     //
     $return = array('type' => false, 'login' => false);
     // APOP
     if (preg_match('/(<.+@.+>)$/', $this->greeting, $token) && 1 == $apop) {
         $response = $this->talk('APOP '.$username.' '.md5($token[1].$password));
         if (strtolower(substr($response, 0, 3)) == '+ok') {
             $return['login'] = 1;
             $return['type'] = 'secure';
         } else {
             $this->_set_error($response);
             $this->close();
             sleep($this->reconnect_sleep);
             $this->_connect($this->server, $this->port);
             if (!$this->_alive()) return $return;
         }
     }
     // USER/PASS
     if (1 != $return['login']) {
         $response = $this->talk('USER '.$username);
         if (strtolower(substr($response, 0, 4)) == '-err') {
             $this->error_set_error($response);
             if (!$this->_alive()) return $return;
         }
         $response = $this->talk('PASS '.$password);
         if (strtolower(substr($response, 0, 3)) == '+ok') {
             $return['login'] = 1;
             $return['type'] = 'normal';
         } else {
             $this->_set_error($response);
             if (!$this->_alive()) return $return;
         }
     }
     return $return;
 }

 // Return LIST, if mail given of this one, else complete
 function get_list($mail = false)
 {
     if ($mail) {
         $line = explode(' ', $this->talk('LIST '.$mail));
         if ('+ok' == strtolower($line[0])) {
             return $line[2];
         } else {
             $this->_set_error('POP server response: '.join(' ', $line));
             return false;
         }
     } else {
         $line = explode(' ', $this->talk('LIST'));
         if ('+ok' == strtolower($line[0])) {
             while ($line = $this->talk_ml()) {
                 list($nummer, $bytes) = explode(' ', trim($line), 2);
                 $return[$nummer] = $bytes;
             }
             return $return;
         } else {
             $this->_set_error('POP server response: '.join(' ', $line));
             return false;
         }
     }
 }

 // Get the header lines of a mail
 function top($mail = false)
 {
     if (!$mail) {
         $this->_set_error('No mail given');
         return false;
     }
     $response = explode(' ', $this->talk('TOP '.$mail.' 0'));
     if ('+ok' == strtolower($response[0])) {
         $return = '';
         while ($line = $this->talk_ml()) {
             $return .= $line;
         }
         return $return;
     } else {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
 }

 // Get the Unique ID of a mail
 function uidl($mail = false)
 {
     if (!$mail) {
         $this->_set_error('No mail given');
         return false;
     }
     $response = explode(' ', $this->talk('UIDL '.$mail));
     if ('+ok' == strtolower($response[0])) {
         return $response[2];
     } else {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
 }

 // Get stats of a POP3 box
 function stat()
 {
     $return = array('mails' => false, 'size' => false);
     $response = explode(' ', $this->talk('STAT'));
     if ('+ok' == strtolower($response[0])) {
         return array('mails' => $response[1], 'size' => $response[2]);
     } else {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
 }

 // Delete a selected Email from POP3 server
 function delete($mail = false)
 {
     if (!$mail) {
         $this->_set_error('No mail given');
         return false;
     }
     $response = explode(' ', $this->talk('DELE '.$mail));
     if ('+ok' == strtolower($response[0])) {
         return true;
     } else {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
 }

 // Do nothing.
 // Since RFC1939 requires a positive response, we don't care about errors yet
 function noop()
 {
     $this->talk('NOOP');
     return true;
 }

 // Unmark any mails marked as deleted.
 // Since RFC1939 requires a positive response, we don't care about errors yet
 function reset()
 {
     $this->talk('RSET');
     return true;
 }

 // Send RETR command to POP3 server
 // Get subsequent server responses via talk_ml()
 function retrieve($mail = false)
 {
     if (!$mail) {
         $this->_set_error('No mail given');
         return false;
     }
     $response = explode(' ', $this->talk('RETR '.$mail));
     if ('+ok' == strtolower($response[0])) {
         return true;
     } else {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
 }

 // Retrieve a mail from server and put into given file
 function retrieve_to_file($mail = false, $path = false)
 {
     if (!$mail || !$path) {
         $this->_set_error('Usage: retrieve_to_file(integer mail, string path)');
         return false;
     }
     if (file_exists(dirname($path)) && is_dir(dirname($path))) {
         $this->_set_error('Non existent directory');
         return false;
     }
     $out = fopen($path, 'w');
     if (!$out) {
         $this->_set_error('Could not open file');
         return false;
     }
     $response = explode(' ', $this->talk('RETR '.$mail));
     if ('+ok' == strtolower($response[0])) {
         while ($line = $this->talk_ml()) {
             fputs($out, $line);
         }
         fclose($out);
         return $path;
     } else {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
 }

 // Send command to POP3 server and return first line of response
 function talk($input = false)
 {
     if (!$input) return false;
     fputs($this->fp, $input.$this->CRLF);
     return trim(fgets($this->fp, 1024));
 }

 // Return a line of multiline POP3 responses, return false on last line
 function talk_ml()
 {
     $line = fgets($this->fp, 1024);
     if (substr($line, 0, 3) != '.'.$this->CRLF) return $line;
     else return false;
 }

 // Close POP3 connection
 function close()
 {
     $response = explode(' ', $this->talk('QUIT'));
     fclose($this->fp);
     if ('+ok' == strtolower($response[0])) {
         return true;
     } else {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
 }

 // internal functions

 // Do the actual connect to the chosen server
 function _connect($server = '', $port = 110)
 {
     $ERRNO  = false;
     $ERRSTR = false;
     $fp = @fsockopen($server, $port, $ERRNO, $ERRSTR, 1);
     if (!$fp) {
         $this->_set_error($ERRSTR.'('.$ERRNO.')');
         return false;
     }
     $this->fp = $fp;
     $response = trim(fgets($fp, 1024));
     if (strtolower(substr($response, 0, 3)) != '+ok') {
         $this->_set_error('POP server response: '.join(' ', $response));
         return false;
     }
     $this->greeting = $response;
     return true;
 }

 // Add or set an (timestamped error), that can be requested via get_last_error()
 function _set_error($error)
 {
     $vorn = ($this->timestamp_errors) ? time().' ' : '';
     if ($this->append_errors) {
         $this->error .= $vorn.$error.$this->LF;
     } else {
         $this->error  = $vorn.$error;
     }
 }

 // Try to find out, wether the connection is still alive
 function _alive()
 {
     // Invalid or non-existent handler
     if (!$this->fp || !is_resource($this->fp)) return false;
     $response = @socket_get_status($this->fp);
     if (!$response || $response['timed_out']) return false;
     return true;
 }

}
?>
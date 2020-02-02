<?php
/* ------------------------------------------------------------------------- */
/* phm_streaming_sendmail.php -> Class to send mails through Sendmail        */
/* (c) 2003-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Common Branch                                                    */
/* v0.1.3                                                                    */
/* ------------------------------------------------------------------------- */

class phm_streaming_sendmail {

 // Define standard line endings (CRLF and LF)
 var $CRLF = "\r\n";
 var $LF   = "\n";

 // Init Error (query with $this->get_last_error()
 var $error = FALSE;

 // How do we operate? 'classic' for PHP < 4.3.0, else 'modern'
 var $pipemode = 'classic';

 // Init resource handle for connection
 var $sndml = FALSE;

 /**
 * The phm_streaming_sendmail constructor method.
 *
 * @param    string    Path to sendmail with arguments
 */
 function phm_streaming_sendmail($path)
 {
     // Try a fallback, if modern is set, but not possible
     if ($this->pipemode == 'modern') {
         $this->pipemode = (function_exists('proc_open')) ? 'modern' : 'classic';
     }
     if ($path != '' && $this->_open($path)) {
         return TRUE;
     } else {
         return TRUE;
     }
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
     return ($this->error) ? $this->error : '';
 }

 /**
 * Write to the pipe
 *
 * @param    string    Line of data to put to the pipe
 * @return   boolean   Returns TRUE on success, FALSE otherwise
 * @access   public
 */
 function put_data_to_stream($line = FALSE)
 {
     if (!is_resource($this->sndml)) return FALSE;
     if (!$line) return FALSE;
     fwrite($this->sndml, $line);
     return TRUE;
 }

 /**
 * Finishing a mail transfer to the sendmail
 * Use this method, if your application doesn't automatically
 * put the final CRLF.CRLF to the stream after
 * putting all the mail data to it.
 * This method implicitly calls check_success().
 *
 * @param    void
 * @return   boolean    Return state of check_success()
 * @access   public
 */
 function finish_transfer()
 {
     fwrite($this->sndml, $this->LF.'.'.$this->LF);
     return $this->check_success();

 }

 /**
 * Call this method after putting your last mail line to the stream
 *
 * @param    void
 * @return   boolean   Returns TRUE on success, FALSE otherwise
 * @access   public
 */
 function check_success()
 {
     return TRUE;
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
     switch ($this->pipemode) {
     case 'modern':
         if (!feof($this->pipes[1])) {
             while (!feof($this->pipes[2]) && $line = fgets($this->pipes[2], 4096)) {
                 $this->error .= $line;
             }
         }
         if (!feof($this->pipes[2])) {
             while (!feof($this->pipes[2]) && $line = fgets($this->pipes[2], 4096)) {
                 $this->error .= $line;
             }
         }
         fclose($this->pipes[0]);
         fclose($this->pipes[1]);
         fclose($this->pipes[2]);
         proc_close($this->process);
         if ($this->error) return FALSE;
         $this->error = 'Connection closed';
         break;
     case 'classic':
         pclose($this->sndml);
         $this->error = 'Connection closed';
         break;
     }
     return TRUE;
 }

 /**
 * Open pipe to Sendmail
 *
 * @param    string    Path with arguments
 * @return   boolean   TRUE on success, FALSE otherwise
 * @access   private
 */
 function _open($path)
 {
     switch ($this->pipemode) {
     case 'modern':
         $descriptors = array
             (0 => array('pipe', 'r') // stdin
             ,1 => array('pipe', 'w') // stdout
             ,2 => array('pipe', 'w') // stderr
             );
         $this->process = proc_open($path, $descriptors, $this->pipes);
         if (!is_resource($this->process)) {
             $this->error = 'Cold not open pipe to '.$path;
             while (!feof($this->pipes[2]) && $line = fgets($this->pipes[2], 4096)) {
                 $this->error .= $line;
             }
             return FALSE;
         }
         $this->sndml = &$this->pipes[0];
         break;
     case 'classic':
         $sndml = popen($path, 'w');
         if (!$sndml) {
             $this->error = 'Could not open pipe to '.$path;
             return FALSE;
         }
         $this->sndml = $sndml;
         break;
     }
     return TRUE;
 }

}
?>
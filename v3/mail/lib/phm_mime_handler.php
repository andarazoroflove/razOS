<?php
/* ------------------------------------------------------------------------- */
/* phm_mime_handler.php -> Map extension to MIME type and vice versa         */
/* (c) 2001-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Santiago                                                         */
/* v1.1.0php4                                                                */
/* ------------------------------------------------------------------------- */

/**
* This is an internal class for PHlyMail, converting various aspects of MIME
* types, filenames and encoding between each other.
* It requires an external file, holding a translation table.
*/
class phm_mime_handler {

    /**
    * The phm_mime_handler constructor method
    * @access    public
    * @param    string    Filing system location of the MIME table file
    * [@param    boolean    Global setting for Safe Mode; if set, all operations
    *                       will return something, even if no entry is found]
    */
    function phm_mime_handler($mimetable = false, $safemode = false)
    {
        $this->safemode = (isset($safemode) && $safemode) ? true : false;

        if (!is_readable($mimetable)) {
            $this->error = 'File name passed to me is not readable. Exitting';
            return false;
        } else {
            $this->_LoadTable($mimetable);
            return true;
        }
    }

    /**
    * Get MIME type for a given filename (DOS style - name.type)
    * @access    public
    * @param    string    file name to get MIME type for
    * [@param    boolean    SafeMode?, @see phm_mime_handler]
    * @return    array    0 => MIME type, 1 => human readable, English description
    */
    function get_type_from_name($filename = '', $safemode = -1)
    {
        if ($safemode == -1 && $this->safemode) $safemode = true;
        preg_match('/\.([^\.]+)$/i', $filename, $found);
        $suff = $found[1];
        foreach ($this->WP_MIME as $buffer) {
            if (strtolower($buffer['ext']) == strtolower($suff)) {
                return array($buffer['type'], $buffer['name']);
                break;
            }
        }
        return ($safemode) ? array('application/octet-stream', false) : array(false, false);
    }

    /**
    * Get extensiion (DOS style - name.type) for given MIME type
    * @access    public
    * @param    string    MIME type to get extension for
    * [@param    boolean    SafeMode?, @see phm_mime_handler; IGNORED]
    * @return    string    extension, if found, false otherwise
    */
    function get_extension_from_type($mimetype = '', $safemode = false)
    {
        foreach ($this->WP_MIME as $buffer) {
            if (strtolower($buffer['type']) == strtolower($mimetype)) {
                return $buffer['ext'];
            }
        }
        return false;
    }

    /**
    * Get typical mail encoding for given MIME type
    * @access    public
    * @param    string    MIME type to get encoding for
    * [@param    boolean    SafeMode?, @see phm_mime_handler]
    * @return    string    'q' for quoted-printable, 'b' for base64, false if not found;
    *                       will return 'b', if SafeMode is set and nothing found
    */
    function get_encoding_from_type($mimetype = '', $safemode = -1)
    {
        if ($safemode == -1 && $this->safemode) $safemode = true;
        foreach ($this->WP_MIME as $buffer) {
            if (strtolower($buffer['type']) == strtolower($mimetype)) {
                return $buffer['encoding'];
            }
        }
        return ($safemode) ? 'b' : false;
    }

    /**
    * Get human readable, English description for a given MIME type
    * @access    public
    * @param    string    MIME type to get description for
    * [@param    boolean    SafeMode?, @see phm_mime_handler]
    * @return    string    Description, if none found, false is returned; if
    *                      SafeMode is active, '' is returned
    */
    function get_typename_from_type($mimetype = '', $safemode = -1)
    {
        if ($safemode == -1 && $this->safemode) $safemode = true;
        foreach ($this->WP_MIME as $buffer) {
            if (strtolower($buffer['type']) == strtolower($mimetype)) {
                return $buffer['name'];
            }
        }
        return ($safemode) ? '' : false;
    }

    /**
    * Initialise the MIME translation table
    * @access    private
    * @param    string    filing system path to the MIME table file
    * @return    void
    */
    function _LoadTable($mimetable)
    {
        foreach (file($mimetable) as $buffer) {
            $buffer = trim($buffer);
            if (!$buffer) continue;
            if ($buffer{0} == '#') continue;
            $parts = explode(';;', $buffer);
            $this->WP_MIME[] = array
                   ('ext' => $parts[0]
                   ,'type' => $parts[1]
                   ,'encoding' => isset($parts[2]) ? $parts[2] : 'b'
                   ,'name' => (isset($parts[3])) ? $parts[3] : false
                   );
        }
    }
}
?>
<?php
/* ------------------------------------------------------------------------- */
/* lib/fxl_template.inc.php                                                  */
/* PHlyMail Common Branch, used with permission                              */
/* v1.2.0                                                                    */
/* ------------------------------------------------------------------------- */

// +----------------------------------------------------------------------+
// | FXL Template v 1.20dev - 2004-02-20                                  |
// +----------------------------------------------------------------------+
// | Copyright          Fever XL GbR Marlene Herr & Steffen Reinecke      |
// | (c) 2002 - 2004    Heinrich-Heine-Str. 14                            |
// |                    10179 Berlin                                      |
// |                    http://www.feverxl.de                             |
// +----------------------------------------------------------------------+
// | [License - english version - englische Version]                      |
// |                                                                      |
// | Feel free to include this library in your own non commercial         |
// | software product. If you like to use it in a commercial              |
// | environment, register this library FOR FREE via e-mail to            |
// | (sreinecke@feverxl.de).                                              |
// |                                                                      |
// | You may copy and distribute verbatim copies of the Library's         |
// | complete source code as you receive it, in any medium, provided that |
// | you keep this copyright notice on each copy.                         |
// |                                                                      |
// | You need a authorisation of the author (sreinecke@feverxl.de) to     |
// | distribute a modified version of this library.                       |
// |                                                                      |
// +----------------------------------------------------------------------+
// | [License - german version - deutsche Version]                        |
// |                                                                      |
// | Sie können diese Software Bibliothek kostenlos in Ihren nicht        |
// | kommerziellen Anwendungen nutzen. Sollten Sie diese Bibliothek im    |
// | kommerziellen Umfeld nutzen wollen, bedarf dieses einer kostenlosen  |
// | Registrierung via E-Mail (sreinecke@feverxl.de).                     |
// |                                                                      |
// | Es ist Ihnen gestattet unlimitiert viele Kopien dieser Bibliothek,   |
// | über Medien Ihrer Wahl, zu verbreiten / vertreiben, solange keine    |
// | Änderungen an dieser Bibliothek und der Copyright Notiz gemacht      |
// | wurden.                                                              |
// |                                                                      |
// | Sollte Interesse daran bestehen diese Bibliothek modfiziert zu       |
// | verbreiten, ist bei dem Author (sreinecke@feverxl.de) eine           |
// | Genehmigung einzuholen.                                              |
// +----------------------------------------------------------------------+
// | Author: Steffen Reinecke <sreinecke@feverxl.de>                      |
// +----------------------------------------------------------------------+
//

/**
*  FXL Template
*
*  The FXL TEMPLATE class allows you to keep your HTML code in some external files
*  which are completely free of PHP code, but contain replacement fields.
*
*  Main features of the FXL TEMPLATE are unlimited nested blocks and file includes.
*  With some upcoming extensions you will also be able to parse other formats than
*  FXL TEMPLATE format.
*
* @author   Steffen Reinecke <sreinecke@feverxl.de>
* @access   public
* @package  FXL_Template
* @version  v1.20 (2004-02-23)
* @copyright fever XL GbR Marlene Herr & Steffen Reinecke (show fxl_template.inc.php)
*/

class fxl_template {

 // START CONFIG AREA

 /**
 * First character of a variable placeholder
 * @var      string
 * @access   public
 * @see      set_parameter()
 */

 var $clipleft="{";

 /**
 * Last character of a variable placeholder
 * @var      string
 * @access   public
 * @see      set_parameter()
 */

 var $clipright="}";

 /**
 * Halt on Error (not fully implemented yet)
 * @var      boolean
 * @access   public
 * @see      set_parameter()
 */

 var $halt_on_error=true;

 /**
 * deletes spaces, carriage returns and newlines around the block (0=no,1=ungreedy,2=greedy)
 *
 * @var      string
 * @access   public
 * @see      set_parameter()
 */

 var $trim_block=0;

 /**
 * extended mode for includes
 *
 * @var      boolean
 * @access   public
 * @see      set_parameter()
 */

 var $extended=false;

 // END CONFIG AREA

 /**
 * the template
 *
 * @var      string
 * @access   private
 */

 var $template;

/**
 * The fxl_template constructor function.
 *
 * @param    string    Filename
 */

 function fxl_template($content='',$mode='file')
 {
     $this->place=array();
     switch($mode) {
     case 'file':
         if(file_exists($content) && is_readable($content)) $this->_loadfile($content);
         elseif($this->halt_on_error) die("Cannot open Templatefile: $content!");
         break;
     case 'string':
         $this->_parse($content);
         break;
     }
 }

 /**
  * Sets a new option value. Available options and values:
  * [clipleft - left delimiter (string)]
  * [clipright - right delimiter (string)]
  * [halt_on_error - halt_on_error (false,true) //work in progress]
  * [trim_block - trim blocks (0,1,2)]
  *
  * @param    string    fxl template option
  * @param    string    new value
  * @return   boolean
  * @access   public
  */

 function set_parameter($k, $v)
 {
     switch($k) {
     case 'clipleft': $this->clipleft = $v; return true;
     case 'clipright': $this->clipright = $v; return true;
     case 'halt_on_error': $this->halt_on_error = $v; return true;
     case 'trim_block': $this->trim_block = $v; return true;
     }
     if($this->halt_on_error) die("There is no parameter: $k!");
     else return false;
 }

 /**
  * assigns a block without any replacements
  *
  * DEPRECATED
  * speed up parsing: use of 'assign' instead of 'get_block'
  *
  * @param    string     block name
  * @return   string     block content
  * @access   public
  */

 function assign_block($blockname) { return $this->assign($blockname, $this->getblock($blockname)); }

 /**
  * loads a file into a block
  *
  * @param    string     block name
  * @param    string     filename
  * @access   public
  */

 function loadfile_into_block($blockname, $filename)
 {
     $ctc = get_class($this);
     $t = new $ctc($filename);
     $this->block[$blockname] = $t;
     $this->block[$blockname]->_parse(($this->trim_block) ? trim($t->template) : $t->template);
     $t->template = $this->_parse_block($blockname, $t->template);
 }

 /**
  * Checks whether a block exists
  *
  * @param    string     block name
  * @return   boolean    false on failure, otherwise true
  * @access   public
  */

 function block_exists($blockname) { return (isset($this->block[$blockname])) ? true : false; }

 /**
  * Returns the content of the block
  *
  * @param    string     block name
  * @return   string     block content
  * @access   public
  */


 function get_block($blockname)
 {
     if(isset($this->block[$blockname])) return $this->block[$blockname];
     elseif($this->halt_on_error) die("Block: $blockname not found!");
     else return false;
 }

 /**
  * Alias for get_block
  *
  * DEPRECATED (renamed to get_block)
  *
  * @param    string     block name
  * @return   string     block content
  * @access   public
  */

 function getblock($blockname)
 {
     return $this->get_block($blockname);
 }


 /**
  * Sets a value.
  *
  * The function can be used eighter like assign("varname","value")
  * or with one array $variables["varname"]="value" given assign($variables)
  *
  * @param    mixed     string with the variable name or an array %variables["varname"] = "value"
  * @param    string    value of the variable or empty if $variable is an array.
  * @access   public
  * @see      reassign()
  */

 function assign($var, $val = '')
 {
     if(is_array($var)) foreach($var as $k => $v) $this->place[$k][] = $v;
     elseif(is_string($var)) $this->place[$var][] = $val;
     elseif($this->halt_on_error) die("ASSIGN: Cannot assign '$var'");
 }

 /**
  * Sets a value after resetting the replacement list of the varname
  *
  * DEPRECATED (maybe not available in v2.0)
  *
  * The function can be used eighter like assign("varname","value")
  * or with one array $variables["varname"]="value" given assign($variables)
  *
  * @param    mixed     string with the variable name or an array %variables["varname"] = "value"
  * @param    string    value of the variable or empty if $variable is an array.
  * @access   public
  * @see      assign()
  */

 function reassign($var, $val = '')
 {
     if(is_array($var)) {
        foreach($var as $k => $v) {
            unset($this->place[$k]);
            $this->place[$k][] = $v;
        }
     }
     else {
        unset($this->place[$var]);
        $this->place[$var][] = $val; 
     }
 }

 /**
  * Returns the template string with all replacements done.
  *
  * @return   string template string
  * @access   public
  * @see      display()
  */

 function get()
 {
     if(count($this->place)) {
         foreach($this->place as $k=>$v) {
             $replace = '';
             for($i=0; $i<count($this->place[$k]); $i++) {
                 $replace.= (is_object($this->place[$k][$i])) ? $this->place[$k][$i]->get() : $this->place[$k][$i];
             }
             $this->template = ($this->trim_block == 2) ?
                               preg_replace('/[\n\r\s]*'.$this->clipleft.$k.$this->clipright.'[\n\r\s]*/',
                                            trim($replace),
                                            $this->template) :
                               str_replace($this->clipleft.$k.$this->clipright, $replace, $this->template);
         }
     }
     return $this->template;
 }

  /**
  * clears a place holder
  *
  * @return   string template string
  * @access   public
  * @see      get()
  */

 function clear() { $this->place = array(); }

  /**
  * Outputs the template string with all replacements done.
  *
  * @return   string template string
  * @access   public
  * @see      get()
  */

 function display()
 {
     echo $this->get_output();
 }

  /**
  * returns the parsed content
  *
  * @return   string template string
  * @access   public
  * @see      get()
  */

 function get_output()
 {
     return preg_replace('/'.$this->clipleft.'([a-z0-9\-\_]+)'.$this->clipright.'/ims', '', $this->get());
 }


  /**
  * load content
  *
  * @access   private
  * @param    string    filename
  */

 function _loadfile($filename = '')
 {
     $this->_parse(implode('', file($filename)));
 }

  /**
  * replaces blocks with place holders
  *
  * @access   private
  * @return   string string with block replacements done
  * @param    string  block name
  * @param    string  string to parse
  */

 function _parse_block($blockname = '', $template)
 {
     return preg_replace(($this->trim_block) ? '/[\s\r\n]+<!--[\s\r\n]+START[\s\r\n]+(' .$blockname. ')?[\s\r\n]+-->.*<!--[\s\r\n]+END[\s\r\n]+(' .$blockname. ')[\s\r\n]+-->[\s\r\n]+/ms' : '/<!--[\s\r\n]+START[\s\r\n]+(' .$blockname. ')?[\s\r\n]+-->.*<!--[\s\r\n]+END[\s\r\n]+(' .$blockname. ')[\s\r\n]+-->/ms', $this->clipleft.$blockname.$this->clipright, $template);
 }

  /**
  * matches blocks while parsing
  *
  * @access   private
  * @return   array matches
  */

 function _match_block()
 {
     preg_match_all('/<!--[\s\r\n]+START[\s\r\n]+(.*)?[\s\r\n]+-->(.*)<!--[\s\r\n]+END[\s\r\n]+(\\1)[\s\r\n]+-->/ms',
                    $this->template,
                    $m);
     return $m;
 }

  /**
  * parses a template string
  *
  * @access   private
  * @param    string  content to parse
  */

 function _parse($tplstring = '')
 {
     $this->template = $tplstring;
     $m = $this->_match_block();
     for($x=0; $x<count($m[0]); $x++) {
         //$ctc=get_class($this);
         $this->block[$m[1][$x]] = new fxl_template(FALSE, 'string');
         $this->block[$m[1][$x]]->_parse(($this->trim_block) ? trim($m[2][$x]) : $m[2][$x]);
         $this->template = $this->_parse_block($m[1][$x], $this->template);
     }
 }

}


// EXPERIMENTAL CACHE SUPPORT

class fxl_cached_template extends fxl_template {

    function fxl_cached_template($template, $cache, $checksum, $mode='auto')
    {
        $this->place = array();
        $ch_cache = (file_exists($cache) && is_readable($cache)) ? TRUE : FALSE;
        $ch_checksum = (file_exists($checksum) && is_readable($checksum)) ? TRUE : FALSE;
        $ch_template = (file_exists($template) && is_readable($template)) ? TRUE : FALSE;
        if(!$ch_template && $this->halt_on_error) die('Error opening template file: '.$template);
        $oldmd5 = '';
        $buffer = '';
        $fp = fopen($template,'r');
        while(!feof($fp)) $buffer.= fgets($fp,4096);
        fclose($fp);
        $newmd5 = md5($buffer);
        if($ch_checksum) {
            if(($fp = fopen($checksum,'r'))) {
                $oldmd5 = fgets($fp,33);
                fclose($fp);
            }
        }
        if($ch_cache && $oldmd5 == $newmd5) {
            $fp = fopen($cache,'r');
            $serialized = '';
            while(!feof($fp)) $serialized.= fgets($fp,4096);
            $this = unserialize($serialized);
            fclose($fp);
        }
        else {
            $this->_loadfile($template);
            $fp = fopen($cache,'w');
            fputs($fp,serialize($this));
            fclose($fp);
            $fp = fopen($checksum,'w');
            fputs($fp,$newmd5);
            fclose($fp);
        }
    }
}

?>
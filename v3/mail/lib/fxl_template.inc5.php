<?php
/* ------------------------------------------------------------------------- */
/* lib/fxl_template.inc5.php                                                 */
/* PHlyMail Common Branch, used with permission                              */
/* v2.0.0mod1                                                                */
/* ------------------------------------------------------------------------- */

# FXL TEMPLATE 2.0
#
# 14.03.2004 php5s object model changed again in Feb 2004 (__clone fix)
# 29.06.2004 MSO::Added alias to get_content()

##########################################
# Seralize Cache Plugin for fxl_template #
##########################################

class fxl_ser_template extends fxl_template
{
    protected $mode = '';
    protected $check = true;
    protected $force = false;
    protected $cache_suffix = '.cache';
    protected $cache_prefix = '';
    protected $cache_file = '';
    protected $template_file = '';
    protected $version = 0.9;
    protected $sub = false;

    public function __construct($template_file = '')
    {
        if ($template_file && !$this->set_template_file($template_file)) return false;
    }

    public function set_check($val)
    {
        $this->check = (bool) $val;
        return true;
    }

    public function set_mode($val)
    {
        if (in_array($val, array('md5'))) {
            $this->mode = $val;
            return true;
        }
        return false;
    }

    public function set_template($data, $type = 'file')
    {
        if ($type == 'file' && (bool) $this->tpl['template'] = file_get_contents($data)) {
            $this->template_file = $data;
            return true;
        } elseif ($type == 'string') {
            return (bool) $this->tpl['template'] = $data;
        }
        return false;
    }

    public function get_cache_file_name($template_file = '') {
        if ($template_file && ($this->cache_prefix || $this->cache_suffix)) {
            return $this->cache_prefix.$this->template_file.$this->cache_suffix;
        } elseif ($this->cache_file) {
            return $this->cache_file;
        } elseif ($this->template_file && ($this->cache_prefix || $this->cache_suffix)) {
            return $this->cache_prefix.$this->template_file.$this->cache_suffix;
        }
        return false;
    }

    public function set_cache_file($filename)
    {
        return (bool) $this->cache_file = $filename;
    }

    public function init()
    {
        if (!$this->tpl['template']) return false;
        if (!$cfile = $this->get_cache_file_name()) return false;

        if ($this->check && file_exists($cfile) && is_readable($cfile)) {
            $fp = fopen($cfile, 'r');
            $header_line = fgets($fp, 256);
            $header = explode(':', $header_line, 2);

            if (!isset($header[1]) || (chop($header[1]) != md5($this->tpl['template']))) $this->force = true;

            if (!$this->force) $ser = fread($fp, filesize($cfile) - strlen($header_line));
            fclose($fp);
        }
        elseif ($this->check && (!file_exists($cfile))) $this->force = true;

        if ($this->force) {
            $head = 'md5:'.md5($this->tpl['template'])."\n";
            $this->parse($this->tpl['template']);
            $cached = serialize($this);
            file_put_contents($cfile, $head.$cached);

        } else {
            $cached = unserialize($ser);
            $this->tpl = $cached->tpl;
        }
    }

    protected function md5_check($val1, $val2)
    {
        return ($val1 == md5($val2));
    }

    protected function __clone()
    {
        $this->cache_file = ''; // cannot be the same
        $this->template_file = ''; // cannot be the same
        $this->sub = true;
    }

    public function version()
    {
        return $this->version;
    }
}



###########################
# THE FXL TEMPLATE ENGINE #
###########################

class fxl_template
{
    protected $halt_on_error = true;
    protected $cache_plugin_dir = '';
    protected $tpl = array
            ('param' => array
                    ('clipleft' => '{'
                    ,'clipright' => '}'
                    ,'trim_block' => 0
                    )
            ,'block' => array()
            ,'place' => array()
            ,'template' => ''
            );

    # CONSTRUCTOR #
    public function __construct ($content = false)
    {
        if ($content) {
            $this->set_template($content, 'file');
            if ($this->tpl['template']) $this->init(file_get_contents($content), 'file');
        }
    }

    # PUBLIC METHODS #
    public function cache()
    {
        $params = func_get_args();
        $type = (count($params)) ? array_shift($params) : 'ser';
        $class = 'fxl_'.$type.'_template';
        if (!class_exists($class)) return false;
        return new $class($params);
    }

    public function set_template($data, $type = 'file')
    {
        if ($type == 'file') {
            if (($this->tpl['template'] = file_get_contents($data))) return true;
        } elseif($type == 'string') {
            $this->tpl['template'] = $string;
            return true;
        }
        return false;
    }

    public function set_template_cache($cache_file = false, $checksum_file = false)
    {
        $this->cache = true;
        $this->cache_file = $cache_file;
        $this->checksum_file = $checksum_file;
        return true;
    }

    public function init()
    {
        return $this->parse($this->tpl['template']);
    }

    public function get_content()
    {
        return preg_replace
                ('/'.$this->tpl['param']['clipleft'].'([a-z0-9\-\_]+)'.$this->tpl['param']['clipright'].'/ims'
                ,''
                , $this->get()
                );
    }

    public function display()
    {
        echo $this->get_content();
    }

    /**
    * returns the parsed content
    *
    * @return   string template string
    * @access   public
    * @see      get()
    */
    public function get_output()
    {
        return $this->get_content();
    }

    public function assign($var, $val = '')
    {
        if (is_array($var)) foreach($var as $k => $v) $this->tpl['place'][$k][] = $v;
        elseif (is_object($val)) $this->tpl['place'][$var][] = clone $val;
        elseif ($var) $this->tpl['place'][$var][] = $val;
    }

    public function get_block($blockname)
    {
        if (isset($this->tpl['block'][$blockname])
                && is_object($this->tpl['block'][$blockname])) {
            return clone $this->tpl['block'][$blockname];
        } elseif ($this->halt_on_error) {
            die("Block: $blockname not found!");
        }
        return false;
    }


    public function assign_block($str)
    {
        $bla = $this->get_block($str);
        $this->assign($str, $bla);
    }


    public function clear()
    {
        $this->tpl['place'] = array();
    }

    # INTERNAL METHODS #
    protected function parse($tplstring = '')
    {
        $this->tpl['template'] = $tplstring;
        $m = $this->_match_block();
        for ($x = 0; $x < count($m[0]); $x++) {
            $this->tpl['template'] = $this->parse_block($m[1][$x], $this->tpl['template']);
            $this->tpl['block'][$m[1][$x]] = clone $this;
            $this->tpl['block'][$m[1][$x]]->tpl['place'] = array();
            $this->tpl['block'][$m[1][$x]]->tpl['block'] = array();
            $this->tpl['block'][$m[1][$x]]->parse(($this->tpl['param']['trim_block']) ? trim($m[2][$x]) : $m[2][$x]);

        }
    }

    protected function parse_block($blockname = '', $template = '')
    {
        return preg_replace
                (($this->tpl['param']['trim_block'])
                        ? "/[\s\r\n]+<!--[\s\r\n]+START[\s\r\n]+("
                          .$blockname
                          .")?[\s\r\n]+-->.*<!--[\s\r\n]+END[\s\r\n]+("
                          .$blockname
                          .")[\s\r\n]+-->[\s\r\n]+/ms"
                        : "/<!--[\s\r\n]+START[\s\r\n]+("
                          .$blockname
                          .")?[\s\r\n]+-->.*<!--[\s\r\n]+END[\s\r\n]+("
                          .$blockname
                          .")[\s\r\n]+-->/ms"
                ,$this->tpl['param']['clipleft'].$blockname.$this->tpl['param']['clipright']
                ,$template
                );
    }

    protected function _match_block()
    {
        preg_match_all
                ("/<!--[\s\r\n]+START[\s\r\n]+(.*)?[\s\r\n]+-->(.*)<!--[\s\r\n]+END[\s\r\n]+(\\1)[\s\r\n]+-->/ms"
                ,$this->tpl['template']
                ,$m
                );
        return $m;
    }

    public function get()
    {
        if (count($this->tpl['place'])) {
            foreach ($this->tpl['place'] as $k => $v) {
                $replace = '';
                for ($i = 0; $i < count($this->tpl['place'][$k]); $i++) {
                    $replace .= (is_object($this->tpl['place'][$k][$i]))
                              ? $this->tpl['place'][$k][$i]->get()
                              : $this->tpl['place'][$k][$i];
                }
                $this->tpl['template'] = ($this->tpl['param']['trim_block'] == 2)
                        ? preg_replace
                                ("/[\n\r\s]*".$this->tpl['param']['clipleft']
                                        .$k.$this->tpl['param']['clipright']."[\n\r\s]/"
                                ,trim($replace)
                                ,$this->tpl['template']
                                )
                        : str_replace
                                ($this->tpl['param']['clipleft'].$k.$this->tpl['param']['clipright']
                                ,$replace
                                ,$this->tpl['template']);
            }
        }
        return $this->tpl['template'];
    }

    public function getblock($block)
    {
        return $this->get_block($block);
    }
}

# Example for using cache the simple way
class fxl_cached_template extends fxl_ser_template
{
    function __construct($tpl, $ctpl)
    {
        $this->set_template($tpl);
        $this->set_cache_file($ctpl);
        $this->set_mode('ser');
        $this->init();
    }
}
?>
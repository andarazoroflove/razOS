<?PHP
/*                              eyeOS project
                     Internet Based Operating System
                               Version 0.9
                     www.eyeOS.org - www.eyeOS.info
       -----------------------------------------------------------------
                  Pau Garcia-Mila Pujol - Hans B. Pufal
       -----------------------------------------------------------------
          eyeOS is released under the GNU General Public License - GPL
               provided with this release in DOCS/gpl-license.txt
                   or via web at www.gnu.org/licenses/gpl.txt

         Copyright 2005-2006 Pau Garcia-Mila Pujol (team@eyeos.org)

          To help continued development please consider a donation at
            http://sourceforge.net/donate/index.php?group_id=145027         */
            
##----------------------------------------------------------------- appIcon ---
function appIcon ($app, $dir='', $test=false) { 
  if (empty ($dir)) {
    $dir = dirname ($app).'/';
    $app = basename ($app);
  }
      
  $appicons = findGraphic ('I', array (APP_ICON, APP_RUNICON), $appdir = "$dir$app/");

  if (empty ($app) || ($dir{0} == '.') || !is_dir ($appdir) || empty ($appicons[0]))
    return false;
  if (!$test)
    showAppIcon ($imgsrc = $appicons[(isset ($_SESSION['apps'][basename($app)]) && !empty($appicons[1])) ?  1 : 0], $app);
	 
  return true;
} ##--------------------------------------------------------------- appIcon ---


##------------------------------------------------------------- showAppIcon ---
function showAppIcon ($imgsrc, $alt) {

  $alt = basename($alt);
  if (substr($namewe = $alt, -7) == ".eyeapp")
    $namewe = substr($namewe, 0, -7);

  echo "<a class='AppIcon' href='?a=$alt' onMouseover=\"document.getElementById('eyeTitles').innerHTML='$namewe'; this.className='AppIconOn'\" onMouseout=\"document.getElementById('eyeTitles').innerHTML=''; this.className='AppIcon'\">";
  if (defined ('BROWSER_IE'))
    echo "
    <DIV class='APPICON IE_$alt' STYLE=\"
      display:inline;
	    width:63px; 
      height:48px;
      filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src='$imgsrc',sizingMethod='scale'); \">
    </DIV>";
    else     
      echo "<img class='APPICON $alt' border='0' alt='$alt' src='$imgsrc' />";
   echo "</a>\n";
} ##----------------------------------------------------------- showAppIcon ---


##----------------------------------------------------------------- makeApp ---
function makeApp ($app, $argv, $install=true) {
   if (isset ($_SESSION['apps'][$app])) {
      if ($install) {	   
         $_SESSION['apps'][$app]['argv'] = $argv;
         $_SESSION['apps'][$app]['window.zindex'] = 10000;
      }
      return true;	    
   }

   if ((strtolower (substr ($app, -4)) == '.app') && (false !== ($appinfo = parse_info ($app)))) {
     if (!isset ($appinfo['app']))
         return false;
	 
      $dir = rtrim ($appinfo['app'], "/\\").'/';
      $appinfo['appinfo'] = $app;
      $app = basename ($app);
      
   } elseif (is_dir ($dir = rtrim ($app, "/\\").'/') && (false !== ($appinfo = parse_info ($dir.APP_INFO)))) {
      $app = basename ($dir);
      $appinfo['appinfo'] = $dir.APP_INFO;
      
   } else
      return false;
   
   if (!is_dir ($dir) || !is_file ($dir.APP_CODE))
      return false;

   if (false !== ($usrappinfo = parse_info (USRDIR.USR."/$app.xml")))
      $appinfo = array_merge ($appinfo, $usrappinfo);   

   $appinfo = array_merge (array (
      'skin' => APPSKIN,
      'syscall' => '',
      'title' => $app,
      'window.x_pos' => -1, 
      'window.y_pos' => -1, 
      'window.zindex' => 100000,
      'window.height' => WINDOW_HEIGHT,
      'window.width' => WINDOW_WIDTH), $appinfo);
	    
   if (count ($wp = @explode (',', $appinfo["state.window"])) == 4) {
      $appinfo['window.width'] = $wp[0];
      $appinfo['window.height'] = $wp[1];
      $appinfo['window.x_pos'] = $wp[2];
      $appinfo['window.y_pos'] = $wp[3];
   }
   else if (empty ($appinfo['window.static']))
      $appinfo["state.window"] = ' ';
   
   $appinfo['skin'] = $dir.rtrim ($appinfo['skin'], "/\\").'/';
   $appinfo['appdir'] = $dir;
   $appinfo['apptime'] = time ();
   $appinfo['argv'] = $argv;

   $appinfo['skins'] = array ();
   if ($adir = opendir ($dir)) {
      while ($skin = readdir ($adir))
      if (($skin{0} != '.') && is_dir ("$dir$skin")  && is_file ("$dir$skin/$app.css"))
	    $appinfo['skins'][] = $skin;
      closedir ($adir);
   } else
   
   $appinfo['scriptfiles'] = array();
   $appinfo['cssfiles'] = array ();
   foreach (array ($dir, $appinfo['skin']) as $sdir) {	       
      if ($adir = @opendir ($sdir)) {
         while ($f = readdir ($adir))
            if (strtolower (substr ($f, -3)) == '.js')
               $appinfo['scriptfiles'][] = $sdir.$f;
            elseif (strtolower (substr ($f, -4)) == '.css')
               $appinfo['cssfiles'][] = $sdir.$f;
         closedir ($adir);
      }
   }
   
   if (!$install)
      return $appinfo;
      
   $_SESSION['apps'][$app] = $appinfo;   
   return true;
}  ##-------------------------------------------------------------- makeApp ---


##---------------------------------------------------------------- closeApp ---
function closeApp ($app) {
  
//error_log ("Trace : closeApp ($app)");
  
  if (!isset ($_SESSION['apps'][$app])) {
    error_log ("Error closeApp ($app) : app is not running");
    return;
  }
  
	if (isset ($_SESSION['apps'][$app]['state.window']))
    $_SESSION['apps'][$app]['state.window'] = implode (',', array (
      $_SESSION['apps'][$app]['window.width'],
      $_SESSION['apps'][$app]['window.height'],
      $_SESSION['apps'][$app]['window.x_pos'],
      $_SESSION['apps'][$app]['window.y_pos']));

	if (!empty ($_SESSION['apps'][$app]['wrapup']))
	  @eval ($_SESSION['apps'][$app]['wrapup']);

  $appstate = array ();
  foreach ($_SESSION['apps'][$app] as $k => $v)
    if (0 === strpos (strtolower($k), 'state.')) {
      $k{5} = '/';   
      $appstate[$k] = $v;
    }
	    
  if (count ($appstate)) {
    $appstate['state'] = '';
    ksort ($appstate);
    parse_update (USRDIR.USR."/$app.xml", $appstate, '', $app);
  }
  unset ($_SESSION['apps'][$app]);
} ##--------------------------------------------------------------- closeApp ---


##--------------------------------------------------------------------- win ---
function win ($nom, $appinfo) {
   static $start = WINDOW_START;
   static $zindex = 99;
   include CONFIG;

   global $appLanguage, $eyeApp;
   $eyeApp = $nom;
   
   $_SESSION['apps'][$nom]['window.zindex'] = ++$zindex;	 

   if (is_dir(SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"])) $themeurl = SYSDIR.'themes/'.$_SESSION['usrinfo']["theme"]."/";
   else $themeurl = THEMESDIR.$_SESSION['usrinfo']["theme"]."/";

   if (!empty ($appinfo['langfile']) && ($t = file_get_contents (filename ($appinfo['langfile']))) &&
      (false !== ($t1 = strpos ($t, '#//'))) && (false !== ($t2 = strpos ($t, '//#', $t1)))) {
      @eval ('$appLanguage = array ('.substr ($t, $t1, $t2-$t1-1).');');
      if (isset ($appLanguage) && count ($appLanguage)) {
         $appLanguage = (isset ($appLanguage[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG])) ?
         $appLanguage[$select] : array ();
         $appLanguage = str_replace (array ("'", '"'), array ("\\'", "\\\""), $appLanguage); 
      }
   } else
      @include $appinfo['appdir'].LANGFILE;	    

   global $actionBar;
   $actionBar = array ();
   
   ob_start ();
   if (!empty ($appinfo['content'])) 
      echo $appinfo['content'];
   else {   
      $appfunction = null;
      include $appinfo['appdir'].APP_CODE;
      if (!empty ($appfunction)) {
         if (! empty ($appinfo['preludecode']))
           @eval ($appinfo['preludecode']); 
         $appinfo['exit'] = call_user_func ($appfunction, $nom, $appinfo);
      }
   }

   if (!empty ($appinfo['exit'])) {
      ob_end_clean ();
      return false;
   }
      
   $appHTML = ob_get_contents ();
   ob_end_clean ();

   $edit = false;  
   foreach ($appinfo as $k => $v) {
     $k = explode ('.', $k);
     if ($edit = ($k[0] == 'param' && count ($k) == 3 && $k[2] == 'args'))
       break;
   }

   if (defined ('BROWSER_IE'))
      $appHTML = "<div>$appHTML</div>";
   
   $appinfo = $_SESSION['apps'][$eyeapp];
   if ((($xpos = $appinfo['window.x_pos']) == -1) || (($ypos = $appinfo['window.y_pos']) == -1))
      $xpos = $ypos = $start += WINDOW_INC;
   
   echo "<!--  eyeOS ".(empty ($appinfo['window.panel']) ? 'WINDOW' : 'PANEL')." : '$nom' Title :'${appinfo['title']}'  -->
      <div id='$nom' class='eyeapp' 
        style='
        position:absolute;
        overflow:hidden;
	      width:${appinfo['window.width']}px; 
	      height:${appinfo['window.height']}px;
	      left:${xpos}px;
	      top:${ypos}px;
	      text-align: left;
	      z-index: $zindex;";
	 
   if (!empty ($appinfo['window.background']))
      echo "
	 background: url(".filename ($appinfo['skin'].$appinfo['window.background'])."); background-repeat: no-repeat;";

   if (!empty ($appinfo['window.panel']))
      echo "
       	 overflow:hidden;'>
	 $appHTML";
   else {
      echo"
   	 overflow:auto;'
     onMouseDown=' if (topapp != this) {
	     topapp = this;    
	     this.style.zIndex = ++maxZ;
   	   sendWindowPos (this.id, xLeft (this), xTop (this), xWidth (this), xHeight (this), xZIndex(this));
	     dbgMessage (\"$nom raised to \"+ maxZ);
	   }'>
   <div id='${nom}DBar' 
	   style='
	     position: relative;
		   cursor: move;
		   margin:0px;
		   padding:0px;
		   color:#ccc;
		   height: 21px;
		   text-align: left; '>
	   <div class='bsupesq'></div>
	     <div class='bsupdre'></div>
	      <div id='${nom}DTop' class='bsupcen'>
	        <div align='right'>".
     	   (!isset ($appinfo['window.fullscreen']) ? 
           " <img 
             id='{$nom}MBtn' 
             style='cursor:pointer;' 
             border='0' 
             alt='"._L('Maximize window')."' 
             title='"._L('Maximize window')."'
             src='".$themeurl."gfxwin/max.png' />" : '').
	         " <img 
              border='0' 
              title='"._L('Close window')."'
              alt='"._L('Close window')."'
              src='".$themeurl."gfxwin/close.png'
              style='cursor:pointer;'
              onclick = 'closeApp (this)'
            />
	        </div>
	      </div>  
  	    <div class='captitol'>
	      "._L($appinfo['title']).
	      (empty ($appinfo['version']) ? '' : ' ' . $appinfo['version'])."
	    ".
		 ((!empty($appinfo['helpfile']) && (is_file($tr = $appinfo['appdir'].$appinfo['helpfile'].'.htm') || is_file($tr .= 'l')) && (($g = findGraphic('', 'btn/help.png')) || true)) ?
		  "&nbsp; <a href='?a=eyeHelp.app($nom,${appinfo['helpfile']})'>
		      <img border='0' alt='"._L('Help')."' title='"._L('Help')."' src='$g' />
		     </a>" : '').
         
		  (($edit && (($g = findGraphic('', 'btn/config.png')) || true)) ?
		    "&nbsp;<img border='0' style='cursor:pointer;' onclick='editParams (this)' alt='"._L('Config')."' title='"._L('Config')."' src='$g' />" : '') ."
        </div>
          </div>
          <div class='bdre'></div>

	  <div class='txt' id='${nom}txt'>
            <div class='interior'>
	  ";
	  
      if (!empty ($actionBar)) {	      
         echo "<div class='actionbar' style='text-align: center;'>
	<div class='barr'></div><div class='barl'></div>";
	    
         if (!empty ($actionBar['left']))
	    echo "<span style='float:left; padding-left:10px; padding-top:5px;'>${actionBar['left']}</span>"; 
	    
         if (!empty ($actionBar['right']))
	    echo "<span style='float:right;padding-right:10px; padding-top:5px;'>${actionBar['right']}</span>"; 

	 echo "<div style='padding-top:5px;'>&nbsp;".@$actionBar['center']."&nbsp</div>
	   </div>";
      }
      
      echo "	  
	      $appHTML
  	    </div>
	        ". ($edit? "<div class='eyeConfig' style='padding:5px 10px 5px 10px;position:absolute; left:10px; top:10px; display:none; ' ></div>" : '')."
          </div>
	  <div class='peu' id='${nom}DDow' >
	    <div class='binfesq'></div>
	    <div class='binfdre'></div>
	    <div class='binfcen'>";
      if (!isset ($appinfo['window.resize']))
         echo "
	      <div id='${nom}RBtn' align='right' class='botobaix' ></div>";
      echo "	      
	    </div>
	  </div>";
   }

   echo "	  
        </div>
      
      <script language='javascript'>
         Setup ('$nom');
	 maxZ = $zindex;
      </script>

<!-- ///////////////////// ".(empty ($appinfo['window.panel']) ? 'WINDOW' : 'PANEL')." END ///////////////////// -->";

   return true;
   } ##-------------------------------------------------------------- /win ---

##-------------------------------------------------------------------- cls ---
   function cls ($txt) {
      $txt = htmlspecialchars (stripslashes ($txt));
      $txt = str_replace ("'", '&acute;', $txt);
      $txt = trim ($txt);
      $txt = basename ($txt);
      return $txt;
   } ##-------------------------------------------------------------- /cls ---

##-------------------------------------------------------------------- msg ---
   function msg ($msg) {
      addActionBar(" <span style='color: #fa912a; font-weight: bold;'>"._L($msg)."</span>", 'right');

   } ##-------------------------------------------------------------- /msg ---


   $XMLreplace = array (
      array ('&lt;', '&gt;', '&quot;', '&apos;', '&amp;'), 
      array ('<',    '>',    '"',      "'",       '&')
   );

##------------------------------------------------------------- parse_info ---
function parse_info ($input, $langflag = true, $params=null) {
   global $XMLreplace;
   if (!is_array ($params)) $params = array ();
//   $xml = xml2array ('', is_file ($input) ? file_get_contents ($input) : $input, '.', '.', true);
   $xml = xmlParse ('', is_file ($input) ? file_get_contents ($input) : $input, 
      array_merge (array ('attrib'=>'.', 'tag'=>'.', 'discard' => true), $params));
   if (count ($xml) == 0) return false;
   
//   echo '<pre>'; print_r ($xml); echo '</pre>';

   $Info = array ();
   foreach ($xml as $k => $v) {
      if (($ks = strpos ($k = strtolower ($k), '.')) !== false) 
         $k = substr ($k, $ks+1);
	 
      $v = str_replace ($XMLreplace[0], $XMLreplace[1], trim ($v));
      if (strlen ($v) > 1) {
         if (($v{0} == substr($v, -1)) && (($v{0} == '"') || ($v{0} == "'")))
            $v = substr ($v, 1, sizeof ($v) -2);
         elseif ($langflag && ($v{0} == '$')) {
	    $v = trim (_L(substr ($v, 1)));
         }
      }
      
      $Info[$k] = $v;
   }

// echo '<pre>'; print_r ($Info); echo '</pre>';
   return $Info;
} ##----------------------------------------------------------- parse_info ---


##------------------------------------------------------------ XMLentities ---
function XMLentities ($str, $replace = null) {
   if (!is_array ($replace)) {
      global $XMLreplace;
      $replace = array (array_reverse ($XMLreplace[1]), array_reverse ($XMLreplace[0]));
   }      
   return str_replace ($replace[0], $replace[1], $str);	
} ##---------------------------------------------------------- XMLentities ---


##----------------------------------------------------------- parse_update ---
function parse_update ($file, $item, $value = '', $autocreate='', $replace = null) {
	
// echo "parse_update ($file, $item, $value, $autocreate)<br />";
   
   if (!is_file ($file)) {
      if (empty ($autocreate) || ! preg_match ('/^[a-z][a-z_0-9]*/i', $autocreate, $autocreate))
         return false;

      $xml = array ($autocreate[0] => '');
   } elseif (empty ($item)) 
      return true;
   else   
      $xml = xmlParse ('', file_get_contents ($file), 
         array ('attrib'=>'.', 'tag'=>'/', 'discard' => false));

   if (isset ($replace) && !is_array ($replace)) {
      global $XMLreplace;
      $replace = array (array_reverse ($XMLreplace[1]), array_reverse ($XMLreplace[0]));
   }
	 
   reset ($xml);
   $root = each ($xml);
   $v = $root['value'];
   $root = $root['key'];
   if (false === ($i = strpos (str_replace('.', '/', $root), '/')))
      $xml = array ();
   else
      $root = substr ($root, 0, $i);
   
   if (!empty ($item) && !is_array ($item))
      $item = array ($item => $value);
      
   $changes = $needsort = false;   
   foreach ($item as $tag => $value) {
      if (is_array ($replace) && !empty ($value))
         $value = str_replace ($replace[0], $replace[1], $value);
	 
      $tag = (($tag{0} != '.') && ($tag{0} != '/')) ? "$root/$tag" : $root . $tag;
      if ($value == null) { 
         if (isset ($xml[$tag])) {
            unset ($xml[$tag]);
	    $changes = true;
	 }
      } elseif (isset ($xml[$tag])) {
         if ($xml[$tag] != $value) {	   
            $xml [$tag] = $value;
	    $changes = true;
	 }
      } else {   
         $xml [$tag] = $value;
	 $changes = $needsort = true;
      }
   }
   
   if (!$changes) return true;
   if ($needsort) ksort ($xml);   
      
//   echo '<pre>'; print_r($xml); echo '</pre>';
   
   ob_start ();
   echo "<?xml version='1.0'?>\n";
   $ctag = '';
   $indent = '';
   $open = '';
   foreach ($xml as $k => $v) {
      $dirname = $basename = $extension = '';	   
      extract (pathinfo ($k));
      if (!empty ($extension))
         $basename = substr ($basename, 0, strrpos ($basename, '.'));
      if ($dirname == '.') $dirname = '';
      
      // echo "\n$k ==> '$dirname' '$basename' '$extension' '$v' ---    ";     

      if ("/$dirname/$basename" != $ctag) {	 
         while ($ctag && (0 !== strpos ("/$dirname", $ctag))) {
	    $indent = substr ($indent,2);
	    if ($open) echo ">"; $open = '';
            echo "\n$indent</".basename ($ctag).'>';
	    if (dirname('/') == ($ctag = dirname ($ctag))) 
	       $ctag = '';
         }

	 if ($dirname) {
            $newtags = array ();
            $tname = '/'.$dirname;
            while (($tname != dirname('/')) && ($ctag != $tname)) {
               array_unshift ($newtags, basename ($tname));
	       $tname = dirname ($tname);
            }
      
            foreach ($newtags as $st) {
               $ctag .= '/'.$st;
	       echo "\n$indent<$st>";
               $indent .= '  ';
            }
	 }

	 if ($open) echo ">"; 
         $ctag .= '/'.$basename;
	 echo "\n$indent<$basename";
         $indent .= '  ';
	 $open = $basename;
      }
      
      if (!empty ($extension)) {
         echo ' ' . $extension . ($v ? "='$v'" : '');
      } else {
         if ($v != trim ($v)) $v = "'$v'";
         echo strlen ($v) ? ">$v</$basename>" : '/>';
	 $ctag = dirname ($ctag);
	 $indent = substr ($indent,2);
	 $open = '';
      }
   }
   
   while ($ctag && ($ctag != dirname ('/'))) {
      $indent = substr ($indent,2);
      echo "\n$indent</".basename ($ctag).'>';
      $ctag = dirname ($ctag); 
   }
   
   $fd = fopen ($file, 'w');
   fwrite ($fd, ob_get_contents());
   fclose ($fd);
   ob_end_clean ();
   
   return true;
} ##--------------------------------------------------------- parse_update ---

##--------------------------------------------------------------- xmlParse ---
function xmlParse ($xml, $params = null, $name = '')
{
   if (is_string ($xml) && ($xml == '')) {
      $args = func_get_args ();
      $xml = $args[1];
      $params = isset ($args[2]) ? $args[2] : null;
      $name = '';
   }

   static
      $A,           // Attribute separator string, defaults to '.' 
      $E,           // Nested element separator string, defaults to '/'
      $P,	    // Separator for processing instructions : < ? ...? > default empty
      $C,           // Comment separator
      $Discard,     // True to discard elements with no data, defaults to false
      $Debug,       // Debugging aid....
      $Strict,      // True for stricter scanning, defaults to false
      $Fmt,         // Set to 1 for linear, 2 for heirarchical return value
      $Ereplace;    // If set, defines string replacement in attributes and cdata

   if (empty ($name)) { // Initialisation call for new parse
      global $XMLreplace;
      $Ereplace = empty ($params['replace']) ? null : 
         ((is_array ($params['replace']) && count ($params['replace'] == 2)) ? $params['replace'] : $XMLreplace);
      $A = empty ($params['attrib']) ? '.' : $params['attrib']; 	   
      $E = empty ($params['tag']) ? '/' : $params['tag']; 	   
      $P = empty ($params['processing']) ? '' : $params['processing']; 	   
      $C = empty ($params['comment']) ? '' : $params['comment']; 	   
      $Discard = empty ($params['discard']) ? false : $params['discard'];
      $Debug = empty ($params['debug']) ? 0 : $params['debug'];
      if (1 == ($Fmt = (!empty ($params['tree']) ? 2 : 1)))
         static $Result;
      $Result = array ();
      $Strict = empty ($params['strict']) ? false : $params['strict'];
   }
   elseif ($Fmt == 1) 
      static $Result;
   else
      $Result = array ();   

   if ($Debug) echo "<br/> ". htmlentities ("xmlParse ('$xml', '$params' '$name')")."<br/>";
      
   $Attrib = array ();   
   $ReElements = '/^'.
      '(?:<!--(.*)-->)|'.
      '(?:<\?([a-zA-Z_[a-zA-Z_:0-9]*)\s+(.*?)\s*\?>)|'.
      '(?:(?:<([a-zA-Z_[a-zA-Z_:0-9]*)\s*(.*?)\s*(?:(?:\/>)|(?:>(.*?)<\/\\4)>)))$/s';
   $ReAttributes = '/^([a-zA-Z_][a-zA-Z_0-9]*)(=(?:("|\')(.*?)\\3)|([^\s]*))?$/';

   if (!$Strict) {
      $ReElements = str_replace (array ('/^', '$/'), '/', $ReElements);
      $ReAttributes = str_replace (array ('/^', '$/'), '/', $ReAttributes);
   }

   preg_match_all ($ReElements, $xml, $elements);

   if ($Debug) { echo '<pre>'; ob_start (); print_r ($elements); $qq = ob_get_contents ();
   ob_end_clean (); echo htmlentities ($qq).'</pre>'; }
   
   $ecount = array ();
   $efreq = array_count_values ($elements[4]);
   foreach ($elements[4] as $ie => $ename) {
      if (!empty ($elements[2][$ie]) && !empty ($P))
         $Result[(($Fmt == 1) ? $name : '').$P.$elements[2][$ie]] = 
	    is_array ($Filter) ? str_replace ($Filter[0], $Filetr[1], trim ($elements[3][$ie])) : trim ($elements[3][$ie]);
	 
      elseif (!empty ($elements[1][$ie]) && !empty ($C))
         $Result[(($Fmt == 1) ? $name : '').$C.$ie] = 
  	    is_array ($Filter) ? str_replace ($Filter[0], $Filetr[1], trim ($elements[1][$ie])) : trim ($elements[1][$ie]);

      if (empty ($ename))
	 continue;

      $nname = ($name ? $name.$E : '').$ename;
      if ($efreq[$ename] > 1) {	 
         if (!isset ($ecount[$ename])) $ecount[$ename] = 1;
         $nname .= '-'.$ecount[$ename]++; 	 
      }

      if (!empty ($A) && ($attributes = trim($elements[5][$ie]))) {
         preg_match_all ($ReAttributes, $attributes, $att);
         foreach ($att[1] as $ia => $aname) {
            $av = !empty ($att[2][$ia]) ? $att[empty ($att[3][$ia]) ? 5 : 4][$ia] : true;
	    $Attrib[(($Fmt == 1) ? $nname : '').$A.$aname] = (!is_array($Ereplace) || !is_string($av)) ? $av : 
	       str_replace ($Ereplace[0], $Ereplace[1], $av); 	  
	 }
      }

      if ($Fmt == 1)
         $Result = array_merge ($Result, $Attrib);
      else
         $Result[$E.$ename] = $Attrib;
	 
      if (($v = trim($elements[6][$ie])) && preg_match ($ReElements, $v)) {
         $v = xmlParse ($v, null, $nname);
	 if ($Fmt == 2)
            $Result[$E.$ename] = array_merge ($Result[$E.$ename], $v);
      } elseif (!empty ($E) && (!$Discard || (strlen ($v) > 0))) {
         $v = (!is_array($Ereplace) || !is_string($v)) ? $v : str_replace ($Ereplace[0], $Ereplace[1], $v);
         if ($Fmt == 1)
	    $Result[$nname] = $v;
	 else   
            $Result[$E.$ename] = array_merge ($Result[$E.$ename], array ($v));
      }
   }

   if (($name == '') || ($Fmt == 2))
      return $Result;
} ##------------------------------------------------------------- /xmlParse ---


##--------------------------------------------------------------- createXML ---
function createXML ($file, $root, $content = null, $replace = null) {
//  echo "createXML ($file, $root, $content, $replace)<br/>";
  
   if (is_file ($file))
      unlink ($file);
   parse_update ($file, $content, '', $root, $replace);
} ##------------------------------------------------------------ /createXML ---


##---------------------------------------------------------------------- _L ---
$corelanguage = 'english';

function _L ($s) {
   global $corelanguage;	
   // This routine will return a translated message string. It looks up the 
   // message string in the current language array and returns the translation
   // or the original message if no translation is availb
   // This means that all translation fiels do not need to be in synch. Any
   // messages which are not yet translated are simply rendered in english 
   // (or catalan?)
   
   if (isset ($_SESSION['lang']) && ($_SESSION['lang'] != $corelanguage)) {
      global $appLanguage, $Translations;		// Translation found here here
      if (($ts = @$appLanguage[$s]) || ($ts = @$Translations[$s])) 
         $s = $ts;
      elseif (DEBUG & 4096)
         $s = "<span style='background-color:red; color:white;'>$s</span>"; 
   }

   if (($na = func_num_args ()) <= 1)
      return $s;
   
   // In addition, the message can have parameters which are inserted into the 
   // message string using %# notation where # is a number 0-9. This allows a 
   // translator to change the order of parameters if that is better for the 
   // translation.
    
   $args = func_get_args ();
   if (($na == 2) && (is_array ($args[1])))
      $args = $args[1];
   else
      array_shift ($args);
      
   return @preg_replace ("/\%([0-9])/e", '$args[\\1]', $s);
} ##------------------------------------------------------------------- /_L ---


##------------------------------------------------------------------- dater ---
function dater ($time, $flags = 3) {
   global $sysdate, $systime;	

   if (empty($sysdate)) $sysdate = SYSFMT_DATE;
   if (empty($systime)) $systime = SYSFMT_TIME;
   
   if ($time == null) $time = time ();
   
   $elapsed = floor ((time () - $time + 30) / 60);
   
   if ($elapsed == 0)
     return _L ('less than a minute ago');
     
   if ($elapsed < 60)
      return _L ('%0 minutes ago', $elapsed);
      
   if ($elapsed < (2 * 60))
      return _L ('%0h %1m ago', floor ($elapsed / 60), $elapsed % 60); 
      
   $midnite = time() - eval ('return '.date ('(G*60+i)*60+s').';');
   $datestr = ($time >= $midnite) ? 'today' : 
         (($time >= ($midnite - (24 * 60 * 60))) ? 'yesterday' : 
	 (($elapsed < (7 * 24 * 60)) ? date ('l', $time) : 'on %0'));
	 
  switch ($flags) {
   case 1:
      return _L($datestr, date ($sysdate, $time));
      
   case 2:
      return _L('at %0', date ($systime, $time));
      
   default:
      return _L($datestr . ' at %1', date($sysdate, $time), date ($systime, $time));
   }   
} ##---------------------------------------------------------------- /dater ---


##---------------------------------------------------------------- filename ---
function filename ($fn) {
   global $eyeapp;
   $r1 = array ('~sys/', '~usr/', '~home/');
   $r2 = array (SYSDIR, USRDIR.USR.'/', HOMEDIR.USR.'/');

   if (!empty ($_SESSION['apps'][$eyeapp])) {
      $r1[] = '~app/';
      $r2[] = $_SESSION['apps'][$eyeapp]['appdir'];
   }
   return str_replace ($r1, $r2, $fn);
} ##------------------------------------------------------------- /filename ---

##----------------------------------------------------------------- makedir ---
function makedir ($d) {
   if (!is_dir (dirname ($d)))
      makedir (dirname ($d));
   if (!is_dir ($d))
      mkdir ($d, 0777);
} ##-------------------------------------------------------------- /makedir ---

##------------------------------------------------------------ macro_expand ---
function macro_expand ($str) {
   global $eyeapp, $sysdate, $systime;	

   if (is_array ($str)) $str = $str[1];
   
   $time = time ();
   if (empty($sysdate)) $sysdate = SYSFMT_DATE;
   if (empty($systime)) $systime = SYSFMT_TIME;
   
   $r1 = array ('~usr', '~date', '~time');
   $r2 = array (USR, date ($sysdate, $time), date ($systime, $time));
   
   if (isset ($eyeapp)) {
      $r1[] = '~app';
      $r2[] = $eyeapp;
   }
   
   return str_replace ($r1, $r2, $str);
} ##---------------------------------------------------------- macro_expand ---


##-------------------------------------------------------- macro_substitute ---
function macro_substitute ($str, $mi = MACRO_OPEN) {
   return preg_replace_callback ("`$mi(.*?)".strrev ($mi).'`', 'macro_expand', $str); 
} ##----------------------------------------------------- /macro_substitute ---


##------------------------------------------------------------- findGraphic ---
function findGraphic ($typ, $name, $eyeapp = null) {
   if (is_array ($name)) {
      $names = array ();	   
      foreach ($name as $n)
         $names[] = findGraphic ($typ, $n, $eyeapp);
      return $names;	 
   }

   $skin = @$_SESSION['sysinfo']['skin'];
   if ($eyeapp)
      if ((file_exists ($f = @$_SESSION['apps'][basename($eyeapp)]['skin'].$name)) ||
          (file_exists ($f = @"$eyeapp$skin$name")) ||
	  (file_exists ($f = @$eyeapp.APPSKIN.$name)) ||
          (($typ == 'I') && file_exists ($f = "$eyeapp$name"))) 
      return $f;
      
   if (file_exists ($f = @"$skin$name") ||
      (defined ('BROWSER_IE') && file_exists ($f = GFXDIR."IE/$name")) || 
      file_exists ($f = GFXDIR.$name))
      return $f;
      
   return '';   
}  ##--------------------------------------------------------- /findGraphic ---


##------------------------------------------------------------ addActionBar ---
function addActionBar ($str, $al='left') {
   global $actionBar;	
   if (isset ($actionBar[$al]))
      $actionBar[$al] .= $str;
   else   
      $actionBar[$al] = $str;
}  ##-------------------------------------------------------- /addActionBar ---


##---------------------------------------------------------------- get_size ---
function get_size ($path) {
  if(!is_dir($path)) 
    //Return size in MB, not in bytes.
    return filesize($path) / 1024 / 1024;
   
  if ($handle = opendir ($path)) {
    $size = 0;
    while (false !== ($file = readdir($handle)))
      if ($file != '.' && $file != '..')
        $size += get_size ($path."/".$file);
    closedir($handle);
    return $size;
  }
}
##--------------------------------------------------------------- /get_size ---

##--------------------------------------------------------- fopen_exclusive ---
function fopen_exclusive ($fn, $fm) { 
  $counter = 0;
  while ($counter++ < 6) {
    if ($fp = fopen ($fn, $fm)) {
      if (flock ($fp, LOCK_EX ))
        return $fp;
      fclose ($fp);
    }
    usleep (200);
  }
  return false;
}
##-------------------------------------------------------- /fopen_exclusive ---

##--------------------------------------------------------------- userinput ---
function userinput($t) {
	return htmlspecialchars($t, ENT_QUOTES);
}
##-------------------------------------------------------------- /userinput ---

?>

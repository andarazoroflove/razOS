<?php
function eyeInfo ($eyeapp, $appinfo) {
   if (!empty ($appinfo['argv']) && (strtolower ($appinfo['argv'][0]) == 'phpinfo')) {
      ob_start ();
      phpinfo (-1);
      $phpinfo = ob_get_contents ();
      ob_end_clean ();
      $phpinfo = substr ($phpinfo, strpos ($phpinfo, '<body>')+6);
      echo substr ($phpinfo, 0, strrpos ($phpinfo, '</body>'));
      return;	      
   }
      
   echo "<div align='center'><a href='?a=eyeNav.eyeapp(http://www.eyeos.org)'><img border='0' title='".OSVERSION." - "._L('Under GPL license')."' alt='".OSVERSION." - "._L('Under GPL license')."' src='".findGraphic ('', "logo.png")."'></a>
<br /><br />
<a href='?a=eyeNav.eyeapp(http://www.eyeos.org/index.php?section=Donate)'><img border='0' title='".OSVERSION." - "._L('Support eyeOS')."' alt='".OSVERSION." - "._L('Support eyeOS')."' src='".findGraphic ('', "support.png")."'></a>
<br /><br />
        <hr width='80%' />

  <h2>"._L('Core Team')."</h2>
  <table border='0' width='100%' align='center'>
    <tr><td>Pau Garcia-Milà</td><td>From Barcelona, Spain</td><td>Coder</td></tr>
    <tr><td>Marc Cercós</td><td>From Barcelona, Spain</td><td>Applications 2.0 Creative</td></tr>
    <tr><td>David Plaza</td><td>From Barcelona, Spain</td><td>Designer</td></tr>
    <tr><td>Hans B. Pufal</td><td>From Greenoble, France</td><td>Coder</td></tr>
    <tr><td>Eduardo Pérez Orue</td><td>From Bilbao, Spain</td><td>Business Developer</td></tr>
    <tr><td>Steven Mautone</td><td>From New York, USA</td><td>Business Developer</td></tr>
  </table><br /><a href='mailto:team@eyeos.org'>Contact</a><br /><br />
        <hr width='80%' />
<h2>"._L('Related Projects')."</h2>
<h3>eyeOS miniserver for Windows</h3>
<p>Tristan Siebers (trizz)</p>
<h3>Documentation Project</h3>
<p>David Bouley (Judland) from Sask., Canada</p>
<br />
        <hr width='80%' />
<h2>"._L('System info')."</h2>
";
	
      echo"
        <h3>"._L('User:')." <small>".USR."</small></h3>
        <h3>"._L('Active applications')."</h3>";
 	 
	$tapps = $_SESSION['apps'];
	foreach ($tapps as $app => $appinfo) {
	if (substr($app, -7) == ".eyeapp") { $app = substr($app, 0, -7); }
	if ($app == $appinfo['title'])
	   echo "$app<br/>";
	else 
	   echo "${appinfo['title']} : $app<br />"; }

      echo "<br /><hr width='80%' />
        <h3> PHP v. ". phpversion()."</h3>
	".((false !== strpos (OSVERSION, 'X')) ? "<a href='?a=eyeInfo.eyeapp(phpinfo,-1)'>PHPinfo</a>" :'');
	
      echo "
      <br/><blockquote>
      ".$_SERVER['HTTP_USER_AGENT'].(defined ('BROWSER_IE') ? ' : <strog>MS-IE</strong>' : '')."
      </blockquote></div><br />";
}

$appfunction = 'eyeInfo';
?>

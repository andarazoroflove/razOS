<?PHP
  if (!defined ('STATSDIR')) define ('STATSDIR', 'etc/stats/');

  function userStats ($usr) {
    while (!$slock = fopen (STATSDIR . 'slock')) {
      usleep (100);
      if (@$retries++ > 5) return;
    }
    
    $logins = array ();
    @include (($sfile = STATSDIR . date('Y-m-d')) . '.php');
    
    if (!isset ($logins[$hr = 'hour_' . date('H')]) && count ($logins > 1000)) {
      @$filecount++;
      rename ($sfile, "$sfile.$filecount.php");  
      $logins = array ();
    }
    
    @$logins[$hr]++; 
    @$logins['user_' . $usr]++; 
    $stats = '$hostname = ' . $_SESSION['sysinfo']['hostname'];
    foreach ($logins as $l => $v)
      $stats .= '$logins[\'' . $l . '\'] = ' . $v . ";\n";
    if (isset ($filecount)) stats.= "\$filecount = $filecount;\n";
    
    $sfile = fopen ("$sfile.php", 'w');
    fwrite ($sfile, "<?PHP\nglobal \$logins;\n$stats?>");
    fclose ($sfile);
    
    fclose ($slock);
  }

  userStats ($_SESSION['usr']);
?>


<?php

   if (!defined ('USR'))
      return;

  /** create directory **/
  $dirMapDir = USRDIR.USR."/Maps.eyeapp/";
  if (!is_dir($dirMapDir)) mkdir($dirMapDir, 0777);

  if($_GET['option'] == "delete") {
    $file = basename($_GET['file']) ;
    @unlink($dirMapDir.$file) ;
    $_GET['file'] =false ;
  }
  
  if($_GET['option'] == "search") {
    $v = (trim($_POST['v']))*1 ;
    $h = (trim($_POST['h']))*1 ;
  }
  if($_GET['option'] == "add") {
    echo 'add' ;
    $v = trim($_POST['v1']) ;
    $h = trim($_POST['h1']) ;
    $namePoint = trim($_POST['namePoint']) ;
    $fileName = date("U").".xml" ;
    createXML ($dirMapDir.$fileName, 'MapsPoints', array (
      'v' => $v,
      'h' => $h,
      'namePoint' => $namePoint), 1);     
  }
  if($_GET['option'] == "view") {
    $file = basename($_GET['file']) ;
    $info = parse_info($dirMapDir.$file) ;
    $v = $info['v'] ;
    $h = $info['h'] ;
    $namePoint = $info['namepoint'] ;
  }
  /** get list of points **/
  $dir = opendir($dirMapDir) ;
  $arrayPoints = array() ;
  $q = 0 ;
  while (($file = readdir($dir)) !== false) {
    if($file != "." && $file != "..") {
      $temp = parse_info($dirMapDir.$file) ;
      $arrayPoints[$q][file] = $file ;
      $arrayPoints[$q][namepoint] = $temp[namepoint] ;
      $arrayPoints[$q][point] = $temp[v].":".$temp[h] ;
      $q++ ;
    }
  }
  closedir($dir);  
  
  echo '<div style="position:absolute;top:3px;left:3px;right:3px;bottom:3px;">' ;
  echo "<div style='position:absolute;top:1px;left:1px;overflow:auto;right:190px;bottom:1px;border:1px solid #cacaca;text-align:center;'>" ;
  echo '<div style="position;absolute;top:0px;left:4px;right:194px;height:45px;border-bottom:1px solid #cacaca;text-align:left;margin-left:10px;margin-right:10px;">' ;
  if($v != 0 && $h != 0) {
    echo '<a href="#" onClick="myswitch(\'win1\');"><img src="'.$appinfo['appdir'].'Images/maps.button.02.png" border="0" style="margin:0 10px 0 10px;"/></a>' ;
  } else {
    echo '<img src="'.$appinfo['appdir'].'Images/maps.yellow.empty.png" border="0" style="margin:0 10px 0 10px;"/>' ;
  }
  if($_GET['file']) {
    echo '<a href="desktop.php?a=Maps.eyeapp&option=delete&file='.$_GET['file'].'"><img src="'.$appinfo['appdir'].'Images/maps.button.01.png" border="0" style="margin:0 10px 0 10px;"/></a>' ;
  } else {
    echo '<img src="'.$appinfo['appdir'].'Images/maps.green.folder.png" border="0" style="margin:0 10px 0 10px;"/>' ;
  }
  echo '<a href="#" onClick="document.formMaps.v.focus();"><img src="'.$appinfo['appdir'].'Images/maps.green.search.png" border="0" style="margin:0 10px 0 10px;"/></a>' ;
  echo '</div>' ;
  echo "<iframe src=\"http://www.eyeos.net/Maps/mapa.php/?v=".$v."&h=".$h."\" style=\"width: 520px; height: 350px\" frameborder=\"0\"></iframe>" ;
  echo "</div>";
  
  /** menu **/
  echo '<form method="post" action="desktop.php?a=Maps.eyeapp&option=search" name="formMaps" >' ;
  echo '<div style="position:absolute;top:1px;right:1px;width:180px;border:1px solid #cacaca;font-size:12px;padding:2px;height:120px;">' ;
  echo _L('Longitude').':<br/><input type="text" name="v" style="width:170px;font-size:11px;" value="'.$v.'" />' ;
  echo _L('Latitude').':<br/><input type="text" name="h" style="width:170px;font-size:11px;" value="'.$h.'" />' ;
  echo '<p style="margin:10px;padding:0px;text-align:center;"><input type="submit" value="'._L('Search').'" style="width:100px;"></p>' ;
  echo '</div>' ;
  echo '</form>' ;
  
  /** bookmarks **/
  echo '<div id="bookmarks" style="position:absolute;top:135px;right:1px;width:180px;border:1px solid #cacaca;padding:2px;height:280px;">' ;
  echo '<h1>'._L('Bookmarks').'</h1>' ;
  echo '<div style="overflow:auto;height:260px;background:url('.$appinfo['appdir'].'Images/bookmarks.background.png) no-repeat center center;">' ;
  for($i=0;$i<count($arrayPoints);$i++) {
    if($arrayPoints[$i][point] == $v.":".$h)
      echo '<p style="margin:2px;padding:1px;border:1px solid #cacaca;background:url('.$appinfo['appdir'].'Images/back_trans2.png);color:#000;"><a href="desktop.php?a=Maps.eyeapp&option=view&file='.$arrayPoints[$i][file].'" style="display:block;">'.$arrayPoints[$i][namepoint].'</a></p>' ;
    else
      echo '<p style="margin:2px;padding:1px;border:1px solid #cacaca;background:url('.$appinfo['appdir'].'Images/back_trans1.png);color:#000;"><a href="desktop.php?a=Maps.eyeapp&option=view&file='.$arrayPoints[$i][file].'" style="display:block;">'.$arrayPoints[$i][namepoint].'</a></p>' ;
  }
  echo '</div>' ;
  echo '</div>' ;

  /** form add bookmarks **/
  echo '<form method="post" action="desktop.php?a=Maps.eyeapp&option=add">' ;
  echo '<input type="hidden" name="v1" value="'.$v.'" />' ;
  echo '<input type="hidden" name="h1" value="'.$h.'" />' ;
  echo '<div id="win1" style="display:none;position:absolute;top:50px;left:50px;width:400px;height:250px;border:1px solid #daaaca;background-color:#e0e0e0;">' ;
  echo '<p style="position:absolute;top:0px;left:0px;right:0px;height:25px;background-color:#d0d0d0;border-bottom:#cacaca;margin:0px;padding:0px;text-align:right;"><a href="#" onClick="myswitch(\'win1\');"><img src="'.$appinfo['appdir'].'Images/close.png" border="0" style="margin:3px;"/></a></p>' ;
  echo '<div style="position:absolute;top:50px;">' ;
  echo '<table border="0" width="90%" align="center">' ;
  echo '<tr><td style="text-align:right;">'._L('Longitude').':</td><td>'.$v.'</td></tr>' ;
  echo '<tr><td style="text-align:right;">'._L('Latitude').':</td><td>'.$h.'</td></tr>' ;
  echo '<tr><td style="text-align:right;">'._L('Name point').':</td><td><input type="text" name="namePoint" /></td></tr>' ;
  echo '<tr><td colspan="2" align="center"><input type="submit" value="'._L('Add').'" style="width:100px;border:1px solid #000;margin-top:50px;"></td></tr>' ;
  echo '</table></div>' ;
  echo '</div>' ;
  echo '</form>' ;
  
  echo '</div>' ;
?>

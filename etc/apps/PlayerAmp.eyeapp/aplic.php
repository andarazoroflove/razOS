<?php
if (!defined ('USR'))
  return;

/** create play list **/
class Music {
  var $homeDir ;
  function Music() {
    $this->homeDir = HOMEDIR . USR ;
  }
  
  function searchMusic() {
    $list = array() ;
     
    function search($dir) {
      global $list ;
      $uchwyt = opendir($dir) ;
      $nazwa = readdir($uchwyt) ;
      while($nazwa) {
        if(is_dir($dir."/".$nazwa)) {
          if($nazwa != "." && $nazwa != "..")
            search($dir."/".$nazwa) ;
          } else {
            $tempNazwa = strtolower($nazwa) ;
            if(ereg(".mp3$",$tempNazwa) || ereg(".ogg$",$tempNazwa)) {
              $i = count($list) ;
              $list[$i]->file = $nazwa ;
              $list[$i]->directory = $dir ;
            }
          }
        $nazwa = @readdir($uchwyt) ;
      }
      return $list;
    } /** end search() **/
    $listFile = array() ;
    $listFile = search($this->homeDir) ;
    return $listFile ;
  }
}

$musicPlayer = new Music ;
$files = $musicPlayer->searchMusic() ; 
$playList = fopen(HOMEDIR . USR ."/playlist.m3u","w+") ;

if($playList) {
  for($i=0;$i<count($files);$i++) {
    fputs($playList,"#EXTINF:-1,".$files[$i]->file."\n") ;
    fputs($playList,"http://".getenv(server_name)."/". $files[$i]->directory . "/" . $files[$i]->file . "\n") ;
  }
  fclose($playList) ;
}
  echo "<div align=\"center\"> <iframe src=\"${appinfo['appdir']}playerapplet.php\" style=\"width: 280px; height: 420px\" frameborder=\"0\"></iframe> </div>"; 
?>

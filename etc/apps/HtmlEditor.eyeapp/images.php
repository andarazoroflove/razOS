<?php
session_start() ;
$usr = $_SESSION['usr'] ;
$home = "home" ;

class images {
  var $homeDir ;
  function images() {
    $this->homeDir = $_SERVER['DOCUMENT_ROOT']."home/". $_SESSION['usr'];
  }
  
  function searchImages() {
    $list = array() ;
    function search($dir,$q) {
      $dir = $dir . "/" . $q ;
      global $list ;
      $uchwyt = opendir($dir) ;
      $nazwa = readdir($uchwyt) ;
      while($nazwa) {
        if(is_dir($dir."/".$nazwa)) {
          if($nazwa != "." && $nazwa != "..")
            search($dir,$nazwa) ;
          } else {
            $tempNazwa = strtolower($nazwa) ;
            if(eregi(".jpeg$",$tempNazwa) || eregi(".jpe$",$tempNazwa) || eregi(".png$",$tempNazwa) || eregi(".gif$",$tempNazwa)) {
              $i = count($list) ;
              $list[$i]->file = $q ."/". $nazwa ;
              $list[$i]->directory = $dir ;
            }
          }
        $nazwa = @readdir($uchwyt) ;
      }
      return $list;
    } /** end search() **/
    $listFile = array() ;
    $listFile = search($this->homeDir,"") ;
    return $listFile ;
  }
} 
$imageFile = new images ;
$files = $imageFile->searchImages() ;
for($i=0;$i<count($files);$i++)
  echo "\nhttp://".getenv(server_name)."/home/$usr/".$files[$i]->file."\n" ;
?>

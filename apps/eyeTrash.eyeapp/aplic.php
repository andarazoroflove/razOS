<?php
if (defined ('USR') && ! function_exists ('Trash')) {
function Trash($eyeapp, &$appinfo) {
 $dirtrash = USRDIR.USR."/Trash/";
 if (!is_dir($dirtrash)) mkdir($dirtrash, 0777);

 addActionBar("<strong>"._L('Removed files')."</strong>",'center');

 function lstrash() 
 {
  $dirtrash = USRDIR.USR."/Trash/";
  $trashdir=opendir($dirtrash);
  echo "
   <script LANGUAGE=\"JavaScript\">
    function reallyRemove() {
     var agree=confirm(\""._L('File will be permanently deleted. Continue?')."\");
     if (agree) return true; else return false ;
    }

     function reallyEmpty() {
      var agree=confirm(\""._L('Empty Trash')."?\");
      if (agree) return true; else return false ;
    }
   </script>
<div style='margin-left: 20px; margin-top: 10px;'><table width='98%' border='0'>";
  $compte = 0;
  while ($arx = readdir($trashdir)){
    if ($arx <> ".." && $arx <> "."){
    $compte++;
    $arxnom = urldecode($arx);
    $arx = urlencode($arx);
    $modificat = date("d-m-Y H:i", filemtime($dirtrash . $arx));
    $mida = round(filesize($dirtrash . $arx) / 1000);
    $ext = strtolower(substr(strrchr($arx, "."), 1));
     if ($ext == "php" || $ext == "php3" || $ext == "phps"){ die("ERROR"); }
echo "<tr>
<td><strong>$arxnom</strong></td><td>$mida kb.</td><td>$modificat</td><td><a href='desktop.php?a=eyeTrash.eyeapp&quinrest=$arx'><img border='0' src='".SYSDIR."gfx/btn/restore.png'></a></td><td><a onclick='return reallyRemove()' href='desktop.php?a=eyeTrash.eyeapp&quinesborrar=$arx'><img border='0' src='".SYSDIR."gfx/btn/delete.png'></a></td>
</tr>";
  }
}
 echo "</table></div><div align='center'>";
     if ($compte == 0)
         echo "<div align='left' style='margin-left: 30px;'>"._L('Trash is empty')."</div>";
      else
	addActionBar("<a onclick='return reallyEmpty()' href='?a=$eyeapp&empty'>
        <img style='margin-top: 4px;' border='0' src='".findGraphic ('', "btn/delete.png")."'>
      </a>");
echo "</div>";

closedir($trashdir);
}

    function emptytrash($d) {
       if(empty($d)) {
         return true;
       }
       if(file_exists($d)) {
         $dd = dir($d);
         while($arxiusdir = $dd->read()) {
           if($arxiusdir != '.' && $arxiusdir != '..') {
             if(is_dir($d.'/'.$arxiusdir)) {
               emptytrash($d.'/'.$arxiusdir);
             } else {
               @unlink($d.'/'.$arxiusdir) or die($error);
             }
           }
         }
         $dd->close();
       } else {
         return false;
       }
       return true;
    }

   // SECURITY : quinesborrar used as file name

     if (isset ($_REQUEST['quinesborrar'])) {
    $quinesborrar = basename($_REQUEST['quinesborrar']);
    $quinesborrar = urlencode($_REQUEST['quinesborrar']);
    $quinesborrar = basename($_REQUEST['quinesborrar']);
      $apaper = USRDIR.USR."/Trash/$quinesborrar";
      if (is_file ($apaper)) {
         unlink ($apaper);
         msg (_L('File deleted'));
      }
   }

      // Restore files
 
      if (isset ($_REQUEST['quinrest'])) {
     $quinrest = cls (@$_REQUEST['quinrest']);

      if (is_file (USRDIR.USR."/Trash/".$quinrest)) {
         copy (USRDIR.USR."/Trash/".$quinrest, HOMEDIR.USR."/".$quinrest);
         unlink(USRDIR.USR."/Trash/".$quinrest);
         msg(_L('File restored'));
      } }

      //Empty trash

    if (isset ($_REQUEST['empty'])) {
         $daborrar = USRDIR.USR."/Trash";
   if (is_dir ($daborrar))
      emptytrash ($daborrar);
         msg(_L('Trash succesfully drained'));
      }
      lstrash ();
return '';
}
$appfunction = 'Trash';
}
?>

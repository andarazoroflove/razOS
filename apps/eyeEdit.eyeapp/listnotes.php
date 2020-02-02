<?php
if (!defined('USR')) return;
$c = 0;
      echo "	
<script LANGUAGE='JavaScript'>
  function estassegur() {
   var agree=confirm('"._L('File will be permanently deleted. Continue?')."');
   if (agree) return true; else return false ; }
</script>
<div class='orangebub'>"._L('Private notes')."</div>
<table width='98%' border='0'>";
if (is_dir($d)) {
  $dnot=opendir($d);
  while ($n = readdir($dnot)){
    if ($n <> ".." && $n <> "." && substr($n, -4) == ".xml"){
     $c++;
     $ndata = parse_info ($d . $n);
     $ntitle = $ndata['title'];
     if (strlen($ntitle) > 17) $ntitle = substr($ntitle, 0, 15) . "...";
     $ndate = date("m.d.y - H:i:s", $ndata['date']);
     $nd = substr(urldecode($n), 0, -12);
     echo "<tr><td><a href='desktop.php?a=$eyeapp&type=openfile&file=$n'><img border='0' src='".SYSDIR."gfx/btn/opensmall.png'> <strong>$ntitle</strong></td><td><small>Last edited: $ndate</a></small></td><td> <a onclick='return estassegur()' href='desktop.php?a=$eyeapp&type=delete&remove=$n'><img border='0' src='".SYSDIR."gfx/btn/delete.png'></a></td></tr>";
    }
  }
 closedir($dnot);
}
if ($c == 0) echo "<tr><td>" . _L('There are no private notes') . "</td></tr>";

echo "</table>
<br /><div class='orangebub'>"._L('Public notes')."</div>
<table width='98%' border='0'>";

$c = 0;
if (is_dir($dpub)) {
 $dnot=opendir("etc/publicnotes/");
 while ($n = readdir($dnot)){
  if ($n <> ".." && $n <> "." && substr($n, -4) == ".xml"){
   $c++;
   $ndata = parse_info ("etc/publicnotes/" . $n);
   $ntitle = $ndata['title'];
   if (strlen($ntitle) > 17) $ntitle = substr($ntitle, 0, 15) . "...";
   $ndate = date("m.d.y - H:i:s", $ndata['date']);
   $nd = substr(urldecode($n), 0, -12);   
   $nereadauthor = $ndata['author'];
   echo "<tr><td><a href='desktop.php?a=$eyeapp&type=openfile&file=$n&public=public'><img border='0' src='".SYSDIR."gfx/btn/opensmall.png'> <strong>$ntitle</strong></td><td><small>Last edited by <strong>".$nereadauthor."</strong> at $ndate</a></small></td><td><a onclick='return estassegur()' href='desktop.php?a=$eyeapp&type=delete&remove=$n&public=public'><img border='0' src='".SYSDIR."gfx/btn/delete.png'></a></td></tr>";
  }
 }
 closedir($dnot);
}
if ($c == 0) echo "<tr><td>" . _L('There are no public notes') . "</td></tr>";

echo "</table>";
?>

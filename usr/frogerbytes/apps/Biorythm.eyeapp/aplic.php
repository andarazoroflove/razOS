<?php
if (!defined ('USR'))
  return;

echo '<div style="width:380px;border:1px solid #cacaca;">' ;
if(function_exists('imagecreate'))
  echo '<p style="text-align:center;"><img src="'.$appinfo['appdir'].'wykres.php?d='.$_POST['day'].'&m='.$_POST['month'].'y='.$POST['year'].'" border="0" alt="Biorythm" style="border:1px solid #e8e8e8;" /></p>' ;
else
  echo '<p style="text-align:center;"><img src="'.$appinfo['appdir'].'no_gd.png" border="0" alt="Biorythm" style="border:1px solid #e8e8e8;" /></p>' ;

echo '<form method="post" action="">' ;
echo '<p style="text-align:center;"><select name="day" style="width:40px;">' ;
for($i=1;$i<32;$i++) {
  echo '<option value="'.$i.'" ' ;
  if($_POST['day'] == $i)
    echo " SELECTED " ;
  echo '>'.$i.'</option>' ;
}
echo '</select>&nbsp;' ;
echo '<select name="month" style="width:40px;">' ;
for($i=1;$i<13;$i++) {
  echo '<option value="'.$i.'" ' ;
  if($_POST['month'] == $i)
    echo ' SELECTED ' ;
  echo '>'.$i.'</option>' ;
}
echo '</select>&nbsp;' ;
echo '<select name="year" style="width:60px;">' ;
for($i=1900;$i<date("Y")+1;$i++) {
  echo '<option value="'.$i.'" ' ;
  if($_POST['year'] == $i)
    echo ' SELECTED ' ;
  echo '>'.$i.'</option>' ;
}
echo '</select>&nbsp;<input type="submit" value="Go" style="width:150px;border:1px solid #000;background-color:#cacaca;color:#000;" /></p>' ;
echo '</form>' ;
echo '<p style="color:red;margin:1px;padding:1px;margin-left:50px;">'._L("Physical graph").'</p>' ;
echo '<p style="color:green;margin:1px;padding:1px;margin-left:50px;">'._L("Intellectual graph").'</p>' ;
echo '<p style="color:blue;margin:1px;padding:1px;margin-left:50px;">'._L("Emotional graph").'</p>' ;
echo '</div>' ;

?>

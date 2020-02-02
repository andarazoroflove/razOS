<?php
if (defined ('USR') && ! function_exists ('eyePhones')) {
function eyePhones ($eyeapp, &$appinfo) {
//Presa de variables globals

//Inici contingut
$dir = USRDIR.USR."/eyePhones/";
if (!is_dir($dir)) {mkdir($dir, 0777);}

function c($txt)
{
  $txt = strip_tags($txt);
  return $txt;
}
if (isset ($_REQUEST['surname']) && isset ($_REQUEST['name']))
{
 $nomguardar = urlencode(cls($_REQUEST['surname']) . cls($_REQUEST['name']));
 createXML ($dir . $nomguardar, $eyeapp, array (
  'surname' => c($_REQUEST['surname']),
  'name' => c($_REQUEST['name']),
  'nickname' => c($_REQUEST['nickname']),
  'home_number' => c($_REQUEST['home_number']),
  'work_number' => c($_REQUEST['work_number']),
  'mobile_phone' => c($_REQUEST['mobile_phone']),
  'mobile_office' => c($_REQUEST['mobile_office']),
  'email' => c($_REQUEST['email']),
  'webpage' => c($_REQUEST['webpage']),
  'company' => c($_REQUEST['company']),
  'home_adress' => c($_REQUEST['home_adress']),
  'company_adress' => c($_REQUEST['company_adress']),
  'instant_messenger' => c($_REQUEST['instant_messenger']),
  'fax' => c($_REQUEST['fax']),
  'voice_over_ip' => c($_REQUEST['voice_over_ip']),
  'contact_notes' => c($_REQUEST['contact_notes']),
   ));
   msg (_L('Saved'));
}

if (isset ($_REQUEST['nomxesb']))
{
  $nomxesb = cls($_REQUEST['nomxesb']);
  if (file_exists($dir . $nomxesb) && is_file($dir . $nomxesb)) {
        unlink($dir . $nomxesb);
        msg($tel_esborrat);
  }
}

$compte = 0;
  $directori=opendir($dir);
  while ($arx = readdir($directori)) {
    if ($arx <> ".." && $arx <> "."){
        $compte++;
        break;
    }
  }

switch (@strtolower ($_REQUEST['type'])) {
default:

echo "<script LANGUAGE=\"JavaScript\">
function deleteAlert() {
var agree=confirm(\""._L('File will be permanently deleted. Continue?')."\");
if (agree) return true; else return false ; }
</script>\n
";
addActionBar("<a href='desktop.php?a=$eyeapp&type=manage'><img alt='"._L('New contact')."' title='"._L('New contact')."' border='0' src='".findGraphic ('', "btn/new.png")."'></a>");

echo "<div align='center'><br />";
if ($compte > 0) {
echo "<table width='90%' border='0'>";

  $directori=opendir($dir);
  while ($arx = readdir($directori)) {
    if ($arx <> ".." && $arx <> "."){
        $arxd = urlencode($arx);
        $compte++;
    $contactread = parse_info ($dir . $arx);
    echo "<tr>
<td><a href='desktop.php?a=$eyeapp&type=manage&edit=$arxd'><img style='position:relative; top: 2px;' alt='"._L('Edit contact')."' title='"._L('Edit contact')."' border='0' src='".$appinfo['appdir']."gfx/contact.png'><strong>
" . $contactread['surname'] . "</strong>, " . $contactread['name'] . "</a></td>
<td>" . @$contactread['home_number'] . "</td>
<td><div align='center'><a href='desktop.php?a=$eyeapp&type=manage&edit=$arxd'><img border='0' src='".findGraphic ('', "btn/edit.png")."'></a></div></td>
<td><div align='center'><a onclick='return deleteAlert()' href='desktop.php?a=$eyeapp&nomxesb=$arxd'><img border='0' src='".findGraphic ('', "btn/delete.png")."'></a></div>
</td>
</tr>";
    }
  }
echo "</table>";
}
 else echo "<div align='left' style='margin-left: 40px;'>"._L('There are no entries')."</div>"; 

echo "</div>";
break;

case "manage":
 addActionbar ("<a href='?a=$eyeapp'><img border='0' alt='"._L('Go Back')."' title='"._L('Go Back')."' src='".findGraphic ('', "btn/back.png")."' /></a>");
 addActionbar ("<div style='position: absolute; top: 5px; left: 38px;'><form name='agregar' method='post' action='desktop.php?a=$eyeapp'><input style='border: 0;  background-color: transparent; color: #929292;' TYPE='image' SRC='".GFXDIR."btn/save.png'></div>");

  if (isset ($_REQUEST['edit'])) {
   $contactfile = @basename(strip_tags($_REQUEST['edit']));
   if (is_file($dir . $contactfile)) @$c = @parse_info ($dir . $contactfile);
  }


echo "
<div style='position: absolute; top: 50px; left: 15px;'>
<table width='100%' border='0' cellspacing='2'>
  <tr>
    <td colspan='2'><div class='categories'>"._L('Personal Information')."</div></td>
    <td colspan='2'><div class='categories'>"._L('Internet Information')."</div></td>
 </tr>
  <tr>
    <td valign='top'>"._L('Family Name')."</td><td valign='top'><input name='surname' type='text' size='24' value='".@$c['surname']."' /></td>
    <td valign='top'>"._L('Nickname')."</td><td valign='top'><input name='nickname' type='text' size='24' value='".@$c['nickname']."' /></td>
  </tr>
  <tr>
    <td valign='top'>"._L('Name')."</td><td valign='top'><input name='name' type='text' size='24' value='".@$c['name']."' /></td>
    <td valign='top'>"._L('E-mail')."</td><td valign='top'><input name='email' type='text' size='24' value='".@$c['email']."' /></td>

  </tr>
  <tr>
    <td valign='top'>"._L('Home Number')."</td><td valign='top'><input name='home_number' type='text' size='24' value='".@$c['home_number']."' /></td>
    <td valign='top'>"._L('Web Page')."</td><td valign='top'><input name='webpage' type='text' size='24' value='".@$c['webpage']."' /></td>
  </tr>
  <tr>
    <td valign='top'>"._L('Mobile Phone')."</td><td valign='top'><input name='mobile_phone' type='text' size='24' value='".@$c['mobile_phone']."' /></td>
    <td valign='top'>"._L('Instant Messenger')."</td><td valign='top'><input name='instant_messenger' type='text' size='24' value='".@$c['instant_messenger']."' /></td>
  </tr>
  <tr>
    <td valign='top'>"._L('Home Adress')."</td><td valign='top'>
<textarea class='llibreta' name='home_adress' rows='2' cols='19'>".@$c['home_adress']."</textarea>
    <td valign='top'>"._L('Voice Over IP')."</td><td valign='top'><input name='voice_over_ip' type='text' size='24' value='".@$c['voice_over_ip']."' /></td>
  </tr>
  <tr>
    <td colspan='2'><div class='categories'>"._L('Professional Information')."</div></td>
    <td colspan='2'><div class='categories'>"._L('Notes')."</div></td>
  </tr>

  <tr>
    <td>"._L('Company')."</td><td><input name='company' type='text' size='24' value='".@$c['company']."' /></td>
    <th rowspan='5' colspan='2' align='left'><textarea class='llibreta' name='contact_notes' rows='6' cols='35' style='width: 94%; height: 97%;'>".@$c['contact_notes']."</textarea></th>
  </tr>
  <tr><td>"._L('Work Number')."</td><td><input name='work_number' type='text' size='24' value='".@$c['work_number']."' /></td></tr>
  <tr><td>"._L('Mobile Office')."</td><td><input name='mobile_office' type='text' size='24' value='".@$c['mobile_office']."' /></td></tr>
  <tr><td>"._L('Fax')."</td><td><input name='fax' type='text' size='24' value='".@$c['fax']."' /></td></tr>
  <tr><td>"._L('Company Adress')."</td><td colspan=3><input name='company_adress' type='text' size='24' value='".@$c['company_adress']."' /></td></tr>
</table>
</form></div>";
  break;
}
}
$appfunction = 'eyePhones';
}
?>

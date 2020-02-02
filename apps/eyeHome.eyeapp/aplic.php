<?php
if (defined ('USR') && ! function_exists ('eyeHome')) {
/*
eyeHome.eyeapp
-------------
Version: 1.0.3

Developers:
-----------
Pau Garcia-Mila
Hans B. Pufal

Possible actions:
----------------
-upload
-remove
-newdir
-removedir

Whole app vars:
--------------
$path: Where you are in your directory tree

TODO:
Order listing of files (first directories, options for ordrering by size, name...)
*/
function eyeHome($eyeapp, &$appinfo) {

if (0 == strpos ($path = realpath (HOMEDIR.USR.'/'.trim(@$_REQUEST['path'])), realpath( HOMEDIR.USR.'/' ))) {

$size = get_size(HOMEDIR.USR) + get_size(USRDIR.USR);


$udir = substr ($path, strlen (realpath (HOMEDIR.USR)) + 1);
if (substr($udir, -1) != "/" && !empty($udir)) $udir .= "/";

$dir = HOMEDIR.USR."/".$udir;
//echo "<pre>path -> $path \ndir ->$dir \nupath -> $udir</pre>";

include $appinfo['appdir']."checkmessages.php"; //eyeMessages check function
if (!is_dir(HOMEDIR.USR."/")) mkdir(HOMEDIR.USR."/", 0777); //Create home directory if not exists

switch (@strtolower ($_REQUEST['type'])) {

case 'upload':  //Upload a file
 if (!empty($_FILES["file"]["name"])) {
  if ((USER_QUOTA && $size < USER_QUOTA) || !USER_QUOTA) {
      $file = $_FILES["file"]["name"];
      $file = str_replace("php", '', $file);
      $file = trim(urlencode(basename($file)));
      if (!file_exists($dir . $file)) {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $dir . $file)) msg(_L('File uploaded'));
      } else msg(_L('File already exists'));
     }
 }
break;

case 'remove':     //Delete a file
 if (!empty($_REQUEST['file'])) {
  $file = urlencode(basename($_REQUEST['file']));
  $trashdir = USRDIR.USR."/Trash/";
  if (!is_dir($trashdir)) mkdir($trashdir, 0777);
  if (is_file($dir . $file)) {
   if(copy ($dir.$file, $trashdir.$file) && unlink($dir.$file))
   msg(_L('File moved to trash'));
  }
 }
break;

case 'newdir':     //Create a directory
 if (!empty($_REQUEST['dirname'])) {
  $dirname = basename(trim($_REQUEST['dirname']));
  if (eregi ("^[a-z0-9]+$", $dirname))
  {
  if (!empty($dirname) && !file_exists($dir . $dirname)) {
   if(mkdir($dir . $dirname, 0777)) msg(_L('New directory created'));
   }
  } else msg(_L('Please, use only letters and numbers'));
 }
break;

case 'removedir':     //Delete a directory
 if (!empty($_REQUEST['dirname'])) {
  $dirname = basename(trim($_REQUEST['dirname']));
  $dirname = str_replace("./", "", $dirname);
  $dirname = str_replace(".", "", $dirname);
  if (!empty($dirname) && file_exists($dir . $dirname) && is_dir($dir . $dirname)) {
   if(@rmdir($dir . $dirname)) msg(_L('Directory removed')); else  msg(_L('The directory is not empty'));
  }
 }
break;

default: break;

}

//New directory hidden layer
echo "<div id='newdir'  class='bubble' style='left:44px; top: 47px; width: 190px; height:85px;'>
<div class='bubbleout'></div>
<div class='bubbletitle' >"._L('Create a new directory')."</div><div align='center'>
  <form action=\"desktop.php?a=$eyeapp\"method=\"post\">
   <input type='hidden' name='type' value='newdir' />
   <input type='hidden' name='path' value='$udir' />
   <div style='margin-bottom: 14px; margin-top: 10px;'><input type='text' name='dirname' maxlength='15' size='22' /></div>
   <input style='border: 0; background-color: transparent; color: #929292;' TYPE='image' SRC='".findGraphic ('', "btn/upload.png")."' /></div>
  </form>
   <div class='bubblecancel'>
     <a href='#' onClick=\"javascript:document.getElementById('newdir').style.display='none';\"><img border='0' alt='"._L('Cancel')."' title='"._L('Cancel')."' src='".findGraphic ('', "btn/cancel.png")."' /></a>
    </div>
</div>
";
addActionBar("<a href='#' onClick=\"javascript:document.getElementById('newdir').style.display='block';\">
  <img border='0' alt='"._L('New directory')."' title='"._L('New directory')."' src='${appinfo['appdir']}gfx/newfolder.png'>
</a>");

if ((USER_QUOTA && $size < USER_QUOTA) || !USER_QUOTA) {
//Upload a file hidden layer
echo "
<div id='newupload' class='bubble' style='left:70px; top: 47px; width: 370px; height:100px; '>
<div class='bubbleout'></div>
<div class='bubbletitle' >"._L('Upload a file')."</div><div align='center'>
 <form action=\"desktop.php?a=$eyeapp\" enctype=\"multipart/form-data\" method=\"post\">
   <input type='hidden' name='type' value='upload' />
   <input name=\"file\" type=\"file\" size=\"30\">
   <br /><br />
   <input style='border: 0; background-color: transparent; color: #929292;' TYPE='image' SRC='".findGraphic ('', "btn/upload.png")."' />
   <input type='hidden' name='path' value='$udir' />
 </form>
</div>
   <div class='bubblecancel'>
     <a href='#' onClick=\"javascript:document.getElementById('newupload').style.display='none';\"><img border='0' alt='"._L('Cancel')."' title='"._L('Cancel')."' src='".findGraphic ('', "btn/cancel.png")."' /></a>
    </div>
</div>
";
addActionBar("<a href='#' onClick=\"javascript:document.getElementById('newupload').style.display='block';\">
  <img border='0' alt='"._L('Upload a file')."' title='"._L('Upload a file')."' src='${appinfo['appdir']}gfx/upload.png'>
</a>");

}

echo "
<div style='position:absolute; left:14px; height:40%; top: 15%; width: 160px; background-color: #fff; border:1px solid #ccc; padding-left: 5px;'>
<div class='actions'>"._L('Actions')."</div>

<span class='sleft'><a href='?a=eyeEdit.eyeapp'>"._L('New Document')."</a></span>
<span class='sright'><img border='0' alt='"._L('New Document')."' title='"._L('New Document')."' src='${appinfo['appdir']}gfx/file.png' /></span>
<br />

<span class='sleft'><a href='?a=eyeMessages.eyeapp&enviarmsg'>"._L('New Message')."</a></span>
<span class='sright'><img border='0' alt='"._L('New Message')."' title='"._L('New Message')."' src='${appinfo['appdir']}gfx/file.png' /></span>
<br />

<span class='sleft'><a href='desktop.php?a=eyePhones.eyeapp&type=manage'>"._L('New Contact')."</a></span>
<span class='sright'><img border='0' alt='"._L('New Contact')."' title='"._L('New Contact')."' src='${appinfo['appdir']}gfx/file.png' /></span>
";

if (APP_INSTALLATION==2 || (APP_INSTALLATION==1 && USR==ROOTUSR)) 
echo "
<br />
<span class='sleft'><a href='desktop.php?a=eyeApps.eyeapp(installer)'>"._L('Install Application')."</a></span>
<span class='sright'><img border='0' alt='"._L('Install a new application')."' title='"._L('Install a new application')."' src='${appinfo['appdir']}gfx/install.png'/></span>";

echo"
<br /><br /></div>
<div style='position:absolute; left:14px; height:20%; top: 58%; width: 160px; background-color: #fff; border:1px solid #ccc; padding-left: 5px;'>

<div class='actions'>"._L('Notifications')."</div>";

if (checkmessages()) //Check if there are new messages
   echo "
<span class='sleft'><a href='desktop.php?a=eyeMessages.eyeapp'><strong>"._L('New Messages')."</strong></a></span>
<span class='sright'><img border='0' alt='"._L('New Messages')."' title='"._L('New Messages')."' src='${appinfo['appdir']}gfx/messages.png' /></span>
<br />";
else
   echo "
<span class='sleft'><a href='desktop.php?a=eyeMessages.eyeapp'>"._L('No new Messages')."</a></span>
<span class='sright'><img border='0' alt='"._L('No new Messages')."' title='"._L('No new Messages')."' src='${appinfo['appdir']}gfx/messages.png' /></span>
<br />";

//End of check
echo "
</div>
<div style=' position:absolute; left:190px; height:83%; top: 16%; right:0px; bottom:0px;'>";

include $appinfo['appdir']."listfiles.php"; //directory listing function

echo "</div>";
} //else: The path doesn't start with HOMEDIR.USR.'/'. Could be an attack.
return '';
}
$appfunction = 'eyeHome';
}
?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<meta name='description' content='eyeOS.info - Your eyeOS virtual desktop today.' />
<link rel='stylesheet' href='login/style.css' type='text/css' />
<script src='system/scripts/gclock.js'></script>
<title><?PHP echo $_SESSION['sysinfo']['hostname']; ?></title>
<link rel='icon' href='<?PHP echo findGraphic ('', "icon.gif"); ?>' type='image/x-icon' />  
<link rel='shortcut icon' href='<?PHP echo findGraphic ('', "icon.gif"); ?>' type='image/x-icon' />
</head>
<body OnLoad='document.loginform.usr.focus();'>
<div align="center">

<div class='bodynav'>
<div class='blockbe'><span class='systitle'><?PHP echo $_SESSION['sysinfo']['hostname']; ?></span>
<br /><?PHP if(file_exists ($uc = dirname (SYSINFO).'/infousers.txt') || file_exists ($uc = 'infousers.txt')) echo "We're ".trim(file_get_contents($uc))." users and counting!"; ?>
<div style='position: absolute; left: 100px; top: 65px; text-align:left;'>
  <span class='be'>be organized</span>
  <br />Calendar, Agenda, RSS Reader, Bookmarks...
</div>
<div style='position: absolute; left: 100px; top: 125px; text-align:left;'>
  <span class='be'>be productive</span>
  <br />Word Processor, Messaging System, Geolocation...
</div>
<div style='position: absolute; left: 100px; top: 185px; text-align:left;'>
  <span class='be'>be entertained</span>
  <br />Music and Video Player, Photo Galleries, Games...
</div>
<div style='position: absolute; left: 100px; top: 245px; text-align:left;'>
  <span class='be'>be connected</span>
  <br />Chat, Social Bookmarking, Public Board...
</div>
</div>


<div class='blocklogin'><div style='margin-top: 90px;'>
<div gclock style='position:absolute; top:40px; right:10px;'></div>
<span class='systitle'><?PHP echo $_SESSION['sysinfo']['hostname']; ?></span><br />
<?PHP if (!empty ($_SESSION['sysinfo']['hostname']) && (strtolower ($_SESSION['sysinfo']['hostname']) != 'eyeos')) 
echo "<span class='running'>running <a href='http://www.eyeos.org'>eyeOS</a> ".OSVERSION."</span>"; ?>
	    <div align='center' style='width: 100%; height: 25px; font-size:10pt;'>
        <?PHP echo $logon_msg ?>
      </div> 
	   <div align='left' style='margin-left: 50px; margin-top:0px;'> 
     
     <form name='loginform' action='index.php' method='post'> 
	      <input type='hidden' name='Toffset' value='' />
			Username<br />
			<input type='text' name='usr' maxlength='80' size='18' /><br />
			Password<br />
			<input type='password' name='pwd' maxlength='80' size='18' /><br />
			Language<br /><select name='newlang'>
          <?PHP 
            if (sizeof($Languages) > 1) 
              foreach ($Languages as $l) echo "<option>$l</option>";
          ?>
		      </select>
          <span style='margin-left:30px;'><input type='submit' name='submit' value='sign in' /></span>
	    </form></div>
</div></div>

<div class='blockdown'>
    <?PHP
      if ($rootEmail) {
    ?>
<div class='blockaccount'><span class='systitle'>create a new account</span> <div align='left' style='margin-left: 50px; margin-top:0px;'>
	      <form name='createnew' action='index.php' method='post'> 
		      Username<br />
		      <input type='text' name='newuser' maxlength='80' size='18' />
		      <br />Password<br />
		      <input type='password' name='newpwd' maxlength='80' size='18' />
		      <br />E-mail<br />
		      <input type='text' name='newmail' maxlength='80' size='11' />
		      <input type='hidden' name='reqkey' value='<?PHP echo $_SESSION['reqkey'] = time() ?>' />

            <input type='submit' name='submit' value='create' />

	      </form></div>
</div>
<div class='blocktext' style='width: 465px;'>
      <?PHP } else echo "<div class='blocktext' style='width: 465px;'>"; ?>
<div style='text-align:justify; clear:both;'>
Welcome to <strong><?PHP echo $_SESSION['sysinfo']['hostname']; ?></strong>, where you can create and use your own eyeOS Virtual Desktop. To find out more about eyeOS join our community by visiting the main <a href='http://www.eyeos.org'>eyeOS website</a>.
<br /><br />
With <strong><?PHP echo $_SESSION['sysinfo']['hostname']; ?></strong> all your data is available where ever you have Internet access and a standards compliant browser.
<br /><br />
        </div>
</div>
</div>
</body>
</html>

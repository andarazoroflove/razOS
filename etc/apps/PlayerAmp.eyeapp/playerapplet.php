<?php
session_start() ;
$usr = $_SESSION['usr'] ;
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<SCRIPT LANGUAGE="JavaScript"><!--
function pressStart()
{
	document.player.pressStart();
}
function pressPause()
{
	document.player.pressPause();
	if (document.panel.pause.value=="Resume/Pause") document.panel.pause.value="Pause/Resume";
	else document.panel.pause.value="Resume/Pause";
}
function pressStop()
{
	document.player.pressStop();
}
function pressShuffle()
{
	document.player.pressShuffle();
}
function pressRepeat()
{
	document.player.pressRepeat();
}

function pressNext()
{
	document.player.pressNext();
}
function pressPrevious()
{
	document.player.pressPrevious();
}
function loadSkin()
{
	var skinURL = document.panel.skinselect.options[document.panel.skinselect.selectedIndex].value;
	if (skinURL != "") document.player.loadMySkin(getBaseURL()+"skins/"+skinURL);
}
function loadPlaylist()
{
	var playlistURL = document.panel.playlist.value;
	if (playlistURL != "")
	{
	   document.player.loadMyPlaylist(playlistURL);
	   document.player.resetMyPlaylist();
	}
}
function getBaseURL()
{
	var baseURL = location.href;
	baseURL = baseURL.substring(0,(baseURL.lastIndexOf("/"))+1);
	return baseURL;
}
function getPlaylist()
{
	var playlist = document.player.getPlaylistDump();
	alert(playlist);
}
function getCurrentState()
{
	var state = document.player.getPlayerState();
	if (state == 0) state = "INIT";
	else if (state == 1) state = "OPEN";
	else if (state == 2) state = "PLAY";
	else if (state == 3) state = "PAUSE";
	else if (state == 4) state = "STOP";
	alert(state);
}
function getCurrentSong()
{
	var song = document.player.getCurrentSongName()+"\n"+document.player.getCurrentSongPath();
	alert(unescape(song));
}

function getGain()
{
	var gain = document.player.getGain();
	return gain;
}

function getBalance()
{
	var balance = document.player.getBalance();
	return balance;
}

function setBalance(val)
{
	document.player.setBalance(val);
}

function setGain(val)
{
	document.player.setGain(val);
}

function init()
{
  DumpPlaylist();
  document.panel.gainvalue.value=getGain();
  document.panel.balancevalue.value=getBalance();
}

function DumpPlaylist()
{
 var playlist=""+document.player.getPlaylistDump()+"";
 var playlist_array=playlist.split("#");
 var iframedoc = document.getElementById('myiframe').contentDocument;

 if (iframedoc==null) iframedoc=document.frames.myiframe.document;
 with (iframedoc)
 {
  open("text/html");
  clear();
  write("<html><head><title>Blank</title></head><body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>\n");
  for (var loop=0; loop < playlist_array.length; loop++)
  {
    var data_array = playlist_array[loop].split("|");
    var col = "#000000";
    for (var loopd=0; loopd < data_array.length; loopd++)
    {
     if (loopd == 0) col = "#000000";
     else col = "#AAAAAA";
     if (data_array[loopd].length > 0) writeln("<font face=Verdana size=-2 color="+col+">"+unescape(data_array[loopd])+"</font><br>");
    }
  }
  write("</body></html>\n");
  close();
  }

}
//--></SCRIPT>
<body bgcolor="#FFFFFF" text="#000000" link="#000066" vlink="#000066" alink="#000066" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="init()">
<div align="center">
<!-- jlGui Applet : Begin copy/paste -->
<SCRIPT LANGUAGE="JavaScript"><!--
    var _info = navigator.userAgent;
    var _ns = false;
    var _ns6 = false;
    var _ie = (_info.indexOf("MSIE") > 0 && _info.indexOf("Win") > 0 && _info.indexOf("Windows 3.1") < 0);
    if (_info.indexOf("Opera") > 0) _ie = false;
//--></SCRIPT>
    <COMMENT>
        <SCRIPT LANGUAGE="JavaScript1.1"><!--
        var _ns = (navigator.appName.indexOf("Netscape") >= 0 && ((_info.indexOf("Win") > 0 && _info.indexOf("Win16") < 0 && java.lang.System.getProperty("os.version").indexOf("3.5") < 0) || (_info.indexOf("Sun") > 0) || (_info.indexOf("Linux") > 0) || (_info.indexOf("AIX") > 0) || (_info.indexOf("OS/2") > 0) || (_info.indexOf("IRIX") > 0)));
        var _ns6 = ((_ns == true) && (_info.indexOf("Mozilla/5") >= 0));
//--></SCRIPT>
    </COMMENT>

<SCRIPT LANGUAGE="JavaScript"><!--
    if (_ie == true) document.writeln('<OBJECT classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" WIDTH = "275" HEIGHT = "348" NAME = "player"  codebase="http://java.sun.com/update/1.4.2/jinstall-1_4-windows-i586.cab#Version=1,4,0,0"><NOEMBED><XMP>');
    else if (_ns == true && _ns6 == false) document.writeln('<EMBED \
	    type="application/x-java-applet;version=1.4" \
            CODE = "javazoom.jlgui.player.amp.PlayerApplet" \
            JAVA_CODEBASE = "./" \
            ARCHIVE = "lib/jlguiapplet2.3.2.jar,lib/jlgui2.3.2-light.jar,lib/tritonus_share.jar,lib/basicplayer2.3.jar,lib/mp3spi1.9.2.jar,lib/jl1.0.jar,lib/vorbisspi1.0.1.jar,lib/jorbis-0.0.13.jar,lib/jogg-0.0.7.jar,lib/commons-logging-api.jar" \
            NAME = "player" \
            WIDTH = "275" \
            HEIGHT = "348" \
            scriptable ="true" \
            skin ="skins/bao.wsz" \
            start ="no" \
            song ="http://<?=getenv(server_name)?>/home/<?=$usr?>/playlist.m3u" \
            init ="jlgui.ini" \
            location ="url" \
            useragent ="winampMPEG/2.7" \
	    scriptable=true \
	    pluginspage="http://java.sun.com/products/plugin/index.html#download"><NOEMBED><XMP>');
//--></SCRIPT>
<APPLET  CODE = "javazoom.jlgui.player.amp.PlayerApplet" JAVA_CODEBASE = "./" ARCHIVE = "lib/jlguiapplet2.3.2.jar,lib/jlgui2.3.2-light.jar,lib/tritonus_share.jar,lib/basicplayer2.3.jar,lib/mp3spi1.9.2.jar,lib/jl1.0.jar,lib/vorbisspi1.0.1.jar,lib/jorbis-0.0.13.jar,lib/jogg-0.0.7.jar,lib/commons-logging-api.jar" WIDTH = "275" HEIGHT = "348" NAME = "player"></XMP>
    <PARAM NAME = CODE VALUE = "javazoom.jlgui.player.amp.PlayerApplet" >
    <PARAM NAME = CODEBASE VALUE = "./" >
    <PARAM NAME = ARCHIVE VALUE = "lib/jlguiapplet2.3.2.jar,lib/jlgui2.3.2-light.jar,lib/tritonus_share.jar,lib/basicplayer2.3.jar,lib/mp3spi1.9.2.jar,lib/jl1.0.jar,lib/vorbisspi1.0.1.jar,lib/jorbis-0.0.13.jar,lib/jogg-0.0.7.jar,lib/commons-logging-api.jar" >
    <PARAM NAME = NAME VALUE = "player" >
    <PARAM NAME="type" VALUE="application/x-java-applet;version=1.4">
    <PARAM NAME="scriptable" VALUE="true">
    <PARAM NAME = "skin" VALUE ="skins/bao.wsz">
    <PARAM NAME = "start" VALUE ="no">
    <PARAM NAME = "song" VALUE ="http://<?=getenv(server_name)?>/home/<?=$usr?>/playlist.m3u">
    <PARAM NAME = "init" VALUE ="jlgui.ini">
    <PARAM NAME = "location" VALUE ="url">
    <PARAM NAME = "useragent" VALUE ="winampMPEG/2.7">
</APPLET>
</NOEMBED>
</EMBED>
</OBJECT>
<!-- jlGui Applet : End copy/paste -->
<hr size="1" width="99%">
<form method="post" action="" name="panel">
<select name="skinselect">
  <option value="wa021.wsz">WinAmper</option>
  <option value="bluev.wsz">Blue Visions</option>
  <option value="blizzard2.wsz">Blizzard 2</option>
  <option value="bao.wsz" selected>Bang & Olufsen</option>
  <option value="33.wsz" selected>My Way</option>
  <option value="metrix.wsz" selected>Metrix</option>
</select>
<input type="button" name="loadskin" value="Load Skin" onClick="loadSkin()">
</form>

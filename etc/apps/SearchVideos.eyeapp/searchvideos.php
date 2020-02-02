<?php
session_start();

if (isset($_REQUEST['video'])) {
	$video = $_REQUEST['video'];
	$_SESSION['searchvideo'] = $video;
}
elseif (isset($_SESSION['searchvideo'])) 
{
	$video = $_SESSION['searchvideo'];
}
$video = basename($video);
$video = trim($video);
$video = strip_tags($video);
$video = urlencode($video);
$videodec = urldecode($video);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META NAME="DESCRIPTION" CONTENT="Search videos on Youtube.">
<link rel="stylesheet" href="styleweb.css" type="text/css">
<title>Search videos on Youtube.</title>
</head>
<body>
<h1>Search videos on <big>Youtube</big></h1>
<form>
Search videos about: 
<input type="text" size="30" name="video" value="<?php echo $videodec ?>"><br />
<input type="submit" value="Search videos">
</form>
  
<?php


require_once('rss_fetch.inc');

$tag = "http://www.youtube.com/rss/tag/".$video.".rss";
if ( $video ) {
echo "<table border='0'>";
	$rss = fetch_rss( $tag );
	$numvideos = 0;
	foreach ($rss->items as $item) {
	if ($numvideos == 3 || $numvideos == 6 || $numvideos == 9 || $numvideos == 12 || $numvideos == 15 || $numvideos == 18 ){ echo "</tr><tr>";}
	$numvideos++;
         preg_match('/img src="(.*)\.jpg"/',$item["description"],$imgUrlMatches);

             $imgurl = $imgUrlMatches[0];
             $title = $item["title"];
             $url = $item["link"];

             echo "<td align='center'><a href=\"$url\">"
                 ."<$imgurl alt=\"$title\"/>"
                 ."</a>\n</td>";
         
	}
echo "</table>";
}
?>
<br /><br /><div align='right'>Thanks to <a href='http://www.Youtube.com'>Youtube</a> and <a href='http://magpierss.sourceforge.net/'>MagPieRSS</a>.</div>
</body>
</html>

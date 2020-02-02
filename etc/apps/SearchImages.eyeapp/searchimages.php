<?php
session_start();

if (isset($_REQUEST['image'])) {
	$image = $_REQUEST['image'];
	$_SESSION['searchimage'] = $image;
}
elseif (isset($_SESSION['searchimage'])) 
{
	$image = $_SESSION['searchimage'];
}
$image = basename($image);
$image = trim($image);
$image = strip_tags($image);
$image = urlencode($image);
$imagedec = urldecode($image);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<META NAME="DESCRIPTION" CONTENT="Search Images on flickr.">
<link rel="stylesheet" href="styleweb.css" type="text/css">
<title>Search Images on flickr.</title>
</head>
<body>
<h1>Search images on <big>Flickr</big></h1>
<form>
Search images about: 
<input type="text" size="30" name="image" value="<?php echo $imagedec ?>"><br />
<input type="submit" value="Search images">
</form>
  
<?php
require_once('rss_fetch.inc');

$tag = "http://www.flickr.com/services/feeds/photos_public.gne?tags=".$image."&format=rss_200";
if ( $image ) {
echo "<table border='0'>";
	$rss = fetch_rss( $tag );
	$numimages = 0;
	foreach ($rss->items as $item) {
	if ($numimages == 2 || $numimages == 4 || $numimages == 6 || $numimages == 8 ){ echo "</tr><tr>";}
	$numimages++;
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
<br /><br /><div align='right'>Thanks to <a href='http://www.flickr.com'>Flickr</a> and <a href='http://magpierss.sourceforge.net/'>MagPieRSS</a>.</div>
</body>
</html>

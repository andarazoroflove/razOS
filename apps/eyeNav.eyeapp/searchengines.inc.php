<?php
switch ($_REQUEST['engine']) {
  default:
  case 'google':
    $w = "http://www.google.com/search?q=".$_REQUEST['url'];
  break;

  case 'a9':
    $w = "http://a9.com/".$_REQUEST['url'];
  break;

  case 'ask':
    $w = "http://www.ask.com/web?q=".$_REQUEST['url'];
  break;

  case 'yahoo':
    $w = "http://search.yahoo.com/search?p=".$_REQUEST['url'];
  break;

  case 'msn':
    $w = "http://search.msn.com/results.aspx?q=".$_REQUEST['url'];
  break;

  case 'amazon':
    $w = "http://www.amazon.com/gp/search/ref=br_ss_hs/102-6134268-4237749?search-alias=aps&keywords=".$_REQUEST['url'];
  break;

  case 'ebay':
    $w = "http://search.ebay.com/".$_REQUEST['url'];
  break;
}
?>

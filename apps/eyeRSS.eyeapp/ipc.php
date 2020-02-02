<?php
  if (! empty ($SYSipcmsg) && defined('USR')) {
    $dir = USRDIR.USR."/RSS.eyeapp/";
//  error_log ("L $eyeapp : $SYSipcmsg");
    switch (strtolower (substr ($SYSipcmsg, 0, strpos ($SYSipcmsg, '=')))) {
    case 'delfeed':
      echo !@unlink ($dir.basename (substr($SYSipcmsg, 8))) ? 
        'Error deleting '.basename (substr($SYSipcmsg, 8)) : 'OK';
      return;
      
    case 'readfeed':
      require_once $appinfo['appdir'].'XML/RSS.php';
      $rss =& new XML_RSS ($_SESSION['apps'][$eyeapp]['state.url'] = 'http://' . ($url =  str_replace('http://', '', substr($SYSipcmsg, 9))));
      $rss->parse ();
      $rssData = $rss->getChannelInfo ();
      echo "<div class='rssfeedid' style='display:none' url='$url' title='".
        ($_SESSION['apps'][$eyeapp]['state.title'] = @$rssData['title'])."'></div>";
      $rssData = $rss->getItems ();
      $maxarticles = (!empty ($appinfo['param.maxarticles']) && $appinfo['param.maxarticles']) ? $appinfo['param.maxarticles'] : count ($rssData);
      for ($i = 0;  ($item = @$rssData[$i]) && ($i < $maxarticles); $i++) {
        if (empty ($item['link']) && empty ($item['description']))
          continue;
        $showlink = $appinfo['param.headlinesonly'] && !empty ($item['description']) ? 
          "<img class='rssShow' src='".$appinfo['appdir']."Images/strzalka.gif' alt='"._L('show')."' title='"._L('show')."' style='cursor: pointer;' />" : '';
        echo "<div>",
          ((empty ($item['pubdate']) && !empty ($showlink)) ? "<span style='float:right;'>$showlink</span>" : ''),
          (!empty ($item['link']) ? 
            "<a style='font-weight:bold;font-size:16px;' href='".
            ($appinfo['param.xlink'] ? "${item['link']}' target='_BLANK'" : "?a=eyeNav.eyeapp(${item['link']})' target='_PARENT'").">".$item['title'].'</a>' :
            "<span style='font-weight:bold;font-size:16px;'>".$item['title'].'</span>'),
          ((!empty ($item['pubdate']) && !empty ($showlink)) ? "<br><span style='clear:left; float:right;'>${item['pubdate']}$showlink</span>" : (!empty ($item['description']) ? '&nbsp;&mdash;&nbsp;' : '')),
          (!empty ($item['description']) ? 
            ($appinfo['param.headlinesonly'] ? "<span class='RSSdesc' style='clear:both; display:none;'><br/>" : '') .
            $item['description']. 
            ($appinfo['param.headlinesonly'] ? '</span>' : '') : ''),
          '<hr style="clear:both">',
          '</div>';
      }
      return;
      
    case 'bookmark':
      if (!empty ($appinfo['state.url']) && !empty ($appinfo['state.title']) && (is_dir ($dir) || @mkdir ($dir)))
        createXML ($dir.date('U').'.xml', 'bookmark', array (
	        'title' => $appinfo['state.title'],
	        'url' => $appinfo['state.url']), 1);
    
    case 'getchannels':
      if ($dh = @opendir($dir)) {
        $feed = array ();
        while ($file = @readdir($dh))
          if (false !== ($q = parse_info ($dir.$file))) {
            if (!empty ($feed[$q['url'].$q['title']]))
              @unlink ($dir.$file);
            else {    
              $feed[$q['url'].$q['title']] = $dir.$file;
              echo "
               <p 
                 style='margin:0px; padding:0px; text-align:left;'
                 eyeFeed='url:$q[url]; title:$q[title]; file:$file;'>
                 <img 
                   src='".findGraphic ('', "btn/cancel.png")."' 
                   border='0' 
                   title='"._L('Delete')."' 
                   alt='"._L('Delete')."'
                   style='cursor:pointer;'
                   onclick='eyeRSS.delChannel(this)'/>
                 &nbsp;
                 <span 
                   style='cursor:pointer;' 
                   onclick='eyeRSS.showFeed(this, \"${q['url']}\", \"${q['title']}\")'>
                   $q[title]
                 </span>
              </p>";
            }
          }
        @closedir($dh);
      }
      return;
    }

    error_log ("E $SYSipcapp bad message : " . $SYSipcmsg);
    return;
}
?>


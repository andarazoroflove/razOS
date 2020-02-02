if (typeof eyeRSS == 'undefined') var eyeRSS = {
  
  showFeed : function (req, app, title, add) {
    var ele;
//  alert ('eyeRSS.showFeed (' + req + ', ' + app + ', ' + title + ')');
    if (req.status && app.id) {
      if (ele = getElementsByClass (app, 'RSSfeed', 'div')[0]) {
        ele.innerHTML = req.responseText;
        if (ele.getAttribute ('newfeed')) {
          ipc (app, 'bookmark=', eyeRSS.showChannels);
          ele.removeAttribute ('newfeed');
        }
        
        ele = getElementsByClass (app, 'rssShow');
        for (var i = ele.length; i--;)
          ele[i].onclick = function () {eyeRSS.showDesc (this)};

        ele = getElementsByClass (app, 'RSSFeedId', 'div')[0];
        var 
          feedURL = ele.getAttribute('url'),
          feedTitle = ele.getAttribute ('title');
        
        if (feedTitle.length > 36) {
          feedTitle = feedTitle.match (/^(\w*\W*\w*)(.*?\W*)(\w*\W*\w*)$/);
          feedTitle = feedTitle[1] + ((feedTitle[2].length > 8) ? ' ... ' : feedTitle[2]) + feedTitle[3];
        }
          
        ele = app.getElementsByTagName ('input');
        for (var i = ele.length; i--;) {
          ele[i].name && (ele[i].name == 'url') && (ele[i].value = feedURL);
          ele[i].name && (ele[i].name == 'title') && (ele[i].value = feedTitle);
        }
        
        ele = getElementsByClass (app, 'eyeRSStitle');
        for (var i = ele.length; i--;) {
          ele[i].innerHTML = feedTitle;
          ele[i].setAttribute ('title', feedURL);
        }
      }
    }
    
    else if (req.tagName) {
      ele = req;
      req = app;
      app = ele;
      while (app && (!app.className || !app.className.match(/(^|\s)eyeapp(\s|$)/i)))
        app = app.parentNode;

      if (req && req.tagName) {
        while (req.tagName != 'DIV')
          req = req.parentNode;
        req = req.getElementsByTagName ('INPUT');
        req = req[0].value;
      }
      
      if (!req && (ele = getElementsByClass (app, 'RSSFeedId', 'div')[0])) {
        req = ele.getAttribute('url');
        title = title || ele.getAttribute('title');
      }
      
      if (req) {
        ele = getElementsByClass (app, 'RSSFeed', 'div')[0]
        add && ele.setAttribute ('newfeed', 1);
        ele.innerHTML = 'Please wait, loading rss feed from ' + (title || req);
        ipc (app, 'readfeed=' + req, eyeRSS.showFeed);
      }
    }
  },

  showDesc : function (ele) {
//  alert ('eyeRSS.showDesc (' + ele + ')');
    var app = ele;
    while (app && (!app.className || !app.className.match(/(^|\s)eyeapp(\s|$)/i)))
      app = app.parentNode;
    var 
      feed = getElementsByClass (app, 'RSSfeed', 'div')[0],
      nodes = getElementsByClass (feed, '(rssShow|rssDesc)');
      
    for (var i = nodes.length; i--;)
      nodes[i].style.display = (nodes[i].tagName.toUpperCase() == 'IMG') ? 'inline' : 'none';
    
    var e = ele.parentNode;
    while (e && (!e.className || (e.className.toLowerCase() != 'rssdesc')))
      e = e.nextSibling; 
      
    e.style.display='inline'; 
    ele.style.display='none';
    ele.parentNode.parentNode.scrollIntoView();
  },
  
  showChannels : function (req, app) {
//  alert ('eyeRSS.showChannels (' + req + ', ' + app + ')');
    if ((arguments.length == 1) && req.tagName)
      ipc (req, 'getchannels=', eyeRSS.showChannels);
    else if (req.status && app.tagName)
      getElementsByClass (app, 'rssChannels', 'div')[0].innerHTML = req.responseText;
    else
      alert ('eyeRSS.showChannels (' + req + ', ' + app + ')');
  },
  
  delChannel : function (req, chan) {
//  alert ('eyeRSS.delChannel (' + req + ', ' + chan + ')');
    if ((arguments.length == 2) && req.responseText) {
      if (req.responseText == 'OK')
        chan.parentNode.removeChild (chan);
      else
        alert ('Error removing channel : ' + req.responseText);
      return;  
    }
    
    var feed;
    while (req && (!req.getAttribute || !(feed = req.getAttribute('eyeFeed'))))
      req = req.parentNode;
  
    if (req && feed) {
      feed= parseAttribute (feed);
      ipc (req, 'delfeed=' + feed.file, eyeRSS.delChannel, req);
    }
  },
  
  init : function () {
    var
      app,
      ele = getElementsByClass (document, 'RSSfeed', 'div');
    
    for (var i = ele.length; i--;) {
      app = ele[i];
      while (app && (!app.className || !app.className.match(/(^|\s)eyeapp(\s|$)/i)))
        app = app.parentNode;
      if (app) {  
        eyeRSS.showFeed (app);
        eyeRSS.showChannels (app);
      }
    }
    
    if (app && (ele = getElementsByClass (app, 'eyeConfig')[0])) 
      ele.updateAction = function () { eyeRSS.showFeed (this)};

    removeEvent (window, 'load', eyeRSS.init, false);
  }
}

if (eyeRSS.init) {
  if (window.addEventListener)
    window.addEventListener ('load', eyeRSS.init, true);
  else if (window.attachEvent)
    window.attachEvent ("onload", eyeRSS.init);
}

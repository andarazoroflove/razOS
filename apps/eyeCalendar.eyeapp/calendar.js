var eyeCal = {
  
deleteNote : function (ele, cal) {
  if (arguments.length == 2) {
    if (ele.responseText.substring (0,2) == 'OK') {
      cal.setAttribute ('events', cal.getAttribute ('events') & ~(1 << (ele.responseText.substring (2,4))));
      eyeCal.show (cal, ele.responseText.substring (2,4), ele.responseText.substring (4,6), ele.responseText.substring (6));
    } else
      alert (ele.responsetText);
    return;  
  }
  
  while (ele && (!ele.className || !ele.className.match(/(^|\s)eyeapp(\s|$)/i)))
    ele = ele.parentNode;
  if (ele) {
    cal = getElementsByClass (ele, 'Cal_month', '*')[0];
    ele = getElementsByClass (ele, 'Cal_note', '*')[0];
  }
  
  if (ele && ((appParam (ele, 'del_confirm') == 0) || confirm (eyeCalString.deleteConfirm))) 
    ipc (ele, 'delete='+ ele.notedate[0] + ']', eyeCal.deleteNote, cal);
},

saveNote : function (ele, app, etyp) {
  
//  alert ('eyeCal.saveNote (' + ele + ', ' + app + ', ' + etyp + ')');

  if (arguments.length == 2) {
    if (ele.responseText.substring (0, 2) == 'OK') {
      app.setAttribute ('events', app.getAttribute ('events') | (1 << (ele.responseText.substring (2,4))));
      eyeCal.show (app, ele.responseText.substring (2,4), ele.responseText.substring (4,6), ele.responseText.substring (6));
    } else
      alert ('Error : ' + ele.responseText);
    return;  
  }
  
  app = ele;
  while (app && (!app.className || !app.className.match(/(^|\s)eyeapp(\s|$)/i)))
    app = app.parentNode;
  
  if (arguments.length == 3) {
    if (etyp == 0) { // keypress    
      app = getElementsByClass (app, 'Cal_save');
      for (var i = app.length; i--;)
        app[i].style.display = 'inline';
    
      return;
    }
    
    if ((etyp == 1) && !appParam (app, 'auto_save'))
      return;  
  }
  
  if (app) {
    ele = getElementsByClass (app, 'Cal_note')[0];
    app = getElementsByClass (app, 'Cal_month')[0];
  }
  
  if (ele) {
    var notedate = ele.notedate;
    ele = ele.firstChild;
    while (ele && !(ele.tagName == 'TEXTAREA'))
       ele = ele.nextSibling;
    ele && app && ipc (ele, 'create='+ notedate[0] + encodeURIComponent (ele.value) + ']', eyeCal.saveNote, app);
  }
},


showNote : function (ele, tag, d, m, y) {
//  alert ('eyeCal.showNote (' + ele + ', ' + tag + ', ' + d + ',' + m + ',' + y + ')');  
  var notetext = '',
      notedate = [];

  if (arguments.length == 5) { // get event text for day d/m/y
    notedate = [('0'+d).slice (-2) + ('0'+m).slice (-2) + y, Date.parse (m + '/' + d + '/' + y)];
    if (tag) {
       ele.notedate = notedate; 
       ipc (ele, 'note='+ notedate[0] + ']', eyeCal.showNote, ele);
       return;
    }
  }

  if (arguments.length == 2) { // process AJAX response : ele is the AJAX request object
    var 
      req = ele;
    ele = tag;
    notedate = ele.notedate;
    notetext = req.responseText;
  }

  if (typeof ele == 'string')
    ele = document.getElementById (ele);
  
  while (ele && (!ele.className || !ele.className.match(/(^|\s)eyeapp(\s|$)/i)))
    ele = ele.parentNode;
    
  if (ele) {
    var
      savers = getElementsByClass (ele, 'Cal_save'),
      deleters = getElementsByClass (ele, 'Cal_delete');
   
    if (ele = getElementsByClass (ele, 'Cal_note')[0]) {
      ele.notedate = notedate;
      for (var i = deleters.length; i--;)
        deleters[i].style.display = notetext ? 'inline' : 'none';
      
      for (var i = savers.length; i--;)
        savers[i].style.display = 'none';
      
      ele.innerHTML = (notetext ? 
        eyeCal.formatDate (ele.notedate[1], appParam (ele, 'note_header')) : '') + 
        '<br/><textarea onkeypress="eyeCal.saveNote(this,0,0)" onblur="eyeCal.saveNote(this,0,1)" style="width:300px; height:190px;">'+notetext+'</textarea>';
    }
  }
},


month : function (cal, m, typ) {
//  alert ('month (' + cal + ', ' + m + ', ' + typ + ')');  

  switch (typ) {
  case 0: // onMouseDown
      eyeCal.timeout = setTimeout ("eyeCal.month (null, 0, 2)", 400);
      eyeCal.ele = cal;
    break;
    
  case 1: // onMouseUp
    if (eyeCal.timeout) {
      clearTimeout (eyeCal.timeout);
      eyeCal.timeout = null;
      eyeCal.show (cal, 0, m);
    }
    break;
    
  case 2: // mouse stayed down 400 ms
    eyeCal.timeout = null;
/*    var s = '<select>';
    for (var m = 1; m <=12; m++)
       s += '<option >' + eyeCalString.mn[m];     
    eyeCal.ele.innerHTML = s + '</select>'
    eyeCal.ele.firstChild.onclick (); */
    break;
  }
  return void 0;
},


show : function (cal, d, m, y, ws) {
  var 
    events;

//  alert ('eyeCal.show ('+cal+', '+d+', '+m+', '+y+', '+ws+')');
  
  if (arguments.length == 2) {
    var rtext = cal.responseText.split (';');
    cal = d;
    
    d = rtext[0].substring(0,2);
    m = rtext[0].substring(2,4);
    y = rtext[0].slice(-4);
    ws = null;
    cal.setAttribute ('month', m);
    cal.setAttribute ('year', y);
    cal.setAttribute ('day', d);
    cal.setAttribute ('events', events = parseInt (rtext[1],10));
    wno = parseInt (rtext[2], 10);
  }

  if (typeof cal == 'string')
    cal = document.getElementById (ele);

  if (cal && (!cal.className || !cal.className.match(/(^|\s)cal_month(\s|$)/i))) {  
    while (cal && (!cal.className || !cal.className.match(/(^|\s)eyeapp(\s|$)/i)))
      cal = cal.parentNode;
    
    cal && (cal = getElementsByClass (cal, 'Cal_month')[0]);
  }

  if (!cal) {
     alert ('No calendar element found');  
     return;
  }

  var 
    today = new Date ();

  if ((d == 'today') || (d == 'tomonth')) {
    y = today.getFullYear ();
    m = today.getMonth () + 1;
    d = (d == 'today') ? today.getDate () :0;
  }

  if ((typeof m == 'undefined') || (m == null)) m = cal.getAttribute ('month');   
  if ((typeof y == 'undefined') || (y == null)) y = cal.getAttribute ('year');   
  if ((typeof d == 'undefined') || (d == null)) d = cal.getAttribute ('day');   
  if ((typeof ws == 'undefined') || (ws == null)) 
    ws = appParam (cal, 'week_start');
  else
    appParam (cal, 'week_start', ws);

  if (typeof m == 'string') m = parseInt (m, 10);
  if (typeof y == 'string') y = parseInt (y, 10);
  if (typeof d == 'string') d = parseInt (d, 10);
  if (typeof ws == 'string') ws = parseInt (ws, 10);

  if ((cal.getAttribute ('month') != m) || (cal.getAttribute ('year') != y)) {
    ipc (cal, 'cald=' + ('0' + d).slice (-2) + ('0' + m).slice (-2) + y + ']', eyeCal.show, cal);
    return;
  } else {
    events = cal.getAttribute ('events'); // .parseInt (events, 10);
  }

  if (typeof y != 'number')
    y = today.getFullYear ();

  if (m == 0) m = 12, y--;
  if (m == 13) m = 1, y++;

  if ((typeof m != 'number') || (m < 1) || (m > 12))
    m = today.getMonth () + 1;
  if ((typeof ws != 'number') || (ws < 0) || (ws > 6))
    ws = 1;

  cal.setAttribute ('wstart', ws);

  cal.innerHTML = eyeCal.make (cal, d, m, y, ws, events)
},


make : function (cal, d, m, y, ws, events) {  
  var 
    weekno = appParam(cal, 'week_no') ? wno : 0,
    today = new Date (),
    caltable = "<table class='cal' cellpadding='1' cellspacing='2'><tr>" +
      (weekno ? "<th class='Cal weekno'></th>" : "") + 
       "<th width='2' class='cal next' style='cursor:pointer;' onmousedown='eyeCal.month (this, "+(m-1) + ", 0)' onmouseup='eyeCal.month (this, "+(m-1) + ", 1)'>&lt;&lt;</th>"+
      "<th class='cal month' colspan='5'>" + eyeCalString.mname[m] + " " + y + "</th>"+
      "<th width='2' class='cal prev' style='cursor:pointer;'  onmousedown='eyeCal.month (this, "+(m+1)+", 0)' onmouseup='eyeCal.month (this, "+(m+1)+", 1)'>&gt;&gt;</th>"+
    "<tr>";

  weekno && (caltable += "<td class='Cal weekno' align='center'>#</td>");

  caltable += "<td width='25' align='center' class='cal day d"+ws+"'>" + eyeCalString.dn[ws] + "</td>";
 
  for (var i = 1; i < 7; i++)
    caltable += "<td style='cursor:pointer;' align='center' class='cal day d"+((i+ws)%7)+"' onclick='eyeCal.show (this,"+d+","+m+","+y+"," + ((i+ws)%7) + ")' >" + eyeCalString.dn[(i+ws)%7] + "</td>";

  var
    md = 1,
    caldate = new Date (y, m-1, md, 0, 0, 0, 0);
    
  caltable += "</tr><tr>";  
  weekno && (caltable += "<td class='Cal weekno' align='center'>"+weekno+"</td>");

  var
    calrow = 0,
    calday = ws;
  while ((calday % 7) != caldate.getDay()) caltable += '<td></td>', calday++; 
  while (true) {
    var cl = '';
    ((m == today.getMonth() + 1) && (md == today.getDate ()) && y == today.getFullYear()) && (cl = " today");
    ((events >> md) & 1) && (cl+= " event");
    if (md == d) {
      cl += ' selected';
      eyeCal.showNote (cal, (events >> md) & 1, d, m, y);  
    }
    caltable += "<td align='center' class='cal "+cl+" d"+(calday%7)+"' style='cursor:pointer;' onclick='eyeCal.show (this, "+md+","+m+","+y+")'  >" + md + "</td>"; 
    caldate.setDate (++md);
    if (caldate.getMonth () + 1 != m) break;
    if ((++calday % 7) == ws) { 
      weekno && (weekno = ((m == 1) && (weekno > 51)) ? 1 : weekno+1);
      calrow++, caltable += "</tr><tr>";
      if (weekno)
        caltable += "<td class='Cal weekno' align='center' title='www'>"+weekno+"</td>";
    }
  }

  while (calrow++ < 5) caltable += "</tr><td colspan='8'>&nbsp;</td><tr>";
  
  return caltable + "</tr></table>";
},


reInit : function (cal) {
  while (cal && (!cal.className || !cal.className.match (/(^|\s+)eyeapp(\s+|$)/i)))
    cal = cal.parentNode;

  if (arguments.length == 2) {
    var 
      req = cal,
      ele = arguments[1];

    alert (req.responseText);

    return;
  }
  
  var 
    t = appParam (cal, 'time');
    
  if (typeof gclock != 'undefined' && t) {  
    var ele = getElementsByClass (cal, 'calClock');
    for (var i = ele.length; i--;) {
      ele[i].setAttribute ('Gclock', 'format:'+t); 
      gclock.init (ele[i]);
    }
  }
  
  ele = getElementsByClass (cal, 'Cal_today');
    for (var i = ele.length; i--;)
      ele[i].innerHTML = eyeCal.formatDate ('today', appParam(cal, 'toolbar_today')); 
},


init : function () {
  var
    app,
    ele;

  ele = getElementsByClass (document, 'Cal_delete');
  for (var i = ele.length; i--;) {
    ele[i].style.display = 'none';
    ele[i].onclick = function () {eyeCal.deleteNote (this)};
  }

  ele = getElementsByClass (document, 'Cal_save');
  for (var i = ele.length; i--;) {
    ele[i].style.display = 'none';
    ele[i].onclick = function () {eyeCal.saveNote (this)};
  }

  ele = getElementsByClass (document, 'Cal_today');
  for (var i = ele.length; i--;)
    ele[i].onclick = function () {eyeCal.show (this, 'today', null, null)};

  ele = getElementsByClass (document, 'Cal_month');
  for (var i = ele.length; i--;) {
    var app = ele[i].parentNode;
    while (app && (!app.className || !app.className.match(/(^|\s)eyeapp(\s|$)/i)))
      app = app.parentNode;
    
    eyeCal.reInit (ele[i]);
    eyeCal.show (ele[i], 'today', null, null, null);
    
    if (app && (ele = getElementsByClass (app, 'eyeConfig')[0])) 
      ele.updateAction = function () { eyeCal.reInit (this); eyeCal.show (this, null, null, null, null)};
  }

  removeEvent (window, 'load', eyeCal.init, false);
},


formatDate : function (date, fmtString) {
  
//alert ('formatDate (' + date + ', ' + fmtString + ')');
  
  if (!date)
    date = new Date ();
  else if (typeof date == 'number')
    date = new Date (date);
  else if (typeof date == 'string')
    date = (date.toLowerCase() == 'today') ? new Date () : new Date (Date.parseDate (date));
  
  fmtString = (fmtString || "%l %j %n %Y").replace ('%d', ('0' + date.getDate()).slice(-2));
  fmtString = fmtString.replace ('%D', eyeCalString.dn[date.getDay()]);
  fmtString = fmtString.replace ('%j', date.getDate());
  fmtString = fmtString.replace ('%l', eyeCalString.dname[date.getDay()]);
  fmtString = fmtString.replace ('%N', date.getDay() + 1);
  fmtString = fmtString.replace ('%w', date.getDay());
  
  fmtString = fmtString.replace ('%F', eyeCalString.mname[date.getMonth()+1]);
  fmtString = fmtString.replace ('%m', ('0' + (date.getMonth()+1)).slice (-2));
  fmtString = fmtString.replace ('%M', eyeCalString.mn[date.getMonth()+1]);
  fmtString = fmtString.replace ('%n', eyeCalString.mname[date.getMonth()+1]);
  
  fmtString = fmtString.replace ('%Y', date.getFullYear());
  fmtString = fmtString.replace ('%y', date.getYear ());
  
  return fmtString;  
}
}


if (eyeCal.init) {
  
  if (window.addEventListener)
    window.addEventListener ('load', eyeCal.init, true);
  else if (window.attachEvent)
    window.attachEvent ("onload", eyeCal.init);
}

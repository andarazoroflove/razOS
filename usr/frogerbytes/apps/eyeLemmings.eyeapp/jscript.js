var op = '';
var oppending = false;
var spacer = "                 ";
var dlen = spacer.length;

function eyeCalcKey (n) {
	
   if (typeof (n) != 'string') {
      n = String.fromCharCode (window.event ? window.event.keyCode : n.which);
   }
   
   if ((n == 's') || (n == 'S')) {
      mem = display;
      sysCall ('sys', 'ipc', eyeApp+'][mem='+mem+']');
      document.getElementById ('memory').innerHTML = "M = "+mem;
      return;
   }

   if ((n == '.') ||( n == ',')) {
      if ((display.indexOf('.') >= 0) && ! oppending )
         return;
      else
         dp = n, n = '.';
   }
	 
   else if (n == '\r') 
      n = '=';

   else if (n == '=')   
      document.getElementById ('memory').innerHTML = "M = "+mem;
      
   if ("%+-/*=".indexOf(n) >= 0) {
      if (oppending & (n == '-')) {	   
         n = '';
	 display = '-';
	 oppending = false;
      }
      else {
         if (op) {
            switch (op) {
               case '%': display = display*value/100; break;
               case '+': display = 1*value + 1*display; break;
               case '*': display = value * display; break;
               case '/': display = value / display; break;
               case '-': display = value - display; break;
            }
            display = (''+display).slice(0,dlen);
	    value = 0;
         } 
	 
	 if (n != '=') 
	    value = display;
	 else
            sysCall ('sys', 'ipc', eyeApp+'][disp='+dp+display+']');
         op = n;
         oppending = true;
         n = '';
      } 
   }
   else {
      if ((-1 == "rR0123456789.".indexOf(n)) && (n != dp))
         return;
	 
      if (oppending)
         oppending = false, display = 0;   
   }
   
   if ((n == 'r') || (n == 'R'))
      display = mem;
      
   else if (n) 
      display = ((display == 0) ? '' : display) + n;
   display = (spacer + display).slice(-dlen);
   
   document.getElementById ('display').innerHTML = display.replace(' ', '&nbsp;').replace('.', dp);
}


<!-- START MOTD -->
<div style="margin:2px; padding:2px; border: #073260 1px solid;">
 {MOTD}
</div><!-- END MOTD -->
<div align="left">

  <table border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td algin="left" valign="top" wdith="45%"><!-- START on_account -->
    <script type="text/javascript">
 <!--
 function loadpause()
 {
     document.getElementById('profsub').disabled = 1;
 }
 // -->
    </script>
    <form action="{PHP_SELF}" method="POST" onSubmit="loadpause();">
    <fieldset>
     <legend><strong>{msg_profile}</strong></legend>
     <select name="profile" size="1"><!-- START menline -->
      <option value="{value}"<!-- START sel --> selected<!-- END sel -->>{text}</option><!-- END menline -->
     </select>&nbsp;
     {passthrough_2}
     <input type="hidden" name="action" value="inbox" />
     <input type="hidden" name="mail" value="{mail}" />
     <input type="submit" id="profsub" value="{msg_login}" /><br />
    </fieldset>
    </form><!-- END on_account -->
    </td>
    <td align="left" valign="top">
    {ext_inbox_profright}
    </td>
   </tr>
  </table>
</div><br /><!-- START errorblock -->
<div align="left" class="errorbox"><strong>{error}</strong></div><br />
<!-- END errorblock --><!-- START returnblock -->
<div align="left" class="returnbox"><strong>{return}</strong></div><br />
<!-- END returnblock --><!-- START refresh -->
<a href="{link_refresh}">{msg_refresh}</a><br /><!-- END refresh -->
<!-- START nomailblock -->
<div>
<p class="emptymailbox">{nonewmail}<!-- START profman --><br /><br />
 <a href="{link_profman}">{msg_profman}</a><!-- END profman -->
</p>
</div><!-- END nomailblock --><!-- START mailblock -->
<script type="text/javascript">
 <!--
 function setBoxes(formular, gruppe, anaus)
 {
     var moep = document.forms[formular].elements[gruppe];
     var betroffen = (typeof(moep.length) != 'undefined') ? moep.length : 0;
     if (betroffen) {
         for (var i = 0; i < betroffen; i++) {
             if (anaus == -1) {
                 moep[i].checked = 1 - moep[i].checked;
             } else {
                 moep[i].checked = anaus;
             }
         }
     } else {
         if(anaus == -1) {
             moep.checked = 1 - moep.checked;
         } else {
             moep.checked = anaus;
         }
     }
     return true;
 }

 function switchOnAll(anaus)
 {
    var betroffen = document.getElementsByName('not_all');
    var anzahl = (typeof(betroffen.length) != 'undefined') ? betroffen.length : 0;
    if (anzahl) {
        var reselect = false;
        for (var i = 0; i < anzahl; i++) {
            if (anaus == 1) {
                if (betroffen[i].selected) {
                    reselect = true;
                }
                betroffen[i].style.display = 'none';
                betroffen[i].disabled = true;
             } else {
                betroffen[i].disabled = false;
                betroffen[i].style.display = 'block';
             }
        }
        if (reselect) {
            document.getElementsByName('may_all')[0].selected = true;
        }
    }
    return true;
 }
 
 function disable_jump()
 {
     if (2 > document.getElementById('maxpage').firstChild.data) {
        document.getElementById('submit_jump').disabled = 1;
     }
 }
 // -->
 </script>
<table border="0" cellpadding="1" cellspacing="0" width="100%">
 <tr>
  <td align="left" valign="middle" title="{rawallsize}">{neueingang} {plural} ({allsize})&nbsp;</td>
  <td align="left" valign="middle">
  <form action="{PHP_SELF}" method="POST" style="display:inline">
  <input type="hidden" name="action" value="{action}" />
  {msg_page}&nbsp;{page}/<span id="maxpage">{boxsize}</span>&nbsp;&nbsp;
  <input type="text" size="{size}" maxlength="{maxlen}" name="WP_core_jumppage" value="{page}" />&nbsp;
  <input type="submit" id="submit_jump" value="{go}" />
  {passthrough_2}
  </form>
  <script type="text/javascript">
  <!--
  disable_jump();
  // -->
  </script>
  </td>
  <td align="left">&nbsp;{newmails} {displaystart} - {displayend}</td>
  <td align="right"><!-- START blstblk -->
   <a href="{link_last}">
    <img src="{skin_path}/images/nav_left.png" alt="" title="{but_last}" border=0 />
   </a>&nbsp;<!-- END blstblk --><!-- START bnxtblk -->
   <a href="{link_next}">
    <img src="{skin_path}/images/nav_right.png" alt="" title="{but_next}" border=0 />
   </a><!-- END bnxtblk -->
  </td>
 </tr> 
</table>
<form method="POST" name="InboxForm" action="{PHP_SELF}">
<table border=0 cellpadding=1 cellspacing=0 align="left" width="100%">
 <tr>
  <td class="body">&nbsp;</td>
  <td class="body" width=8>&nbsp;</td>
  <td class="body" align="left">{hsubject}</td>
  <td class="body" align="left">{hfrom}</td>
  <td class="body" align="left">{hdate}</td>
  <td class="body" align="right">{hsize}&nbsp;</td>
 </tr><!-- START maillines -->
 <tr>
  <td><input type="checkbox" name="kmail[]" value="{id}"></td>
  <td style="white-space:nowrap;"><!-- START mark_read -->
   <img src="{skin_path}/mail_read.png" title="" border="0" />&nbsp;<!-- END mark_read --><!-- START mark_unread -->
   <img src="{skin_path}/mail_unread.png" title="" border="0" />&nbsp;<!-- END mark_unread --><!-- START attach -->
   <img src="{attach}" border=0 title="{title}" />&nbsp;<!-- END attach --><!-- START prio -->
   <img src="{prio}" title="{title}" border="0" />&nbsp;<!-- END prio -->
  </td>
  <td align="left"><a href="{viewlink}" title="{subj_title}">{subject}</a></td>
  <td align="left"><a href="{from_1}" title="{from_2}">{from_3}</a></td>
  <td align="left" title="{date}" style="white-space:nowrap;">{short_date}</td>
  <td align="right" title="{rawsize}">{size}{eval_size}<input type="hidden" name="uidl[{id}]" value="{uidl}"></td>
 </tr><!-- END maillines -->
 <tr>
  <td class="body" colspan="5" align="left" valign="top">
   {passthrough_2}
   <input type="hidden" name="oldaction" value="{action}">
   <input type="hidden" name="inboxcount" value="{neueingang}">
   <input type="radio" checked="checked" onChange="switchOnAll(0);" name="kill_mode" value="kill_selection" id="label_1">
   <label for="label_1">&nbsp;{selection}&nbsp;</label>&nbsp;
   <input type="radio" name="kill_mode" onChange="switchOnAll(0);" value="kill_page" id="label_2">
   <label for="label_2">&nbsp;{allpage}&nbsp;</label>&nbsp;<!-- START ifkillall -->
   <input type="radio" name="kill_mode" onChange="switchOnAll(1);" value="kill_all" id="label_3">
   <label for="label_3">&nbsp;{all}&nbsp;</label>&nbsp;<!-- END ifkillall -->
   <select name="action" size="1">
    <option name="may_all" value="kill">{del}</option>
    <option name="not_all" value="bounce">{bounce}</option><!-- START markread_ops -->
    <option name="not_all" value="markread_set">{msg_markreadset}</option>
    <option name="not_all" value="markread_unset">{msg_markreadunset}</option><!-- END markread_ops -->
   </select>&nbsp;
   <input type="submit" value="{go}">{eval_headmenu}
  </td>
  <td class="body" align="right" title="{rawsumsize}">{sumsize}{eval_headsize}</td>
 </tr>
 <tr>
  <td colspan=4 align="left">
   <script type="text/javascript">
   <!--
    document.write('<input class="input" type="button" value="{msg_all}" onClick="setBoxes(\'InboxForm\',\'kmail[]\',1)"> ');
    document.write('<input class="input" type="button" value="{msg_none}" onClick="setBoxes(\'InboxForm\',\'kmail[]\',0)"> ');
    document.write('<input class="input" type="button" value="{msg_rev}" onClick="setBoxes(\'InboxForm\',\'kmail[]\',-1)">');
   // -->
  </script>
 </td>
 <td colspan="2" align="right">{blstblk}&nbsp;{bnxtblk}</td>
</tr>
</table>
</form>
<!-- END mailblock -->
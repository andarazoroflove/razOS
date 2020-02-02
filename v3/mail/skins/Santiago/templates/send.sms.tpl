<!-- START nosender --><br />
<br />
<p class="emptymailbox">{msg_nosender}<br /><br /><a href="{link_setup}">{msg_setup}</a></p>
<!-- END nosender --><!-- START normal -->
<form action="{PHP_SELF}" method="POST" name="SendForm">
<table border="0" cellpadding="1" cellspacing="0" width="100%"><!-- START error -->
 <tr><td valign="top" align="left" width="100%" colspan="2">{error}</td></tr><!-- END error -->
 <tr>
  <td align="left" width="85"><strong>{msg_from}:</strong></td>
  <td align="left">{from}</td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_to}:</strong></td>
  <td align="left">
   <input type="text" name="WP_send[to]" value="{to}" size="56" maxlength="255" /> {ext_sms_to}
  </td>
 </tr>
 <tr>
  <td>&nbsp;</td><td align="left">
   <input type="checkbox" id="lbl_savesent" name="save_sent" value="1"<!-- START savesent_check --> checked="checked"<!-- END savesent_check -->><label for="lbl_savesent">&nbsp;{msg_copytobox}:</label>&nbsp;<!-- START copy_more --><select name="profile" size="1"><!-- START profline -->
    <option value="{id}"<!-- START sel --> selected="selected"<!-- END sel -->>{profile}</option><!-- END profline -->
   </select><!-- END copy_more --><!-- START copy_one -->{from}&nbsp;({address}) <input type=hidden name="profile" value="{profile}"><!-- END copy_one --><br /><br />
  </td>
 </tr>
 <tr>
  <td align="left" colspan="2" valign="top">
   <input type="text" size="4" maxlength="4" name="counter" id="counter" disabled="disabled" /> / 160 {msg_charsleft}<br />
   <textarea cols="64" rows="5" name="WP_send[body]" id="body" onKeyUp="update_counter();">{body}</textarea>
   <script type="text/javascript">
   <!--
   function update_counter()
   {
       var curr = document.forms[0].body.value.length;
       document.forms[0].counter.value = 160-curr;
   }
   update_counter();

   // -->
   </script>
  </td>
 </tr><!-- START mms -->
 <tr>
  <td align="left" valign="top"><strong>{msg_attach}:</strong></td>
  <td align="left">
   <input type="file" name="WP_upload[1]" size="48" />{ext_send_attach}<br />
   <input type="file" name="WP_upload[2]" size="48" />{ext_send_attach}<br />
   <input type="file" name="WP_upload[3]" size="48" />{ext_send_attach}<br />
   <input type="hidden" name="max_file_size" value="{maxupload}">
  </td>
 </tr><!-- END mms --><!-- START attachblock -->
 <tr class="body"><td valign="top" colspan="2"><strong>{msg_attachs}</strong></td></tr>
 <tr class="body">
  <td valign="top">{msg_selection} --&gt;<br />
   <script type="text/javascript">
   <!--
   function setBoxes(formular, gruppe, anaus)
   {
       var moep = document.forms[formular].elements[gruppe];
       var betroffen = (typeof(moep.length) != 'undefined') ? moep.length : 0;
       if(betroffen) {
           for (var i=0; i < betroffen; i++) {
               moep[i].checked = anaus;
           }
       } else {
           moep.checked = anaus;
       }
       return true;
   }
   document.write('<input class="input" type="button" value="{msg_all}" onClick="setBoxes(\'SendForm\',\'WP_send[attach][]\',1)"><br><br>');
   document.write('<input class="input" type="button" value="{msg_none}" onClick="setBoxes(\'SendForm\',\'WP_send[attach][]\',0)"><br><br>');
   // -->
   </script>
  </td>
  <td align="left" valign="top">
   <table border=0 cellpadding=0 cellspacing=1 valign="top" align="left"><!-- START attachline -->
    <tr>
     <td align="left">
      <input type="checkbox" name="WP_send[attach][]" value="{att_num}"<!-- START attsel --> checked<!-- END attsel --> />&nbsp;
      <img src="{att_icon}" align="absmiddle" alt="{att_icon_alt}"> {att_name},&nbsp;{att_size}, {msg_att_type}: {att_type}
     </td>
    </tr><!-- END attachline -->
   </table>
  </td>
 </tr><!-- END attachblock -->
 <tr>
  <td align="left" colspan="2">
   <input type="submit" name="WP_send[send_action]" value="{msg_send}" />
  </td>
 </tr>
</table>
{passthrough_2}
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="oldaction" value="{oldaction}">
</form><br /><!-- END normal --><!-- START stats -->
<br />
<fieldset>
 <legend><strong>{leg_smsstat}</strong></legend>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr>
   <td align="left">{msg_curruse}:</td>
   <td align="left">{curr_use} {msg_sms} ({msg_approx} {curr_approx}/{msg_month})</td>
  </tr><!-- START iffree -->
  <tr>
   <td align="left">{msg_freesms}:</td>
   <td align="left">{free_used}/{free_given}</td>
  </tr><!-- END iffree -->
  <tr>
   <td align="left">{msg_lastuse}:</td>
   <td align="left">{last_use} {msg_sms}</td>
  </tr>
 </table>
</fieldset><!-- END stats --><!-- START on_send -->
<table border="0" cellpadding="1" cellspacing="0" width="100%">
 <tr><td colspan="2" align="center"><h5>{msg_sending}</h5></td></tr>
 <tr><td colspan="2" align="center"><strong>{msg_redir}</strong></td></tr><!-- START more -->
 <tr><td align="left"><strong>{msg_listsize}:</strong>&nbsp;</td><td align="left">{curr} / {listsize}</td></tr><!-- END more -->
 <tr><td align="left"><strong>{msg_status}:</strong>&nbsp;</td><td align="left">{status}</td></tr>
 <tr><td align="left"><strong>{msg_from}:</strong>&nbsp;</td><td align="left">{from}</td></tr>
 <tr><td align="left"><strong>{msg_to}:</strong>&nbsp;</td><td align="left">{to}</td></tr>
 <tr>
  <td align="left" valign="top"><strong>{msg_text}:</strong>&nbsp;</td>
  <td align="left" valign="top">{text}</td>
 </tr>
</table><!-- END on_send -->
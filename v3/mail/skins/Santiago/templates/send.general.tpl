<form action="{PHP_SELF}" method="POST" name="SendForm" enctype="multipart/form-data" onSubmit="return checkForm();">
<table border="0" cellpadding="1" cellspacing="0" align="left" width="100%"><!-- START error -->
 <tr><td valign="top" align="left" colspan="2">{error}</td></tr><!-- END error --><!-- START full -->
 <tr>
  <td align="left" width="85"><b>{msg_from}:</b></td>
  <td align="left"><!-- START on_manual -->
   <input type="text" name="WP_send[from]" value="{from}" size="{input_sendfrom}" maxlength="255" class="input">&nbsp;<!-- END on_manual --><!-- START on_account -->
   <select name="WP_send[from_profile]" size=1><!-- START on_man2 -->
    <option value="">* {msg_manual} *</option><!-- END on_man2 --><!-- START accmenu -->
    <option value="{counter}" <!-- START selected -->selected<!-- END selected -->>{profilenm}</option><!-- END accmenu -->
   </select>&nbsp;&nbsp;
   <input type="submit" onMouseOver="nocheck=true;" onMouseOut="nocheck=false;" name="WP_send[send_sig]" value="{msg_sigload}"><!-- END on_account -->
<!-- START one_account -->
   {from}&nbsp;({address}) <input type="hidden" name="WP_send[from_profile]" value="{profile}">
<!-- END one_account -->
  </td>
 </tr>
 <tr>
  <td align="left"><b>{msg_to}:</b></td>
  <td align="left">
   <input type="text" name="WP_send[to]" value="{to}" size="60" maxlength="255" /> {ext_send_to}
  </td>
 </tr>
 <tr>
  <td align="left"><b>CC:</b></td>
  <td align="left">
   <input type="text" name="WP_send[cc]" value="{cc}" size="60" maxlength="255" />
  </td>
 </tr>
 <tr>
  <td align="left"><b>BCC:</b></td>
  <td align="left">
   <input type="text" name="WP_send[bcc]" value="{bcc}" size="60" maxlength="255" />&nbsp;&nbsp;<!-- START addrcheck -->
   <input type="submit" onMouseOver="nocheck=true;" onMouseOut="nocheck=false;" name="WP_send[send_valid]" value="{msg_valid}"><!-- END addrcheck -->
  </td>
 </tr>
 <tr>
  <td align="left"><b>{msg_subject}:</b></td>
  <td align="left">
   <input type="text" name="WP_send[subj]" id="subject" value="{subject}" size="60" maxlength="255" />
   <script type="text/javascript">
   <!--
   function checkForm()
   {
       if (nocheck == true) {
           return true;
       }
       var subject = document.getElementById('subject');
       if (subject.value == '') {
           return confirm("{msg_confirm_no_subject}");
       }
   }
   var nocheck = false;
   // -->
   </script>
  </td>
 </tr>
 <tr>
  <td align="left"><b>{msg_prio}:</b></td>
  <td align="left">
   <select name="WP_send[importance]" style="vertical-align: top;"><!-- START priomen -->
    <option value="{prioval}"<!-- START priosel --> selected<!-- END priosel -->>{priotxt}</option><!-- END priomen -->
   </select>
  </td>
 </tr>
  <tr>
   <td> </td>
   <td align="left">
    <input type="checkbox" id="lbl_savesent" name="save_sent" value="1"<!-- START savesent_check --> checked<!-- END savesent_check -->>
    <label for="lbl_savesent">&nbsp;{msg_copytobox}</label><br />
    <input type="checkbox" id="lbl_receipt" name="receipt_out" value="1"<!-- START receipt_check --> checked<!-- END receipt_check -->>
    <label for="lbl_receipt">&nbsp;{msg_receipt_out}</label>
  </td>
 </tr>
 <tr>
  <td align="left" colspan="2" valign="top">
   <textarea cols="80" rows="16" name="WP_send[body]" id="body">{body}</textarea>&nbsp;
   <script type="text/javascript">
   <!--
   document.write ('<input class="input" type="button" value="{msg_del}" onClick="this.form.body.value=\'\';">');
   // -->
   </script>
  </td>
 </tr>
 <tr>
  <td align="left" valign="top"><b>{msg_attach}:</b></td>
  <td align="left">
   <input type="file" name="WP_upload[1]" size="64" />{ext_send_attach}<br />
   <input type="file" name="WP_upload[2]" size="64" />{ext_send_attach}<br />
   <input type="file" name="WP_upload[3]" size="64" />{ext_send_attach}<br />
   <input type="hidden" name="max_file_size" value="{maxupload}">
  </td>
 </tr>
 <tr>
  <td align="left" valign="top"><b>{msg_sig}:</b></td>
  <td valign="top" align="left">
   <textarea cols="63" rows="5" name="WP_send[sign]" id="sign">{sign}</textarea>&nbsp;
   <script type="text/javascript">
   <!--
   document.write('<input class="input" type="button" value="{msg_del}" onClick="this.form.sign.value=\'\';">');
   // -->
   </script>
  </td>
 </tr><!-- START attachblock -->
 <tr class="body"><td valign="top" colspan="2"><b>{msg_attachs}</b></td></tr>
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
 </tr><!-- END attachblock --><!-- END full --><!-- START on_bounce -->
 <tr><td colspan="2" align="center"><h5>{msg_bounce}</h5></td></tr><!-- START redir -->
 <tr><td colspan="2" align="center"><b>{msg_redir}</b></td></tr><!-- END redir --><!-- START header -->
 <tr><td align="left"><b>{msg_still}:</b>&nbsp;</td><td align="left">{still}</td></tr>
 <tr><td align="left"><b>{msg_from}:</b>&nbsp;</td><td align="left">{from}</td></tr>
 <tr><td align="left"><b>{msg_to}:</b>&nbsp;</td><td align="left">{to}</td></tr>
 <tr><td align="left"><b>{msg_subj}:</b>&nbsp;</td><td align="left">{subj}</td></tr>
 <tr><td colspan="2">&nbsp;</td></tr><!-- END header -->
 <tr>
  <td align="left"><b>{msg_bounceto}:</b></td>
  <td align="left">
   <input type="text" name="WP_send[to]" value="{bounceto}" size="56" maxlength="255" />{ext_send_to}
   <script type="text/javascript">
   <!--
   function checkForm()
   {
       return true;
   }
   // -->
   </script>
  </td>
 </tr>
 <tr>
  <td align="left">&nbsp;</td>
  <td align="left">
   <input type="checkbox" name="deleorig" value="1"<!-- START delesel --> checked<!-- END delesel --> id="label_delor" />
   <label for="label_delor">{deleorig}</label><input type="hidden" name="WP_send[subj]" value="{subject}">
  </td>
 </tr><!-- START forothers -->
 <tr>
  <td align="left">&nbsp;</td>
  <td align="left">
   <input type="checkbox" name="doothers" value="1" <!-- START forsel --> checked<!-- END forsel -->id="label_others" />
   <label for="label_others">{msg_doothers}</label>
  </td>
 </tr><!-- END forothers --><!-- END on_bounce -->
 <tr>
  <td align="left" colspan="2">
   <input type="submit" name="WP_send[send_action]" value="{msg_send}" />
  </td>
 </tr><!-- START bouncebreaker -->
 <tr>
  <td align="right" colspan="2"><a href="{link_target}">{cancel}</a></td>
 </tr><!-- END bouncebreaker -->
</table>
{passthrough_2}
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="oldaction" value="{oldaction}">
<input type="hidden" name="mail" value="{mail}">
<input type="hidden" name="profile" value="{from_profile}">
<input type="hidden" name="WP_send[sendway]" value="{sendway}">
</form>
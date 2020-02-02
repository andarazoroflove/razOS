<!-- START returnblock --><strong>{return}</strong><br />
<br /><!-- END returnblock -->
<script type="text/javascript">
<!--
function copyf(from, to)
{
    document.getElementsByName(to)[0].value = document.getElementsByName(from)[0].value;
}
// -->
</script>
<form action="{PHP_SELF}" method="post">
<table border="0" cellpadding="2" cellspacing="0">
 <tr>
  <td><strong>{msg_profile}:</strong></td>
  <td><input type="text" name="popname" size="24" value="{profilename}" maxlength="64" /></td>
 </tr>
 <tr>
  <td>{msg_email}:</td>
  <td><input type="text" name="address" size="24" value="{email}" maxlength="64" /></td>
 </tr>
 <tr>
  <td>{msg_realname}:</td>
  <td><input type="text" name="real_name" size="24" value="{realname}" maxlength="64" /></td>
 </tr>
 <tr>
  <td colspan="2" align="left">
   <input type="checkbox" id="lbl_accon" name="acc_on" value="1"<!-- START acc_ck --> checked="checked"<!-- END acc_ck --> />
   <label for="lbl_accon">{msg_inmenu}</label><br />
   <input type="text" name="killsleep" size="1" value="{killsleep}" maxlength="2" />  {msg_killsleep}<br />
   <br />
  </td>
 </tr>
 <tr class="body">
  <td colspan=2><strong>POP3</strong></td>
 </tr>
 <tr class="body">
  <td>{msg_popserver}:</td>
  <td>
   <input type="text" name="popserver" size="24" value="{popserver}" maxlength="64" />
   <img src="{skin_path}/images/nav_down.png" alt="" title="{copy_smtp}" style="cursor:pointer;" onClick="copyf('popserver', 'smtp_host')" />
  </td>
 </tr>
 <tr class="body">
  <td>{msg_popport}:</td>
  <td><input type="text" name="popport" size="24" value="{popport}" maxlength="64" /></td>
 </tr>
 <tr class="body">
  <td>{msg_popuser}:</td>
  <td>
   <input type="text" name="popuser" size="24" value="{popuser}" maxlength="64" />
   <img src="{skin_path}/images/nav_down.png" alt="" title="{copy_smtp}" style="cursor:pointer;" onClick="copyf('popuser', 'smtp_user')" />
  </td>
 </tr>
 <tr class="body">
  <td>{msg_poppass}:
   {passthrough_2}
   <input type="hidden" name="action" value="{action}" />
   <input type="hidden" name="mode" value="{mode}" />
   <input type="hidden" name="account" value="{account}" />
  </td>
  <td>
   <input type="password" name="poppass" size="24" value="{poppass}" maxlength="64" />
   <img src="{skin_path}/images/nav_down.png" alt="" title="{copy_smtp}" style="cursor:pointer;" onClick="copyf('poppass', 'smtp_pass')" />
  </td>
 </tr>
 <tr class="body">
  <td>{msg_popapop}:</td>
  <td>
   <select name="popapop" size="1">
    <option value="">{msg_auto}</option>
    <option value="1"<!-- START apopsel --> selected="selected"<!-- END apopsel -->>{msg_no}</option>
   </select>
  </td>
 </tr>
 <tr>
  <td colspan=2>&nbsp;</td>
 </tr>
 <tr class="body">
  <td colspan=2><strong>SMTP</strong></td>
 </tr>
 <tr class="body">
  <td align="left">{msg_smtphost}:</td>
  <td align="left">
   <input type="text" size="24" name="smtp_host" value="{smtp_host}" />
   <img src="{skin_path}/images/nav_up.png" alt="" title="{copy_pop3}" style="cursor:pointer;" onClick="copyf('smtp_host', 'popserver')" />
  </td>
 </tr>
 <tr class="body">
  <td align="left">{msg_smtpport}:</td>
  <td align="left"><input type="text" size="24" name="smtp_port" value="{smtp_port}" /></td>
 </tr>
 <tr class="body">
  <td align="left">{msg_smtpuser}:</td>
  <td align="left">
   <input type="text" size="24" name="smtp_user" value="{smtp_user}" />
   <img src="{skin_path}/images/nav_up.png" alt="" title="{copy_pop3}" style="cursor:pointer;" onClick="copyf('smtp_user', 'pop_user')" />
  </td>
 </tr>
 <tr class="body">
  <td align="left">{msg_smtppass}:</td>
  <td align="left">
   <input type="password" size="24" name="smtp_pass" value="{smtp_pass}" />
   <img src="{skin_path}/images/nav_up.png" alt="" title="{copy_pop3}" style="cursor:pointer;" onClick="copyf('smtp_pass', 'poppass')" />
  </td>
 </tr>
 <tr class="body">
  <td align="left" colspan="2">
   <input type="checkbox" id="lbl_smtpafterpop" name="smtpafterpop" value="1"<!-- START smtpafterpop --> checked="checked"<!-- END smtpafterpop --> />
   <label for="lbl_smtpafterpop">SMTP after POP</label><br />
  </td>
 </tr>
 <tr>
  <td colspan="2">&nbsp;</td>
 </tr>
 <tr>
  <td colspan="2" align="left">
   <input type="checkbox" id="lbl_sigon" name="sig_on" value="1"<!-- START sig_ck --> checked="checked"<!-- END sig_ck --> />
   <label for="lbl_sigon"><strong>{msg_sigon}</strong></label><br />
   <textarea cols="60" rows="4" name="signature">{signature}</textarea><br />
  </td>
 </tr>
 <tr>
  <td align="left"><br />
   <input type="submit" value="{msg_save}"><br /><br />
  </td>
  <td align="right">
   <a href="{PHP_SELF}?{passthrough}&amp;action=setup&amp;mode=edit">{msg_cancel}</a>
  </td>
 </tr>
</table>
</form>
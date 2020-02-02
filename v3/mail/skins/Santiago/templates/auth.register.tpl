<form action="{PHP_SELF}?{passthrough}&special=register_me" method="POST">
<table border="0" cellpadding="2" cellspacing="0">
<tr>
 <td align="center" colspan="2"><b>{msg_register}</b><br /></td>
</tr>
<!-- START error --><tr>
 <td align="left" colspan=2><b>{error}</b></td></tr><!-- END error -->
<tr>
 <td align="left">{msg_popuser}</td>
 <td align="left">
  <input type="text" size="16" maxlength="32" name="PHM[username]" value="{username}" />
 </td>
</tr>
<tr>
 <td align="left">{msg_syspass}</td>
 <td align="left">
  <input type="password" size="16" maxlength="32" name="PHM[password]" value="{password}" />
 </td>
</tr>
<tr>
 <td align="left">{msg_syspass2}</td>
 <td align="left">
  <input type="password" size="16" maxlength="32" name="PHM[password2]" value="{password2}" />
 </td>
</tr>
<tr>
 <td align="left">{msg_email}</td>
 <td align="left">
  <input type="text" size="32" maxlength="255" name="PHM[externalemail]" value="{email}" />
 </td>
</tr>
<tr>
 <td>&nbsp;</td>
 <td align="left"><input type="submit" value="{msg_register}" /></td>
</tr>
<tr>
 <td colspan="2" align="right"><a href="{PHP_SELF}">{msg_cancel}</a></td>
</tr>
</table>
</form>
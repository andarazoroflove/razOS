<form action="{PHP_SELF}?{passthrough}&special=activate" method="POST">
<input type="hidden" name="WPuid" value="{uid}" />
<table border="0" cellpaddin="2" cellspacing="0">
<tr>
 <td align="left" valign"top" colspan="2">{msg_actnow}<br /></td>
</tr><!-- START error -->
<tr>
 <td align="left" colspan="2" class="texterror"><strong>{error}</strong></td>
</tr><!-- END error -->
<tr>
 <td align="left">{msg_popuser}</td>
 <td align="left"><input type="text" size="16" maxlength="32" name="username" value="{username}" /></td>
</tr>
<tr>
 <td align="left">{msg_syspass}</td>
 <td align="left"><input type="password" size="16" maxlength="32" name="password" value="{password}" /></td>
</tr>
<tr>
 <td>&nbsp;</td>
 <td align="left"><input type="submit" value="{msg_activate}" /></td>
</tr>
</table>
</form>
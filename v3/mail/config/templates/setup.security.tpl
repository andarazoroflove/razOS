<form action="{target_link}" method=POST>
<div align="left">
{head_text}<br />
<br />
<b>{WP_return}</b><br />
<br /><!-- START sessionip --><!-- END sessionip -->
<fieldset>
 <legend><b>{leg_wronglogin}</b></legend>
 {about_wronglogin}<br />
 <br />
 <table cellspacing="0" cellpading="2" border="0">
  <tr>
   <td align="left">{msg_waitonfail}:</td>
   <td align="left">
    <input type="tex"t name="WP_newwaitfail" style="text-align:right;" value="{waitonfail}" size="8" maxlength="8" />
   </td>
  </tr>
  <tr><td align="left"><input type=submit value="{msg_save}"></td>&nbsp;</td></tr>
 </table>
</fieldset>
</div>
</form>
<form action="{target}" method="POST">
{msg_adbadd}:<br />
<br />
<table border="0" cellpadding="2" cellspacing="0">
 <tr>
  <td align="left"><strong>{msg_group}:</strong></td>
  <td align="left">
   <select size="1" name="WP_plug_adbfield[gid]">
    <option value="">&lt; {msg_none} &gt;</option><!-- START groupline -->
    <option value="{id}"<!-- START selected --> selected="selected"<!-- END selected -->>{name}</option><!-- END groupline -->
   </select>
  </td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_nick}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[nick]" value="{nick}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_fnam}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[firstname]" value="{firstname}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_snam}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[lastname]" value="{lastname}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_email1}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[email1]" value="{email1}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_email2}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[email2]" value="{email2}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_www}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[www]" value="{www}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_fon}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[tel_private]" value="{tel_private}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_fon2}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[tel_business]" value="{tel_business}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_cell}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[cellular]" value="{cellular}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_fax}:</strong></td>
  <td align="left"><input type="text" size=32 name="WP_plug_adbfield[fax]" value="{fax}"></td>
 </tr>
 <tr>
  <td align="left"><strong>{msg_bday}:</strong></td>
  <td align="left">
   <select name="WP_plug_adbfield[birthday][day]" size="1"><!-- START bday_dayline -->
    <option value="{day}"<!-- START selected --> selected="selected"<!-- END selected -->>{day}</option><!-- END bday_dayline -->
   </select>&nbsp;
   <select name="WP_plug_adbfield[birthday][month]" size="1"><!-- START bday_monthline -->
    <option value="{month}"<!-- START selected --> selected="selected"<!-- END selected -->>{month}</option><!-- END bday_monthline -->
   </select>&nbsp;
   <input type="text" size="4" name="WP_plug_adbfield[birthday][year]" value="{birthday_year}">
   &nbsp;{msg_bday_format}
   </td>
 </tr>
 <tr>
  <td align="left" valign="top"><strong>{msg_address}:</strong></td>
  <td align="left"><textarea rows=4 cols=20 name="WP_plug_adbfield[address]">{address}</textarea></td>
 </tr>
 <tr>
  <td align="left" valign="top"><strong>{msg_cmnt}:</strong></td>
  <td align="left"><textarea rows=4 cols=40 name="WP_plug_adbfield[comments]">{comments}</textarea></td>
 </tr>
</table>
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="mail" value="{mail}">
<input type="hidden" name="WP_plug[adb_action]" value="{adb_action}">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="WP_plug[adb_do]" value="{adb_do}">
<input type="hidden" name="WP_plug[adb_mode]" value="{adb_mode}">
{passthrough}
<input type="submit" name="WP_plug[adb_done]" value="{msg_save}">
</form>
<div align="left">
 <form action="{target}" method="POST" enctype="multipart/form-data">
 <fieldset style="width: 400">
  <legend><strong>{leg_import}</strong></legend>
  {about_import}<br />
  <br />
  <table border="0" cellpadding="2" cellspacing="0">
  <tr>
   <td align="left">{msg_format}:</td>
   <td align="left">
    <select name="WP_plug[adb_imform]" size="1">
     <option value="">--- {msg_select} ---</option><!-- START imoption -->
     <option value="{value}">{name}</option><!-- END imoption -->
    </select>
    <input type=hidden name="WP_plug[adb_do]" value="import">
    <input type=hidden name="WP_plug[adb_action]" value="{adb_action}">
    <input type=hidden name="WP_plug[adb_mode]" value="{adb_mode}">
    <input type=hidden name="action" value="{action}">
    {passthrough}
   </td>
  </tr>
  <tr>
   <td align="left">{msg_file}:</td>
   <td align="left">
    <input type="file" name="WP_plug_adb_imfile" size=32>
   </td>
  </tr>
  <tr>
   <td></td>
   <td align="left">
   <fieldset><legend><strong>{msg_csv_only}</strong></legend>
    <input type="checkbox" name="fieldnames" id="im_fieldnames" value="1" />
    <label for="im_fieldnames">{msg_fieldnames}</label><br />
    <input type="checkbox" name="is_quoted" id="im_quoted" value="1" />
    <label for="im_quoted">{msg_csv_quoted}</label><br />
    {msg_field_delimiter}:&nbsp;<input type="text" name="delimiter" value=";" size="1" maxlength="1" /><br />
    </fieldset>
   </td>
  </tr>
  <tr>
   <td></td>
   <td align="left">
    <input type="submit" value="Go!">
   </td>
  </tr>
  </table>
 </fieldset>
 </form><br />
 <br /><!-- START export -->
 <form action="{target}" method="POST">
 <fieldset style="width: 400">
  <legend><strong>{leg_export}</strong></legend>
  {about_export}<br />
  <br />
  <table border="0" cellpadding="2" cellspacing="0">
  <tr>
   <td align="left">{msg_format}:</td>
   <td align="left">
    <select name="WP_plug[adb_exform]" size=1>
     <option value="">--- {msg_select} ---</option><!-- START exoption -->
     <option value="{value}">{name}</option><!-- END exoption -->
    </select><br />
    <input type=hidden name="WP_plug[adb_do]" value="export">
    <input type=hidden name="WP_plug[adb_action]" value="{adb_action}">
    <input type=hidden name="WP_plug[adb_mode]" value="{adb_mode}">
    <input type=hidden name="action" value="{action}">
    {passthrough}
   </td>
  </tr>
  <tr>
   <td></td>
   <td align="left">
   <fieldset><legend><strong>{msg_csv_only}</strong></legend>
    <input type="checkbox" name="fieldnames" id="ex_fieldnames" value="1" />
    <label for="ex_fieldnames">{msg_fieldnames}</label><br />
    <input type="checkbox" name="is_quoted" id="ex_quoted" value="1" />
    <label for="ex_quoted">{msg_csv_quoted}</label><br />
    {msg_field_delimiter}:&nbsp;<input type="text" name="delimiter" value=";" size="1" maxlength="1" /><br />
    </fieldset>
   </td>
  </tr>
  <tr>
   <td></td>
   <td align="left">
    <input type="submit" value="Go!">
   </td>
  </tr>
  </table>
 </fieldset>
 </form><!-- END export -->
</div>
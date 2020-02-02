<form action="{target_link}" method="POST"><!-- START return -->
<b>{WP_return}</b><br />
<br /><!-- END return -->
{head_text}<br />
<br />
<fieldset>
<legend><b>{leg_general}</b></legend>
<table border="0" cellpadding="2" cellspacing="0">
 <tr>
  <td align="left">{msg_optskin}:</td>
  <td align="left">
   <select name="WP_newskin"><!-- START skinline -->
    <option value="{key}"<!-- START sel --> selected<!-- END sel -->>{skinname}</option><!-- END skinline -->
   </select>
  </td>
 </tr>
 <tr>
  <td align="left">{msg_optlang}:</td>
  <td align="left">
   <select name="WP_newlang"><!-- START langline -->
    <option value="{key}"<!-- START sel --> selected<!-- END sel -->>{langname}</option><!-- END langline -->
   </select>
  </td>
 </tr>
 <tr>
  <td align="left">{msg_opttele}:</td>
  <td align="left">
   <select name="WP_newtele">
    <option value="pro"<!-- START teleprosel --> selected<!-- END teleprosel -->>{msg_txt_prop}</option>
    <option value="sys"<!-- START telesyssel --> selected<!-- END telesyssel -->>{msg_txt_syst}</option>
   </select>
  </td>
 </tr>
 </table><br />
</fieldset>
<br />
<fieldset>
 <legend><b>{leg_copytobox}</b></legend>
 <input type="checkbox" name="WP_newsavesent" id="lbl_savesent" value="1"<!-- START savesent --> checked<!-- END savesent --> />
 <label for="lbl_savesent">&nbsp;{msg_optcopybox}</label><br />
 <br />
 {about_copytobox}<br />
</fieldset>
<br />
<fieldset>
 <legend><b>{leg_receipt}</b></legend>
 <input type="checkbox" name="WP_newreceiptout" id="lbl_receipt" value="1"<!-- START receipt --> checked<!-- END receipt --> />
 <label for="lbl_receipt">&nbsp;{msg_optreceipt}</label><br />
 <br />
 {about_receipt}<br />
</fieldset>
<br />
<fieldset>
 <legend><b>{leg_wrap}</b></legend>
 <input type="checkbox" name="WP_newsendwordwrap" id="lbl_wrap" value="1"<!-- START wordwrap --> checked<!-- END wordwrap --> />
 <label for="lbl_wrap">&nbsp;{msg_optwrap}</label><br />
 <br />
 {about_wrap}<br />
</fieldset>
<br />
<input type="submit" value="{msg_save}">
</form>
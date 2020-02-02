<form action="{target_link}" method="POST">
 <table cellspacing="0" cellpadding="2" border="0">
 <tr><td align="left" colspan=2><b>{WP_return}</b></td></tr>
 <tr><td align="left" colspan=2>{head_text}</td></tr>
 <tr>
  <td align="left">{msg_optskin}:</td>
  <td align="left">
   <select name="WP_newskin" size="1"><!-- START skinline -->
    <option value="{skinname}"<!-- START sel --> selected<!-- END sel -->>{skinname}</option><!-- END skinline -->
   </select>
  </td>
 </tr>
 <tr>
  <td align="left">{msg_optlang}:</td>
  <td align="left">
   <select name="WP_newlang" size="1"><!-- START langline -->
    <option value="{langname}"<!-- START sel --> selected<!-- END sel -->>{langname}</option><!-- END langline -->
   </select>
  </td>
 </tr>
 <tr>
  <td align="left">{msg_opttele}:</td>
  <td align="left">
   <select name="WP_newtele" size="1">
    <option value="pro"<!-- START teleprosel --> selected<!-- END teleprosel -->>{msg_txt_prop}</option>
    <option value="sys"<!-- START telesyssel --> selected<!-- END telesyssel -->>{msg_txt_syst}</option>
   </select>
  </td>
 </tr>
 <tr>
  <td align="left" colspan=2>
   <input type="checkbox" name="WP_newsavesent" value="1" id="lbl_copy"<!-- START savesent --> checked<!-- END savesent -->>
   <label for="lbl_copy">&nbsp;{msg_optcopybox}</label>
  </td>
 </tr>
 <tr>
  <td align="left" colspan=2>
   <input type="checkbox" name="WP_newreceiptout" value="1" id="lbl_receipt"<!-- START receipt --> checked<!-- END receipt -->>
   <label for="lbl_receipt">&nbsp;{msg_optreceipt}</label>
  </td>
 </tr>
 <tr>
  <td align="left" colspan=2>
   <input type="checkbox" name="WP_newsendwordwrap" value="1" id="lbl_wrap"<!-- START wordwrap --> checked<!-- END wordwrap -->>
   <label for="lbl_wrap">&nbsp;{msg_optwrap}</label>
  </td>
  </tr>
  <tr><td colspan=2>&nbsp;</td></tr>
  <tr>
   <td align="left">{msg_email}</td>
   <td align="left"><input type="text" name="WP_newemail" size=16 maxlength=255 value="{email}" /></td>
  </tr><!-- START smssender -->
  <tr>
   <td align="left">{msg_smssender}</td>
   <td align="left"><input type="text" name="WP_newsmsssender" size=16 maxlength=255 value="{sms_sender}" /></td>
  </tr><!-- END smssender -->
  <tr><td colspan=2>&nbsp;</td></tr>
  <tr>
   <td align="left">{msg_newpw}</td>
   <td align="left"><input type="password" name="WP_newpw" size=16 maxlength=32 /></td>
  </tr>
  <tr>
   <td align="left">{msg_newpw2}</td>
   <td align="left"><input type="password" name="WP_newpw2" size=16 maxlength=32 /></td>
  </tr>
  <tr><td colspan=2>&nbsp;</td></tr>
  <tr>
   <td align="left"><input type="submit" value="{msg_save}"></td>
   <td align="right"><a href="{link_base}">{msg_cancel}</a></td>
  </tr>
 </table>
</form>
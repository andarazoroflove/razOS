<table border="0" cellpadding="2" cellspacing="0" width="90%">
 <tr>
  <td colspan="2">
   <table width="100%" border="0" cellpadding="2" cellspacing="0">
    <tr class="body">
     <td><strong>{msg_account}</strong></td>
     <td><strong>{msg_menu}</strong></td>
     <td colspan="2"><strong>APOP</strong></td>
    </tr><!-- START menline -->
    <tr>
     <td><a href="{PHP_SELF}?{passthrough}&amp;action=setup&amp;mode=edit&amp;account={counter}">{profilenm}</a></td>
     <td>{acc_on}</td>
     <td>{popapop}</td>
     <td><a href="{PHP_SELF}?{passthrough}&amp;action=setup&amp;mode=kill&amp;account={counter}">{msg_del}</a></td>
    </tr><!-- END menline -->
   </table>
  </td>
 </tr>
 <tr class="body">
  <td align="left"><a href="{PHP_SELF}?{passthrough}&amp;action=setup&amp;mode=add&amp;account={counter}">{msg_addacct}</a></td>
  <td align="right"><a href="{PHP_SELF}?{passthrough}&amp;action=setup">{msg_backLI}</a></td>
 </tr>
</table><br />
<br />
<form action="{form_target}" method="POST">
<fieldset>
 <legend><strong>{msg_defacc}</strong></legend>
 {about_defacc}<br />
 <br />
 <strong>{msg_defacc}:</strong>&nbsp;
 <select name="def_prof" size="1">
  <option value="0">{msg_notdef}</option><!-- START profline -->
  <option value="{id}"<!-- START sel --> selected="selected"<!-- END sel -->>{name}</option><!-- END profline -->
 </select>&nbsp;
 <input type="submit" value="{msg_save}" />
</fieldset>
</form>
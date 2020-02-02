{head_text}<br />
<form action="{target_link}" method="POST">
<b>{WP_return}</b><br />
<br /><!-- START online_yes --><!-- END online_yes --><!-- START online_no --><!-- END online_no -->
<fieldset><legend><b>{leg_providername}</b></legend>
 {msg_providername}:&nbsp;<input type="text" size=32 name="WP_newprovidername" value="{providername}" /><br />
 <br />
 {about_providername}<br />
 <br />
</fieldset>
<br />
<fieldset>
 <legend><b>{msg_optsendmethod}:</b>&nbsp;
  <select name="WP_newsendmethod" onChange="hide();" id="sendmethod">
   <option value="sendmail"<!-- START methsmsel --> selected<!-- END methsmsel -->>Sendmail</option>
   <option value="smtp"<!-- START methsmtpsel --> selected<!-- END methsmtpsel -->>SMTP</option>
  </select>
 </legend>
 <br />
 {about_sendmethod}<br />
 <br />
 <fieldset><legend><b>Sendmail</b></legend>
 <span id="hide_sendmail">{msg_fillin_sm}<br /><br /></span>
 {msg_path}:&nbsp;<input type="text" size=24 name="WP_newsendmail" value="{sendmail}" /><br />
 </fieldset>
 <br />
 <fieldset><legend><b>SMTP</b></legend>
 <span id="hide_smtp">{msg_fillin_smtp}<br /><br /></span>
 <table border="0" cellpadding="2" cellspacing="0">
  <tr>
   <td align="left">{msg_smtphost}:</td>
   <td align="left"><input type="text" size=24 name="WP_newsmtphost" value="{smtphost}" /></td>
  </tr>
  <tr>
   <td align="left">{msg_smtpport}:</td>
   <td align="left"><input type="text" size=24 name="WP_newsmtpport" value="{smtpport}" /></td>
  </tr>
  <tr>
   <td align="left">{msg_smtpuser}:</td>
   <td align="left"><input type="text" size=24 name="WP_newsmtpuser" value="{smtpuser}" /></td>
  </tr>
  <tr>
   <td align="left">{msg_smtppass}:</td>
   <td align="left"><input type="password" size=24 name="WP_newsmtppass" value="{smtppass}" /></td>
  </tr>
 </table>
 </fieldset><br />
</fieldset>
<br />
<fieldset><legend><b>{size_limit}</b></legend>
 {about_sizelimit}<br /><br />
 <table border="0" cellspacing="0" cellpadding="2">
 <tr>
  <td align="left">{msg_bigmark}:</td>
  <td align="left">
   <input type="text" size=16 name="WP_newbigmark" value="{bigmark}" title="{title_bigmark}" style="text-align:right;" />&nbsp;{sizeexample}
  </td>
 </tr>
 <tr>
  <td align="left">{msg_noshow}:</td>
  <td align="left">
   <input type="text" size=16 name="WP_newnoshow" value="{noshow}" title="{title_noshow}" style="text-align:right;" />&nbsp;{sizeexample}
  </td>
 </tr>
 <tr>
  <td align="left">{msg_maxupload}:</td>
  <td align="left">
   <input type="text" size=16 name="WP_newmaxupload" value="{maxupload}" title="{title_maxupload}" style="text-align:right;" />&nbsp;{sizeexample}
  </td>
 </tr>
 </table>
</fieldset>
 <br />
<fieldset><legend><b>{leg_misc}</b></legend><!-- START allowsend --><!-- END allowsend --><!-- START confacc --><!-- END confacc --><!-- START allowconf --><!-- END allowconf -->
 <input type="checkbox" name="WP_newaddrcheck" id="lbl_addrcheck" value="1"<!-- START addrcheck --> checked<!-- END addrcheck --> />&nbsp;
 <label for="lbl_addrcheck">{msg_addrcheck}</label><br />
 <input type="checkbox" name="WP_newkillall" id="lbl_killall" value="1"<!-- START killall --> checked<!-- END killall --> />&nbsp;
 <label for="lbl_killall">{msg_killall}</label><br />
 <input type="checkbox" name="WP_usegzip" id="lbl_usegzip" value="1"<!-- START usegzip --> checked<!-- END usegzip --> />&nbsp;
 <label for="lbl_usegzip">{msg_usegzip}</label><br />
 <br />
 {msg_pagesize}:&nbsp;<input type="text" size=5 name="WP_newpagesize" value="{pagesize}" style="text-align:right;" /><br />
 {about_pagesize}<br />
 <br />
 {msg_killsleep}:&nbsp;<input type=text name="WP_newkillsleep" style="text-align:right;" value="{killsleep}" size=8 maxlength=8 /><br />
 {about_killsleep}<br />
 <br />
</fieldset>
<br /><!-- START use_provsig --><!-- END use_provsig --><!-- START showmotd --><!-- END showmotd -->
<input type="submit" value="{msg_save}" />
</form>
<script type="text/javascript">
<!--
var TypeList = new Array("sendmail", "smtp");
var Durchlauf = TypeList.length;
function hide()
{
    for (var j = 0; j < Durchlauf; j++) {
        var what = "hide_" + TypeList[j];
        document.getElementById(what).style.display = "none";
    }
    var sel  = document.getElementById("sendmethod");
    var type = sel.options[sel.selectedIndex].value;
    CurrType = type;
    var what = "hide_" + type;
    document.getElementById(what).style.display = "block";
    return true;
}
hide();
// -->
</script>
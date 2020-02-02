<div align="left">
{head_text}<br /><br /><!-- START error -->
<b>{error}</b><br /><br /><!-- END error -->
<form action="{target_link}" method="POST">
<b>{msg_currdrvr}</b>&nbsp;<!-- START one_no_driver -->{output}
<!-- END one_no_driver --><!-- START drivermenu -->
<select class="input" name="new_driver" size=1><!-- START menuline -->
 <option value="{drivername}"<!-- START selected --> selected<!-- END selected -->>{drivername}</option><!-- END menuline -->
</select>&nbsp;<input class="input" type=submit value="{msg_save}"><br /><!-- END drivermenu -->
</form>
<br />
<form action="{link_base}driver" method="POST">
<fieldset><legend><b>{msg_settings}</b></legend>
{conf_output}
</fieldset>
</form>
</div>
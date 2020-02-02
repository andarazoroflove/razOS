<!-- START error -->{error}<br />
<br /><!-- END error -->
<form action="{target}" method="POST">
 {msg_name} <input type="text" name="WPadb_grpname" value="{name}" size=16 maxlength=32 />
 <input type=hidden name="WP_plug[adb_done]" value="yes" />
 <input type=hidden name="WP_plug[adb_action]" value="{adb_action}" />
 <input type=hidden name="WP_plug[adb_do]" value="{adb_do}" />
 <input type=hidden name="WP_plug[adb_mode]" value="{adb_mode}" />
 <input type=hidden name="id" value="{id}" />
 <input type=hidden name="action" value="{action}" />
 <input type=hidden name="mail" value="{mail}" />
 {passthrough}
 <input type="submit" value="{msg_save}" />
</form>
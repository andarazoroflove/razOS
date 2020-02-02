<div align="center">
 {msg_dele}<br />
 <br />
 <form action="{target}" method=POST>
  <input type=hidden name="WP_plug[adb_done]" value="yes">
  <input type=hidden name="WP_plug[adb_action]" value="{adb_action}">
  <input type=hidden name="WP_plug[adb_do]" value="{adb_do}">
  <input type=hidden name="WP_plug[adb_mode]" value="{adb_mode}">
  <input type=hidden name="id" value="{id}">
  <input type=hidden name="action" value="{action}">
  <input type=hidden name="mail" value="{mail}">
  {passthrough}
  <input type=submit class="input" name="WP_plug[adb_yesiwant]" value="{msg_yes}">&nbsp;&nbsp;
  <input type=submit class="input" name="WP_plug[adb_nononotme]" value="{msg_no}">
 </form>
</div>
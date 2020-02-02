<form action="{PHP_SELF}" method="POST">
 <table width="100%" align="center" height="100" valign="middle" cellspacing="2">
  <tr>
   <td width="100%" align="center" valign="bottom">{kill_request}<br /></td>
  </tr>
  <tr>
   <td width="100%" align="center" valign="top"><!-- START hidden -->
    <input type="hidden" name="{name}" value="{value}" /><!-- END hidden -->
    <input type="hidden" name="action" value="kill" />
    <input type="submit" name="noidonotwantto" value="{no}" />&nbsp;&nbsp;
    <input type="submit" name="yesiwantto" value="{yes}" />
   </td>
  </tr>
 </table>
</form>
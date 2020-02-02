<div align="left">
<form action="{target_link}" method=POST>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
 <tr>
  <td class="contthleft"><b>{msg_optactive}</b></td>
  <td class="contthmiddle" align="left" colspan=2><b>{msg_optplugin}</b></td>
  <td class="contthright" align="left"><b>{msg_optdescr}</b></td>
 </tr><!-- START plugline --><!-- START odd --><!-- END odd -->
 <tr>
  <td class="conttd" valign="top" align="left"><input type="checkbox" name="acti_pi[]" value="{plugname}"<!-- START sel --> checked<!-- END sel --> /></td>
  <td class="conttd" valign="top" align="left"><img src="{icon_link}" title="{plugname}" alt="{plugname}">&nbsp;</td>
  <td class="conttd" valign="top" align="left">&nbsp;{plugname}&nbsp;</td>
  <td class="conttd" valign="top" align="left">{description}</td>
 </tr><!-- END plugline --><!-- START maysave -->
<tr>
 <td align="left" colspan="4"><input type="submit" value="{msg_save}" /></td>
</tr><!-- END maysave -->
</table>
</form>
</div>
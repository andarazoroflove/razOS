<form action="{target}" method="POST">
{content}<br />
<table border="0" cellpadding="2" cellspacing="0">
 <tr>
  <td align="left"><!-- START ord_rec_up -->
  <a href="{link_sort}" title="{msg_sort}">{msg_receiver}</a>&nbsp;<img src="{skin_path}/images/nav_up.png" alt="" /><!-- END ord_rec_up --><!-- START ord_rec_down -->
  <a href="{link_sort}" title="{msg_sort}">{msg_receiver}</a>&nbsp;<img src="{skin_path}/images/nav_down.png" alt="" /><!-- END ord_rec_down --><!-- START ord_rec_none -->
  <a href="{link_sort}" title="{msg_sort}">{msg_receiver}</a><!-- END ord_rec_none -->
  /
  <!-- START ord_grp_up -->
  <a href="{link_sort}" title="{msg_sort}">{msg_group}</a>&nbsp;<img src="{skin_path}/images/nav_up.png" alt="" /><!-- END ord_grp_up --><!-- START ord_grp_down -->
  <a href="{link_sort}" title="{msg_sort}">{msg_group}</a>&nbsp;<img src="{skin_path}/images/nav_down.png" alt="" /><!-- END ord_grp_down --><!-- START ord_grp_none -->
  <a href="{link_sort}" title="{msg_sort}">{msg_group}</a><!-- END ord_grp_none -->
  </td>
  <td align="left">&nbsp;{msg_to}&nbsp;</td>
  <td align="left">&nbsp;{msg_cc}&nbsp;</td>
  <td align="left">&nbsp;{msg_bcc}&nbsp;</td>
 </tr><!-- START entry --><!-- START name -->
 <tr>
  <td align="left" class="body">&nbsp;<strong>{nickname}</strong>&nbsp;</td>
  <td align="right" colspan="3" class="body">&nbsp;<i>{group}</i>&nbsp;</td>
 </tr><!-- END name --><!-- START selection -->
 <tr>
  <td align="left">&nbsp;-&nbsp;{email}&nbsp;</td>
  <td>
   <input type="hidden" name="WP_plug[adb_email][{key}]" value="{email}" />
   <input type="checkbox" name="WP_plug[adb_to][{key}]" value="1" />
  </td>
  <td>
   <input type="checkbox" name="WP_plug[adb_cc][{key}]" value="1" />
  </td>
  <td>
   <input type="checkbox" name="WP_plug[adb_bcc][{key}]" value="1" />
  </td>
 </tr><!-- END selection --><!-- END entry -->
</table>
<input type="hidden" name="action" value="{action}" />
{passthrough}
<input type="hidden" name="mail" value="{mail}" />
<input type="hidden" name="WP_plug[adb_action]" value="select" />
<input type="submit" name="WP_plug[done_adb]" value="{insert}" />
</form>
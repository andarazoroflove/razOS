<div align="center"><h4>{msg_setAU}</h4></div><!-- START idxExists -->
<div align="center">
 {msg_idxexists}<br />
 <br />
 <a href="{link_remove}">{msg_remove}</a>&nbsp;&nbsp;<a href="{link_goon}">{msg_goon}</a><br />
</div><!-- END idxExists --><!-- START main --><!-- START return -->
<div align="left" style="max-height: 100px; overflow:auto;"><strong>{WP_return}</strong><br /><br /></div>
<!-- END return -->
<div align="left">
 {msg_trycon} {msg_status}<br /><br />
 <table border="0" cellpadding="2" cellspacing="0" width="100%">
  <tr>
   <td align="left" valign="top">{msg_version_installed}</td>
   <td algin="left" valign="top">{version_installed}</td>
  </tr>
  <tr>
   <td align="left" valign="top">{msg_version_server}</td>
   <td algin="left" valign="top">{version_server}</td>
  </tr>
 </table><br />
 <br /><!-- START current -->
 <strong>{msg_uptodate}</strong></br /><!-- END current --><!-- START newer -->
 <table border="0" cellpadding="2" cellspacing="0" width="100%">
  <tr>
   <td align="right" valign="top"><strong>{msg_prio}</strong></td>
   <td align="left" valign="top">{prio}</td>
  </tr>
  <tr>
   <td align="right" valign="top"><strong>{msg_size}</strong></td>
   <td align="left" valign="top">{size}</td>
  </tr>
  <tr>
   <td align="right" valign="top"><strong>{msg_comment}</strong></td>
   <td align="left" valign="top"><div align="left" style="max-height: 100px; overflow:auto;">{comment}</div></td>
  </tr><!-- START runurl -->
  <tr>
   <td>&nbsp;</td>
   <td align="left"><br /><a href="{link_run}">{msg_run}</a></td>
  </tr><!-- END runurl -->
 </table><!-- END newer --><!-- END main -->
</div>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <title>{version}</title>
 {metainfo}
 <link rel="stylesheet" href="{skin_path}/style.css" type="text/css">
</head>
<body>
<div align="center"><br />
<table cellpadding="0" cellspacing="0" border="0" width="750">
 <tr>
  <td colspan="2" id="topcell">
   <table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
     <td rowspan="2" height="72" align="left" valign="middle" id="logocell">
      <img src="{skin_path}/images/phlymail.png" border=0 alt="{version}" />
     </td>
     <td rowspan="2" width="10">&nbsp;</td>
     <td align="right" valign="top">
      <div id="topteaser">{version}</div>
     </td>
    </tr>
    <tr>
     <td align="right" valign="bottom">
      <div id="topmenu"><!-- START logout_i -->
       <a href="{link_logout}">{msg_logout}</a>&nbsp;&nbsp;<!-- END logout_i --><!-- START goconfig -->
       <b>|</b>&nbsp;&nbsp;<a href="{link_goconfig}">{msg_goconfig}</a><!-- END goconfig -->
      </div>
     </td>
    </tr>
   </table>
  </td>
 </tr>
 <tr>
  <td align="left" valign="top" width="150" height="400" id="menucell">
   <table width="100%" cellpadding=0 cellspacing=0>
    <tr>
     <td valign=top>
      <br />
      <br /><!-- START inbox_i -->
      <span class="menui">&nbsp;<a href="{link_inbox}">{msg_inbox}</a></span><!-- END inbox_i --><!-- START inbox_a -->
      <span class="menua">&nbsp;{msg_inbox}</span><!-- END inbox_a --><!-- START inbox_d -->
      <span class="menud">&nbsp;{msg_inbox}</span><!-- END inbox_d -->&nbsp;<br /><br /><!-- START view_i -->
      <span class="menui">&nbsp;<a href="{link_view}">{msg_view}</a></span><!-- END view_i --><!-- START view_a -->
      <span class="menua">&nbsp;{msg_view}</span><!-- END view_a --><!-- START view_d -->
      <span class="menud">&nbsp;{msg_view}</span><!-- END view_d -->&nbsp;<br /><br /><!-- START send_i -->
      <span class="menui">&nbsp;<a href="{link_send}">{msg_send}</a></span><!-- END send_i --><!-- START send_a -->
      <span class="menua">&nbsp;{msg_send}</span><!-- END send_a --><!-- START send_d -->
      <span class="menud">&nbsp;{msg_send}</span><!-- END send_d -->&nbsp;<br /><br /><!-- START sms_i -->
      <span class="menui">&nbsp;<a href="{link_sms}">{msg_sms}</a></span>&nbsp;<br /><br /><!-- END sms_i --><!-- START sms_a -->
      <span class="menua">&nbsp;{msg_sms}</span>&nbsp;<br /><br /><!-- END sms_a --><!-- START sms_d -->
      <span class="menud">&nbsp;{msg_sms}</span>&nbsp;<br /><br /><!-- END sms_d --><!-- START setup_i -->
      <span class="menui">&nbsp;<a href="{link_setup}">{msg_setup}</a></span><!-- END setup_i --><!-- START setup_a -->
      <span class="menua">&nbsp;{msg_setup}</span><!-- END setup_a --><!-- START setup_d -->
      <span class="menud">&nbsp;{msg_setup}</span><!-- END setup_d -->&nbsp;<br /><br />
      </td>
     </tr>
    </table>
   </td>
  <td align="left" valign="top" id="contentcell" width="600"><br />
   {phlymail_content}
   <br />
   <br />
   <br />
  </td>
 </tr>
</table>
</div>
<br />
<br />
</body>
</html><!-- START logout_a --><!-- END logout_a --><!-- START logout_d --><!-- END logout_d -->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <title>{version}</title>
 {metainfo}
 <link rel="stylesheet" href="{confpath}/schemes/{scheme}.css" type="text/css">
</head>
<body bgcolor="#EEEEEE" text="#000000">
<div align="center"><br />
<table cellpadding="0" cellspacing="0" border="0" width="750" bgcolor="#FFFFFF">
 <tr>
  <td colspan="2" style="border: 1px solid black; background-color: rgb(1, 124, 179);">
   <table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
     <td rowspan="2" height="72" align="left" valign="middle" style="padding-left: 12px;">
      <img src="{confpath}/icons/adminlogo.png" border="0" alt="PHlyMail Config" />
     </td>
     <td rowspan="2" width="10">&nbsp;</td>
     <td align="right" valign="top">
      <div class="topteaser">
       {provider_name}&nbsp;Config
      </div>
     </td>
    </tr>
    <tr>
     <td align="right" valign="bottom">
      <div class="topmenu">
       <a href="{link_logout}">{msg_logout}</a>&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;<a href="{link_frontend}">{msg_frontend}</a>
      </div>
     </td>
    </tr>
   </table>
  </td>
 </tr>
 <tr>
  <td align="left" valign="top" width="200" style="background-color: rgb(1, 154, 205); border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px dashed black; padding-left: 2px; padding-right: 4px;">
   {menu}
  </td>
  <td align="left" valign="top" style="border-right: 1px solid black; border-bottom: 1px solid black; padding-left: 10px; padding-right: 10px;"><br />
   {phlymail_content}
   <br clear="all" />
   <br />
   <br />
  </td>
 </tr>
</table>
</div>
<br clear="all" />
<br />
</body>
</html>
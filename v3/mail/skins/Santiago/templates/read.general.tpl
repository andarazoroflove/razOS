<!-- START printhead --><!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
 <title>{pagetitle}</title>
</head>
<body bgcolor="#FFFFFF" text="#000000" link="#0000FF" alink="#0000FF" vlink="#0000FF" onLoad="self.print();">
<table width=650 border=0 cellpadding=2 cellspacing=0>
 <tr><td style="background-color: black; color: white;" align="left"><b>{printview}</b></td></tr>
 <tr><td><!-- END printhead -->
  <table border=0 cellpadding=1 cellspacing=0 align="left" width="100%"><!-- START standard -->
    <tr>
     <td colspan=2>
     <form action="{PHP_SELF}" method="POST">
      <table width="100%" border=0 cellpadding=1 cellspacing=0>
       <tr>
        <td align="left">
        <div class="body">&nbsp;&nbsp;<!-- START blstblk -->
         <a href="{link_last}"><img border="0" src="{skin_path}/images/nav_left.png" alt="{but_last}" /></a>&nbsp;&nbsp;<!-- END blstblk -->
         <a href="{link_inbox}"><img border="0" src="{skin_path}/images/nav_up.png" alt="{but_up}" /></a>&nbsp;&nbsp;<!-- START bnxtblk -->
         <a href="{link_next}"><img border="0" src="{skin_path}/images/nav_right.png" alt="{but_next}" /></a>&nbsp;&nbsp;<!-- END bnxtblk -->
         &nbsp;&nbsp;&nbsp;{msg_mail}&nbsp;{mail}/{boxsize}&nbsp;&nbsp;
         <input type="text" size="{size}" maxlength="{maxlen}" name="mail" value="{mail}" />&nbsp;
         <input type="submit" value="{goto}" /></div>
         <input type="hidden" name="profile" value="{profile}" />
         <input type="hidden" name="action" value="{action}" />
         {passthrough_2}
         <br /><!-- START onsend -->
         <a href="{link_answer}"><img border="0" src="{skin_path}/images/answer.png" alt="{but_answer}" /></a>&nbsp;&nbsp;
         <a href="{link_answerAll}"><img border="0" src="{skin_path}/images/answerall.png" alt="{but_answerAll}" /></a>&nbsp;&nbsp;
         <a href="{link_forward}"><img border="0" src="{skin_path}/images/forward.png" alt="{but_forward}" /></a>&nbsp;&nbsp;
         <a href="{link_bounce}"><img border="0" src="{skin_path}/images/bounce.png" alt="{but_bounce}" /></a>&nbsp;&nbsp;<!-- START dismiss -->
         <a href="{link_dismiss}"><img border="0" src="{skin_path}/images/dismiss.png" alt="{but_dismiss}" /></a>&nbsp;&nbsp;<!-- END dismiss --><!-- END onsend -->
         <a href="{link_dele}"><img border="0" src="{skin_path}/images/dele.png" alt="{but_dele}" /></a>&nbsp;&nbsp;<!-- START normalheader -->
         <a href="{link_header}"><img border="0" src="{skin_path}/images/normalheader.png" alt="{but_header}" /></a><!-- END normalheader --><!-- START fullheader -->
         <a href="{link_header}"><img border="0" src="{skin_path}/images/fullheader.png" alt="{but_header}" /></a><!-- END fullheader -->&nbsp;&nbsp;
         <!-- START teletype_pro --><a href="{link_teletype}"><img border="0" src="{skin_path}/images/proportional.png" alt="{but_teletype}" /></a><!-- END teletype_pro --><!-- START teletype_sys --><a href="{link_teletype}"><img border="0" src="{skin_path}/images/fixedwidth.png" alt="{but_teletype}" /></a><!-- END teletype_sys -->&nbsp;&nbsp;
         <a href="{link_rawdata}" target="_blank"><img border="0" src="{skin_path}/images/rawdata.png" alt="{but_pure}" /></a>&nbsp;&nbsp;
         <a href="{link_save}"><img border="0" src="{skin_path}/images/save.png" alt="{but_save}" /></a>&nbsp;&nbsp;
         <a href="{link_print}" target="_blank"><img border="0" src="{skin_path}/images/print.png" alt="{but_print}" /></a>
        </td>
       </tr>
      </table>
     </form>
     </td>
    </tr><!-- END standard -->
    <tr>
     <td colspan=2>
      <table border=0 cellpadding=2 cellspacing=0 width="100%"><!-- START headerlines -->
       <tr>
        <td class="body" valign="top" align="left" width=50><strong>{hl_key}:</strong>&nbsp;</td>
        <td class="body" valign="top" align="left"><span{hl_add}>{hl_val}</span>&nbsp;{hl_eval}</td>
       </tr><!-- END headerlines -->
       <tr><td align="left" colspan="2">&nbsp;</td></tr>
       <tr>
        <td align="left" colspan=2>{mailbody}</td>
       </tr>
       <tr><td align="left" colspan="2">&nbsp;</td></tr><!-- START attachblock -->
       <tr>
        <td class="body" valign=top colspan=2><b>{msg_attachs}</b></td>
       </tr>
       <tr>
        <td align="left" colspan=2>
         <table border=0 cellpadding=0 cellspacing=1><!-- START attachline -->
          <tr>
           <td align="left">
            <img src="{att_icon}" align="absmiddle" alt="{att_icon_alt}" />&nbsp;
            <a href="{PHP_SELF}?{link_target}&amp;mail={mail}&amp;profile={profile}&amp;attach={att_num}&amp;{passthrough}"
              target="_blank">{att_name}</a>&nbsp;&nbsp;{att_size}&nbsp;{msg_att_type}:&nbsp;{att_type}
           </td>
          </tr><!-- END attachline -->
         </table>
        </td>
       </tr><!-- END attachblock -->
      </table>
     </td>
    </tr>
   </table><!-- START printfoot -->
  </td>
 </tr>
</table>
</body>
</html>
<!-- END printfoot -->
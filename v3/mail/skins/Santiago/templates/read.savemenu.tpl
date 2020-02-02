<script language="javascript">
<!--
function disable(anaus)
{
 var moep=document.forms['menu'].elements['save_opt'];
 var betroffen=(typeof(moep.length)!='undefined')?moep.length:0;
 if(betroffen){for(var i=0;i<betroffen;i++){moep[i].disabled=anaus;}}
 document.forms['menu'].elements['save_att'].disabled=anaus;
 this.schalter.disabled=anaus;
 return true;
}
-->
</script>
<form action="{linkbase}&action={action}&mail={mail}&pure=true" target="_blank" method="POST"
 name="menu" onMouseMove="init();">
{msg_choose}:<br />
<input type="radio" name="save_as" value="raw" id="sa_raw" OnClick="disable(1);" checked="checked" />
<label for="sa_raw">&nbsp;{msg_complete}</label><br />
<input type="radio" name="save_as" value="body" id="sa_body" OnClick="disable(1);" />
<label for="sa_body">&nbsp;{msg_body}</label><br />
<br />
<table align="left" cellpadding="2" cellspacing="0">
<tr>
 <td valign="top" align="left" class="body">
  <input type="radio" name="save_as" value="txt" id="sa_txt" OnClick="disable(0);" />
  <label for="sa_txt">&nbsp;Text</label><br />
  <input type="radio" name="save_as" value="html" id="sa_html" OnClick="disable(0);" />
  <label for="sa_html">&nbsp;HTML</label><br />
  <input type="radio" name="save_as" value="xml" id="sa_xml" OnClick="disable(0);" />
  <label for="sa_xml">&nbsp;XML</label><br />
 </td>
 <td valign="top" align="left" id="schalter" class="body">
  <input type="radio" name="save_opt" value="shead" id="so_head" checked="checked" />
  <label for="so_head">&nbsp;{msg_shead}</label><br />
  <input type="radio" name="save_opt" value="complete" id="so_cmpl" />
  <label for="so_cmpl">&nbsp;{msg_ahead}</label><br />
  <br />
  <input type="checkbox" name="save_att" value="yes" id="s_a" checked="checked" />
  <label for="s_a">&nbsp;{msg_alist}</label><br />
 </td>
</tr>
</table><br clear="all" />
<br />
<input type="submit" value="{msg_save}" />&nbsp;<a href="{linkbase}&action={action}&mail={mail}">{msg_cancel}</a><br />
<script language="javascript">
<!--
 disable(1);
-->
</script>
</form>
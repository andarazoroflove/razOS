<?php
if (!defined('USR')) return;
echo "
<script language=\"javascript\" type=\"text/javascript\" src=\"${appinfo['appdir']}tiny_mce/tiny_mce.js\"></script>
<script language=\"javascript\" type=\"text/javascript\">
	tinyMCE.init({
		mode : \"textareas\",
		theme : \"advanced\",
		plugins : \"table,advimage,advlink,iespell,insertdatetime,preview,flash,searchreplace,print,paste,directionality,fullscreen,noneditable\",
		theme_advanced_buttons1_add : \"fontselect,fontsizeselect\",
		theme_advanced_buttons2_add : \"separator,insertdate,inserttime,preview,separator,forecolor,backcolor\",
		theme_advanced_buttons2_add_before: \"cut,copy,paste,pastetext,pasteword,separator,search,replace,separator\",
		theme_advanced_buttons3_add_before : \"tablecontrols,separator\",
		theme_advanced_buttons3_add : \"iespell,separator,print,separator,ltr,rtl,separator,fullscreen\",
		theme_advanced_toolbar_location : \"top\",
		theme_advanced_toolbar_align : \"left\",
		theme_advanced_path_location : \"bottom\",
	    plugin_insertdate_dateFormat : \"%Y-%m-%d\",
	    plugin_insertdate_timeFormat : \"%H:%M:%S\",
		extended_valid_elements : \"hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]\",
		external_link_list_url : \"example_link_list.js\",
		external_image_list_url : \"example_image_list.js\",
		flash_external_list_url : \"example_flash_list.js\",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true
	});
</script>";
?>

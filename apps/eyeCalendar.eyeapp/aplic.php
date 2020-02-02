<?PHP
if (defined ('USR') && !function_exists ('eyeCalendar')) {
function eyeCalendar ($eyeapp, &$appinfo) {
  $usr = USR;
  $dir = USRDIR."$usr/eyeCalendar/";

  if (@strtolower ($appinfo['argv'][0]) == 'check') {
    $date = time () + $_SESSION['Toffset'];
    for ($i = @$appinfo['argv'][1] ? $appinfo['argv'][1] : $appinfo['param.startcheck']; 
      $i-- && !is_file ($dir.substr (date ("0dmY", $date + $i * 60 * 60 * 24), -8) . '.eyeCal');)
      ;
    if ($i < 0)
      return ('exit');
  }
  
  addActionBar ("<img class='Cal_delete'
      style='padding-top:5px;' border='0' 
      alt='"._L('Delete note')."'
      title='"._L('Delete note')."'
      src='" . findGraphic('B', 'btn/delete.png')."'/>");
  addActionBar ("<img class='Cal_save' 
      style='display:none; padding-top:5px;' border='0' 
      alt='"._L('Save data')."'
      title='"._L('Save data')."'
      src='".findGraphic('B', 'btn/save.png')."'/>");
      
  addActionBar ("<span class='Cal_today' style='cursor:pointer;' title='"._L('Goto today')."'></span>", 'center');
    
  addActionBar ("<span class='calClock'></span>", 'right');

  echo "
    <div style='position:absolute; left:10px; right:10px; top:50px; bottom:5px;'>
      <div class='Cal_month' style='padding-left:10px; float:right;'></div>
      <div class='Cal_note'". (@$appinfo['param.auto_save'] ? ' auto_save ' :'')." 
        style='position:absolute; left:0px; top:0px; bottom:5px;'>&nbsp;</div> 
    </div>
    
    <script>
    var
      eyeCalString = {
        deleteConfirm : '"._L('OK to delete note?')."',
        mname : [ null, '".
        _L('January')."', '"._L('February')."', '"._L('March')."', '".
        _L('April')."', '"._L('May')."', '"._L('June')."', '".
        _L('July')."', '"._L('August')."', '"._L('September')."', '".
        _L('October')."', '"._L('November')."', '"._L('December')."'],
        
        mn : [ null, '".
        _L('Jan')."', '"._L('Feb')."', '"._L('Mar')."', '"._L('Apr')."', '".
        _L('May')."', '"._L('Jun')."', '"._L('Jul')."', '"._L('Aug')."', '".
        _L('Sep')."', '"._L('Oct')."', '"._L('Nov')."', '"._L('Dec')."'],
        
        dname : [ '".
        _L('Sunday')."', '"._L('Monday')."', '"._L('Tuesday')."', '".
        _L('Wednesday')."', '"._L('Thursday')."', '"._L('Friday')."', '".
        _L('Saturday')."'],
        
        dn : [ '".
        _L('Sun')."', '"._L('Mon')."', '"._L('Tue')."', '"._L('Wed')."', '".
        _L('Thu')."', '"._L('Fri')."', '"._L('Sat')."'] }; ";

  echo "
    </script> ";
  $_SESSION['apps'][$eyeapp]['param.toolbar_today'] = _L($appinfo['param.toolbar_today']);  
  $_SESSION['apps'][$eyeapp]['param.note_header'] = _L($appinfo['param.note_header']);  
  return '';       
}
}

$appfunction = 'eyeCalendar';  ?>

<?php
if (defined ('USR') && !function_exists('eyeFrame')) {
/*  eyeFrame.eyeapp 
    Version: 1.0.2
    Developers: Pau Garcia-Mila

    This app is a frame for other small apps (with are composed by only one 
    file, NameoftheApp.app). The type of the app opened is checked with 
    $appinfo['param.type']. Since the propierties XML file opened will be 
    the one from the small app (not from eyeFrame.eyeapp), the param.type 
    will be the specified there.

    If eyeFrame.eyeapp is opened directly, we close the app, since it wouldn't
    contain anything.
*/

function eyeFrame ($eyeapp, &$appinfo) {

  switch (strtolower ($appinfo['param.type'])) {
  case 'help':
    $argv = $appinfo['argv'];
    addActionBar (_L('Help for %0', basename($argv[0])), 'center'); 
    if ($w = @$_SESSION['apps'][basename($argv[0])]) {
      $w = $w['appdir'] . (!empty ($argv[1]) ? basename ($argv[1]) : 
        (!empty ($w['helpfile']) ? $w['helpfile'] : 'index.htm'));
      $w = (is_file ($w) || is_file ($w .= '.htm') || is_file ($w .= 'l')) ?
        (empty ($argv[2]) ? $w : "$w#$argv[2]") : '';
    }
    break;

  default:
    addActionBar (_L('Viewing %0', basename($appinfo['argv'][0])), 'center');
    if (0 !== strpos (realpath (HOMEDIR.USR.'/'. trim ($appinfo['argv'][0])),
        realpath (HOMEDIR.USR.'/')))
      $w = '';
    else $w = HOMEDIR.USR."/".$appinfo['argv'][0];
    break;
  }

  if (empty ($w))
    return 'exit';
  
  echo "
  <iframe
    name='frm_${appinfo['param.type']}'
    id='frm_${appinfo['param.type']}'
    style='border: 1px solid #D9D9D9; margin-left: 14px; margin-top: 10px;'
    height='${appinfo['param.height']}'
    width='${appinfo['param.width']}'
    src='$w'>
  </iframe>";

  addActionBar ("<img src='" .findGraphic ('', 'btn/print.png'). "'
    style='cursor:pointer;'
    alt='" ._L("Print"). "'
    title='" ._L("Print"). "'
    onClick='frm_${appinfo['param.type']}.focus();frm_${appinfo['param.type']}.print();'
    border='0' />");

  return '';       
}
}
$appfunction = 'eyeFrame';
?>

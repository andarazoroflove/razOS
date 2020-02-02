<?PHP

if (defined ('USR') && !function_exists ('eyeRSS')) {

function eyeRSS ($eyeapp, &$appinfo) {

$url = @$appinfo['state.url'];  
$title = @$appinfo['state.title'];  
  
  addActionBar ("<span style='font:italic 900 20px Verdana, Helvetica, Arial, sans-serif' class='eyeRSStitle'></span>", 'center');
  if (!empty ($appinfo['param.clock']))
    addActionBar ("<span class='Gclock' Gclock='format:".$appinfo['param.clock'].";'></span>", 'right');

echo "
<div 
  class='RSSfeed'
  style='
    position:absolute;
    height:85%;
    top:50px;
    bottom:0px; 
    left:3px; 
    width:68%; 
    border:1px solid #d8d8d8; 
    padding:5px;
    overflow:auto;'>
  <div class='RssFeedId' url='$url' title='$title' style='display:none;'></div>    
</div>  

<div
  style='
    position:absolute;
    height:85%;
    top:50px;
    bottom:0px; 
    right:3px; 
    width:27%; 
    border:1px solid #d8d8d8; 
    padding:5px;
    text-align:center;
    overflow:hidden;'>
  <h2 style:margins:0; padding:0;>Channels</h2>
  <input type='text' name='url' value='$url' style='width:100%;' />
  <button 
    style='text-align:center; margin:3px;'
    onclick='eyeRSS.showFeed (this, this, null, 1)'
    align='center'>
    "._L('Add feed')."
  </button>

  <div class='rssChannels'    
   style='
      height:70%;
      width:95%;
      overflow:auto;
      padding:3px;'>
  </div>
</div>";
  return '';
}
}

$appfunction = 'eyeRSS';  ?>

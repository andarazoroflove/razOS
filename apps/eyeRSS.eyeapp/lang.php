<?PHP
/* Application language file  */
   
$AppLanguages = array (

'polish' => array (
	"Go"=>"Idź",
	"Channels"=>"Kanały",
	"Channel Name"=>"Nazwa kanału",
	"Channel URL"=>"Adres kanału",
	"Add"=>"Dodaj",
	"Delete"=>"Usuń"
)

);
   global $Translations;
   if (isset ($AppLanguages[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG]))
      $Translations = array_merge ($Translations, $AppLanguages[$select]);   
?>

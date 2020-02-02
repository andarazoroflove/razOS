<?PHP
/* Application language file  */
   
$AppLanguages = array (

'polish' => array (
	"Latitude"=>"Szer. geograficzna",
	"Longitude"=>"Dług. geograficzna",
  "Search"=>"Szukaj",
  "Bookmarks"=>"Zakładki",
  "Name point" => "Nazwa punktu",
  "Add" => "Dodaj"
)

);
   global $Translations;
   if (isset ($AppLanguages[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG]))
      $Translations = array_merge ($Translations, $AppLanguages[$select]);   
?>

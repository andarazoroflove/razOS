<?PHP
/* Application language file  */
   
$AppLanguages = array (

'polish' => array (
	"Physical graph"=>"Wykres fizyczny",
	"Intellectual graph"=>"Wykres intelektualny",
  "Emotional graph"=>"Wykres emocjonalny"
)

);
   global $Translations;
   if (isset ($AppLanguages[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG]))
      $Translations = array_merge ($Translations, $AppLanguages[$select]);   
?>

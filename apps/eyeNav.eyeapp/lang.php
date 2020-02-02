<?PHP
/* Application language file 
   */
   
$AppLanguages = array (
'catalan' => array (
	'Go' => "Anar",
	'Search' => "Cercar",
	'Bookmark' => "Marcadors",
	'There are no bookmarks' => "No hi ha marcadors",
),
'spanish' => array (
	'Go' => "Ir",
	'Search' => "Buscar",
	'Bookmark' => "Favoritos",
	'There are no bookmarks' => "No hay favoritos",
),
'bulgarian' => array (
	'Go' => "Отиди",
),
'polish' => array (
	'Go' => "Idź",
),
'french' => array (
	'Go' => "OK",
),
'german' => array (


),
'turkish' => array (


),
'portuguese' => array (


),
'swedish' => array (


),
'chinese' => array (


),
'dutch' => array (


),
'hungarian' => array (


),
'italian' => array (


),
'russian' => array (


),
'danish' => array (


),
'finnish' => array (

),
'romanian' => array (
	'Go' => "Dute",
),
);
   global $Translations;
   if (isset ($AppLanguages[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG]))
      $Translations = array_merge ($Translations, $AppLanguages[$select]);   
?>

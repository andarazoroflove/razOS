<?PHP
/* Application language file  for eyeCalendar

'english' => array (
  'January' => '',
  'February' => '',
  'March' => '',
  'April' => '',
  'May' => '',
  'June' => '',
  'July' => '',
  'August' => '',
  'September' => '',
  'October' => '',
  'November' => '',
  'December' => '',
  
  'Jan' => '',
  'Feb' => '',
  'Mar' => '',
  'Apr' => '',
  'May' => '',
  'Jun' => '',
  'Jul' => '',
  'Aug' => '',
  'Sep' => '',
  'Oct' => '',
  'Nov' => '',
  'Dec' => '',

  'Sunday' => '',
  'Monday' => '',
  'Tuesday' => '',
  'Wednesday' => '',
  'Thursday' => '',
  'Friday' => '',
  'Saturday' => '',

  'Sun' => '',
  'Mon' => '',
  'Tue' => '',
  'Wed' => '',
  'Thu' => '',
  'Fri' => '',
  'Sat' => '',

  'OK to delete note?' => '',
  'Today is %0 %1' => '',
  'Save data' => '',
  'Delete note' => '',
  'Note for %0' => '',
  'Cannot find note %0' =>'',
  'Error : Failed to save note'=> '',
  
  'Show week numbers' => 'Affiche numeros des semaines',
  'Confirm note deletes' => 'Demande confirmation de supression',
  'Autosave on note edit	' => 'Saufegarde automatique des notes',
  'Clock display format' => "Format d'affichage de l'horloge"',
  'Toolbar today text' => "Texte d'aujourd'hui",
  'Note title message' => 'Texte du note titre',
  'Selector test' => 'Teste de selector'
),

   */
$AppLanguages = array (

'catalan' => array (
	'Selected day' => "Dia seleccionat"
),
'spanish' => array (
	'Selected day' => 'Día seleccionado'
),
'bulgarian' => array (
	'Selected day' => "Избран ден"
),

'polish' => array (
	'Selected day' => "Wybrany dzień"
),
'french' => array (
  'January' => 'janvier',
  'February' => 'fevrier',
  'March' => 'mars',
  'April' => 'avril',
  'May' => 'mai',
  'June' => 'juin',
  'July' => 'juilliet',
  'August' => 'aout',
  'September' => 'setpembre',
  'October' => 'octobre',
  'November' => 'novembre',
  'December' => 'decembre',
  
  'Jan' => 'jan',
  'Feb' => 'fev',
  'Mar' => 'mar',
  'Apr' => 'avr',
  'May' => 'mai',
  'Jun' => 'jun',
  'Jul' => 'jui',
  'Aug' => 'aou',
  'Sep' => 'sep',
  'Oct' => 'oct',
  'Nov' => 'nov',
  'Dec' => 'dec',

  'Sunday' => 'dimanche',
  'Monday' => 'lundi',
  'Tuesday' => 'mardi',
  'Wednesday' => 'mercredi',
  'Thursday' => 'jeudi',
  'Friday' => 'vendredi',
  'Saturday' => 'samedi',

  'Sun' => 'dim',
  'Mon' => 'lun',
  'Tue' => 'mar',
  'Wed' => 'mer',
  'Thu' => 'jeu',
  'Fri' => 'ven',
  'Sat' => 'sam',

  'OK to delete note?' => 'OK pour supprimer le note?',
  'Today is %l %d %F %Y' => "Aujourd'hui c'est %l le %d %F %Y",
  'Save data' => 'Saufegarder',
  'Delete note' => 'Supprimer',
  'Note for %l %d %F %Y' => 'Note pour %l le %d %F %Y',
  'Cannot find note %0' => 'Impossible de trouver %0',
  'Error : Failed to save note'=> 'Erreurs : not non saufegarder',
  
  'Show week numbers' => 'Affiche numeros des semaines',
  'Confirm note deletes' => 'Demande confirmation de supression',
  'Autosave on note edit	' => 'Saufegarde automatique des notes',
  'Clock display format' => "Format d'affichage de l'horloge",
  'Toolbar today text' => "Texte d'aujourd'hui",
  'Note title message' => 'Texte du titre du note',
  'Selector test' => 'Teste de selector'
),

'german' => array (
	'Selected day' => "Gewählter Tag"
),
'turkish' => array (
	'Selected day' => "Seçili Gün"
),
'portuguese' => array (
	'Selected day' => "Dia selecionado"
),
'swedish' => array (
	'Selected day' => "Vald dag"
),
'chinese' => array (
	'Selected day' => "选择日期"
),
'dutch' => array (
	'Selected day' => "Gekozen dag"
),
'hungarian' => array (
	'Selected day' => "Kiválasztott nap"
),
'italian' => array (
	'Selected day' => "Giorno selezionato"
),
'russian' => array (
	'Selected day' => "Выбраный день"
),
'danish' => array (
        'Selected day' => "Vælg dag"
),
'finnish' => array (
	'Selected day' => "Valittu päivä"
),
'romanian' => array (
	'Selected day' => "Alege ziua"
),
);
   global $Translations;
   if (isset ($AppLanguages[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG]))
      $Translations = array_merge ($Translations, $AppLanguages[$select]);   
?>

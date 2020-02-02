<?PHP
/* Application language file 
   */
   
$AppLanguages = array (
'catalan' => array (
	'File deleted' => "Arxiu esborrat",
	'File restored' => "Arxiu restaurat",
	'Trash succesfully drained' => "Paperera buidada amb èxit",
	'Trash is empty' => "La paperera és buida",
	'Empty Trash' => "Buidar la paperera",
	'Trash' => "Paperera"
),
'spanish' => array (
	'File deleted' => 'Archivo eliminado',
	'File restored' => 'Archivo restaurado',
	'Trash succesfully drained' => 'Papelera vaciada con éxito',
	'Trash is empty' => 'La papelera está vacía',
	'Empty Trash' => 'Papelera vacía',
	'Trash' => 'Papelera'
),
'bulgarian' => array (
	'File deleted' => "Файлът е изтрит",
	'File restored' => "Файлът е възстановен",
	'Trash succesfully drained' => "Кошчето е изпразнено успешно",
	'Trash is empty' => "Кошчето е празно",
	'Empty Trash' => "Изпразване на кошчето",
	'Trash' => "Кошче"
),
'polish' => array (
	'File deleted' => "Plik usunięty",
	'File restored' => "Plik przywrócony",
	'Trash succesfully drained' => "Kosz został wyczyszczony",
	'Trash is empty' => "Kosz jest pusty",
	'Empty Trash' => "Opróżnij kosz",
	'Trash' => "Kosz"
),
'french' => array (
	'File deleted' => "Fichier supprimé",
	'File restored' => "Fichier restauré",
	'Trash succesfully drained' => "Corbeille vidée avec succés",
	'Trash is empty' => "La corbeille est vide",
	'Empty Trash' => "Vider la corbeille",
	'Trash' => "Corbeille"
),
'german' => array (
	'File deleted' => "Datei gelöscht",
	'File restored' => "Datei wiederhergestellt",
	'Trash succesfully drained' => "Papierkorb erfolgreich geleert",
	'Trash is empty' => "Der Papierkorb ist leer",
	'Empty Trash' => "Papierkorb leeren",
	'Trash' => "Papierkorb"
),
'turkish' => array (
	'File deleted' => "Dosya silindi",
	'File restored' => "Dosya geri alındı",
	'Trash succesfully drained' => "Çöp başarıyla boşaltıldı",
	'Trash is empty' => "Çöp kutusu boş",
	'Empty Trash' => "Çöp kutusunu boşalt",
	'Trash' => "Çöp Kutusu"
),
'portuguese' => array (
	'File deleted' => "Documento eliminado",
	'File restored' => "Documento recuperado",
	'Trash succesfully drained' => "Lixeira esvaziada com sucesso",
	'Trash is empty' => "Lixeira vazia",
	'Empty Trash' => "Esvaziar lixeira",
	'Trash' => "Lixeira"
),
'swedish' => array (
	'File deleted' => "Filen raderades",
	'File restored' => "Filen återställdes",
	'Trash succesfully drained' => "Papperskorgen tömdes",
	'Trash is empty' => "Papperskorgen är tom",
	'Empty Trash' => "Töm papperskorgen ",
	'Trash' => "Papperskorg"
),
'chinese' => array (
	'File deleted' => "文件已删除",
	'File restored' => "文件已恢复",
	'Trash succesfully drained' => "回收站已清空",
	'Trash is empty' => "没有文件",
	'Empty Trash' => "回收站为空",
	'Trash' => "回收站"
),
'dutch' => array (
	'File deleted' => "Bestand verwijderd",
	'File restored' => "Bestand herstelt",
	'Trash succesfully drained' => "Prullenbak succesvol geleegd",
	'Trash is empty' => "De prullenbak is leeg",
	'Empty Trash' => "Prullenbak legen",
	'Trash' => "Prullenbak"
),
'hungarian' => array (
	'File deleted' => "Fájl törölve",
	'File restored' => "Fájl visszaállítva",
	'Trash succesfully drained' => "",
	'Trash is empty' => "A lomtár üres",
	'Empty Trash' => "Lomtár ürítése",
	'Trash' => "Lomtár"
),
'italian' => array (
	'File deleted' => "File cancellato",
	'File restored' => "File ricaricato",
	'Trash succesfully drained' => "Il cestino è stato svuotato",
	'Trash is empty' => "Il cestino è vuoto",
	'Empty Trash' => "Svuota il cestino",
	'Trash' => "Cestino"
),
'russian' => array (
	'File deleted' => "Файл удален",
	'File restored' => "Файл востановлен",
	'Trash succesfully drained' => "Мусорник опустошен",
	'Trash is empty' => "Мусорник пуст",
	'Empty Trash' => "Опустошить мусорник",
	'Trash' => "Мусорник"
),
'danish' => array (
	'File deleted' => "Fil slettet",
	'File restored' => "Filen blev genoprettet",
	'Trash succesfully drained' => "Affaldspand blev tømt",
	'Trash is empty' => "Affaldspanden er tom",
	'Empty Trash' => "Tøm Affaldspanden",
	'Trash' => "Affaldspand"
),
'finnish' => array (
	'File deleted' => "Tiedosto poistettu",
	'File restored' => "Tiedosto palautettu",
	'Trash succesfully drained' => "Roskakori tyhjennetty",
	'Trash is empty' => "Roskakori on tyhjä",
	'Empty Trash' => "Tyhjennä roskakori",
	'Trash' => "Roskakori"
),
'romanian' => array (
	'File deleted' => "Fisier sters",
	'File restored' => "Fisier restaurat",
	'Trash succesfully drained' => "Gunoiul a fost distrus",
	'Trash is empty' => "Gunoiul este gol",
	'Empty Trash' => "Gunoi gol",
	'Trash' => "Gunoi",
)
);
   global $Translations;
   if (isset ($AppLanguages[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG]))
      $Translations = array_merge ($Translations, $AppLanguages[$select]);   
?>

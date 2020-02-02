<?PHP
if (!defined ('USR')) return;

		function esborradir($nomdirectori) {
		   if(empty($nomdirectori)) {
			   return true;
		   }
		   if(file_exists($nomdirectori)) {
			   $directoriborrant = dir($nomdirectori);
			   while($arxiusdir = $directoriborrant->read()) {
				   if($arxiusdir != '.' && $arxiusdir != '..') {
					   if(is_dir($nomdirectori.'/'.$arxiusdir)) {
						   esborradir($nomdirectori.'/'.$arxiusdir);
					   } else {
						   @unlink($nomdirectori.'/'.$arxiusdir) or die($error);
					   }
				   }
			   }
			   $directoriborrant->close();
			   @rmdir($nomdirectori) or die($error);
		   } else {
			   return false;
		   }
		   return true;
		}

?>

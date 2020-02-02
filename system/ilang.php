<?PHP
/*                              eyeOS project
                     Internet Based Operating System
                               Version 0.9
                     www.eyeOS.org - www.eyeOS.info
       -----------------------------------------------------------------
                  Pau Garcia-Mila Pujol - Hans B. Pufal
       -----------------------------------------------------------------
          eyeOS is released under the GNU General Public License - GPL
               provided with this release in DOCS/gpl-license.txt
                   or via web at www.gnu.org/licenses/gpl.txt

         Copyright 2005-2006 Pau Garcia-Mila Pujol (team@eyeos.org)

          To help continued development please consider a donation at
            http://sourceforge.net/donate/index.php?group_id=145027         */
/* PHP core system language file.
  
   Holds all core system translation tables, one per language supported
   Builds an array of supported languages
   Loads the current language table into the system 
   */
   
$Languages = array (
'english' => array (),

'-' => array (
  'Please enter all information requested' => '',
  'Passwords do not match, please reenter' => '',
  'Installation failed.... cannot find file %0' => '',
  'Please create the following diretories manually setting their permissions to 0777 :' => '',
  'Please manually CHMOD 0777 the following directories' => '',
  'Welcome to eyeOS installation script.' => '',
  'Before using your new eyeOS system, you need specify some system parameters' => '',
  'The install process will be completed in a minute!' => '', 
  'Please specify a name for your system :' => '', 
  'Choose the default language for your system :' => '', 
  'Specify the password for the root user :' => '',
  'Retype your password :' => '',
  'Install eyeOS >>' => '',
),
);

   global $Translations;
   if (isset ($Languages[$select = !empty ($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULTLANG])) {
      $Translations = $Languages[$select];   
   } else
      $Translations = array ();
      
   $Languages = array_keys ($Languages); 
?>


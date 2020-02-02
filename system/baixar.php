<?php
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
   session_start(); 
   if (!isset($_SESSION['usrinfo'])) {
      session_destroy ();
      exit;
   }
   
   $usr = $_SESSION['usr'];
   $fabaixar = strip_tags($_REQUEST['fabaixar']);
   if (0 == strpos (realpath ("../home/$usr/$fabaixar"),realpath ("../home/$usr/"))) {
     $arxiuxbaixar = "../home/$usr/$fabaixar"; 
     header ('Content-Disposition: attachment; filename=' . $fabaixar); 
     header ('Content-Type: application/octet-stream');
     header ('Content-Length: ' . filesize($arxiuxbaixar));
     readfile ($arxiuxbaixar);
   }
?>

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

   function ar_support () {
      $supported = array ();	   
      if (function_exists('gzopen'))
         $supported[] = 'gz';
      if (function_exists('zip_open'))
         $supported[] = 'zip';
      return $supported;	 
   }

   function ar_open ($afile, $aname) {
      if ((strtolower (substr ($aname, -7)) == '.tar.gz') ||
         (strtolower (substr ($aname, -4)) == '.tgz')) {
	 if (function_exists ('gzopen'))
            return array ('type'=> 'tgz', 'fd' => gzopen ($afile, 'rb'));		 
      }

      if ((strtolower (substr ($aname, -4)) == '.zip') && function_exists ('zip_open'))
         return array (
	   'type'=> 'zip', 
	   'afile' => $afile, 
	   'fd' => zip_open ($afile)
	 );		 

      return _L('Archive file %0 not supported', $aname);
   }
   
   function ar_nextfile ($ar) {
      switch ($ar['type']) {
      case'tgz':
         if ($fhdr = gzread ($ar['fd'], 512)) {
            for ($flen = intval (trim (substr ($fhdr, 124, 12)), 8); $flen > 0; $flen -= 512)
               gzread ($ar['fd'], 512);
	    return ($fn = trim (substr ($fhdr, 0, 100))) ? array (
	      'name' => $fn, 
	      'size' => intval (trim (substr ($fhdr, 124, 12)), 8)
	    ) : false;
	 }

      case'zip':
         if ($ze = zip_read ($ar['fd']))
	    return array (
	      'name' => zip_entry_name ($ze),
	      'size' => zip_entry_filesize ($ze)
	    );
      }
      return false;
   }
   
   function ar_rewind (&$ar) {
      switch ($ar['type']) {
      case'tgz':
         gzrewind ($ar['fd']);
	 return;
      
      case'zip':
         zip_close ($ar['fd']);
	 $ar['fd'] = zip_open ($ar['afile']);
	 return;
      }
      return false;	   
   }
   
   function ar_close ($ar) {
      switch ($ar['type']) {
      case'tgz':
         gzclose ($ar['fd']);
	 return true;
	 
      case'zip':
         zip_close ($ar['fd']);
	 return true;
      }
      
      return false;	   
   }
   
   function ar_extractfile ($ar, $nf) {
      switch ($ar['type']) {
      case'tgz':
         $fhdr = gzread ($ar['fd'], 512);
         for ($flen = intval (trim (substr ($fhdr, 124, 12)), 8); $flen > 0; $flen -= 512)
            fwrite ($nf, substr (gzread ($ar['fd'], 512), 0, $flen > 512 ? 512 : $flen));
	 return intval (trim (substr ($fhdr, 124, 12)), 8);
	 
      case'zip':
         if (($ze = zip_read ($ar['fd'])) && ($zef = zip_entry_open ($ar['fd'], $ze))) {     
	    while (fwrite ($nf, zip_entry_read ($ze, 1024)))
	       ;     
	    return zip_entry_filesize ($ze);
	 }
      }
      return false;
   }
?>

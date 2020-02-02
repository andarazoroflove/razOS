<?php
/* ------------------------------------------------------------------------- */
/* drivers/fslite/admin.php - PHlyMail 2.1.0+                                */
/* Administrative methods for use with the file system                       */
/* (c) 2003-2004 blue birdy, Berlin (http://bluebirdy.de)                    */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.1.1mod1                                                                */
/* ------------------------------------------------------------------------- */

class admin extends driver {

 // This is the constructor
 function admin($Conf)
 {
     return $this->driver($Conf); // MOD /
 }

 // Administrators counterpart of authenticate()
 // Input  : adm_auth(string user name)
 // Returns: $return     array data on success, FALSE otherwise
 //          $return[0] uid
 //          $return[1] MD5 hash of user's password
 function adm_auth($un = '')
 {
     return $this->authenticate($un);
 }

 // Return the basic user data for an admin's user ID
 // Input  : get_admdata(integer user id)
 // Returns: $return    array data on success, FALSE otherwise
 //          $return['accname'][id]  Display name of the account(s)
 function get_admdata($uid = 0)
 {
     return $this->get_usrdata($uid);
 }

 // Administrators counterparts for failure count (identical API)
 function get_admfail($uid = FALSE)
 {
     return $this->get_usrfail($uid);
 }

 function set_admfail($uid = FALSE)
 {
     return $this->set_usrfail($uid);
 }

 function reset_admfail($uid = FALSE)
 {
     return $this->reset_usrfail($uid);
 }

 // Set login timestamp of a specific admin
 // Input : set_admlogintime(integer user id)
 // Return: void
 function set_admlogintime($uid = FALSE)
 {
     return $this->set_logintime($uid);
 }

 // Set logout timestamp of a specific user
 // Input : set_logouttime(integer user id)
 // Return: void
 function set_admlogouttime($uid = FALSE)
 {
     return $this->set_logouttime($uid);
 }

}
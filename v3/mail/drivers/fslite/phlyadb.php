<?php
/* ------------------------------------------------------------------------- */
/* drivers/fslite/phlyadb.php - PHlyMail 1.2.0+                              */
/* Methods regarding address book for use with the file system               */
/* (c) 2003-2004 phlyLabs, Berlin (http://phlylabs.de)                       */
/* All rights reserved                                                       */
/* PHlyMail Lite                                                             */
/* v0.1.1                                                                    */
/* ------------------------------------------------------------------------- */

class phlyadb extends driver {

 // Valid Search Criteria
 var $criteria_list;

 // This is the constructor
 function phlyadb($Conf)
 {
     // Init translation of valid search criteria to actual field list
     $this->criteria_list = array
             ('nick' => array('nick'), 'name' => array('firstname', 'lastname')
             ,'company' => array('company'), 'address' => array('address')
             ,'email' => array('email1', 'email2')
             ,'phone' => array('tel_private', 'tel_business', 'cellular', 'fax')
             ,'comment' => array('comment'), 'group' => array('gid')
             ,'birthday' => array('birthday')
             ,'www' => array('www')
             ,'free' => array('free1', 'free2')
             );
     return $this->driver($Conf);
 }

 // Get a list of valid search criteria
 // Input    adb_get_criteria(void)
 // Returns: $return array data
 function adb_get_criteria()
 {
     return $this->criteria_list;
 }

 // Get count of address stored in the user's address book
 // Input  : adb_get_adrcount(integer user id, boolean with global adr)
 // Returns: string count on success or FALSE on failure
 function adb_get_adrcount($uid = 0, $inc_global)
 {
     if (!file_exists($this->DB['file_adb_adr']) || !is_readable($this->DB['file_adb_adr'])) return 0;
     $ini = parse_ini_file($this->DB['file_adb_adr'], 1);
     return count($ini);
 }

 // Get all addresses stored in the user's address book
 // Input  : adb_get_adridx(integer user id, boolean with global adr, [string pattern [,string criteria
 //          [,integer num [,integer start[,string order by field[,string order direction ('asc|desc')]]]]]])
 // Returns: $return     array data on success, FALSE otherwise
 function adb_get_adridx($uid = 0, $inc_global, $pattern = '', $criteria = '', $num = 0, $start = 0
         ,$order_by = false, $order_dir = 'asc')
 {
     $return = array();
     $order_dir = ('desc' == $order_dir) ? SORT_DESC : SORT_ASC;

     if (!file_exists($this->DB['file_adb_adr']) || !is_readable($this->DB['file_adb_adr'])) return $return;
     $ini = parse_ini_file($this->DB['file_adb_adr'], 1);

     $grouplist = $this->adb_get_grouplist($uid, $inc_global);

     foreach ($grouplist as $k => $v) {
         $group[$v['gid']] = $v['name'];
     }
     unset($grouplist);

     foreach ($ini as $k => $v) {
         foreach ($v as $k2 => $v2) {
             $v[$k2] = urldecode($v2);
         }
         $v['aid'] = $k;
         $v['group'] = isset($group[$v['gid']]) ? $group[$v['gid']] : '' ;
         $v['displayname'] = ($v['nick'])
                     ? $v['nick']
                     : (($v['lastname'] && $v['firstname'])
                             ? $v['lastname'].', '.$v['firstname']
                             : $v['lastname'].$v['firstname']
                       );

         // Handle sorting
         if ($order_by == 'group') {
             $sortfield = $v['group'];
         } else {
             $sortfield = $v['displayname'];
         }
         $return[] = $v;
         $sort[$k] = $sortfield;
     }
     array_multisort($return, $order_dir, $sort);
     return $return;
 }

 // Return a specific address
 // Input  : adb_get_address(integer address id)
 // Returns: array data on success or FALSE on failure
 function adb_get_address($aid = 0)
 {
     if (!file_exists($this->DB['file_adb_adr']) || !is_readable($this->DB['file_adb_adr'])) return $return;
     $ini = parse_ini_file($this->DB['file_adb_adr'], 1);
     if (isset($ini[$aid])) {
         foreach($ini[$aid] as $k => $v) {
             $ini[$aid][$k] = urldecode($v);
         }
         return $ini[$aid];
     } else return array();
 }

 // Return addresses right before and right after a given
 function adb_get_prevnext($aid)
 {
     if (!file_exists($this->DB['file_adb_adr']) || !is_readable($this->DB['file_adb_adr'])) return $return;
     $ini = parse_ini_file($this->DB['file_adb_adr'], 1);
     // remove transport encoding
     foreach ($ini as $k => $v) {
         foreach ($v as $k2 => $v2) {
             $v[$k2] = urldecode($v2);
         }
         $v['aid'] = $k;
         $return[$k] = $v;
         $sort[$k] = $v['nick'];
     }
     // Sort by nick
     array_multisort($return, SORT_ASC, $sort);
     $prev = FALSE;
     $next = FALSE;
     $hit = FALSE;

     foreach ($return as $v) {
         if ($v['aid'] == $aid) {
             $hit = TRUE;
             continue;
         }
         if ($hit) {
             $next = $v['aid'];
             break;
         }
         $prev = $v['aid'];
     }
     return array($prev, $next);
 }

 // Delete a given address from address book
 // Input  : adb_dele_address(integer address id)
 // Returns: TRUE on success or FALSE on failure
 function adb_dele_address($aid = 0)
 {
     if (!$aid) return FALSE;
     if (!file_exists($this->DB['file_adb_adr']) || !is_readable($this->DB['file_adb_adr'])) return array();
     $ini = parse_ini_file($this->DB['file_adb_adr'], 1);
     unset($ini[$aid]);
     return $this->_write_file($this->DB['file_adb_adr'], $ini, FALSE);
 }

 // Add an address to the address book
 // Omit data you don't want to set
 // Set the owner to 0 for a global address
 // Input  : adb_add_address(array field data)
 // Returns: TRUE on success or FALSE on failure
 function adb_add_address($data)
 {
     if (file_exists($this->DB['file_adb_adr']) && is_readable($this->DB['file_adb_adr'])) {
         $ini = parse_ini_file($this->DB['file_adb_adr'], 1);
         $next_index = max(array_keys($ini)) + 1;
     } else {
         $next_index = 1;
     }
     $add = array();
     foreach (array('nick', 'firstname', 'lastname', 'company', 'address', 'email1', 'email2'
             ,'tel_private', 'tel_business','cellular', 'fax', 'www', 'birthday', 'comments'
             ,'free1', 'free2', 'gid') as $k) {
         if (isset($data[$k])) $add[$next_index][$k] = urlencode($data[$k]);
     }
     if (!empty($add)) {
         return $this->_write_file($this->DB['file_adb_adr'], $add, TRUE);
     } else {
         return FALSE;
     }
 }

 // Update an address in the address book
 // Omit data you don't want to update
 // Input  : adb_update_address(array field data)
 // Returns: TRUE on success or FALSE on failure
 function adb_update_address($data)
 {
     $add = array();
     foreach (array('nick', 'firstname', 'lastname', 'company', 'address', 'email1', 'email2'
             ,'tel_private', 'tel_business','cellular', 'fax', 'www', 'birthday', 'comments'
             ,'free1', 'free2', 'gid') as $k) {
         if (isset($data[$k])) $add[$data['aid']][$k] = urlencode($data[$k]);
     }
     if (!empty($add)) {
         return $this->_write_file($this->DB['file_adb_adr'], $add, TRUE);
     } else {
         return FALSE;
     }
 }

 // Return list of groups associated with a certain user
 // Input  : adb_get_grouplist(integer user id, boolean with global, [string pattern [,integer num [,integer start]]])
 // Returns: $return     array data on success, FALSE otherwise
 function adb_get_grouplist($uid = 0, $inc_global, $pattern = '', $num = 0, $start = 0)
 {
     $return = array();
     if (!file_exists($this->DB['file_adb_grp']) || !is_readable($this->DB['file_adb_grp'])) return $return;
     $ini = parse_ini_file($this->DB['file_adb_grp'], 1);

     foreach ($ini as $k => $v) {
         foreach ($v as $k2 => $v2) {
             $v[$k2] = urldecode($v2);
         }
         $v['gid'] = $k;
         $return[] = $v;
         $sort[$k] = $v['name'];
     }
     array_multisort($return, SORT_ASC, $sort);
     return $return;
 }

 // Return group by given owner and group id
 // Input  : adb_get_group(integer owner, integer group id)
 // Returns: string group name on success, FALSE otherwise
 function adb_get_group($uid = 0, $gid = 0)
 {
     if (!$gid) return FALSE;
     if (!file_exists($this->DB['file_adb_grp']) || !is_readable($this->DB['file_adb_grp'])) return FALSE;
     $ini = parse_ini_file($this->DB['file_adb_grp'], 1);
     return (isset($ini[$gid])) ? urldecode($ini[$gid]['name']) : FALSE;
 }

 // Update a given group
 // Input  : adb_update_group(integer owner, integer group id, string group name)
 // Returns: TRUE on success, FALSE otherwise
 function adb_update_group($uid = 0, $gid = 0, $name = '')
 {
     if (!$gid) return FALSE;
     $data[$gid]['name'] = urlencode($name);
     return $this->_write_file($this->DB['file_adb_grp'], $data, TRUE);
 }

 // Insert a group
 // Input  : adb_add_group(integer owner, integer group id, string group name)
 // Returns: TRUE on success, FALSE otherwise
 function adb_add_group($uid = 0, $name = '')
 {
     if (file_exists($this->DB['file_adb_grp']) && is_readable($this->DB['file_adb_grp'])) {
         $ini = parse_ini_file($this->DB['file_adb_grp'], 1);
         $next_index = max(array_keys($ini)) + 1;
     } else {
         $next_index = 1;
     }
     $data[$next_index]['name'] = urlencode($name);
     return $this->_write_file($this->DB['file_adb_grp'], $data, TRUE);
 }

 // Check, wether a group name for a ceratin user already exists
 // Input  : adb_checkfor_groupname(integer owner, string groupname)
 // Returns: group id if yes, FALSE otherwise
 function adb_checkfor_groupname($uid = 0, $name = '')
 {
     if (!file_exists($this->DB['file_adb_grp']) || !is_readable($this->DB['file_adb_grp'])) return FALSE;
     foreach (parse_ini_file($this->DB['file_adb_grp'], 1) as $k => $v) {
           if ($v['name'] == $name) return $k;
     }
     return FALSE;
 }

 // Delete a given group from address book
 // Input  : adb_dele_group(integer group id)
 // Returns: TRUE on success or FALSE on failure
 function adb_dele_group($gid = 0)
 {
     if (!$gid) return FALSE;
     if (!file_exists($this->DB['file_adb_grp']) || !is_readable($this->DB['file_adb_grp'])) return array();
     $ini = parse_ini_file($this->DB['file_adb_grp'], 1);
     unset($ini[$gid]);
     return $this->_write_file($this->DB['file_adb_grp'], $ini, FALSE);
 }

 // Placeholder method only, not supported by this driver
 function adb_get_bday_list($uid = 0, $groups = 0, $days = 7)
 {
     return array();
 }

}
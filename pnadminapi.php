<?php
/**
 * @package      ContactList
 * @version      $Id: $
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Get group configuration
 *
 * This function gets the group configuration it inividualtemplating 
 * is disabled just for some zikula groups
 *
 * @return	array
 */
function ContactList_adminapi_getGroupsConfiguration()
{
  	$groups = pnModAPIFunc('Groups','user','getall');
  	$disabledgroups = pnModGetVar('ContactList','disabledgroups');
  	$result = array();
  	foreach ($groups as $group) {
  	  	$gid = $group['gid'];
	    if (in_array($gid,$disabledgroups)) {
		  	$disabled = 1;
		}
	    else {
			$disabled = 0;
		}
		$result[] = array(	'gid' 		=> $gid, 
							'disabled' 	=> $disabled,
							'name' 		=> $group['name']
							);
	}
	return $result;
}
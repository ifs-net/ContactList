<?php
/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
 

/**
 * Delete a user in the module "UserPictures"
 * 
 * @param	$args['uid']	int		user id
 * @return	array   
 */
function ContactList_userdeletionapi_delUser($args)
{
  	$uid = $args['uid'];
	if (!pnModAPIFunc('UserDeletion','user','SecurityCheck',array('uid' => $uid))) {
	  	$result 	= _NOTHINGDELETEDNOAUTH;
	}
	else {
	    $tables 			=& pnDBGetTables();
	  	// Delete all ignore list entries
	    $buddycolumn 		= &$tables['contactlist_buddylist_column'];
		$where 				= $buddycolumn['uid']." = ".$uid." OR ".$buddycolumn['bid']." = ".$uid;
		$objArray 			= DBUtil::selectObjectArray('contactlist_buddylist',$where);
		foreach ($objArray as $obj) DBUtil::deleteObject($obj,'contactlist_buddylist');
		$result.= (count($objArray)/2)." "._CONTACTLISTBUDDYLISTENTRIESAND." ";

	    $ignorecolumn 		= &$tables['contactlist_ignorelist_column'];
		$where 				= $ignorecolumn['uid']." = ".$uid." OR ".$ignorecolumn['iuid']." = ".$uid;
		$objArray 			= DBUtil::selectObjectArray('contactlist_ignorelist',$where);
		foreach ($objArray as $obj) DBUtil::deleteObject($obj,'contactlist_ignorelist');
		$result.= count($objArray)." "._CONTACTLISTIGNORELISTENTRIESDELETEDFOR." ";
		$result.= pnUserGetVar('uname',$uid);
	}
	return array(
			'title' 	=> _CONTACTLISTMODULETITLE,
			'result'	=> $result

		);
}
?>
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
 * initialise the ContactList module
 *
 * @return       bool       true on success, false otherwise
 */
function ContactList_init()
{

    if (!DBUtil::createTable('contactlist_ignorelist')) return false;
    if (!DBUtil::createTable('contactlist_buddylist')) return false;

    // Set some default values for module variables
    pnModSetVar('ContactList','usemyprofilebirthday','');
    pnModSetVar('ContactList','myprofilebirthday','');
    pnModSetVar('ContactList','useprofilebirthday','');
    pnModSetVar('ContactList','profilebirthday','');
    pnModSetVar('ContactList','useignore',1);
    pnModSetVar('ContactList','noconfirm',0);
    pnModSetVar('ContactList','itemsperpage',10);
    pnModSetVar('ContactList','nopubliccomment',0);
    pnModSetVar('ContactList','dateformat','%d.%m.%Y');
    pnModSetVar('ContactList','nopublicbuddylist',0);

    // Initialisation successful
    return true;
}

/**
 * delete the ContactList module
 *
 * @return       bool       true on success, false otherwise
 */
function ContactList_delete()
{
    // Delete all attributes we set to manage the user's preferences
    $res = DBUtil::deleteObjectByID('objectdata_attributes','contactlist_publicstate','attribute_name');
    if (!$res) return false;

    // Delete the table
    if (!DBUtil::dropTable('contactlist_ignorelist')) return false;
    if (!DBUtil::dropTable('contactlist_buddylist')) return false;

    // Delete all module variables
    pnModDelVar('ContactList');

    // Deletion successful
    return true;
}

function ContactList_upgrade($oldVersion)
{
	// Upgrade dependent on old version number
	switch($oldVersion)
		{
	    case '1.1':
	    case '1.0':
	    	// table structure changed!
	    	if (!DBUtil::changeTable('contactlist_buddylist')) return false;
	    case '1.2':
	    case '1.3':
	    case '1.4':
	    case '1.3':
	}
    // Update successful
	return true;
}

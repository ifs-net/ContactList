<?php
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
	pnModSetVar('usemyprofilebirthday','usemyprofilebirthday','');
	pnModSetVar('myprofilebirthday','myprofilebirthday','');
	pnModSetVar('useprofilebirthday','useprofilebirthday','');
	pnModSetVar('profilebirthday','profilebirthday','');
	pnModSetVar('ContactList','useignore',1);
	pnModSetVar('ContactList','noconfirm',0);
	pnModSetVar('ContactList','nopubliccomment',0);
    pnModSetVar('ContactList','dateformat','%d.%m.%Y'));	


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
    // Delete the table
    if (!DBUtil::dropTable('contactlist_ignorelist')) return false;
    if (!DBUtil::dropTable('contactlist_buddylist')) return false;

	// Delete all module variables
	pnModDelVar('ContactList');
	
    // Deletion successful
    return true;
}
?>
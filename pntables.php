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
 * Populate pntables array
 *
 * @return       array       The table information.
 */
function ContactList_pntables()
{
    // Initialise table array
    $pntable = array();

    // Set the table name
    $pntable['contactlist_ignorelist'] = DBUtil::getLimitedTablename('contactlist_ignorelist');
    $pntable['contactlist_buddylist'] = DBUtil::getLimitedTablename('contactlist_buddylist');

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $pntable['contactlist_ignorelist_column'] = array(
						'id'			=> 'id',
						'uid'			=> 'uid',
						'iuid'			=> 'iuid'
						);

						$pntable['contactlist_ignorelist_column_def'] = array (
						'id'			=>	"I NOTNULL AUTO PRIMARY",
						'uid'			=> 	"I NOTNULL DEFAULT 0",
						'iuid'			=> 	"I NOTNULL DEFAULT 0"
						);

						$pntable['contactlist_buddylist_column'] = array(
						'id'			=> 'id',
						'uid'			=> 'uid',
						'bid'			=> 'bid',
						'prv_comment'	=> 'prv_comment',
						'pub_comment'	=> 'pub_comment',
						'request_text'	=> 'request_text',
						'date'			=> 'date',
						'state'			=> 'state'		// 0 = unconfirmed 	(one way!)
						// 1 = confirmed	(both directions!)
						// 2 = rejected		(one way!)
						// 3 = suspended	(one way!)
						);

						$pntable['contactlist_buddylist_column_def'] = array (
						'id'			=>	"I NOTNULL AUTO PRIMARY",
						'uid'			=> 	"I NOTNULL DEFAULT 0",
						'bid'			=> 	"I NOTNULL DEFAULT 0",
						'prv_comment'	=>	"XL NOTNULL DEFAULT ''",
						'pub_comment'	=>	"XL NOTNULL DEFAULT ''",
						'request_text'	=>	"XL NOTNULL DEFAULT ''",
						'date'			=>	"T",
						'state'			=>	"I(1) NOTNULL DEFAULT 0"
						);

						// Return the table information
						return $pntable;
}

<?php
/**
 * ContactList
 *
 * @copyright Florian Schießl, Carsten Volmer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package ContactList
 * @author Florian Schießl <info@ifs-net.de>.
 * @link http://www.ifs-net.de
 */

/**
 * Populate tables array
 *
 * @return       array       The table information.
 */
function ContactList_tables()
{
    // Initialise table array
    $table = array();

    // Set the table name
    $table['contactlist_ignorelist'] = 'contactlist_ignorelist';
    $table['contactlist_buddylist'] = 'contactlist_buddylist';
    $table['contactlist_watchlist'] = 'contactlist_watchlist';

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.
    $table['contactlist_ignorelist_column'] = array(
                        'id'			=> 'id',
                        'uid'			=> 'uid',
                        'iuid'			=> 'iuid'
                        );

    $table['contactlist_ignorelist_column_def'] = array (
                        'id'			=>	"I NOTNULL AUTO PRIMARY",
                        'uid'			=> 	"I NOTNULL DEFAULT 0",
                        'iuid'			=> 	"I NOTNULL DEFAULT 0"
                        );

    $table['contactlist_watchlist_column'] = array(
                        'id'			=> 'id',
                        'uid'			=> 'uid',
                        'wuid'			=> 'wuid',
                        'date'			=> 'date',
                        'prv_comment'	=> 'prv_comment'
                        );

    $table['contactlist_watchlist_column_def'] = array (
                        'id'			=>	"I NOTNULL AUTO PRIMARY",
                        'uid'			=> 	"I NOTNULL DEFAULT 0",
                        'wuid'			=> 	"I NOTNULL DEFAULT 0",
                        'date'			=>	"T",
                        'prv_comment'	=>	"C(255) NOTNULL"
                        );

    $table['contactlist_buddylist_column'] = array(
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

    $table['contactlist_buddylist_column_def'] = array (
                        'id'			=>	"I NOTNULL AUTO PRIMARY",
                        'uid'			=> 	"I NOTNULL DEFAULT 0",
                        'bid'			=> 	"I NOTNULL DEFAULT 0",
                        'prv_comment'	=>	"C(255) NOTNULL",
                        'pub_comment'	=>	"C(160) NOTNULL",
                        'request_text'	=>	"C(160) NOTNULL",
                        'date'			=>	"T",
                        'state'			=>	"I(1) NOTNULL DEFAULT 0"
                        );

    // Return the table information
    return $table;
}
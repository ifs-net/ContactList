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
 * Return an array of items to show in the your account panel
 *
 * @return   array
 */
function ContactList_accountapi_getall($args)
{
    // Create an array of links to return
    pnModLangLoad('ContactList');
    $items = array(array('url'     => pnModURL('ContactList', 'user','main'),
                         'title'   => _CONTACTLISTMYCONTACTS,
                         'icon'    => 'userbutton.gif'));
    // Return the items
    return $items;
}


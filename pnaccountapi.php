<?php
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

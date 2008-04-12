<?php
/**
 * @package      ContactList
 * @version      $Id$ 
 * @author       Florian Schie�l, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * This function returns the name of the tab
 *
 * @return string
 */
function ContactList_myprofileapi_getTitle($args)
{
    pnModLangLoad('ContactList');
    return _CONTACTLISTTABTITLE;
}

/**
 * This function returns additional options that should be added to the plugin url
 *
 * @return string
 */
function ContactList_myprofileapi_getURLAddOn($args)
{
    return '';
}

/**
 * This function shows the content of the main MyProfile tab
 *
 * @return output
 */
function ContactList_myprofileapi_tab($args)
{
   	// generate output
    $render = pnRender::getInstance('ContactList');
    $render->assign('uid',(int)$args['uid']);
    $buddies = pnModAPIFunc('ContactList','user','getall', array('uid' => $args['uid'], 'state' => 1 ) );
    $render->assign('buddies',$buddies);
    $render->assign('nopubliccomment',pnModGetVar('ContactList','nopubliccomment'));
    $render->display('contactlist_myprofile_tab.htm');
    return;
}


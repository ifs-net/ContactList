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
    // check if list should be shown
    $nopublicbuddylist = (int)pnModGetVar('ContactList','nopublicbuddylist');
    pnModLangLoad('ContactList');
    // check for privacy settings
    $prefs = pnModAPIFunc('ContactList','user','getPreferences',array('uid' => $args['uid']));
    $display = false;
    if ($args['uid'] != pnUserGetVar('uid')) switch ($prefs['publicstate']) {
        case 1:		$display=false;
        break;
        case 2:		$isBuddy = pnModAPIFunc('ContactList','user','isBuddy',array('uid1' => $uid, 'uid2' => pnUserGetVar('uid')));
        if ($isBuddy > 0) $display = true;
        break;
        case 3:		if (pnUserLoggedIn()) $display = true;
        break;
        default: 	return LogUtil::registerPermissionError();
        break;
    }
    else $display = true;

    // generate output
    $render = pnRender::getInstance('ContactList');

    if (($nopublicbuddylist == 1) or (!$display)) $render->assign('display',false);
    else $render->assign('display',true);

    $render->assign('uid',(int)$args['uid']);
    $render->assign('viewer_uid',pnUserGetVar('uid'));
    $buddies = pnModAPIFunc('ContactList','user','getall', array('uid' => $args['uid'], 'state' => 1 ) );
    $render->assign('contacts_all',count($buddies));
    $render->assign('contactlistavailable',	pnModAvailable('ContactList'));
    if (pnModAvailable('ContactList')) $render->assign('contactlist_nopublicbuddylist',	pnModGetVar('ContactList','nopublicbuddylist'));
    // pagination
    $cl_limit 		= pnModGetVar('ContactList','itemsperpage');
    $cl_startnum	= (int)FormUtil::getPassedValue('cl_startnum',1);
    $render->assign('cl_limit',		$cl_limit);
    $render->assign('cl_startnum',	$cl_startnum);
    // now just give back the buddy list we need for this page
    // I know this is not really very performant - but there is no other way to do this because
    // of the data and the sort criterias, that are included in the result list
    $c = 1;
    $c_start = $cl_startnum;
    $c_stop = $cl_startnum + $cl_limit;
    foreach ($buddies as $buddy) {
        if (($c>=$c_start) && ($c < $c_stop)) $assign_buddies[]=$buddy;
        $c++;
    }
    $render->assign('buddies',$assign_buddies);
    // public comments
    $render->assign('nopubliccomment',pnModGetVar('ContactList','nopubliccomment'));
    $render->display('contactlist_myprofile_tab.htm');
    return;
}


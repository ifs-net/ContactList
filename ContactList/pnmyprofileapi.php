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
 * This function returns 1 if Ajax should not be used loading the plugin
 *
 * @return string
 */

function ContactList_myprofileapi_noAjax($args)
{
  	return true;
}

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
    if ($args['uid'] != pnUserGetVar('uid')) {
      if (pnUserGetVar('uid') == 3230) prayer($prefs);
		switch ($prefs['publicstate']) {
	        case 1:
				$display=false;
	        	break;
	        case 2:
				$display = pnModAPIFunc('ContactList','user','isBuddy',array('uid1' => $args['uid'], 'uid2' => pnUserGetVar('uid')));
			    break;
	        case 3:
				$display = pnUserLoggedIn();
	        	break;
	        default:
				return LogUtil::registerPermissionError();
        		break;
    	}
    }
    else $display = true;

    // generate output
    $render = pnRender::getInstance('ContactList');
    if (($nopublicbuddylist == 1) or (!$display)) $render->assign('display',0);
    else $render->assign('display',1);

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
	$render->assign('viewer_uid',pnUserGetVar('uid'));
    $output = $render->fetch('contactlist_myprofile_tab.htm');
    return $output;
}


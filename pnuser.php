<?php
/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

Loader::requireOnce('modules/ContactList/includes/handlers.php');

/**
 * the main user function
 *
 * @return       output
 */
function ContactList_user_main()
{
    // Security check
    if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // check for action
    $action = FormUtil::getPassedValue('action', '');
    if (!empty($action) && !(SecurityUtil::confirmAuthKey())) return Logutil::registerAuthIDError();
    if ($action == "decline") {
        if (pnModAPIFunc('ContactList','user','decline',array('id'=>(int)FormUtil::getPassedValue('id')))) LogUtil::registerStatus(_CONTACTLISTREQUESTDECLINED);
        else LogUtil::registerError(_CONTACTLISTREQUESTDECLINEERR);
    }
    else if ($action == "confirm") {
        if (pnModAPIFunc('ContactList','user','confirm',array('id'=>(int)FormUtil::getPassedValue('id')))) LogUtil::registerStatus(_CONTACTLISTREQUESTACCEPTED);
        else LogUtil::registerError(_CONTACTLISTREQUESTACCERR);
    }
    else if ($action == "suspend") {
        if (pnModAPIFunc('ContactList','user','suspend',array('id'=>(int)FormUtil::getPassedValue('id')))) LogUtil::registerStatus(_CONTACTLISTBUDDYSUSPENDED);
        else LogUtil::registerError(_CONTACTLISTSUSPENDERROR);
    }

    // redirect after any action to avoid auth-id problems
    if (!empty($action)) return pnRedirect(pnModURL('ContactList','user','main',array('state'=>FormUtil::getPassedValue('state'))));

    // check if the result should be sorted
    $birthday	= FormUtil::getPassedValue('birthday',	true);
    $sort		= FormUtil::getPassedValue('sort',		'uname');
    // some validations
    if (($sort == 'birthday') || ($sort == 'nextbirthday') || ($sort == 'daystonextbirthday')) $birthday = true;	// for this sort criteria we need the user's birthday
    else if (($sort != 'state') && ($sort != 'uname')) $sort = 'uname';	// this is just a check if the agument $sort is valid

    // Create output
    $render = FormUtil :: newpnForm('ContactList');

    // assign data
    $uid = pnUserGetVar('uid');
    $render->assign('sort',FormUtil::getPassedValue('sort'));
    $render->assign('dateformat',pnModGetVar('ContactList','dateformat'));

    // unconfirmed buddies are always assigned
    $render->assign('buddies_unconfirmed',pnModAPIFunc('ContactList','user','getall',
    array(	'bid'		=> $uid,
		    		'sort'		=> $sort,
		    		'birthday'	=> $birthday,
					'state'		=> 0 ) ));

    // Do some filtering? state 1,2,3 is possibe
    $state = FormUtil::getPassedValue('state');

    $buddies = array();

    if ($state != "") {
        $buddies = pnModAPIFunc('ContactList','user','getall',
        array(	'uid'		=> $uid,
				'state'		=> $state,
				'birthday'	=> $birthday,
	    		'sort'		=> $sort
        ) );
    }
    else {	// assign and fetch all data all we have otherwise
        $buddies_pending = pnModAPIFunc('ContactList','user','getall',
        array(	'uid'		=> $uid,
				'birthday'	=> $birthday,
	    		'sort'		=> $sort,
				'state'		=> 0 ) );
        $buddies_confirmed = pnModAPIFunc('ContactList','user','getall',
        array(	'uid'		=> $uid,
				'state'		=> 1,
				'birthday'	=> $birthday,
	    		'sort'		=> $sort
        ) );
        $buddies_rejected = pnModAPIFunc('ContactList','user','getall',
        array(	'uid'		=> $uid,
				'birthday'	=> $birthday,
	    		'sort'		=> $sort,
				'state'		=> 2 ) );
        $buddies_suspended = pnModAPIFunc('ContactList','user','getall',
        array(	'uid'		=> $uid,
				'birthday'	=> $birthday,
	    		'sort'		=> $sort,
				'state'		=> 3 ) );
        if (is_array($buddies_pending)) 	foreach ($buddies_pending   as $buddy) $buddies[]=$buddy;
        if (is_array($buddies_confirmed)) 	foreach ($buddies_confirmed as $buddy) $buddies[]=$buddy;
        if (is_array($buddies_suspended)) 	foreach ($buddies_suspended as $buddy) $buddies[]=$buddy;
        if (is_array($buddies_rejected)) 	foreach ($buddies_rejected  as $buddy) $buddies[]=$buddy;
        // let's sort the buddies array
        $buddies = _cl_sortList($buddies,$sort);
    }
    $render->assign('contacts_all',count($buddies));
    $render->assign('state',$state);
    $render->assign('contacts',count($buddies_confirmed));
    $render->assign('nopubliccomment',(int)pnModGetVar('ContactList','nopubliccomment'));
    $render->assign('nopublicbuddylist',(int)pnModGetVar('ContactList','nopublicbuddylist'));
    $render->assign('authid',SecurityUtil::generateAuthKey());
    // pagination
    $cl_limit 		= pnModGetVar('ContactList','itemsperpage');
    $cl_startnum	= (int)FormUtil::getPassedValue('cl_startnum',1);
    $render->assign('cl_limit',		$cl_limit);
    $render->assign('cl_startnum',	$cl_startnum);
    // now just give back the buddy list we need for this page
    // I know this is not really very performant - but there is no other way to do this because
    // of the data and the sort criterias, that are included in the result list

    $numBuddies = count($buddies) - $cl_startnum;
    if ($cl_limit > $numBuddies) $cl_limit = $numBuddies+1;
    $c_stop = $cl_startnum + $cl_limit;
    for ($c = $cl_startnum-1; $c < $c_stop-1; $c++) {
        $assign_buddies[] = $buddies[$c];
    }

    $render->assign('buddies',$assign_buddies);
    // return output
    return $render->pnFormExecute('contactlist_user_main.htm', new contactlist_user_mainHandler());
}

/**
 * the main user function
 *
 * @return       output
 */
function ContactList_user_preferences()
{
    // Security check
    if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // Create output
    $render = FormUtil :: newpnForm('ContactList');

    // assign some data
    $render->assign('nopubliccomment',(int)pnModGetVar('ContactList','nopubliccomment'));

    // return output
    return $render->pnFormExecute('contactlist_user_preferences.htm', new contactlist_user_preferencesHandler());
}

/**
 * display a user's buddy list
 *
 * @return       output
 */
function ContactList_user_display()
{

    // check if buddy list is public
    if (pnModGetVar('ContactList','nopublicbuddylist')) return LogUtil::registerPermissionError();
    $uid = (int) FormUtil::getPassedValue('uid');
    if (!$uid) return LogUtil::registerError(_GETFAILED);

    // Security Check
    if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_READ)) return LogUtil::registerPermissionError();

    $display = false;
    if (pnUserGetVar('uid') != $uid) {
        // check for privacy settings
        $prefs = pnModAPIFunc('ContactList','user','getPreferences',array('uid' => $uid));
        switch ($prefs['publicstate']) {
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
    }
    else $display = true;
   	// generate and return output
    $render = pnRender::getInstance('ContactList');
    if (!$display) return $render->fetch('contactlist_user_nodisplay.htm');
    $render->assign('uid',$uid);
    $buddies = pnModAPIFunc('ContactList','user','getall', array('uid' => $uid, 'state' => 1 ) );
    $render->assign('buddies',$buddies);
    $render->assign('nopubliccomment',(int)pnModGetVar('ContactList','nopubliccomment'));
    return $render->fetch('contactlist_user_display.htm');
}

/**
 * edit additional information for a buddy
 *
 * @param	$args['id']		int		buddy id
 * @return	output
 */
function ContactList_user_edit()
{
    // Security check
    if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // Create output
    $render = FormUtil :: newpnForm('ContactList');

    // assign some data
    $render->assign('nopubliccomment',(int)pnModGetVar('ContactList','nopubliccomment'));

    // return output
    return $render->pnFormExecute('contactlist_user_edit.htm', new contactlist_user_editHandler());
}

/**
 * edit additional information for a buddy
 *
 * @param	$args['id']		int		buddy id
 * @return	output
 */
function ContactList_user_ignore()
{
    // Security check
    if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // Create output
    $render = FormUtil :: newpnForm('ContactList');
    $render->assign('ignorelist',pnModAPIFunc('ContactList','user','getallignorelist',array('uid' => pnUserGetVar('uid'), 'sort' => 'iuname')));
    $render->assign('authid',SecurityUtil::generateAuthKey());

    // check for action
    $action = FormUtil::getPassedValue('action');
    if (isset($action) && (strtolower($action) == 'delete')) {
        if (!SecurityUtil::confirmAuthKey()) return LogUtil::registerAuthIDError();
        $iuid = (int)FormUtil::getPassedValue('iuid');
        if (pnModAPIFunc('ContactList','user','deleteIgnoredUser',array('iuid' => $iuid))) LogUtil::registerStatus(_CONTACTLISTUSERNOLONGERIGNORED);
        else LogUtil::registerError(_CONTACTLISTUSERUPDATEERROR);
        return pnRedirect(pnModURL('ContactList','user','ignore'));
    }

    // return output
    return $render->pnFormExecute('contactlist_user_ignore.htm', new contactlist_user_ignoreHandler());
}

/**
 * create buddy request
 *
 * @return       output
 */
function ContactList_user_create()
{
    // Security check
    if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // Create output
    $render = FormUtil :: newpnForm('ContactList');
    $render->assign('noconfirm',pnModGetVar('ContactList','noconfirm'));
    $render->assign('nopubliccomment',pnModGetVar('ContactList','nopubliccomment'));

    // return output
    return $render->pnFormExecute('contactlist_user_create.htm', new contactlist_user_createHandler());
}
?>
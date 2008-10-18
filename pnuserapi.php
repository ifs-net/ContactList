<?php
/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

Loader::requireOnce('modules/ContactList/includes/common.php');

/**
 * get all buddys
 *
 * @param   $args['uid']        int         user's id to get his buddies
 * @param   $args['bid']        int         buddy's id to get users that have this person as buddy
 * @param   $args['state']      int         filter different states
 *                                          0 = unconfirmed
 *                                          1 = confirmed
 *                                          2 = rejected
 *                                          3 = suspended
 * @param   $args['birthday']   boolean     default: false; include birthday in result
 * @param   $args['sort']       string      default: no sort order.
 *                                          options: birthday, nextbirthday, daystonextbirthday, state
 * @return	array
 */
function ContactList_userapi_getall($args) {
    // filter for buddy or user id
    if (isset($args['uid']) && isset($args['bid'])) $where='uid = '.(int)$args['uid'].' and bid = '.(int)$args['bid'];
    else if (isset($args['uid'])) $where = 'uid = '.(int)$args['uid'];
    else if (isset($args['bid'])) $where = 'bid = '.(int)$args['bid'];

    // filter if state should be filtered
    if (!isset($where) && isset($args['state'])) $where = 'state = '.(int)$args['state'];
    if (isset($where) && isset($args['state'])) $where.= ' and state = '.(int)$args['state'];

    $sort = (isset($args['sort']) && !empty($args['sort'])) ? $args['sort'] : '';
    $birthday = isset($args['birthday']) ? $args['birthday'] : false;

    // return objects
    $res = DBUtil::selectObjectArray('contactlist_buddylist',$where);
    if (count($res) >0) {
        if ($birthday) {
            $myprofile = (pnModGetVar('ContactList','usemyprofilebirthday') && pnModAvailable('MyProfile'));
            $profile = (pnModGetVar('ContactList','useprofilebirthday') && pnModAvailable('Profile'));

            // some preparations for the birthday days calculation
            $now = mktime(23, 59, 59, date("m",time()), date("d",time()), date("Y",time()));
            $year = date("Y",$now);

            if ($myprofile)	{					// if myprofile is activated and used as birthday date provider continue ;-)
                $myprofilebirthday = pnModGetVar('ContactList','myprofilebirthday');
                foreach ($res as $item) {
                    $data = pnModAPIFunc('MyProfile','user','getUserVars',array('name' => $myprofilebirthday, 'uid' => $item['bid']));
                    $item['birthday'] = $data['value'];
                    $item['nextbirthday'] = $item['birthday'][5].$item['birthday'][6].$item['birthday'][8].$item['birthday'][9];
                    // calculate days to next birthday
                    if ($item['birthday'] != '') {
                        $birth_array = explode("-",$item['birthday']);
                        $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], $year);
                        if ($act_birthday < $now) $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], ($year+1));
                        $item['daystonextbirthday'] = round(($act_birthday-$now)/60/60/24);
                    }
                    else $item['daystonextbirthday'] = -1;
                    $r[] = $item;
                }
                $result = $r;
            }

            else if ($profile) {				// otherwise we'll use the regular profile plugin
                $profilebirthday = pnModGetVar('ContactList','profilebirthday');
                foreach ($res as $item) {
                    $item['birthday'] = pnUserGetVar($profilebirthday,$item['bid']);

                    $item['nextbirthday'] = $item['birthday'][5].$item['birthday'][6].$item['birthday'][8].$item['birthday'][9];
                    // calculate days to next birthday
                    if ($item['birthday'] != '') {
                        $birth_array = explode("-",$item['birthday']);
                        $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], $year);
                        if ($act_birthday < $now) $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], ($year+1));
                        $item['daystonextbirthday'] = round(($act_birthday-$now)/60/60/24);
                    }
                    else $item['daystonextbirthday'] = -1;
                    $r[] = $item;
                }
                $result = $r;
            }
            else $result = $res;					// no MyProfile or Profile but a birthday request...
        }
        else $result = $res;
    }
    else return;

    // add onlinestatus and username and sort and returl the result
    return _cl_sortList(_cl_addOnlineStatusAndUsername($result,$args),$sort);
}

/**
 * create a new buddy request or add a new buddy
 *
 * @param   $args['uid']            int         user's id to get his buddies
 * @param   $args['bid']            int         buddy's id to get users that have this person as buddy
 * @param   $args['prv_comment']    string      private comment
 * @param   $args['pub_comment']    string      public comment
 * @param   $args['request_text']   string      request text
 * @return  boolean
 */
function ContactList_userapi_create($args) {
    // some checks
    $uid                = $args['uid'];
    $bid                = $args['bid'];
    $prv_comment        = $args['prv_comment'];
    $pub_comment        = $args['pub_comment'];
    $request_text       = $args['request_text'];
    if (!($uid > 1) || !($bid > 1)) return false;

    // is there an old rejected or suspended connection?
    $result = ContactList_userapi_getall(array('uid' => $bid, 'bid' => $uid));
    if (count($result) == 1) {
        $conn = $result[0];
        // now set the state to 0 or 1 and do not overwrite comments etc.
        // even if confirmation is set to true we will not ask for confirmation
        // because the connection is wanted from both sides now...
        $conn['state'] = 1;
        DBUtil::updateObject($conn,'contactlist_buddylist');
        $nocounterconnection = true;
    }

    // now add or create the request
    $noconfirm = pnModGetVar('ContactList','noconfirm');
    if ($noconfirm) {
        $obj[] = array (
            'bid'           => $bid,
            'uid'           => $uid,
            'state'         => 1,
            'date'          => date("Y-m-d H:i:s"),
            'prv_comment'   => $prv_comment,
            'pub_comment'   => $pub_comment,
        );
        if (!$nocounterconnection) 	$obj[] = array (
            'bid'           => $uid,
            'uid'           => $bid,
            'state'         => 1,
            'date'          => date("Y-m-d H:i:s"),
            'prv_comment'   => $prv_comment,
            'pub_comment'   => $pub_comment,
        );
        if (DBUtil::insertObjectArray($obj,'contactlist_buddylist')) {
            LogUtil::registerStatus(_CONTACTLISTBUDDYADDED);
            // send email
            $render = pnRender::getInstance('ContactList');
            $render->assign('sitename',	pnConfigGetVar('sitename'));
            $render->assign('bid',	$bid);
            $render->assign('uid',	$uid);
            $body = $render->fetch('contactlist_email_add_noconfirm.htm');
            $subject = _CONTACTLISTUNCONFIRMSUBJECT;
            pnMail(pnUserGetVar('email',$bid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);
            return true;
        }
        else return false;
    }
    else {
        $obj = array (
            'bid'           => $bid,
            'uid'           => $uid,
            'state'         => 0,
            'date'          => date("Y-m-d H:i:s"),
            'prv_comment'   => $prv_comment,
            'pub_comment'   => $pub_comment,
            'request_text'  => $request_text
        );
        // update an old rejected connection if needed
        if ($nocounterconnection) $obj['state']=1;
        if (DBUtil::insertObject($obj,'contactlist_buddylist')) {
            LogUtil::registerStatus(_CONTACTLISTREQUESTSENT);
            // send email
            $render = pnRender::getInstance('ContactList');
            $render->assign('sitename',	pnConfigGetVar('sitename'));
            $render->assign('bid',	$bid);
            $render->assign('uid',	$uid);
            $render->assign('nocounterconnection',$nocounterconnection);
            $render->assign('request_text',	$request_text);
            $body = $render->fetch('contactlist_email_add_confirm.htm');
            $subject = _CONTACTLISTCONFIRMSUBJECT;
            pnMail(pnUserGetVar('email',$bid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);
            return true;
        }
        else return false;
    }
    return true;
}

/**
 * reject buddy request
 *
 * @param   $args['id']     int     id of buddy request
 * @return  bool
 */
function ContactList_userapi_decline($args) {
    // get object
    $id = (int)$args['id'];
    $uid = pnUserGetVar('uid');
    $obj = DBUtil::selectObjectByID('contactlist_buddylist',$id);
    // only the user that should be a new buddy should be able to decline
    if ($obj['bid'] != $uid) return false;

    // change state to "2, rejected"
    $obj['state'] = 2;
    if (!DBUtil::updateObject($obj,'contactlist_buddylist')) {
        return LogUtil::registerError('error updating buddy object');
    }

    // send email
    $render = pnRender::getInstance('ContactList');
    $render->assign('sitename',	pnConfigGetVar('sitename'));
    $render->assign('bid',	$obj['bid']);
    $render->assign('uid',	$obj['uid']);
    $body = $render->fetch('contactlist_email_rejected.htm');
    $subject = _CONTACTLISTREQUESTREJECTED;
    // the counter part - not the one who clicked to accept - should get the email
    if (pnUserGetVar('uid') == $obj['bid']) $uid = $obj['uid'];
    else $uid = $obj['bid'];
    pnMail(pnUserGetVar('email',$uid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);
    return true;
}

/**
 * delete a buddy
 *
 * @param   $args['id']     int     id of buddy request
 * @return  bool
 */
function ContactList_userapi_suspend($args) {
    // get object
    $id = (int)$args['id'];
    $uid = pnUserGetVar('uid');
    $obj = DBUtil::selectObjectByID('contactlist_buddylist',$id);
    // Security check: only the user that is a buddy can suspend the connection
    if ($obj['uid'] != $uid) return false;

    // if the connection is only in one direction (already suspended) just delete the object
    if ($obj['state'] == 2) return DBUtil::deleteObject($obj,'contactlist_buddylist');
    else if ($obj['state'] == 3) return DBUtil::deleteObject($obj,'contactlist_buddylist');
    else if ($obj['state'] == 0) {	// now we have to handle an request with no response!
        // is the request old enough to be deleted? otherwise a user might nerve other users
        // sending and deleting requests in a loop
        $date_now       = time();
        $date_request   = strtotime($obj['date'].' GMT');
        $date_diff      = $date_now-$date_request;
        if ($date_diff > (60*60*24*30)) return DBUtil::deleteObject($obj,'contactlist_buddylist');
        else {
            return LogUtil::registerError(_CONTACTLISTCANNOTDELETEYET);
        }
    }
    // get the counterpart
    $counter_obj = pnModAPIFunc('ContactList','user','getall',array('uid'=>$obj['bid'],'bid'=>$obj['uid']));
    $counter_obj = $counter_obj[0];
    if (!($counter_obj['id']>0)) return false;

    // change state to "3, suspended"
    $counter_obj['state'] = 3;
    if (!DBUtil::updateObject($counter_obj,'contactlist_buddylist')) {
        return LogUtil::registerError('error updating buddy object');
    }

    // delete the old object
    DBUtil::deleteObject($obj,'contactlist_buddylist');

    // send email only if we do not delete a already suspended connection
    $noemail = (int)FormUtil::getPassedValue('ne');
    if (!(isset($ne) && ($ne == 1))) {
        $render = pnRender::getInstance('ContactList');
        $render->assign('sitename',	pnConfigGetVar('sitename'));
        $render->assign('bid',	$obj['bid']);
        $render->assign('uid',	$obj['uid']);
        $body = $render->fetch('contactlist_email_suspended.htm');
        $subject = _CONTACTLISTBUDDYSUSPENDEDYOU;
        if (pnUserGetVar('uid') == $obj['bid']) $uid = $obj['uid'];
        else $uid = $obj['bid'];
        pnMail(pnUserGetVar('email',$uid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);
    }
    return true;
}

/**
 * confirm buddy request
 *
 * @param   $args['id']     int         id of buddy request
 * @return  bool
 */
function ContactList_userapi_confirm($args) {
    // get object
    $id = (int)$args['id'];
    $uid = pnUserGetVar('uid');
    $obj = DBUtil::selectObjectByID('contactlist_buddylist',$id);
    // only the user that should be a new buddy should be able to decline
    if ($obj['bid'] != $uid) return false;

    // change state to "1, acepted" and delete request text
    $obj['state'] = 1;
    $obj['request_text'] = '';

    if (!DBUtil::updateObject($obj,'contactlist_buddylist')) {
        return LogUtil::registerError('error updating buddy object');
    }
    // create counterpart
    $counterobj = array ( 'uid'   => $obj['bid'],
                          'bid'   => $obj['uid'],
                          'date'  => date("Y-m-d H:i:s"),
                          'state' => 1);
    DBUtil::insertObject($counterobj,'contactlist_buddylist');

    // send email
    $render = pnRender::getInstance('ContactList');
    $render->assign('sitename', pnConfigGetVar('sitename'));
    $render->assign('bid', $obj['bid']);
    $render->assign('uid', $obj['uid']);
    $body = $render->fetch('contactlist_email_accepted.htm');
    $subject = _CONTACTLISTREQUESTACCEPTED;
    // the counter part - not the one who clicked to accept - should get the email
    if (pnUserGetVar('uid') == $obj['bid']) $uid = $obj['uid'];
    else $uid = $obj['bid'];
    pnMail(pnUserGetVar('email',$uid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);
    return true;
}

/**
 * Get user preferences
 * This function gets the user preferences if the contact list should be
 * public to all, to friends or to nobody
 * If public contact lists are disabled by the administrator this settings
 * will also be "visible to nobody"
 *
 * @param   $args['uid']    int
 * @return  array           array: 'publicstate' => int { 1 = not visible; 2 = visible for friends; 3 = visible for registered users;}
 */
function ContactList_userapi_getPreferences($args) {
    $uid = (int) $args['uid'];
    // get user and attributes
    $user = DBUtil::selectObjectByID('users', $uid, 'uid', null, null, null, false);
    if (!is_array($user)) return false; // no user data?
    if (!isset($user['__ATTRIBUTES__']) || (!isset($user['__ATTRIBUTES__']['contactlist_publicstate']))) {
        // userprefs for this user do not exist, create them with defaults
        $user['__ATTRIBUTES__']['contactlist_publicstate'] = 2;
        // store attributes
        DBUtil::updateObject($user, 'users', '', 'uid');
    }
    return array('publicstate' => $user['__ATTRIBUTES__']['contactlist_publicstate']);
}

/**
 * Store user preferences
 *
 * @param   $args['uid']            int
 * @param   $args['preferences']    array
 * @return  boolean
 */
function ContactList_userapi_setPreferences($args) {
    $uid = (int) $args['uid'];
    if (!($uid > 1)) return false;
    // check the user attributes for userprefs
    $user = DBUtil::selectObjectByID('users', $uid, 'uid', null, null, null, false);
    if (!is_array($user)) return false; // no user data?
    else {
        $user['__ATTRIBUTES__']['contactlist_publicstate'] = (int)$args['preferences']['publicstate'];
        // store attributes
        return DBUtil::updateObject($user, 'users', '', 'uid');
    }
    return true;
}

/**
 * This function checks if there is a buddy connection between two users
 * and returns a boolean value (true or false)
 * To be used from other module developers!
 *
 * @param   $args['uid1']   int
 * @param   $args['uid2']   int
 * @return  boolean (false) or integer (partnerhip id) if true
 */
function ContactList_userapi_isBuddy($args) {
    $buddies = pnModAPIFunc('ContactList','user','getall',array(
                    'uid'   => (int)$args['uid1'],
                    'bid'   => (int)$args['uid2'],
                    'state' => '1'));
    if (count($buddies) > 0) return (int)$buddies[0]['id'];
    else return false;
}

/**
 * ignore another user
 *
 * @param   $args['uid']    int
 * @param   $args['iuid']   int
 * @return  boolean
 */
function ContactList_userapi_ignoreUser($args) {
    $uid    = (int)$args['uid'];
    $iuid   = (int)$args['iuid'];
    if ($uid == $iuid) return false;
    if (!($uid > 1) || !($iuid > 1)) return false;
    $obj = array (
                'uid'   => $uid,
                'iuid'  => $iuid
    );
    if (ContactList_userapi_isIgnored($args)) return false;
    if (DBUtil::insertObject($obj,'contactlist_ignorelist')) return true;
    else return false;
}

/**
 * get all user that are ignored by another user all users that ignore another user
 *
 * @param   $args['uid']    int         user id
 * @param   $args['iuid']   int         ignored user id
 * @param   $args['sort']   string      identifier value to sort the list for (iuname,uname)
 * @return  boolean
 */
function ContactList_userapi_getallignorelist($args) {

    // return false if ignore list functionallity is disabled by the admin
    if (!pnModGetVar('ContactList','useignore')) return false;
    // otherwise do some checks
    $uid    = (int)$args['uid'];
    $iuid   = (int)$args['iuid'];
    if (($uid > 1) && ($iuid > 1)) $where = 'iuid = '.$iuid.' and uid = '.$uid;
    else if ($uid > 1) $where = 'uid = '.$uid;
    else if ($iuid > 1) $where = 'iuid = '.$iuid;
    // get database result
    $res = DBUtil::selectObjectArray('contactlist_ignorelist',$where);
    foreach ($res as $item) {
        $item['uname'] 	= pnUserGetVar('uname',$item['uid']);
        $item['iuname']	= pnUserGetVar('uname',$item['iuid']);
        $ignorelist[] = $item;
    }

    if (isset($args['sort'])) {		// Apply an "order by"?
        foreach ($ignorelist as $key => $row) {
            if ($args['sort'] == 'iuname') $sort[$key]  = $row['iuname'];
            else if ($args['sort'] == 'uname') $sort[$key]  = $row['uname'];
        }
        array_multisort($sort, SORT_ASC, $ignorelist);
    }
    return $ignorelist;
}

/**
 * check if a user is ignored by another user
 * To be used from other module developers
 *
 * @param   $args['uid']    int         user id
 * @param   $args['iuid']   int         ignored user id
 * @return  boolean
 */
function ContactList_userapi_isIgnored($args) {
    // return false if ignore list functionallity is disabled by the admin
    if (!pnModGetVar('ContactList','useignore')) return false;
    // otherwise do some checks
    $uid    = (int)$args['uid'];
    $iuid   = (int)$args['iuid'];
    if ($uid == $iuid) return false;
    if (!($uid > 1) || !($iuid > 1)) return false;
    $where = 'uid = '.$uid.' and iuid = '.$iuid;
    $res = DBUtil::selectObjectArray('contactlist_ignorelist',$where);
    if (count($res)>0) return true;
    else return false;
}

/**
 * delete a user from the users ignore list
 *
 * @param   $args['iuid']       int
 * @return  boolean
 */
function ContactList_userapi_deleteIgnoredUser($args) {
    $iuid = (int) $args['iuid'];
    $uid = pnUserGetVar('uid');
    if (!isset($iuid) || (!($iuid > 1))) return false;
    // get ignore link
    $objects = ContactList_userapi_getallignorelist(array('uid' => $uid, 'iuid' => $iuid));
    return DBUtil::deleteObject($objects[0],'contactlist_ignorelist');
}

/**
 * This function returns an array including another array with each
 * buddy connection a user has.
 * To be used from other module developers!
 *
 * @param   $args['uid']        int
 * @return  array               uid => user id  uname => uname
 */
function ContactList_userapi_getBuddyList($args) {
    $buddies = pnModAPIFunc('ContactList','user','getall',array(
        'uid'       => (int)$args['uid'],
        'state'     => '1'));
    if (count($buddies)==0) return false;
    foreach ($buddies as $buddy) $res[] = array('uid' => $buddy['bid'], 'uname' => pnUserGetVar('uname',$buddy['bid']));

    return $res;
}

/**
 * get nearest foaf-link for two users
 *
 * @param   $args['uid1']   int
 * @param   $args['uid2']   int
 * @return  output
 */
function ContactList_userapi_getFOAFLink($args) {
    $res = false;
    $uid1 = (int) $args['uid1'];
    $uid2 = (int) $args['uid2'];

    // case 1: user views his own profile
    if (($uid1 == $uid2) || (!($uid1 > 1)) || (!($uid2 > 1))) return false;

    // case 2: user views the profile of a friend
    // uid1, x1, uid2
    if (ContactList_userapi_isBuddy(array('uid1' => $uid1, 'uid2' => $uid2))) {
        $res[] = cl_addToArrayLink($uid1);
        $res[] = cl_addToArrayLink($uid2);
    }
	// before we continue we habe to check if the "target" user has its buddy list not hidden
	$preferences = ContactList_userapi_getPreferences(array('uid' => $uid2));
	if ($preferences['publicstate'] == 1) return false;

  	// get tables and column
   	pnModDBInfoLoad('objectdata');
   	$tables 	= pnDBGetTables();
   	$cltable 	= DBUtil::getLimitedTableName('contactlist_buddylist');
   	$oatable 	= DBUtil::getLimitedTableName('objectdata_attributes');
   	$oacolumn = $tables['objectdata_attributes_column'];
	
	// case3: user views the profile of a friend's friend
	// uid1, x1, uid2
    if (!$res) {
		$sql = '	SELECT DISTINCT
						select_1.uid,
						select_2.uid,
						select_2.bid
				 	FROM 
					 	'.$cltable.' as select_1,
						'.$cltable.' as select_2,
						'.$oatable.' as attributes
					WHERE
						attributes.'.$oacolumn['value'].' > 1 AND
						attributes.'.$oacolumn['attribute_name'].' = \'contactlist_publicstate\' AND
						select_1.bid = select_2.uid AND
						select_1.uid = '.$uid1.' AND
						select_2.bid = '.$uid2.'
						';
		$results = DBUtil::executeSQL($sql);
		foreach ($results as $r) $this_results[] = $r;
		if (count($this_results) > 0) {	// if there is more than one result found we'll shuffle
		  	$nr = mt_rand(1,count($this_results))-1;
		  	$one_result = $this_results[$nr];
		}
		if (is_array($one_result) && (count($one_result) > 0)) {	// we found a result!
		  	$res = array();
		  	foreach ($one_result as $uid) $res[] = cl_addToArrayLink($uid);
		}
	}
	// case3: 
	// uid1, x1, x2, uid2
    if (!$res) {
		$sql = '	SELECT DISTINCT 
						select_1.uid,
						select_2.uid,
						select_3.uid,
						select_3.bid
				 	FROM 
					 	'.$cltable.' as select_1,
						'.$cltable.' as select_2,
						'.$cltable.' as select_3,
						'.$oatable.' as attributes
					WHERE
						attributes.'.$oacolumn['value'].' > 1 AND
						attributes.'.$oacolumn['attribute_name'].' = \'contactlist_publicstate\' AND
						select_1.bid = select_2.uid AND
						select_2.bid = select_3.uid AND
						select_1.uid = '.$uid1.' AND
						select_3.bid = '.$uid2.'
						';
		$results = DBUtil::executeSQL($sql);
		foreach ($results as $r) $this_results[] = $r;
		if (count($this_results) > 0) {	// if there is more than one result found we'll shuffle
		  	$nr = mt_rand(1,count($this_results))-1;
		  	$one_result = $this_results[$nr];
		}
		if (is_array($one_result) && (count($one_result) > 0)) {	// we found a result!
		  	$res = array();
		  	foreach ($one_result as $uid) $res[] = cl_addToArrayLink($uid);
		}
	}
	// case4: 
	// uid1, x1, x2, x3, uid2
    if (!$res) {
		$sql = '	SELECT DISTINCT 
						select_1.uid,
						select_2.uid,
						select_3.uid,
						select_4.uid,
						select_4.bid
				 	FROM 
					 	'.$cltable.' as select_1,
						'.$cltable.' as select_2,
						'.$cltable.' as select_3,
						'.$cltable.' as select_4,
						'.$oatable.' as attributes
					WHERE
						attributes.'.$oacolumn['value'].' > 1 AND
						attributes.'.$oacolumn['attribute_name'].' = \'contactlist_publicstate\' AND
						select_1.bid = select_2.uid AND
						select_2.bid = select_3.uid AND
						select_3.bid = select_4.uid AND
						select_1.uid = '.$uid1.' AND
						select_4.bid = '.$uid2.'
						';
		$results = DBUtil::executeSQL($sql);
		foreach ($results as $r) $this_results[] = $r;
		if (count($this_results) > 0) {	// if there is more than one result found we'll shuffle
		  	$nr = mt_rand(1,count($this_results))-1;
		  	$one_result = $this_results[$nr];
		}
		if (is_array($one_result) && (count($one_result) > 0)) {	// we found a result!
		  	$res = array();
		  	foreach ($one_result as $uid) $res[] = cl_addToArrayLink($uid);
		}
	}
	// case5: 
	// uid1, x1, x2, x3, x4, uid2
    if (!$res) {
		$sql = '	SELECT DISTINCT 
						select_1.uid,
						select_2.uid,
						select_3.uid,
						select_4.uid,
						select_5.uid,
						select_5.bid
				 	FROM 
					 	'.$cltable.' as select_1,
						'.$cltable.' as select_2,
						'.$cltable.' as select_3,
						'.$cltable.' as select_4,
						'.$cltable.' as select_5,
						'.$oatable.' as attributes
					WHERE
						attributes.'.$oacolumn['value'].' > 1 AND
						attributes.'.$oacolumn['attribute_name'].' = \'contactlist_publicstate\' AND
						select_1.bid = select_2.uid AND
						select_2.bid = select_3.uid AND
						select_3.bid = select_4.uid AND
						select_4.bid = select_5.uid AND
						select_1.uid = '.$uid1.' AND
						select_5.bid = '.$uid2.'
						';
		$results = DBUtil::executeSQL($sql);
		foreach ($results as $r) $this_results[] = $r;
		if (count($this_results) > 0) {	// if there is more than one result found we'll shuffle
		  	$nr = mt_rand(1,count($this_results))-1;
		  	$one_result = $this_results[$nr];
		}
		if (is_array($one_result) && (count($one_result) > 0)) {	// we found a result!
		  	$res = array();
		  	foreach ($one_result as $uid) $res[] = cl_addToArrayLink($uid);
		}
	}
	// no more searches now
    $render = pnRender::getInstance('ContactList');
    $render->assign('FOAFList',     $res);
    $render->assign('FOAFLinkDepth',(count($res)-1));
    // load language file for ContactList module (function call might be from other modules)
    pnModLangLoad('ContactList','user');
    $output = $render->display('contactlist_user_foaf.htm');
    print DataUtil::convertToUTF8($output);
    return;
}

function cl_addToArrayLink($uid) {
  	return array (
  		'uid'	=> $uid,
  		'uname'	=> pnUserGetVar('uname',$uid)
	  );
}
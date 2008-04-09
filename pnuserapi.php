<?php

/**
 * get all buddys
 * 
 * @param	$args['uid']		int			user's id to get his buddies
 * @param	$args['bid']		int			buddy's id to get users that have this person as buddy
 * @param	$args['state']		int			filter different states
 *												0 = unconfirmed
 *												1 = accepted
 *												2 = rejected
 *												3 = suspended
 * @param	$args['birthday']	boolean		default: false; include birthday in result
 * @param	$args['uname']		boolean		default: false; include username in result
 * @param	$args['sort']		string		default: no sort order.
 *												options: birthday, uname
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

	// return objects
	$res = DBUtil::selectObjectArray('contactlist_buddylist',$where);
	if (count($res) >0) {
	  	$birthday = $args['birthday'];
	  	if (isset($birthday)) {
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
	// shoud we apply an "order by"?
	if (isset($args['sort'])) {		// Apply an "order by"?
		foreach ($result as $key => $row) {
		    if ($args['sort'] == 'birthday') $sort[$key]  = $row['birthday'];
		    else if ($args['sort'] == 'nextbirthday') $sort[$key]  = $row['nextbirthday'];
		    else if ($args['sort'] == 'daystonextbirthday') $sort[$key]  = $row['daystonextbirthday'];
		}	
		array_multisort($sort, SORT_ASC, $result);
	}
	return $result;
}

/**
 * create a new buddy request or add a new buddy
 * 
 * @param	$args['uid']		int			user's id to get his buddies
 * @param	$args['bid']		int			buddy's id to get users that have this person as buddy
 * @param	$args['prv_comment']	string 		private comment
 * @param	$args['pub_comment']	string 		public comment
 * @param	$args['request_text']	string 		request text
 * @return	boolean
 */
function ContactList_userapi_create($args) {
  	// ToDo: BUG BUG BUG: A lehnt B sienen Antrag ab. A hat zu B eine beziehung, umgekehrt nicht.
  	// dann fragt B A an => es entstehen zwei versch. beziehungen von A zu B dadurch. Update der alten beziehung notwendig!
  	// some checks
  	$uid 			= $args['uid'];
  	$bid 			= $args['bid'];
  	$prv_comment 	= $args['prv_comment'];
  	$pub_comment 	= $args['pub_comment'];
  	$request_text 	= $args['request_text'];
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
	  		'bid'			=> $bid,
	  		'uid'			=> $uid,
	  		'state'			=> 1,
	  		'date'			=> date("Y-m-d H:i:s"),
	  		'prv_comment'	=> $prv_comment,
	  		'pub_comment'	=> $pub_comment,
		  	);
		if (!$nocounterconnection) 	$obj[] = array (
	  		'bid'			=> $uid,
	  		'uid'			=> $bid,
	  		'state'			=> 1,
	  		'date'			=> date("Y-m-d H:i:s"),
	  		'prv_comment'	=> $prv_comment,
	  		'pub_comment'	=> $pub_comment,
		  	);
		if (DBUtil::insertObjectArray($obj,'contactlist_buddylist')) {
		  	LogUtil::registerStatus(_CONTACTLISTBUDDYADDED);
		  	// send email
		  	$render = pnRender::getInstance('ContactList');
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
	  		'bid'			=> $bid,
	  		'uid'			=> $uid,
	  		'state'			=> 0,
	  		'date'			=> date("Y-m-d H:i:s"),
	  		'prv_comment'	=> $prv_comment,
	  		'pub_comment'	=> $pub_comment,
	  		'request_text'	=> $request_text
		  	);
		// update an old rejected connection if needed
	  	if ($nocounterconnection) $obj['state']=1;
		if (DBUtil::insertObject($obj,'contactlist_buddylist')) {
		  	LogUtil::registerStatus(_CONTACTLISTREQUESTSENT);
		  	// send email
		  	$render = pnRender::getInstance('ContactList');
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
 * @param	$args['id']		int			id of buddy request
 * @return	bool
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
  	  	LogUtil::registerError('error updating buddy object');
	    return false;
	}

  	// send email
  	$render = pnRender::getInstance('ContactList');
  	$render->assign('bid',	$obj['bid']);
  	$render->assign('uid',	$obj['uid']);
  	$body = $render->fetch('contactlist_email_rejected.htm');
	$subject = _CONTACTLISTREQUESTREJECTED;
	pnMail(pnUserGetVar('email',$uid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);	
  	return true;
}

/**
 * delete a buddy
 *
 * @param	$args['id']		int			id of buddy request
 * @return	bool
 */
function ContactList_userapi_suspend($args) {
  	// get object
  	$id = (int)$args['id'];
  	$uid = pnUserGetVar('uid');
  	$obj = DBUtil::selectObjectByID('contactlist_buddylist',$id);
  	// only the user that is a buddy can suspend the connection
  	if ($obj['uid'] != $uid) return false;
  	
  	// if the connection is only in one direction (already suspended) just delete the object
  	if ($obj['state'] == 3) return DBUtil::deleteObject($obj,'contactlist_buddylist');
  	// get the counterpart
  	$counter_obj = pnModAPIFunc('ContactList','user','getall',array('uid'=>$obj['bid'],'bid'=>$obj['uid']));
  	$counter_obj = $counter_obj[0];
  	if (!($counter_obj['id']>0)) return false;

  	// change state to "3, suspended"
  	$counter_obj['state'] = 3;
  	if (!DBUtil::updateObject($counter_obj,'contactlist_buddylist')) {
  	  	LogUtil::registerError('error updating buddy object');
	    return false;
	}
  	
	// delete the old object
	DBUtil::deleteObject($obj,'contactlist_buddylist');

  	// send email only if we do not delete a already suspended connection
  	$noemail = (int)FormUtil::getPassedValue('ne');
  	if (!(isset($ne) && ($ne == 1))) {
	  	$render = pnRender::getInstance('ContactList');
	  	$render->assign('bid',	$obj['bid']);
	  	$render->assign('uid',	$obj['uid']);
	  	$body = $render->fetch('contactlist_email_suspended.htm');
		$subject = _CONTACTLISTBUDDYSUSPENDEDYOU;
		pnMail(pnUserGetVar('email',$uid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);	
	}
  	return true;
}

/**
 * confirm buddy request
 *
 * @param	$args['id']		int			id of buddy request
 * @return	bool
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
  	  	LogUtil::registerError('error updating buddy object');
	    return false;
	}
	// create counterpart
	$counterobj = array	(	'uid'	=>	$obj['bid'],
							'bid'	=>	$obj['uid'],
							'date'	=>	date("Y-m-d H:i:s"),
							'state'	=>	1);
  	DBUtil::insertObject($counterobj,'contactlist_buddylist');

  	// send email
  	$render = pnRender::getInstance('ContactList');
  	$render->assign('bid',	$obj['bid']);
  	$render->assign('uid',	$obj['uid']);
  	$body = $render->fetch('contactlist_email_accepted.htm');
	$subject = _CONTACTLISTREQUESTACCEPTED;
	pnMail(pnUserGetVar('email',$uid), $subject, $body, array('header' => '\nMIME-Version: 1.0\nContent-type: text/plain'), false);	
  	return true;
}

/**
 * This function checks if there is a buddy connection between two users
 * and returns a boolean value (true or false)
 * To be used from other module developers!
 *
 * @param	$args['uid1']	int
 * @param	$args['uid2']	int
 * @return	boolean			
 */
function ContactList_userapi_isBuddy($args) {
  	$buddies = pnModAPIFunc('ContactList','user','getall',array(
	  	'uid' 	=> (int)$args['uid1'], 
		'bid' 	=> (int)$args['uid2'],
		'state'	=> '1')					);
  	if (count($buddies) > 0) return true;
  	else return false;
}

/**
 * ignore another user
 *
 * @param	$args['uid']	int
 * @param	$args['iuid']	int
 * @return	boolean
 */
function ContactList_userapi_ignoreUser($args) {
  	$uid 	= (int)$args['uid'];
  	$iuid 	= (int)$args['iuid'];
  	if ($uid == $iuid) return false;
  	if (!($uid > 1) || !($iuid > 1)) return false;
  	$obj = array (
  		'uid'	=> $uid,
  		'iuid'	=> $iuid
	  );	
	if (ContactList_userapi_isIgnored($args)) return false;
  	if (DBUtil::insertObject($obj,'contactlist_ignorelist')) return true;
	else return false;
}

/**
 * get all user that are ignored by another user all users that ignore another user
 *
 * @param	$args['uid']	int			user id
 * @param	$args['iuid']	int			ignored user id
 * @param	$args['sort']	string		identifier value to sort the list for (iuname,uname)
 * @return 	boolean
 */
function ContactList_userapi_getallignorelist($args) {
  
  	// return false if ignore list functionallity is disabled by the admin
  	if (!pnModGetVar('ContactList','useignore')) return false;
  	// otherwise do some checks
  	$uid 	= (int)$args['uid'];
  	$iuid 	= (int)$args['iuid'];
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
		array_multisort(	$sort, SORT_ASC, $ignorelist);
	}
	return $ignorelist;
}

/**
 * check if a user is ignored by another user
 * To be used from other module developers
 *
 * @param	$args['uid']	int			user id
 * @param	$args['iuid']	int			ignored user id
 * @return 	boolean
 */
function ContactList_userapi_isIgnored($args) {
  	// return false if ignore list functionallity is disabled by the admin
  	if (!pnModGetVar('ContactList','useignore')) return false;
  	// otherwise do some checks
  	$uid 	= (int)$args['uid'];
  	$iuid 	= (int)$args['iuid'];
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
 * @param	$args['iuid']	int
 * @return	boolean
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
 * @param	$args['uid']	int
 * @return	array			uid => user id	uname => uname
 */
function ContactList_userapi_getBuddyList($args) {
  	$buddies = pnModAPIFunc('ContactList','user','getall',array(
	  	'uid' 		=> (int)$args['uid'], 
		'state'		=> '1')					);
	if (count($buddies)==0) return false;
	foreach ($buddies as $buddy) $res[] = array('uid' => $buddy['bid'], 'uname' => pnUserGetVar('uname',$buddy['bid']));
	
  	return $res;
}
?>

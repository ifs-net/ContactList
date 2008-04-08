<?php
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
	$action = FormUtil::getPassedValue('action');
//	if (isset($action) && !(SecurityUtil::confirmAuthKey())) return Logutil::registerAuthIDError();
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
	if (isset($action)) return pnRedirect(pnModURL('ContactList','user','main'));
	
	// Create output
	$render = pnRender::getInstance('ContactList');
	
	// assign data
	$uid = pnUserGetVar('uid');
	$render->assign('dateformat',pnModGetVar('ContactList','dateformat'));
	$render->assign('buddies_unconfirmed',pnModAPIFunc('ContactList','user','getall',
									array(	'bid'	=> $uid,
											'state'	=> 0 ) ));
	$buddies_pending = pnModAPIFunc('ContactList','user','getall',
									array(	'uid'	=> $uid,
											'state'	=> 0 ) );
	$buddies_confirmed = pnModAPIFunc('ContactList','user','getall',
									array(	'uid'	=> $uid,
											'state'	=> 1,
											'birthday' => true
											) );
	$buddies_rejected = pnModAPIFunc('ContactList','user','getall',
									array(	'uid'	=> $uid,
											'state'	=> 2 ) );
	$buddies_suspended = pnModAPIFunc('ContactList','user','getall',
									array(	'uid'	=> $uid,
											'state'	=> 3 ) );
	foreach ($buddies_pending   as $buddy) $buddies[]=$buddy;
	foreach ($buddies_confirmed as $buddy) $buddies[]=$buddy;
	foreach ($buddies_suspended as $buddy) $buddies[]=$buddy;
	foreach ($buddies_rejected  as $buddy) $buddies[]=$buddy;
	$render->assign('buddies',$buddies);
	$render->assign('contacts',count($buddies_confirmed));
	$render->assign('nopubliccomment',(int)pnModGetVar('ContactList','nopubliccomment'));
	$render->assign('authid',SecurityUtil::generateAuthKey());
	// return output
	return $render->fetch('contactlist_user_main.htm');
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

/* ********************************************** classes ********************************************** */

class contactlist_user_editHandler {
	function initialize(& $render) {
	  	// get buddy object
	  	$this->id = (int)FormUtil::getPassedValue('id');
	  	if (!($this->id > 0)) return false;
		if ($this->id > 0) {
			$data = DBUtil::selectObjectByID('contactlist_buddylist', $this->id);
			if ($data['uid'] != pnUserGetVar('uid')) {
			  	LogUtil::registerError(_CONTACTLISTFOREIGNBUDDY);  
			  	return pnRedirect(pnModURL('ContactList','user','main'));
			}
			$render->assign($data);
		}
		else {
		  	LogUtil::registerError(_CONTACTLISTBUDDYNOTFOUND);
		  	return pnRedirect(pnModUrl('ContactList','user','main'));
		}
	  	return true;
	}
	function handleCommand(& $render, & $args) {
		if ($args['commandName'] == 'update') {
			if (!$render->pnFormIsValid()) return false;
			$data = $render->pnFormGetValues();
			// get "original" object
			$obj = DBUtil::selectObjectByID('contactlist_buddylist',$this->id);
			if ($obj['uid'] != pnUserGetVar('uid')) {
			  	LogUtil::registerError(_CONTACTLISTFOREIGNBUDDY);  
			  	return pnRedirect(pnModURL('ContactList','user','main'));
			}
			$obj['prv_comment'] = $data['prv_comment'];
			$obj['pub_comment'] = $data['pub_comment'];
			if (DBUtil::updateObject($obj,'contactlist_buddylist')) LogUtil::registerStatus(_CONTACTLISTBUDDYUPDATED);
			else LogUtil::registerStatus(_CONTACTLISTBUDDYUPDATEFAILED);
			return pnRedirect(pnModURL('ContactList','user','main'));
		}
		return true;
	}
}
class contactlist_user_createHandler {
	function initialize(& $render) {
	  	$uname = FormUtil::getPassedValue('uname');
	  	if (isset($uname) && (pnUserGetIDFromName($uname) > 0)) $render->assign('uname',$uname);
	  	return true;
	}
	function handleCommand(& $render, & $args) {
		if ($args['commandName'] == 'create') {
			if (!$render->pnFormIsValid()) return false;
			$data = $render->pnFormGetValues();
			$uname = $data['uname'];
			$bid = pnUserGetIDFromName($uname);
			$uid = pnUserGetVar('uid');
			// own user name
			if ($bid == $uid) {
			  	LogUtil::registerError(_CONTACTLISTNOTADDYOURSELF);
			  	return false;
			}
			// valid user name?
			if (!($bid > 0)) {
			  	LogUtil::registerError(_CONTACTLISTUNAMEINVALID);
			  	return false;
			}
			// already my buddy?
			$buddies = pnModAPIFunc('ContactList','user','getall',array('bid' => $bid, 'uid' => $uid));
			if (count($buddies)>0) {
				LogUtil::registerError(_CONTACTLISTDUPLICATEREQUEST);
				return false;
			}
			if (pnModAPIFunc('ContactList','user','create',array(	
						'uid'			=> $uid,
						'bid'			=> $bid,
						'prvcomment'	=> $data['prvcomment'],
						'pubcomment'	=> $data['pubcomment'],
						'request_text'	=> $data['request_text']
						))) return pnRedirect(pnModURL('ContactList', 'user', 'main'));		
			else return false;
		}
		return true;
	}
}


?>
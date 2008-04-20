<?php
/**
 * @package      ContactList
 * @version      $Id: handlers.php 131 2008-04-19 12:56:02Z herr_vorragend $
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Handler for function " mein"
 */
class contactlist_user_mainHandler {
    function initialize(& $render) {
        $items_sortlist = array (
        array('text' => _CONTACTLISTSORTUNAME, 					'value' => 'uname'),
        array('text' => _CONTACTLISTSORTSTATE,					'value' => 'state'),
        array('text' => _CONTACTLISTSORTAYSTONEXTBIRTHDAY, 		'value' => 'daystonextbirthday'),
        array('text' => _CONTACTLISTSORTBIRTHDAY,				'value' => 'nextbirthday'),
        );
        $render->assign('items_sortlist',$items_sortlist);
        return true;
    }
    function handleCommand(& $render, & $args) {
        if ($args['commandName'] == 'update') {
            $data = $render->pnFormGetValues();
            $render->assign($data);
            return pnRedirect(pnModURL('ContactList','user','main',array('sort' => $data['sort'])));
        }
        return true;
    }
}

/**
 * Handler for function "ignore"
 */
class contactlist_user_ignoreHandler {
    function initialize(& $render) {
        $uname = FormUtil::getPassedValue('uname');
        if (isset($uname) && (pnUserGetIDFromName($uname) > 1)) $render->assign('uname',$uname);
        return true;
    }
    function handleCommand(& $render, & $args) {
        if ($args['commandName'] == 'update') {
            if (!$render->pnFormIsValid()) return false;
            // get data
            $data = $render->pnFormGetValues();
            $iuid = pnUserGetIDFromName($data['uname']);
            $uid = pnUserGetVar('uid');
            // validation check
            if ($uid == $iuid) {
                return LogUtil::registerError(_CONTACTLISTDONOTIGNOREYOURSELF);
            }
            // does the user exist?
            if (!($iuid > 1)) {
                return LogUtil::registerError(_CONTACTLISTUSERNOTFOUND);
            }
            // ToDo: check if user is already ignored

            // ignore the user from now on...
            if (pnModAPIFunc('ContactList','user','ignoreUser',array('uid' => $uid, 'iuid' => $iuid))) LogUtil::registerStatus(_CONTACTLISTIGNOREDUSERADDED);
            else return false;
            return pnRedirect(pnModURL('ContactList','user','ignore'));
        }
        return true;
    }
}

/**
 * Handler for function "edit"
 */
class contactlist_user_editHandler {
    function initialize(& $render) {
        // get buddy object
        $this->id = (int)FormUtil::getPassedValue('id');
        if (!($this->id > 1)) return false;
        if ($this->id > 1) {
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

/**
 * Handler for function "preferences"
 */
class contactlist_user_preferencesHandler {
    function initialize(& $render) {
        // get preferences
        $uid = pnUserGetVar('uid');
        $set = pnModAPIFunc('ContactList','user','getPreferences',array('uid' => $uid));
        $render->assign('publicstate',$set['publicstate']);
        // assign items for dropdown menu
        $items_publicstate = array (
        array('text' => _CONTACTLISTPRIVACYNOBODY, 	'value' => 1),
        array('text' => _CONTACTLISTPRIVACYBUDDIES,	'value' => 2),
        array('text' => _CONTACTLISTPRIVACYMEMBERS,	'value' => 3)
        );
        $render->assign('items_publicstate',$items_publicstate);
        return true;
    }
    function handleCommand(& $render, & $args) {
        if ($args['commandName'] == 'update') {
            if (!$render->pnFormIsValid()) return false;
            $data = $render->pnFormGetValues();
            $preferences = array (	'publicstate' => $data['publicstate']);
            $result= pnModAPIFunc('ContactList','user','setPreferences',array('uid' => pnUserGetVar('uid'), 'preferences' => $preferences));
            if ($result) LogUtil::registerStatus(_CONTACTLISTPREFSUPDATED);
            else LogUtil::registerError(_CONTACTLISTPREFSUPDATEERROR);
            return pnRedirect(pnModURL('ContactList','user','preferences'));
        }
        return true;
    }
}

/**
 * Handler for function "create"
 */
class contactlist_user_createHandler {
    function initialize(& $render) {
        $uname = FormUtil::getPassedValue('uname');
        if (isset($uname) && (pnUserGetIDFromName($uname) > 1)) $render->assign('uname',$uname);
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
                return LogUtil::registerError(_CONTACTLISTNOTADDYOURSELF);
            }
            // valid user name?
            if (!($bid > 1)) {
                return LogUtil::registerError(_CONTACTLISTUNAMEINVALID);
            }
            // already my buddy?
            $buddies = pnModAPIFunc('ContactList','user','getall',array('bid' => $bid, 'uid' => $uid));
            if (count($buddies)>0) {
                return LogUtil::registerError(_CONTACTLISTDUPLICATEREQUEST);
            }
            if (pnModAPIFunc('ContactList','user','create',array(
						'uid'			=> $uid,
						'bid'			=> $bid,
						'prv_comment'	=> $data['prv_comment'],
						'pub_comment'	=> $data['pub_comment'],
						'request_text'	=> $data['request_text']
            ))) return pnRedirect(pnModURL('ContactList', 'user', 'main'));
            else return false;
        }
        return true;
    }
}
?>
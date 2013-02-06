<?php
/**
 * ContactList
 *
 * @copyright Florian Schießl, Carsten Volmer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package ContactList
 * @author Florian Schießl <info@ifs-net.de>.
 * @link http://www.ifs-net.de
 */

/**
 * This class provides the API for all non-administrative functionality
 */
class ContactList_Api_User extends Zikula_AbstractApi
{
    
//???????? ToDo Loader::requireOnce('modules/ContactList/includes/common.php');

    /**
     * get all buddys
     *
     * @param   int     $args['uid']        user's id to get his buddies
     * @param   int     $args['bid']            buddy's id to get users that have this person as buddy
     * @param   int     $args['state']      filter different states
     *                                          0 = unconfirmed
     *                                          1 = confirmed
     *                                          2 = rejected
     *                                          3 = suspended
     * @param   boolean $args['birthday']   default: false; include birthday in result
     * @param   string  $args['sort']           default: no sort order.
     *                                          options: birthday, nextbirthday, daystonextbirthday, state, random
     * @return	array
     */
    public function getall($args) {
        // filter for buddy or user id
        if (isset($args['uid']) && isset($args['bid'])) {
            $where='uid = '.(int)$args['uid'].' and bid = '.(int)$args['bid'];
        } else if (isset($args['uid'])) {
            $where = 'uid = '.(int)$args['uid'];
        } else if (isset($args['bid'])) {
            $where = 'bid = '.(int)$args['bid'];
        }
            
        // filter if state should be filtered
        if (!isset($where) && isset($args['state'])) {
            $where = 'state = '.(int)$args['state'];
        }
        if (isset($where) && isset($args['state'])) {
            $where.= ' and state = '.(int)$args['state'];
        }
        
        $sort = (isset($args['sort']) && !empty($args['sort'])) ? $args['sort'] : '';
        $birthday = isset($args['birthday']) ? $args['birthday'] : false;

        // return objects
        $res = DBUtil::selectObjectArray('contactlist_buddylist',$where);
        if (count($res) >0) {
            if ($birthday) {
                $myprofile = (ModUtil::getVar('ContactList','usemyprofilebirthday') && ModUtil::available('MyProfile'));
                $profile   = (ModUtil::getVar('ContactList','useprofilebirthday') && ModUtil::available('Profile'));

                // some preparations for the birthday days calculation
                $now = mktime(23, 59, 59, date("m",time()), date("d",time()), date("Y",time()));
                $year = date("Y",$now);

                if ($myprofile)	{					// if myprofile is activated and used as birthday date provider continue ;-)
                    $myprofilebirthday = ModUtil::getVar('ContactList','myprofilebirthday');
                    $fields = ModUtil::apiFunc('MyProfile','admin','getFields');
                    $birthday_restriction = false;
                    foreach ($fields as $field) {
                        if (($field['identifier'] == $myprofilebirthday) && ($field['public_status'] == 9)) {
                            $birthday_restriction = true;
                        }
                    }
                    foreach ($res as $item) {
                        $data = ModUtil::apiFunc('MyProfile','user','getUserVars',array('name' => $myprofilebirthday, 'uid' => $item['bid']));
                        $item['birthday'] = $data['value'];
                        // we will only set a value here if the user is allowed to see the other buddies birthday date (MyProfile 1.1 feature)
                        // if birthday should be hidden we'll delete its value again
                        if ($birthday_restriction) {
                            $settings = ModUtil::apiFunc('MyProfile','user','getSettings',array('uid' => $item['bid']));
                            $customsettings = $settings['customsettings'];
                            // customsettings:
                            // 0 = everybody, 1 = all members, 2 = confirmed buddies, 3 = only listed users
                            // we only have a problem if listed users is set as user settings
                            if ($customsettings == 3) {
                                // get users list
                                $list = ModUtil::apiFunc('MyProfile','user','getCustomFieldList',array('uid' => $item['bid']));
                                if (!in_array(UserUtil::getVar('uid'),$list)) {
                                    unset($item['birthday']);
                                }
                            }
                        }
                        $item['nextbirthday'] = $item['birthday'][5].$item['birthday'][6].$item['birthday'][8].$item['birthday'][9];
                        // calculate days to next birthday
                        if ($item['birthday'] != '') {
                            $birth_array = explode("-",$item['birthday']);
                            $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], $year);
                            if ($act_birthday < $now) $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], ($year+1));
                            $item['daystonextbirthday'] = round(($act_birthday-$now)/60/60/24);
                        } else {
                            $item['daystonextbirthday'] = -1;
                        }
                        $r[] = $item;
                    }
                    $result = $r;
                } else if ($profile) {				// otherwise we'll use the regular profile plugin
                    $profilebirthday = ModUtil::getVar('ContactList','profilebirthday');
                    foreach ($res as $item) {
                        $item['birthday'] = UserUtil::getVar($profilebirthday,$item['bid']);

                        $item['nextbirthday'] = $item['birthday'][5].$item['birthday'][6].$item['birthday'][8].$item['birthday'][9];
                        // calculate days to next birthday
                        if ($item['birthday'] != '') {
                            $birth_array = explode("-",$item['birthday']);
                            $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], $year);
                            if ($act_birthday < $now) $act_birthday = mktime(23, 59, 59, $birth_array[1], $birth_array[2], ($year+1));
                            $item['daystonextbirthday'] = round(($act_birthday-$now)/60/60/24);
                        } else {
                            $item['daystonextbirthday'] = -1;
                        }
                        $r[] = $item;
                    }
                    $result = $r;
                } else {
                    $result = $res;					// no MyProfile or Profile but a birthday request...
                }
            } else {
                $result = $res;
            }
        } else {
            return;
        }
        
        // add onlinestatus and username and sort and returl the result
        Loader::requireOnce('modules/ContactList/includes/common.php');
        return _cl_sortList(_cl_addOnlineStatusAndUsername($result,$args),$sort);
    }

    /**
     * This function returns the Watchlist of a user
     * 
     * @param   int     $uid        userId
     * @param   boolean $countonly  return result or just count results
     * @return boolean
     */
    
    public function getWatchList($args)
    {
        $uid = (int)$args['uid'];
        if (!($uid > 1)) {
            return false;
        } else {
            $where = 'tbl.uid = '.$uid;
        }
        $joinInfo[] = array (	'join_table'          =>  'users',	// table for the join
                                'join_field'          =>  'uname',	// field in the join table that should be in the result with
                                'object_field_name'   =>  'uname',	// ...this name for the new column
                                'compare_field_table' =>  'wuid',	// regular table column that should be equal to
                                'compare_field_join'  =>  'uid'		// ...the table in join_table
            );

        // get and return result
        if ($args['countonly']) {
            $res = DBUtil::selectExpandedObjectCount('contactlist_watchlist',$joinInfo,$where);
            return $res; 
        } else {
            $res = DBUtil::selectExpandedObjectArray('contactlist_watchlist',$joinInfo,$where,'uname');
            return $res;
        }
    }

    /**
    * create a new buddy request or add a new buddy
    *
    * @param    int     $args['uid']            user's id to get his buddies
    * @param    int     $args['bid']            buddy's id to get users that have this person as buddy
    * @param    string  $args['prv_comment']    private comment
    * @param    string  $args['pub_comment']    public comment
    * @param    string  $args['request_text']   request text
    * @param    int     $args['force']		optional (==1 create without asking and sending emails)
    * @return  boolean
    */
    public function create($args) {
        // some checks
        $uid            = $args['uid'];
        $bid            = $args['bid'];
        $prv_comment    = $args['prv_comment'];
        $pub_comment    = $args['pub_comment'];
        $request_text   = $args['request_text'];
        $force          = (int)$args['force'];
        if (!($uid > 1) || !($bid > 1)) return false;

        // is there an old rejected or suspended connection?
        $result = ModUtil::apiFunc('ContactList','user','getall',array('uid' => $bid, 'bid' => $uid));
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
        $noconfirm = (ModUtil::getVar('ContactList','noconfirm') || ($force == 1));
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
                LogUtil::registerStatus($this->__('Buddy was added successfully'));
                // send email
                if ($force != 1) {
                    // create view instance and assign Variables to output
                    $view = Zikula_View::getInstance($this->name, false);
                    $viewArgs=array(
                        'sitename'      => System::getVar('sitename'),
                        'hostname'      => System::serverGetVar('REMOTE_ADDR'),
                        'bid'           => $bid,
                        'buname'        => UserUtil::getVar('uname',$bid),
                        'uid'           => $uid,
                        'uname'         => UserUtil::getVar('uname',$uid),
                        'url'           => ModUtil::url($this->name, 'user', 'main', array(), null, null, true)
                    );
                    $view->assign($viewArgs);
                    $htmlBody = $view->fetch('user/email_added-noconfirm_html.htm');
                    $plainTextBody = $view->fetch('user/email_added-noconfirm_plain.htm');
                    // Send message via Mailer module
                    $emailMessageSent = ModUtil::apiFunc('Mailer', 'user', 'sendMessage', array(
                        'toaddress' => UserUtil::getVar('email'),
                        'subject'   => $this->__f('%s added you as buddy', UserUtil::getVar('uname', $uid)),
                        'body'      => $htmlBody,
                        'altbody'   => $plainTextBody
                    ));
                    // Check if mail was sent successfully
                    if (!$emailMessageSent) {
                        $this->registerError($this->__('Error! Unable to send e-mail message.'));
                    }
                }
                return true;
            } else {
                return false;
            }
        } else {
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
            if ($nocounterconnection) {
                $obj['state']=1;
            }
            if (DBUtil::insertObject($obj,'contactlist_buddylist')) {
                LogUtil::registerStatus($this->__('Buddy request sent. You will be noticed when your request was accepted.'));

                // send email
                if ($force != 1) {
                    // create view instance and assign Variables to output
                    $view = Zikula_View::getInstance($this->name, false);
                    $viewArgs=array(
                        'sitename'              => System::getVar('sitename'),
                        'hostname'              => System::serverGetVar('REMOTE_ADDR'),
                        'bid'                   => $bid,
                        'buname'                => UserUtil::getVar('uname',$bid),
                        'uid'                   => $uid,
                        'uname'                 => UserUtil::getVar('uname',$uid),
                        'url'                   => ModUtil::url($this->name, 'user', 'main', array(), null, null, true),
                        'request_text'          => $obj['request_text'],
                        'nocounterconnection'   => $nocounterconnection
                    );
                    $view->assign($viewArgs);
                    $htmlBody = $view->fetch('user/email_add-request_html.htm');
                    $plainTextBody = $view->fetch('user/email_add-request_plain.htm');
                    // Send message via Mailer module
                    $emailMessageSent = ModUtil::apiFunc('Mailer', 'user', 'sendMessage', array(
                        'toaddress' => UserUtil::getVar('email'),
                        'subject'   => $this->__f('%s wants to add you as buddy', UserUtil::getVar('uname', $uid)),
                        'body'      => $htmlBody,
                        'altbody'   => $plainTextBody
                    ));
                    // Check if mail was sent successfully
                    if (!$emailMessageSent) {
                        $this->registerError($this->__('Error! Unable to send e-mail message.'));
                    }
                return true;
                }
            } else {
                return false;
            }
        }
        return true;
    }

    /**
    * reject buddy request
    *
    * @param    int     $args['id'] id of buddy request
    * @param    string  $args['id'] optional, skip = send no email
    * @return  boolean
    */
    public function decline($args) {
        // get object
        $id = (int)$args['id'];
        $uid = UserUtil::getVar('uid');
        $bid = UserUtil::getVar('bid');
        $obj = DBUtil::selectObjectByID('contactlist_buddylist',$id);
        // only the user that should be a new buddy should be able to decline
        if ($obj['bid'] != $uid) return false;

        // change state to "2, rejected"
        $obj['state'] = 2;
        if (!DBUtil::updateObject($obj,'contactlist_buddylist')) {
            return LogUtil::registerError('error updating buddy object');
        }

        // send email
        if ($args['notification'] != 'skip') {

            // create view instance and assign Variables to output
            $view = Zikula_View::getInstance($this->name, false);
            $viewArgs=array(
                'sitename'              => System::getVar('sitename'),
                'hostname'              => System::serverGetVar('REMOTE_ADDR'),
                'bid'                   => $bid,
                'buname'                => UserUtil::getVar('uname',$bid),
                'uid'                   => $uid,
                'uname'                 => UserUtil::getVar('uname',$uid),
                'url'                   => ModUtil::url($this->name, 'user', 'main', array(), null, null, true),
                'request_text'          => $obj['request_text']
            );
            $view->assign($viewArgs);
            $htmlBody = $view->fetch('user/email_decline-request_html.htm');
            $plainTextBody = $view->fetch('user/email_add-request_plain.htm');
            // Send message via Mailer module
            $emailMessageSent = ModUtil::apiFunc('Mailer', 'user', 'sendMessage', array(
                'toaddress' => UserUtil::getVar('email'),
                'subject'   => $this->__f('%s has rejected your buddy request', UserUtil::getVar('uname', $uid)),
                'body'      => $htmlBody,
                'altbody'   => $plainTextBody
            ));
            // Check if mail was sent successfully
            if (!$emailMessageSent) {
                $this->registerError($this->__('Error! Unable to send e-mail message.'));
            }
        }
        return true;
    }

    /**
    * suspend watchlist entry
    *
    * @param   int  $args['id']     id of entry
    * @return  boolean
    */
    public function suspendWatchList($args)
    {
        $id = (int)$args['id'];
        if (!($id > 0)) {
            return false;
        } else {
            // get entry
            $obj = DBUtil::selectObjectByID('contactlist_watchlist',$id);
            if (!$obj || ($obj['uid'] != UserUtil::getVar('uid'))) {
                return false;
            } else {
                $deleteAction = DBUtil::deleteObject($obj,'contactlist_watchlist');
                return $deleteAction;
            }
        }
    }

    /**
    * delete a buddy
    *
    * @param    int     $args['id']     id of buddy request
    * @return  boolean
    */
    public function suspend($args) {
        // get object
        $id = (int)$args['id'];
        $uid = UserUtil::getVar('uid');
        $obj = DBUtil::selectObjectByID('contactlist_buddylist',$id);
        // Security check: only the user that is a buddy can suspend the connection
        if ($obj['uid'] != $uid) {
            return false;
        }

        // We will only remove the buddy or potential buddy from the buddylist
        // Then we will set actual user (if neccessarry) to "suspended" for
        // the counterpart
        
        // if the connection is only in one direction (already suspended) just delete the object
        if ($obj['state'] != 1) {
            return DBUtil::deleteObject($obj,'contactlist_buddylist');
        } else {
            // get the counterpart if the connection was confirmed (state == 1)
            $counter_obj = ModUtil::apiFunc('ContactList','user','getall',array('uid'=>$obj['bid'],'bid'=>$obj['uid']));
            $counter_obj = $counter_obj[0];
            if (!($counter_obj['id']>0)) {
                return false;
            }

            // change state to "3, suspended"
            $counter_obj['state'] = 3;
            if (!DBUtil::updateObject($counter_obj,'contactlist_buddylist')) {
                return LogUtil::registerError($this->__('error updating buddy object'));
            }

            // delete the old object
            DBUtil::deleteObject($obj,'contactlist_buddylist');
        }
        return true;
    }

    /**
    * confirm buddy request
    *
    * @param    int     $args['id']     id of buddy request
    * @return  boolean
    */
    public function confirm($args) {
        // get object
        $id = (int)$args['id'];
        $uid = UserUtil::getVar('uid');
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


        
        // create view instance and assign Variables to output
        $view = Zikula_View::getInstance($this->name, false);
        $viewArgs=array(
            'sitename'      => System::getVar('sitename'),
            'hostname'      => System::serverGetVar('REMOTE_ADDR'),
            'bid'           => $counterobj['bid'],
            'buname'        => UserUtil::getVar('uname',$counterobj['bid']),
            'uid'           => $counterobj['uid'],
            'uname'         => UserUtil::getVar('uname',$counterobj['uid']),
            'url'           => ModUtil::url($this->name, 'user', 'main', array(), null, null, true)
        );
        $view->assign($viewArgs);
        $htmlBody = $view->fetch('user/email_request-accepted_html.htm');
        $plainTextBody = $view->fetch('user/email_request-accepted_plain.htm');
        // Send message via Mailer module
        $emailMessageSent = ModUtil::apiFunc('Mailer', 'user', 'sendMessage', array(
            'toaddress' => UserUtil::getVar('email',$bid),
            'subject'   => $this->__f('The user \'%s\' has accepted your contact request', UserUtil::getVar('uname', $uid)),
            'body'      => $htmlBody,
            'altbody'   => $plainTextBody
        ));
        // Check if mail was sent successfully
        if (!$emailMessageSent) {
            $this->registerError($this->__('Error! Unable to send e-mail message.'));
        }
        return true;
    }

    /**
    * Get user preferences
    * This function gets the user preferences if the contact list should be
    * public to all, to friends or to nobody
    * If public contact lists are disabled by the administrator this settings
    * will also be "visible to nobody"
    *
    * @param    int     $args['uid'] 
    * @return  array    array: 'publicstate' => int { 1 = not visible; 2 = visible for friends; 3 = visible for registered users;}
    */
    public function getPreferences($args) {
        $uid = (int) $args['uid'];
        // get user and attributes
        $user = DBUtil::selectObjectByID('users', $uid, 'uid', null, null, null, false);
        if (!is_array($user)) {
            return false; // no user data?
        }
        if (!isset($user['__ATTRIBUTES__']) || (!isset($user['__ATTRIBUTES__']['contactlist_publicstate']))) {
            // userprefs for this user do not exist, create them with defaults
            $defaultvalue = (int)ModUtil::getVar('ContactList','defaultprivacystatus');
            if ($defaultvalue == 0) {
                $defaultvalue = 3;
            }
            $user['__ATTRIBUTES__']['contactlist_publicstate'] = $defaultvalue;
            // store attributes
            DBUtil::updateObject($user, 'users', '', 'uid');
        }
        return array('publicstate' => $user['__ATTRIBUTES__']['contactlist_publicstate']);
    }

    /**
    * Store user preferences
    *
    * @param   int      $args['uid']
    * @param   array    $args['preferences']
    * @return  boolean
    */
    public function setPreferences($args) {
        $uid = (int) $args['uid'];
        if (!($uid > 1)) {
            return false;
        }
        // check the user attributes for userprefs
        $user = DBUtil::selectObjectByID('users', $uid, 'uid', null, null, null, false);
        if (!is_array($user)) {
            return false; // no user data?
        } else {
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
    * @param   int  $args['uid1']
    * @param   int  $args['uid2']
    * @return  boolean (false) or integer (partnerhip id) if true
    */
    public function isBuddy($args) {
        $buddies = ModUtil::apiFunc('ContactList','user','getall',array(
                        'uid'   => (int)$args['uid1'],
                        'bid'   => (int)$args['uid2'],
                        'state' => '1'));
        if (count($buddies) > 0) {
            return (int)$buddies[0]['id'];
        } else {
            return false;
        }
    }

    /**
    * ignore another user
    *
    * @param   int  $args['uid']
    * @param   int  $args['iuid'}
    * @return  boolean
    */
    public function ignoreUser($args) {
        $uid    = (int)$args['uid'];
        $iuid   = (int)$args['iuid'];
        if ($uid == $iuid) {
            return false;
        }
        if (!($uid > 1) || !($iuid > 1)) {
            return false;
        }
        $obj = array (
                    'uid'   => $uid,
                    'iuid'  => $iuid
            );
        if (ModUtil::apiFunc('ContactList','user','isIgnored',$args)) {
            return false;
        }
        if (DBUtil::insertObject($obj,'contactlist_ignorelist')) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * get all user that are ignored by another user all users that ignore another user
    *
    * @param   int      $args['uid']    user id
    * @param   int      $args['iuid']   ignored user id
    * @param   string   $args['sort']   identifier value to sort the list for (iuname,uname)
    * @return  array
    */
    public function getallignorelist($args) {

        // return false if ignore list functionallity is disabled by the admin
        if (!ModUtil::getVar('ContactList','useignore')) {
            return false;
        }
        // otherwise do some checks
        $uid    = (int)$args['uid'];
        $iuid   = (int)$args['iuid'];
        if (($uid > 1) && ($iuid > 1)) {
            $where = 'iuid = '.$iuid.' and uid = '.$uid;
        } else if ($uid > 1) {
            $where = 'uid = '.$uid;
        } else if ($iuid > 1) {
            $where = 'iuid = '.$iuid;
        }
        // get database result
        $res = DBUtil::selectObjectArray('contactlist_ignorelist',$where);
        foreach ($res as $item) {
            $item['uname'] 	= UserUtil::getVar('uname',$item['uid']);
            $item['iuname']	= UserUtil::getVar('uname',$item['iuid']);
            $ignorelist[] = $item;
        }

        if (isset($args['sort']) && isset($ignorelist)) {		// Apply an "order by"?
            foreach ($ignorelist as $key => $row) {
                if ($args['sort'] == 'iuname') {
                    $sort[$key]  = $row['iuname'];
                } else if ($args['sort'] == 'uname') {
                    $sort[$key] = $row['uname'];
                }
            }
            array_multisort($sort, SORT_ASC, $ignorelist);
        }
        return $ignorelist;
    }

    /**
    * check if a user is ignored by another user
    * To be used from other module developers
    *
    * @param   int  $args['uid']    user id
    * @param   int  $args['iuid']   ignored user id
    * @return  boolean
    */
    public function isIgnored($args) {
        // return false if ignore list functionallity is disabled by the admin
        if (!ModUtil::getVar('ContactList','useignore')) {
            return false;
        }
        // otherwise do some checks
        $uid    = (int)$args['uid'];
        $iuid   = (int)$args['iuid'];
        if ($uid == $iuid) {
            return false;
        }
        if (!($uid > 1) || !($iuid > 1)) {
            return false;
        }
        $where = 'uid = '.$uid.' and iuid = '.$iuid;
        $res = DBUtil::selectObjectArray('contactlist_ignorelist',$where);
        if (count($res)>0) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * delete a user from the users ignore list
    *
    * @param   int  $args['iuid']
    * @return  boolean
    */
    public function deleteIgnoredUser($args) {
        $iuid = (int) $args['iuid'];
        $uid = UserUtil::getVar('uid');
        if (!isset($iuid) || (!($iuid > 1))) {
            return false;
        }
        // get ignore link
        $objects = ModUtil::apiFunc('ContactList','user','getallignorelist',array('uid' => $uid, 'iuid' => $iuid));
        return DBUtil::deleteObject($objects[0],'contactlist_ignorelist');
    }

    /**
    * This function returns an array including another array with each
    * buddy connection a user has.
    * To be used from other module developers!
    *
    * @param   int  $args['uid'] 
    * @return  array               uid => user id  uname => uname
    */
    public function getBuddyList($args) {
        $buddies = ModUtil::apiFunc('ContactList','user','getall',array(
            'uid'       => (int)$args['uid'],
            'state'     => '1'));
        if (count($buddies)==0) {
            return false;
        }
        $res = array();
        foreach ($buddies as $buddy) {
            $res[] = array('uid' => $buddy['bid'], 'uname' => UserUtil::getVar('uname',$buddy['bid']));
        }
        return $res;
    }

    /**
    * get confirmed contacts of first and second grade
    *
    * @param   int  $args['uid']
    * @return  output
    */
    public function getContactsInfo($args) {
        // get tables and column
        ModUtil::dbInfoLoad('objectdata');
        $tables 	= DBUtil::getTables();
        $cltable 	= DBUtil::getLimitedTableName('contactlist_buddylist');
        $oatable 	= DBUtil::getLimitedTableName('objectdata_attributes');
        $oacolumn = $tables['objectdata_attributes_column'];
        $uid = (int) $args['uid'];
        
        // direct contacts
        $sql = "SELECT COUNT(*) AS C FROM ".$cltable." WHERE uid = ".$uid." AND state = 1";
        $res = DBUtil::executeSql($sql);
        $first = (int)$res->fields[0];
        // second grade
        if ($first > 0) {
            $sql = '	SELECT COUNT(DISTINCT select_2.bid)
                            FROM 
                                '.$cltable.' as select_1,
                                '.$cltable.' as select_2,
                                '.$oatable.' as attributes
                            WHERE
                                attributes.'.$oacolumn['value'].' > 1 AND
                                attributes.'.$oacolumn['attribute_name'].' = \'contactlist_publicstate\' AND
                                select_1.state = 1 AND
                                select_2.state = 1 AND
                                select_1.bid = select_2.uid AND
                                select_1.uid = '.$uid.'
                                
                                ';
            $res = DBUtil::executeSQL($sql);
            $second = (int)$res->fields[0];
        } else {
            $second = 0;
        }
        return array(
            '1st'	=> $first,
            '2nd'	=> $second
            );
    }
      
    /**
    * get nearest foaf-link for two users
    *
    * @param   int  $args['uid1']
    * @param   int  $args['uid2']
    * @return  output
    */
    public function getFOAFLink($args) {
        // get tables and column
        ModUtil::dbInfoLoad('objectdata');
        $tables 	= DBUtil::getTables();
        $cltable 	= DBUtil::getLimitedTableName('contactlist_buddylist');
        $oatable 	= DBUtil::getLimitedTableName('objectdata_attributes');
        $oacolumn = $tables['objectdata_attributes_column'];

        $res = false;
        $uid1 = (int) $args['uid1'];
        $uid2 = (int) $args['uid2'];

        // case 1: user views his own profile
        if (($uid1 == $uid2) || (!($uid1 > 1)) || (!($uid2 > 1))) {
            return false;
        }

        // case 2: user views the profile of a friend
        // uid1, x1, uid2
        if (ContactList_userapi_isBuddy(array('uid1' => $uid1, 'uid2' => $uid2))) {
            $res[] = $this->cl_addToArrayLink($uid1);
            $res[] = $this->cl_addToArrayLink($uid2);
            // there is no privacy check needed because a buddy should be 
            // allowed to see that he is a buddy :-)
        }

        // case3: user views the profile of a friend's friend
        // uid1, x1, x2, uid2
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
                            select_1.state = 1 AND
                            select_2.state = 1 AND
                            select_1.bid = select_2.uid AND
                            select_1.uid = '.$uid1.' AND
                            select_2.bid = '.$uid2.'
                            ';
            $results = DBUtil::executeSQL($sql);
            foreach ($results as $r) {
                $this_results[] = $r;
            }
            if (count($this_results) > 0) {	// if there is more than one result found we'll shuffle
                $nr = mt_rand(1,count($this_results))-1;
                $one_result = $this_results[$nr];
            }
            if (is_array($one_result) && (count($one_result) > 0)) {	// we found a result!
                $res = array();
                foreach ($one_result as $uid) {
                    $res[] = $this->cl_addToArrayLink($uid);
                }
            }
        }
        // case4: 
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
                            select_1.state = 1 AND
                            select_2.state = 1 AND
                            select_3.state = 1 AND
                            select_1.bid = select_2.uid AND
                            select_2.bid = select_3.uid AND
                            select_1.uid = '.$uid1.' AND
                            select_3.bid = '.$uid2.'
                            ';
            $results = DBUtil::executeSQL($sql);
            foreach ($results as $r) {
                $this_results[] = $r;
            }
            if (count($this_results) > 0) {	// if there is more than one result found we'll shuffle
                $nr = mt_rand(1,count($this_results))-1;
                $one_result = $this_results[$nr];
            }
            if (is_array($one_result) && (count($one_result) > 0)) {	// we found a result!
                $res = array();
                foreach ($one_result as $uid) {
                    $res[] = $this->cl_addToArrayLink($uid);
                }
            }
        }
        // case5: 
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
                            select_1.state = 1 AND
                            select_2.state = 1 AND
                            select_3.state = 1 AND
                            select_4.state = 1 AND
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
                foreach ($one_result as $uid) {
                    $res[] = $this->cl_addToArrayLink($uid);
                }
            }
        }
        // case6: 
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
                            select_1.state = 1 AND
                            select_2.state = 1 AND
                            select_3.state = 1 AND
                            select_4.state = 1 AND
                            select_5.state = 1 AND
                            select_1.bid = select_2.uid AND
                            select_2.bid = select_3.uid AND
                            select_3.bid = select_4.uid AND
                            select_4.bid = select_5.uid AND
                            select_1.uid = '.$uid1.' AND
                            select_5.bid = '.$uid2.'
                            ';
            $results = DBUtil::executeSQL($sql);
            foreach ($results as $r) {
                $this_results[] = $r;
            }
            if (count($this_results) > 0) {	// if there is more than one result found we'll shuffle
                $nr = mt_rand(1,count($this_results))-1;
                $one_result = $this_results[$nr];
            }
            if (is_array($one_result) && (count($one_result) > 0)) {	// we found a result!
                $res = array();
                foreach ($one_result as $uid) {
                    $res[] = $this->cl_addToArrayLink($uid);
                }
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

    /**
    * returns all user ids that have a buddy at least 
    * who celebrates birthday today
    * 
    * @return array
    */
    public function getBirthdayBuddies()
    {
        $myprofilebirthday = ModUtil::getVar('ContactList','myprofilebirthday');
        if ($myprofilebirthday != '') {
            $tables = DBUtil::getTables();
            $cl_table = $tables['contactlist_buddylist'];
            $cl_column = $tables['contactlist_buddylist_column'];
            $mp_table = $tables['myprofile'];
            $mp_column = $tables['myprofile_column'];
            $sql = "
                SELECT DISTINCT ".$cl_table.".".$cl_column['bid']."
                FROM ".$cl_table.", ".$mp_table."
                WHERE ".$cl_table.".".$cl_column['state']." = 1 
                and ".$mp_table.".".$mp_column['id']." = ".$cl_table.".".$cl_column['uid']." 
                and ".$mp_table.".".$mp_column[$myprofilebirthday]." like '".date("%-m-d",time())."'";
            $result = DBUtil::executeSql($sql);
            if (!$result) {
                return false;
            } else {
                foreach ($result as $item) {
                    $email = UserUtil::getVar('email',$item[0]);
                    if ($email != '') {
                        $res[$item[0]] = $item[0];
                    }
                }
                return $res;
            }
        } else  if ($profilebirthday != '') {
            // ToDo later...
          
        } else {
            return false;
        }
    }

    /**
     * This function chanegs an array of user IDs into an array or arrays
     * with user id and user name
     * 
     * @param type $uid
     * @return type
     */
    private function cl_addToArrayLink($uid) {
        return array (
            'uid'	=> $uid,
            'uname'	=> UserUtil::getVar('uname',$uid)
          );
    }
}
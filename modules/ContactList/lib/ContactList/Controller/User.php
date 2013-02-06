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

class ContactList_Controller_User extends Zikula_AbstractController
{
    
    /**
     *
     * @return       output
     */
    public function main()
    {
        Loader::requireOnce('modules/ContactList/includes/common.php');

        // Check this before Security check - maybe after login user has enough rights
        if (!UserUtil::isLoggedIn()) {
          return System::redirect(ModUtil::url('ContactList', 'user', 'loginscreen', array('page' => 'main')));
        }
        // Security Check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_COMMENT));
        
        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        // Execute form using supplied template and page event handler
        return $view->execute('user/main.htm', new ContactList_Form_Handler_User_Main());
    }

    /**
     * the main user function
     *
     * @return       output
     */
    public function preferences()
    {
        // This is a user only module - redirect everyone else
        // Check this before Security check - maybe after login user has enough rights
        if (!UserUtil::isLoggedIn()) {
          return System::redirect(ModUtil::url('ContactList', 'user', 'loginscreen', array('page' => 'preferences')));
        }

        // Security check
        if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) {
          return LogUtil::registerPermissionError(System::getVar('entrypoint', 'index.php'));
        }

        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        // Execute form using supplied template and page event handler
        return $view->execute('user/preferences.htm', new ContactList_Form_Handler_User_Preferences());
    }

    /**
     * 
     *
     * @param	$args['id']		int		buddy id
     * @return	output
     */
    public function ignore()
    {
        // This is a user only module - redirect everyone else
        // Check this before Security check - maybe after login user has enough rights
        if (!UserUtil::isLoggedIn()) {
            $returnpage = urlencode(ModUtil::url('ContactList','user','ignore'));
            return System::redirect(ModUtil::url('Users', 'user', 'login', array('returnpage' => $returnpage)));
        }
        // Security check
        if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) {
          return LogUtil::registerPermissionError(System::getVar('entrypoint', 'index.php'));
        }

        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        // Execute form using supplied template and page event handler
        return $view->execute('user/ignore.htm', new ContactList_Form_Handler_User_Ignore());
    }

    /**
     * create buddy request
     *
     * @return       output
     */
    public function create()
    {
        // This is a user only module - redirect everyone else
        // Check this before Security check - maybe after login user has enough rights
        if (!UserUtil::isLoggedIn()) {
            $returnpage = urlencode(ModUtil::url('ContactList','user','create'));
            return System::redirect(ModUtil::url('Users', 'user', 'login', array('returnpage' => $returnpage)));
        }
        // Security check
        if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) {
          return LogUtil::registerPermissionError(System::getVar('entrypoint', 'index.php'));
        }

        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        // Execute form using supplied template and page event handler
        return $view->execute('user/create.htm', new ContactList_Form_Handler_User_Create());
    }
    
    /**
     * manage a users watchlist
     *
     * @return       output
     */
    public function watchlist()
    {
        // This is a user only module - redirect everyone else
        // Check this before Security check - maybe after login user has enough rights
        if (!UserUtil::isLoggedIn()) {
          return System::redirect(ModUtil::url('ContactList', 'user', 'loginscreen', array('page' => 'main')));
        }
        // Security check
        if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_COMMENT)) {
          return LogUtil::registerPermissionError(System::getVar('entrypoint', 'index.php'));
        }

        // check for action
        $action = FormUtil::getPassedValue('action', '');
        if (!empty($action) && !(SecurityUtil::confirmAuthKey())) return Logutil::registerAuthIDError();
        if ($action == "suspend") {
            if (ModUtil::apiFunc('ContactList','user','suspendWatchList',array('id'=>(int)FormUtil::getPassedValue('id')))) {
                LogUtil::registerStatus($this->__('User was removed from your watchlist.'));
            } else {
                LogUtil::registerError($this->__('An error occurred while trying to suspend the buddy'));
            }
        }

        // redirect after any action to avoid auth-id problems
        if (!empty($action)) {
            return System::redirect(ModUtil::url('ContactList','user','watchlist'));
        }
        
        // assign data
        $uid = UserUtil::getVar('uid');
        $data['contactinfo'] = ModUtil::apiFunc('ContactList','user','getContactsInfo',array('uid' => $uid));
        $data['dateformat'] = ModUtil::getVar('ContactList','dateformat');
        // pagination
        $cl_limit       = ModUtil::getVar('ContactList','itemsperpage');
        $cl_startnum    = (int)FormUtil::getPassedValue('cl_startnum',1);
        $data['cl_limit'] = $cl_limit;
        $data['cl_startnum'] = $cl_startnum;
        
        // Get watchlist
        $buddies_count = ModUtil::apiFunc('ContactList','user','getWatchList',
            array(  'bid' => $uid,
                    'countonly' => true 
                ));
        $buddies = ModUtil::apiFunc('ContactList','user','getWatchList',
            array(  'uid' => UserUtil::getVar('uid') 
                ));
        
        $data['buddies'] = $buddies;
        $data['contacts_all'] = $buddies_count;
        $data['authid'] = SecurityUtil::generateAuthKey();
        
        // Assign Data and return output
        return $this->view->assign($data)
                          ->fetch('user/watchlist.htm');
    }
    
    /**
     * edit form to edit own comments (private and public) to a buddy
     *
     * @return       output
     */
    public function edit()
    {

        // Check this before Security check - maybe after login user has enough rights
        if (!UserUtil::isLoggedIn()) {
            $returnpage = urlencode(ModUtil::url('ContactList','user','edit'));
            return System::redirect(ModUtil::url('Users', 'user', 'login', array('returnpage' => $returnpage)));
        }
        // Security Check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_COMMENT));
        
        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);


        // Execute form using supplied template and page event handler
        return $view->execute('user/edit.htm', new ContactList_Form_Handler_User_Edit());
    }

    /**
     * display a user's buddy list
     *
     * @return       output
     */
    public function display()
    {
        // check if buddy list is public
        if (ModUtil::getVar('ContactList','nopublicbuddylist')) {
            return LogUtil::registerPermissionError();
        }
        // Check this before Security check - maybe after login user has enough rights
        if (!UserUtil::isLoggedIn()) {
            $returnpage = urlencode(ModUtil::url('ContactList','user','display'));
            return System::redirect(ModUtil::url('Users', 'user', 'login', array('returnpage' => $returnpage)));
        }
        // Security Check
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_COMMENT));

        // Get the user Id of the viewing user
        $viewer_uid = (int) FormUtil::getPassedValue('uid');
        if (!$viewer_uid) {
            return LogUtil::registerError(__('Error! Could not load items.', $dom));
        }
        
        $display = false;
        $current_uid = (int) UserUtil::getVar('uid');

        if ($current_uid != $viewer_uid) {
            // check for privacy settings
            $prefs = ModUtil::apiFunc('ContactList','user','getPreferences',array('uid' => $viewer_uid));
            switch ($prefs['publicstate']) {
                case 1:
                    $display=false;
                    break;
                case 2:
                    $display = ModUtil::apiFunc('ContactList','user','isBuddy',array('uid1' => $viewer_uid, 'uid2' => $current_uid));
                    break;
                case 3:	
                    $display = UserUtil::isLoggedIn();
                    break;
                default:
                    return LogUtil::registerPermissionError();
                    break;
            }
        } else {
            $display = true;
        }
        
        $buddies = ModUtil::apiFunc('ContactList','user','getall',
            array( 'uid'        => $viewer_uid,
                   'state'      => 1 )
                );

        $cl_limit = ModUtil::getVar('ContactList','itemsperpage');
        $cl_startnum = (int)FormUtil::getPassedValue('cl_startnum',1);

        // now just give back the buddy list we need for this page
        // I know this is not really very performant - but there is no other way to do this because
        // of the data and the sort criterias, that are included in the result list
        $numBuddies = count($buddies) - $cl_startnum;
        if ($cl_limit > $numBuddies) {
            $cl_limit = $numBuddies+1;
        }
        $c_stop = $cl_startnum + $cl_limit;
        for ($c = $cl_startnum-1; $c < $c_stop-1; $c++) {
            $assign_buddies[] = $buddies[$c];
        }

        if (!$display) {
            return $this->view->fetch('user/nodisplay.htm');
        } else {
            $this->view->assign('contacts_all', count($buddies));
            $this->view->assign('cl_limit', $cl_limit);
            $this->view->assign('cl_startnum', $cl_startnum);    
            $this->view->assign('viewer_uid', $viewer_uid);
            $this->view->assign('viewer_uname', UserUtil::getVar('uname',$viewer_uid));    
            $this->view->assign('current_uid', $current_uid);    
            $this->view->assign('buddies', $assign_buddies);
            $this->view->assign('nopubliccomment', (int)ModUtil::getVar('ContactList','nopubliccomment'));
        }
        return $this->view->fetch('user/display.htm');
    }
}
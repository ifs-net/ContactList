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
class ContactList_Form_Handler_User_Main extends Zikula_Form_AbstractHandler
{
    public function initialize(Zikula_Form_View $view)
    {
        // We need to init some variables
        $items_sortlist = array (
            array(
                'text'  => $this->__('username'),
                'value' => 'uname'
                ),
            array(
                'text'  => $this->__('state'),
                'value' => 'state'
                ),
            array(
                'text'  => $this->__('next birthday'),
                'value' => 'daystonextbirthday'
                ),
            array(
                'text'  => $this->__('birthday'),
                'value' => 'nextbirthday'
                ),
            );
        // Assign variables to the template
        $this->view->assign('items_sortlist',$items_sortlist);
        
        // First we will check if there is an action to do
        // If there is an action to to, we will do this action first
        // Afterwards we will redirect to the main page again and reload
        // this page without any action.
        $action = FormUtil::getPassedValue('action', '');
        if (!empty($action) && !(SecurityUtil::confirmAuthKey())) {
            return Logutil::registerAuthIDError();
        }
        if ($action == "decline") {
            if (ModUtil::apiFunc('ContactList','user','decline',array('id'=>(int)FormUtil::getPassedValue('id'), 'notification' => FormUtil::getPassedValue('notification')))) {
                LogUtil::registerStatus($this->__('Request rejected'));
            } else {
                LogUtil::registerError($this->__('Error rejecting request'));
            }
        }
        else if ($action == "confirm") {
            if (ModUtil::apiFunc('ContactList','user','confirm',array('id'=>(int)FormUtil::getPassedValue('id')))) {
                LogUtil::registerStatus($this->__('Buddy accepted'));
            } else {
                LogUtil::registerError($this->__('Error accepting request'));
            }
        }
        else if ($action == "suspend") {
            if (ModUtil::apiFunc('ContactList','user','suspend',array('id'=>(int)FormUtil::getPassedValue('id')))) {
                LogUtil::registerStatus($this->__('The connection to this buddy was suspended successfully'));
            } else {
                LogUtil::registerError($this->__('An error occurred while trying to suspend the buddy'));
            }
        }
        // If there was an action to be done it should be done until here
        // Now we redirect after to avoid auth-id problems
        if (!empty($action)) {
            return System::redirect(ModUtil::url('ContactList','user','main',array('state'=>FormUtil::getPassedValue('state'))));
        }

        // Now we proceed - there is no action to be done and we can continue creating our output.
        // check if the result should be sorted
        $birthday	= FormUtil::getPassedValue('birthday',	true);
        $sort		= FormUtil::getPassedValue('sort',		'uname');
        // some validations
        if (($sort == 'birthday') || ($sort == 'nextbirthday') || ($sort == 'daystonextbirthday')) {
            $birthday = true;	// for this sort criteria we need the user's birthday
        } else if (($sort != 'state') && ($sort != 'uname')) {
            $sort = 'uname';	// this is just a check if the agument $sort is valid
        }

        // assign data
        $uid = pnUserGetVar('uid');
        $this->view->assign('sort',FormUtil::getPassedValue('sort'));
        $this->view->assign('dateformat',ModUtil::getVar('ContactList','dateformat'));
        $this->view->assign('contactinfo',ModUtil::apiFunc('ContactList','user','getContactsInfo',array('uid' => $uid)));
        // unconfirmed buddies are always assigned
        $this->view->assign('buddies_unconfirmed',ModUtil::apiFunc('ContactList','user','getall',
            array(
                'bid' => $uid,
                'sort' => $sort,
                'birthday' => $birthday,
                'state' => 0 )
                ));

        $state = FormUtil::getPassedValue('state');
        $buddies = array();

        if ($state != "") {
            $buddies = ModUtil::apiFunc('ContactList','user','getall',
                array( 
                    'uid'       => $uid,
                    'state'     => $state,
                    'birthday'  => $birthday,
                    'sort'      => $sort
            ));
        }
        else {  // assign and fetch all data all we have otherwise
            $buddies_pending = ModUtil::apiFunc('ContactList','user','getall',
            array( 'uid'        => $uid,
                   'birthday'   => $birthday,
                   'sort'       => $sort,
                   'state'      => 0 ));
            $buddies_confirmed = ModUtil::apiFunc('ContactList','user','getall',
            array( 'uid'        => $uid,
                   'state'      => 1,
                   'birthday'   => $birthday,
                   'sort'       => $sort ));
            $buddies_rejected = ModUtil::apiFunc('ContactList','user','getall',
            array( 'uid'        => $uid,
                   'birthday'   => $birthday,
                   'sort'       => $sort,
                   'state'      => 2 ));
            $buddies_suspended = ModUtil::apiFunc('ContactList','user','getall',
            array( 'uid'        => $uid,
                   'birthday'   => $birthday,
                   'sort'       => $sort,
                   'state'      => 3 ));
            if (is_array($buddies_pending)) {
                foreach ($buddies_pending   as $buddy) {
                    $buddies[]=$buddy;
                }
            }
            if (is_array($buddies_confirmed)) {
                foreach ($buddies_confirmed as $buddy) {
                    $buddies[]=$buddy;
                }
            }
            if (is_array($buddies_suspended)) {
                foreach ($buddies_suspended as $buddy) {
                    $buddies[]=$buddy;
                }
            }
            if (is_array($buddies_rejected)) {
                foreach ($buddies_rejected  as $buddy) {
                    $buddies[]=$buddy;
                }
            }
            // let's sort the buddies array
            $buddies = _cl_sortList($buddies,$sort);
        }
        $this->view->assign('contacts_all',count($buddies));
        $this->view->assign('state',$state);
        $this->view->assign('contacts',count($buddies_confirmed));
        $this->view->assign('nopubliccomment',(int)ModUtil::getVar('ContactList','nopubliccomment'));
        $this->view->assign('nopublicbuddylist',(int)ModUtil::getVar('ContactList','nopublicbuddylist'));
        $this->view->assign('authid',SecurityUtil::generateAuthKey('ContactList'));
        
        // pagination
        $cl_limit       = ModUtil::getVar('ContactList','itemsperpage');
        $cl_startnum    = (int)FormUtil::getPassedValue('cl_startnum',1);
        $this->view->assign('cl_limit',		$cl_limit);
        $this->view->assign('cl_startnum',	$cl_startnum);

        // now just give back the buddy list we need for this page
        // I know this is not really very performant - but there is no other way to do this because
        // of the data and the sort criterias, that are included in the result list
        $numBuddies = count($buddies) - $cl_startnum;
        if ($cl_limit > $numBuddies) $cl_limit = $numBuddies+1;
        $c_stop = $cl_startnum + $cl_limit;
        for ($c = $cl_startnum-1; $c < $c_stop-1; $c++) {
            $assign_buddies[] = $buddies[$c];
        }
        $this->view->assign('buddies',$assign_buddies);
        // That's all for now...
        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        $formData = $this->view->getValues();
        $this->view->assign($formData);
        return ModUtil::url('ContactList','user','main',array('sort' => $formData['sort']));
    }    
}
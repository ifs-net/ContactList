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
class ContactList_Form_Handler_User_Ignore extends Zikula_Form_AbstractHandler
{
    public function initialize(Zikula_Form_View $view)
    {
        // We have to check for action before the handler is called
        // If there is an action we do this action and then we will redirect
        // to this page again - without action.
        $action = FormUtil::getPassedValue('action');
        if (isset($action) && (strtolower($action) == 'delete')) {
            if (!SecurityUtil::confirmAuthKey()) {
                return LogUtil::registerAuthIDError();
            }
            $iuid = (int)FormUtil::getPassedValue('iuid');
            if (ModUtil::apiFunc('ContactList','user','deleteIgnoredUser',array('iuid' => $iuid))) {
                LogUtil::registerStatus($this->__('User was removed from ignore list'));
            } else {
                LogUtil::registerError(__('Updating user information failed'));
            }
            return System::redirect(ModUtil::url('ContactList','user','ignore'));
        }

        // We need the username
        $uname = FormUtil::getPassedValue('uname');
        if (isset($uname) && (pnUserGetIDFromName($uname) > 1)) {
            $this->view->assign('uname',$uname);
        }
        // assign some mroe data
        $this->view->assign('nopubliccomment',(int)ModUtil::getVar('ContactList','nopubliccomment'));
        $this->view->assign('ignorelist',ModUtil::apiFunc('ContactList','user','getallignorelist',array('uid' => pnUserGetVar('uid'), 'sort' => 'iuname')));
        $this->view->assign('authid',SecurityUtil::generateAuthKey());

        // That's all for now...
        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        $formData = $this->view->getValues();
        $this->view->assign($formData);

        if ($args['commandName'] == 'update') {
            // Validate input
            if (!$this->view->isValid()) {
                return false;
            }
            // get data
            $iuid = UserUtil::getIdFromName($formData['uname']);
            $uid = UserUtil::getVar('uid');
            // validation check
            if ($uid == $iuid) {
                return LogUtil::registerError($this->__('I know, some people do not like themself - but you really cannot ignore you own username :-)'));
            }
            // does the user exist?
            if (!($iuid > 1)) {
                return LogUtil::registerError($this->__('User could not be found'));
            }
            // is the user forbidden to be added?
            $disabledgroups = ModUtil::getVar('ContactList','disabledgroups');
            $usergroups = ModUtil::apiFunc('Groups','user','getusergroups',array('uid' => $iuid));
            $disallowed = 0;
            foreach ($usergroups as $groups) {
                    $gid = $groups['gid'];
                    if (in_array($gid,$disabledgroups)) {
                            $disallowed = 1;
                    }
            }
            if ($disallowed) {
                    return LogUtil::registerError($this->__('The user is member of a group that was marked by the site admin that the members of this group canot be ignored by anyone'));
            }
            // is the user a buddy?
            if (ModUtil::apiFunc('ContactList','user','isBuddy',array('uid1'=>$iuid, 'uid2'=>$uid))) {
				return LogUtil::registerError($this->__('You are funny - a person cannot be added to the ignore list if this person is a confirmed buddy!'));
			}
            // ignore the user from now on...
            if (ModUtil::apiFunc('ContactList','user','ignoreUser',array('uid' => $uid, 'iuid' => $iuid))) {
			  	LogUtil::registerStatus($this->__('User was set to your ignore list successfully'));
			}
            else {
                return false;            
            }
            return System::redirect(ModUtil::url('ContactList','user','ignore'));
        }
        return true;
    }    
}

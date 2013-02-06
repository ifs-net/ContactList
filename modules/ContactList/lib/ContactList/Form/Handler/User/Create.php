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
class ContactList_Form_Handler_User_Create extends Zikula_Form_AbstractHandler
{
    public function initialize(Zikula_Form_View $view)
    {
        // Get parameter - user that should be added as buddy
        $uname = FormUtil::getPassedValue('uname');

        if (isset($uname) && (UserUtil::getIdFromName($uname) > 1)) {
            $formData['uname'] = $uname;
        } else {
            $uid = FormUtil::getPassedValue('uid');
            if (isset($uid) && !isset($uname)) {
                $uname = UserUtil::getVar('uname',$uid);
                if ($uname) {
                    $formData['uname'] = $uname;
                }
            }
	}
        // Assign some variables
        $formData['noconfirm'] = ModUtil::getVar('ContactList','noconfirm');
        $formData['nopubliccomment'] = ModUtil::getVar('ContactList','nopubliccomment');
        $this->view->assign($formData);

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

            $uname = $formData['uname'];
            $bid = UserUtil::getIdFromName($uname);
            $uid = UserUtil::getVar('uid');
            // own user name
            if ($bid == $uid) {
                return LogUtil::registerError($this->__('You can not add yourself as buddy. Go out and find some friends :-)'));
            }
            // valid user name?
            if (!($bid > 1)) {
                return LogUtil::registerError($this->__('You have to submit a valid username'));
            }
            // for watchlist handling
            
            if ($formData['watchlist'] == 1) {
                // check if user is already a buddy
                if (ModUtil::apiFunc('ContactList','user','isBuddy',array('uid1' => $uid, 'uid2' => $bid))) {
                    return LogUtil::registerError($this->__('The user is already in your buddy list and cannot be stored for the watchlist, too'));
                }
                $alreadyWatchListed = DBUtil::selectObjectCount('contactlist_watchlist','wuid = '.$bid.' and uid = '.$uid);
                if ($alreadyWatchListed) {
                    return LogUtil::registerError($this->__('User was already added to your watchlist and cannot be added twice.'));
                }
                $obj = array(
                        'uid'   => $uid,
                        'wuid'  => $bid,
                        'prv_comment'   => $formData['prv_comment'],
                        'date'  => date("Y-m-d H:i:s",time())
                    );
                $insertAction = DBUtil::insertObject($obj,'contactlist_watchlist');
                if ($insertAction) {
                    LogUtil::registerStatus($this->__('The user was added to your watchlist.'));
                    return System::redirect(ModUtil::url('ContactList','user','watchlist'));
                    // redirect
                } else {
                    return LogUtil::registerError($this->__('Adding user to watchlist failed.'));
                }
            }
            
            // for real contact handling
            // is the potential buddy ignoring me?
            if (ModUtil::apiFunc('ContactList','user','isIgnored',array('uid' => $bid, 'iuid' => $uid))) {
                return LogUtil::registerError($this->__("Seems as if there is a conflict between you and your potential buddy because you are on the other user's ignore list. So, your request could not be sent!"));
                }
            // or is the new buddy ignored by myself?
            if (ModUtil::apiFunc('ContactList','user','isIgnored',array('iuid' => $bid, 'uid' => $uid))) {
                return LogUtil::registerError($this->__("You are ignoring the potential new buddy... That's not a good start of a new friendship..."));
            }
            // already my buddy?
            $buddies = ModUtil::apiFunc('ContactList','user','getall',array('bid' => $bid, 'uid' => $uid));
            if (count($buddies)>0) {
                return LogUtil::registerError($this->__('You can not make multiple requests. The user is your buddy or has already recieved a buddy request from you.'));
            }
            if (ModUtil::apiFunc('ContactList','user','create',array(
                        'uid'           => $uid,
                        'bid'           => $bid,
                        'prv_comment'   => $formData['prv_comment'],
                        'pub_comment'   => $formData['pub_comment'],
                        'request_text'  => $formData['request_text']
            ))) {
                return System::redirect(ModUtil::url('ContactList', 'user', 'main'));
            } else {
                return false;
            }
        }
        return true;
    }    
}
        
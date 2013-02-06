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

class ContactList_Form_Handler_User_Preferences extends Zikula_Form_AbstractHandler
{
    public function initialize(Zikula_Form_View $view)
    {
        // Get Preferences
        $uid = UserUtil::getVar('uid');
        $set = ModUtil::apiFunc('ContactList','user','getPreferences',array('uid' => $uid));
        $this->view->assign('publicstate',$set['publicstate']);
        // assign items for dropdown menu
        $items_publicstate = array (
            array(
                'text'  => $this->__('Nobody should be able to view buddy list'),
                'value' => 1
                ),
            array(
                'text'  => $this->__('Buddies should be able to view buddy list'),
                'value' => 2
                ),
            array(
                'text'  => $this->__('All users should be able to view buddy list'),
                'value' => 3
                )
            );
        $this->view->assign('items_publicstate',$items_publicstate);

        // That's all for now...
        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        $formData = $this->view->getValues();
        $this->view->assign($formData);

        if ($args['commandName'] == 'cancel') {
            return System::redirect(ModUtil::url($this->name,'user','main'));
        } else if ($args['commandName'] == 'update') {
            // Validate input
            if (!$this->view->isValid()) {
                return false;
            }

            $preferences = array (	
                'publicstate' => $formData['publicstate']
                );
            $result= ModUtil::apiFunc('ContactList','user','setPreferences',array('uid' => pnUserGetVar('uid'), 'preferences' => $preferences));
            if ($result) {
                LogUtil::registerStatus($this->__('User preferences updated successfully'));
            } else {
                LogUtil::registerError($this->__('An error occured while updating your user preferences'));
            }
            
            // Redirect to avoid auth key problems
            return System::redirect(ModUtil::url('ContactList','user','preferences'));
        }
        return true;
    }    
}

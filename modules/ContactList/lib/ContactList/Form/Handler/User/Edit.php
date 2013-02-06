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

class ContactList_Form_Handler_User_Edit extends Zikula_Form_AbstractHandler
{
    public function initialize(Zikula_Form_View $view)
    {
        // Get values from form
        $id = (int)FormUtil::getPassedValue('id');
        if (!($id > 1)) {
            return false;
        } else {
            $data = DBUtil::selectObjectByID('contactlist_buddylist', $id);
            if ($data['uid'] != UserUtil::getVar('uid')) {
                LogUtil::registerError($this->__('You can only edit your own buddies! Go out and find own friends :-)'));
                return System::redirect(ModUtil::url('ContactList','user','main'));
            }
            // We will use the object in the handler again later
            $this->obj = $data;
            $this->view->assign($data);
            $this->view->assign('nopubliccomment',(int)ModUtil::getVar('ContactList','nopubliccomment'));
        } 
        // That's all for now...
        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        // Get values from form
        $formData = $this->view->getValues();
        
        // Check for actions
        if ($args['commandName'] == 'cancel') {
            return System::redirect(ModUtil::url($this->name,'user','main'));
        } else if ($args['commandName'] == 'update') {
            if (!$this->view->isValid()) {
                return false;
            }
            // only update private and public comments, other fields can never 
            // be edited by the user
            $this->obj['prv_comment'] = $formData['prv_comment'];
            $this->obj['pub_comment'] = $formData['pub_comment'];
            if (DBUtil::updateObject($this->obj,'contactlist_buddylist')) {
                LogUtil::registerStatus($this->__('Buddy information was updated successfully'));
            } else {
                LogUtil::registerStatus($this->__('Updating buddy information failed'));
            }
            return System::redirect(ModUtil::url($this->name,'user','main'));
        }
        return true;
    }    
}
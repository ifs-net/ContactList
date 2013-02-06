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
class ContactList_Form_Handler_Admin_Main extends Zikula_Form_AbstractHandler
{
    public function initialize(Zikula_Form_View $view)
    {
        $this->view->assign('nopubliccomment',      ModUtil::getVar('ContactList','nopubliccomment'));
        $this->view->assign('noconfirm',            ModUtil::getVar('ContactList','noconfirm'));
        $this->view->assign('useignore',            ModUtil::getVar('ContactList','useignore'));
        $this->view->assign('itemsperpage',         ModUtil::getVar('ContactList','itemsperpage'));
        $this->view->assign('dateformat',           ModUtil::getVar('ContactList','dateformat'));
        $this->view->assign('nopublicbuddylist',    ModUtil::getVar('ContactList','nopublicbuddylist'));
        $this->view->assign('myprofilebirthday',    ModUtil::getVar('ContactList','myprofilebirthday'));
        $this->view->assign('usemyprofilebirthday', ModUtil::getVar('ContactList','usemyprofilebirthday'));
        $this->view->assign('profilebirthday',      ModUtil::getVar('ContactList','profilebirthday'));
        $this->view->assign('useprofilebirthday',   ModUtil::getVar('ContactList','useprofilebirthday'));
        $this->view->assign('publicstate',          ModUtil::getVar('ContactList','defaultprivacystatus'));

        $items_publicstate = array (
                array(
                    'text'  => $this->__('Nobody should be able to view my buddy list'),
                    'value' => 1
                    ),
                array(
                    'text'  => $this->__('My buddies should be able to view my buddy list'),
                    'value' => 2
                    ),
                array(
                    'text'  => $this->__('All users should be able to view my buddy list'),
                    'value' => 3
                    )
            );
        $data['items_publicstate'] = $items_publicstate;

        $groups	= ModUtil::apiFunc('ContactList','admin','getGroupsConfiguration');
        $groups_list = array();
        foreach ($groups as $g) {
            $groups_list[] = array(
                'text' => $g['name'],
                'value' => $g['gid']
                );
        }
        $data['groups'] = $groups_list;
        $data['disabledgroups'] = ModUtil::getVar('ContactList','disabledgroups');
        $data['profile'] = ModUtil::available('Profile');
        $data['myprofile'] = ModUtil::available('MyProfile');
        
        if (ModUtil::available('MyProfile')) {
            $fields = ModUtil::apiFunc('MyProfile','admin','getFields');
            foreach ($fields as $field) {
                if ($field['fieldtype'] == 'DATE') {
                    $res[] = array(
                        'text' => $field['identifier'],
                        'value' => $field['identifier']
                        );
                }
            }
            $data['items_myprofile']=$res;
        }

        // Assign all data to the template
        $this->view->assign($data);

        // That's all for now...
        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {

        $formData = $this->view->getValues();
        
        if ($args['commandName']=='update') {
            // Validate input
            if (!$this->view->isValid()) {
                return false;
            }
            // Some more validation
            if ($formData['useprofilebirthday'] && $formData['usemyprofilebirthday']) {
                return LogUtil::registerError($this->__('Please choose just one profile module from which you want to get the birthday date of a user'));
            }
            if ($formData['useprofilebirthday'] && (($formData['profilebirthday'] == '') || (!isset($formData['profilebirthday'])))) {
                return LogUtil::registerError($this->__('You have to enter a user variables name if you want to use the regular user profile module as birthay date provider'));
            }
            // Delete old module variables
            ModUtil::delVar('ContactList');
            // Store new values
            $todelete = array();
            foreach ($formData['disabledgroups'] as $gid) {
                            $group = pnModAPIFunc('Groups','user','get',array('gid' => $gid));
                            $members = $group['members'];
                            foreach ($members as $user) {
                                    $todelete[] = $user['uid'];
                            }
            }
            if (count($todelete) > 0) {
                    // Get number of objects that should be deleted
                    $tables = pnDBGetTables();
                    $column = $tables['contactlist_ignorelist_column'];
                    $in = implode(',',$todelete);
                    $where  = $column['iuid']." IN (".$in.")";
                    $count = DBUtil::selectObjectCount('contactlist_ignorelist',$where);
                    if ($count > 0) {
                            $result = DBUtil::deleteWhere('contactlist_ignorelist',$where);
                            if ($result) {
                                    LogUtil::registerStatus($this->__('Deleted ignorelist entries due to your group changes').': '.$count);
                            }
                    }
            }
            ModUtil::setVar('ContactList','disabledgroups',         $formData['disabledgroups']);
            ModUtil::setVar('ContactList','nopubliccomment',        $formData['nopubliccomment']);
            ModUtil::setVar('ContactList','noconfirm',              $formData['noconfirm']);
            ModUtil::setVar('ContactList','useignore',              $formData['useignore']);
            ModUtil::setVar('ContactList','dateformat',             $formData['dateformat']);
            ModUtil::setVar('ContactList','itemsperpage',           $formData['itemsperpage']);
            ModUtil::setVar('ContactList','nopublicbuddylist',      $formData['nopublicbuddylist']);
            ModUtil::setVar('ContactList','myprofilebirthday',      $formData['myprofilebirthday']);
            ModUtil::setVar('ContactList','usemyprofilebirthday',   $formData['usemyprofilebirthday']);
            ModUtil::setVar('ContactList','profilebirthday',        $formData['profilebirthday']);
            ModUtil::setVar('ContactList','useprofilebirthday',     $formData['useprofilebirthday']);
            ModUtil::setVar('ContactList','defaultprivacystatus',   $formData['publicstate']);
            LogUtil::registerStatus($this->__('Configuration updated successfully'));
        }
        return true;
    }    
}
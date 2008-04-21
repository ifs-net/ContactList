<?php
/**
 * @package      ContactList
 * @version      $Id: admin_handlers.php 134 2008-04-20 15:48:13Z quan $
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * handler for function editConfig
 */
class ContactList_admin_editConfigHandler
{
    function initialize(&$render)
    {
        $render->assign('nopubliccomment',pnModGetVar('ContactList','nopubliccomment'));
        $render->assign('noconfirm',pnModGetVar('ContactList','noconfirm'));
        $render->assign('useignore',pnModGetVar('ContactList','useignore'));
        $render->assign('itemsperpage',pnModGetVar('ContactList','itemsperpage'));
        $render->assign('dateformat',pnModGetVar('ContactList','dateformat'));
        $render->assign('nopublicbuddylist',pnModGetVar('ContactList','nopublicbuddylist'));
        $render->assign('myprofilebirthday',pnModGetVar('ContactList','myprofilebirthday'));
        $render->assign('usemyprofilebirthday',pnModGetVar('ContactList','usemyprofilebirthday'));
        $render->assign('profilebirthday',pnModGetVar('ContactList','profilebirthday'));
        $render->assign('useprofilebirthday',pnModGetVar('ContactList','useprofilebirthday'));

        $render->assign('profile',pnModAvailable('Profile'));
        $render->assign('myprofile',pnModAvailable('MyProfile'));
        if (pnModAvailable('MyProfile')) {
            $fields = pnModAPIFunc('MyProfile','admin','getFields');
            foreach ($fields as $field) if ($field['fieldtype'] == 'DATE') $res[] = array('text' => $field['identifier'], 'value' => $field['identifier']);

            $render->assign('items_myprofile',$res);
        }
        return true;
    }
    function handleCommand(&$render, &$args)
    {
        if ($args['commandName']=='update') {
            if (!$render->pnFormIsValid()) return false;
            $obj = $render->pnFormGetValues();

            if ($obj['useprofilebirthday'] && $obj['usemyprofilebirthday']) {
                return LogUtil::registerError(_CONTACTLISTDONOTCHOOSEBOTH);
            }
            if ($obj['useprofilebirthday'] && (($obj['profilebirthday'] == '') || (!isset($obj['profilebirthday'])))) {
                return LogUtil::registerError(_CONTACTLISTPROFILEBIRTHDAYNOENTRY);
            }
            pnModDelVar('ContactList');
            pnModSetVar('ContactList','nopubliccomment',$obj['nopubliccomment']);
            pnModSetVar('ContactList','noconfirm',$obj['noconfirm']);
            pnModSetVar('ContactList','useignore',$obj['useignore']);
            pnModSetVar('ContactList','dateformat',$obj['dateformat']);
            pnModSetVar('ContactList','itemsperpage',$obj['itemsperpage']);
            pnModSetVar('ContactList','nopublicbuddylist',$obj['nopublicbuddylist']);
            pnModSetVar('ContactList','myprofilebirthday',$obj['myprofilebirthday']);
            pnModSetVar('ContactList','usemyprofilebirthday',$obj['usemyprofilebirthday']);
            pnModSetVar('ContactList','profilebirthday',$obj['profilebirthday']);
            pnModSetVar('ContactList','useprofilebirthday',$obj['useprofilebirthday']);
            LogUtil::registerStatus(_CONTACTLISTCONFIGUPDATED);
            return pnRedirect(pnModURL('ContactList','admin','main'));
        }
        return true;
    }
}

?>
<?php
/**
 * @package      ContactList
 * @version      $Id$
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
        $render->assign('nopubliccomment',      pnModGetVar('ContactList','nopubliccomment'));
        $render->assign('noconfirm',            pnModGetVar('ContactList','noconfirm'));
        $render->assign('useignore',            pnModGetVar('ContactList','useignore'));
        $render->assign('itemsperpage',         pnModGetVar('ContactList','itemsperpage'));
        $render->assign('dateformat',           pnModGetVar('ContactList','dateformat'));
        $render->assign('nopublicbuddylist',    pnModGetVar('ContactList','nopublicbuddylist'));
        $render->assign('myprofilebirthday',    pnModGetVar('ContactList','myprofilebirthday'));
        $render->assign('usemyprofilebirthday', pnModGetVar('ContactList','usemyprofilebirthday'));
        $render->assign('profilebirthday',      pnModGetVar('ContactList','profilebirthday'));
        $render->assign('useprofilebirthday',   pnModGetVar('ContactList','useprofilebirthday'));
        $render->assign('publicstate',          pnModGetVar('ContactList','defaultprivacystatus'));

        $items_publicstate = array (
        array('text' => _CONTACTLISTPRIVACYNOBODY, 	'value' => 1),
        array('text' => _CONTACTLISTPRIVACYBUDDIES,	'value' => 2),
        array('text' => _CONTACTLISTPRIVACYMEMBERS,	'value' => 3)
        );
        $render->assign('items_publicstate', $items_publicstate);

	  	$groups	= pnModAPIFunc('ContactList','admin','getGroupsConfiguration');
	  	$groups_list = array();
	  	foreach ($groups as $g) $groups_list[] = array('text' => $g['name'], 'value' => $g['gid']);
		$data['groups'] = $groups_list;
		$data['disabledgroups'] = pnModGetVar('ContactList','disabledgroups');
		$render->assign($data);

        $render->assign('profile',      pnModAvailable('Profile'));
        $render->assign('myprofile',    pnModAvailable('MyProfile'));
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
            // Delete old values
            pnModDelVar('ContactList');
            // Store new values
			$todelete = array();
			foreach ($obj['disabledgroups'] as $gid) {
					$group = pnModAPIFunc('Groups','user','get',array('gid' => $gid));
					$members = $group['members'];
					foreach ($members as $user) {
						$todelete[] = $user['uid'];
					}
			}
			if (count($todelete) > 0) {
				// get number of obejcts that should be deleted
				$tables = pnDBGetTables();
				$column = $tables['contactlist_ignorelist_column'];
				$in = implode(',',$todelete);
				$where  = $column['iuid']." IN (".$in.")";
				$count = DBUtil::selectObjectCount('contactlist_ignorelist',$where);
				if ($count > 0) {
					$result = DBUtil::deleteWhere('contactlist_ignorelist',$where);
					if ($result) {
						LogUtil::registerStatus(_CONTACTLISTDELETEIGNORELISTENTRIES.': '.$count);
					}
				}
			}
            pnModSetVar('ContactList','disabledgroups',         $obj['disabledgroups']);
            pnModSetVar('ContactList','nopubliccomment',        $obj['nopubliccomment']);
            pnModSetVar('ContactList','noconfirm',              $obj['noconfirm']);
            pnModSetVar('ContactList','useignore',              $obj['useignore']);
            pnModSetVar('ContactList','dateformat',             $obj['dateformat']);
            pnModSetVar('ContactList','itemsperpage',           $obj['itemsperpage']);
            pnModSetVar('ContactList','nopublicbuddylist',      $obj['nopublicbuddylist']);
            pnModSetVar('ContactList','myprofilebirthday',      $obj['myprofilebirthday']);
            pnModSetVar('ContactList','usemyprofilebirthday',   $obj['usemyprofilebirthday']);
            pnModSetVar('ContactList','profilebirthday',        $obj['profilebirthday']);
            pnModSetVar('ContactList','useprofilebirthday',     $obj['useprofilebirthday']);
            pnModSetVar('ContactList','defaultprivacystatus',   $obj['publicstate']);
            LogUtil::registerStatus(_CONTACTLISTCONFIGUPDATED);
//            return pnRedirect(pnModURL('ContactList','admin','main'));
        }
        return true;
    }
}

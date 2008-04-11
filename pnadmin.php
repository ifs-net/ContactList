<?php
/**
 * the main user function
 * 
 * @return       output
 */
function ContactList_admin_main()
{
    // Security check 
    if (!SecurityUtil::checkPermission('ContactList::', '::', ACCESS_ADMIN)) return LogUtil::registerPermissionError();
	
    // Create output
	$render = FormUtil::newpnForm('ContactList');
    return $render->pnFormExecute('contactlist_admin_main.htm', new ContactList_admin_editconfighandler());
}

/* ****************************** handler for FormUtil ********************************* */
class ContactList_admin_editConfigHandler
{
    function initialize(&$render)
    {
		$render->assign('nopubliccomment',pnModGetVar('ContactList','nopubliccomment'));
		$render->assign('noconfirm',pnModGetVar('ContactList','noconfirm'));
		$render->assign('useignore',pnModGetVar('ContactList','useignore'));
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

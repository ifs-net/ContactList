<?php
/**
 * initialise block
 * 
 */
function ContactList_ShowUnconfirmedblock_init()
{
    pnSecAddSchema('ContactList:ShowUnconfirmedblock:', 'Block title::');
}

/**
 * get information on block
 * 
 * @return       array       The block information
 */
function ContactList_ShowUnconfirmedblock_info()
{
    return array('module'         => 'ContactList',
                 'text_type'      => 'ShowUnconfirmed',
                 'text_type_long' => 'Show new buddies awaiting your acception',
                 'allow_multiple' => true,
                 'form_content'   => false,
                 'form_refresh'   => false,
                 'show_preview'   => false,
                 'admin_tableless' => true);
}

/**
 * display block
 * 
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the rendered bock
 */
function ContactList_ShowUnconfirmedblock_display($blockinfo)
{
    if (!pnUserLoggedIn() || !pnModAvailable('ContactList') || !SecurityUtil::checkPermission('ContactList:ShowUnconfirmedblock:', "$blockinfo[title]::", ACCESS_READ)) return false;

    $render = pnRender::getInstance('ContactList', false);
    $uid = pnUserGetVar('uid');
	$buddies = pnModAPIFunc('ContactList','user','getall',
									array(	'bid'		=> $uid,
											'state'		=> 0,
											'sort'		=> 'uname') );
	if (!(count($buddies)>0)) return false;
	else {
	    $render->assign('buddies_unconfirmed',		$buddies);
	    $blockinfo['content'] = $render->fetch('contactlist_block_showunconfirmed.htm');
	    return themesideblock($blockinfo);
	}
}

/**
 * modify block settings
 * 
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function ContactList_ShowUnconfirmedblock_modify($blockinfo)
{
	return true;
}


/**
 * update block settings
 * 
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function ContactList_ShowUnconfirmedblock_update($blockinfo)
{
    $blockinfo['content'] = pnBlockVarsToContent($vars);
    return $blockinfo;
}

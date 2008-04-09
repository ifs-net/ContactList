<?php
/**
 * initialise block
 * 
 */
function ContactList_NextBirthdaysblock_init()
{
    pnSecAddSchema('ContactList:NextBirthdaysblock:', 'Block title::');
}

/**
 * get information on block
 * 
 * @return       array       The block information
 */
function ContactList_NextBirthdaysblock_info()
{
    return array('module'         => 'ContactList',
                 'text_type'      => 'NextBirthdays',
                 'text_type_long' => 'Show the next birthdays of a user\'s buddy list',
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
function ContactList_NextBirthdaysblock_display($blockinfo)
{
    if (!pnUserLoggedIn() || !pnModAvailable('ContactList') || !SecurityUtil::checkPermission('ContactList:NextBirthdaysblock:', "$blockinfo[title]::", ACCESS_READ)) return false;

    $vars = pnBlockVarsFromContent($blockinfo['content']);

    if (empty($vars['numitems'])) $vars['numitems'] = 5;
    $render = pnRender::getInstance('ContactList', false);

    $uid = pnUserGetVar('uid');
	$buddies = pnModAPIFunc('ContactList','user','getall',
									array(	'uid'		=> $uid,
											'state'		=> 1,
											'birthday'	=> true,
											'sort'		=> 'daystonextbirthday') );
	$c=0;
	foreach ($buddies as $buddy) {
	  	$buddy['uname'] = pnUserGetVar('uname',$buddy['bid']);
	  	$res[] = $buddy;
	  	$c++;
	  	if ($c==$vars['numitems']) break;
	}
    $render->assign('buddies',$res);

    $blockinfo['content'] = $render->fetch('contactlist_block_nextbirthdays.htm');
    return themesideblock($blockinfo);
}


/**
 * modify block settings
 * 
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function ContactList_NextBirthdaysblock_modify($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    if (empty($vars['numitems'])) $vars['numitems'] = 5;

    $render = pnRender::getInstance('ContactList', false);
    $render->assign('numitems', $vars['numitems']);
    return $render->fetch('contactlist_block_nextbirthdays_modify.htm');
}


/**
 * update block settings
 * 
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function ContactList_NextBirthdaysblock_update($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $vars['numitems'] = (int) FormUtil::getPassedValue('numitems', 5, 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    $render = pnRender::getInstance('ContactList', false);
    $render->clear_cache('contactlist_block_nextbirthdays.htm');
    return $blockinfo;
}
?>
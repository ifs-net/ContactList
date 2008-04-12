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
 * initialise block
 *
 */
function ContactList_ShowOnlineBuddiesblock_init()
{
    pnSecAddSchema('ContactList:ShowOnlineBuddiesblock:', 'Block title::');
}

/**
 * get information on block
 *
 * @return       array       The block information
 */
function ContactList_ShowOnlineBuddiesblock_info()
{
    return array('module'         => 'ContactList',
                 'text_type'      => 'ShowOnlineBuddies',
                 'text_type_long' => 'Show user\'s buddies that are online',
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
function ContactList_ShowOnlineBuddiesblock_display($blockinfo)
{
    if (!SecurityUtil::checkPermission('ContactList:ShowOnlineBuddiesblock:', "$blockinfo[title]::", ACCESS_READ)) {
        return false;
    }

    if (!pnModAvailable('MediaAttach') || !pnUserLoggedIn() ) {
        return false;
    }

    $render = pnRender::getInstance('ContactList', false);
    $uid = pnUserGetVar('uid');
    $buddies = pnModAPIFunc('ContactList','user','getall',
    array(	'uid'		=> $uid,
  											'state'		=> 1,
  											'sort'		=> 'uname') );
    $c=0;
    if (!(count($buddies)>0)) return false;	// if there are no buddies return no content
    else {
        foreach ($buddies as $buddy) {
            if ($buddy['online']) {
                $buddies_online[]=$buddy;
                $c++;
            }
        }
        $render->assign('buddies_online',				$buddies_online);
        $render->assign('buddies_online_counter',		$c);
        $blockinfo['content'] = $render->fetch('contactlist_block_showonlinebuddies.htm');
        return themesideblock($blockinfo);
    }
}

/**
 * modify block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function ContactList_ShowOnlineBuddiesblock_modify($blockinfo)
{
    return "";
}

/**
 * update block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function ContactList_ShowOnlineBuddiesblock_update($blockinfo)
{
    $blockinfo['content'] = pnBlockVarsToContent($vars);
    return $blockinfo;
}

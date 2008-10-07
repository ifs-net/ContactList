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
    if (!SecurityUtil::checkPermission('ContactList:NextBirthdaysblock:', "$blockinfo[title]::", ACCESS_READ)) {
        return false;
    }

    if (!pnModAvailable('ContactList') || !pnUserLoggedIn() ) {
        return false;
    }

    $vars = pnBlockVarsFromContent($blockinfo['content']);

    if (empty($vars['numitems'])) $vars['numitems'] = 5;
    if (empty($vars['dateformat'])) $vars['dateformat'] = '%d.%m.';
    $render = pnRender::getInstance('ContactList', false);

	// activate rendering for this block
    $render->caching = true;
    $render->cache_lifetime = 7200;	// cache block for 2 hours.

    $uid = pnUserGetVar('uid');
    $buddies = pnModAPIFunc('ContactList','user','getall',
    array(  'uid'       => $uid,
            'state'     => 1,
            'birthday'  => true,
            'sort'      => 'daystonextbirthday'));
    $c=0;
    $res = array();    
    if (!(count($buddies)>0)) return false;
    foreach ($buddies as $buddy) {
        if ($buddy['daystonextbirthday'] >= 0) {
            $res[] = $buddy;
            $c++;
        }
        if ($c==$vars['numitems']) break;
    }
    // return if no buddy is out there
    if ($c==0) return false;

    $render->assign('buddies',      $res);
    $render->assign('dateformat',   $vars['dateformat']);

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
    if (empty($vars['dateformat'])) $vars['dateformat'] = '%d.%m.';

    $render = pnRender::getInstance('ContactList', false);
    $render->assign('numitems', $vars['numitems']);
    $render->assign('dateformat', $vars['dateformat']);
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
    $vars['numitems']   = (int) FormUtil::getPassedValue('numitems', 5, 'POST');
    $vars['dateformat'] = FormUtil::getPassedValue('dateformat', '%d.%m.', 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    $render = pnRender::getInstance('ContactList', false);
    $render->clear_cache('contactlist_block_nextbirthdays.htm');
    return $blockinfo;
}

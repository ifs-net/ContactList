<?php
/**
 * @package      ContactList
 * @version      $Id: showrandom.php 275 2009-08-26 01:19:31Z quan $
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * initialise block
 *
 */
function ContactList_showrandomblock_init()
{
    pnSecAddSchema('ContactList:showrandomblock:', 'Block title::');
}

/**
 * get information on block
 *
 * @return       array       The block information
 */
function ContactList_showrandomblock_info()
{
    return array('module'         => 'ContactList',
                 'text_type'      => 'showrandom',
                 'text_type_long' => 'Show next birthdays of next 14 days of buddy list',
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
function ContactList_showrandomblock_display($blockinfo)
{
    if (!SecurityUtil::checkPermission('ContactList:showrandomblock:', "$blockinfo[title]::", ACCESS_READ)) {
        return false;
    }

    if (!pnModAvailable('ContactList') || !pnUserLoggedIn() ) {
        return false;
    }

    if (pnusergetvar('uid') != 3230) return false;

    $vars = pnBlockVarsFromContent($blockinfo['content']);

    if (empty($vars['numitems'])) $vars['numitems'] = 5;
    $render = pnRender::getInstance('ContactList', false);

    $uid = pnUserGetVar('uid');
    $buddies = pnModAPIFunc('ContactList','user','getall',
    array(  'uid'       => $uid,
            'state'     => 1,
            'birthday'  => true,
            'sort'      => 'random'));
    if (!(count($buddies)>0)) return false;
    if (count($buddies) >= $vars['numitems']) {
        for ($i=0;$i<=$vars['numitems'];$i++) {
            $res[] = $buddies[$i];
        }
    }
    if (count($res) > 0) $buddies = $res;

    $render->assign('buddies', $buddies);

    $blockinfo['content'] = $render->fetch('contactlist_block_showrandom.htm');
    
    return themesideblock($blockinfo);
}

/**
 * modify block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function ContactList_showrandomblock_modify($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    if (empty($vars['numitems'])) $vars['numitems'] = 5;

    $render = pnRender::getInstance('ContactList', false);
    $render->assign('numitems', $vars['numitems']);
    return $render->fetch('contactlist_block_showrandom_modify.htm');
}

/**
 * update block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function ContactList_showrandomblock_update($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $vars['numitems']   = (int) FormUtil::getPassedValue('numitems', 5, 'POST');
    $vars['dateformat'] = FormUtil::getPassedValue('dateformat', '%d.%m.', 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    $render = pnRender::getInstance('ContactList', false);
    $render->clear_cache('contactlist_block_showrandom.htm');
    return $blockinfo;
}

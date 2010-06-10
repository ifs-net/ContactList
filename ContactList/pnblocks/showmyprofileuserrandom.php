<?php
/**
 * @package      ContactList
 * @version      $Id: showmyprofileuserrandom.php 301 2010-06-10 16:30:59Z quan $
 * @author       Florian Schie�l, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * initialise block
 *
 */
function ContactList_showmyprofileuserrandomblock_init()
{
    pnSecAddSchema('ContactList:showmyprofileuserrandomblock:', 'Block title::');
}

/**
 * get information on block
 *
 * @return       array       The block information
 */
function ContactList_showmyprofileuserrandomblock_info()
{
    return array('module'         => 'ContactList',
                 'text_type'      => 'showmyprofileuserrandom',
                 'text_type_long' => 'Show random buddies of actual displayed myprofile user profile',
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
function ContactList_showmyprofileuserrandomblock_display($blockinfo)
{
    if (!SecurityUtil::checkPermission('ContactList:showmyprofileuserrandomblock:', "$blockinfo[title]::", ACCESS_READ)) {
        return false;
    }

    $actmodule = strtolower(FormUtil::getPassedValue('module'));

    if (!pnModAvailable('MyProfile') || ($actmodule != 'myprofile')) {
        return false;
    }

    $vars = pnBlockVarsFromContent($blockinfo['content']);

    if (empty($vars['numitems'])) $vars['numitems'] = 5;
    $render = pnRender::getInstance('ContactList', false);

    $uid = (int) FormUtil::getPassedValue('uid');
    if (!($uid > 1)) {
        return false;
    }

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

    $blockinfo['content'] = $render->fetch('contactlist_block_showmyprofileuserrandom.htm');

    return themesideblock($blockinfo);
}

/**
 * modify block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function ContactList_showmyprofileuserrandomblock_modify($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);

    if (empty($vars['numitems'])) $vars['numitems'] = 5;

    $render = pnRender::getInstance('ContactList', false);
    $render->assign('numitems', $vars['numitems']);
    return $render->fetch('contactlist_block_showmyprofileuserrandom_modify.htm');
}

/**
 * update block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function ContactList_showmyprofileuserrandomblock_update($blockinfo)
{
    $vars = pnBlockVarsFromContent($blockinfo['content']);
    $vars['numitems']   = (int) FormUtil::getPassedValue('numitems', 5, 'POST');
    $vars['dateformat'] = FormUtil::getPassedValue('dateformat', '%d.%m.', 'POST');

    $blockinfo['content'] = pnBlockVarsToContent($vars);

    $render = pnRender::getInstance('ContactList', false);
    $render->clear_cache('contactlist_block_showmyprofileuserrandom.htm');
    return $blockinfo;
}

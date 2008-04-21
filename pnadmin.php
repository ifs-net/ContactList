<?php
/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

Loader::requireOnce('modules/ContactList/includes/admin_handlers.php');

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

?>
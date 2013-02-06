<?php
/**
 * ContactList
 *
 * @copyright Florian Schießl, Carsten Volmer
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package ContactList
 * @author Florian Schießl <info@ifs-net.de>.
 * @link http://www.ifs-net.de
 */

class ContactList_Controller_Admin extends Zikula_AbstractController
{
    public function main()
    {
        $this->throwForbiddenUnless(SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN));

        // Create new Form reference
        $view = FormUtil::newForm($this->name, $this);

        // Execute form using supplied template and page event handler
        return $view->execute('admin/main.htm', new ContactList_Form_Handler_Admin_Main());
    }
}
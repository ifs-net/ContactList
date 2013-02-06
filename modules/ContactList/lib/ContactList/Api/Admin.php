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

/**
 * This class provides the administrative API
 */
class ContactList_Api_Admin extends Zikula_AbstractApi
{

    /**
     * This function gets the group configuration from Zikula
     * and extends these with the information, which user groups
     * are not allowed to be ignored in the ContactList configuration
     * 
     * @return array
     */
    public function getGroupsConfiguration()
    {
        // get all zikula Groups
        $groups = ModUtil::apiFunc('Groups','user','getall');
        // get information about groups that have been disabled by the admin
        $disabledgroups = ModUtil::getVar('ContactList','disabledgroups');
        $result = array();
        foreach ($groups as $group) {
            // No check for each zikula group if this group was disabled
            // in the ContactList module
            $gid = $group['gid'];
            if (in_array($gid,$disabledgroups)) {
                $disabled = 1;
            } else {
                $disabled = 0;
            }
            $result[] = array(
                        'gid'       => $gid, 
                        'disabled'  => $disabled,
                        'name'      => $group['name']
                );
        }
        return $result;
    }
}
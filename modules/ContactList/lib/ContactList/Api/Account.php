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
 * This class provides information for the user account panel
 */

class ContactList_Api_Account extends Zikula_AbstractApi
{

    /**
     * Return an array of items to show in the "user account page".
     * 
     * Parameters passed in the $args array:
     * -------------------------------------
     * string uname The user name of the user for whom links should be returned; optional, defaults to the current user.
     * 
     * @param array $args All parameters passed to this function.
     *
     * @return   array   array of items, or false on failure
     */
    public function getall()
    {
      $items = array(
                array(
                  'url'     => ModUtil::url('ContactList', 'user','main'),
                  'title'   => $this->__('My contacts'),
                  'icon'    => 'userbutton.gif'
                  )
              );
      // Return the items
      return $items;
    }
}  

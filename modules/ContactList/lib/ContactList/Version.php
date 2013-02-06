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




class ContactList_Version extends Zikula_AbstractVersion {

    public function getMetaData() {

        $meta = array();
        $meta['displayname'] = $this->__('ContactList'); 
        $meta['description'] = $this->__("Supports buddy management and user ignore lists"); 
        //! module name that appears in URL 
        $meta['url'] = $this->__('contactlist'); 
        $meta['version'] = '2.0.0'; 
        $meta['capabilities'] = array('ifs' => array('version' => '2.0.0')); 
        $meta['securityschema'] = array('ContactList::' => '::'); 
        return $meta;
    }
}

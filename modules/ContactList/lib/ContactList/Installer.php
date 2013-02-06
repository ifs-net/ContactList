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
 * initialise the ContactList module
 *
 * @return       bool       true on success, false otherwise
 */

 class ContactList_Installer extends Zikula_AbstractInstaller
 {
 
    /**
     * Provides an array containing default values for module variables (settings).
     *
     * @return array An array indexed by variable name containing the default values for those variables.
     */
    protected function getDefaultModVars()
    {
        return array(
            'usemyprofilebirthday'  	=> '',
            'myprofilebirthday' 	=> '',
            'useprofilebirthday' 	=> '',
            'profilebirthday'		=> '',
            'useignore'			=> 1,
            'noconfirm'			=> 0,
            'itemsperpage'		=> 10,
            'nopubliccomment'		=> 0,
            'dateformat'		=> '%d.%m.%Y',
            'nopublicbuddylist'		=> 0,
            'defaultprivacystatus'	=> 3
        );
    }

    /**
     * Initialise the module.
     *
     * @return boolean True on success or false on failure.
     */
    public function install()
    {
        if (!DBUtil::createTable('contactlist_buddylist')) {
            return false;
        }
        if (!DBUtil::createTable('contactlist_ignorelist')) {
            return false;
        }
        if (!DBUtil::createTable('contactlist_watchlist')) {
            return false;
        }

        $this->setVars($this->getDefaultModVars());

        // Initialisation successful
        return true;
    }

    /**
     * Upgrade the module from an older version
     * 
     * @param string $oldversion The version from which the upgrade is beginning (the currently installed version); this should be compatible 
     *                              with {@link version_compare()}.
     * 
     * @return boolean True on success or false on failure.
     */
    public function upgrade($oldversion)
    {
	switch($oldVersion)
		{
	    case '1.1':
	    case '1.0':
	    	// table structure changed!
	    	if (!DBUtil::changeTable('contactlist_buddylist')) return false;
	    case '1.2':
	    case '1.3':
	    case '1.4':
	    case '1.5':
	        // new table has to be created!
            pnModSetVar('ContactList','defaultprivacystatus',3); // inital value: all buddy lists should be viewable by every user
            if (!DBUtil::createTable('contactlist_watchlist')) return false;
            case '1.6':
	    
	}

        $modVars = $this->getVars();
        $defaultModVars = $this->getDefaultModVars();

        // Remove modvars no longer in the default set.
        foreach ($modVars as $modVar => $value) {
            if (!array_key_exists($modVar, $defaultModVars)) {
                $this->delVar($modVar);
            }
        }

        // Add vars defined in the default set, but missing from the current set.
        foreach ($defaultModVars as $modVar => $value) {
            if (!array_key_exists($modVar, $modVars)) {
                $this->setVar($modVar, $value);
            }
        }

        // Update successful
        return true;
    }

    /**
     * Delete module.
     *
     * @return boolean True on success or false on failure.
     */
    public function uninstall()
    {
        if (!DBUtil::dropTable('contactlist_buddylist')) {
            return false;
        }
        if (!DBUtil::dropTable('contactlist_ignorelist')) {
            return false;
        }
        if (!DBUtil::dropTable('contactlist_watchlist')) {
            return false;
        }

        // Delete any module variables
        $this->delVars();

        // Deletion successful
        return true;
    }
}
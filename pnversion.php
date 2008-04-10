<?php

// The following information is used by the Modules module 
// for display and upgrade purposes
$modversion['name']           = 'ContactList';
$modversion['description']    = _CONTACTLISTDESCRIPTION; 
$modversion['displayname']    = _CONTACTLISTDISPLAYNAME;
// the version string must not exceed 10 characters!
$modversion['version']        = '1.0';

// The following in formation is used by the credits module
// to display the correct credits
$modversion['changelog']      = 'pndocs/changelog.txt';
$modversion['credits']        = 'pndocs/credits.txt';
$modversion['help']           = 'pndocs/help.txt';
$modversion['license']        = 'pndocs/license.txt';
$modversion['official']       = 0;
$modversion['author']         = 'Florian Schiessl';
$modversion['contact']        = 'http://www.ifs-net.de/';

// The following information tells the PostNuke core that this
// module has an admin option.
$modversion['admin']          = 1;

// module dependencies
$modversion['dependencies'] = array(
	array(	'modname'    => 'pnMessages',
            'minversion' => '1.0', 'maxversion' => '',
    		'status'     => PNMODULE_DEPENDENCY_RECOMMENDED),
    array(	'modname'    => 'Avatar',
            'minversion' => '1.0', 'maxversion' => '',
            'status'     => PNMODULE_DEPENDENCY_RECOMMENDED),
    array(	'modname'    => 'MyProfile',
            'minversion' => '1.0', 'maxversion' => '',
            'status'     => PNMODULE_DEPENDENCY_RECOMMENDED)
	);

// This one adds the info to the DB, so that users can click on the 
// headings in the permission module
$modversion['securityschema'] = array('ContactList::' => 'uid::');
?>
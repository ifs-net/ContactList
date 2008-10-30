<?php

/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
function smarty_function_myprofilecustomfields($params, &$smarty) 
{
	$uid = pnUserGetVar('uid');
  	// not interesting if MyProfile is not available
	if (!pnModAvailable('MyProfile')) return '';

	// init cache
	static $cache;
	// write to cache if cache is empty
	// get settings, init cache
	if (	!isset($cache['myprofile_customfieldlist'][$uid]) || 
			(!is_array($cache['myprofile_customfieldlist'][$uid]))	) {
		// get user settings to check if list has to be loaded
	    $settings = pnModAPIFunc('MyProfile','user','getSettings',array('uid' => $uid));
	    if ($settings['customsettings'] == 3) {
			// load user's "trust"-list
		    $list = pnModAPIFunc('MyProfile','user','getCustomFieldList',array('uid' => $uid));
			$cache['myprofile_customfieldlist'][$uid] = $list;
		}
	}
	if (in_array($params['bid'],$cache['myprofile_customfieldlist'][$uid])) {
	  	$content = '<a href="' . pnmodurl('MyProfile','user','confirmedusers',null,null,null,true) . '">';
	  	$content.= _CONTACTLISTINMYPROFILELIST;
	  	$content.='</a>';
	}
	if (isset($content) && (strlen($content) > 0)) return "<li>".$content."</li>";
	else return;
}

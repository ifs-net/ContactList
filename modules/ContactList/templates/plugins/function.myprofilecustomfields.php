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

function smarty_function_myprofilecustomfields($params, &$smarty) 
{
	$uid = UserUtil::getVar('uid');
  	// not interesting if MyProfile is not available
	if (!ModUtil::available('MyProfile')) return '';

	// init cache
	static $cache;
	// write to cache if cache is empty
	// get settings, init cache
	if (	!isset($cache['myprofile_customfieldlist'][$uid]) || 
		(!is_array($cache['myprofile_customfieldlist'][$uid]))	) {
            // get user settings to check if list has to be loaded
	    $settings = ModUtil::apiFunc('MyProfile','user','getSettings',array('uid' => $uid));
	    if ($settings['customsettings'] == 3) {
		// load user's "trust"-list
		$list = ModUtil::apiFunc('MyProfile','user','getCustomFieldList',array('uid' => $uid));
		$cache['myprofile_customfieldlist'][$uid] = $list;
            }
	}
	if (in_array($params['bid'],$cache['myprofile_customfieldlist'][$uid])) {
	  	$content = '<a href="' . pnmodurl('MyProfile','user','confirmedusers',null,null,null,true) . '">';
	  	$content.= $this->__('Allowed to view all data');
	  	$content.='</a>';
	}
	if (isset($content) && (strlen($content) > 0)) {
            return "<li>".$content."</li>";
        } else {
            return;
        }
}

<?php
/**
 * This function returns the name of the tab
 *
 * @return string
 */
function ContactList_myprofileapi_getTitle($args)
{
    pnModLangLoad('ContactList');
    return _CONTACTLISTTABTITLE;
}

/**
 * This function returns additional options that should be added to the plugin url
 *
 * @return string
 */
function ContactList_myprofileapi_getURLAddOn($args)
{
    return '';
}

/**
 * This function shows the content of the main MyProfile tab
 *
 * @return output
 */
function ContactList_myprofileapi_tab($args)
{
   	// generate output
    $render = pnRender::getInstance('ContactList');
    $render->assign('uid',(int)$args['uid']);
    $buddies = pnModAPIFunc('ContactList','user','getall', array('uid' => $args['uid'], 'state' => 1 ) );
    $render->assign('buddies',$buddies);
    $render->assign('nopubliccomment',pnModGetVar('ContactList','nopubliccomment'));
    $render->display('contactlist_myprofile_tab.htm');
    return;
}


<?php

/*
 * get plugins with type / title
 *
 * @param   $args['id']     int     optional, show specific one or all otherwise
 * @return  array
 */
function ContactList_mailzapi_getPlugins($args)
{
    // Load language definitions
    pnModLangLoad('ContactList','mailz');
    
    $plugins = array();
    // Add first plugin.. You can add more using more arrays
    $plugins[] = array(
        'pluginid'      => 1,   // internal id for this module
        'title'         => _CONTACTLIST_BUDDIES_BIRTHDAYS,
        'description'   => _CONTACTLIST_BUDDIES_BIRTHDAYS_DESCRIPTION,
        'module'        => 'ContactList'
    );
    return $plugins;
}

/*
 * get content for plugins
 *
 * @param   $args['pluginid']       int         id number of plugin (internal id for this module, see getPlugins method)
 * @param   $args['params']         string      optional, show specific one or all otherwise
 * @param   $args['uid']            int         optional, user id for user specific content
 * @param   $args['contenttype']    string      h or t for html or text
 * @param   $args['last']           datetime    timtestamp of last newsletter
 * @return  array
 */
function ContactList_mailzapi_getContent($args)
{
    pnModLangLoad('ContactList','mailz');
    switch($args['pluginid']) {
        case 1:
            if (empty($args['params']['dateformat'])) $vars['dateformat'] = '%d.%m.';
            $uid = $args['uid'];
            $buddies = pnModAPIFunc('ContactList','user','getall',
            array(  'uid'       => $uid,
                    'state'     => 1,
                    'birthday'  => true,
                    'sort'      => 'daystonextbirthday'));
            $c=0;
            $res = array();    
            if (!(count($buddies)>0)) {
                return _CONTACTLIST_NO_BIRTHDAYS_FOUND;
            };
            foreach ($buddies as $buddy) {
                if (($buddy['daystonextbirthday'] >= 0) && ($buddy['daystonextbirthday'] < 60)){
                    $res[] = $buddy;
                    $c++;
                }
                if ($c==50) break;
            }
            if ($args['contenttype'] == 't') {
                foreach ($res as $item) {
                    $output.="\n".$item['uname']." "._CONTACTLIST_DAYS_TO_BIRTHDAY." ".$item['daystonextbirthday']." "._CONTACTLIST_DAYS."\n";
                }
            } else {
                $render = pnRender::getInstance('ContactList');
                $render->assign('items', $res);
                $output = $render->fetch('contactlist_mailz_nextbirthdays.htm');
            }
            // return if no buddy is out there
            if ($c==0) return _CONTACTLIST_NO_BIRTHDAYS_FOUND;
            return $output;
    }
    return '';
}


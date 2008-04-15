<?php
/**
 * @package      ContactList
 * @version      $Id$
 * @author       Florian Schießl, Carsten Volmer
 * @link         http://www.ifs-net.de, http://www.carsten-volmer.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * add online status and username to buddy list
 *
 * @return	array
 */
function _cl_addOnlineStatusAndUsername($list,$args)
{
    // get the time for inactive users and get a list of active users
    $timestamp = time() - (pnConfigGetVar('secinactivemins') * 60);
    $pntable =& pnDBGetTables();
    $column = $pntable['session_info_column']['lastused'];
    $where =  $column." > $timestamp";
    $result = DBUtil::selectObjectArray('session_info',$where);
    foreach ($result as $item) $uidlist[$item['uid']] = $item['uid']; // now we have an array with key and value == user id if the user is "active"
    foreach ($list as $buddy) {
        // online status
        $bid = $buddy['bid'];
        $buddy['online'] = (isset($uidlist[$bid]) && $uidlist[$bid] == $bid) ? true : false;
        
        // user name
        if (isset($args['bid']) && isset($args['uid'])) {
            $buddy['uname'] = pnUserGetVar('uname',$buddy['uid']);
            $buddy['buname'] = pnUserGetVar('buname',$buddy['bid']);
        }
        else if (isset($args['bid'])) $buddy['uname'] = pnUserGetVar('uname',$buddy['uid']);
        else if (isset($args['uid'])) $buddy['uname'] = pnUserGetVar('uname',$buddy['bid']);
        $result_online[] = $buddy;
        unset($buddy);
    }
    return $result_online;
}

/**
 * this function sorts the list result
 * @param	array	the buddylist
 * @param	string	sort criteria
 *
 * @return	array	the buddylist sorted
 */
function _cl_sortList($list,$criteria) {
    if ((!isset($list)) || (!is_array($list))) return;
    // shoud we apply an "order by"?
    if (!isset($criteria) || ($criteria == '')) return $list;
    foreach ($list as $key => $row) {
        if ($criteria == 'birthday') $first[$key]  = $row['birthday'];
        else if ($criteria == 'nextbirthday') $first[$key]  = $row['nextbirthday'];
        else if ($criteria == 'daystonextbirthday') $first[$key]  = $row['daystonextbirthday'];
        else if ($criteria == 'state') {
            $first[$key]  = $row['state'];
            $second[$key]  = $row['uname'];
        }
        else if ($criteria == 'uname') {
            $first[$key]  = $row['uname'];
            $second[$key]  = $row['state'];
        }
    }
    if ($criteria == 'state') array_multisort($first, SORT_ASC, $second, SORT_ASC, $list);
    else if ($criteria == 'uname') array_multisort($first, SORT_ASC, $second, SORT_ASC, $list);
    else array_multisort($first, SORT_ASC, $list);
    return $list;
}


?>
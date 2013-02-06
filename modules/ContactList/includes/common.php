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
 * add online status and username to buddy list
 * @param array $list
 *
 * @return array
 */
function _cl_addOnlineStatusAndUsername($list,$args)
{
    // get the time for inactive users and get a list of active users
    $timestamp = time() - (pnConfigGetVar('secinactivemins') * 60);
    $pntable =& pnDBGetTables();
    $column = $pntable['session_info_column']['lastused'];
    $where =  $column." > '".date("Y-m-d H:i:s",$timestamp)."'";
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
 * @param  array $list
 * @param  string $criteria
 *
 * @return  array   the buddylist sorted
 */
function _cl_sortList($list,$criteria) {
    // If there is no buddy list available yet we can return an empty array 
    // to avoid php warning / info notice
    if (empty($list)) {
        return array();
    }
    if ((!isset($list)) || (!is_array($list))) return;
    // just shuiffle?
    if ($criteria == 'random') {
        shuffle($list);
        return $list;
    }
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
            $first[$key]  = strtolower($row['uname']);
            $second[$key]  = $row['state'];
        }
    }
    if ($criteria == 'state') array_multisort($first, SORT_ASC, $second, SORT_ASC, $list);
    else if ($criteria == 'uname') array_multisort($first, SORT_ASC, $second, SORT_ASC, $list);
    else array_multisort($first, SORT_ASC, $list);
    return $list;
}

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
 * get userlist of online users
 *
 * @return	array
 */
function _cl_getOnline($args)
{
    $timestamp = time() - (pnConfigGetVar('secinactivemins') * 60);
    $sql =  "lastused  > $timestamp";
    $result = DBUtil::selectObjectArray('session_info',$where);
    foreach ($result as $item) $uidlist[] = $item['uid'];
    return $uidlist;
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
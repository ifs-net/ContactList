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

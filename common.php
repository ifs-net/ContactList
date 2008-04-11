<?php

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

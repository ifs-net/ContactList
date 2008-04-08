<?php

function cmpByUserName($a, $b)
{
    if ($a['uname'] == $b['uname']) {
        return 0;
    }
    return ($a['uname'] < $b['uname']) ? -1 : 1;
}
function cmpBySortNr($a, $b)
{
    if ($a['uname'] == $b['uname']) {
        return 0;
    }
    return ($a['uname'] < $b['uname']) ? -1 : 1;
}

// uasort($item, "cmpBySortNr");

?>
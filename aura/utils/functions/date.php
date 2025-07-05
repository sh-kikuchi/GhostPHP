<?php
/******************************
 * timestamp
 ******************************/
/**
 * Convert timestamps to Y-m-d format.
 *
 * @param timestamp $ts
 * @return string Y-m-d
 */
function toDateYmd($ts) {
    return date('Y-m-d', $ts);
}
/**
 * Convert timestamps to Y-m-d H: i format.
 *
 * @param timestamp $ts
 * @return string Y-m-d H:i
 */
function toDateYmdHi($ts) {
    return date('Y-m-d H:i', $ts);
}
/**
 * Receive date and time in string and convert TimeStamp
 *
 * @param string $ymd Y-m-d
 * @param string $hi H:i
 * @return timestamp
 */
function toTimeStamp($ymd="", $hi="")
{
    if (empty($ymd) || empty($hi)) trigger_error("empty ymd or hi!", E_USER_ERROR);
    $ymd = explode("-", $ymd);
    $hi = explode(":", $hi);
    return mktime($hi[0], $hi[1], 0, $ymd[1], $ymd[2], $ymd[0]);
}

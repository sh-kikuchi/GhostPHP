<?php

/******************************
 * timestamp
 ******************************/

/**
 * Convert a timestamp to Y-m-d format.
 *
 * @param int $ts Unix timestamp.
 * @return string Formatted date (Y-m-d).
 */
function toDateYmd(int $ts): string {
    return date('Y-m-d', $ts);
}

/**
 * Convert a timestamp to Y-m-d H:i format.
 *
 * @param int $ts Unix timestamp.
 * @return string Formatted date (Y-m-d H:i).
 */
function toDateYmdHi(int $ts): string {
    return date('Y-m-d H:i', $ts);
}

/**
 * Convert a date and time string to a Unix timestamp.
 *
 * @param string $ymd Date in Y-m-d format.
 * @param string $hi Time in H:i format.
 * @return int Unix timestamp.
 */
function toTimeStamp(string $ymd = "", string $hi = ""): int {
    if (empty($ymd) || empty($hi)) {
        trigger_error('empty ymd or hi!', E_USER_ERROR);
    }

    $ymd = explode('-', $ymd);
    $hi = explode(':', $hi);

    return mktime($hi[0], $hi[1], 0, $ymd[1], $ymd[2], $ymd[0]);
}
<?php

/**
 * Case-insensitive in_array() implementation.
 *
 * @param $needle
 * @param $haystack
 * @return bool
 */
function in_array_ci($needle, $haystack) {
    return in_array(strtolower($needle), array_map('strtolower', $haystack));
}
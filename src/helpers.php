<?php declare(strict_types = 1);

if (!function_exists('in_array_ci')) {
    /*
 * Case-insensitive in_array() implementation.
 */
    function in_array_ci(string $needle, array $haystack): bool
    {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
}

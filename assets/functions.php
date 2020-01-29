<?php

/**
 * Check if current php version is greater
 * 
 * @param string $version
 * @return bool
 */
function checkPhpVersion(string $version): bool
{
    return version_compare(phpversion(), $version, 'ge');
}

/**
 * Check if current mysql version is greater
 * 
 * @param int $version
 * @return bool
 */
function checkMysqlVersion(int $version = 56000): bool
{
    if (! function_exists('mysqli_connect')) {
        return false;
    }

    if (mysqli_get_client_version() > $version) {
        return true;
    }

    return false;
}
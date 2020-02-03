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
function checkMysqlVersion(int $versionNumReq = 5600): bool
{
    if ($output = shell_exec('mysqld --version')) {
        preg_match("/[Vv][Ee][Rr] .*-/", $output, $matches);
        $version = explode(' ', $matches[0])[1];
        $versions = explode('.', $version);
        $versionNum = $versions[0] * 1000 + $versions[1] * 100;

        return $versionNum >= $versionNumReq;
    } else {
        return false;
    }
}

function checkDirPermissions(): bool
{
    return is_writable(__DIR__);
}

function execute($exec)
{
    exec($exec . " -q 2>&1 --no-ansi", $output, $code);

    return $code;
}
<?php

function getLatestVersion(string $module)
{
    $apiUrl = "https://api.github.com/repos/lithecore/lithe_modules/contents/$module?ref=main";
    $files = getFileData($apiUrl);

    if ($files === false) {
        return 'v1.0.0'; // Return a default version if fetching fails
    }

    $versions = array_filter($files, function ($file) {
        return $file['type'] === 'dir' && preg_match('/^v\d+\.\d+\.\d+$/', $file['name']);
    });

    if (empty($versions)) {
        return 'v1.0.0'; // Return a default version if no versions are found
    }

    usort($versions, function ($a, $b) {
        return version_compare($b['name'], $a['name']);
    });

    return $versions[0]['name'];
}
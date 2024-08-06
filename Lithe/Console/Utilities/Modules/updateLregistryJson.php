<?php

use Symfony\Component\Console\Style\SymfonyStyle;

function updateLregistryJson(string $module, string $version, string $modulesFile, SymfonyStyle $io)
{
    $registry = ['modules' => []];

    if (file_exists($modulesFile)) {
        $registry = json_decode(file_get_contents($modulesFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }
    }

    $moduleExists = false;
    foreach ($registry['modules'] as &$existingModule) {
        if ($existingModule['name'] === $module) {
            $existingModule['version'] = $version;
            $moduleExists = true;
            break;
        }
    }

    if (!$moduleExists) {
        $registry['modules'][] = ['name' => $module, 'version' => $version];
    }

    file_put_contents($modulesFile, json_encode($registry, JSON_PRETTY_PRINT));

    return true;
}
<?php

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Command\Command;

/**
 * Remove module files and update lregistry.json.
 *
 * @param string $module The name of the module to remove.
 * @param string $destination The path to the module files.
 * @param string $modulesFile The path to the lregistry.json file.
 * @param SymfonyStyle $io The SymfonyStyle instance for console output.
 * @return int Command::SUCCESS on success, Command::FAILURE on failure.
 */
function removeModuleFilesAndUpdateRegistry(string $module, SymfonyStyle $io): int
{
    $destination = ".lithe_modules/$module";
    $modulesFile = "lregistry.json";

    if (!file_exists($destination)) {
        $io->error('Module not found.');
        return Command::FAILURE;
    }

    // Remove module files
    deleteDirectory($destination);

    // Remove module entry from lregistry.json
    if (file_exists($modulesFile)) {
        $registry = json_decode(file_get_contents($modulesFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error('Failed to parse lregistry.json.');
            return Command::FAILURE;
        }

        if (isset($registry['modules'])) {
            $registry['modules'] = array_filter($registry['modules'], function ($mod) use ($module) {
                return $mod['name'] !== $module;
            });

            file_put_contents($modulesFile, json_encode($registry, JSON_PRETTY_PRINT));
        }
    }

    return Command::SUCCESS;
}

/**
 * Recursively delete a directory.
 *
 * @param string $dir The directory path.
 */
function deleteDirectory(string $dir)
{
    if (!file_exists($dir)) return;
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }
    rmdir($dir);
}

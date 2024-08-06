<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

function installModule(string $module, ?string $version, SymfonyStyle $io): int
{
    $version = $version ?: getLatestVersion($module);

    // Define the URL for the versioned directory
    $apiUrl = "https://api.github.com/repos/lithecore/lithe_modules/contents/$module/$version";
    $destination = ".lithe_modules/$module";

    // Create the .lithe_modules directory if it does not exist
    createDirectoryIfNotExists(".lithe_modules");

    // Get file details from the GitHub API
    $files = getFileData($apiUrl);
    if ($files === false) {
        $io->error('Failed to fetch file details. Please check the module name and version, and try again.');
        return Command::FAILURE;
    }

    $success = true; // Flag to track overall success

    // Iterate over files and download each one
    foreach ($files as $fileData) {
        if (isset($fileData['download_url'])) {
            $fileUrl = $fileData['download_url'];
            $filePath = $destination . '/' . $fileData['name'];

            // Create directory for file if it does not exist
            createDirectoryIfNotExists(dirname($filePath));

            // Download the file
            $fileContents = file_get_contents($fileUrl);
            if ($fileContents === false) {
                $io->error("Failed to install: {$fileData['name']}.");
                $success = false; // Set flag to false if any file fails to download
                continue; // Continue to next file
            }

            file_put_contents($filePath, $fileContents);
        }
    }

    $io->writeln(sprintf("\r %s (version: %s) ................................................................................... <info>DONE</info>", basename($module), $version));

    // Update lregistry.json
    $modulesFile = "lregistry.json";
    if (!updateLregistryJson($module, $version, $modulesFile, $io)) {
        $io->error('Failed to update lregistry.json.');
        return Command::FAILURE;
    }

    return $success ? Command::SUCCESS : Command::FAILURE;
}
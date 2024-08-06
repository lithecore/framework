<?php

use Lithe\Console\Line;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

return Line::create(
    'module:update',
    'Update a specific module to the latest version, or all modules listed in lregistry.json if no module is specified.',
    function (InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        // Create a custom style for the "INFO" message
        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info-bg', $outputStyle);

        $module = $input->getArgument('module');

        if ($module) {
            // Update the specified module
            updateModule($module, $io);
        } else {
            // Update all modules listed in lregistry.json
            $modulesFile = "lregistry.json";

            if (!file_exists($modulesFile)) {
                $io->writeln('<error>lregistry.json does not exist. Cannot update modules.</error>');
                return Command::FAILURE;
            }

            $modules = json_decode(file_get_contents($modulesFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $io->writeln('<error>Failed to parse lregistry.json.</error>');
                return Command::FAILURE;
            }

            foreach ($modules as $mod) {
                updateModule($mod['name'], $io);
            }
        }

        $io->newLine();

        return Command::SUCCESS;
    },
    [
        'module' => [InputArgument::OPTIONAL, 'The name of the module to update']
    ]
);

function updateModule(string $module, SymfonyStyle $io)
{
    $latestVersion = getLatestVersion($module);
    $io->writeln(" \n\r<info-bg> INFO </info-bg> Updating module: $module to version: $latestVersion \n");

    // Uninstall the module first
    removeModuleFilesAndUpdateRegistry($module, $io);

    // Install the latest version
    installModule($module, $latestVersion, $io);
}

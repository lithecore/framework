<?php

use Lithe\Console\Line;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

return Line::create(
    'module:install',
    'Install a specific module from the lithe_modules repository, or all modules listed in lregistry.json if no module is specified.',
    function (InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        // Create a custom style for the "INFO" message
        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info-bg', $outputStyle);

        $module = $input->getArgument('module');
        $version = $input->getArgument('version') ?: null;

        $io->writeln(" \n\r<info-bg> INFO </info-bg> Installing modules...\n");

        if ($module) {
            // Install the specified module
            $result = installModule($module, $version, $io);
            if ($result === Command::FAILURE) {
                return Command::FAILURE;
            }

            $io->newLine();
            return Command::SUCCESS;
            
        } else {
            // Install all modules listed in lregistry.json
            $modulesFile = 'lregistry.json';

            if (!file_exists($modulesFile)) {
                $io->writeln('<error>lregistry.json does not exist. Cannot install modules.</error>');
                return Command::FAILURE;
            }

            $registry = json_decode(file_get_contents($modulesFile), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $io->writeln('<error>Failed to parse lregistry.json.</error>');
                return Command::FAILURE;
            }

            if (!isset($registry['modules'])) {
                $io->writeln('<error>No modules found in lregistry.json.</error>');
                return Command::FAILURE;
            }

            $success = true; // Flag to track overall success

            foreach ($registry['modules'] as $mod) {
                $result = installModule($mod['name'], $mod['version'], $io);
                if ($result === Command::FAILURE) {
                    $success = false;
                    $io->error("Failed to install module: {$mod['name']}.");
                }
            }

            if (!$success) {
                return Command::FAILURE;
            }
        }
        
        $io->newLine();
        return Command::SUCCESS;
    },
    [
        'module' => [InputArgument::OPTIONAL, 'The name of the module to install'],
        'version' => [InputArgument::OPTIONAL, 'The version of the module to install']
    ]
);

function createDirectoryIfNotExists(string $path)
{
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
    }
}
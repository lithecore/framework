<?php

use Lithe\Console\Line;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

return Line::create(
    'module:version',
    'Check the version of a specific module or list all module versions.',
    function (InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        $module = $input->getArgument('module');
        $modulesFile = "lregistry.json";

        if (!file_exists($modulesFile)) {
            $io->error('lregistry.json does not exist. Cannot check module versions.');
            return Command::FAILURE;
        }

        $registry = json_decode(file_get_contents($modulesFile), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error('Failed to parse lregistry.json.');
            return Command::FAILURE;
        }

        if (isset($registry['modules'])) {
            if ($module) {
                // Check version of the specified module
                foreach ($registry['modules'] as $mod) {
                    if ($mod['name'] === $module) {
                        $io->writeln("\n\r Module: {$mod['name']}, Version: {$mod['version']}\n");
                        return Command::SUCCESS;
                    }
                }
                $io->error("Module $module not found.");
                return Command::FAILURE;
            } else {
                // List all module versions
                foreach ($registry['modules'] as $mod) {
                    $io->writeln("\n\r Module: {$mod['name']}, Version: {$mod['version']}\n");
                }
                return Command::SUCCESS;
            }
        } else {
            $io->error('No modules found in lregistry.json.');
            return Command::FAILURE;
        }
    },
    [
        'module' => [InputArgument::OPTIONAL, 'The name of the module to check the version of']
    ]
);

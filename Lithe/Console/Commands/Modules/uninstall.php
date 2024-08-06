<?php

use Lithe\Console\Line;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

return Line::create(
    'module:uninstall',
    'Uninstall a specific module and remove its files.',
    function (InputInterface $input, OutputInterface $output): int {
        $io = new SymfonyStyle($input, $output);

        // Create a custom style for the "INFO" message
        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info-bg', $outputStyle);

        $module = $input->getArgument('module');

        $io->writeln(" \n\r<info-bg> INFO </info-bg> Uninstalling module: $module\n");

        // Chama a função para remover arquivos do módulo e atualizar o registro
        $result = removeModuleFilesAndUpdateRegistry($module, $io);

        if ($result === Command::SUCCESS) {
            // Se a remoção for bem-sucedida, exibe a mensagem de sucesso
            $io->writeln(sprintf("\r %s .......................................................................................... <info>UNINSTALLED</info>", basename($module)));
        } else {
            // Se a remoção falhar, encerra a execução com um código de falha
            return Command::FAILURE;
        }
        
        $io->newLine();

        return Command::SUCCESS;
    },
    [
        'module' => [InputArgument::REQUIRED, 'The name of the module to uninstall']
    ]
);

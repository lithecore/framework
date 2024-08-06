<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Lithe\Console\Line;

return Line::create(
    'key:generate',
    'Generate a new encryption key and set it in the .env file',
    function (InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        // Create a custom style for the "INFO" message
        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info-bg', $outputStyle);

        // Define the .env file path
        $envFilePath = '.env';

        if (!file_exists($envFilePath)) {
            $io->error(".env file not found!");
            return Command::FAILURE;
        }

        // Load the .env file content
        $envContent = file_get_contents($envFilePath);

        // Check if APP_KEY already exists in the .env file
        if (preg_match('/^APP_KEY=.*$/m', $envContent)) {
            $io->note("An APP_KEY already exists in the .env file.");
            $confirm = $io->confirm("Do you want to overwrite it and generate a new key?", false);

            if (!$confirm) {
                $io->success("No changes made. Exiting.");
                return Command::SUCCESS;
            }
        }

        // Generate a new encryption key
        $key = base64_encode(random_bytes(32)); // Use Base64 encoding

        // Update or add APP_KEY in the .env file
        if (preg_match('/^APP_KEY=.*$/m', $envContent)) {
            $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $envContent);
        } else {
            $envContent .= "\nAPP_KEY=" . $key;
        }

        // Save the updated .env file
        file_put_contents($envFilePath, $envContent);

        $io->writeln("\n\r<info-bg> INFO </info-bg> New APP_KEY key generated and set in the .env file.\n");

        return Command::SUCCESS;
    }
);

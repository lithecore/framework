<?php

use Lithe\Console\Line;
use Lithe\Support\Env;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Style\SymfonyStyle;

// Create the command
return Line::create(
    'make:model', // Command name
    'Create a Model', // Command description
    function (InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        // Retrieve the 'name' argument for the model
        $name = $input->getArgument('name');
        // Define the path where the new model file will be created
        $modelPath = "src/models/{$name}.php";
        // Get the specified template option, if any
        $template = $input->getOption('template');

        // Create a custom style for informational messages with a blue background
        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info-bg', $outputStyle);

        // Define available templates for the model
        $templates = [
            'default' => [
                'use' => '',
                'extends' => ''
            ],
            'eloquent' => [
                'use' => 'use Illuminate\Database\Eloquent\Model;',
                'extends' => 'extends Model'
            ],
        ];

        // Retrieve the database connection method from environment variables
        $db_connection_method = Env::get('DB_CONNECTION_METHOD', 'default');

        // Use the specified database connection method as the template
        $template = $db_connection_method;

        // Check if the model name is provided
        if (empty($name)) {
            $io->warning('The name of the model is mandatory.');
            return Command::FAILURE;
        }

        // Validate the specified template
        if ($template && !isset($templates[$template])) {
            $io->error('Invalid template');
            return Command::FAILURE;
        }

        // Check if the model file already exists
        if (file_exists($modelPath)) {
            $io->warning("The model already exists in {$modelPath}.");
            return Command::FAILURE;
        }

        // Generate the content for the new model file
        $modelContent = <<<PHP
<?php
namespace App\Models;

{$templates[$template]['use']}

class {$name} {$templates[$template]['extends']}
{
    // Your model logic goes here
}
PHP;

        // Write the generated content to the model file
        file_put_contents($modelPath, $modelContent);

        // Output a success message indicating the model was created
        $io->writeln("\n\r<info-bg> INFO </info-bg> Model [src/models/{$name}.php] created successfully.\n");

        return Command::SUCCESS;
    },
    [
        // Define the 'name' argument for the command
        'name' => [
            InputArgument::REQUIRED, // Argument is required
            'Model name' // Description of the argument
        ]
    ],
    [
        // Define the 'template' option for the command
        'template' => [
            null, // No short option
            InputOption::VALUE_REQUIRED, // Option requires a value
            'Template type', // Description of the option
            '' // Default value if not provided
        ]
    ]
);

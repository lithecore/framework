<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use Lithe\Console\Line;

return Line::create(
    'make:controller',
    'Create a Controller',
    function (InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $outputStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info-bg', $outputStyle);

        $name = $input->getArgument('name');
        $controllerPath = "src/Http/Controllers/{$name}.php";

        if (controllerExists($controllerPath)) {
            $io->warning("The controller already exists in {$controllerPath}.");
            return Command::FAILURE;
        }

        $controllerContent = generateControllerContent($name);
        file_put_contents($controllerPath, $controllerContent);

        $io->writeln("\n\r<info-bg> INFO </info-bg> Controller [{$controllerPath}] created successfully.\n");

        return Command::SUCCESS;
    },
    [
        'name' => [
            InputArgument::REQUIRED,
            'Controller name'
        ]
    ]
);

function controllerExists($controllerPath)
{
    return file_exists($controllerPath);
}

function generateControllerContent($name)
{
    return <<<PHP
<?php

namespace App\Http\Controllers;

use Lithe\Contracts\Http\Request;
use Lithe\Contracts\Http\Response;

class {$name}
{
    // Your controller logic goes here
}
PHP;
}

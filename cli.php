#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

if (\file_exists(__DIR__.'/.env')) {
    (new Dotenv())->load(__DIR__.'/.env');
}

$application = new Application();
$application->setName('PdfGeneratorAPI console utility');

$application->addCommands([
    new \MSansen\Command\TemplateListCommand(),
    new \MSansen\Command\TemplateGetCommand(),
    new \MSansen\Command\TemplateCreateCommand(),
    new \MSansen\Command\TemplateCopyCommand(),
    new \MSansen\Command\TemplateDeleteCommand(),
]);

$application->run();

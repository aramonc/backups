#!/usr/bin/env php
<?php
require_once __DIR__.'/../vendor/autoload.php';

use Ils\Command;
use Symfony\Component\Console\Application;

$app = new Application('Backups', '1.0');
$app->addCommands([
        new Command\Database()
    ]);
$app->run();
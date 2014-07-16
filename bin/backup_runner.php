<?php
require __DIR__."/../vendor/autoload.php";

use Ils\Command;
use Symfony\Component\Console\Application;

$app = new Application('Backups', '1.0');

$config = new \Zend\ServiceManager\Config(include __DIR__ . '/../src/dependencies.php');
$serviceLocator = new \Zend\ServiceManager\ServiceManager($config);

$db = new Command\Database();
$db->setServiceManager($serviceLocator);

$app->addCommands([$db]);
$app->run();
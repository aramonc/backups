<?php
namespace Ils\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Database extends BaseCommand {

    protected function configure()
    {
        $this->setName('databases')
            ->setDescription('Creates backups of the databases specified in the configuration file')
            ->addOption('conf', 'c', InputOption::VALUE_REQUIRED, 'Configuration for the database connection');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        var_dump(__DIR__);
    }
} 
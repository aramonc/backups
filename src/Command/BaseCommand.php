<?php

namespace Ils\Command;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface as InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface as OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class BaseCommand extends Command
{
    protected $filesystem;

    protected function configure()
    {
        $this
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The file name of the backup')
            ->addOption('location', 'l', InputOption::VALUE_REQUIRED, 'The path to store the backups')
            ->addOption('send', 's', InputOption::VALUE_NONE, 'Send the backup to a remote server')
            ->addOption('send-config', null, InputOption::VALUE_OPTIONAL, 'Configuration of where to send the backup')
            ->addOption('compress', null, InputOption::VALUE_NONE, 'Compress the file(s) generated')
            ->addOption('dry-run', null, InputOption::VALUE_OPTIONAL, 'Test backup, but don\'t actually create it');
    }

    protected function getFs()
    {
        if(!$this->filesystem) {
//            $this->filesystem = new Filesystem(new Local());
        }
    }
} 
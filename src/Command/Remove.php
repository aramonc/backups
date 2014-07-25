<?php
namespace Ils\Command;

use Ils\BackupInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Remove extends BaseCommand {

    protected $compress = false;
    protected $compressionLib = null;

    protected function configure()
    {
        $this->setName('remove')
            ->addArgument('files', InputArgument::IS_ARRAY, "List of files to remove")
            ->setDescription('Remove a list of files or directories using the flags -rf');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');

        if(empty($files)) {
            return '';
        }

        exec('rm -rf ' . implode(' ', $files), $result, $code);

        return $code;
    }
} 
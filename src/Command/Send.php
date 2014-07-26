<?php
namespace Ils\Command;

use Ils\FileSystem\FileSystem;
use Ils\SftpAdapter;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Send extends BaseCommand {

    protected $compress = false;
    protected $compressionLib = null;

    protected function configure()
    {
        $this->setName('send')
            ->addArgument('files', InputArgument::IS_ARRAY, "List of files to send to a remote server specified in the config file")
            ->setDescription('Send a list of files or directories to a remote server');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig($input->getOption('conf'));
        if(!isset($config['remote'])) {
            return 1;
        }

        $config = $config['remote']['ftp'];

        $adapter = new SftpAdapter($config);
        $fs = new FileSystem($adapter);

        foreach($input->getArgument('files') as $path) {
            $file = new \SplFileInfo($path);
            $fs->writeStream($input->getOption('location') . DIRECTORY_SEPARATOR . $file->getFilename(), $file);
        }

        return 0;
    }

} 
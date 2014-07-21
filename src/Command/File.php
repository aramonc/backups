<?php
namespace Ils\Command;

use Ils\BackupInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class File extends BaseCommand {

    protected function configure()
    {
        $this->setName('file')
            ->setDescription('Creates backups of the directories specified in the configuration file');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig($input->getOption('conf'));
        $storage = new \SplFileInfo($config['tmp_storage']);
        $archivePath = $storage->getRealPath() . DIRECTORY_SEPARATOR . $input->getOption('name') . '.tar.gz';
        $phar = new \PharData($archivePath, \Phar::CURRENT_AS_FILEINFO | \Phar::KEY_AS_PATHNAME, $input->getOption('name'), \Phar::TAR);
        if($config['gzip'] && $phar->canCompress(\Phar::GZ)) {
            $phar->compress(\Phar::GZ);
        }
        $phar->startBuffering();
        foreach($config['files'] as $path) {
            $phar->buildFromDirectory($path);
        }
        $phar->stopBuffering();

        if(isset($config['remote']) && !empty($config['remote'])) {
            $this->sendFiles(new \SplFileInfo($archivePath), $input->getOption('location'), $config['remote']['ftp']);
        }
    }


} 
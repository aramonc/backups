<?php
namespace Ils\Command;

use Ils\BackupInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Database extends BaseCommand {

    protected function configure()
    {
        $this->setName('databases')
            ->setDescription('Creates backups of the databases specified in the configuration file');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getConfig($input->getOption('conf'));
        foreach($config['database'] as $type => $dbConfig) {
            $svc = $this->getBUService($type);
            $svc->setConfig($dbConfig)
                ->setUseNice($config['use_nice'])
                ->setPath($config['tmp_storage'])
                ->setDryRun($input->getOption('dry-run'))
                ->run($output);
        }

        $package = $this->packageFiles($input->getOption('name'), new \SplFileInfo($config['tmp_storage']), !!$config['gzip']);
        if(isset($config['remote']) && !empty($config['remote'])) {
            $this->sendFiles(new \SplFileInfo($package), $input->getOption('location'), $config['remote']['ftp']);
        }
    }

    /**
     * @param $type
     * @return BackupInterface
     */
    protected function getBUService($type)
    {
        $service = null;
        switch($type) {
            case 'mysql':
                $service = $this->getServiceManager()->get('mysql');
                break;
        }

        return $service;
    }
} 